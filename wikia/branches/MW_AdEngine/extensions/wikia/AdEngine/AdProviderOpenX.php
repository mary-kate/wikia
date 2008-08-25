<?php

$wgExtensionCredits['other'][] = array(
	'name' => 'OpenX ad provider for AdEngine',
);

class AdProviderOpenX implements iAdProvider {

	protected static $instance = false;

	public static function getInstance() {
		if(self::$instance == false) {
			self::$instance = new AdProviderOpenX();
		}
		return self::$instance;
	}

	private $zoneIds = array(	'HOME_TOP_LEADERBOARD' => 626,
								'HOME_TOP_RIGHT_BOXAD' => 627,
								'HOME_LEFT_SKYSCRAPER_1' => 628,
								'HOME_LEFT_SKYSCRAPER_2' => 629,
								'TOP_LEADERBOARD' => 630,
								'TOP_RIGHT_BOXAD' => 631,
								'LEFT_SKYSCRAPER_1' => 632,
								'LEFT_SKYSCRAPER_2' => 633,
								'FOOTER_BOXAD' => 634,
								'LEFT_SPOTLIGHT_1' => 635,
								'FOOTER_SPOTLIGHT_LEFT' => 635,
								'FOOTER_SPOTLIGHT_MIDDLE' => 635,
								'FOOTER_SPOTLIGHT_RIGHT' => 635);

	public function getAd($slotname, $slot) {

		if(empty($this->zoneIds[$slotname])) {
			// Don't throw an exception. Under no circumstances should an ad failing
			// prevent the page from rendering.
                        $NullAd = new NullAd("Invalid slotname ($slotname) for " . __CLASS__);
                        return $NullAd->getAd();
		}

		$zoneId = $this->zoneIds[$slotname];

		$adtag = <<<EOT
<!-- AdProviderOpenX slot: $slotname zoneid: $zoneId  -->
<script type='text/javascript'>
   var source = Array();
   source.push('slot=$slotname');
   source.push('catid=' + wgCatId);
   source.push('lang=' + wgContentLanguage);

  document.write('<scr'+'ipt type="text/javascript">');
  document.write('var base_url = "http://wikia-ads.wikia.com/www/delivery/ajs.php";');
  document.write('base_url += "?loc=" + escape(window.location);');
  document.write('if(typeof document.referrer != "undefined") base_url += "&referer=" + escape(document.referrer);');
  document.write('if(typeof document.context != "undefined") base_url += "&context=" + escape(document.context);');
  document.write('if(typeof document.mmm_fo != "undefined") base_url += "&mmm_fo=1";');
  document.write('base_url += "&zoneid=$zoneId";');
  document.write('base_url += "&cb=" + Math.floor(Math.random()*99999999999);');
  document.write('if(typeof document.MAX_used != "undefined" && document.MAX_used != ",") base_url += "&exclude=" + document.MAX_used;');
  document.write('base_url += "&source='+source.join(';')+'";');
  document.write('base_url += "&block=1";');
  document.write('</scr'+'ipt>');
  document.write('<scr'+'ipt type="text/javascript" src="'+base_url+'"></scr'+'ipt>');

</script>
EOT;
		return $adtag;

	}

}
