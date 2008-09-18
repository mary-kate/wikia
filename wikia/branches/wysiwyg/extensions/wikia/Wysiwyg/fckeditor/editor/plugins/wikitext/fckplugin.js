// Rename the "Source" button to "Wikitext".
FCKToolbarItems.RegisterItem( 'Source', new FCKToolbarButton( 'Source', 'Wikitext', null, FCK_TOOLBARITEM_ICONTEXT, true, true, 1 ) ) ;

(function() {
	var original = FCK.SwitchEditMode ;

	FCK.SwitchEditMode = function() {
		var args = arguments;

		if(typeof FCK.loadingIndicatort == 'undefined') {
			FCK.loadingIndicatort = document.createElement('span');
			FCK.loadingIndicatort.innerHTML = '&nbsp;Please wait...&nbsp;';
			FCK.loadingIndicatort.style.position = 'absolute';
			FCK.loadingIndicatort.style.left = '5px';
		}
		FCK.loadingIndicatort.style.visibility = 'visible';

		if(FCK.EditMode == FCK_EDITMODE_SOURCE) {
			FCK.EditingArea.Textarea.style.visibility = 'hidden';
			FCK.EditingArea.Textarea.parentNode.appendChild(FCK.loadingIndicatort, FCK.EditingArea.Textarea);
			setTimeout(function() { original.apply(FCK, args); }, 1000);
		} else {
			original.apply(FCK, args) ;
			FCK.EditingArea.Textarea.style.visibility = 'hidden';
			FCK.EditingArea.Textarea.parentNode.appendChild(FCK.loadingIndicatort, FCK.EditingArea.Textarea);
			setTimeout(function() {
				FCK.EditingArea.Textarea.style.visibility = '';
				FCK.loadingIndicatort.style.visibility = 'hidden';
			}, 1000);
		}
	}
})() ;