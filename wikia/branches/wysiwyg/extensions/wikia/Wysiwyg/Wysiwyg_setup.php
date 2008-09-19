<?php
$wgExtensionCredits['other'][] = array(
	'name' => 'Wysiwyg',
	'description' => 'FCKeditor integration for MediaWiki'
);

$wgHooks['EditPage::showEditForm:initial'][] = 'WysiwygInitial';

function WysiwygInitial($form) {

	// if namespace of edited article is main or image
	if($form->mTitle->mNamespace == NS_MAIN || $form->mTitle->mNamespace == NS_IMAGE) {

		// if article wikitext does not contain '<!-', '{{{' and '}}}'
		if(!strpos($form->textbox1, '<!-') && !strpos($form->textbox1, '{{{') && !strpos($form->textbox1, '}}}')) {

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
