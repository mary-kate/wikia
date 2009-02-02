<?php

/**
 * BlogLockdown extension - implements restrictions on blog namespaces
 *
 * @file
 * @ingroup Extensions
 *
 * @author Krzysztof Krzyżaniak (eloy) <eloy@wikia-inc.com>
 *
 * @copyright Copyright © 2008 Krzysztof Krzyżaniak, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0
 *
 * Based on Lockdown extension from Daniel Kinzler, brightbyte.de
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

$wgHooks['userCan'][] = 'BlogLockdown::userCan';

class BlogLockdown {

	public static function userCan( $title, $user, $action, &$result ) {

		$namespace = $title->getNamespace();

		/**
		 * here we only handle Blog articles, everyone can read it
		 */
		if( $namespace != NS_BLOG_ARTICLE && $namespace != NS_BLOG_ARTICLE_TALK ) {
			$result = true;
			return true;
		}

		$owner = BlogArticle::getOwner( $title );
		$username = $user->getName();
		$isOwner = (bool)( $username == $owner );
		$isArticle = (bool )( $namespace == NS_BLOG_ARTICLE );

		/**
		 * returned values
		 */
		$result = array();
		$return = false;

		switch( $action ) {
			case "move":
				$result = array();
				$return = false;
				break;

			case "read":
				$result = true;
				$return = true;
				break;

			/**
			 * creating permissions:
			 * 	-- article can be created only by blog owner
			 *	-- comment can be created by everyone
			 */
			case "create":
				if( $isArticle) {
					$return = ( $username == $owner );
					$result = ( $username == $owner );
				}
				else {
					$result = true;
					$return = true;
				}
				break;

			/**
			 * edit permissions -- owner of blog and one who has
			 *	 "blog-articles-edit" permission
			 */
			case "edit":
				if( $isArticle && ( $user->isAllowed( "blog-articles-edit" ) || $isOwner ) ) {
					$result = true;
					$return = true;
				}
				break;

			case "delete":
				if( !$isArticle && $user->isAllowed( "blog-comments-delete" ) ) {
					$result = true;
					$return = true;
				}
				if( $user->isAllowed( 'delete' ) ) {
					$result = true;
					$return = true;
				}
				break;

			default:
				/**
				 * for other actions we demand that user has to be logged in
				 */
				if( $user->isAnon( ) ) {
					$result = array( "{$action} is forbidden for anon user" );
					$return = false;
				}
				else {
					if( $username != $owner ) $result = array();
					$return = ( $username == $owner );
				}
		}
		Wikia::log( __METHOD__, $action, "result: {$result}; return: {$return}");
		return $return;
	}
}
