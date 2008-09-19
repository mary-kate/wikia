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
		}
	}
	return true;
}
