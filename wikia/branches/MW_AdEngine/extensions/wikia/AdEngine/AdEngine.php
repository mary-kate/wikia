<?php
require dirname(__FILE__) . '/ArticleAdLogic.php';

$wgExtensionCredits['other'][] = array(
	'name' => 'AdEngine',
	'author' => 'Inez Korczynski, Nick Sullivan'
);

interface iAdProvider {
	public static function getInstance();
	public function getAd($slotname, $slot);
}

class AdEngine {

	const cacheKeyVersion = "1.5";

	const cacheTimeout = 1800;

	// TODO: pull these from wikicities.provider
	private $providers = array('1' => 'DART', '2' => 'OpenX', '3' => 'Google', '-1' => 'Null');

	private $slots = array();

	private $placeholders = array();

	protected static $instance = false;

	protected function __construct() {
		$this->loadConfig();
		global $wgAutoloadClasses;
		foreach($this->providers as $p) {
			$wgAutoloadClasses['AdProvider' . $p]=dirname(__FILE__) . '/AdProvider'.$p.'.php';
		}
	}

	public static function getInstance() {
		if(self::$instance == false) {
			self::$instance = new AdEngine();
		}
		return self::$instance;
	}

	public function loadConfig() {
		$skin_name = 'monaco'; // Hard code for now.
		global $wgMemc, $wgCityId;

		$cacheKey = wfMemcKey('slots', $skin_name, self::cacheKeyVersion);
		$this->slots = $wgMemc->get($cacheKey);

		if(is_array($this->slots)){
			// Found a cached value
			return true;
		}
			
		$db = wfGetDB(DB_SLAVE);

		$sql = "SELECT ad_slot.as_id, ad_slot.slot, ad_slot.size,
				COALESCE(adso.provider_id, ad_slot.default_provider_id) AS provider_id,
				COALESCE(adso.enabled, ad_slot.default_enabled) AS enabled
				FROM wikicities.ad_slot
				LEFT OUTER JOIN wikicities.ad_slot_override AS adso
				  ON ad_slot.as_id = adso.as_id AND city_id=".intval($wgCityId)."
				WHERE skin='".$db->strencode($skin_name)."'";

		$res = $db->query($sql);

		while($row = $db->fetchObject($res)){
			$this->slots[$row->slot] = array(
				'as_id' => $row->as_id,
				'size' => $row->size,
				'provider_id' => $row->provider_id,
				'enabled' => $row->enabled
			);
		}

		$sql = "SELECT * FROM wikicities.ad_provider_value WHERE
			 (city_id = ".intval($wgCityId)." OR city_id IS NULL) ORDER by city_id";
		$res = $db->query($sql);
		while($row = $db->fetchObject($res)) {
			 foreach($this->slots as $slotname => $slot) {
			 	if($slot['provider_id'] == $row->provider_id){
					$this->slots[$slotname]['provider_values'][$row->keyname] = $row->keyvalue;
			 	}
			 }
		}
		$wgMemc->set($cacheKey, $this->slots, self::cacheTimeout);

		return true;
	}

	// For the selected provider, get an ad tag. Logic for hiding/displaying ads
	// should be here, not in the skin.
	public function getAd($slotname) {
		global $wgShowAds, $wgUser;

		if(empty($this->providers[$this->slots[$slotname]['provider_id']])) {
			// Note: Don't throw an exception here. Fail gracefully for ads,
			// don't under any circumstances fail the rendering of the page
			$AdProviderNull=new AdProviderNull('Unrecognized Providerid', true);
			return $AdProviderNull->getAd($slotname, $this->slots[$slotname]);

		} else if ( $wgShowAds == false ){

			$AdProviderNull=new AdProviderNull('$wgShowAd set to false', false);
			return $AdProviderNull->getAd($slotname, $this->slots[$slotname]);

		} else if ( is_object($wgUser) && $wgUser->isLoggedIn() && !$wgUser->getOption('showAds') ){

			$AdProviderNull=new AdProviderNull('User is logged in', false);
			return $AdProviderNull->getAd($slotname, $this->slots[$slotname]);
			
		} else {

			$provider = $this->getAdProvider($this->slots[$slotname]['provider_id']);
			return $provider->getAd($slotname, $this->slots[$slotname]);

		}
	}

