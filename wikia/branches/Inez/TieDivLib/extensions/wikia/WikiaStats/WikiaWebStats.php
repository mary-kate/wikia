<?php
if(!defined('MEDIAWIKI')) {
    exit( 1 ) ;
}

$wgHooks['SkinAfterBottomScripts'][] = 'wfWikiaWebStatsScript';

function wfWikiaWebStatsScript($this, $bottomScriptText) {
	global $wgUser, $wgArticle, $wgTitle, $wgCityId, $wgDotDisplay, $wgAdServerTest;
	if(!empty($wgCityId) && !empty($wgDotDisplay)) {
		$url = 'http://wikia-ads.wikia.com/onedot.php?c='.$wgCityId.'&u='.$wgUser->getID().'&a='.(is_object($wgArticle) ? $wgArticle->getID() : null).'&n='.$wgTitle->getNamespace().(!empty($wgAdServerTest) ? '&db_test=1' : '');
		$bottomScriptText = '<script type="text/javascript">document.write("<img src=\"'.$url.'"+((typeof document.referrer != "undefined") ? "&r="+escape(document.referrer) : "")+"&cb="+(new Date).valueOf()+"\" width=\"1\" height=\"1\" border=\"0\" />");</script><noscript><img src="'.$url.'" width="1" height="1" border="0" /></noscript>';
	}
	return true;
}