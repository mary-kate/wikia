var FASTtoc = false;
var FASTtocY;
var FASTtocHeight;
var FASTcontentY = YAHOO.util.Dom.getY('bodyContent');

if($('toc')) {
	FASTtoc = true;
	FASTtocY = YAHOO.util.Dom.getY('toc');
	FASTtocHeight = $('toc').offsetHeight - 38;
}

YAHOO.log('FASTtoc: ' + FASTtoc);
YAHOO.log('FASTtocY: ' + FASTtocY);
YAHOO.log('FASTtocHeight: ' + FASTtocHeight);
YAHOO.log('FASTcontentY: ' + FASTcontentY);

function FASTfix(banner) {
	var Dom = YAHOO.util.Dom;

	YAHOO.log('FASTfix: ' + banner);

	if(banner == 'FAST1') {

		Dom.setStyle('adSpace' + curAdSpaceId, 'margin-bottom', '10px');
		Dom.setStyle('adSpace' + curAdSpaceId, 'text-align', 'center');

	} else if(banner == 'FAST2') {

		Dom.setStyle('adSpace' + curAdSpaceId, 'float', 'right');
		Dom.setStyle('adSpace' + curAdSpaceId, 'margin-bottom', '10px');
		Dom.setStyle('adSpace' + curAdSpaceId, 'margin-left', '10px');

		Dom.getElementsBy(function(el) {
			if((el.nodeName == 'DIV' || el.nodeName == 'TABLE') && el.id.substring(0,7) != 'adSpace' && Dom.getStyle(el, 'float') == 'right') {
				return true;
			}
			return false;
		}, null, 'bodyContent', function(el) {
			if((FASTtoc && Dom.getY(el) > FASTtocY && Dom.getY(el) - FASTtocHeight < FASTcontentY + 300) || Dom.getY(el) < FASTcontentY + 300) {
				Dom.setStyle(el, 'clear', 'right');
			}
		});

	} else if(banner == 'FAST3') {

		Dom.setStyle('adSpace' + curAdSpaceId, 'float', 'left');
		Dom.setStyle('adSpace' + curAdSpaceId, 'margin-right', '20px');
		Dom.setStyle('adSpace' + curAdSpaceId, 'margin-bottom', '10px');

		var sections = Dom.getElementsByClassName('mw-headline');
		var lastSectionY = Dom.getY(sections[sections.length - 2]);

		YAHOO.log('lastSectionY: ' + lastSectionY);

		Dom.getElementsBy(function(el) {
			if((el.nodeName == 'DIV' || el.nodeName == 'TABLE') && el.id.substring(0,7) != 'adSpace' && Dom.getStyle(el, 'float') == 'left') {
				return true;
			}
			return false;
		}, null, 'bodyContent', function(el) {
			if(Dom.getY(el) < (lastSectionY + 300 + 35)) {
				Dom.setStyle(el, 'clear', 'left');
			}
		});

	} else if(banner == 'FAST4') {

		Dom.setStyle('adSpace' + curAdSpaceId, 'float', 'right');
		Dom.setStyle('adSpace' + curAdSpaceId, 'margin-left', '10px');
		Dom.setStyle('adSpace' + curAdSpaceId, 'margin-bottom', '10px');

		var sections = Dom.getElementsByClassName('mw-headline');
		var lastSectionY = Dom.getY(sections[sections.length - 2]);

		YAHOO.log('lastSectionY: ' + lastSectionY);

		Dom.getElementsBy(function(el) {
			if((el.nodeName == 'DIV' || el.nodeName == 'TABLE') && el.id.substring(0,7) != 'adSpace' && Dom.getStyle(el, 'float') == 'right') {
				return true;
			}
			return false;
		}, null, 'bodyContent', function(el) {
			if(Dom.getY(el) < (lastSectionY + 300 + 35)) {
				Dom.setStyle(el, 'clear', 'right');
			}
		});

	} else if(banner == 'FAST5') {

		if($('adSpaceFAST5')) {
			curAdSpaceId = 'FAST5';
		}
		Dom.setStyle($('adSpace' + curAdSpaceId).parentNode, 'display', '');

	} else if(banner == 'FAST6') {

		Dom.setStyle($('adSpace' + curAdSpaceId).parentNode, 'display', '');

	} else if(banner == 'FAST7') {

		if($('adSpaceFAST7')) {
			curAdSpaceId = 'FAST7';
		}
		Dom.setStyle($('adSpace' + curAdSpaceId).parentNode, 'display', '');

	} else if(banner == 'FAST_HOME1') {

		Dom.setStyle('adSpace' + curAdSpaceId, 'margin-bottom', '10px');
		Dom.setStyle('adSpace' + curAdSpaceId, 'text-align', 'right');


	} else if(banner == 'FAST_HOME3') {

		Dom.setStyle($('adSpace' + curAdSpaceId).parentNode, 'display', '');

	} else if(banner == 'FAST_HOME4') {

		Dom.setStyle($('adSpace' + curAdSpaceId).parentNode, 'display', '');

	}

	if(banner == 'FAST1' || banner == 'FAST_HOME1') {
		Dom.setStyle('adSpace' + curAdSpaceId, 'margin-left', 'auto');
	}
	Dom.setStyle('adSpace' + curAdSpaceId, 'width', adSizes[banner][0]+'px');
	Dom.setStyle('adSpace' + curAdSpaceId, 'height', adSizes[banner][1]+'px');

	return true;
}

