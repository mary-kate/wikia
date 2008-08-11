<?php

/**
 * Register when & where user is logged in
 *
 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
 */

#$wgHooks[ "UserLoginComplete" ][ ] = array( "UserChangesHistory::LoginHistoryInsert", "form" );
#$wgHooks[ "UserLoadFromSession" ][ ] = array( "UserChangesHistory::LoginHistoryInsert", "auto" );
$wgHooks[ "SavePreferences" ][ ] = array( "UserChangesHistory::SavePreferencesHook" );

/**
 * load file with class
 */
$wgAutoloadClasses[ "UserChangesHistory" ] =  dirname(__FILE__) . "/UserChangesHistory.class.php";
