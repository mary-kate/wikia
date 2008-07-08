<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A Special Page extension that moves Wikipedia deleted content
 * @author Andrew Yasinsky andrewy@wikia.com
 */

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Wikilad',
	'author' => 'Andrew Yasinsky',
	'description' => 'Moves deleted Wikipedia content',
);

$wgAvailableRights[] = 'wikilad';
$wgGroupPermissions['staff']['wikilad'] = true;

$dir =  dirname( __FILE__ );
$wgAutoloadClasses['Wikilad'] = $dir . '/SpecialWikilad_body.php';
$wgSpecialPages['wikilad'] = array( /*class*/ 'Wikilad', /*name*/ 'Wikilad', /* permission */false, /*listed*/ true, /*function*/ false, /*file*/ false );
$wgExtensionMessagesFiles['Wikilad'] = $dir . '/SpecialWikilad.i18n.php';

$wgAjaxExportList[] = 'wikiladAjax';

function wikiladAjax() {
	global $wgRequest, $wgUser;
	global $wgOut, $wgTitle, $wgImportSources;
	global $wgImportTargetNamespace;
    global $wgAllowCopyUploads;
    
	$removeArticle = $wgRequest->getVal('removeArticle');
	$getArticleDetails = $wgRequest->getVal('getArticleDetails');
	$notifyUsers = $wgRequest->getVal('notifyUsers');
	$userArray = $wgRequest->getIntArray('userarray');
	
	if( $removeArticle!='' ){
		$dbw =& wfGetDB( DB_MASTER );
		$query = "update `wikilad`.articles set completed=1 where article_id=$removeArticle";
		$result = $dbw->query( $query ) ;
		$dbw->close();
	  	$response = array('status' => 'ok');
	}
	
	if( ( $notifyUsers != '' ) && ( count( $userArray) > 0 ) ){
		$dbw =& wfGetDB( DB_MASTER );
		$query = "update `wikilad`.users set user_notify=1 where user_id in (" . implode(',',$userArray) . ")";
		$result = $dbw->query( $query ) ;
		$dbw->close();
		$response = array('status' => 'ok');
	}
	
	if($getArticleDetails!=''){
		$dbr =& wfGetDB( DB_SLAVE );
		$query = 	$query = "select * from `wikilad`.users where article_id = $getArticleDetails and completed = 0";
		$result = $dbr->query ( $query ) ;
		
		//$msg .= '<div class="hd">Process Article</div>'; 
		$msg .=	'<div class="bd" id="content">';
		
		while( $row = $dbr->fetchObject( $result ) ) {
			$r =	get_object_vars( $row );
			$msg .= '<div class="bddarker"><input type="checkbox" name="userarray[]" value="'.$r['user_id'].'" />notify <b>'.$r['user_name'].'</b></div>';
			$msg .= '<div class="bd">'.$r['user_comment'].'</div>';
		}
		
		$msg .= '</div>';
		$dbr->freeResult( $result );
		$response = array( 'status' => 'ok', 'response' => $msg );
	}

	return new AjaxResponse( Wikia::json_encode( $response ) );
}	 	
		 	