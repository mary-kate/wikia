<?php
/**
 * A Special Page extension that displays Wiki Google Webtools stats.
 * This page can be accessed from Special:Webtools
 * @addtogroup Extensions
 * @author Andrew Yasinsky <andrewy@wikia.com>
 * This extension will funnel all content pages and their derivatives back into single page, but only for traffic identified as bot and hence not cached.
 * (Artur and Jason promise to set some header in Varnish once it is installed)
 * It is only working with English wikis at the moment
 * If page is Content and not / root
 * a.	If page is Main_Page but alternative Main_Page is set in Mediawiki:Mainpage -> redirect 301 to  set in Mediawiki:Mainpage
 * b.	If page is Derivative of a pageN with #REDIRECT [[page]] -> redirect back to pageN
 * The above is also true with pages accessed through index.php?title=page and limited only to content display pages.
 */

if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install this extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/wikia/Userengagement/SpecialFunnel.php" );
EOT;
    exit( 1 );
}

$wgExtensionFunctions[] = 'wfSpecialFunnel';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Funnel',
	'author' => 'Andrew Yasinsky',
	'description' => 'Funnel Redirects',
);

$bot = false;

$wgSpecialPages['Funnel'] = array( /*class*/ 'Funnel', /*name*/ 'Funnel', false, false );

function wfSpecialFunnel() {
  //init
}

function wfFunnel(){
  //this function will redirect all bot requests to a singlgle page copy if several copy of same content is available	

  global $IP, $wgMessageCache, $wgAutoloadClasses, $wgSpecialPages, $wgOut, $wgRequest, $wgTitle, $wgLanguageCode;
  $prefix = '/wiki/';
  $isContent = $wgTitle->getNamespace() == NS_MAIN;
  $params = array();
  
  parse_str( $_SERVER['QUERY_STRING'], $params );
  
  $canonicalUrl = $wgTitle->mUrlform; 
  $wgMainpage = str_replace( ' ', '_', trim( wfMsgForContent( 'mainpage' ) ) );

  if( ( $wgLanguageCode == 'en' ) && ( trim( $params['title'] ) != '' ) && ( $isContent ) ){ 
  	if( ( $canonicalUrl == 'Main_Page') && ( $wgMainpage != '' ) && ( $wgMainpage != 'Main_Page' ) ){
  	//this is page request via index.php?title=Blah then redirect in good way and only if title is only one otherwise we dont care
		$url = 'http://'.$_SERVER['SERVER_NAME'].$prefix.$wgMainpage;
	  	header( "Location: {$url}", true, 301);
		exit(0);
	}	
  	 
  	if( ( strtolower( $params['title'] ) != 'index.php' ) && ( strpos( strtolower( $_SERVER['SCRIPT_URL'] ), 'index.php' ) != false ) && ( count( $params ) == 1 ) ) {
		//NOTE this is redundant as robots.txt prevent google and such from index.php? pages 	
		//this is page request via index.php?title=Blah then redirect in good way and only if title is only one otherwise we dont care
		$url = 'http://'.$_SERVER['SERVER_NAME'] . $prefix . $canonicalUrl;
	  	header( "Location: {$url}", true, 301 );
		exit( 0 );
	}
	  
	if( ( $params['title'] != $canonicalUrl )) {
		//final check see if this is #redirect page
		$url = 'http://'.$_SERVER['SERVER_NAME'] . $prefix . $canonicalUrl;
	  	header( "Location: {$url}", true, 301 );
		exit( 0 );	
	}
 }
 
 return true;	 
}

if($bot) {
 $wgHooks['BeforePageDisplay'][] = array( 'wfFunnel', array() );
}