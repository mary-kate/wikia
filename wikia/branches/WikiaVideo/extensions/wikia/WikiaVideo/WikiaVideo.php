<?php
if(!defined('MEDIAWIKI')) {
	exit(1);
}

$wgAutoloadClasses['VideoPage'] = dirname(__FILE__). '/VideoPage.php';

function WikiaVideo_makeVideo($title, $options, $sk) {
	wfProfileIn('WikiaVideo_makeVideo');
	if(!$title->exists() || true) {
		// TO DO: Generate redlinks for not existing video
		$out = $sk->makeColouredLinkObj(Title::newFromText('Test', NS_SPECIAL), 'new', 'text', 'query');
	} else {
		// defaults
		$width = 400;
		$thumb = '';
		$align = 'left';
		$caption = '';

		$params = explode('|', $options);
		foreach($params as $param) {
			$width_check = strpos($param, 'px');
			if($width_check > -1) {
				$width = str_replace('px', '', $param);
			} else if('thumb' == $param) {
				$thumb = 'thumb';
			} else if(('left' == $param) || ('right' == $param)) {
				$align = $param;
			} else {
				$caption = $param;
			}
		}

		$video = new VideoPage($title);
		$video->load();
		$out = $video->generateWindow($align, $width, $caption, $thumb);
	}
	wfProfileOut('WikiaVideo_makeVideo');
	return $out;
}

$wgHooks['MWNamespace:isMovable'][] = 'WikiaVideo_isMovable';
function WikiaVideo_isMovable($result, $index) {
	if($index == NS_VIDEO) {
		$result = false;
	}
	return true;
}

$wgHooks['ArticleFromTitle'][] = 'WikiaVideoArticleFromTitle';
function WikiaVideoArticleFromTitle($title, $article) {
	if(NS_VIDEO == $title->getNamespace()) {
		$article = new VideoPage($title);
	}
	return true;
}