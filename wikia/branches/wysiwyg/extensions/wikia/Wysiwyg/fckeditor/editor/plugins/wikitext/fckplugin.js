// Rename the "Source" button to "Wikitext".
FCKToolbarItems.RegisterItem( 'Source', new FCKToolbarButton( 'Source', 'Wikitext', null, FCK_TOOLBARITEM_ICONTEXT, true, true, 1 ) ) ;

(function() {
	var original = FCK.SwitchEditMode ;

	FCK.SwitchEditMode = function() {
		var args = arguments;

		if(FCK.EditMode == FCK_EDITMODE_SOURCE) {
			// Hide the textarea to avoid seeing the code change.
			// FCK.EditingArea.Textarea.style.visibility = 'hidden' ;
			// setTimeout(function() { original.apply(FCK, args) ; }, 1000);
		} else {
			// original.apply(FCK, args) ;
		}
	}
})() ;