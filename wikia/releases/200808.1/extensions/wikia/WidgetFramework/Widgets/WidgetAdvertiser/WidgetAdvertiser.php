<?php
/**
 * @author Inez Korczyński
 * @author Maciej Brencz
 * */
if(!defined('MEDIAWIKI')) {
	die(1);
}

global $wgWidgets;
$wgWidgets['WidgetAdvertiser'] = array(
	'callback' => 'WidgetAdvertiser',
	'title' => array(
		'en' => 'Wikia Spotlight'
	),
	'desc' => array(
		'en' => 'Showing spotlights / ads'
    ),
    'closeable' => false,
    'editable' => false,
    'listable' => false // don't show on Special:Widgets
);

function WidgetAdvertiser($id, $params) {
    wfProfileIn(__METHOD__);
    global $wgUser, $wgShowAds, $wgUseAdServer, $wgAdCalled, $wgRequest;

	if(!$wgShowAds || !$wgUseAdServer) {
		wfProfileOut(__METHOD__);
		return '';
	}
    $ret = '';
	$type = $wgUser->isLoggedIn() ? 'user' : 'anon';

	if($wgRequest->getVal('action', 'view') != 'view') {
		return '';
	}

	switch ($type) {
		case 'anon':
			if(get_class($wgUser->getSkin()) == 'SkinMonaco') {
				$ret = AdServer::getInstance()->getAd('bl');
			} else {
				$ret = str_replace('&','&amp;',WidgetAdvertiserWrapAd('tr', $id)) . str_replace('&','&amp;',WidgetAdvertiserWrapAd('l', $id));
			}
			break;
		case 'user':
			if(get_class($wgUser->getSkin()) == 'SkinMonaco') {
				$ret = AdServer::getInstance()->getAd('r');
			} else {
				$ret = str_replace('&','&amp;',WidgetAdvertiserWrapAd('tl', $id )) . str_replace('&','&amp;',WidgetAdvertiserWrapAd('t', $id));
			}
			break;
	}
	wfProfileOut(__METHOD__);
    return $ret;
}

function WidgetAdvertiserWrapAd($pos, $id) {
	$ad = AdServer::getInstance()->getAd($pos);
	return empty($ad) ? '' : '<div id="'.$id.'_'.$pos.'" class="widgetAdvertiserAd widgetAdvertiserAd_'.$pos.'">'.$ad.'</div>';
}
