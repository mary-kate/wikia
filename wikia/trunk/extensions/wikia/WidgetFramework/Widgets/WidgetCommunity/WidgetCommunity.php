<?php
/**
 * @author Inez Korczynski <inez@wikia.com>
 * @author Maciej Brencz
 * */
if(!defined('MEDIAWIKI')) {
	die(1);
}

global $wgWidgets;
$wgWidgets['WidgetCommunity'] = array(
	'callback' => 'WidgetCommunity',
	'title' => array(
		'en' => 'Community'
	),
	'desc' => array(
		'en' => 'Community'
   	),
	'closeable' => false,
	'editable' => false,
	'listable' => false
);

function WidgetCommunity($id, $params) {
	if($params['skinname'] != 'monaco') {
		return '';
	}

	wfProfileIn(__METHOD__);

	global $wgUser, $wgLang, $wgStylePath;
	$total = SiteStats::articles();
	$total = $wgLang->formatNum($total);

	$avatar = $wgStylePath.'/monaco/images/community_avatar.gif';
	if( class_exists("BlogAvatar") ) {
		$avatar = BlogAvatar::newFromUser( $wgUser )->getLinkTag( 29, 29, false, false, "community_avatar" );
	}
	if(class_exists("WikiaAvatar")) {
		$userAvatar = new WikiaAvatar($wgUser->getId());
		$image = $userAvatar->getAvatarImage("m");
		$avatar = '<a rel="nofollow" href="/index.php?title=Special:AvatarUpload"><img src="'.$image.'" id="community_avatar" /></a>';
	}

	// WhosOnline
	$online = array();
	global $wgEnableWhosOnlineExt;
	if( !empty( $wgEnableWhosOnlineExt ) ) {
		$aResult = WidgetFrameworkCallAPI(array('action' => 'query', 'list' => 'whosonline', 'wklimit' => 5));
		if(!empty($aResult['query']['whosonline'])) {
			$online = $aResult['query']['whosonline'];
		}
	}

	// recently edited
	$aResult = WidgetFrameworkCallAPI(array(
		"action" => "query",
		"list" => "recentchanges",
		"rclimit" => 2,
		"rctype" => "edit|new",
		"rcshow" => "!anon|!bot",
		"rcnamespace" => "0|1|2|3|6|7",
		"rcprop" => "title|timestamp|user"));

	if(!empty($aResult['query']['recentchanges'])) {
		$recentlyEdited = $aResult['query']['recentchanges'];
	} else {
		$recentlyEdited = array();
	}

	// template stuff
	$tmpl = new EasyTemplate(dirname( __FILE__ ));
	$tmpl->set_vars(array(
		'widgetId' => $id,
		'total' => $total,
		'recentlyEdited' => $recentlyEdited,
		'username' => $wgUser->getName(),
		'userpageurl' => $wgUser->getUserPage()->getLocalURL(),
		'talkpageurl' => $wgUser->getTalkPage()->getLocalURL(),
		'users' => $online,
		'avatarLink' => $avatar));

	$output = $tmpl->execute('WidgetCommunity');

	wfProfileOut(__METHOD__);
	return $output;
}

function WidgetCommunityFormatTime($time) {
	$diff = time() - strtotime($time);
	if ($diff < 60) { //less than a minute
		return wfMsgExt( 'widget-community-secondsago', array( 'parsemag' ), $diff );
	} else if ($diff < (60 * 60)) { //less than an hour
		$minutes = floor($diff/60);
		return wfMsgExt('widget-community-minutesago', array( 'parsemag' ), $minutes);
	} else if ($diff < (60 * 60 * 24)) { //less than a day
		$hours = floor($diff/(60*60));
		return wfMsgExt('widget-community-hoursago', array( 'parsemag' ), $hours);
	} else if ($diff < (60 * 60 * 24 * 2)) { //less than 2 days
		return wfMsg('widget-community-yesterday');
	}
	return '';
}
