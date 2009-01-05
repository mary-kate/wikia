<?php

/**
 * @addto SpecialPages
 *
 * @author Adrian Wieczorek
 */
class CreateBlogListingPage extends SpecialBlogPage {

	private $mTagBody = '';

	public function __construct() {
		// initialise messages
		wfLoadExtensionMessages( "Blogs" );
		parent::__construct( 'CreateBlogListingPage' /*class*/, '' /*restriction*/, true);
	}

	public function execute() {
		global $wgOut, $wgUser, $wgRequest;

		if( !$wgUser->isLoggedIn() ) {
			$wgOut->showErrorPage( 'create-blog-no-login', 'create-blog-login-required');
			return;
		}

		$this->mTitle = Title::makeTitle( NS_SPECIAL, 'CreateBlogListingPage' );

		$wgOut->setPageTitle( wfMsg('create-blog-listing-title') );

		if($wgRequest->wasPosted()) {
			$this->parseFormData();
			if(count($this->mFormErrors) > 0 || !empty($this->mRenderedPreview)) {
				$this->renderForm();
			}
			else {
				$this->save();
			}
		}
		else {
			if($wgRequest->getVal('article') != null) {
				$this->parseTag(urldecode($wgRequest->getVal('article')));
			}
			$this->renderForm();
		}

	}

	protected function parseFormData() {
		global $wgUser, $wgRequest, $wgOut, $wgParser;

		$this->mFormData['listingTitle'] = $wgRequest->getVal('blogListingTitle');
		$this->mFormData['listingCategories'] = $wgRequest->getVal('wpCategoryTextarea1');
		$this->mFormData['listingAuthors'] = $wgRequest->getVal('blogListingAuthors');
		$this->mFormData['listingSortBy'] = $wgRequest->getVal('blogListingSortBy');
		$this->mFormData['listingPageCategories'] = $wgRequest->getVal('wpCategoryTextarea2');
		$this->mFormData['listingType'] = $wgRequest->getVal('listingType');
		$this->mFormData['isExistingArticleEditAllowed'] = $wgRequest->getVal('articleEditAllowed');

		if(empty($this->mFormData['listingTitle'])) {
			$this->mFormErrors[] = wfMsg('create-blog-empty-title-error');
		}
		else {
			$oPostTitle = Title::newFromText( $this->mFormData['listingTitle'], NS_BLOG_LISTING );

			if(!($oPostTitle instanceof Title)) {
				$this->mFormErrors[] = wfMsg('create-blog-invalid-title-error');
			}
			else {
				$this->mPostArticle = new Article($oPostTitle, 0);
				if($this->mPostArticle->exists() && ($this->mFormData['listingType'] == 'plain') && !$this->mFormData['isExistingArticleEditAllowed']) {
					$this->mFormErrors[] = wfMsg('create-blog-article-already-exists');
				}
			}
		}

		if(!count($this->mFormErrors)) {
			$this->buildTag();
		}

		if(!count($this->mFormErrors) && $wgRequest->getVal('wpPreview')) {
			if($this->mFormData['listingType'] == 'plain') {
					$this->mRenderedPreview = BlogTemplateClass::parseTag($this->mTagBody, array(), $wgParser);
			}
			else {
				$this->mRenderedPreview = '<pre>' . htmlspecialchars($this->mTagBody) . '</pre>';
			}
		}

	}

	protected function renderForm() {
		global $wgOut, $wgScriptPath;

		$wgOut->addScript( '<script type="text/javascript" src="' . $wgScriptPath . '/extensions/wikia/Blogs/js/categoryCloud.js"><!-- categoryCloud js --></script>');

		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );

	$sQuery = "SELECT replace(replace(cl_to,'_News',''),'_Opinions','') AS cl_to, count(*) AS count
 FROM `categorylinks` cl1
 WHERE cl_from in (select page_id from page where page_namespace = '" . NS_BLOG_ARTICLE . "')
 GROUP BY cl_to
 ORDER BY
 count DESC
 LIMIT 0,10";

		$oTmpl->set_vars( array(
			'categoryCloudTitle' => wfMsg('create-blog-listing-blog-post-categories-title'),
			'cloud' => new TagCloud(10, $sQuery),
			'cols' => 10,
			'cloudNo' => 1,
			'textCategories' => (isset($this->mFormData['listingCategories'])) ? $this->mFormData['listingCategories'] : "" )
		);

		$sBlogCategoryCloud = $oTmpl->execute("createPostCategoryCloud");

		$oTmpl->set_vars( array(
			'categoryCloudTitle' => wfMsg('create-blog-listing-page-categories-title'),
			'cloud' => new TagCloud(),
			'cols' => 10,
			'cloudNo' => 2,
			'textCategories' => (isset($this->mFormData['listingPageCategories'])) ? $this->mFormData['listingPageCategories'] : "")
		);

		$sPageCategoryCloud = $oTmpl->execute("createPostCategoryCloud");

