<?php

/**
 * Register when & where user is logged in
 *
 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
 */

if( $wgDefaultExternalStore && $wgEnableExternalStorage ) {
	$wgHooks[ "UserLoginComplete" ][ ] = array( "UserChangesHistory::LoginHistoryHook", 1 /* UserChangesHistory::LOGIN_FORM */ );
	$wgHooks[ "UserLoadFromSessionInfo" ][ ] = array( "UserChangesHistory::LoginHistoryInsert", 0 /* UserChangesHistory::LOGIN_AUTO */ );
	$wgHooks[ "SavePreferences" ][ ] = 'UserChangesHistory::SavePreferencesHook';
	$wgHooks[ 'AddNewAccount' ][ ] = array( 'UserChangesHistory::LoginHistoryHook', 2 /* UserChangesHistory::LOGIN_REGISTRATION */ );

	/**
	 * load file with class
	 */
	$wgAutoloadClasses[ "UserChangesHistory" ] =  dirname(__FILE__) . "/UserChangesHistory.class.php";
}
