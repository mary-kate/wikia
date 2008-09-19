// Rename the "Source" button to "Wikitext".
FCKToolbarItems.RegisterItem( 'Source', new FCKToolbarButton( 'Source', 'Wikitext', null, FCK_TOOLBARITEM_ICONTEXT, true, true, 1 ) ) ;

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
				window.parent.document.getElementById('wysiwygData').value = res_array[1];
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
		var wysiwygData = window.parent.document.getElementById('wysiwygData').value;

		window.parent.sajax_request_type = 'POST';
		window.parent.sajax_do_call('wfWysywigAjax', ['html2wiki', html, wysiwygData], function(res) {
			window.parent.document.getElementById('wysiwygData').value = '';
			FCK.EditingArea.Textarea.value = res.responseText;
			FCK.EditingArea.TargetElement.className = '';
			FCK.ToolbarSet.Items[0].Enable();
			FCK.ToolbarSet.Items[0].RefreshState();
		});

	}
});

/*
FCK.Events.AttachEvent( 'OnAfterSetHTML', function() {
	if(FCK.EditMode == FCK_EDITMODE_WYSIWYG) {
		FCK.wysiwygData = eval("{"+window.parent.document.getElementById('wysiwygData').value+"}");

		var spans = FCK.EditingArea.Document.body.getElementsByTagName('span');
		for(var i = 0; i < spans.length; i++) {
			var refid = spans[i].getAttribute('refid');
			if(refid != null) {
				spans[i].style.backgroundColor = '#ffff00';
				spans[i].style.color = '#000000';
				spans[i].contentEditable = false;
				spans[i].innerHTML = "|-" + FCK.wysiwygData[refid].type + "-|";
			}
		}

		FCK.EditorDocument.addEventListener( 'click', function(e) {
			if(e.target.tagName == 'SPAN' && e.target.getAttribute('refid') != null) {
				FCKSelection.SelectNode(e.target);
			}
		}, true);

	}
});
*/