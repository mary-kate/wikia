<?php
/**
 * A Special Page extension that displays Wiki Google Webtools stats.
 *
 * This page can be accessed from Special:Webtools
 *
 * @addtogroup Extensions
 *
 * @author Andrew Yasinsky <nadrewy@wikia.com>
 */

if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install this extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/wikia/Wikireadr/SpecialWikireadr.php" );
EOT;
        exit( 1 );
}

$wgExtensionFunctions[] = 'wfSpecialWikireadr';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Wikireadr',
	'author' => 'Andrew Yasinsky',
	'description' => 'Incremental Wiki reading from Wikipedia',
);

#--- permissions
$wgAvailableRights[] = 'wikireadr';
$wgGroupPermissions['staff']['wikireadr'] = true;

$wgSpecialPages['wikireadr'] = array('Wikireadr', 'wikireadr', false,false);
$wgAutoloadClasses['Wikireadr'] = dirname( __FILE__ ) . '/SpecialWikireadr_body.php';

function wfSpecialWikireadr() {
	global $IP, $wgMessageCache, $wgAutoloadClasses, $wgSpecialPages;

	require_once ('SpecialWikireadr.i18n.php' );
	foreach( efSpecialWikireadrMessages() as $lang => $messages )
		$wgMessageCache->addMessages( $messages, $lang );
}

$wgAjaxExportList[] = 'wikireadrAjax';

//unfortunately these got to be all loaded too

require_once( "$IP/extensions/wikia/Wikireadr/classes/wikireadr.php" );
require_once( "$IP/extensions/wikia/Wikireadr/classes/httpclient.php" );

function wikireadrAjax() {
	global $wgRequest, $wgUser;
	global $wgOut, $wgTitle, $wgImportSources;
	global $wgImportTargetNamespace;
    global $wgAllowCopyUploads;
    
	$interwiki = false;
	$namespace = $wgImportTargetNamespace;
	$frompage = '';
	$history = false;
	$response = array();

	$page = $wgRequest->getVal('pageUrl');
	$wslinksraw = $wgRequest->getVal('inclLinks');
	$wslinks=explode('||',$wslinksraw);
	
	$wsl = array();
	$remred=false;	
	
	
	if(count($wslinks)>0){
	
	 foreach($wslinks as $key => $value){
	 	if( $value != '' ){
	 	 $wsl[] = $value;	
	 	}
	 }
	 if(count($wsl)>0){
	  $remred=true;
	 }else{
	  $remred = false;	
	 }  	
	}
	
	$url = parse_url($page);
	$base = $url['scheme'].'://'.$url['host'].'/wiki/';
	$api = $url['scheme'].'://'.$url['host'].'/w/';
		
	//grab the page
	$seed = new clsWikiReadr();
	$res = $seed->get_page(array('url' => $page, 'media' => 'true', 'wslinks' => $wsl, 'remred' => $remred, 'api' => $api, 'base' => $base ) );
    $response = WikireadrImport($res['content'],$res['title'],0);
    $rt = WikireadrImport($res['talkcontent'],'',1);    

    foreach($res['images'] as $key => $value){

      //import image pages via XML	
      $url = $base . 'Special:Export/' . urlencode(str_replace(' ',"_",utf8_decode($value)));
      $r = $seed->get_page(array('url' => $url, 'media' => 'true', 'wslinks' => array(), 'remred' => false, 'api' => $api, 'base' => $base, 'ismedia' => true) );
      
      
            
      if(!empty($r['content'])){
      	  $rr = WikireadrImport($r['content']);
	    if ( ( $value != '' ) ){
	      $wgAllowCopyUploads = true;
          $img = $seed->get_image( array( 'url' => $base . urlencode(str_replace(' ',"_",utf8_decode($value))),'imagename' => ucfirst( str_replace( $res['imagekey'].':','',str_replace(' ',"_",$value) ) ) , 'orgimg'=>$value,) );
	      $wgAllowCopyUploads = false;
        }
      }
	}
	
	return new AjaxResponse( Wikia::json_encode( $response ) );
}

function importWikireardRevision( &$revision ) {
	$dbw = wfGetDB( DB_MASTER );
	return $dbw->deadlockLoop( array( &$revision, 'importOldWikireardRevision' ) );
}

function WikireadrImport($content,$title='',$ns){
	global $wgUser;	

	if(trim($content)==''){
	  $response = array('response' => wfMsg( 'ws.status.failure' ), 'status' => 'fail','title' => $title);
	}
	
	$source = new ImportStringSource($content);
	
	if( WikiError::isError( $source ) ) {
		$response = array('response' => wfMsg( 'ws.status.failure' ),'status' => 'fail', 'title' => $title);
	} else {
		$importer = new WikiImporter( $source );
		$importer->setTargetNamespace( $ns );
			
		$reporter = new ImportReporter( $importer, '', '' );
		
		$reporter->open();
		$result = $importer->doImport();
		$reporter->close();
	
		if( WikiError::isError( $result ) ) {
		  $response = array('response' => wfMsg( 'ws.status.failure' ), 'status' => 'fail','title' => $title);		
		} else {
		  # Success!
		  $response = array('response' => wfMsg( 'ws.status.success' ), 'status' => 'ok','title' => $title);
		}
	}
	return $response;	
}


