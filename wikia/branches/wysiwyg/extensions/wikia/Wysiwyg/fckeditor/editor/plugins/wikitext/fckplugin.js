// Rewrite the link command to use our link.html
FCKCommands.RegisterCommand('Link', new FCKDialogCommand('Link', FCKLang.DlgLnkWindowTitle, FCKConfig.PluginsPath + 'wikitext/dialogs/link.html', 400, 250));


(function() {

	var originalSwitchEditMode = FCK.SwitchEditMode;

	FCK.SwitchEditMode = function() {

		var args = arguments;

		if(FCK.EditMode == FCK_EDITMODE_SOURCE) {
			FCK.ToolbarSet.Items[0].Disable();
			FCK.ToolbarSet.Items[0].RefreshState();

			FCK.EditingArea.TargetElement.className = 'childrenHidden';

			window.parent.sajax_request_type = 'POST';
			window.parent.sajax_do_call('wfWysywigAjax', ['wiki2html', FCK.EditingArea.Textarea.value, false, window.parent.wgArticleId], function(res) {
				var separator = res.getResponseHeader('X-sep');
				if(typeof separator == "undefined") {
					separator = res.getResponseHeader('X-Sep');
				}
				var res_array = res.responseText.split('--'+separator+'--');
				FCK.wysiwygData = eval("{"+res_array[1]+"}");
				FCK.EditingArea.Textarea.value = res_array[0];
				FCK.EditingArea.TargetElement.className = '';
				originalSwitchEditMode.apply(FCK, args);
				FCK.ToolbarSet.Items[0].Enable();
				FCK.ToolbarSet.Items[0].RefreshState();
			});

		} else if(FCK.EditMode == FCK_EDITMODE_WYSIWYG) {
			FCK.ToolbarSet.Items[0].Disable();
			FCK.ToolbarSet.Items[0].RefreshState();
			FCK.EditingArea.TargetElement.className = 'childrenHidden';
			originalSwitchEditMode.apply(FCK, args);
		}

	}

})();

FCK.Events.AttachEvent( 'OnAfterSetHTML', function() {
	if(FCK.EditingArea.TargetElement.className == 'childrenHidden') {
		var html = FCK.GetData();

		window.parent.sajax_request_type = 'POST';
		window.parent.sajax_do_call('wfWysywigAjax', ['html2wiki', html, window.parent.YAHOO.Tools.JSONEncode(FCK.wysiwygData)], function(res) {
			FCK.EditingArea.Textarea.value = res.responseText;
			FCK.EditingArea.TargetElement.className = '';
			FCK.ToolbarSet.Items[0].Enable();
			FCK.ToolbarSet.Items[0].RefreshState();
		});

	}
});

// initialize wysiwygData
FCK.wysiwygData = (typeof window.parent.FCKdata != 'undefined') ? window.parent.FCKdata : [];

// setup wikimarkup placeholders
FCK.RegisterDoubleClickHandler( function(placeholder) {
	refId = placeholder.getAttribute('refid');
	alert(placeholder.value);
}, 'INPUT' );
