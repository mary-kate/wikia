/*
Copyright (c) 2007-2008, Wikia Inc.
Author: Inez Korczynski (inez (at) wikia.com), Maciej B³aszkowski <marooned at wikia-inc.com>
Version: 1.0
*/

var initTracker = function() {
	var Tracker = YAHOO.Wikia.Tracker;
	var Event = YAHOO.util.Event;

	/*
	navigation [nav-bar]
	featured_box [main featured hub]
	featured_hubs
	all_hubs [hubs footer]
	feature_footer
	*/

	Event.addListener(['navigation','featured_box','featured_hubs','all_hubs','feature_footer'], 'click', function(e) {
		var el = Event.getTarget(e);
		if(el.nodeName == 'A') {
			var str  = 'main_page/' + el.id;
			Tracker.trackByStr(e, str);
		}
	});
};