(function() {
var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var DDM = YAHOO.util.DragDropMgr;

/**
 * @author Inez Korczynski
 */
var value = null;
Event.onDOMReady(function() {
	searchField = Dom.get('search_field');
	if (searchField.value == '') {
		value = searchField.value = searchField.title;
	}
	else if (searchField.value != searchField.title) {
		value = searchField.title;
		searchField.style.color = 'black';
	}
	else {
		value = searchField.title;
	}
	Event.addListener('search_field', 'click', function() {
		if(value == null || value == Dom.get('search_field').value) {
			Dom.get('search_field').value = '';
			Dom.get('search_field').style.color = 'black';
		}
		Dom.get('search_field').focus();
	});
	Event.addListener('search_field', 'blur', function() {
		if(Dom.get('search_field').value == '') {
			Dom.get('search_field').value = value;
			Dom.get('search_field').style.color = 'gray';
		}
	});
	Event.addListener('search_button', 'click', function() {
		if (Dom.get('search_field').value == value) {
			Dom.get('search_field').value = '';
		}

		Dom.get('searchform').submit();
	});

	var submitAutoComplete_callback = {
		success: function(o) {
			if(o.responseText !== undefined) {
				window.location.href=o.responseText;
			}
		}
	}

	var submitAutoComplete = function(comp, resultListItem) {
		YAHOO.Wikia.Tracker.trackByStr(null, 'search/suggestItem/' + escape(YAHOO.util.Dom.get('search_field').value.replace(/ /g, '_')));
		sUrl = wgServer + wgScriptPath + '?action=ajax&rs=getSuggestedArticleURL&rsargs=' + encodeURI(Dom.get('search_field').value);
		var request = YAHOO.util.Connect.asyncRequest('GET', sUrl, submitAutoComplete_callback);
	}

	Event.addListener('search_field', 'keypress', function(e) {if(e.keyCode==13) {Dom.get('searchform').submit();}});

	// Init datasource
	var oDataSource = new YAHOO.widget.DS_XHR(wgServer + wgScriptPath, [ "\n" ]);
	oDataSource.responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
	oDataSource.scriptQueryParam = "rsargs";
	oDataSource.scriptQueryAppend = "action=ajax&rs=searchSuggest";

	// Init AutoComplete object and assign datasource object to it
	var oAutoComp = new YAHOO.widget.AutoComplete('search_field','searchSuggestContainer', oDataSource);
	oAutoComp.highlightClassName = oAutoComp.prehighlightClassName = 'navigation-hover';
	oAutoComp.autoHighlight = false;
	oAutoComp.typeAhead = true;
	oAutoComp.queryDelay = 1;
	oAutoComp.itemSelectEvent.subscribe(submitAutoComplete);
});

/**
 * @author Inez Korczynski
 */
//Event.onContentReady("navigation", function() {
//	var navMenu = new YAHOO.widget.Menu("navigation", { position: "static", showdelay: 0, hidedelay: 750 });
//	navMenu.render();
//	Dom.addClass("navigation", "navLoaded");
//});

/**
 * @author Christian Williams, Inez Korczynski
 */
Event.onContentReady("background_strip", function() {
	function pos(menuId, buttonId, side) {
		Event.addListener(buttonId, 'click', function() {
			if (Dom.get('headerMenuHub') && Dom.get('headerMenuUser')) {
				Dom.setStyle(['headerMenuUser', 'headerMenuHub'], 'visibility', 'hidden');
			}
			if(menuId == 'headerMenuUser') {
				var buttonCenter = Dom.getViewportWidth() - (Dom.getX(this) + this.offsetWidth/2) - 10;
			} else {
				var buttonCenter = YAHOO.util.Dom.getX(this) + this.offsetWidth/2;
			}
			var menuWidth = Dom.get(menuId).offsetWidth;
			if((buttonCenter - (menuWidth/2)) < 10) {
				targetRight = 10;
			} else {
				targetRight = buttonCenter - (menuWidth/2);
			}

			// #3108
			if (Dom.hasClass('body', 'rtl')) {
				Dom.setStyle(menuId, side, targetRight + 'px');
			}

			if (YAHOO.util.Dom.get(menuId).style.visibility == 'visible') {
				YAHOO.util.Dom.get(menuId).style.visibility = 'hidden';
			} else {
				YAHOO.util.Dom.get(menuId).style.visibility = 'visible';
			}
			//Dom.setStyle(menuId, side, targetRight + 'px');
		});
		var headerMenuTimer;
		Event.addListener(menuId, 'mouseout', function() {
			headerMenuTimer = setTimeout("YAHOO.util.Dom.get('"+menuId+"').style.visibility = 'hidden';", 300);
		});
		Event.addListener(menuId, 'mouseover', function() {
			clearTimeout(headerMenuTimer);
		});
	}
	pos('headerMenuUser', 'headerButtonUser', 'right');
	pos('headerMenuHub', 'headerButtonHub', 'left');
});

/**
 * @author Inez Korczynski
 */
Event.onDOMReady(function() {
	var callback = {
		success: function(o) {
			o = YAHOO.Tools.JSONParse(o.responseText);
			Dom.setStyle('current-rating', 'width', Math.round(o.item.wkvoteart[0].avgvote * 17)+'px');
			Dom.setStyle(['star1','star2','star3','star4','star5'], 'display', o.item.wkvoteart[0].remove ? '' : 'none');
			Dom.setStyle('unrateLink', 'display', o.item.wkvoteart[0].remove ? 'none' : '');
			YAHOO.util.Dom.removeClass('star-rating', 'star-rating-progress');
			YAHOO.util.Connect.asyncRequest('POST', window.location.href, null, "action=purge");
		}
	}
	Event.addListener('unrateLink', 'click', function(e) {
		Event.preventDefault(e);
		YAHOO.util.Connect.asyncRequest('GET', wgScriptPath+'/api.php?action=delete&list=wkvoteart&format=json&wkpage='+wgArticleId, callback);
		YAHOO.util.Dom.addClass('star-rating', 'star-rating-progress');
		 Dom.setStyle('unrateLink', 'display', 'none');
	});
	Event.addListener(['star1','star2','star3','star4','star5'], 'click', function(e) {
		Event.preventDefault(e);
		var rating = this.id.substr(4,1);
		YAHOO.util.Connect.asyncRequest('GET', wgScriptPath+'/api.php?action=insert&list=wkvoteart&format=json&wkvote='+rating+'&wkpage='+wgArticleId, callback);
		YAHOO.util.Dom.addClass('star-rating', 'star-rating-progress');
	});

	// fix for IE6(#1843)
	if (YAHOO.env.ua.ie == 6) {
		Event.addListener(['star1','star2','star3','star4','star5'], 'mouseover', function(e) {
			var rating = this.id.substr(4,1);
			YAHOO.util.Dom.addClass(this, 'hover');
			YAHOO.util.Dom.setStyle(this, 'width', parseInt(rating*17) + 'px');
		});
		Event.addListener(['star1','star2','star3','star4','star5'], 'mouseout', function(e) {
			YAHOO.util.Dom.removeClass(this, 'hover');
			YAHOO.util.Dom.setStyle(this, 'width', '17px');
		});
	}
});

Event.onDOMReady(function() {
	Event.addListener('body', 'mouseover', clearMenu);
});

})();

