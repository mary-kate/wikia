// Rename the "Source" button to "Wikitext".
FCKToolbarItems.RegisterItem( 'Source', new FCKToolbarButton( 'Source', 'Wikitext', null, FCK_TOOLBARITEM_ICONTEXT, true, true, 1 ) ) ;

(function() {
	var original = FCK.SwitchEditMode ;

	FCK.SwitchEditMode = function() {

		var args = arguments;

		if(FCK.EditMode == FCK_EDITMODE_SOURCE) {
			original.apply(FCK, args);
		} else if(FCK.EditMode == FCK_EDITMODE_WYSIWYG) {
			original.apply(FCK, args);
		}

	}
})() ;


FCK.DataProcessor.ConvertToDataFormat = function(rootNode, excludeRoot, ignoreIfEmptyParagraph, format) {
	FCK.EditingArea.TargetElement.className = 'childrenHidden';

	var html = FCKDataProcessor.prototype.ConvertToDataFormat.call(this, rootNode, excludeRoot, ignoreIfEmptyParagraph, format);
	var wysiwygData = window.parent.document.getElementById('wysiwygData').value;

	window.parent.sajax_request_type = 'POST';
	window.parent.sajax_do_call('wfWysywigAjax', ['html2wiki', html, wysiwygData], function(res) {
		window.parent.document.getElementById('wysiwygData').value = '';
		FCK.EditingArea.Textarea.value = res.responseText;
		FCK.EditingArea.TargetElement.className = '';
	});

	return '';
};
