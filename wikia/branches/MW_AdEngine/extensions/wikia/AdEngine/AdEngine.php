<?php

$wgExtensionCredits['other'][] = array(
	'name' => 'AdEngine',
	'author' => 'Inez Korczynski, Nick Sullivan'
);

interface iAdProvider {
	public static function getInstance();
    public function getAd($slotname, $slot);
}

class AdEngine {

	const cacheKeyVersion = "1.2";

	const cacheTimeout = 1800;

	private $providers = array(1 => 'DART', 2 => 'OpenX');

	private $slots = array();

	protected static $instance = false;

	protected function __construct() {
		$this->loadConfig();
		foreach($this->providers as $provider) {
			require('AdProvider'.$provider.'.php');
		}
	}

	public static function getInstance() {
		if(self::$instance == false) {
			self::$instance = new AdEngine();
		}
		return self::$instance;
	}

	public function loadConfig() {
		$skin_name = 'monaco';
		global $wgMemc, $wgCityId;

		$cacheKey = wfMemcKey('slots', $skin_name, self::cacheKeyVersion);
		$this->slots = $wgMemc->get($cacheKey);

		if(empty($this->slots) && !is_array($this->slots)) {
			$db = wfGetDB(DB_SLAVE);

			$sql = "SELECT ad_slot.id, ad_slot.name, ad_slot.size,
					COALESCE(adso.provider_id, ad_slot.provider_id) AS provider_id,
					COALESCE(adso.enabled, ad_slot.enabled) AS enabled
					FROM wikicities.ad_slot
					LEFT OUTER JOIN wikicities.ad_slot_override AS adso
					  ON ad_slot.id = adso.id AND city_id=".intval($wgCityId)."
					WHERE skin='".$db->strencode($skin_name)."'";

			$res = $db->query($sql);

			while($row = $db->fetchObject($res)){
				$this->slots[$row->name] = array(
					'ad_slot_id' => $row->id,
					'size' => $row->size,
					'provider_id' => $row->provider_id,
					'enabled' => $row->enabled
				);
			}

			$sql = "SELECT * FROM wikicities.ad_provider_value WHERE
				 (city_id = ".intval($wgCityId)." OR city_id IS NULL)";
			$res = $db->query($sql);
			while($row = $db->fetchObject($res)) {
				 foreach($this->slots as $slotname => $slot) {
				 	if($slot['provider_id'] == $row->provider_id){
						$this->slots[$slotname]['provider_values'][$row->keyname] = $row->keyvalue;
				 	}
				 }
			}
			$wgMemc->set($cacheKey, $this->slots, self::cacheTimeout);
		}

		return true;
	}

	public function getAd($slotname) {
		if(!empty($this->providers[$this->slots[$slotname]['provider_id']])) {
			$provider = $this->getAdProvider($this->slots[$slotname]['provider_id']);
			return $provider->getAd($slotname, $this->slots[$slotname]);
		} else {
			throw new Exception();
		}
	}

	private function getAdProvider($provider_id) {
		if($this->providers[$provider_id] == 'DART') {
			return AdProviderDART::getInstance();
		} else if($this->providers[$provider_id] == 'OpenX') {
			return AdProviderOpenX::getInstance();
		} else {
			throw new Exception();
		}
	}
}