//Edit Tips
var editorMode = 'normal';
function editorAnimate(editorModeRequest) {
	var animationSpeed = .75;
	var easing = YAHOO.util.Easing.easeOut;
	if (editorModeRequest == editorMode) {
		var sidebarAnim = new YAHOO.util.Anim('widget_sidebar', {
			left: { to: 5 }
		}, animationSpeed, easing);
		var pageAnim = new YAHOO.util.Anim('wikia_page', {
			marginLeft: { to: 221 }
		}, animationSpeed, easing);
		var editorAnim = new YAHOO.util.Anim(['editTipWrapper2', 'wikiPreview'], {
			marginLeft: { to: 0 }
		}, animationSpeed, easing);
		var previewAnim = new YAHOO.util.Anim(['wikiPreview', 'wikiPreview'], {
			marginLeft: { to: 0 }
		}, animationSpeed, easing);

		sidebarAnim.animate();
		pageAnim.animate();
		editorAnim.animate();
		previewAnim.animate();
		YAHOO.util.Dom.get('editTipsLink').innerHTML = 'Show Editing Tips';
		YAHOO.util.Dom.get('editWideLink').innerHTML = 'Go Widescreen';
		AccordionMenu.seriouslyCollapseAll('editTips');
		editorMode = 'normal';
	} else if (editorModeRequest == 'tips') {
		var sidebarAnim = new YAHOO.util.Anim('widget_sidebar', {
			left: { to: -211 }
		}, animationSpeed, easing);
		var pageAnim = new YAHOO.util.Anim('wikia_page', {
			marginLeft: { to: 5 }
		}, animationSpeed, easing);
		var editorAnim = new YAHOO.util.Anim('editTipWrapper2', {
			marginLeft: { to: 216 }
		}, animationSpeed, easing);
		var previewAnim = new YAHOO.util.Anim('wikiPreview', {
			marginLeft: { to: 216 }
		}, animationSpeed, easing);

		sidebarAnim.animate();
		pageAnim.animate();
		editorAnim.animate();
		previewAnim.animate();
		YAHOO.util.Dom.get('editTipsLink').innerHTML = 'Show Navigation';
		YAHOO.util.Dom.get('editWideLink').innerHTML = 'Go Widescreen';
		editorMode = 'tips';
	} else if (editorModeRequest == 'wide') {
		var sidebarAnim = new YAHOO.util.Anim('widget_sidebar', {
			left: { to: -211 }
		}, animationSpeed, easing);
		var pageAnim = new YAHOO.util.Anim('wikia_page', {
			marginLeft: { to: 5 }
		}, animationSpeed, easing);
		var editorAnim = new YAHOO.util.Anim('editTipWrapper2', {
			marginLeft: { to: 0 }
		}, animationSpeed, easing);
		var previewAnim = new YAHOO.util.Anim(['wikiPreview', 'wikiPreview'], {
			marginLeft: { to: 216 }
		}, animationSpeed, easing);

		sidebarAnim.animate();
		pageAnim.animate();
		editorAnim.animate();
		previewAnim.animate();
		YAHOO.util.Dom.get('editTipsLink').innerHTML = 'Show Editing Tips';
		YAHOO.util.Dom.get('editWideLink').innerHTML = 'Exit Widescreen';
		AccordionMenu.seriouslyCollapseAll('editTips');
		editorMode = 'wide';
	}
}
//Skin Navigation
var m_timer;
var displayed_menus = new Array();
var last_displayed = '';
var last_over = '';
function menuItemAction(e) {
	clearTimeout(m_timer);
	if (!e) var e = window.event;
	e.cancelBubble = true;
	if (e.stopPropagation) e.stopPropagation();
	var source_id = '*';
	try {source_id = e.target.id;}
	catch (ex) {source_id = e.srcElement.id}
	if (source_id.indexOf("a-") == 0) {
		source_id = source_id.substr(2);
	}
	if (source_id && menuitem_array[source_id]) {
		if ($(last_over)) YAHOO.util.Dom.removeClass(last_over, "navigation-hover");
		last_over = source_id;
		YAHOO.util.Dom.addClass(source_id, "navigation-hover");
		check_item_in_array(menuitem_array[source_id], source_id);
	}
}
function check_item_in_array(item, source_id) {
	clearTimeout(m_timer);
	var sub_menu_item = 'sub-menu' + item;
	if (last_displayed == '' || ((sub_menu_item.indexOf(last_displayed) != -1) && (sub_menu_item != last_displayed))) {
		do_menuItemAction(item, source_id);
	} else {
		var exit = false;
		count = 0;
		var the_last_displayed;
		while( !exit && displayed_menus.length > 0 ) {
			the_last_displayed = displayed_menus.pop();
			if ((sub_menu_item.indexOf(the_last_displayed.item) == -1)) {
				doClear(the_last_displayed.item, '');
				YAHOO.util.Dom.removeClass(the_last_displayed.source, "navigation-hover");
			}
			else {
				displayed_menus.push(the_last_displayed);
				exit = true;
				//do_menuItemAction(item, source_id);
			}
			count++;
		}
		do_menuItemAction(item, source_id);
	}
}
function do_menuItemAction(item, source_id) {
	if ($('sub-menu'+item)) {
		$('sub-menu'+item).style.display="block";
		YAHOO.util.Dom.addClass(source_id, "navigation-hover");
		displayed_menus.push({"item":'sub-menu'+item,"source":source_id});
		last_displayed = 'sub-menu'+item;
	}
}
function sub_menuItemAction(e) {
	clearTimeout(m_timer);
	if (!e) var e = window.event;
	e.cancelBubble = true;
	if (e.stopPropagation) e.stopPropagation();
	var source_id = '*';
	try {source_id = e.target.id;}
	catch (ex) {source_id = e.srcElement.id}
	if (source_id.indexOf("a-") == 0) {
		source_id = source_id.substr(2);
	}
	if (source_id && submenuitem_array[source_id]) {
		check_item_in_array(submenuitem_array[source_id], source_id);
		for (var i=0; i<displayed_menus.length; i++) {
			YAHOO.util.Dom.addClass(displayed_menus[i].source, "navigation-hover");
		}
	}
}
function clearBackground(e) {
	if (!e) var e = window.event;
	e.cancelBubble = true;
	if (e.stopPropagation) e.stopPropagation();
	var source_id = '*';
	try {source_id = e.target.id;}
	catch (ex) {source_id = e.srcElement.id}
	var source_id = (source_id.indexOf("a-") == 0) ? source_id.substr(2) : source_id;
	if (source_id && $(source_id) && menuitem_array[source_id]) {
		YAHOO.util.Dom.removeClass(source_id, "navigation-hover");
		clearMenu(e);
	}
}
function clearMenu(e) {
	clearTimeout(m_timer);
	m_timer = setTimeout(function() { doClearAll(); }, 300);
}
function doClear(item, type) {
	if ($(type+item)) {
		$(type+item).style.display="none";
	}
}
function doClearAll() {
	var the_last_displayed;
	while( displayed_menus.length > 0 ) {
		the_last_displayed = displayed_menus.pop();
		doClear(the_last_displayed.item, '');
		YAHOO.util.Dom.removeClass(the_last_displayed.source, "navigation-hover");
	}
	last_displayed = '';
}
/**
 * Automatic color detection for Google AdSense
 * @author Inez Korczynski
 */
