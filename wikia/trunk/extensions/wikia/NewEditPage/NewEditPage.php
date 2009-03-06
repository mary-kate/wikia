<?
$wgExtensionCredits['other'][] = array(
        'name' => 'New Edit Page',
	'description' => 'Applies edit page changes',
        'version' => 0.2,
        'author' => '[http://pl.wikia.com/wiki/User:Macbre Maciej Brencz]'
);

$wgExtensionFunctions[] = 'wfNewEditPageInit';

function wfNewEditPageInit() {
	global $wgHooks, $wgExtensionMessagesFiles;

	// i18n
	$wgExtensionMessagesFiles['NewEditPage'] = dirname(__FILE__).'/NewEditPage.i18n.php';

	// edit page
	$wgHooks['EditPage::showEditForm:initial2'][] = 'wfNewEditPageAddCSS';

	// not existing articles
	$wgHooks['ArticleFromTitle'][] = 'wfNewEditPageArticleView';

	// add red preview notice
	$wgHooks['EditPage::showEditForm:initial'][] = 'wfNewEditPageAddPreviewBar';
	return true;
}

// add custom CSS to page of not existing articles
function wfNewEditPageArticleView($title) {

	global $wgNewEditPageNewArticle;

	if (!$title->exists()) {
		$wgNewEditPageNewArticle = true;
		wfNewEditPageAddCSS();
	}

	return $title;
}

// add CSS to edit pages
function wfNewEditPageAddCSS() {
	global $wgWysiwygEdit, $wgOut, $wgUser, $wgExtensionsPath, $wgStyleVersion, $wgNewEditPageNewArticle;

	// do not touch monobook
	$skinName = get_class($wgUser->getSkin());
	if ($skinName == 'SkinMonoBook') {
		return true;
	}

	if (!empty($wgNewEditPageNewArticle)) {
		// new article notice
		$cssFile = 'NewEditPageNewArticle.css';
	}
	else if (!empty($wgWysiwygEdit)) {
		// edit mode in wysiwyg
		$cssFile = 'NewEditPageWysiwyg.css';
	}
	else {
		// edit mode in old MW editor
		$cssFile = 'NewEditPage.css';
	}

	// add static CSS file
	$wgOut->addLink(array(
		'rel' => 'stylesheet',
		'href' => "{$wgExtensionsPath}/wikia/NewEditPage/{$cssFile}?{$wgStyleVersion}",
		'type' => 'text/css'
	));

	return true;
}

// add red preview notice in old editor
function wfNewEditPageAddPreviewBar($editPage) {
	global $wgOut, $wgUser, $wgHooks;

	// do not touch monobook
	$skinName = get_class($wgUser->getSkin());
	if ($skinName == 'SkinMonoBook') {
		return true;
	}

	// we're in preview mode
	if ($editPage->formtype == 'preview') {
		wfLoadExtensionMessages('NewEditPage');
		$wgOut->addHTML('<div id="new_edit_page_preview_notice">' . wfMsg('new-edit-page-preview-notice') . '</div>');

		// add page title before preview HTML
		$wgHooks['OutputPageBeforeHTML'][] = 'wfNewEditPageAddPreviewTitle';
	}

	return true;
}

// add page title before preview HTML
function wfNewEditPageAddPreviewTitle($wgOut, $text) {
	global $wgTitle;
	$wgOut->addHTML('<h1 id="new_edit_page_preview_title">' . $wgTitle->getPrefixedText() . '</h1>');

	// find first closing </h2> and remove preview notice
	$pos = strpos($text, '</h2>');
	if ($pos !== false) {
		$text = substr($text, $pos+5);
	}

	return true;
}
