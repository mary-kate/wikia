<?php
/**
 * @author Maciej Brencz
 * */
if(!defined('MEDIAWIKI')) {
	die(1);
}

global $wgWidgets;
$wgWidgets['WidgetBookmark'] = array(
	'callback' => 'WidgetBookmark',
	'title' => array(
		'en' => 'Bookmarks',
		'pl' => 'Zakładki'
	),
	'desc' => array(
		'en' => 'Add your favorite pages',
		'pl' => 'Zachowaj swoje ulubione strony'
	),
	'params' => array(),
	'closeable' => true,
	'editable' => false,
	'listable' => true
);

function WidgetBookmark($id) {

	global $wgRequest, $wgCityId;

	wfProfileIn(__METHOD__);

	 // maybe user is trying to add a page
	if ( $wgRequest->getInt('pid') && $wgRequest->getVal('rs') == 'WidgetFrameworkAjax' && $wgRequest->getVal('cmd') == 'add' ) {
		$pages = WidgetBookmarkAddPage( $wgRequest->getInt('pid') );
	}
	// or maybe he wants to remove the page
	else if ( $wgRequest->getVal('pid') && $wgRequest->getVal('rs') == 'WidgetFrameworkAjax' && $wgRequest->getVal('cmd') == 'remove' ) {
                $pages = WidgetBookmarkRemovePage( $wgRequest->getVal('pid') );
        }
	else {
		// prepare list of pages
		$pages = WidgetBookmarkGetPages();
	}

	$list = '<ul>';

	if ( is_array($pages) && count($pages) > 0 ) {

		// the newest bookmarks on top
		$pages = array_reverse($pages);	

		$list .= '<!-- '.count($pages).' bookmarks -->';

		foreach($pages as $page_id => $page) {
			// filter the list by cityId
			if (isset($page['city']) && $page['city'] == $wgCityId) {
				$list .= '<li><a href="'.$page['href'].'">'.htmlspecialchars(  shortenText($page['title'], 30)  ).'</a>'.
				'<a class="WidgetBookmarkRemove" onclick="WidgetBookmarkDo('.$id.', \'remove\', \''.$page_id.'\')">x</a></li>';
			}
		}
	}
	$list .= '</ul>';

	// menu
	$menu = '<div class="WidgetBookmarkMenu">'.
		'<a class="addBookmark" onclick="WidgetBookmarkDo('.$id.', \'add\', wgArticleId)" title="'.wfMsg('export-addcat').'">&nbsp;</a></div>';

	wfProfileOut(__METHOD__);

	return $menu . $list;
}

function WidgetBookmarkGetPages() {
	global $wgUser;

	$pages = unserialize( $wgUser->getOption('widget_bookmark_pages') );

	return $pages;
}

function WidgetBookmarkAddPage($pageId) {

	global $wgCityId, $wgSitename, $wgUser;

	$key = $wgCityId . ':' . $pageId;
	$title = Title::newFromID( $pageId );

	// validate
	if (!$title) {
		return;
	}

	$pages = WidgetBookmarkGetPages();

	// don't duplicate entries
	if ( isset($pages[$key]) ) {
		return $pages;
	}
	
	// add page
	$pages[ $key ] = array(
		'city'  => $wgCityId,
		'wiki'  => $wgSitename,
		'title' => $title->getPrefixedText(),
		'href'  => $title->getFullURL(),
	);

	// limit number of pages to 20
	$pages = array_slice($pages, -20, 20, true);

	// save pages list in user profile
	$wgUser->setOption('widget_bookmark_pages', serialize($pages));
	$wgUser->saveSettings();

	// make sure we save user settings
	$dbw = wfGetDB( DB_MASTER );
	$dbw->close();

	return $pages;
}

function WidgetBookmarkRemovePage($pageId) {

	global $wgUser;

	$pages = WidgetBookmarkGetPages();

	if ( !isset($pages[$pageId]) ) {
		return $pages;
	}

	// remove
	unset($pages[$pageId]);

	// save pages list in user profile
	$wgUser->setOption('widget_bookmark_pages', serialize($pages));
	$wgUser->saveSettings();

	// make sure we save user settings
	$dbw = wfGetDB( DB_MASTER );
	$dbw->close();

	return $pages;
}
