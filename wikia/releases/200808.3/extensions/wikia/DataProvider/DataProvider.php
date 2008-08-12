<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Data Provider for Wikia skins
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Inez Korczynski <inez@wikia.com>
 * @author Tomasz Klim <tomek@wikia.com>
 * @author Maciej Brencz <macbre@wikia.com>
 * @author Gerard Adamczewski <gerard@wikia.com>
 * @copyright Copyright (C) 2007 Inez Korczynski, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['other'][] = array(
	'name' => 'DataProvider',
	'description' => 'data provider for wikia skins',
	'author' => 'Inez Korczyński, Tomasz Klim'
);


class DataProvider
{
	private $skin;

	/*
	 * Author: Tomasz Klim (tomek at wikia.com)
	 */
	final public static function &singleton( &$skin = false ) {
		static $instance;
		if ( !isset( $instance ) ) {
			$instance = new DataProvider( $skin );
		}
		return $instance;
	}

	final private function DataProvider( &$skin ) {
		$this->skin =& $skin;
	}

	/*
	 * Author: Tomasz Klim (tomek at wikia.com)
	 */
	final public function /* array */ GetData() {
		return $this->skin->data;
	}

	/*
	 * Author: Tomasz Klim (tomek at wikia.com)
	 *
	 * Deprecated. Use getContext() instead.
	 */
	final public function /* object */ GetSkinObject() {
		return $this->skin;
	}

	/*
	 * Returns original translation or translation used in monobook if original is not found
	 * Author: Gerard, Inez, Tomek
	 */
	final public static function /* string */ Translate( $key ) {
		wfProfileIn( __METHOD__ );
		global $wgLang, $wgSkinTranslationMap;

		if( $wgLang->getCode() == 'en' ) {
			wfProfileOut( __METHOD__ );
			return wfMsg( $key );
		}
		if ( ( $message = $wgLang->getMessage( $key ) ) === false || ( trim( $message ) == '' ) ) {
			if ( array_key_exists( $key, $wgSkinTranslationMap ) ) {
				$message = wfMsg( $wgSkinTranslationMap[ $key ] );
			} else {
				$message = wfMsg( $key );
			}
		}
		if ( !isset( $message ) || trim( $message ) == '' ) {
			$message = $key;
		}

		wfProfileOut( __METHOD__ );
		return $message;
	}

	/*
	 * Author: Inez Korczynski (inez at wikia.com)
	 */
	final public static function /* array */ GetTopFiveArray() {
		wfProfileIn( __METHOD__ );
		global $wgMemc;

		$links = array();
        $links['most_popular'] = 'GetMostPopularArticles';
        $links['most_visited'] = 'GetMostVisitedArticles';
		$links['newly_changed'] = 'GetNewlyChangedArticles';
		$links['highest_ratings'] = 'GetTopVotedArticles';
		$links['community'] = 'GetTopFiveUsers';

		if ( isset ( $_COOKIE['topfive'] ) && isset ( $links[$_COOKIE['topfive']] ) ) {
			$active = $_COOKIE['topfive'];
		} else {
            $active = 'most_visited';
        }

		wfProfileOut( __METHOD__ );
		return array($links, $active);
	}

	/*
	 * Return array of links (href, text, id) for this wiki box
	 * Author: Inez Korczynski (inez at wikia.com)
	 */
	final public static function /* array */ GetThisWiki() {
		wfProfileIn( __METHOD__ );
		global $wgWikiaUsePHPBB;

		$data = array();
		$data['header'] = self::Translate( 'this_wiki' );

		$data['home']['url'] = Skin::makeMainPageUrl();
		$data['home']['text'] = self::Translate( 'home' );

		if (!empty($wgWikiaUsePHPBB)) {
			$data['forum']['url'] = '/forum/';
			$data['forum']['text'] = self::Translate( 'forum' );
		} else {
			$data['forum']['url'] = Skin::makeInternalOrExternalUrl( wfMsgForContent( 'forum-url' ) );
			$data['forum']['text'] = self::Translate( 'forum' );
		}

		$data['randompage']['url'] = Skin::makeSpecialUrl( 'Randompage' );
		$data['randompage']['text'] = self::Translate( 'randompage' );

		$data['help']['url'] = 'http://www.wikia.com/wiki/Help:Tutorial_1';
		$data['help']['text'] = self::Translate( 'helpfaq' );

		$data['joinnow']['url'] = Skin::makeSpecialUrl( 'Userlogin', "type=signup" );
		$data['joinnow']['text'] = self::Translate('joinnow');

		wfProfileOut( __METHOD__ );
		return $data;
	}

