<?php

class QuickVideoAddForm extends SpecialPage {
	var	$mAction,
		$mPosted,
		$mName,
		$mUrl;

	/* constructor */
	function __construct () {
		$this->mAction = "";
		parent::__construct( "QuickVideoAdd", "quickvideoadd" );
	}

	public function execute( $subpage ) {
		global $wgOut, $wgRequest;

		wfLoadExtensionMessages('QuickVideoAdd');

		$this->mTitle = Title::makeTitle( NS_SPECIAL, 'QuickVideoAdd' );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setPageTitle( "QuickVideoAdd" );
		$wgOut->setArticleRelated( false );

		$this->mAction = $wgRequest->getVal( "action" );
		$this->mPosted = $wgRequest->wasPosted();

		switch( $this->mAction ) {
			case 'submit' :
				if ( $wgRequest->wasPosted() ) {
					$this->mAction = $this->doSubmit();
				}
				break;
			default:
				$this->showForm();
				break;
		}
	}

	public function showForm() {
		global $wgOut, $wgRequest;
		$titleObj = Title::makeTitle( NS_SPECIAL, 'QuickVideoAdd' );
		$action = $titleObj->escapeLocalURL( "action=submit" );
		( '' != $wgRequest->getVal( 'name' ) ) ? $name = $wgRequest->getVal( 'name' ) : $name = '';

		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$oTmpl->set_vars( array(
					"out"		=> 	$wgOut,
					"action"	=>	$action,
					"name"		=> 	$name,
				       ) );
		$wgOut->addHTML( $oTmpl->execute("quickform") );
	}

	public function doSubmit() {
		global $wgOut, $wgRequest, $IP, $wgUser;
		require_once( "$IP/extensions/wikia/WikiaVideo/VideoPage.php" );	

		if( '' == $wgRequest->getVal( 'wpQuickVideoAddName' ) ) {
			if( '' != $wgRequest->getVal( 'wpQuickVideoAddPrefilled' ) ) {
				$this->mName = $wgRequest->getVal( 'wpQuickVideoAddPrefilled' );
			} else {
				$this->mName = '';
			}			
		} else {
			$this->mName = $wgRequest->getVal( 'wpQuickVideoAddName' );
		}

		( '' != $wgRequest->getVal( 'wpQuickVideoAddUrl' ) ) ? $this->mUrl = $wgRequest->getVal( 'wpQuickVideoAddUrl' ) : $this->mUrl = '';	

		if ( ( '' != $this->mName ) && ( '' != $this->mUrl ) ) {
			$title = Title::makeTitle( NS_SPECIAL, $this->mName );
			if ( $title instanceof Title ) {
				$video = new VideoPage( $title );	
				$video->parseUrl( $this->mUrl );
				$video->setName( $this->mName );
				$video->save();				
			}
			$sk = $wgUser->getSkin();
	                $link_back = $sk->makeKnownLinkObj( $title );
			$wgOut->addHTML( wfMsg( 'qva-success', $link_back ) );
		} else {
			$wgOut->addHTML( wfMsg( 'qva-failure' ) );
		}
	}
}