var HCHARS = "0123456789ABCDEF";

function dec2hex(n) {
	n = parseInt(n, 10);
	n = (YAHOO.lang.isNumber(n)) ? n : 0;
	n = (n > 255 || n < 0) ? 0 : n;
	return HCHARS.charAt((n - n % 16) / 16) + HCHARS.charAt(n % 16);
}

function rgb2hex(r, g, b) {
	if (YAHOO.lang.isArray(r)) {
		return rgb2hex(r[0], r[1], r[2]);
	}
	return dec2hex(r) + dec2hex(g) + dec2hex(b);
}

function getHEX(color) {
	if(color == 'transparent') {
		return color;
	}

	if(color.match("^\#")) {
		return color.substring(1);
	}

	return rgb2hex(color.substring(4).substr(0, color.length-5).split(', '));
}

function AdGetColor(type) {

	if(typeof adColorsContent == 'undefined') {
		adColorsContent = new Array();
	}

	if(typeof themename == 'string') {
		if(typeof adColorsContent[themename] == 'object') {
			if(typeof adColorsContent[themename][type] == 'string') {
				return adColorsContent[themename][type];
			}
		}
	}

	if(typeof adColorsContent[type] == 'string') {
		return adColorsContent[type];
	}

	if(type == 'text') {
		adColorsContent[type] = getHEX(YAHOO.util.Dom.getStyle('article', 'color'));
		YAHOO.log("Detected - text: " + adColorsContent[type]);
		return adColorsContent[type];
	}

	if(type == 'link' || type == 'url') {
		var link;

		var editSections = YAHOO.util.Dom.getElementsByClassName('editsection', 'span', 'article');
		if(editSections.length > 0) {
			link = editSections[0].getElementsByTagName('a')[0];
		}

		if(link == null) {
			var links = $('bodyContent').getElementsByTagName('a');
			for(i = 0; i < links.length; i++) {
				if(!YAHOO.util.Dom.hasClass(links[i], 'new')) {
					link = links[i];
					break;
				}
			}
			if(link == null) {
				link = links[0];
			}
		}

		adColorsContent[type] = getHEX(YAHOO.util.Dom.getStyle(link, 'color'));
		YAHOO.log("Detected - text: " + adColorsContent[type]);
		return adColorsContent[type];
	}

	if(type == 'bg') {
        var color = getHEX(YAHOO.util.Dom.getStyle('article', 'background-color'));

        if(color == 'transparent' || color == AdGetColor('text')) {
	        color = getHEX(YAHOO.util.Dom.getStyle('wikia_page', 'background-color'));
        }

        if(color == 'transparent' || color == '000000') {
            color = getHEX(YAHOO.util.Dom.getStyle(document.body, 'background-color'));
        }

		adColorsContent[type] = color;
		YAHOO.log("Detected - text: " + adColorsContent[type]);
		return adColorsContent[type];
	}

}
/**
 * @author Inez Korczynski
 */
