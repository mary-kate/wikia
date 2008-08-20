<?php

/**
 * Register when & where user is logged in and what was changed in
 * user preferences. Could be used for restoring badly saved preferences (undo).
 *
 * @author Krzysztof Krzyżaniak <eloy@wikia-inc.com>
 */

/**
CREATE TABLE `user_login_history` (
  `user_id` int(5) unsigned NOT NULL,
  `city_id` int(9) unsigned default '0',
  `ulh_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ulh_from` tinyint(4) default '0',
  KEY `idx_user_login_history_timestamp` (`ulh_timestamp`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_history` (
  `user_id` int(5) unsigned NOT NULL,
  `user_name` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `user_real_name` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `user_password` tinyblob NOT NULL,
  `user_newpassword` tinyblob NOT NULL,
  `user_email` tinytext NOT NULL,
  `user_options` blob NOT NULL,
  `user_touched` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
  `user_token` varchar(32) character set latin1 collate latin1_bin NOT NULL default '',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  KEY `user_name` (`user_name`(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8

**/

/**
 * static methods, wait for PHP with namespaces
 */
class UserChangesHistory {

	const CLUSTER = "archive1";
	const LOGIN_AUTO = 0;
	const LOGIN_FORM = 1;

	/**
	 * LoginHistoryInsert
	 *
	 */
	static public function LoginHistoryHook( $from, $User ) {
		global $wgCityId; #--- private wikia identifier, you can use wgDBname

		wfProfileIn( __METHOD__ );

		/**
		 * if user id is empty it means that user object is not loaded
		 * store information only for registered users
		 */
		$id = $User->getId();
		if ( $id ) {

			$external = new ExternalStoreDB();
			$dbw = $external->getMaster( self::CLUSTER );
			$dbw->selectDb( "dbstats" );
			$dbw->begin();
			$status = $dbw->insert(
				"user_login_history",
				array(
					"user_id"   => $id,
					"city_id"   => $wgCityId,
					"ulh_from"  => $from
				),
				__METHOD__
			);
			if( $status ) {
				$dbw->commit();
			}
			else {
				$dbw->rollback();
			}
		}

		wfProfileOut( __METHOD__ );

		return true;
	}


	/**
	 * SavePreferencesHook
	 *
	 * Store row from user table before changes of preferences are saved.
	 * Row is stored in external storage archive1
	 */
	static public function SavePreferencesHook( $preferences, $User, $msg ) {

		wfProfileIn( __METHOD__ );

		$id = $User->getId();
		if( $id ) {
			/**
			 * caanot use "insert from select" because we got two different db
			 * clusters. But we should have all user data already loaded.
			 */

			$external = new ExternalStoreDB();
			$dbw = $external->getMaster( self::CLUSTER );

			/**
			 * so far encodeOptions is public by default but could be
			 * private in future
			 */
			$dbw->selectDb( "dbstats" );
			$dbw->begin();
			$status = $dbw->insert(
				"user_history",
				array(
					"user_id"          => $id,
					"user_name"        => $User->mName,
					"user_real_name"   => $User->mRealName,
					"user_password"    => $User->mPassword,
					"user_newpassword" => $User->mNewpassword,
					"user_email"       => $User->mEmail,
					"user_options"     => $User->encodeOptions(),
					"user_touched"     => $User->mTouched,
					"user_token"       => $User->mToken,
				),
				__METHOD__
			);
			if( $status ) {
				$dbw->commit();
			}
			else {
				$dbw->rollback();
			}
		}

		wfProfileOut( __METHOD__ );

		return true;
	}

}
