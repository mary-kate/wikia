<?php
$wgExtensionCredits['other'][] = array(
	'name' => 'Wysiwyg',
	'description' => 'FCKeditor integration for MediaWiki',
	'version' => 0.01,
	'author' => array('Inez Korczyński', 'Maciej Brencz', 'Maciej Błaszkowski (Marooned)', 'Łukasz \'TOR\' Garczewski')
);

$wgHooks['EditPage::showEditForm:initial'][] = 'WysiwygInitial';
function WysiwygInitial($form) {
	global $wgDisableWysiwygExt;
	if (!empty($wgDisableWysiwygExt)) {
		return true;
	}
	// only if edited article is in main or image namespace and article wikitext does not contain '<!-', '{{{' and '}}}'
	if(($form->mTitle->mNamespace == NS_MAIN || $form->mTitle->mNamespace == NS_IMAGE) && !strpos($form->textbox1, '<!-') && !strpos($form->textbox1, '{{{') && !strpos($form->textbox1, '}}}')) {
		global $IP;
		require("$IP/extensions/wikia/Wysiwyg/fckeditor/fckeditor_php5.php");
		// only if user browser is compatible with FCK
		if(FCKeditor_IsCompatibleBrowser()) {
			global $wgExtensionsPath, $wgStyleVersion, $wgOut;
			$script = '<script type="text/javascript" src="'.$wgExtensionsPath.'/wikia/Wysiwyg/fckeditor/fckeditor.js?'.$wgStyleVersion.'"></script>';
			$script .= <<<EOT
<script type="text/javascript">
function initEditor() {
	if($('wmuLink')) {
		$('wmuLink').parentNode.style.display = 'none';
	}
	var oFCKeditor = new FCKeditor("wpTextbox1");
	oFCKeditor.BasePath = "$wgExtensionsPath/wikia/Wysiwyg/fckeditor/";
	oFCKeditor.Config["CustomConfigurationsPath"] = "$wgExtensionsPath/wikia/Wysiwyg/wysiwyg_config.js";
	oFCKeditor.ready = true;
	oFCKeditor.Height = '450px';
	oFCKeditor.ReplaceTextarea();
}
addOnloadHook(initEditor);
</script>
<style type="text/css">/*<![CDATA[*/
	.mw-editTools {display: none}
/*]]>*/</style>
EOT;
			$wgOut->addScript($script);

			list($form->textbox1, $wysiwygData) = wfWysiwygWiki2Html($form->textbox1, -1, true);
			$wgOut->addHTML('<input type="hidden" id="wysiwygData" name="wysiwygData" value="'.htmlspecialchars($wysiwygData).'" />');
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
			$separator = Parser::getRandomString();
			header('X-sep: ' . $separator);
			return new AjaxResponse(join(wfWysiwygWiki2Html($input, $articleId, true), "--{$separator}--"));

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
	//$options->setTidy(true);

	$parser = new WysiwygParser();
	$parser->setOutputType(OT_HTML);

	//$wikitext = preg_replace('/(?<!\n)\n(?!\n)/', "\n\x7f\n", $wikitext);

	$FCKparseEnable = true;
	$html = $parser->parse($wikitext, $title, $options)->getText();
	$FCKparseEnable = false;

	//$html = str_replace("\x7f\n", "<!--\x7f-->", $html);

	$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");

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