TieDivLib = new function() {

	var tieObjects = Array();

	var count = 0;

	var adjustY = (YAHOO.env.ua.ie > 0) ? -2 : 0;

	var adjustX = (YAHOO.env.ua.ie > 0) ? -YAHOO.util.Dom.getX('wikia_header') : 0;

	var inProcess = false;

	this.tie = function(source, target) {
		tieObjects.push([source, target]);
		$(source).style.position = 'absolute';
		if($(target).parentNode.parentNode.parentNode.parentNode.parentNode.id == 'spotlight_footer') {
			$(source).style.zIndex = 4;
		} else if(YAHOO.util.Dom.hasClass($(target).parentNode.parentNode, 'WidgetAdvertiser')) {
			$(source).style.zIndex = 6;
		} else {
			$(source).style.zIndex = 5;
		}
		$(source).style.visibility = 'visible';
		TieDivLib.process();
	}

	this.fixPositions = function() {
		if(!inProcess) {
			inProcess = true;
			for(i = 0; i < tieObjects.length; i++) {
				if(YAHOO.util.Dom.getXY(tieObjects[i][0]) != YAHOO.util.Dom.getXY(tieObjects[i][1])) {
					$(tieObjects[i][0]).style.top = (YAHOO.util.Dom.getY(tieObjects[i][1]) + adjustY) + 'px';
					$(tieObjects[i][0]).style.left = (YAHOO.util.Dom.getX(tieObjects[i][1]) + adjustX) + 'px';
				}
			}
			inProcess = false;
		}
	}

	this.process = function() {
		TieDivLib.fixPositions();
		setTimeout(TieDivLib.fixPositions, 180);
		setTimeout(TieDivLib.fixPositions, 360);
	}

	this.startTie = function() {
		YAHOO.util.Event.addListener(window, 'resize', TieDivLib.process);
		YAHOO.util.Event.addListener(window, 'click', TieDivLib.process);
		YAHOO.util.Event.addListener(window, 'load', TieDivLib.process);
		YAHOO.util.Event.addListener(window, 'keydown', TieDivLib.fixPositions);
		YAHOO.util.Event.addListener(window, 'focus', TieDivLib.fixPositions);
	}

	this.getTieObjects = function() {
		return tieObjects;
	}

};

