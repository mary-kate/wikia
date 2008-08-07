<?php

/**
 * Register when & where user is logged in
 *
 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
 */

/**
CREATE TABLE `user_login_history` (
  `user_id` int(5) unsigned NOT NULL,
  `city_id` int(9) unsigned default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `login_from` varchar(10) NOT NULL default 'auto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8
**/


$wgHooks[ "UserLoginComplete" ][ ] = array( "UserChangesHistory::LoginHistoryInsert", "form" );
$wgHooks[ "UserLoadFromSession" ][ ] = array( "UserChangesHistory::LoginHistoryInsert", "auto" );
$wgHooks[ "SavePreferences" ][ ] = array( "UserChangesHistory::SavePreferencesInsert" );
