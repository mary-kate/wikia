// Rewrite the link command to use our link.html
FCKCommands.RegisterCommand('Link', new FCKDialogCommand('Link', FCKLang.DlgLnkWindowTitle, FCKConfig.PluginsPath + 'wikitext/dialogs/link.html', 400, 250));

var inputClickCommand = new FCKDialogCommand('inputClickCommand', '&nbsp;', FCKConfig.PluginsPath + 'wikitext/dialogs/inputClick.html', 400, 100);

var originalSwitchEditMode = FCK.SwitchEditMode;

FCK.SwitchEditMode = function() {

	if(FCK.InProgress == true) {
		return true;
	}

	FCK.InProgress = true;

	var args = arguments;

	if(FCK.EditMode == FCK_EDITMODE_WYSIWYG) {

		FCK.EditingArea.TargetElement.className = 'childrenHidden';
		originalSwitchEditMode.apply(FCK, args);

	} else if(FCK.EditMode == FCK_EDITMODE_SOURCE) {

		FCK.EditingArea.TargetElement.className = 'childrenHidden';

		window.parent.sajax_request_type = 'POST';
		window.parent.sajax_do_call('wfWysywigAjax', ['wiki2html', FCK.EditingArea.Textarea.value, false, window.parent.wgArticleId], function(res) {
			var separator = res.getResponseHeader('X-sep');
			if(typeof separator == "undefined") separator = res.getResponseHeader('X-Sep');
			var res_array = res.responseText.split('--'+separator+'--');
			FCK.wysiwygData = eval("{"+res_array[1]+"}");
			FCK.EditingArea.Textarea.value = res_array[0];
			FCK.EditingArea.TargetElement.className = '';
			originalSwitchEditMode.apply(FCK, args);
			setTimeout(function() {FCK.InProgress = false;}, 100);
		});

	}

	return true;
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', function() {
	if(FCK.EditingArea.TargetElement.className == 'childrenHidden') {
		var html = FCK.GetData();
		var wysiwygDataEncoded =  window.parent.YAHOO.Tools.JSONEncode(FCK.wysiwygData);

		window.parent.sajax_request_type = 'POST';
		window.parent.sajax_do_call('wfWysywigAjax', ['html2wiki', html, wysiwygDataEncoded], function(res) {
			FCK.EditingArea.Textarea.value = res.responseText;
			FCK.EditingArea.TargetElement.className = '';
			setTimeout(function() {FCK.InProgress = false;}, 100);
		});

	}
	if(!FCK.wysiwygData) {
		FCK.wysiwygData = eval("{"+window.parent.document.getElementById('wysiwygData').value+"}");
	}
	if(FCK.EditMode == FCK_EDITMODE_WYSIWYG) {
		FCK.EditorDocument.addEventListener( 'click', function(e) {
			if(e.target.tagName == 'INPUT') {
				var refid = e.target.getAttribute('refid');
				if(refid) {
					inputClickCommand.Execute();
				}
			}
		}, true);
	}
});