	/*
	 * Return array of links (href, text, id) for my stuff box
	 * Author: Inez Korczynski (inez at wikia.com)
	 */
	final public function /* array */ GetMyStuff() {
		wfProfileIn( __METHOD__ );

		$links = array();
		if ( !is_null( $this->skin ) ) {
			$links_temp = $this->skin->data['personal_urls'];
			unset($links_temp['login']);
			unset($links_temp['switchskin']);

			foreach ( $links_temp as $key => $val ) {
				$links[] = array( 'id' => $key, 'href' => $val['href'], 'text' => $val['text'] );
			}
		}

		wfProfileOut( __METHOD__ );
		return $links;
	}


	/*
	 * Return array of links (href, text, id) for expert tools box
	 * Author: Inez Korczynski (inez at wikia.com)
	 */
	final public function /* array */ GetExpertTools() {
		wfProfileIn( __METHOD__ );

		$links = array();

		# Create page
		$url = Skin::makeSpecialUrl( 'Createpage' );
		$text = self::Translate('createpage');
		$id = 'createpage';
		$links[] = array('url' => $url, 'text' => $text, 'id' => $id);

		# Recent changes
		$url = SpecialPage::getTitleFor( 'Recentchanges' )->getLocalURL();
		$text = self::Translate('recentchanges');
		$id = 'recentchanges';
		$links[] = array('url' => $url, 'text' => $text, 'id' => $id);

		# Live wiki help
		$url = Skin::makeInternalOrExternalUrl( wfMsgForContent( 'irc-url' ) );
		$text = self::Translate( 'irc' );
		$links[] = array('url' => $url, 'text' => $text, 'id' => 'irc-url');

		if ( !is_null( $this->skin) && !empty($this->skin->data['nav_urls']) ) {
			foreach( $this->skin->data['nav_urls'] as $key => $val ) {
				if( !empty( $val ) && $key != 'mainpage' && $key != 'print' ) {
					$links[] = array( 'url' => $val['href'], 'text' => self::Translate( $key ), 'id' => $key );
				}
			}
		}

		if ( !is_null( $this->skin) && !empty($this->skin->data['feeds']) ) {
			foreach( $this->skin->data['feeds'] as $key => $val ) {
				if( !empty( $val ) && $key != 'mainpage' && $key != 'print' ) {
					$links[] = array( 'url' => $val['href'], 'text' => $val['text'], 'id' => $key );
				}
			}
		}

		wfProfileOut( __METHOD__ );
		return $links;
	}


	/*
	 * Return array of top voted articles
	 * Author: Inez Korczynski (inez at wikia.com)
	 */
	final public static function /* array */ GetTopVotedArticles($limit = 7) {
		wfProfileIn( __METHOD__ );
		global $wgMemc;

		$memckey = wfMemcKey("TopVoted", $limit);
		$results = $wgMemc->get( $memckey );

		if( !is_array( $results ) ) {

			$oApi = new ApiMain( new FauxRequest( array( "action" => "query", "list" => "wkvoteart", "wklimit" => $limit * 2, "wktopvoted" => 1 ) ) );
			$oApi->execute();
			$aResult = &$oApi->GetResultData();

			$results = array();

			if ( count( $aResult['query']['wkvoteart'] ) > 0 ) {
				foreach ( $aResult['query']['wkvoteart'] as $key => $val ) {
					$title = Title::newFromID( $key );

					if( is_object( $title ) ) {
						$article['url'] = $title->getLocalUrl();
						$article['text'] = $title->getPrefixedText();
						$results[] = $article;
					}
				}
			}

			self::removeAdultPages($results);

			$results = array_slice( $results, 0, $limit );
			$wgMemc->set( $memckey, $results, 60 * 60);
		}

		wfProfileOut( __METHOD__ );
		return $results;
	}

