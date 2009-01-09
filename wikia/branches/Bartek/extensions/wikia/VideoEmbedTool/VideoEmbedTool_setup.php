<?php
/*
 * @author Bartek Łapiński
 */

if(!defined('MEDIAWIKI')) {
	exit(1);
}


// for now it's more a copy of VideoEmbedTool files
$wgExtensionCredits['other'][] = array(
        'name' => 'Video Embed Tool',
        'author' => 'Bartek Łapiński',
	'version' => '0.19',
);

$dir = dirname(__FILE__).'/';

$wgExtraNamespaces[400] = "Video";
$wgExtraNamespaces[401] = "Video_talk";

$wgExtensionFunctions[] = "VETSetupHook";
$wgExtensionMessagesFiles['VideoEmbedTool'] = $dir.'/VideoEmbedTool.i18n.php';
$wgHooks['EditPage::showEditForm:initial2'][] = 'VETSetup';
$wgHooks['ArticleFromTitle'][] = 'VETArticleFromTitle';
$wgHooks['ParserBeforeStrip'][] = 'VETParserBeforeStrip';


function VETSetupHook() {
	global $wgParser;
		
	$wgParser->setHook( "video", "VETParserHook" );
	return true;
}

function VETParserHook( $input, $argv, $parser ) {
	// todo get video name, get embed code, display that code
	$output = "";
	return $output;
}

function VETSetup($editform) {
	global $wgOut, $wgStylePath, $wgExtensionsPath, $wgStyleVersion, $wgHooks, $wgUser;
	if(get_class($wgUser->getSkin()) == 'SkinMonaco') {
		wfLoadExtensionMessages('VideoEmbedTool');
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
		$wgOut->addHtml('<div id="vetLinkDiv" style="float: left; margin-top: 20px;' . $marg .'">' . $sep . '<a href="#" id="vetLink">' . wfMsg ('vet-imagelink') . '</a></div>');
	}
	return true;
}

function VETArticleFromTitle( $title, $article  ) {
	global $wgUser, $IP;
	
	require_once( "$IP/extensions/wikia/VideoEmbedTool/Video.php" );
        require_once( "$IP/extensions/wikia/VideoEmbedTool/VideoPage.php" );

	if (NS_VIDEO == $title->getNamespace() ) {
		//todo for edit
		$article = new VideoPage( $title );
	}	
	return true;
}

function VETParserBeforeStrip( $parser, $text, $strip_state  ) {
	$pattern = "@(\[\[Video:)([^\]]*?)].*?\]@si";
        $text = preg_replace_callback($pattern, 'VETRenderVideo', $text);

        return true;
}

function VETRenderVideo( $matches ) {
	global $IP, $wgOut;
	require_once( "$IP/extensions/wikia/VideoEmbedTool/Video.php" );
	$name = $matches[2];
	$params = explode("|",$name);
	$video_name = $params[0];
	$video =  Video::newFromName( $video_name );

	$x = 1;

	$width = 300;
	$align = 'left';
	$caption = '';

        foreach($params as $param){
                if($x > 1){
                        $width_check = preg_match("/px/i", $param );

                        if($width_check){
                                $width = preg_replace("/px/i", "", $param);
                        } else if ($x == 3){
                                $align = $param;
                        } else if ($x == 4) {
				$caption = $param;
			}
                }
                $x++;
        }

	if ( is_object( $video ) ) {
			$output = "<video name=\"{$video->getName()}\" width=\"{$width}\" align=\"{$align}\" caption=\"{$caption}\"></video>";
			return $output;
	}
	return $matches[0];
}

function VETSetupVars($vars) {
	global $wgFileBlacklist, $wgCheckFileExtensions, $wgStrictFileExtensions, $wgFileExtensions;

	$vars['vet_back'] = wfMsg('vet-back');
	$vars['vet_imagebutton'] = wfMsg('vet-imagebutton') ;
	$vars['vet_close'] = wfMsg('vet-close');
	$vars['vet_warn1'] = wfMsg('vet-warn1');
	$vars['vet_warn2'] = wfMsg('vet-warn2');
	$vars['vet_bad_extension'] = wfMsg('vet-bad-extension');
	$vars['filetype_missing'] = wfMsg('filetype-missing');
	$vars['file_extensions'] = $wgFileExtensions;
	$vars['file_blacklist'] = $wgFileBlacklist;
	$vars['check_file_extensions'] = $wgCheckFileExtensions;
	$vars['strict_file_extensions'] = $wgStrictFileExtensions;
	$vars['vet_show_message'] = wfMsg('vet-show-message');
	$vars['vet_hide_message'] = wfMsg('vet-hide-message');
	$vars['vet_show_license_message'] = wfMsg('vet-show-license-msg');
	$vars['vet_hide_license_message'] = wfMsg('vet-hide-license-msg');
	$vars['vet_max_thumb'] = wfMsg('vet-max-thumb');
	$vars['vet_title'] = wfMsg('vet-title');

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
	$vet = new VideoEmbedTool();

	$html = $vet->$method();
	$domain = $wgRequest->getVal('domain', null);
	if(!empty($domain)) {
		$html .= '<script type="text/javascript">document.domain = "' . $domain  . '"</script>';
	}
	return new AjaxResponse($html);
}