	private function getAdProvider($provider_id) {
		if($this->providers[$provider_id] == 'DART') {
			return AdProviderDART::getInstance();
		} else if($this->providers[$provider_id] == 'OpenX') {
			return AdProviderOpenX::getInstance();
		} else {
			// Note: Don't throw an exception here. Fail gracefully for ads,
			// don't under any circumstances fail the rendering of the page
			return new AdProviderNull("Unrecognized provider_id ($provider_id)", true);
		}
	}

	/* Size is stored as $widthx$size character column. Split here.
 	 * You may be asking, why not just store it as separate values to be begin with?
 	 * Because size is not always height/width. Possible values for size include:
 	 * 728x60
 	 * 300x250,300x600
 	 * 728x*
 	 *
 	 * Do the best you can to return a height/width 
 	 */	
        public function getHeightWidthFromSize($size){
                if (preg_match('/^([0-9]{2,4})x([0-9]{2,4})/', $size, $matches)){
                        return array('width'=>$matches[1], 'height'=>$matches[2]);
                } else if (preg_match('/^([0-9]{2,4})x\*/', $size, $matches)){
                        return array('width'=>$matches[1], 'height'=>'*');
                } else {
                        return false;
                }
        }


	/* For delayed ad loading, we have a place holder div that gets placed in the content,
	   to be loaded at the bottom of the page with an absolute position.
	   Keep track fo the placeholders for future refence */
	public function getPlaceHolderDiv($slotname, $reserveSpace=true){
		$style = "";

		if (! empty($this->slots[$slotname])){
			if($this->slots[$slotname]['enabled'] == 'No'){
				// if the ad is disabled, hide the div
				$style = ' style="display:none"';
			} else {
				if ($reserveSpace == true ){
					$dim = self::getHeightWidthFromSize($this->slots[$slotname]['size']);
					if (!empty($dim['width'])){
						$style = " style=\"width: {$dim['width']}px; height: {$dim['height']}px\"";
					}
				}

				// We will use this at the bottom of the page for ads.
				$this->placeholders[] = $slotname;
			}
		}


		return "<div id=\"$slotname\"$style></div>";
	}


	public function getDelayedLoadingCode(){
		if (empty($this->placeholders)){
			// No delayed ads on this page
			return '<!-- No placeholders called for ' . __METHOD__ . " -->\n";
		}

$out = <<<EOT
<script>
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
		var extraY = GetElementAbsolutePos($('monaco_shrinkwrap_main')).y;

		for(i = 0; i < items.length; i++) {
			YAHOO.log("slotname: " + items[i][0]);
			Dom.setStyle(items[i][0]+'_load', 'position', 'absolute');
			Dom.setStyle(items[i][0]+'_load', 'zIndex', 100);
			//alert('setting ' + items[i][0] + '_load to ' + getAbsolutePosition($(items[i][0])).y); 
			//Dom.setY(items[i][0]+'_load', GetElementAbsolutePos($(items[i][0])).y);
			//Dom.setStyle(items[i][0]+'_load', 'pixelTop', GetElementAbsolutePos($(items[i][0])).y + 'px');
			//Dom.setStyle(items[i][0]+'_load', 'right', Dom.getClientWidth() - Dom.getX(items[i][0]) - $(items[i][0]).offsetWidth + 'px');
			//Dom.setStyle(items[i][0]+'_load', 'top', parseFloat(getAbsolutePosition($(items[i][0])).y) + 'px');
		}
	}

}
</script>
EOT;
		$out .= "<!-- #### BEGIN " . __CLASS__ . '::' . __METHOD__ . " ####-->\n";
		foreach ($this->placeholders as $slotname){
			$out .= '<div id="' . $slotname . '_load">' . $this->getAd($slotname) . "</div>\n";
			$out .= "<script>TieDivLibrary.tie('{$slotname}');</script>\n";
		}	
		$out .= "<!-- #### END " . __CLASS__ . '::' . __METHOD__ . " ####-->\n";
		$out .= "<script>TieDivLibrary.calculate();</script>\n";
		return $out;
	}


	public function getPlaceHolders(){
		return $this->placeholders;
	}
}