	/*
	 * Return array of most popular articles
	 * Author: Inez Korczynski (inez at wikia.com)
	 */
	final public static function /* array */ GetMostPopularArticles($limit = 7) {
		wfProfileIn( __METHOD__ );
		global $wgDBname, $wgMemc;

		$memckey = wfMemcKey("MostPopular", $limit);
		$results = $wgMemc->get( $memckey );

		if ( !is_array( $results ) ) {
			$results = array();
			$templateTitle = Title::newFromText ('Most popular articles', NS_MEDIAWIKI);
			if( $templateTitle->exists() ) {
			    /* take data from MW articles */
				$templateArticle = new Article ($templateTitle);
				$templateContent = $templateArticle->getContent();
				$lines = explode( "\n\n", $templateContent );
				foreach( $lines as $line ) {
					$title = Title::NewFromText( $line );

					if( is_object( $title) ) {
						$article['url'] = $title->getLocalUrl();
						$article['text'] = $title->getPrefixedText();
						$results[] = $article;
					}
				}
			}

			if ( count( $results ) < $limit ) {
			    if ( function_exists("wfGetMostPopularArticlesFromCache") ) {
                    $most_popular = wfGetMostPopularArticlesFromCache($limit, 0);
                    if ( is_array($most_popular) && (!empty($most_popular)) ) {
                        foreach ($most_popular as $row_title => $cnt) {
                            $title = Title::makeTitleSafe( NS_MAIN, $row_title );

                            if(is_object($title)) {
                                if(wfMsg("mainpage") != $title->getText()) {
                                    $article['url'] = $title->getLocalUrl();
                                    $article['text'] = $title->getPrefixedText();
                                    $results[] = $article;
                                }
                            }
                        }
                    }
                }
			}

			self::removeAdultPages($results);

            if (!empty($results)) {
			    $results = array_slice( $results, 0, $limit );
            }
			$wgMemc->set( $memckey, $results, 60 * 60 * 3);
		}


		wfProfileOut( __METHOD__ );
		return $results;
	}

	/*
	 * Return array of most visited articles
	 * Author: Inez Korczynski (inez at wikia.com)
	 */
	final public static function /* array */ GetMostVisitedArticles($limit = 7) {
		wfProfileIn( __METHOD__ );
		global $wgDBname, $wgMemc;

		$memckey = wfMemcKey("MostVisited", $limit);

		$results = $wgMemc->get( $memckey );

		if ( !is_array( $results ) ) {
            /* take data from 'page_visited' table */
            $query = "SELECT page_namespace, page_title, page_id, count as cnt FROM page, page_visited WHERE page_namespace = 0 and article_id = page_id ORDER BY cnt DESC";
            self::GetTopContentQuery($results, $query, $limit, 'page_visited');

			if ( count( $results ) < $limit ) {
			    if ( function_exists("wfGetMostPopularArticlesFromCache") ) {
                    $most_popular = wfGetMostPopularArticlesFromCache($limit, 0);
                    if ( is_array($most_popular) && (!empty($most_popular)) ) {
                        foreach ($most_popular as $row_title => $cnt) {
                            $title = Title::makeTitleSafe( NS_MAIN, $row_title );

                            if(is_object($title)) {
                                if(wfMsg("mainpage") != $title->getText()) {
                                    $article['url'] = $title->getLocalUrl();
                                    $article['text'] = $title->getPrefixedText();
                                    $results[] = $article;
                                }
                            }
                        }
                    }
                }
			}

            self::removeAdultPages($results);

            if (!empty($results)) {
                $results = array_slice ($results, 0, $limit);
            }

			$wgMemc->set( $memckey, $results, 60 * 60 * 3);
		}

		wfProfileOut( __METHOD__ );
		return $results;
	}

