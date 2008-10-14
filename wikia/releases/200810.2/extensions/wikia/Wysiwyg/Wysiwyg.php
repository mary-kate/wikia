<?php
$wgExtensionCredits['other'][] = array(
	'name' => 'Wysiwyg',
	'description' => 'FCKeditor integration for MediaWiki',
	'version' => 0.01,
	'author' => array('Inez Korczyński', 'Maciej Brencz', 'Maciej Błaszkowski (Marooned)', 'Łukasz \'TOR\' Garczewski')
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['Wysiwyg'] = $dir . 'Wysiwyg.i18n.php';

// user preferences
$wgHooks['UserToggles'][] = 'wfWysiwygToggle';
$wgHooks['getEditingPreferencesTab'][] = 'wfWysiwygToggle';
function wfWysiwygToggle($toggles, $default_array = false) {
	if(is_array($default_array)) {
		$default_array[] = 'disablewysiwyg';
        } else {
		$toggles[] = 'disablewysiwyg';
	}
	return true;
}

// register __NOWYSIWYG__ magic word
$wgHooks['MagicWordwgVariableIDs'][] = 'wfWysiwygRegisterMagicWordID';
$wgHooks['LanguageGetMagic'][] = 'wfWysiwygGetMagicWord';
$wgHooks['ParserAfterStrip'][] = 'wfWysiwygAfterStrip';
function wfWysiwygRegisterMagicWordID(&$magicWords) {
	$magicWords[] = 'MAG_NOWYSIWYG';
	return true;
}

function wfWysiwygGetMagicWord(&$magicWords, $langCode) {
	$magicWords['MAG_NOWYSIWYG'] = array(0, '__NOWYSIWYG__');
	return true;
}

function wfWysiwygAfterStrip(&$parser, &$text, &$strip_state) {
	MagicWord::get('MAG_NOWYSIWYG')->matchAndRemove($text);
	return true;
}

$wgHooks['AlternateEdit'][] = 'WysiwygAlternateEdit';
function WysiwygAlternateEdit($form) {
	global $wgRequest;
	if(isset($wgRequest->data['wpTextbox1'])) {
		if(isset($wgRequest->data['wysiwygData'])) {
			if($wgRequest->data['wysiwygData'] != '') {
				$wgRequest->data['wpTextbox1'] = wfWysiwygHtml2Wiki($wgRequest->data['wpTextbox1'], $wgRequest->data['wysiwygData'], true);
			}
		}
	}
	return true;
}

$wgHooks['EditForm:BeforeDisplayingTextbox'][] = 'WysiwygBeforeDisplayingTextbox';
function WysiwygBeforeDisplayingTextbox($a, $b) {
	global $wgOut, $wgWysiwygData;
	$wgOut->addHTML('<input type="hidden" id="wysiwygData" name="wysiwygData" value="'.htmlspecialchars($wgWysiwygData).'" />');
	return true;
}

$wgHooks['EditPage::showEditForm:initial2'][] = 'WysiwygInitial2';
function WysiwygInitial2($form) {
	global $wgWysiwygData, $wgWysiwygGo;
	if(!empty($wgWysiwygGo)) {
		list($form->textbox1, $wgWysiwygData) = wfWysiwygWiki2Html($form->textbox1, -1, true);
	}
	return true;
}

$wgHooks['EditPage::showEditForm:initial'][] = 'WysiwygInitial';
function WysiwygInitial($form) {
	global $wgDisableWysiwygExt, $wgWysiwygGo;
	if (!empty($wgDisableWysiwygExt)) {
		return true;
	}

	// check user option
	global $wgUser;
	if($wgUser->getOption('disablewysiwyg') == true){
		return true;
	}

	// only if edited article is in main or image namespace
	if(($form->mTitle->mNamespace == NS_MAIN || $form->mTitle->mNamespace == NS_IMAGE || $form->mTitle->mNamespace == NS_USER)) {
		//search for not handled edge-cases
		$edgecasesFound = wfFCKTestEdgeCases($form->textbox1);
		if ($edgecasesFound != '') {
			global $wgOut;
			$wgOut->setSubtitle('<div id="FCKEdgeCaseMessages" class="usermessage">' . $edgecasesFound . '</div>');
			return true;
		}
		global $IP;
		require("$IP/extensions/wikia/Wysiwyg/fckeditor/fckeditor_php5.php");
		// only if user browser is compatible with FCK
		if(FCKeditor_IsCompatibleBrowser()) {
			global $wgExtensionsPath, $wgStyleVersion, $wgOut;
			$script = '<script type="text/javascript" src="'.$wgExtensionsPath.'/wikia/Wysiwyg/fckeditor/fckeditor.js?'.$wgStyleVersion.'"></script>';
			$script .= <<<EOT
<script type="text/javascript">
//document.domain = 'wikia.com';
function FCKeditor_OnComplete(editorInstance) {
	editorInstance.LinkedField.form.onsubmit = function() {
		if(editorInstance.EditMode == FCK_EDITMODE_SOURCE) {
			YAHOO.util.Dom.get('wysiwygData').value = '';
		} else {
			YAHOO.util.Dom.get('wysiwygData').value = YAHOO.Tools.JSONEncode(editorInstance.wysiwygData);
		}
	}
}
function initEditor() {
	if($('wmuLink')) {
		$('wmuLink').parentNode.style.display = 'none';
	}
	var oFCKeditor = new FCKeditor("wpTextbox1");
	oFCKeditor.wgStyleVersion = wgStyleVersion;
	oFCKeditor.BasePath = "$wgExtensionsPath/wikia/Wysiwyg/fckeditor/";
	oFCKeditor.Config["CustomConfigurationsPath"] = "$wgExtensionsPath/wikia/Wysiwyg/wysiwyg_config.js";
	oFCKeditor.ready = true;
	oFCKeditor.Height = '450px';
	oFCKeditor.Width = document.all ? '99%' : '100%'; // IE fix
	oFCKeditor.ReplaceTextarea();
}
addOnloadHook(initEditor);
</script>
<style type="text/css">/*<![CDATA[*/
	.mw-editTools,
	#editform #toolbar {
		display: none
	}
	#wpTextbox1 {
		visibility: hidden;
	}
	#editform {
		background: transparent url('$wgExtensionsPath/wikia/Wysiwyg/fckeditor/editor/skins/default/images/progress_transparent.gif') no-repeat 50% 35%;
	}
/*]]>*/</style>
EOT;
			$wgOut->addScript($script);
			$wgWysiwygGo = true;
		}
	}
	return true;
}

