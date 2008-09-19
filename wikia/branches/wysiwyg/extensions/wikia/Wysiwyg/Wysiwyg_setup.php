<?php
$wgExtensionCredits['other'][] = array(
	'name' => 'Wysiwyg',
	'description' => 'FCKeditor integration for MediaWiki'
);

$wgHooks['EditPage::showEditForm:initial'][] = 'WysiwygInitial';
function WysiwygInitial($form) {
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
EOT;
			$wgOut->addScript($script);

			list($form->textbox1, $wysiwygData) = wfWysiwygWiki2Html($form->textbox1, -1, true);
			$wgOut->addHTML('<input type="hidden" id="wysiwygData" name="wysiwygData" value="'.$wysiwygData.'" />');
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

	$options = new ParserOptions();
	$options->setTidy(true);

	$parser = new WysiwygParser();
	$parser->setOutputType(OT_HTML);

	$wikitext = preg_replace('/(?<!\n)\n(?!\n)/', "\x7f_1\n", $wikitext);

	$FCKparseEnable = true;
	$html = $parser->parse($wikitext, $wgTitle, $options)->getText();
	$FCKparseEnable = false;

	$html = str_replace("\x7f_1", "<!--\x7f_1-->", $html);

	$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");

	$wysiwygData = $FCKmetaData;

	if(!is_array($wysiwygData)) {
		$wysiwygData = array();
	}

	if($encode) {
		$wysiwygData = Wikia::json_encode($wysiwygData, true);
	}

	return array($html, $wysiwygData);
}