	/*
	 * Return array of newly changed articles
	 * Author: Inez Korczynski (inez at wikia.com)
	 */
	final public static function /* array */ GetNewlyChangedArticles($limit = 7) {
		wfProfileIn( __METHOD__ );
		global $wgDBname, $wgMemc;

		$memckey = wfMemcKey("NewlyChanged", $limit);
		$results = $wgMemc->get( $memckey );

		if ( !is_array( $results ) ) {

			$query = "SELECT page_namespace, page_title FROM page WHERE page_namespace = 0 ORDER BY page_latest DESC";
			$dbr = &wfGetDB( DB_SLAVE );
			$res = $dbr->query( $dbr->limitResult($query, $limit * 2, 0) );

			$results = array();

			while ( $row = $dbr->fetchObject($res) ) {
				$title = Title::makeTitleSafe( $row->page_namespace, $row->page_title );

				if ( is_object( $title ) ) {
					$article['url'] = $title->getLocalUrl();
					$article['text'] = $title->getPrefixedText();
					$results[] = $article;
				}
			}
			$dbr->freeResult( $res );

			self::removeAdultPages($results);

			$results = array_slice( $results, 0, $limit );
			$wgMemc->set($memckey, $results, 60 * 10);
		}

		wfProfileOut( __METHOD__ );
		return $results;
	}

	/*
	 * Return array of top five users
	 * Author: Inez Korczynski (inez at wikia.com)
	 */
	final public static function /* array */ GetTopFiveUsers($limit = 7) {
		wfProfileIn( __METHOD__ );
		global $wgDBname, $wgMemc;

		$memckey = wfMemcKey("TopFiveUsers", $limit);
		$results = $wgMemc->get( $memckey );

		if ( !is_array( $results ) ) {
			$query = "select rev_user, rev_cnt as cnt from user_rev_cnt where rev_user != 0 and rev_user NOT IN (SELECT ug_user FROM user_groups WHERE ug_group = 'staff')  order by rev_cnt desc";
			$dbr = &wfGetDB( DB_SLAVE );
			if ($dbr->tableExists("user_rev_cnt") === false) {
				$query = "select rev_user, cnt from ";
				$query .= "(SELECT rev_user, count(0) as cnt FROM revision where rev_user > 0 and rev_user not in (select ug_user from user_groups where ug_group = 'staff') ";
				$query .= "GROUP BY rev_user) as c ";
				$query .= "ORDER BY cnt desc";
			}
			$res = $dbr->query( $dbr->limitResult($query, $limit * 4, 0) );

			$results = array();

			while ( $row = $dbr->fetchObject($res) ) {
				$user = User::newFromID( $row->rev_user );

				if (!$user->isBlocked() && !$user->isBot() && $user->getUserPage()->exists() ) {
					$article['url'] = $user->getUserPage()->getLocalUrl();
					$article['text'] = $user->getName();
					$results[] = $article;
				}
			}
			$dbr->freeResult( $res );

			$results = array_slice( $results, 0, $limit );
			$wgMemc->set($memckey, $results, 60 * 60 * 3);
		}

		wfProfileOut( __METHOD__ );
		return $results;
	}

	/*
	 * Return array of top content
	 * Author: Inez Korczynski (inez at wikia.com)
	 */
	final private static function /* array */ GetTopContentQuery(&$results, $query, $limit, $exists_table = "") {
		wfProfileIn( __METHOD__ );

        $dbr = &wfGetDB( DB_SLAVE );

        /* check if table exists */
        if (!empty($exists_table)) {
            if ($dbr->tableExists($exists_table) === false) {
                return false;
            }
        }
        /* check query */
        if (!empty($query)) {
            $res = $dbr->query( $dbr->limitResult($query, $limit * 2, 0) );

            while($row = $dbr->fetchObject($res)) {
                $title = Title::makeTitleSafe( $row->page_namespace, $row->page_title );

                if(is_object($title)) {
                    if(wfMsg("mainpage") != $title->getText()) {
                        $article['url'] = $title->getLocalUrl();
                        $article['text'] = $title->getPrefixedText();
                        $results[] = $article;
                    }
                }
            }
            $dbr->freeResult( $res );
        }

		wfProfileOut( __METHOD__ );
		return true;
    }

