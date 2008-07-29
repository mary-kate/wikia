/**
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
		return adColorsContent[type];
	}

}

/**
 * @author Inez Korczynski
 */
function ad_call(adSpaceId, zoneId, pos) {

	curAdSpaceId = -1;

	if($('adSpace' + adSpaceId)) {

		if(pos.substring(0, 4) == 'FAST' && !FASTisValid(pos)) {
			return;
		}

		curAdSpaceId = adSpaceId;

 		var source = Array();
 		source.push('cat=' + wgWikiaAdvertiserCategory);
 		source.push('lang=' + wgContentLanguage);
		if(pos == 'FAST_BOTTOM' && FASTisCollisionBottom()) {
			source.push('fast=1');
		} else if(pos == 'FAST_TOP') {
			source.push('fast=' + (FASTisCollisionTop() ? '14' : '24'));
		}

		document.write('<scr'+'ipt type="text/javascript">');
		document.write('var base_url = "http://wikia-ads.wikia.com/www/delivery/ajs.php";');
		document.write('base_url += "?loc=" + escape(window.location);');
		document.write('if(typeof document.referrer != "undefined") base_url += "&referer=" + escape(document.referrer);');
		document.write('if(typeof document.context != "undefined") base_url += "&context=" + escape(document.context);');
		document.write('if(typeof document.mmm_fo != "undefined") base_url += "&mmm_fo=1";');
		document.write('base_url += "&zoneid='+zoneId+'";');
		document.write('base_url += "&cb=" + Math.floor(Math.random()*99999999999);');
		document.write('if(typeof document.MAX_used != "undefined" && document.MAX_used != ",") base_url += "&exclude=" + document.MAX_used;');
		document.write('base_url += "&source='+source.join(';')+'";');
		document.write('</scr'+'ipt>');
		document.write('<scr'+'ipt type="text/javascript" src="'+base_url+'"></scr'+'ipt>');
	}
}

/**
 * @author Inez Korczynski
 */
TieDivLib = new function() {

	var items = Array();

	var interval = 300;

	var block = false;

	var adjustY = ((YAHOO.env.ua.ie > 0) ? 2 : 0) + YAHOO.util.Dom.getY('monaco_shrinkwrap_main');

	var adjustX = (YAHOO.env.ua.ie > 0) ? YAHOO.util.Dom.getX('wikia_header') : 0;

	this.tie = function(source, target, pos) {

		if($(target).style.height == '') {
			$(target).style.height = '75px';
			$(target).style.width = '200px';
			$(target).style.margin = '0 auto';
		}

		items.push([source, target]);

		$(source).style.display = '';
		$(source).style.position = 'absolute';

		if(pos == 'bl' || pos == 'r' || pos == 'FAST_SIDE') {
			$(source).style.zIndex = 21;
		} else {
			$(source).style.zIndex = 5;
		}
	}

	this.start = function() {
		TieDivLib.timer();
		YAHOO.util.Event.addListener(window, 'resize', TieDivLib.recalc);
		YAHOO.util.Event.addListener(window, 'click', function() {
			TieDivLib.recalc();
			setTimeout(TieDivLib.recalc, 200);
		});
		YAHOO.util.Event.addListener(window, 'load', function() {
			TieDivLib.setInterval(5000);
		});
		YAHOO.util.Event.addListener(window, 'keydown', function() {
			TieDivLib.recalc();
			TieDivLib.setInterval(300);
		});
		YAHOO.util.Event.addListener(window, 'mousedown', function() {
			TieDivLib.recalc();
			TieDivLib.setInterval(300);
		});
		YAHOO.util.Event.addListener(window, 'keyup', function() {
			TieDivLib.recalc();
			TieDivLib.setInterval(5000);
		});
		YAHOO.util.Event.addListener(window, 'mouseup', function() {
			TieDivLib.recalc();
			TieDivLib.setInterval(5000);
		});
	}

	this.timer = function() {
		TieDivLib.recalc();
		setTimeout(TieDivLib.timer, interval);
	}

	this.recalc = function() {
		if(block) {
			return;
		}
		block = true;
		for(i = 0; i < items.length; i++) {
			if(YAHOO.util.Dom.getXY(items[i][0]) != YAHOO.util.Dom.getXY(items[i][1])) {
				$(items[i][0]).style.top = (YAHOO.util.Dom.getY(items[i][1]) - adjustY) + 'px';
				$(items[i][0]).style.left = (YAHOO.util.Dom.getX(items[i][1]) - adjustX) + 'px';
			}
		}
		block = false;
	}

	this.setInterval = function(n) {
		interval = n;
	}

	this.getItems = function() {
		return items;
	}
}