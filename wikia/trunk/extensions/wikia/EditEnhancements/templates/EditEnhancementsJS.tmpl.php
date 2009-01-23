<script type="text/javascript">

EditEnhancements = function() {
	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;
	jQuery.noConflict();

	this.calculate = function() {
		// rescale textbox
		if (document.getElementById('wpTextbox1___Frame')) {
			var textbox = 'wpTextbox1___Frame';
		} else {
			var textbox = 'wpTextbox1';
		}

		var viewport = Dom.getViewportHeight();
		var textareaTop = jQuery("#" + textbox).offset().top;
		var toolbar = jQuery("#edit_enhancements_toolbar").outerHeight(true);
		var heightDiff = jQuery("#" + textbox).outerHeight(true) - jQuery("#" + textbox).height();
		
		var targetHeight = viewport - textareaTop - toolbar - heightDiff;

		targetHeight = Math.max(targetHeight, 200);
		targetHeight = Math.min(targetHeight, 800);

		Dom.setStyle(textbox, 'height', targetHeight + 'px');

		// rescale summary box
		var items = Dom.get('edit_enhancements_toolbar').getElementsByTagName('li');
		var summaryBox = jQuery('#wpSummaryEnhanced');
		var itemsWidth = 0;
		
		for (i=0; i<items.length; i++) {
			itemsWidth += jQuery(items[i]).innerWidth() + 5;
		}

		itemsWidth -= summaryBox.innerWidth();

		var newSummaryWidth =  (jQuery("#edit_enhancements_toolbar").innerWidth() - 10) - itemsWidth - 10;

		newSummaryWidth = Math.max(newSummaryWidth, 200);
		newSummaryWidth = Math.min(newSummaryWidth, 500);

		Dom.setStyle('wpSummaryEnhanced', 'width', newSummaryWidth + 'px');
	}
	
	this.calculate();
}

EditEnhancementsLoad = function() {
	this.EditEnhancementsLoop = function() {
		if (count == 3) {
			clearInterval(interval);
		} else {
			EditEnhancements();
			count++;
		}
	}
	var count = 0;

	// change the id of summary box, so changing its size works in IE
	jQuery('#wpSummary').attr('id', 'wpSummaryEnhanced');

	var interval = setInterval("EditEnhancementsLoop()", 500);
}

YAHOO.util.Event.on(window, 'load', EditEnhancementsLoad);
YAHOO.util.Event.on(window, 'resize', EditEnhancements);
</script>
