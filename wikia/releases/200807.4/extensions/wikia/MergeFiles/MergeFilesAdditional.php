<?php
/*
 * Author: Inez Korczynski (inez@wikia.com)
 */
if(!defined('MEDIAWIKI')) {
	exit(1);
}

function getWidgetsAssets() {
	$js = $css = array();
	$dir = 'extensions/wikia/WidgetFramework/Widgets/';
	if(is_dir($dir)) {
		if($dh = opendir($dir)) {
			while(($file = readdir($dh)) !== false) {
				if(filetype($dir.$file) == 'dir') {
					if(file_exists($dir.$file.'/'.$file.'.js')) {
						$js[] = '../'.$dir.$file.'/'.$file.'.js';
					}
					if(file_exists($dir.$file.'/'.$file.'.css')) {
						$css[] = '../'.$dir.$file.'/'.$file.'.css';
					}
				}
			}
		}
		closedir($dh);
	}
	return array('js' => $js, 'css' => $css);
}

$widgetsAssets = getWidgetsAssets();

global $MF;

$MF['quartz_js']['source'] = array(
	'common/yui/2.5.1/utilities/utilities.js',
	'common/yui/2.5.1/container/container.js',
	'common/yui/2.5.1/autocomplete/autocomplete.js',
	'common/yui/2.5.1/logger/logger.js',
	'common/yui/2.5.1/menu/menu.js',
	'common/yui/2.5.1/tabview/tabview.js',
	'common/yui/2.4.0/3rdpart/tools.js',
	'common/yui/2.4.0/3rdpart/carousel.js',
	'common/urchin.js',
	'quartz/js/main.js',
	'quartz/js/usermenu.js',
	'quartz/js/languagemenu.js',
	'quartz/js/shareit.js',
	'quartz/js/ratearticle.js',
	'quartz/js/search.js',
	'common/widgets/js/widgetsConfig.js',
	'common/widgets/js/widgetsFramework.js',
	'common/wikibits.js',
	'quartz/js/tracker.js',
	'common/tracker.js',
	'../extensions/wikia/AjaxLogin/AjaxLogin.js',
	'../extensions/wikia/ProblemReports/js/ProblemReports-loader.js',
	'../extensions/wikia/HelperPanel/HelperPanel.js',
	'../extensions/wikia/onejstorule.js',
);
$MF['quartz_js']['source'] = array_merge($MF['quartz_js']['source'], $widgetsAssets['js']);

$MF['quartz_css']['source'] = $widgetsAssets['css'];

$MF['monobook_js']['source'] = array(
	'common/yui/2.5.1/utilities/utilities.js',
	'common/yui/2.5.1/container/container.js',
	'common/yui/2.5.1/autocomplete/autocomplete.js',
	'common/yui/2.5.1/logger/logger.js',
	'common/yui/2.5.1/menu/menu.js',
	'common/yui/2.5.1/tabview/tabview.js',
	'common/yui/2.4.0/3rdpart/tools.js',
	'common/urchin.js',
	'common/wikibits.js',
	'monobook/main.js',
	'monobook/tracker.js',
	'common/tracker.js',
	'../extensions/wikia/ProblemReports/js/ProblemReports-loader.js',
	'../extensions/wikia/onejstorule.js',
);

$MF['monaco_loggedin_js']['source'] = array(
	'common/ajax.js',
	'common/yui/2.5.1/utilities/utilities.js',
	'common/yui/2.5.1/container/container.js',
	'common/yui/2.5.1/autocomplete/autocomplete.js',
	'common/yui/2.5.1/tabview/tabview.js',
	'common/yui/2.5.1/animation/animation-min.js',
	'common/yui/2.4.0/3rdpart/tools.js',
	'common/urchin.js',
	'common/yui/2.4.0/3rdpart/carousel.js',
	'common/yui/2.5.1/logger/logger.js',
	'common/wikibits.js',
	'monaco/js/tracker.js',
	'common/tracker.js',
	'common/ajaxwatch.js',
	'monaco/js/main.js',
	'common/widgets/js/widgetsConfig.js',
	'common/widgets/js/widgetsFramework.js',
	'../extensions/wikia/ProblemReports/js/ProblemReports-loader.js',
	'../extensions/wikia/onejstorule.js',
	'../extensions/wikia/FAST/FAST.js',
);
$MF['monaco_loggedin_js']['source'] = array_merge($MF['monaco_loggedin_js']['source'], $widgetsAssets['js']);

$MF['monaco_non_loggedin_js']['source'] = array(
	'common/ajax.js',
	'common/yui/2.5.1/utilities/utilities.js',
	'common/yui/2.5.1/container/container.js',
	'common/yui/2.5.1/autocomplete/autocomplete.js',
	'common/yui/2.5.1/tabview/tabview.js',
	'common/yui/2.5.1/animation/animation-min.js',
	'common/yui/2.4.0/3rdpart/tools.js',
	'common/urchin.js',
	'common/yui/2.5.1/logger/logger.js',
	'common/wikibits.js',
	'monaco/js/tracker.js',
	'common/tracker.js',
	'monaco/js/main.js',
	'common/widgets/js/widgetsConfig.js',
        'common/widgets/js/widgetsFramework.js',
	'../extensions/wikia/AjaxLogin/AjaxLogin.js',
	'../extensions/wikia/ProblemReports/js/ProblemReports-loader.js',
	'../extensions/wikia/onejstorule.js',
	'../extensions/wikia/FAST/FAST.js',
);
$MF['monaco_non_loggedin_js']['source'] = array_merge($MF['monaco_non_loggedin_js']['source'], $widgetsAssets['js']);

$MF['monaco_css']['source'] = array(
	'common/yui/2.5.1/container/assets/container.css',
	'common/yui/2.5.1/tabview/assets/tabview.css',
	'common/shared.css',
	'monaco/css/monobook_modified.css',
	'monaco/css/reset_modified.css',
	'monaco/css/root.css',
	'monaco/css/header.css',
	'monaco/css/article.css',
	'monaco/css/widgets.css',
	'monaco/css/footer.css',
	'monaco/css/star_rating.css',
	'monaco/css/ny.css',
);
$MF['monaco_css']['source'] = array_merge($MF['monaco_css']['source'], $widgetsAssets['css']);

