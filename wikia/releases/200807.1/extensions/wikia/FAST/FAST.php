<?php
if(!defined('MEDIAWIKI')) {
	die(1);
}

$wgExtensionCredits['other'][] = array(
    'name' => 'FAST',
    'author' => 'Inez Korczyński, Christian Williams',
);

$wgHooks['OutputPageBeforeHTML'][] = 'wfFASTHook';
$wgHooks['AfterCategoryPageView'][] = 'wfFASTCategoryHook';

function wfFASTCategoryHook($page) {
	global $wgOut;
	$text = $wgOut->getHTML();
	wfFASTHook($wgOut, $text, true);
	$wgOut->clearHTML();
	$wgOut->addHTML($text);
	return true;
}

$wgFASTCalled = false;

function wfFASTHook(&$out, &$text, $category = false) {
	global $wgTitle, $wgUser, $wgRequest, $wgOut, $wgExtensionsPath, $wgStyleVersion, $wgFASTSIDE, $wgEnableFAST_HOME2, $wgFASTCalled;

	if($wgFASTCalled == true ||($category == false && $wgTitle->getNamespace() == NS_CATEGORY)) {
		return true;
	}

	$wgFASTCalled = true;

	$mainpage = $wgTitle->getArticleId() == Title::newMainPage()->getArticleId();
	$exists = $wgTitle->exists();
	$isLoggedIn = $wgUser->isLoggedIn();
	$isContentPage = in_array($wgTitle->getNamespace(), array(NS_MAIN, NS_IMAGE, NS_CATEGORY)) || $wgTitle->getNamespace() >= 100;
	$isView = $wgRequest->getVal('action', 'view') == 'view';
	$isPreview = $wgRequest->getVal('wpPreview') != '' && $wgRequest->getVal('action') == 'submit';

	$fast = array();

	if(!$isLoggedIn && $isContentPage && $isView) {
		if($mainpage) {
			$fast[] = 'FAST_HOME3';
			$fast[] = 'FAST_HOME4';
		} else {
			$fast[] = 'FAST_SIDE';
		}
	}

	if($mainpage && (($isView && $exists) || $isPreview)) {
		$fast[] = 'FAST_HOME1';
		if(!empty($wgEnableFAST_HOME2)) {
			$fast[] = 'FAST_HOME2';
		}
	} else if($isContentPage) {
		if(($isView && $exists) || $isPreview) {
			$fast[] = 'FAST_TOP';
			if(!$isLoggedIn) {
				$fast[] = 'FAST_BOTTOM';
			}
		}
	}

// FAST.js is part of allinone*.js
//	if(count($fast) > 0) {
//		$wgOut->addScript('<script type="text/javascript" src="'.$wgExtensionsPath.'/wikia/FAST/FAST.js?'.$wgStyleVersion.'" ></script>');
//	}

	if(in_array('FAST_TOP', $fast)) {
		$text = AdServer::getInstance()->getAd('FAST_TOP').$text;
	} else {
		if(in_array('FAST_HOME2', $fast)) {
			$text = AdServer::getInstance()->getAd('FAST_HOME2').$text;
		}
		if(in_array('FAST_HOME1', $fast)) {
			$text = AdServer::getInstance()->getAd('FAST_HOME1').$text;
		}
	}

	$pos = strrpos($text, '</span></h2>');

	if(in_array('FAST4', $fast)) {
		if($pos > -1) {
			$text = substr($text, 0, $pos + 12).AdServer::getInstance()->getAd('FAST4').substr($text, $pos + 12);
		} else {
			$fast[] = 'FAST_BOTTOM';
		}
	}

	if(in_array('FAST_BOTTOM', $fast)) {
		if($pos > -1) {
			$text = substr($text, 0, $pos + 12).AdServer::getInstance()->getAd('FAST_BOTTOM').substr($text, $pos + 12);
			$adContainer = '<div id="adSpaceFAST5"></div>';
		} else {
			$adContainer = AdServer::getInstance()->getAd('FAST5');
		}
		$text .= '<div id="fast_bottom_ads" style="display: none;"><a name="Advertisement"></a><h2><span class="mw-headline">Advertisement</span></h2>'.$adContainer.'</div>';
	}

	if(in_array('FAST_SIDE', $fast)) {
		$wgFASTSIDE[0] = AdServer::getInstance()->getAd('FAST_SIDE');
		$wgFASTSIDE[1] = '<div id="adSpaceFAST7"></div>';
	} else {
		if(in_array('FAST_HOME3', $fast)) {
			$wgFASTSIDE[0] = AdServer::getInstance()->getAd('FAST_HOME3');
		}
		if(in_array('FAST_HOME4', $fast)) {
			$wgFASTSIDE[1] = AdServer::getInstance()->getAd('FAST_HOME4');
		}
	}

	return true;
}
