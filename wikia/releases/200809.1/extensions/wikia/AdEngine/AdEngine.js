/* Utility class for functions related to AdEngine
 * @author Nick Sullivan
*/

function AdEngine (){
	this.bodyWrapper = 'bodyContent';
}

/* For pages that have divs floated right, clear right so they appear under a box ad
 * Param side should be either "left" or "right"
 * Code pulled originally from FAST.js, with some modifications.
 */
AdEngine.resetCssClear = function (side) {
	var Dom = YAHOO.util.Dom;
	Dom.getElementsBy(function(el) {
	if((el.nodeName == 'DIV' || el.nodeName == 'TABLE') &&
		    // el.id.substring(0,7) != 'adSpace' && 
		    Dom.getStyle(el, 'float') == side) {
			return true;
		}
		return false;

	}, null, this.bodyWrapper , function(el) {
			Dom.setStyle(el, 'clear', side);
	});

}
