<?php
/**
 * blog listing for user, something similar to CategoryPage
 *
 * @author Krzysztof Krzyżaniak <eloy@wikia-inc.com>
 */

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "This is MediaWiki extension.\n";
    exit( 1 ) ;
}

$wgHooks[ "ArticleFromTitle" ][] = "BlogListPage::hook";

class BlogListPage extends Article {

	/**
	 * overwritten Article::view function
	 */
	public function view() {
		global $wgOut, $wgUser, $wgRequest, $wgTitle, $wgContLang;

		$feed = $wgRequest->getText( "feed", false );
		if( $feed && in_array( $feed, array( "rss", "atom" ) ) ) {
			$this->showFeed( $feed );
		}
		elseif ( $wgTitle->isSubpage() ) {
			/**
			 * blog article
			 */
			Article::view();
			if( 1 ) {
				$pageid = $this->getLatest();
				$FauxRequest = new FauxRequest( array(
					"action" => "query",
					"list" => "wkvoteart",
					"wkpage" => $this->getLatest(),
					"wkuservote" => true
				));
				$oApi = new ApiMain( $FauxRequest );
				$oApi->execute();
				$aResult = $oApi->GetResultData();

				if( count($aResult['query']['wkvoteart']) > 0 ) {
					if(!empty($aResult['query']['wkvoteart'][ $pageid ]['uservote'])) {
						$voted = true;
					}
					else {
						$voted = false;
					}
					$rating = $aResult['query']['wkvoteart'][ $pageid ]['votesavg'];
				}
				else {
					$voted = false;
					$rating = 0;
				}

				$hidden_star = $voted ? ' style="display: none;"' : '';
				$rating = round($rating * 2)/2;
				$ratingPx = round($rating * 17);
			}
			$tmpl = new EasyTemplate( dirname( __FILE__ ) . '/templates/' );
			$tmpl->set_vars( array(
				"voted" => $voted,
				"rating" => $rating,
				"hidden_star" => $hidden_star,
				"ratingPx" => $ratingPx,
				"edited" => $wgContLang->timeanddate( $this->getTimestamp() )
			) );
			$wgOut->addHTML( $tmpl->execute("footer") );
			$this->showBlogComments();
		}
		else {
			/**
			 * blog listing
			 */
			Article::view();
			$this->showBlogListing();
		}
	}

	/**
	 * display comments connected with article
	 *
	 * @access private
	 */
	private function showBlogComments() {

	}

	/**
	 * take data from blog tag extension and display it
	 *
	 * @access private
	 */
	private function showBlogListing() {
		global $wgOut, $wgRequest, $wgParser, $wgMemc;

		/**
		 * use cache or skip cache when action=purge
		 */
		$user    = $this->mTitle->getDBkey();
		$listing = false;
		$purge   = $wgRequest->getVal( 'action' ) == 'purge';
		$offset  = 0;

		if( !$purge ) {
			$listing  = $wgMemc->get( wfMemcKey( "blog", "listing", $user, $offset ) );
		}

		if( !$listing ) {
			$params = array(
				"author" => $user,
				"count"  => 50,
				"summary" => true,
				"summarylength" => 750,
				"style" => "plain",
				"title" => "Blogs",
				"timestamp" => true,
				"offset" => $offset
			);
			$listing = BlogTemplateClass::parseTag( "<author>$user</author>", $params, $wgParser );
			$wgMemc->set( wfMemcKey( "blog", "listing", $user, $offset ), $listing, 3600 );
		}
		$wgOut->addHTML( $listing );
	}

	/**
	 * generate xml feed from returned data
	 */
	private function showFeed( $format ) {
		global $wgOut, $wgRequest, $wgParser, $wgMemc, $wgFeedClasses, $wgTitle;

		$user    = $this->mTitle->getDBkey();
		$listing = false;
		$purge   = $wgRequest->getVal( 'action' ) == 'purge';
		$offset  = 0;

		wfProfileIn( __METHOD__ );

		if( !$purge ) {
			$listing  = $wgMemc->get( wfMemcKey( "blog", "feed", $user, $offset ) );
		}

		if ( $listing ) {
			$params = array(
				"count"  => 50,
				"summary" => true,
				"summarylength" => 750,
				"style" => "array",
				"title" => "Blogs",
				"timestamp" => true,
				"offset" => $offset
			);

			$listing = BlogTemplateClass::parseTag( "<author>$user</author>", $params, $wgParser );
			$wgMemc->set( wfMemcKey( "blog", "feed", $user, $offset ), $listing, 3600 );

			$feed = new $wgFeedClasses[ $format ](
				"Test title", "Test description", $wgTitle->getFullUrl() );

			$feed->outHeader();
			if( is_array( $listing ) ) {
				foreach( $listing as $item ) {
					$title = Title::newFromText( $item["title"], NS_BLOG_ARTICLE );
					$item = new FeedItem(
						$title->getPrefixedText(),
						$item["description"],
						$item["url"],
						$item["timestamp"],
						$item["author"]
					);
					$feed->outItem( $item );
				}
			}
			$feed->outFooter();
		}
		wfProfileOut( __METHOD__ );
	}

	/**
	 * static entry point for hook
	 *
	 * @static
	 * @access public
	 */
	static public function hook( &$Title, &$Article ) {
		global $wgRequest;

		/**
		 * we are only interested in User_blog:Username pages
		 */
		if( $Title->getNamespace() !== NS_BLOG_ARTICLE ) {
			return true;
		}

		Wikia::log( __METHOD__, "article" );
		$Article = new BlogListPage( $Title );

		return true;
	}

}
