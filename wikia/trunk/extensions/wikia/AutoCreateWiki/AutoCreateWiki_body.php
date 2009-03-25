<?php
/**
 * Main part of Special:AutoCreateWiki
 *
 * @file
 * @ingroup Extensions
 * @author Krzysztof Krzyżaniak <eloy@wikia-inc.com> for Wikia Inc.
 * @author Adrian Wieczorek <adi@wikia-inc.com> for Wikia Inc.
 * @author Piotr Molski <moli@wikia-inc.com> for Wikia Inc.
 * @copyright © 2009, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @version 1.0
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is a MediaWiki extension and cannot be used standalone.\n";
	exit( 1 );
}

class AutoCreateWikiPage extends SpecialPage {

	private
		$mTitle,
		$mAction,
		$mSubpage,
		$mWikiData,
		$mWikiId,
		$mMYSQLdump,
		$mMYSQLbin,
		$mPHPbin,
		$mStarters,
		$mCurrTime,
		$mPosted,
		$mPostedErrors,
		$mErrors;
	/**
	 * test database, CAUTION! content will be destroyed during tests
	 */
	const TESTDB = "testdb";
	const STARTER_GAME = 2; /** gaming **/
	const STARTER_ENTE = 3; /** enter. **/
	const LOG = "autocreatewiki";
	const IMGROOT = "/images/";
    const CREATEWIKI_LOGO = "/images/central/images/2/22/Wiki_Logo_Template.png";
    const CREATEWIKI_ICON = "/images/central/images/6/64/Favicon.ico";
    const SESSION_TIME = 60;
    const DAILY_LIMIT = 200;
    const DAILY_USER_LIMIT = 10;
    const TEMPLATE_LIST_WIKIA = "Template:List_of_Wikia_New";
    const ARTICLE_NEW_WIKIS = "New_wikis_this_week/Draft";
    const DEFAULT_STAFF = "Angela";
    const SEND_WELCOME_MAIL = 1;
    const CACHE_LOGIN_KEY = 'awc_beforelog';

	/**
	 * constructor
	 */
	public function  __construct() {
		parent::__construct( "AutoCreateWiki" /*class*/ );

		/**
		 * initialize some data
		 */
		$this->mWikiData = array();

		/**
		 * hub starters
		 */
		$this->mStarters = array(
			self::STARTER_GAME => 3578,
			self::STARTER_ENTE => 3711
		);

		/**
		 * set paths for external tools
		 */
		$this->mPHPbin =
			( file_exists("/usr/bin/php") && is_executable( "/usr/bin/php" ))
			? "/usr/bin/php" : "/opt/wikia/php/bin/php";

		$this->mMYSQLdump =
			( file_exists("/usr/bin/mysqldump") && is_executable( "/usr/bin/mysqldump" ))
			? "/usr/bin/mysqldump" : "/opt/wikia/bin/mysqldump";

		$this->mMYSQLbin =
			( file_exists("/usr/bin/mysql") && is_executable("/usr/bin/mysql") )
			? "/usr/bin/mysql" : "/opt/wikia/bin/mysql";
	}

	/**
	 * Main entry point
	 *
	 * @access public
	 *
	 * @param $subpage Mixed: subpage of SpecialPage
	 */
	public function execute( $subpage ) {
		global $wgRequest, $wgAuth, $wgUser;
		global $wgOut;
		global $wgDevelEnvironment;

		wfLoadExtensionMessages( "AutoCreateWiki" );

		$this->setHeaders();
		$this->mTitle = Title::makeTitle( NS_SPECIAL, "AutoCreateWiki" );
		$this->mAction = $wgRequest->getVal( "action", false );
		$this->mSubpage = $subpage;
		$this->mPosted = $wgRequest->wasPosted();
		$this->mPostedErrors = array();
		$this->mErrors = 0;


		if( $wgDevelEnvironment ) {
			global $wgDevelDomains;
			$this->mDefSubdomain = array_shift( $wgDevelDomains );
		}
		else {
			$this->mDefSubdomain = "wikia.com";
		}

		$this->mNbrCreated = $this->countCreatedWikis();

		if ( $this->mNbrCreated >= self::DAILY_LIMIT ) {
			$wgOut->addHTML(wfMsg('autocreatewiki-limit-creation'));
			return;
		}

		if( $subpage === "test" ) {
			#---
			$this->create();
		} elseif ( $subpage === "Caching" ) {
			$this->setValuesToSession();
			exit;
		} elseif ( $subpage === "Testing" ) {
			if ( $this->setVarsFromSession() > 0 ) {
				$this->test();
			}
		} elseif ( $subpage === "Processing" ) {
			$this->log (" session: " . print_r($_SESSION, true). "\n");
			if ( isset( $_SESSION['mAllowToCreate'] ) && ( $_SESSION['mAllowToCreate'] >= wfTimestamp() ) ) {
				$this->mNbrUserCreated = $this->countCreatedWikisByUser();
				if ( $this->mNbrUserCreated >= self::DAILY_USER_LIMIT ) {
					$wgOut->addHTML(wfMsg('autocreatewiki-limit-creation'));
					return;
				}
				if ( $this->setVarsFromSession() > 0 ) {
					$this->createWiki();
				}
			} else {
				$this->log ("restriction error\n");
				$this->displayRestrictionError();
				return;
			}
		} elseif ( $subpage === "Wiki_create" ) {
			if ( isset( $_SESSION['mAllowToCreate'] ) && ( $_SESSION['mAllowToCreate'] >= wfTimestamp() ) ) {
				#--- Limit of user creation
				$this->mNbrUserCreated = $this->countCreatedWikisByUser();
				if ( $this->mNbrUserCreated >= self::DAILY_USER_LIMIT ) {
					$wgOut->addHTML(wfMsg('autocreatewiki-limit-creation'));
					return;
				}
				if ( $this->setVarsFromSession() > 0 ) {
					$this->processCreatePage();
				}
			} else {
				$this->clearSessionKeys();
				$wgOut->redirect( $this->mTitle->getLocalURL() );
			}
		} else {
			if ($this->mPosted) {
				#---
				$this->clearSessionKeys();
				$this->makeRequestParams();
				$this->checkWikiCreationParams();
				if ( $wgUser->isAnon() ) {
					$oUser = $wgUser;
					if ( empty($this->mLoggedin) ) {
						// create account form
						$oUser = $this->addNewAccount();
						if ( !is_null($oUser) ) {
							# user ok - so log in
							$wgAuth->updateUser( $oUser );
							$wgUser = $oUser;
							$wgUser->setCookies();
						}
					}
					# log in

/*					$isLoggedIn = $this->loginAfterCreateAccount( );
					if ( !empty($isLoggedIn) ) {
						if ( !empty($this->mRemember) ) {
							$wgUser->setOption( 'rememberpassword', 1 );
							$wgUser->saveSettings();
						}
					} else {*
						$this->makeError( "wiki-username", wfMsg('autocreatewiki-busy-username') );
					}*/
					if ( $wgUser->isAnon() ) {
						$this->makeError( "wiki-username", wfMsg('autocreatewiki-user-notloggedin') );
					} else {
						if ( !empty($this->mRemember) ) {
							$wgUser->setOption( 'rememberpassword', 1 );
							$wgUser->saveSettings();
						}
					}
				}

				#-- user logged in or just create
				if ( empty( $this->mErrors ) && ( $wgUser->getID() > 0 ) ) {
					#--- save values to session and redirect
					$this->makeRequestParams(true);
					$_SESSION['mAllowToCreate'] = wfTimestamp() + self::SESSION_TIME;
					$wgOut->redirect($this->mTitle->getLocalURL() . '/Wiki_create');
				} else {
					#--- some errors
					if ( isset($_SESSION['mAllowToCreate']) ) {
						unset($_SESSION['mAllowToCreate']);
					}
				}
			}
			$this->createWikiForm();
		}
	}

	private function test() {
		global $wgOut;
		for ($i = 1; $i < 9; $i++) {
			$this->setInfoLog('OK', wfMsg('autocreatewiki-step' . $i));
			sleep(1);
		}

		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$oTmpl->set_vars( array(
			"domain" => "testtestest.wikia.com",
		));
		#---
		$sFinishText = $oTmpl->execute("finish");
		$this->setInfoLog('END', $sFinishText);
	}

	/**
	 * main function for extension -- create wiki in wikifactory cluster
	 * we are assumming that data is valid!
	 *
	 */
	private function createWiki() {
		global $wgDebugLogGroups, $wgOut, $wgUser, $IP, $wgDBname, $wgSharedDB;
		global $wgDBserver, $wgDBuser,	$wgDBpassword, $wgWikiaLocalSettingsPath;
		global $wgHubCreationVariables, $wgLangCreationVariables, $wgUniversalCreationVariables;

		# $wgDebugLogGroups[ self::LOG ] = "/tmp/autocreatewiki.log";
		wfProfileIn( __METHOD__ );

		/**
		 * this will clean test database and fill mWikiData with test data
		 */
		$this->prepareValues();

		$this->mCurrTime = wfTime();
		$startTime = $this->mCurrTime;
		$this->mFounder = $wgUser;

		/**
		 * create image folder
		 */
		wfMkdirParents( $this->mWikiData[ "images"] );
		$this->log( "Create {$this->mWikiData[ "images"]} folder" );
		$this->setInfoLog('OK', wfMsg('autocreatewiki-step1'));

		/**
		 * check and create database
		 */
		$dbw = wfGetDB( DB_MASTER );
		$Row = $dbw->selectRow(
			wfSharedTable("city_list"),
			array( "count(*) as count" ),
			array( "city_dbname" => $this->mWikiData[ "dbname"] ),
			__METHOD__
		);
		$this->log( "Checking if database {$this->mWikiData[ "dbname"]} already exists");
		$error = 0;
		if( $Row->count > 0 ) {
			#error
			$this->log( "Database {$this->mWikiData[ "dbname"]} exists!" );
			$error = 1;
		} else {
			$dbw->query( sprintf( "CREATE DATABASE %s", $this->mWikiData[ "dbname"]) );
			$this->log( "Creating database {$this->mWikiData[ "dbname"]}" );
		}

		$msgType = ($error == 1) ? 'ERROR' : 'OK';
		$this->setInfoLog( $msgType, wfMsg('autocreatewiki-step2') );
		if ($error) {
			return;
		}
		/**
		 * create position in wiki.factory
		 * (I like sprintf construction, so sue me)
		 */
		$insertFields = array(
			'city_title'          => $this->mWikiData[ "title" ],
			'city_dbname'         => $this->mWikiData[ "dbname"],
			'city_url'            => sprintf( "http://%s.%s/", $this->mWikiData[ "subdomain" ], "wikia.com" ),
			'city_founding_user'  => $wgUser->getID(),
			'city_founding_email' => $wgUser->getEmail(),
			'city_path'           => $this->mWikiData[ "path" ],
			'city_description'    => $this->mWikiData[ "title" ],
			'city_lang'           => $this->mWikiData[ "language" ],
			'city_created'        => wfTimestamp( TS_DB, time() ),
		);

		$bIns = $dbw->insert( wfSharedTable("city_list"),$insertFields, __METHOD__ );
		if ( empty($bIns) ) {
			$this->setInfoLog( 'ERROR', wfMsg('autocreatewiki-step3') );
			$this->log( "Cannot set data in city_list table" );
			$wgOut->addHTML(wfMsg('autocreatewiki-step3-error'));
			return;
		}
		/*
		 * get Wiki ID
		 */
		$this->mWikiId = $dbw->insertId();
		if ( empty($this->mWikiId) ) {
			$this->setInfoLog( 'ERROR', wfMsg('autocreatewiki-step3') );
			$this->log( "Empty city_id = {$this->mWikiId}" );
			$wgOut->addHTML(wfMsg('autocreatewiki-step3-error'));
			return;
		}
		$this->mWikiData[ "city_id" ] = $this->mWikiId;
		$this->mWikiData[ "founder" ] = $wgUser->getId();
		$this->log( "Creating row in city_list table, city_id = {$this->mWikiId}" );

		$bIns = $dbw->insert(
			wfSharedTable("city_domains"),
			array(
				array(
					'city_id'     =>  $this->mWikiId,
					'city_domain' => sprintf("%s.%s", $this->mWikiData[ "subdomain" ], "wikia.com" )
				),
				array(
					'city_id'     =>  $this->mWikiId,
					'city_domain' => sprintf("www.%s.%s", $this->mWikiData[ "subdomain" ], "wikia.com" )
				)
			),
			__METHOD__
		);
		if ( empty($bIns) ) {
			$this->setInfoLog( 'ERROR', wfMsg('autocreatewiki-step3') );
			$this->log( "Cannot set data in city_domains table" );
			$wgOut->addHTML(wfMsg('autocreatewiki-step3-error'));
			return;
		}
		$this->setInfoLog( 'OK', wfMsg('autocreatewiki-step3') );

		$this->log( "Populating city_domains" );

		/**
		 * copy defaul logo & favicon
		 */
		wfMkdirParents("{$this->mWikiData[ "images" ]}/images/b/bc");
		wfMkdirParents("{$this->mWikiData[ "images" ]}/images/6/64");

		if (file_exists(self::CREATEWIKI_LOGO)) {
			copy(self::CREATEWIKI_LOGO, "{$this->mWikiData[ "images" ]}/images/b/bc/Wiki.png");
		}
		if (file_exists(self::CREATEWIKI_ICON)) {
			copy(self::CREATEWIKI_ICON, "{$this->mWikiData[ "images" ]}/images/6/64/Favicon.ico");
		}
		$this->log( "Coping favicon and logo" );
		$this->setInfoLog( 'OK', wfMsg('autocreatewiki-step4') );

		/**
		 * wikifactory variables
		 */
		$WFSettingsVars = array(
			'wgSitename'				=> $this->mWikiData[ 'title' ],
			'wgScriptPath'				=> '',
			'wgScript'					=> '/index.php',
			'wgArticlePath'				=> '/wiki/$1',
			'wgLogo'					=> '$wgUploadPath/b/bc/Wiki.png',
			'wgUploadPath'				=> "http://images.wikia.com/{$this->mWikiData[ "dir_part" ]}/images",
			'wgUploadDirectory'			=> "/images/{$this->mWikiData[ "dir_part" ]}/images",
			'wgDBname'					=> $this->mWikiData[ "dbname" ],
			'wgSharedDB'				=> 'wikicities',
			'wgLocalInterwiki'			=> $this->mWikiData[ 'title' ],
			'wgLanguageCode'			=> $this->mWikiData['language'],
			'wgServer'					=> "http://{$this->mWikiData["subdomain"]}." . "wikia.com",
			'wgFavicon'					=> '$wgUploadPath/6/64/Favicon.ico',
			'wgDefaultSkin'				=> 'monaco',
			'wgDefaultTheme'			=> 'sapphire',
			'wgEnableNewParser'			=> true,
			'wgEnableEditEnhancements'	=> true,
			'wgEnableSectionEdit'	    => true,
		);

		if( $WFSettingsVars[ "wgLanguageCode" ] === "en" ) {
			$WFSettingsVars[ "wgEnableWysiwygExt" ] = true;
		}

		foreach( $WFSettingsVars as $variable => $value ) {
			/**
			 * first, get id of variable
			 */
			$Row = $dbw->selectRow(
				wfSharedTable("city_variables_pool"),
				array( "cv_id" ),
				array( "cv_name" => $variable ),
				__METHOD__
			);

			/**
			 * then, insert value for wikia
			 */
			if( isset( $Row->cv_id ) && $Row->cv_id ) {
				$dbw->insert(
					wfSharedTable( "city_variables" ),
					array(
						"cv_value"       => serialize( $value ),
						"cv_city_id"     => $this->mWikiId,
						"cv_variable_id" => $Row->cv_id
					),
					__METHOD__
				);
			}
		}
		$this->log( "Populating city_variables" );
		$this->setInfoLog( 'OK', wfMsg('autocreatewiki-step5') );

		/**
		 * we got empty database created, now we have to create tables and
		 * populate it with some default values
		 */
		$tmpSharedDB = $wgSharedDB;
		$wgSharedDB = $this->mWikiData[ "dbname"];

		$dbw->selectDb( $this->mWikiData[ "dbname"] );
		$sqlfiles = array(
			"{$IP}/maintenance/tables.sql",
			"{$IP}/maintenance/interwiki.sql",
			"{$IP}/maintenance/wikia/default_userrights.sql",
			"{$IP}/maintenance/wikia/city_interwiki_links.sql",
			"{$IP}/maintenance/wikia-additional-tables.sql",
			"{$IP}/extensions/CheckUser/cu_changes.sql",
			"{$IP}/extensions/CheckUser/cu_log.sql",
		);

		foreach ($sqlfiles as $file) {
			$error = $dbw->sourceFile( $file );
			if ($error !== true) {
				$this->setInfoLog( 'ERROR', wfMsg('autocreatewiki-step6') );
				$wgOut->addHTML(wfMsg('autocreatewiki-step6-error'));
				return;
			}
		}
		$wgSharedDB = $tmpSharedDB;
		$this->log( "Creating tables in database" );
		$this->setInfoLog( 'OK', wfMsg('autocreatewiki-step6') );

		/**
		 * import language starter
		 */
		if( in_array( $this->mWikiData[ "language" ], array("en", "ja", "de", "fr") ) ) {
			$prefix = ( $this->mWikiData[ "language" ] === "en") ? "" : $this->mWikiData[ "language" ];
			$starterDB = $prefix. "starter";

			/**
			 * first check whether database starter exists
			 */
			$sql = sprintf( "SHOW DATABASES LIKE '%s';", $starterDB );
			$Res = $dbw->query( $sql, __METHOD__ );
			$numRows = $Res->numRows();
			if ( !empty( $numRows ) ) {
				$cmd = sprintf(
					"%s -h%s -u%s -p%s %s categorylinks externallinks image imagelinks langlinks page pagelinks revision templatelinks text | %s -h%s -u%s -p%s %s",
					$this->mMYSQLdump,
					$wgDBserver,
					$wgDBuser,
					$wgDBpassword,
					$starterDB,
					$this->mMYSQLbin,
					$wgDBserver,
					$wgDBuser,
					$wgDBpassword,
					$this->mWikiData[ "dbname"]
				);
				$this->log($cmd);
				wfShellExec( $cmd );

				$error = $dbw->sourceFile( "{$IP}/maintenance/cleanupStarter.sql" );
				if ($error !== true) {
					$this->setInfoLog( 'ERROR', wfMsg('autocreatewiki-step7') );
					$wgOut->addHTML(wfMsg('autocreatewiki-step7-error'));
					return;
				}

				$startupImages = sprintf( "%s/starter/%s/images/", self::IMGROOT, $prefix );
				if (file_exists( $startupImages ) && is_dir( $startupImages ) ) {
					wfShellExec("/bin/cp -af $startupImages {$this->mWikiData[ "images" ]}/");
					$this->log("/bin/cp -af $startupImages {$this->mWikiData[ "images" ]}/");
				}
				$cmd = sprintf(
					"SERVER_ID=%d %s %s/maintenance/updateArticleCount.php --update --conf %s",
					$this->mWikiId,
					$this->mPHPbin,
					$IP,
					$wgWikiaLocalSettingsPath
				);
				$this->log($cmd);
				wfShellExec( $cmd );

				$this->log( "Copying starter database" );
				$this->setInfoLog( 'OK', wfMsg('autocreatewiki-step7') );
			}
			else {
				$this->log( "No starter database for this language, {$starterDB}" );
			}
		}

		/**
		 * making the wiki founder a sysop/bureaucrat
		 */
		if ( $wgUser->getID() ) {
			$dbw->replace( "user_groups", array( ), array( "ug_user" => $wgUser->getID(), "ug_group" => "sysop" ) );
			$dbw->replace( "user_groups", array( ), array( "ug_user" => $wgUser->getID(), "ug_group" => "bureaucrat" ) );
		}
		$this->log( "Create user sysop/bureaucrat" );

		/**
		 * set hub/category
		 */
		$hub = WikiFactoryHub::getInstance();
		$hub->setCategory( $this->mWikiId, $this->mWikiData[ "hub" ] );
		$this->log( "Wiki added to the category hub " . $this->mWikiData[ "hub" ] );

		$this->setInfoLog( 'OK', wfMsg('autocreatewiki-step8') );

		/**
		 * modify variables
		 */
		$this->addCustomSettings( 0, $wgUniversalCreationVariables, "universal" );

		/**
		 * set variables per language
		 */
		$this->addCustomSettings( $this->mWikiData[ "language" ], $wgLangCreationVariables, "language" );

		/**
		 * use starter when wikia in proper hub
		 */
		if( isset( $this->mStarters[ $this->mWikiData[ "hub" ] ] )
			&& $this->mStarters[ $this->mWikiData[ "hub" ] ]
			&& $this->mWikiData[ "language" ] === "en" ) {

			$wikiMover = WikiMover::newFromIDs(
				$this->mStarters[ $this->mWikiData[ "hub" ] ], /** source **/
				$this->mWikiId /** target **/
			);
			$wikiMover->setOverwrite( true );
			$wikiMover->mMoveUserGroups = false;
			$wikiMover->load();
			$wikiMover->move();

			/**
			 * WikiMove has internal log engine
			 */
            foreach( $wikiMover->getLog( true ) as $log ) {
                $this->log( $log["info"] );
            }
			$this->addCustomSettings( $this->mWikiData[ "hub" ], $wgHubCreationVariables, 'hub' );
		}

		/**
		 * set images timestamp to current date (see: #1687)
		 */
		$dbw->update(
			"image",
			array( "img_timestamp" => date('YmdHis') ),
			"*",
			__METHOD__
		);
		$this->log( "Set images timestamp to current date" );

		/**
		 * commit all in new database
		 */
		$this->setInfoLog( 'OK', wfMsg('autocreatewiki-step9') );

		$dbw->commit();
		/**
		 * add local job
		 */
		$localJob = new AutoCreateWikiLocalJob(	Title::newFromText( NS_MAIN, "Main" ), $this->mWikiData );
		$localJob->WFinsert( $this->mWikiId, $this->mWikiData[ "dbname" ] );

		/**
		 * inform task manager
		 */
		$Task = new LocalMaintenanceTask();
		$Task->createTask(
			array(
				"city_id" => $this->mWikiId,
				"command" => "maintenance/runJobs.php",
				"arguments" => "--type ACWLocal"
			),
			TASK_QUEUED
		);


		$dbw->selectDB( $wgDBname );

		/**
		 * add central job
		 */
		$this->setCentralPages();
		$this->setInfoLog( 'OK', wfMsg('autocreatewiki-step10') );

		if ( self::SEND_WELCOME_MAIL == 1 ) {
			$this->sendWelcomeMail();
			$this->setInfoLog( 'OK', wfMsg('autocreatewiki-step11') );
		}

		/**
		 * show congratulation message
		 */
		$this->setInfoLog( 'OK', wfMsg('autocreatewiki-congratulation')  );

		/**
		 * show total time
		 */
		$info = sprintf( "Total: %F", wfTime() - $startTime );
		$this->log( $info );

		$sSubdomain = ( $this->awcLanguage === 'en' ) ? strtolower( trim( $this->awcDomain ) ) : $this->awcLanguage . "." . strtolower( trim( $this->awcDomain ) );
		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$oTmpl->set_vars( array(
			"domain" => sprintf("%s.%s", $sSubdomain, $this->mDefSubdomain),
		));
		#---
		$sFinishText = $oTmpl->execute("finish");
		$this->setInfoLog('END', $sFinishText);

		wfProfileOut( __METHOD__ );
	}

	private function prepareValues() {
		global $wgContLang;
		wfProfileIn( __METHOD__ );

		$this->mWikiData[ "hub" ]		= $this->awcCategory;
        $this->mWikiData[ "name" ]      = strtolower( trim( $this->awcDomain ) );
        $this->mWikiData[ "title" ]     = trim( $wgContLang->ucfirst( $this->awcName ) . " Wiki" );
        $this->mWikiData[ "language" ]  = $this->awcLanguage;
        $this->mWikiData[ "subdomain" ] = $this->mWikiData[ "name"];
        $this->mWikiData[ "redirect" ]  = $this->mWikiData[ "name"];
		$this->mWikiData[ "dir_part" ]  = $this->mWikiData[ "name"];
		$this->mWikiData[ "dbname" ]    = substr( str_replace( "-", "", $this->mWikiData[ "name"] ), 0, 64);
		$this->mWikiData[ "path" ]      = "/usr/wikia/docroot/wiki.factory";
        $this->mWikiData[ "images" ]    = self::IMGROOT . $this->mWikiData[ "name"];
        $this->mWikiData[ "testWiki" ]  = false;

        if ( isset( $this->mWikiData[ "language" ] ) && $this->mWikiData[ "language" ] !== "en" ) {
			$this->mWikiData[ "subdomain" ] = strtolower( $this->mWikiData[ "language"] ) . "." . $this->mWikiData[ "name"];
			$this->mWikiData[ "redirect" ]  = strtolower( $this->mWikiData[ "language" ] ) . "." . ucfirst( $this->mWikiData[ "name"] );
			$this->mWikiData[ "dbname" ]    = strtolower( str_replace( "-", "", $this->mWikiData[ "language" ] ). $this->mWikiData[ "dbname"] );
			$this->mWikiData[ "images" ]   .= "/" . strtolower( $this->mWikiData[ "language" ] );
			$this->mWikiData[ "dir_part" ] .= "/" . strtolower( $this->mWikiData[ "language" ] );
		}

		wfProfileOut( __METHOD__ );
	}

	/**
	 * create wiki form
	 *
	 * @access public
	 *
	 * @param $subpage Mixed: subpage of SpecialPage
	 */
	public function createWikiForm() {
		global $wgOut, $wgUser, $wgExtensionsPath, $wgStyleVersion, $wgScriptPath, $wgStylePath;
		global $wgCaptchaTriggers, $wgRequest, $wgDBname, $wgMemc;
		wfProfileIn( __METHOD__ );
		#-
		$aTopLanguages = explode(',', wfMsg('autocreatewiki-language-top-list'));
		$aLanguages = $this->getFixedLanguageNames();
		#-
		$hubs = WikiFactoryHub::getInstance();
		$aCategories = $hubs->getCategories();
		#--
		$params = $this->fixSessionKeys();
		if ( empty($params) && empty($this->mPosted) ) {
			$ip = wfGetIP();
			$key = wfMemcKey( self::CACHE_LOGIN_KEY, $wgDBname, $ip );
			$params = $wgMemc->get($key);
		}
		#--
		$f = new FancyCaptcha();
		#--
		$wgOut->addScript( "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$wgStylePath}/common/form.css?{$wgStyleVersion}\" />" );
		/* run template */
		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$oTmpl->set_vars( array(
			"wgUser" => $wgUser,
			"wgExtensionsPath" => $wgExtensionsPath,
			"wgStyleVersion" => $wgStyleVersion,
			"aLanguages" => $aLanguages,
			"aTopLanguages" => $aTopLanguages,
			"aCategories" => $aCategories,
			"wgScriptPath" => $wgScriptPath,
			"mTitle" => $this->mTitle,
			"mPostedErrors" => $this->mPostedErrors,
			"wgStylePath" => $wgStylePath,
			"captchaForm" => $f->getForm(),
			"params" => $params
		));

		#---
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );
		$wgOut->addHtml($oTmpl->execute("create-wiki-form"));
		wfProfileOut( __METHOD__ );
		return;
	}

	/**
	 * create wiki form
	 *
	 * @access public
	 *
	 * @param $subpage Mixed: subpage of SpecialPage
	 */
	public function processCreatePage() {
		global $wgOut, $wgUser, $wgExtensionsPath, $wgStyleVersion, $wgScriptPath, $wgStylePath;
		global $wgCaptchaTriggers, $wgRequest;
		wfProfileIn( __METHOD__ );
		#-
		$aLanguages = $this->getFixedLanguageNames();
		#-
		$hubs = WikiFactoryHub::getInstance();
		$aCategories = $hubs->getCategories();
		#--
		/* run template */
		$wgOut->addScript( "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$wgStylePath}/common/form.css?{$wgStyleVersion}\" />" );
		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$oTmpl->set_vars( array(
			"wgExtensionsPath" => $wgExtensionsPath,
			"wgStyleVersion" => $wgStyleVersion,
			"mTitle" => $this->mTitle,
			"awcName" => $this->awcName,
			"awcDomain" => $this->awcDomain,
			"awcCategory" => $this->awcCategory,
			"awcLanguage" => $this->awcLanguage,
			"subdomain" => ( $this->awcLanguage === 'en' ) ? strtolower( trim( $this->awcDomain ) ) : $this->awcLanguage . "." . strtolower( trim( $this->awcDomain ) ),
			"domain" => $this->mDefSubdomain,
		));

		#---
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );
		$wgOut->addHtml($oTmpl->execute("process-create-form"));
		wfProfileOut( __METHOD__ );
		return;
	}

	/**
	 * set request parameters
	 */
	private function makeRequestParams( $toSession = false) {
		global $wgRequest;
		wfProfileIn( __METHOD__ );
		$aValues = $wgRequest->getValues();
		if ( !empty($aValues) && is_array($aValues) ) {
			foreach ($aValues as $key => $value) {
				$k = trim($key);
				if ( strpos($key, "wiki-") !== false ) {
					$key = str_replace("wiki-", "", $key);
					if ( $toSession === true ) {
						$key = str_replace("-", "_", "awc".ucfirst($key));
						$_SESSION[$key] = strip_tags($value);
					} else {
						$key = str_replace("-", "_", "m".ucfirst($key));
						$this->mPostedErrors[$k] = "";
						$this->$key = strip_tags($value);
					}
				}
			}
		}
		wfProfileOut( __METHOD__ );
	}

	/*
	 *
	 */
	private function fixSessionKeys() {
		global $wgRequest;
		$__params = $wgRequest->getValues();
		$params = array();
		if ( !empty($__params) && is_array($__params) ) {
			foreach ($__params as $key => $value) {
				$k = trim($key);
				if ( strpos($key, "wiki-") !== false ) {
					$params[$key] = htmlspecialchars($value);
				}
			}
		}
		return $params;
	}

	/**
	 * clear session parameters
	 */
	private function clearSessionKeys() {
		wfProfileIn( __METHOD__ );
		$res = 0;
		if ( !empty($_SESSION) && is_array($_SESSION) ) {
			foreach ($_SESSION as $key => $value) {
				if ( preg_match('/^awc/', $key) !== false ) {
					unset($_SESSION[$key]);
					$res++;
				}
			}
		}
		wfProfileOut( __METHOD__ );
		return $res;
	}

	/**
	 * set local variables from session
	 */
	private function setVarsFromSession() {
		wfProfileIn( __METHOD__ );
		$res = 0;
		foreach ($_SESSION as $key => $value) {
			if ( preg_match('/^awc/', $key) !== false ) {
				$this->$key = $value;
				$res++;
			}
		}
		wfProfileOut( __METHOD__ );
		return $res;
	}

	/**
	 * check wiki creation form
	 */
	private function checkWikiCreationParams() {
		global $wgUser;
		$res = true;
		wfProfileIn( __METHOD__ );

		#-- check Wiki's name
		$sResponse = AutoCreateWiki::checkWikiNameIsCorrect($this->mName);
		if ( !empty($sResponse) ) {
			$this->makeError( "wiki-name", $sResponse );
			$res = false;
		}

		#-- check Wiki's domain
		$sResponse = AutoCreateWiki::checkDomainIsCorrect($this->mDomain, $this->mLanguage);
		if ( !empty($sResponse) ) {
			$this->makeError( "wiki-domain", $sResponse );
			$res = false;
		}

		#-- check Wiki's category
		$sResponse = AutoCreateWiki::checkCategoryIsCorrect($this->mCategory);
		if ( !empty($sResponse) ) {
			$this->makeError( "wiki-category", $sResponse );
			$res = false;
		}

		#-- check Wiki's language
		$sResponse = AutoCreateWiki::checkLanguageIsCorrect($this->mLanguage);
		if ( !empty($sResponse) ) {
			$this->makeError( "wiki-language", $sResponse );
			$res = false;
		}

		wfProfileOut( __METHOD__ );
		return $res;
	}

	/**
	 * create account function (see SpecialUserLogin.php to compare)
	 */
	private function addNewAccount() {
		global $wgUser, $wgOut;
		global $wgEnableSorbs, $wgProxyWhitelist;
		global $wgMemc, $wgAccountCreationThrottle;
		global $wgAuth, $wgMinimalPasswordLength;
		global $wgEmailConfirmToEdit;

		wfProfileIn( __METHOD__ );

		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return false;
		}

		$ip = wfGetIP();

		#-- check username
		$sResponse = AutoCreateWiki::checkUsernameIsCorrect($this->mUsername);
		if ( !empty($sResponse) ) {
			$this->makeError( "wiki-username", $sResponse );
		}

		#-- check email
		$sResponse = AutoCreateWiki::checkEmailIsCorrect($this->mEmail);
		if ( !empty($sResponse) ) {
			$this->makeError( "wiki-email", $sResponse );
		}

		#-- check if the date has been choosen
		$sResponse = AutoCreateWiki::checkBirthdayIsCorrect($this->mUser_year, $this->mUser_month, $this->mUser_day);
		if ( !empty($sResponse) ) {
			$this->makeError( "wiki-birthday", $sResponse );
		}

		# Check permissions
		if ( !$wgUser->isAllowed( 'createaccount' ) ) {
			$this->makeError( "wiki-username", wfMsg('autocreatewiki-blocked-username') );
		} elseif ( $wgUser->isBlockedFromCreateAccount() ) {
			$blocker = User::whoIs( $wgUser->mBlock->mBy );
			$block_reason = $wgUser->mBlock->mReason;
			if ( strval( $block_reason ) === '' ) {
				$block_reason = wfMsg( 'blockednoreason' );
			}
			$this->makeError( "wiki-username", wfMsg('autocreatewiki-blocked-username', $ip, $block_reason, $blocker) );
		}

		$ip = wfGetIP();
		if ( $wgEnableSorbs && !in_array( $ip, $wgProxyWhitelist ) && $wgUser->inSorbsBlacklist( $ip ) ) {
			$this->makeError( "wiki-username", wfMsg( 'sorbs_create_account_reason' ) . ' (' . htmlspecialchars( $ip ) . ')' );
		}

		$sResponse = AutoCreateWiki::checkPasswordIsCorrect($this->mUsername, $this->mPassword);
		if ( !empty($sResponse) ) {
			$this->makeError( "wiki-password", $sResponse );
		}

		$sResponse = AutoCreateWiki::checkRetypePasswordIsCorrect($this->mPassword, $this->mRetype_password);
		if ( !empty($sResponse) ) {
			$this->makeError( "wiki-retype-password", $sResponse );
		}

		# Now create a dummy user ($oUser) and check if it is valid
		$name = trim( $this->mUsername );
		$oUser = User::newFromName( $name, 'creatable' );
		if ( is_null( $oUser ) ) {
			$this->makeError( "wiki-username", wfMsg( 'noname' ) );
		} else {
			if ( 0 != $oUser->idForName() ) {
				$this->makeError( "wiki-username", wfMsg( 'userexists' ) );
			}
		}

		if ( $oUser instanceof User) {
			# Set some additional data so the AbortNewAccount hook can be
			# used for more than just username validation
			$oUser->setEmail( $this->mEmail );

			$abortError = '';
			if ( !wfRunHooks( 'AbortNewAccount', array( $oUser, &$abortError ) ) ) {
				// Hook point to add extra creation throttles and blocks
				wfDebug( "LoginForm::addNewAccountInternal: a hook blocked creation\n" );
				$this->makeError( "wiki-blurry-word", $abortError );
			}

			if ( $wgAccountCreationThrottle && $wgUser->isPingLimitable() ) {
				$key = wfMemcKey( 'acctcreate', 'ip', $ip );
				$value = $wgMemc->incr( $key );
				if ( !$value ) {
					$wgMemc->set( $key, 1, 86400 );
				}
				if ( $value > $wgAccountCreationThrottle ) {
					$this->makeError( "wiki-username", wfMsgExt('acct_creation_throttle_hit', $wgAccountCreationThrottle) );
				}
			}

			if ( !$wgAuth->addUser( $oUser, $this->mPassword, $this->mEmail, "" ) ) {
				$this->makeError( "wiki-username", wfMsg('externaldberror') );
			}
		} else {
			$this->makeError( "wiki-username", wfMsg('autocreatewiki-blocked-username') );
		}

		if ( $this->mErrors > 0 ) {
			$oUser = null;
		} else {
			$userBirthDay = strtotime("{$this->mUser_year}-{$this->mUser_month}-{$this->mUser_day}");
			$oUser = $this->initUser( $oUser, false );
			$user_id = $oUser->getID();
			if (!empty($user_id)) {
				$dbw = wfGetDB(DB_MASTER);
				$dbw->update(
					'user',
					array( 'user_birthdate' => date('Y-m-d', $userBirthDay) ),
					array( 'user_id' => $user_id ),
					__METHOD__
				);
			}
			$result = $oUser->sendConfirmationMail();
		}

		wfProfileOut( __METHOD__ );
		return $oUser;
	}

	/**
	 * Actually add a user to the database.
	 * Give it a User object that has been initialised with a name.
	 *
	 * @param $oUser User object.
	 * @param $autocreate boolean -- true if this is an autocreation via auth plugin
	 * @return User object.
	 * @private
	 */
	function initUser( $oUser, $autocreate ) {
		global $wgAuth;
		wfProfileIn( __METHOD__ );

		$oUser->addToDatabase();

		if ( $wgAuth->allowPasswordChange() ) {
			$oUser->setPassword( $this->mPassword );
		}

		$oUser->setEmail( $this->mEmail );
		$oUser->setToken();

		$wgAuth->initUser( $oUser, $autocreate );

		$oUser->setOption( 'rememberpassword', isset($this->mRemember) ? 1 : 0 );
		$oUser->setOption('skinoverwrite', 1);

		$oUser->saveSettings();

		# Update user count
		$ssUpdate = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
		$ssUpdate->doUpdate();

		wfProfileOut( __METHOD__ );
		return $oUser;
	}

	/*
	 * Login after create account
	 */
	private function loginAfterCreateAccount() {
		wfProfileIn( __METHOD__ );
		$apiParams = array(
			"action" => "login",
			"lgname" => $this->mUsername,
			"lgpassword" => $this->mPassword,
		);
		$oApi = new ApiMain( new FauxRequest( $apiParams ) );
		$oApi->execute();
		$aResult = &$oApi->GetResultData();
		error_log("awc-autologin: " . print_r($aResult, true));
		wfProfileOut( __METHOD__ );

		return ( isset($aResult['login']['result']) && ( $aResult['login']['result'] == 'Success' ) );
	}

	/**
	 * create account function (see SpecialUserLogin.php to compare)
	 */
	private function makeError( $key, $msg ) {
		if ( array_key_exists($key, $this->mPostedErrors) ) {
			if ( empty( $this->mPostedErrors[$key] ) ) {
				$this->mPostedErrors[$key]= $msg;
			}
		}
		$this->mErrors++;
	}

	/**
	 * addCustomSettings
	 *
	 * @author tor@wikia-inc.com
	 * @param  string $match
	 * @param  array  $settings
	 * @param  string $type
	 */
	public function addCustomSettings( $match, $settings, $type = 'unknown' ) {
        global $wgUser;
		wfProfileIn( __METHOD__ );

        if( !empty( $match ) && isset( $settings[ $match ] ) && is_array( $settings[ $match ] ) ) {
            $this->log("Found '$match' in $type settings array.");

            /**
			 * switching user for correct logging
			 */
            $oldUser = $wgUser;
            $wgUser = User::newFromName( 'CreateWiki script' );

            foreach( $settings[$match] as $key => $value ) {
                $success = WikiFactory::setVarById( $key, $this->mWikiId, $value );
                if( $success ) {
                    $this->log("Successfully added setting for {$this->mWikiId}: {$key} = {$value}");
                } else {
                    $this->log("Failed to add setting for {$this->mWikiId}: {$key} = {$value}");
                }
            }
			$wgUser = $oldUser;

			$this->log("Finished adding $type settings.");
        } else {
            $this->log("'$match' not found in $type settings array. Skipping this step.");
		}

		wfProfileOut( __METHOD__ );
		return 1;
	}

	/**
	 * set central pages
	 */
	private function setCentralPages() {
		global $wgDBname, $wgUser;

		/**
		 * do it only when run on central wikia
		*/
		if ( $wgDBname != "wikicities" ) {
			$this->log( "Not run on central wikia. Cannot set wiki description page" );
			return false;
		}

		$oldUser = $wgUser;
		/**
		 * set user for all maintenance work on central
		 */
		$wgUser = User::newFromName( 'CreateWiki script' );
		$this->log( "Creating and modifing pages on Central Wikia (as user: " . $wgUser->getName() . ")..." );

		/**
		 * title of page
		 */
		$centralTitleName = $this->mWikiData[ "name"];

		#--- title for this page
		$centralTitle = Title::newFromText( $centralTitleName, NS_MAIN );
		$oHubs = WikiFactoryHub::getInstance();
		$aCategories = $oHubs->getCategories();

		if ( $centralTitle instanceof Title ) {
			#--- and article for for this title
			$this->log( sprintf("[debug] Got title object for page: %s", $centralTitle->getFullUrl( ) ) );
		    $oCentralArticle = new Article( $centralTitle, 0);
		    #--- set category name
	    	$sCategory = $this->mWikiData[ "hub" ];
			if (!empty( $aCategories ) && isset( $aCategories[ $this->mWikiData[ "hub" ] ] ) ) {
		    	$sCategory = $aCategories[ $this->mWikiData[ "hub" ] ];
			}

			$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
			$oTmpl->set_vars( array(
				"data"          => $this->mWikiData,
				"wikid"         => $this->mWikiId,
				"founder"       => $this->mFounder,
				"timestamp"     => $sTimeStamp = gmdate("j F, Y"),
				"category"		=> $sCategory
			));

			if (!$oCentralArticle->exists()) {
				#--- create article
				$this->log( sprintf("[debug] Creating new article: %s", $centralTitle->getFullUrl( ) ) );

				$sPage = $oTmpl->execute("central");

				$this->log( "[debug] Page body formatted, launching doEdit() ..." );
				$oCentralArticle->doEdit( $sPage, "created by autocreate Wiki process", EDIT_FORCE_BOT );
				$this->log( sprintf("Article %s added.", $centralTitle->getFullUrl()) );
			} else {
				#--- update article
				$this->log( sprintf("[debug] Updating existing article: %s", $centralTitle->getFullUrl()) );

				$sContent = $oCentralArticle->getContent();
				$sContent = $oTmpl->execute("central");

				$oCentralArticle->doEdit( $sContent, "modified by autocreate Wiki process", EDIT_FORCE_BOT );
				$this->log( sprintf("Article %s already exists... content added", $centralTitle->getFullUrl()) );
			}
		}
		else {
			$this->log( "ERROR: Unable to create title object for page on Central Wikia: " . $centralTitleName );
			return false;
		}

		/**
		 * add to Template:List_of_Wikia_New
		 */
		$oCentralListTitle = Title::newFromText( self::TEMPLATE_LIST_WIKIA, NS_MAIN );
		if ( $oCentralListTitle instanceof Title ) {
			$oCentralListArticle = new Article( $oCentralListTitle, 0);
			if ( $oCentralListArticle->exists() ) {
				$sContent =  $oCentralListArticle->getContent();
				$sContent .= "{{subst:nw|" . $this->mWikiData['subdomain'] . "|";
				$sContent .= $centralTitleName . "|" . $this->mWikiData['language'] . "}}";

				$oCentralListArticle->doEdit( $sContent, "modified by autocreate Wiki process", EDIT_FORCE_BOT);
				$this->log( sprintf("Article %s modified.", $oCentralListTitle->getFullUrl()) );
			}
			else {
				$this->log( sprintf("Article %s not exists.", $oCentralListTitle->getFullUrl()) );
			}

			#--- add to New_wikis_this_week/Draft
			$oCentralListTitle = Title::newFromText( self::ARTICLE_NEW_WIKIS, NS_MAIN );
			$oCentralListArticle = new Article( $oCentralListTitle, 0);

			if ( $oCentralListArticle->exists() ) {
				$sReplace =  "{{nwtw|" . $this->mWikiData['language']  . "|" ;
				$sReplace .= $aCategories[ $this->mWikiData[ "hub" ] ] . "|" ;
				$sReplace .= $centralTitleName . "|http://" . $this->mWikiData['subdomain'] . ".wikia.com}}\n|}";

				$sContent = str_replace("|}", $sReplace, $oCentralListArticle->getContent());

				$oCentralListArticle->doEdit( $sContent, "modified by autocreate Wiki process", EDIT_FORCE_BOT);
				$this->log( sprintf("Article %s modified.", $oCentralListTitle->getFullUrl()) );
			}
			else {
				$this->log( sprintf("Article %s not exists.", $oCentralListTitle->getFullUrl()) );
			}
		}
		else {
			$this->log( "ERROR: Unable to create title object for page: " . $sCentralListTitle);
			return false;
		}

		if ( strcmp( strtolower( $this->mWikiData['redirect'] ), strtolower( $centralTitleName ) ) != 0 ) {
			#--- add redirect(s) on central
			$oCentralRedirectTitle = Title::newFromText( $this->mWikiData['redirect'], NS_MAIN );
			if ( $oCentralRedirectTitle instanceof Title ) {
				$oCentralRedirectArticle = new Article( $oCentralRedirectTitle, 0);
				if ( !$oCentralRedirectArticle->exists() ) {
					$sContent = "#Redirect [[" . $centralTitleName . "]]";
					$oCentralRedirectArticle->doEdit( $sContent, "modified by autocreate Wiki process", EDIT_FORCE_BOT);
					$this->log( sprintf("Article %s added (redirect to: " . $centralTitleName . ").", $oCentralRedirectTitle->getFullUrl()) );
				} else {
					$this->log( sprintf("Article %s already exists.", $oCentralRedirectTitle->getFullUrl()) );
				}

				if ( ( $this->mWikiData['language'] == 'en' ) && ( !eregi("^en.", $this->mWikiData['subdomain']) ) ) {
					// extra redirect page: en.<subdomain>
					$sCentralRedirectTitle = 'en.' . $this->mWikiData['subdomain'];
					$oCentralRedirectTitle = Title::newFromText( $sCentralRedirectTitle, NS_MAIN );
					if ( !$oCentralRedirectArticle->exists() ) {
						$sContent = "#Redirect [[" . $centralTitleName . "]]";
						$oCentralRedirectArticle->doEdit( $sContent, "modified by autocreate Wiki process", EDIT_FORCE_BOT);
						$this->log( sprintf("Article %s added (extra redirect to: " . $centralTitleName . ").", $oCentralRedirectTitle->getFullUrl()) );
					} else {
						$this->log( sprintf("Article %s already exists.", $oCentralRedirectTitle->getFullUrl()) );
					}
				}
			} else {
				$this->log( "ERROR: Unable to create title object for redirect page: " . $this->mWikiData['redirect'] );
				return false;
			}
		}

		/**
		 * revert back to original User object, just in case
		 */
		$wgUser = $oldUser;

		$this->log( "Creating and modifing pages on Central Wikia finished." );
		return true;
	}

	/**
	 * sendWelcomeMail
	 *
	 * sensd welcome email to founder (if founder has set email address)
	 *
	 * @author eloy@wikia-inc.com
	 * @author adi@wikia-inc.com
	 * @author moli@wikia-inc.com
	 * @access private
	 *
	 * @return boolean status
	 */
	private function sendWelcomeMail() {
		global $wgDevelEnvironment, $wgUser, $wgPasswordSender;

		$oReceiver = $this->mFounder;
		if ( !empty( $wgDevelEnvironment ) ) {
			$oReceiver = $wgUser;
		}

		$sServer = "http://{$this->mWikiData["subdomain"]}." . "wikia.com";
		// set apropriate staff member
		$oStaffUser = AutoCreateWiki::getStaffUserByLang( $this->mWikiData['language'] );
		$oStaffUser = ( $oStaffUser instanceof User ) ? $oStaffUser : User::newFromName( self::DEFAULT_STAFF );

		$sFrom = new MailAddress( $wgPasswordSender, "The Wikia Community Team" );
		$sTo = $oReceiver->getEmail();

		$aBodyParams = array (
			0 => $sServer,
			1 => $oReceiver->getName(),
			2 => $oStaffUser->getRealName(),
			3 => htmlspecialchars( $oStaffUser->getName() ),
			4 => sprintf( "%s%s", $sServer, $oReceiver->getTalkPage()->getLocalURL() ),
		);

		$sBody = $sSubject = null;
		if ( !empty( $this->mWikiData['language'] ) ) {
			// custom lang translation
			$sBody = wfMsgExt("autocreatewiki-welcomebody",
				array( 'language' => $this->mWikiData['language'] ),
				$aBodyParams
			);
			$sSubject = wfMsgExt("autocreatewiki-welcomesubject",
				array( 'language' => $this->mWikiData['language'] ),
				array( $this->mWikiData[ "title" ] )
			);
		}

		if ( is_null( $sBody ) ) {
			// default lang (english)
			$sBody = wfMsg("autocreatewiki-welcomebody", $aBodyParams);
		}

		if ( $sSubject == null ) {
			// default lang (english)
			$sSubject = wfMsg( "autocreatewiki-welcomesubject", array( $this->mWikiData[ 'title' ] ) );
		}

		if ( !empty($sTo) ) {
			$bStatus = $oReceiver->sendMail( $sSubject, $sBody, $sFrom );
			if ( $bStatus === true ) {
				$this->log( "Mail to founder {$sTo} sent." );
			}
			else {
				$this->log( "Mail to founder {$sTo} probably not sent. sendMail returned false." );
			}
		} else {
			$this->log( "Founder email is not set. Welcome email is not sent" );
		}
	}

	/**
	 * common log function
	 */
	private function log( $info ) {
		global $wgOut, $wgUser, $wgErrorLog;

		$oldValue = $wgErrorLog;
		$wgErrorLog = true;
		$info = sprintf( "%s: %F", $info, wfTime() - $this->mCurrTime );
		Wikia::log( __METHOD__, "", $info );
		$wgErrorLog = $oldValue;
		$this->mCurrTime = wfTime();
	}

	/**
	 * set log to display info by js AJAX functions
	 */
	private function setInfoLog($msgType, $sInfo) {
		wfProfileIn( __METHOD__ );
		$aParams = 	array (
			'awcName' => $this->awcName,
			'awcDomain' => $this->awcDomain,
			'awcCategory' => $this->awcCategory,
			'awcLanguage' => $this->awcLanguage
		);
		$aInfo = array( 'type' => $msgType, 'info' => $sInfo );
		$key = AutoCreateWiki::logMemcKey ("set", $aParams, $aInfo);
		wfProfileOut( __METHOD__ );
		return $key;
	}

	/**
	 * set form fields values to memc
	 */
	private function setValuesToSession() {
		global $wgDBname, $wgRequest,$wgMemc;
		$params = $this->fixSessionKeys();
		if (!empty($params)) {
			$ip = wfGetIP();
			$key = wfMemcKey( self::CACHE_LOGIN_KEY, $wgDBname, $ip );
			if ( !$value ) {
				$wgMemc->set( $key, $params, 30);
			}
		}
	}

	/**
	 * prepareTest, clear test database
	 */
	private function prepareTest() {

		global $wgContLang;

		$languages = array( "de", "en", "pl", "fr", "es" );
		shuffle( $languages );

		$this->mWikiData[ "hub" ]		= rand( 1, 19 );
        $this->mWikiData[ "name"]       = strtolower( trim( self::TESTDB ) );
        $this->mWikiData[ "title" ]     = trim( $wgContLang->ucfirst( self::TESTDB ) . " Wiki" );
        $this->mWikiData[ "language" ]  = array_shift( $languages );
        $this->mWikiData[ "subdomain" ] = $this->mWikiData[ "name"];
        $this->mWikiData[ "redirect"]   = $this->mWikiData[ "name"];
		$this->mWikiData[ "dir_part"]   = $this->mWikiData[ "name"];
		$this->mWikiData[ "dbname"]     = substr( str_replace( "-", "", $this->mWikiData[ "name"] ), 0, 64);
		$this->mWikiData[ "path"]       = "/usr/wikia/docroot/wiki.factory";
        $this->mWikiData[ "images"]     = self::IMGROOT . $this->mWikiData[ "name"];
        $this->mWikiData[ "testWiki"]   = true;

        if ( isset( $this->mWikiData[ "language" ] ) && $this->mWikiData[ "language" ] !== "en" ) {
			$this->mWikiData[ "subdomain" ] = strtolower( $this->mWikiData[ "language"] ) . "." . $this->mWikiData[ "name"];
			$this->mWikiData[ "redirect" ]  = strtolower( $this->mWikiData[ "language" ] ) . "." . ucfirst( $this->mWikiData[ "name"] );
			$this->mWikiData[ "dbname" ]    = strtolower( str_replace( "-", "", $this->mWikiData[ "language" ] ). $this->mWikiData[ "dbname"] );
			$this->mWikiData[ "images" ]   .= "/" . strtolower( $this->mWikiData[ "language" ] );
			$this->mWikiData[ "dir_part" ] .= "/" . strtolower( $this->mWikiData[ "language" ] );
		}

		/**
		 * drop test table
		 */
		$dbw = wfGetDB( DB_MASTER );
		$dbw->query( sprintf( "DROP DATABASE IF EXISTS %s", $this->mWikiData[ "dbname"] ) );

		/**
		 * clear wikifactory tables: city_list, city_variables, city_domains
		 */
		$city_id = WikiFactory::DBtoID( $this->mWikiData[ "dbname"] );
		if( $city_id ) {
			$dbw->begin();
			$dbw->delete(
				wfSharedTable( "city_domains" ),
				array( "city_id" => $city_id ),
				__METHOD__
			);
			$dbw->delete(
				wfSharedTable( "city_domains" ),
				array( "city_domain" => sprintf("%s.%s", $this->mWikiData[ "subdomain" ], "wikia.com" ) ),
				__METHOD__
			);
			$dbw->delete(
				wfSharedTable( "city_domains" ),
				array( "city_domain" => sprintf("www.%s.%s", $this->mWikiData[ "subdomain" ], "wikia.com" ) ),
				__METHOD__
			);
			$dbw->delete(
				wfSharedTable( "city_variables" ),
				array( "cv_city_id" => $city_id ),
				__METHOD__
			);
			$dbw->delete(
				wfSharedTable( "city_cat_mapping" ),
				array( "city_id" => $city_id ),
				__METHOD__
			);
			$dbw->commit();
		}

		/**
		 * remove image directory
		 */
		if ( file_exists( $this->mWikiData[ "images" ] ) && is_dir( $this->mWikiData[ "images" ] ) ) {
			exec( "rm -rf {$this->mWikiData[ "images" ]}" );
		}
	}

	/**
	 * get number of created Wikis for current day
	 */
	private function countCreatedWikis($iUser = 0) {
		wfProfileIn( __METHOD__ );

		$dbr = wfGetDB( DB_SLAVE );
		$where = array( "date_format(city_created, '%Y%m%d') = date_format(now(), '%Y%m%d')" );
		if ( !empty($iUser) ) {
			$where[] = "city_founding_user = '{$iUser}' ";
		}
		$oRow = $dbr->selectRow(
			wfSharedTable("city_list"),
			array( "count(*) as count" ),
			$where,
			__METHOD__
		);

		wfProfileOut( __METHOD__ );
		return $oRow->count;
	}

	/**
	 * get number of created Wikis by user today
	 */
	private function countCreatedWikisByUser() {
		global $wgUser;
		wfProfileIn( __METHOD__ );

		$iUser = $wgUser->getId();
		$iCount = $this->countCreatedWikis($iUser);

		wfProfileOut( __METHOD__ );
		return $iCount;
	}

	/*
	 * get a list of language names available for wiki request
	 * (possibly filter some)
	 *
	 * @author nef@wikia-inc.com
	 * @return array
	 *
	 * @see Language::getLanguageNames()
	 * @see RT#11870
	 */
	private function getFixedLanguageNames() {
		$languages = Language::getLanguageNames();

		$filter_languages = explode(',', wfMsg('requestwiki-filter-language'));
		foreach ($filter_languages as $key) {
			unset($languages[$key]);
		}
		return $languages;
	}

}
