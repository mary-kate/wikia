/**
 * http://acko.net/blog/mouse-handling-and-absolute-positions-in-javascript
 */
function getAbsolutePosition(element){
	var r = {
		x:element.offsetLeft,
		y:element.offsetTop
	};
	if(element.offsetParent){
		var tmp = getAbsolutePosition(element.offsetParent);
		r.x += tmp.x;
		r.y += tmp.y;
	}
	return r;
};

var __isFireFox = navigator.userAgent.match(/gecko/i);


//returns the absolute position of some element within document
function GetElementAbsolutePos(element) {
	var res = {x:0, y:0};

	if (element !== null) {

		// http://www.quirksmode.org/dom/w3c_cssom.html
		// works in IE5.5+, FF3 (almost) and Opera9.51+
		if (document.documentElement.getBoundingClientRect) {
			var rect = element.getBoundingClientRect();
			res.x = rect.left;
			res.y = Math.round(rect.top); // ff doesn't round

			YAHOO.log('using getBoundingClientRect()', 'info', 'TieDivLib');
			YAHOO.log(rect);

			return res;
		}

		res.x = element.offsetLeft; 
		res.y = element.offsetTop; 
    	
		var offsetParent = element.offsetParent;
		var parentNode = element.parentNode;

		while (offsetParent != null) {
			res.x += offsetParent.offsetLeft;
			res.y += offsetParent.offsetTop;

			if (offsetParent != document.body && offsetParent != document.documentElement) {
				res.x -= offsetParent.scrollLeft;
				res.y -= offsetParent.scrollTop;
				YAHOO.log(offsetParent.scrollLeft);
				YAHOO.log(offsetParent.scrollTop);
			}
			//next lines are necessary to support FireFox problem with offsetParent
			if (__isFireFox) {

				// ff2 bug fix (ads within article content) - include #wikia_page border width
				if (offsetParent.id && offsetParent.id == 'wikia_page') {
					res.x += 1;
					res.y += 1;
				}

				while (offsetParent != parentNode && parentNode !== null) {
					res.x -= parentNode.scrollLeft;
					res.y -= parentNode.scrollTop;
					
					parentNode = parentNode.parentNode;
				}    
			}
			parentNode = offsetParent.parentNode;
			offsetParent = offsetParent.offsetParent;
		}
	}

	return res;
}


/**
 * @author Inez Korczynski
 */
TieDivLibrary = new function() {

	var Dom = YAHOO.util.Dom;

	var items = Array();

	var browser = Array();

	this.init = function() {
		//new YAHOO.widget.LogReader(null, {width: "350px", height: "300px", draggable: true}); // setup onpage logger 
		//Dom.addClass('body', 'yui-skin-sam');

		this.browser = YAHOO.env.ua;
	}

	this.tie = function(slotname) {
		items.push([slotname]);
	}

	this.getXY = function(element) {

		var pos = {x:0, y:0};

		element = Dom.get(element);

		// FireFox3+ and Opera 9.51+
		if ( (this.browser.gecko >= 1.9) || (this.browser.opera >= 9.51) ) {
			[pos.x, pos.y] = Dom.getXY(element);
		}
		// all the rest
		else {
			pos = GetElementAbsolutePos(element);
		}

		// offset for IE
		if (this.browser.ie) {
			pos.x -= 2;
			pos.y -= 2;

			// spotlights fix
			if ( Dom.hasClass(element, 'wikia_spotlight') ) {
				pos.y -= 1;
			}
		}

		pos.x = Math.round(pos.x);
		pos.y = Math.round(pos.y);

		YAHOO.log('getXY for ' + element.id + ': (' + pos.x + ', ' + pos.y + ')', 'info', 'TieDivLib');
		
		return pos;
	}

	this.calculate = function() {
		for(i = 0; i < items.length; i++) {
			var pos = this.getXY(items[i][0]);
			Dom.setStyle(items[i][0]+'_load', 'left', pos.x + 'px');
			Dom.setStyle(items[i][0]+'_load', 'top',  pos.y + 'px');
		}
	}

	this.init();
}

