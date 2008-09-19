// Rename the "Source" button to "Wikitext".
FCKToolbarItems.RegisterItem( 'Source', new FCKToolbarButton( 'Source', 'Wikitext', null, FCK_TOOLBARITEM_ICONTEXT, true, true, 1 ) ) ;

(function() {

	var original = FCK.SwitchEditMode ;

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
				window.parent.document.getElementById('wysiwygData').value = res_array[1];
				FCK.EditingArea.Textarea.value = res_array[0];
				FCK.EditingArea.TargetElement.className = '';
				original.apply(FCK, args);
				FCK.ToolbarSet.Items[0].Enable();
				FCK.ToolbarSet.Items[0].RefreshState();
			});

		} else if(FCK.EditMode == FCK_EDITMODE_WYSIWYG) {
			FCK.ToolbarSet.Items[0].Disable();
			FCK.ToolbarSet.Items[0].RefreshState();
			original.apply(FCK, args);
		}

	}
})();


FCK.DataProcessor.ConvertToDataFormat = function(rootNode, excludeRoot, ignoreIfEmptyParagraph, format) {
	FCK.EditingArea.TargetElement.className = 'childrenHidden';

	var html = FCKDataProcessor.prototype.ConvertToDataFormat.call(this, rootNode, excludeRoot, ignoreIfEmptyParagraph, format);
	var wysiwygData = window.parent.document.getElementById('wysiwygData').value;

	window.parent.sajax_request_type = 'POST';
	window.parent.sajax_do_call('wfWysywigAjax', ['html2wiki', html, wysiwygData], function(res) {
		window.parent.document.getElementById('wysiwygData').value = '';
		FCK.EditingArea.Textarea.value = res.responseText;
		FCK.EditingArea.TargetElement.className = '';
		FCK.ToolbarSet.Items[0].Enable();
		FCK.ToolbarSet.Items[0].RefreshState();
	});

	return '';
};