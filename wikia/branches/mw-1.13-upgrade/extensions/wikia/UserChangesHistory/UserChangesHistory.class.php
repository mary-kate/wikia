<?php

/**
 * Register when & where user is logged in and what was changed in
 * user preferences. Could be used for restoring badly saved preferences (undo).
 *
 * @author Krzysztof Krzyżaniak <eloy@wikia-inc.com>
 */

/**
 * static methods, wait for PHP with namespaces
 */
class UserChangesHistory {

	/**
	 * LoginHistoryInsert
	 *
	 */
	static public function LoginHistoryInsert( $from, $User ) {
		global $wgCityId; #--- private wikia identifier, you can use wgDBname

		wfProfileIn( __METHOD__ );

		/**
		 * if user id is empty it means that user object is not loaded
		 * store information only for registered users
		 */
		$user_id = $User->getId();
		if ( $user_id ) {
			$dbw = wfGetDB( DB_MASTER );
			$dbw->insert(
				wfSharedTable( "user_login_history" ),
				array(
					"user_id" => $user_id,
					"city_id" => $wgCityId,
					"login_from" => $from
				),
				__METHOD__
			);
		}

		wfProfileOut( __METHOD__ );

		return true;
	}


	static public function SavePreferencesInsert( $preferences, $User, $msg ) {

	}

}