var adSizes= new Array(11)
adSizes["FAST1"] = [728,90];
adSizes["FAST2"] = [300,250];
adSizes["FAST3"] = [300,250];
adSizes["FAST4"] = [300,250];
adSizes["FAST5"] = [728,90];
adSizes["FAST_HOME1"] = [728,90];
adSizes["FAST6"] = [160,600];
adSizes["FAST7"] = [160,600];
adSizes["FAST_HOME3"] = [160,600];
adSizes["FAST_HOME4"] = [160,600];
adSizes["FAST_HOME2"] = [300,250];

function FASTisCollisionBottom() {
	var Dom = YAHOO.util.Dom;
	var sections = Dom.getElementsByClassName('mw-headline');
	var lastSectionY = Dom.getY(sections[sections.length - 2]);
	var tables = $('bodyContent').getElementsByTagName('table');
	for(var i = 0; i < tables.length; i++) {
		if(Dom.getY(tables[i]) > (lastSectionY + 30)) {
			YAHOO.log('FASTisCollisionBottom: true - 1');
			return true;
		}
	}

	if(lastSectionY < (Dom.getY('bodyContent') + 500)) {
		YAHOO.log('FASTisCollisionBottom: true - 2');
		return true;
	}

	YAHOO.log('FASTisCollisionBottom: false');
	return false;
}

function FASTisCollisionTop() {
	var Dom = YAHOO.util.Dom;

	var tables = $('bodyContent').getElementsByTagName('table');
	for(var i = 0; i < tables.length; i++) {
		if(tables[i].id != 'toc' && Dom.getStyle(tables[i], 'float') == 'none' && ((FASTtoc && Dom.getY(tables[i]) > FASTtocY && Dom.getY(tables[i]) - FASTtocHeight < FASTcontentY + 300) || (Dom.getY(tables[i]) < FASTcontentY + 300))) {
			YAHOO.log('FASTisCollisionTop: true');
			return true;
		}
	}
	YAHOO.log('FASTisCollisionTop: false');
	return false;
}

function FASTisLongArticle() {
	var res = ($('bodyContent').offsetHeight > 800 ? true : false);
	YAHOO.log('FASTisLongArticle: ' + res);
	return res;
}

function FASTisShortArticle() {
	var res = ($('bodyContent').offsetHeight < 400 ? true : false);
	YAHOO.log('FASTisShortArticle: ' + res);
	return res;
}

function FASTisValid(pos) {
	YAHOO.log("FASTisValid: " + pos);

	if(FASTisShortArticle()) {
		return false;
	}

	if(pos == 'FAST_SIDE' || pos == 'FAST_BOTTOM' || pos == 'FAST4' || pos == 'FAST5') {
		if(!FASTisLongArticle()) {
			return false;
		}
	}

	return true;
}