$wgAjaxExportList[] = 'wfWysywigAjax';
function wfWysywigAjax($type, $input = false, $wysiwygData = false, $articleId = -1) {
	switch ($type) {
		case 'html2wiki':
			return new AjaxResponse(wfWysiwygHtml2Wiki($input, $wysiwygData, true));
		case 'wiki2html':
			$edgecases = wfFCKTestEdgeCases($input);
			if ($edgecases != '') {
				header('X-edgecases: 1');
				return $edgecases;
			} else {
				$separator = Parser::getRandomString();
				header('X-sep: ' . $separator);
				return new AjaxResponse(join(wfWysiwygWiki2Html($input, $articleId, true), "--{$separator}--"));
			}
	}
	return false;
}

function wfWysiwygHtml2Wiki($html, $wysiwygData, $decode = false) {
	require(dirname(__FILE__).'/ReverseParser.php');
	$reverseParser = new ReverseParser();

	if ($decode) {
		$wysiwygData = Wikia::json_decode($wysiwygData, true);
	}

	return $reverseParser->parse($html, $wysiwygData);
}

function wfWysiwygWiki2Html($wikitext, $articleId = -1, $encode = false) {
	global $IP, $FCKmetaData, $FCKparseEnable, $wgTitle;
	require("$IP/extensions/wikia/WysiwygInterface/WysiwygInterface_body.php");

	if($articleId == -1) {
		$title = $wgTitle;
	} else {
		$title = Title::newFromID($articleId);
	}

	wfDebug("wfWysiwygWiki2Html wikitext: {$wikitext}\n");

	$options = new ParserOptions();

	$parser = new WysiwygParser();
	$parser->setOutputType(OT_HTML);

	$FCKparseEnable = true;
	$html = $parser->parse($wikitext, $title, $options)->getText();
	$FCKparseEnable = false;

	$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");

	$html = preg_replace('%<span refid="(\\d+)">(.*?)</span>%sie', '"<input type=\"button\" refid=\"\\1\" value=\"" . htmlspecialchars("\\2") . "\" title=\"" . htmlspecialchars("\\2") . "\" class=\"wysiwygDisabled\" />"', $html);

	// macbre: quick for whitespaces added before <input>
	$html = str_replace("\n<input", '<input', $html);

	wfDebug("wfWysiwygWiki2Html html: {$html}\n");

	$wysiwygData = $FCKmetaData;

	if(!is_array($wysiwygData)) {
		$wysiwygData = array();
	}

	if($encode) {
		$wysiwygData = Wikia::json_encode($wysiwygData, true);
	}

	return array($html, $wysiwygData);
}

