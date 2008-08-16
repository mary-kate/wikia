<?php

$wgExtensionCredits['other'][] = array(
	'name' => 'DART ad provider for AdEngine',
);

class AdProviderDART implements iAdProvider {

	private $isMainPage;

	protected static $instance = false;

	public static function getInstance() {
		if(self::$instance == false) {
			self::$instance = new AdProviderDART();
		}
		return self::$instance;
	}

	protected function __construct() {
		global $wgTitle;
		$this->isMainPage = $wgTitle->getArticleId() == Title::newMainPage()->getArticleId();
	}

	public function getAd($slotname, $slot){

		$url = 'http://ad.doubleclick.net/';
		$url .= $this->getAdType() . '/';
		$url .= $this->getDartSite() . '/';
		$url .= $this->getZone1() . '/';
		$url .= $this->getZone2() . ';';
		$url .= 's1=' . $this->getZone1() . ';'; // this seems redundant
		$url .= 's2=' . $this->getZone2() . ';';
		$url .= 'pos=' . $slotname . ';';
		$url .= 'kw=' . urlencode($this->getSearchKeywords()) . ';';
		$url .= $this->getKeyValues($slot);
		$url .= $this->getArticleID();
		$url .= "tile=" . $this->getTile($slotname) . ';';
		$url .= "dcopt=" . $this->getDcopt($slotname) . ';';
		$url .= "sz=" . $slot['size'] . ';';
		// special "end" delimiter per Michael
		$url .= 'endtag=$;';
		$url .= "ord=" . $this->getSessionRandom();  // FIXME change to javascript rand

		return $url;

		/*
		$out = "<!-- " . __CLASS__ . " slot: $slotname , " . print_r($slot, true) . "-->";
		$out .= '<script src="' . $url . '" type="text/javascript"></script>';

		return $out;
		*/
	}

	/* From DART Webmaster guide:
	 * ad - For a standard image-based ad.
	 * adf - In a frame.
	 * adl - In a layer.
	 * adi - In an iframe.
	 * adj - Served using JavaScript.
	 * adx - Served using streaming technologies.
	 */
	function getAdType(){
		// Someday we may want to change this dynamically.
		return 'adj';
	}

	function getDartSite(){
		global $wgCat;
		// Why oh why couldn't they have made this easier?
		switch(@$wgCat['name']){
			case 'Auto' : return 'wka.auto';
			case 'Creative' : return 'wka.crea';
			case 'Education' : return 'wka.edu';
			case 'Entertainment' : return 'wka.ent';
			case 'Finance' : return 'wka.fin';
			case 'Gaming' : return 'wka.gaming';
			case 'Green' : return 'wka.green';
			case 'Humor' : return 'wka.humor';
			case 'Lifestyle' : return 'wka.life';
			case 'Music' : return 'wka.music';
			case 'Philosophy and Religion' : return 'wka.phil';
			case 'Politics and Activism' : return 'wka.poli';
			case 'Science and Nature' : return 'wka.sci';
			case 'Sports' : return 'wka.sports';
			case 'Technology' : return 'wka.tech';
			case 'Test Site' : return 'wka.test';
			case 'Toys' : return 'wka.toys';
			case 'Travel' : return 'wka.travel';
			// "Miscellaneous" goes to default.
			default: return 'wka.wikia';
		}
	}

	// Effectively the dbname, defaulting to wikia.
	function getZone1(){
		global $wgDBname;
		// Zone1 is prefixed with "_" because zone's can't start with a number, and some dbnames do.
		if(empty($wgDBname)) {
			return '_wikia';
		} else {
			return '_' . $wgDBname;
		}
	}

	// Page type, ie, "home" or "article"
	function getZone2(){
		if($this->isMainPage) {
			return 'home';
		} else {
			return 'article';
		}
	}

	function getDartUrl(){
		global $wgTitle;
		return $wgTitle->getText();
		/* From DART doc:
		url is a key value that pulls in page-specific attributes from the actual page url (forward slashes and other non alpha-numeric characters must be converted to underscores) */
		// Nick wrote: I don't know what that really means. I asked for clarification
		// via skype, leaving this blank until I know what it means.
	}


	function getKeyValues($slot){
		if(empty($slot['provider_values'])){
			return '';
		}

		$out='';
		foreach ($slot['provider_values'] as $keyname => $keyvalue){
			$out .= $keyname . '=' . urlencode($keyvalue) . ';';
		}
		return $out;
	}

	function getTile($slotname){
		/* From DART doc:
		 * tile=1 is a parameter that, in conjunction with other sequential tile values on a page, will enable the competitive categories and roadblock features to work. Tile values should match the amount of ads on a given page, but they do not necessarily need to match the order in which the ads appear.													*/
		// Nick wrote: Chose to hard code this for now based on slot, for simplicity
		switch($slotname) {
			case 'TOP_LEADERBOARD': return 1;
			case 'TOP_RIGHT_BOXAD': return 2;
			case 'LEFT_SKYSCRAPER_1': return 3;
			case 'LEFT_SKYSCRAPER_2': return 4;
			case 'FOOTER_BOXAD': return 5;
			case 'HOME_TOP_LEADERBOARD': return 1;
			case 'HOME_TOP_RIGHT_BOXAD': return 2;
			case 'HOME_LEFT_SKYSCRAPER_1': return 3;
			case 'HOME_LEFT_SKYSCRAPER_2': return 4;
			default: return '';
		}
	}

	function getDcopt($slotname){
		/* From DART doc:
			dcopt=ist is a parameter that enables interstitial ad types to run.
			This should only be included in the top tag on each page.
		*/
		// Nick wrote: Chose to hard code this for now based on slot, for simplicity
		switch ($slotname){
			case 'TOP_LEADERBOARD': return 'ist';
			case 'HOME_TOP_LEADERBOARD': return 'ist';
			default: return '';
		}
	}

	/* If the user did a search, return the term for keyword targeting.
	 * If no search was done, false is returned.
	 * Note that this is raw input from the user, and should be escaped.
	 */
	public function getSearchKeywords(){
		if(isset($_GET['search'])){
			return $_GET['search'];
		} else {
			return false;
		}
	}

	// A "cache busting" random number, but constant across the script invocation,
	// Note DART will prevent the same ad twice if the number is the same
	public function getSessionRandom() {
		static $number = null;
		if(is_null($number)){
			$number = mt_rand(1, 99999999);
		}
		return $number;
	}

	
	// Title is one of the always-present key-values
	public function getArticleID(){
		global $wgTitle;
		if (is_object($wgTitle)){
			return "articleid=" . $wgTitle->getArticleID() . ';';
		} else {
			return '';
		}
	}
	
}
