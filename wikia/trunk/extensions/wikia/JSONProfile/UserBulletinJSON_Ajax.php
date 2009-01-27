<?php
/*
 * Ajax Functions used by Wikia extensions
 */


$wgAjaxExportList [] = 'wfGetUserBulletinsJSON';

function wfGetUserBulletinsJSON($user_name, $count=18, $type = -1, $page = 1){  
	global $IP, $wgUser;
	$user_name = urldecode( $user_name );
	$id = User::idFromName( $user_name );

	$rp = new ProfilePhoto( $id);
	
	$p = new ProfilePrivacy();
	$p->loadPrivacyForUser( $user_name );
		
	if( $user_name == $wgUser->getName() || ( $p->getPrivacyCheckForUser("VIEW_FULL") && $p->getPrivacyCheckForUser("VIEW_BULLETINS") ) ){	
		$b = new UserBulletinList( $user_name );
		$bulletins = $b->getList($type, $count, $page);
	}else{
		$bulletins = array();	
	}
	
	$profile_JSON_array["activity"] = array(
			"time" => time(),
			"user_name_display"=>user_name_display($id, $user_name),
			"r_avatar"=>$rp->getProfileImageURL("l"),
			"title"=>"Recent Activity",
			"activity"=>$bulletins,
	);

	$profile_JSON_array["type"] = $type;

	$types = UserBulletin::$bulletin_types;
	foreach( $types as $id => $type ){
		$type_array[] = array( "id" => $id, "type" => $type );
	}
	$profile_JSON_array["types"] = $type_array;
	
	$text = "write_activity(" . jsonify($profile_JSON_array) . ");";
	$response = new AjaxResponse( $text );
	$response->setContentType( "application/javascript; charset=utf-8" ); 
	return $response;
}