	/*
	 * Return array of user's messages
	 * Author: Piotr Molski (moli at wikia.com)
	 */
	static public function GetUserEventMessages($limit = 1)
	{
		global $wgDBname, $wgShareDB, $wgMessageCache, $wgOut;

		wfProfileIn( __METHOD__ );

		#$memckey = "{$wgDBname}:UserEventMessages";
		#$results = $wgMemc->get( $memckey );

		$oApi = new ApiMain( new FauxRequest( array( "action" => "query", "list" => "wkevents", "wklimit" => $limit) ) );
		$oApi->execute();
		$aResult = &$oApi->GetResultData();

		$results = array();

		if ( count( $aResult['query']['wkevents'] ) > 0 ) {
			#---
			#$wgMessageCache->loadFromDB();
			#---
			foreach ( $aResult['query']['wkevents'] as $eventType => $val )
			{
				#--- title
				if (!empty($val['title']))
				{
					$parseTitle = wfMsg($val['title']);
					if (!empty($parseTitle))
					{
						$val['title'] = $wgOut->parse($parseTitle, false, true);
					}
				}
				#--- content
				if (!empty($val['content']))
				{
					$parseContent = wfMsg($val['content']);
					if (!empty($parseContent))
					{
						$val['content'] = $wgOut->parse($parseContent, false, true);
					}
				}
				$results[] = $val;
			}
		}
		#$wgMemc->set( $memckey, $results, 300 );

		wfProfileOut( __METHOD__ );
		return $results;
	}

	/**
	 * removeAdultPages
	 *
	 * common entry point for removing adult pages
	 * remove all or just depreciate a little choosen according
	 * to wgAdultPagesDepreciationLevel global variable
	 * pages present in global wgAdultPages are removed
	 *
	 * @access public
	 * @author ppiotr
	 *
	 * @param array $articles: data to check out (by reference!)
	 */
	public static function removeAdultPages(&$articles)
	{
		wfProfileIn( __METHOD__ );

		global $wgAdultPages, $wgAdultPagesDepreciationLevel;
		if (!empty($wgAdultPages) && is_array($wgAdultPages))
		{
			if (!empty($wgAdultPagesDepreciationLevel) && is_integer($wgAdultPagesDepreciationLevel))
			{
				$articles = self::removeAdultPagesGradually($articles, $wgAdultPages, $wgAdultPagesDepreciationLevel);
			} else
			{
				$articles = self::removeAdultPagesAtOnce($articles, $wgAdultPages);
			}
		}

		wfProfileOut( __METHOD__ );
	}

	/**
	 * removeAdultPagesAtOnce
	 *
	 * remove from data pages present in to_remove
	 *
	 * @access public
	 * @author ppiotr
	 *
	 * @param array $data: data to sort out
	 * @param array $to_remove: records to remove from data
	 *
	 * @param array
	 */
	public static function removeAdultPagesAtOnce($data, $to_remove)
	{
		wfProfileIn( __METHOD__ );

		$output = array();
		foreach ($data as $row)
		{
			if (in_array($row['text'], $to_remove))
			{
				wfDebug(sprintf("%s: page '%s' removed.\n", __METHOD__, $row['text']));
			} else
			{
				$output[] = $row;
			}
		}

		wfProfileOut( __METHOD__ );
		return $output;
	}

	/**
	 * removeAdultPagesGradually
	 *
	 * move within data pages present in to_remove
	 * move them by depreciate_by points down
	 *
	 * @access public
	 * @author ppiotr
	 *
	 * @param array $data: data to sort out
	 * @param array $to_remove: records to move within data
	 * @param array $depreciate_by: move them by this many points down
	 *
	 * @param array
	 */
	public static function removeAdultPagesGradually($data, $to_remove, $depreciate_by)
	{
		wfProfileIn( __METHOD__ );

		$depreciated_to = array();
		$i = 0;

		$output = array();
		foreach ($data as $row)
		{
			if (in_array($row['text'], $to_remove))
			{
				$depreciated_to[$i + $depreciate_by] = $row;

				wfDebug(sprintf("%s: page '%s' will be moved to #%d.\n", __METHOD__, $row['text'], $i + $depreciate_by));
			} else
			{
				$output[] = $row;
			}

			$i++;
		}

		$j = 0;

		$output2 = array();
		while ($j < $i)
		{
			if (!empty($depreciated_to[$j]))
			{
				$output2[] = $depreciated_to[$j];

				wfDebug(sprintf("%s: page '%s' put at #%d from depreciated_to array.\n", __METHOD__, $depreciated_to[$j]['text'], $j));
			} else
			{
				$output2[] = array_shift($output);
			}

			$j++;
		}
		$output = $output2;

		wfProfileOut( __METHOD__ );
		return $output;
	}
}