/**
 * wfFCKSetRefId
 *
 * Adding reference ID to the $text variable
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 * @access public
 *
 * @return string refId
 */
function wfFCKSetRefId($type, &$text, $link, $trail, $wasblank, $noforce, $returnOnly = false, $lineStart = false) {
	global $FCKmetaData;
	$tmpDescription = $wasblank ? '' : $text;
	$refId = count($FCKmetaData);
	if (!$returnOnly) {
		$text .= "\x1$refId\x1";
	}
	$tmpInside = '';
	if ($trail != '') {
		list($tmpInside, $tmpTrail) = Linker::splitTrail($trail);
	}
	$FCKmetaData[$refId] = array('type' => $type, 'href' => ($noforce ? '' : ':') . $link, 'description' => $tmpDescription, 'trial' => $tmpInside);
	if($lineStart) {
		$FCKmetaData[$refId]['lineStart'] = true;
	}
	return " refid=\"$refId\"";
}

/**
 * wfFCKGetRefId
 *
 * Getting and removing reference ID from the $text variable
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 * @access public
 *
 * @return string refId
 */
function wfFCKGetRefId(&$text, $returnIDonly = false) {
	preg_match("#\x1([^\x1]+)#", $text, $m);
	$refId = isset($m[1]) ? ($returnIDonly ? $m[1] : " refid=\"{$m[1]}\"") : '';
	$text = preg_replace("#\x1[^\x1]+\x1#", '', $text);
	return $refId;
}

/**
 * wfFCKTestEdgeCases
 *
 * Search for not handled edge cases in FCK editor
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 * @access public
 *
 * @return array messages keys for use with wfMsg for every found edge case
 */
function wfFCKTestEdgeCases($text) {
	wfLoadExtensionMessages('Wysiwyg');
	$resultMsg = '';
	$edgecasesFound = array();
	$edgecases = array(
		'regular' => array(
			//HTML comments
			'<!--' => 'wysiwyg-edgecase-comment',
			//template parameters
			'{{{' => 'wysiwyg-edgecase-triplecurls',
			//new magic word to disable FCK for current article
			'__NOWYSIWYG__' => 'wysiwyg-edgecase-nowysiwyg',
			//span with fck metadata - shouldn't be used by user
			'<span refid=' => 'wysiwyg-edgecase-syntax',
		),
		'regexp' => array(
			//external url or template found in the description of a link
			'/\[\[[^|]+\|.*?(?:(?:' . wfUrlProtocols() . ')|{{).*?]]/' => 'wysiwyg-edgecase-complex-description',
			//template with link as a parameter
			'/{{[^}]*(?<=\[)[^}]*}}/' => 'wysiwyg-edgecase-template-with-link',
			//template with link as a parameter
			'/\[\[(?:image|media)[^]|]+\|[^]]+(?:\[\[|(?:' . wfUrlProtocols() . '))(?:[^]]+])?[^]]+]]/si' => 'wysiwyg-edgecase-image-with-link',
		)
	);
	foreach($edgecases['regular'] as $str => $msgkey) {
		if (strpos($text, $str) !== false) {
			$edgecasesFound[] = wfMsg($msgkey);
		}
	}
	foreach($edgecases['regexp'] as $regexp => $msgkey) {
		if (preg_match($regexp, $text)) {
			$edgecasesFound[] = wfMsg($msgkey);
		}
	}
	//if edge case was found add main information about edge cases, like "Edge cases found:"
	if (count($edgecasesFound)) {
		$resultMsg = wfMsg('wysiwyg-edgecase-info') . ' ' . implode(', ', $edgecasesFound);
	}
	return $resultMsg;
}
