<?php
/**
 * Main part of Special:Our404Handler
 *
 * @file
 * @ingroup Extensions
 * @author Krzysztof Krzyżaniak <eloy@wikia-inc.com> for Wikia.com
 * @copyright © 2007, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @version 1.0
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is a MediaWiki extension and cannot be used standalone.\n";
	exit( 1 );
}

class Our404HandlerPage extends UnlistedSpecialPage {
	const IMAGEROOT = '/images';
	const FAVICON_ICO = '/images/central/images/6/64/Favicon.ico';
	const FAVICON_URL = 'http://images.wikia.com/central/images/6/64/Favicon.ico';
	const LOGOWIDE_PNG = 'templates/Wiki_wide.png';
	const LOGOWIDE_URL = 'http://images.wikia.com/starter/images/e/ed/TitleTemplate.png';

	public $mTitle, $mAction, $mSubpage;

	/**
	 * Constructor
	 */
	public function  __construct() {
		parent::__construct( 'Our404Handler'/*class*/ );
	}

	/**
	 * Main entry point
	 * Default action is to make thumb
	 *
	 * @access public
	 *
	 * @param $subpage Mixed: subpage of SpecialPage
	 */
	public function execute( $subpage ) {
		global $wgRequest;
		wfLoadExtensionMessages( 'Our404Handler' );

		$this->setHeaders();

		$this->mTitle = Title::makeTitle( NS_SPECIAL, 'Our404Handler' );
		$this->mAction = $wgRequest->getVal( 'action' );
		$this->mSubpage = $subpage;
		$this->mAction = ( $this->mAction )
			? $wgRequest->getVal( 'action' )
			: 'thumb';

		if ( isset( $this->mSubpage ) && $this->mSubpage === 'thumb' ) {
			$sURI = $wgRequest->getVal( 'uri' );
			return $this->doThumbnail( $sURI );
		}

		$this->doRender404();
	}

	/**
	 * Just return thumbnail (create if needed). code based on /thumb.php
	 * from MediaWiki. Use cache for storing known values
	 *
	 * @access public
	 */
	public function doThumbnail( $uri ){
		global $wgOut, $wgMemc;

		wfProfileIn( __METHOD__ );
		/**
		 * take last part, it should be nnnpx-title schema
		 * (622px-Welcome_talk.png)
		 */
		$favicon = 0;
		$logowide = 0;
		$aParts = explode( '/', $uri );
		$sLast = array_pop( $aParts );

		/**
		 * maybe we have 404 on favicon.ico
		 */
		switch( $sLast ) {
			case 'Favicon.ico':
				$favicon = 1;
				break;

			case 'Wiki_wide.png':
				$logowide = 1;
				break;

			default:
				preg_match( "/(\d+)px\-([^\?]+)/", $sLast, $aParts );
				if( isset( $aParts[1] ) && isset( $aParts[2] ) ) {
					$sThumbWidth = $aParts[1];
					$sThumbName = $aParts[2];
				} else {
					wfProfileOut( __METHOD__ );
					return $this->doRender404();
				}
				break;
		}

		/**
		 * part before /images/ tell us what wiki it is
		 *
		 * this is little tricky because we're almost guessing by using image path
		 * which wiki it is. We ask twice, with and without closing "/"
		 */
		$sUploadDirectoryY = self::IMAGEROOT . substr( $uri, 0, strpos( $uri, '/images/' ) + strlen( '/images/' ) );
		$sUploadDirectoryN = rtrim( $sUploadDirectoryY, "/" );

		/**
		 * first check in cache, maybe we already saw it
		 */
		$sCacheKey = $this->cacheKey( $sUploadDirectoryN );
		$oRow = $wgMemc->get( $sCacheKey );

		if( !isset( $oRow->city_id ) ) {
			wfProfileIn( __METHOD__ ."-db" );

			/**
			 * try to find which wiki it is based on $sUploadDirectory value.
			 * first without trailing slash
			 */
			$dbr = wfGetDB( DB_SLAVE );
			$oRow = $dbr->selectRow(
				array(
					wfSharedTable( 'city_variables' ),
					wfSharedTable( 'city_list' )
				),
				array( 'city_id', 'city_url' ),
				array(
					'cv_value' => serialize( $sUploadDirectoryN ),
					'city_id = cv_city_id'
				),
				__METHOD__
			);
			if( empty( $oRow->city_id ) ) {
				/**
				 * second query with trailing slash
				 */
				$oRow = $dbr->selectRow(
					array(
						wfSharedTable( 'city_variables' ),
						wfSharedTable( 'city_list' )
					),
					array( 'city_id', 'city_url' ),
					array(
						'cv_value' => serialize( $sUploadDirectoryY ),
						'city_id = cv_city_id'
					),
					__METHOD__
				);
			}

			if( isset( $oRow->city_id ) ) {
				$wgMemc->set( $sCacheKey, $oRow, 86400 );
			}
			wfProfileOut( __METHOD__ ."-db" );
		} else {
			wfDebug( "Upload directory taken from cache" );
		}

		# still empty?
		if( !empty( $oRow->city_id ) && !empty( $oRow->city_url ) ) {

			if ( $favicon == 1 ) {
				# copy default favicon
				wfProfileOut( __METHOD__ );
				return $this->doCopyDefaultFavicon( $oRow );
			}
			if ( $logowide == 1 ) {
				# copy default logo
				wfProfileOut( __METHOD__ );
				return $this->doCopyDefaultLogo( $oRow );
			} else {
				/**
				 * thumbnail of file.svg file is file.svg.png, so we have to
				 * check if part before last is svg
				 */
				$svg = false;
				$parts = explode( ".", $sThumbName );
				if( is_array( $parts ) ) {
					$ext1 = array_pop( $parts );
					$ext2 = array_pop( $parts );
					if( strtolower( $ext2 ) === 'svg' ) {
						$svg = true;
						/**
						 * now build new filename without last part (extension)
						 */
						$file = implode( ".", $parts );
						$sThumbName = $file . "." . $ext2;
					}
				}

				# build API query
				$sApiQuery = sprintf(
					"%s/api.php?action=imagethumb&tiimage=%s&tiwidth=%d&format=json",
					rtrim( $oRow->city_url, "/" ),
					$sThumbName,
					$sThumbWidth
				);
				$sResponse = Http::get( $sApiQuery, 60 );
				$oResponse = Wikia::json_decode( $sResponse );
				if( !empty( $oResponse->query->imagethumb->thumb->exists ) ) {
					return $wgOut->redirect( $oResponse->query->imagethumb->thumb->url );
				}
			}
		}

		wfProfileOut( __METHOD__ );
		return $this->doRender404( $uri );
	}

	/**
	 * Just render some simple 404 page
	 *
	 * @access public
	 */
	public function doRender404( $uri = null ) {
		global $wgOut, $wgContLang, $wgCanonicalNamespaceNames;

		/**
		 * check, maybe we have article with that title, if yes 301redirect to
		 * this article
		 */
		if( $uri === null ) {
			$uri = $_SERVER['REQUEST_URI'];
			if ( !preg_match( '!^https?://!', $uri ) ) {
				$uri = 'http://unused' . $uri;
			}
			$uri = substr( parse_url( $uri, PHP_URL_PATH ), 1 );
		}
		$title = $wgContLang->ucfirst( $uri );
		$namespace = NS_MAIN;

		/**
		 * first check if title is in namespace other than main
		 */
		$parts = explode( ":", $title, 2 );
		if( count( $parts ) == 2 ) {
			foreach( $wgCanonicalNamespaceNames as $id => $name ) {
				$translated = $wgContLang->getNsText( $id );
				if( strtolower( $translated ) === strtolower( $parts[0] ) ||
					strtolower( $name ) === strtolower( $parts[0] ) ) {
					$namespace = $id;
					$title = $parts[1];
					break;
				}
			}
		}

		/**
		 * create title from parts
		 */
		$oTitle = Title::newFromText( $title, $namespace );

		if( !is_null( $oTitle ) ) {
			if( $namespace == NS_SPECIAL || $namespace == NS_MEDIA ) {
				/**
				 * these namespaces are special and don't have articles
				 */
				header( sprintf( "Location: %s", $oTitle->getFullURL() ), true, 301 );
				exit( 0 );

			} else {
				$oArticle = new Article( $oTitle );
				if( $oArticle->exists() ) {
					header( sprintf( "Location: %s", $oTitle->getFullURL() ), true, 301 );
					exit( 0 );
				}
			}

		}

		/**
		 * but if doesn't exist, we eventually show 404page
		 */
		$wgOut->setStatusCode( 404 );

		$info = wfMsgForContent( 'message404', $uri, $title );
		$wgOut->addHTML( '<h2>'.wfMsg( 'our404handler-oops' ).'</h2>
						<div>'. $wgOut->parse( $info ) .'</div>' );
	}

	/**
	 * Copy default favicon.ico if missing
	 *
	 * @access public
	 *
	 * @param $oWiki DatabaseRow: database object for row from city_list
	 * @return redirect to default favicon
	 */
	public function doCopyDefaultFavicon( $oWiki ){
		global $wgOut;

		# Get image directory for wiki
		$sUploadDirectory = WikiFactory::getVarValueByName( 'wgUploadDirectory', $oWiki->city_id );
		$sTargetFavicon = $sUploadDirectory.'/6/64/Favicon.ico';
		if( !file_exists( $sTargetFavicon ) ) {
			wfMkdirParents( dirname($sTargetFavicon) );
			@copy( self::FAVICON_ICO, $sTargetFavicon );
		}

		$sTargetFavicon = $sUploadDirectory.'/6/64/favicon.ico';
		if( !file_exists( $sTargetFavicon ) ) {
			wfMkdirParents( dirname( $sTargetFavicon ) );
			@copy( self::FAVICON_ICO, $sTargetFavicon );
		}

		return $wgOut->redirect( self::FAVICON_URL );
	}

	/**
	 * Copy default logo if missing
	 *
	 * @access public
	 *
	 * @param $oWiki DatabaseRow: database object for row from city_list
	 * @return redirect to default favicon
	 */
	public function doCopyDefaultLogo( $oWiki ){
		global $wgOut;

		# Get image directory for wiki
		$sUploadDirectory = WikiFactory::getVarValueByName( 'wgUploadDirectory', $oWiki->city_id );
		$sTargetLogo = $sUploadDirectory.'/b/bf/Wiki_wide.png';
		if( !file_exists( $sTargetLogo ) ) {
			wfMkdirParents( dirname( $sTargetLogo ) );
			@copy( dirname( __FILE__ ) . '/'. self::LOGOWIDE_PNG, $sTargetLogo );
		}
		return $wgOut->redirect( self::LOGOWIDE_URL );
	}

	/**
	 * Create cache key
	 *
	 * @access private
	 * @param $directory String: path to upload directory
	 * @return String: cache key
	 */
	private function cacheKey( $directory ){
		$parts = str_replace( '/', ':', $directory );
		return '404handler' . $parts;
	}

};
