<?php

/**
 * CategorySelect
 *
 * A CategorySelect extension for MediaWiki
 * Provides an interface for managing categories in article without editing whole article
 *
 * @author Maciej Błaszkowski (Marooned) <marooned at wikia-inc.com>
 * @date 2009-01-13
 * @copyright Copyright (C) 2009 Maciej Błaszkowski, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @package MediaWiki
 *
 * To activate this functionality, place this file in your extensions/
 * subdirectory, and add the following line to LocalSettings.php:
 *     require_once("$IP/extensions/wikia/CategorySelect/CategorySelect.php");
 */

if (!defined('MEDIAWIKI')) {
	echo "This is MediaWiki extension named CategorySelect.\n";
	exit(1) ;
}

$wgExtensionCredits['other'][] = array(
	'name' => 'CategorySelect',
	'author' => '[http://www.wikia.com/wiki/User:Marooned Maciej Błaszkowski (Marooned)]',
	'description' => 'Provides an interface for managing categories in article without editing whole article.'
);

$wgExtensionFunctions[] = 'CategorySelectInit';
$wgExtensionMessagesFiles['CategorySelect'] = dirname(__FILE__) . '/CategorySelect.i18n.php';
$wgAjaxExportList[] = 'CategorySelectAjaxGetCategories';

/**
 * Initialize hooks
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 */
function CategorySelectInit() {
	global $wgHooks, $wgCategorySelectEnabled, $wgCategorySelectMetaData, $wgAutoloadClasses;
	$wgAutoloadClasses['CategorySelect'] = 'extensions/wikia/CategorySelect/CategorySelect_body.php';
//	$wgCategorySelectEnabled = true;
//	$wgHooks['OutputPageMakeCategoryLinks'][] = 'CategorySelectHiddenCategory';
	$wgHooks['OutputPageBeforeHTML'][] = 'CategorySelectOutput';
//	$wgHooks['ParserBeforeStrip'][] = 'CategorySelectInit';
}

/**
 * Get categories via AJAX
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 */
function CategorySelectAjaxGetCategories() {
	global $wgRequest;
	$cat = $wgRequest->getText('query');

	$dbr = wfGetDB(DB_SLAVE);
	$res = $dbr->select(
		'category',
		'cat_title',
		'cat_title LIKE "%' . $dbr->escapeLike($cat) . '%"',
		__METHOD__,
		array('LIMIT' => '10')
	);

	$categories = '';
	while($row = $dbr->fetchObject($res)) {
		$categories .= $row->cat_title . "\n";
	}

	$ar = new AjaxResponse($categories);
	$ar->setCacheDuration(60 * 20);

	return $ar;
}

/**
 * Get categories via AJAX
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 */
function CategorySelectAjaxGetCategoriesXYZ($titleName) {
	$result = array('error' => null, 'wikitext' => null, 'categories' => null);
	$title = Title::newFromText($titleName);
	if($title->exists()) {
		$rev = Revision::newFromTitle($title);
		$wikitext = $rev->getText();
		$data = CategorySelect::SelectCategoryAPIgetData($wikitext);
		$result = array_merge($results, $data);
	} else {
		$result['error'] = wfMsg('');
	}
	$ar = new AjaxResponse(Wikia::json_encode($results));
	$ar->setCacheDuration(60 * 20);
	return $ar;
}

/**
 *
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 */
function CategorySelectOutput(&$out, &$text) {
	global $wgOut, $wgCategorySelectMetaData, $wgExtensionsPath, $wgStyleVersion, $wgTitle;
	$wgOut->addScript("<script type=\"text/javascript\" src=\"$wgExtensionsPath/wikia/CategorySelect/CategorySelect.js?$wgStyleVersion\"></script>");
	$wgOut->addScript("<link rel=\"stylesheet\" type=\"text/css\" href=\"$wgExtensionsPath/wikia/CategorySelect/CategorySelect.css?$wgStyleVersion\" />");
	$wgOut->addHTML('
	<div id="myAutoComplete">
	    <input id="myInput" type="text">
	    <div id="myContainer"></div>
	</div>');

	$rev = Revision::newFromTitle($wgTitle);
	$wikitext = $rev->getText();
	$wgCategorySelectMetaData = CategorySelect::SelectCategoryAPIgetData($wikitext);
	$wgOut->addHTML('<pre>output:' . print_r ($wgCategorySelectMetaData['categories'], true) .'</pre>');

	$categoriesJSON = Wikia::json_encode($wgCategorySelectMetaData['categories']);
	$wgOut->addScript("<script type=\"text/javascript\">var categories = $categoriesJSON;</script>");
	return true;
}

///**
// * Bogus function for setHook
// *
// * @author Maciej Błaszkowski <marooned at wikia-inc.com>
// */
//function CategorySelectParserHookCallback($input, $args, $parser) {
//	echo '<pre>'; print_r ($input); echo '</pre>';
//	return $input;
//}
//
///**
// * Bogus function for setHook
// *
// * @author Maciej Błaszkowski <marooned at wikia-inc.com>
// */
//function CategorySelectHiddenCategory($out, $categories, &$links) {
//	global $wgCategorySelectMetaData;
//	foreach ($categories as $category => $type) {
//		$wgCategorySelectMetaData[$category]['hidden'] = $type == 'hidden' ? 1 : 0;
//	}
//	return true;
//}
//
///**
// * Load JS/CSS for extension
// *
// * @author Maciej Błaszkowski <marooned at wikia-inc.com>
// */
//function CategorySelectIncludeJSCSS( $skin, & $bottomScripts) {
//	global $wgExtensionsPath, $wgStyleVersion;
////	$bottomScripts .= "<script type=\"text/javascript\" src=\"$wgExtensionsPath/wikia/CategorySelect/SpecialCategorySelect.js?$wgStyleVersion\"></script>";
//	return true;
//}
//
///**
// * Add messages above the editor on UserTalk page so the user with empty UTP would see their message when clicking on UTP link
// *
// * @author Maciej Błaszkowski <marooned at wikia-inc.com>
// */
//function CategorySelectArticleEditor($editPage) {
//	global $wgOut, $wgTitle, $wgUser;
//	if ($wgTitle->getNamespace() == NS_USER_TALK &&						//user talk page?
//		$wgUser->getName() == $wgTitle->getPartialURL() &&				//*my* user talk page?
//		!$wgUser->isAllowed('bot')										//user is not a bot?
//	) {																	//if all above == 'yes' - display user's messages
//		$wgOut->addHTML(CategorySelectGetUserMessagesContent());
//	}
//	return true;
//}