		$oTmpl->set_vars( array(
			"title" => $this->mTitle,
			"formErrors" => $this->mFormErrors,
			"formData" => $this->mFormData,
			"preview" => $this->mRenderedPreview,
			"sortByKeys" => BlogTemplateClass::$aBlogParams['order']['pattern'],
			"blogCategoryCloud" => $sBlogCategoryCloud,
			"pageCategoryCloud" => $sPageCategoryCloud )
		);

		$wgOut->addHTML( $oTmpl->execute("createBlogListingForm") );

		return;
	}

	protected function save() {
		global $wgOut;
		if($this->mFormData['listingType'] == 'box') {
			$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
			$oTmpl->set_vars( array(
				"tagBody" => $this->mTagBody)
			);

			$wgOut->addHTML( $oTmpl->execute("createListingConfirm") );
		}
		else {
			$sPageBody = $this->mTagBody;

			if(!empty($this->mFormData['listingPageCategories'])) {
				// add categories
				$aCategories = preg_split ("/\|/", $this->mFormData['listingPageCategories'], -1);
				$sPageBody .= $this->getCategoriesAsText($aCategories);
			}

			$this->mPostArticle->doEdit($sPageBody, "Blog listing page created." );

			$wgOut->redirect($this->mPostArticle->getTitle()->getFullUrl());
		}
	}

	private function buildTag() {
		$this->mTagBody = "<bloglist summary=\"true\" timestamp=\"true\" count=50>\n";
		$this->buildTagContent();
		$this->mTagBody.= "</bloglist>\n";
	}

	private function parseTag($sTitle) {
		global $wgParser;
		$oTitle = Title::newFromText($sTitle, NS_BLOG_LISTING);
		$oArticle = new Article($oTitle, 0);
		$sArticleBody = $oArticle->getContent();

		$aMatches = null;
		preg_match('/<bloglist[^>]*>(.*)<\/bloglist>/siU', $sArticleBody, $aMatches);

		if(isset($aMatches[1]) && !empty($aMatches)) {
			BlogTemplateClass::parseTag($aMatches[1], array(), $wgParser);
			$aOptions = BlogTemplateClass::getOptions();

			//echo "<pre>"; print_r($aOptions); echo "</pre>";

			$sPageCategories = '';
			foreach(array_keys($oTitle->getParentCategories()) as $sCategoryFullName) {
				$aCategoryNameParts = explode(':', $sCategoryFullName);
				if(!empty($aCategoryNameParts[1])) {
					$sPageCategories .= (!empty($sPageCategories) ? "|" : "") . strtr($aCategoryNameParts[1], '_', ' ');
				}
			}

			$sListingCategories = '';
			foreach(BlogTemplateClass::getCategoryNames() as $sCategoryName) {
				$sListingCategories .= (!empty($sListingCategories) ? "|" : "") . strtr($sCategoryName, '_', ' ');
			}

			$this->mFormData['listingTitle'] = $aOptions['title'];
			$this->mFormData['listingCategories'] = $sListingCategories;
			$this->mFormData['listingSortBy'] = array_search($aOptions['order'], BlogTemplateClass::$aBlogParams['order']['pattern']);
			$this->mFormData['listingType'] = $aOptions['type'];
			$this->mFormData['listingPageCategories'] = $sPageCategories;
			$this->mFormData['isExistingArticleEditAllowed'] = 1;
		}
		else {
			$this->mFormErrors[] = wfMsg('create-blog-listing-tag-format-not-recognized-on-page') . ": <a href=\"" . $oTitle->getFullUrl() . "\">" . $oTitle->getFullText() . "</a>";
		}
	}

	public function buildTagContent() {
		$aListingCategories = explode('|', $this->mFormData['listingCategories']);
		$aListingAuthors = explode(',', $this->mFormData['listingAuthors']);

		$this->mTagBody .= "<title>" . $this->mFormData['listingTitle'] . "</title>\n";
		$this->mTagBody .= "<type>" . $this->mFormData['listingType'] . "</type>\n";
		$this->mTagBody .= "<order>" . $this->mFormData['listingSortBy'] . "</order>\n";

		foreach($aListingCategories as $sCategoryName) {
			if(!empty($sCategoryName)) {
				$this->mTagBody .= "<category>" . $sCategoryName . "</category>\n";
			}
		}
		foreach($aListingAuthors as $sAuthorName) {
			if(!empty($sAuthorName)) {
				$this->mTagBody .= "<author>" . trim($sAuthorName) . "</author>\n";
			}
		}

		return $this->mTagBody;
	}

	public static function axBlogListingCheckMatches() {
		global $wgRequest, $wgParser;

		$oSpecialPage = new CreateBlogListingPage;

		$oSpecialPage->setFormData('listingCategories', $wgRequest->getVal('categories'));
		$oSpecialPage->setFormData('listingAuthors', $wgRequest->getVal('authors'));
		$oSpecialPage->setFormData('listingType', 'count');

		return (string) BlogTemplateClass::parseTag($oSpecialPage->buildTagContent(), array(), $wgParser);
	}

}
