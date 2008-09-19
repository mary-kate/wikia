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

			/* temporary code begin */
			require("$IP/extensions/wikia/WysiwygInterface/WysiwygInterface_body.php");
			$options = new ParserOptions();
			$options->setTidy(true);

			$parser = new WysiwygParser();
			$parser->setOutputType(OT_HTML);
			global $FCKmetaData, $FCKparseEnable, $wgTitle;
			$FCKparseEnable = true;
			$form->textbox1 = $parser->parse($form->textbox1, $wgTitle, $options)->getText();
			$FCKparseEnable = false;
			$form->textbox1 = mb_convert_encoding($form->textbox1, 'HTML-ENTITIES', "UTF-8");
			if(!is_array($FCKmetaData)) {
				$FCKmetaData = array();
			}
			$wgOut->addHTML('<input type="hidden" id="wysiwygData" name="wysiwygData" value="'.Wikia::json_encode($FCKmetaData, true).'" />');
			/* temporary code end */

		}
	}
	return true;
}

$wgAjaxExportList[] = 'wfWysywigAjax';
function wfWysywigAjax($type, $input = false, $wysiwygData = false) {
	switch ($type) {
		case 'html2wiki':
			return wfWysiwygHtml2Wiki($input, $wysiwygData, true);
			break;
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