function ad_call(adSpaceId, zoneId, pos) {

	curAdSpaceId = -1;

	if(document.getElementById('adSpace' + adSpaceId)) {
		if(pos.substring(0, 4) == 'FAST') {
			if(!FASTisValid(pos)) {
				return;
			}
		}

		curAdSpaceId = adSpaceId;

		document.write('<scr'+'ipt type="text/javascript">');
		document.write('var base_url = "http://wikia-ads.wikia.com/www/delivery/ajs.php";');
		document.write('base_url += "?loc=" + escape(window.location);');
		document.write('if(typeof document.referrer != "undefined") base_url += "&referer=" + escape(document.referrer);');
		document.write('if(typeof document.context != "undefined") base_url += "&context=" + escape(document.context);');
		document.write('if(typeof document.mmm_fo != "undefined") base_url += "&mmm_fo=1";');
		document.write('base_url += "&zoneid='+zoneId+'";');
		document.write('base_url += "&cb=" + Math.floor(Math.random()*99999999999);');
		document.write('if(typeof document.MAX_used != "undefined" && document.MAX_used != ",") base_url += "&exclude=" + document.MAX_used;');

/**
 * Parameters description
 * 1 - collision
 * 2 - no-collision
 * 3 - logged in
 * 4 - not logged in
 */

		if(pos == 'FAST_BOTTOM') {
			document.write('if(FASTisCollisionBottom()) base_url += "&source=1";');
		} else if(pos == 'FAST_TOP') {
			if(wgUserName != null) {
				document.write('if(FASTisCollisionTop()) base_url += "&source=13"; else base_url += "&source=23";');
			} else {
				document.write('if(FASTisCollisionTop()) base_url += "&source=14"; else base_url += "&source=24";');
			}
		}

		document.write('</scr'+'ipt>');
		document.write('<scr'+'ipt type="text/javascript" src="'+base_url+'"></scr'+'ipt>');

		document.write('<scr'+'ipt type="text/javascript">');
		document.write('if(curAdSpaceId != -1) { TieDivLib.tie("realAd'+adSpaceId+'", "adSpace"+curAdSpaceId); }');
		document.write('</scr'+'ipt>');
	}
}