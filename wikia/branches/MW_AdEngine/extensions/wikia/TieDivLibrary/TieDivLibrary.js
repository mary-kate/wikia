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
		alert(r.y);
	}
	return r;
};

var __isFireFox = navigator.userAgent.match(/gecko/i);


//returns the absolute position of some element within document
function GetElementAbsolutePos(element) {
	var res = new Object();
	res.x = 0; res.y = 0;
	if (element !== null) {
		res.x = element.offsetLeft; 
		res.y = element.offsetTop; 
    	
		var offsetParent = element.offsetParent;
		var parentNode = element.parentNode;

		while (offsetParent !== null) {
			res.x += offsetParent.offsetLeft;
			res.y += offsetParent.offsetTop;

			if (offsetParent != document.body && offsetParent != document.documentElement) {
				res.x -= offsetParent.scrollLeft;
				res.y -= offsetParent.scrollTop;
			}
			//next lines are necessary to support FireFox problem with offsetParent
			if (__isFireFox) {
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

	this.tie = function(slotname) {
		items.push([slotname]);
	}

	this.calculate = function() {
		//var extraY = Dom.getY('monaco_shrinkwrap_main');
		var extraY = GetElementAbsolutePos(Dom.get('monaco_shrinkwrap_main')).y;

		for(i = 0; i < items.length; i++) {
			YAHOO.log("slotname: " + items[i][0]);
			Dom.setStyle(items[i][0]+'_load', 'position', 'absolute');
			Dom.setStyle(items[i][0]+'_load', 'zIndex', 100);
			Dom.setY(items[i][0]+'_load', GetElementAbsolutePos(Dom.get(items[i][0])).y);
			Dom.setX(items[i][0]+'_load', GetElementAbsolutePos(Dom.get(items[i][0])).x);
		}
	}

}

