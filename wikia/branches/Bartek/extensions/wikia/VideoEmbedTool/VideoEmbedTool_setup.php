<?php
/*
 * @author Bartek Łapiński
 */

if(!defined('MEDIAWIKI')) {
	exit(1);
}


// for now it's more a copy of WikiaMiniUpload files
$wgExtensionCredits['other'][] = array(
        'name' => 'Video Embed Tool',
        'author' => 'Bartek Łapiński',
);

$dir = dirname(__FILE__).'/';

$wgExtensionMessagesFiles['VideoEmbedTool'] = $dir.'/VideoEmbedTool.i18n.php';
$wgHooks['EditPage::showEditForm:initial2'][] = 'VETSetup';

function VETSetup($editform) {
	global $wgOut, $wgStylePath, $wgExtensionsPath, $wgStyleVersion, $wgHooks, $wgUser;
	if(get_class($wgUser->getSkin()) == 'SkinMonaco') {
		wfLoadExtensionMessages('VideoEmbedTools');
		$wgHooks['ExtendJSGlobalVars'][] = 'VETSetupVars';
		$wgOut->addScript('<script type="text/javascript" src="'.$wgStylePath.'/common/yui_2.5.2/slider/slider-min.js?'.$wgStyleVersion.'"></script>');
		$wgOut->addScript('<script type="text/javascript" src="'.$wgExtensionsPath.'/wikia/VideoEmbedTool/js/VET.js?'.$wgStyleVersion.'"></script>');
		$wgOut->addScript('<link rel="stylesheet" type="text/css" href="'.$wgExtensionsPath.'/wikia/VideoEmbedTool/css/VET.css?'.$wgStyleVersion.'" />');
		if (isset ($editform->ImageSeparator)) {
			$sep = $editform->ImageSeparator ;
			$marg = 'margin-left:5px;' ;
		} else {
			$sep = '' ;
			$marg =  'clear: both;' ;
			$editform->ImageSeparator = ' - ' ;
		}
		$wgOut->addHtml('<div id="wmuLinkDiv" style="float: left; margin-top: 20px;' . $marg .'">' . $sep . '<a href="#" id="wmuLink">' . wfMsg ('wmu-imagelink') . '</a></div>');
	}
	return true;
}

function WMUSetupVars($vars) {
	global $wgFileBlacklist, $wgCheckFileExtensions, $wgStrictFileExtensions, $wgFileExtensions;

	$vars['wmu_back'] = wfMsg('wmu-back');
	$vars['wmu_imagebutton'] = wfMsg('wmu-imagebutton') ;
	$vars['wmu_close'] = wfMsg('wmu-close');
	$vars['wmu_warn1'] = wfMsg('wmu-warn1');
	$vars['wmu_warn2'] = wfMsg('wmu-warn2');
	$vars['wmu_bad_extension'] = wfMsg('wmu-bad-extension');
	$vars['filetype_missing'] = wfMsg('filetype-missing');
	$vars['file_extensions'] = $wgFileExtensions;
	$vars['file_blacklist'] = $wgFileBlacklist;
	$vars['check_file_extensions'] = $wgCheckFileExtensions;
	$vars['strict_file_extensions'] = $wgStrictFileExtensions;
	$vars['wmu_show_message'] = wfMsg('wmu-show-message');
	$vars['wmu_hide_message'] = wfMsg('wmu-hide-message');
	$vars['wmu_show_license_message'] = wfMsg('wmu-show-license-msg');
	$vars['wmu_hide_license_message'] = wfMsg('wmu-hide-license-msg');
	$vars['wmu_max_thumb'] = wfMsg('wmu-max-thumb');

	return true;
}

$wgAjaxExportList[] = 'VET';

function VET() {
	global $wgRequest, $wgGroupPermissions, $wgAllowCopyUploads;

	// todo change
	wfLoadExtensionMessages('VideoEmbedTool');

	// Overwrite configuration settings needed by image import functionality
	$wgAllowCopyUploads = true;
	$wgGroupPermissions['user']['upload_by_url']   = true;
	$dir = dirname(__FILE__).'/';
	require_once($dir.'VideoEmbedTool_body.php');

	$method = $wgRequest->getVal('method');
	$wmu = new WikiaMiniUpload();

	$html = $wmu->$method();
	$domain = $wgRequest->getVal('domain', null);
	if(!empty($domain)) {
		$html .= '<script type="text/javascript">document.domain = "' . $domain  . '"</script>';
	}
	return new AjaxResponse($html);
}
