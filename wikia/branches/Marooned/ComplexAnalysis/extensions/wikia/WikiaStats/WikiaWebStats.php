<?php

/**
 * @package MediaWiki
 * @subpackage WikiaStats
 * @author Piotr Molski <moli@wikia.com> for Wikia.com
 * @version: 0.1
 *
 * container for configuration variable
 */

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "This is MediaWiki extension named WikiaWebStats.\n";
    exit( 1 ) ;
}

/**
 *
 */
$wgHooks['SkinAfterBottomScripts'][] = 'wfWikiaWebStatsScript';

function wfWikiaWebStatsScript($this, $bottomScriptText) {
    global $wgCityId, $wgDotDisplay, $wgAdServerUrl, $wgAdServerTest;
    
    if (!empty($wgDotDisplay)) {
        $wWebStats = new WikiaWebStats( $wgCityId, 0);
        $test = (!empty($wgAdServerTest))?"db_test=1&":"";
        $bottomScriptText .= "<!-- 1dot statistics -->\n";
        $bottomScriptText .= "<img src=\"".$wgAdServerUrl."/1dot.php?".$test."js=".$wWebStats->makeStatUrl()."\" alt=\".\" width=\"1\" height=\"1\" border=\"0\" />\n";
        $bottomScriptText .= "<!-- end of 1dot statistics -->\n";
    }

    return true;
}

class WikiaWebStats {
	var $mCity_id, $mTimestamp;

    /**
     * constructor
     *
     * city could be int like city_id
     * or string like city_domain
     */
    public function __construct( $city, $time = 0 ) {
    	$domain = $_SERVER['SERVER_NAME'];
        if ( $this->pIsInt($city) ) {
            $this->mCity_id = $city;
        }
        else {
        	$this->mCity_id = $this->pGetDomainFromCache($domain);
        	if ( (empty($this->mCity_id)) || (!$this->pIsInt($this->mCity_id)) ) {
        		$this->mCity_id = $this->pDomainToId( $domain );
			}
        }

        if (!empty($time)) {
        	$this->mTimestamp = $time;
        } else {
        	$this->mTimestamp = time();
        }
    }

    /**
     * public methods
     */
    public function getStatsTime()
    {
        return $this->mTimestamp;
    }

    public function getCityId()
    {
        return $this->mCity_id;
    }

    /**
     * get city ID for selected domain
     *
     * retun $cityid
     */
    private function pDomainToId( $domain ) {
    	$dbr = wfGetDB( DB_MASTER );
    	$dbr->selectDB("wikicities");

    	$sth = $dbr->select( "city_domains","city_id, city_domain",array("city_domain" => $domain), array("limit" => 1) );
    	$city_id = 0;
    	if ($row = $dbr->fetchObject( $sth ))
    	{
    		$city_id = $row->city_id;
		}
    	$dbr->freeResult( $sth );
    	#---
    	return $city_id;
    }

    /**
     * @access private
     * @param $domain string - domain name
     * @author eloy@wikia
     *
     * @return id of wiki or null
     */
    private function pGetDomainFromCache( $domain )
    {
        if (method_exists("WikiFactory", "DomainToID")) {
            return WikiFactory::DomainToID( $domain );
        }
        return null;
    }

    /**
     * check that $number is really integer
     *
     * retun true|false
     */
    private function pIsInt($number) {
    	return (is_numeric($number) ? intval($number) == $number : false);
    }

    /**
     * create base64 string with data
     *
     * retun base64String
     */
	public function makeStatUrl () {
		global $wgUser, $wgAllowUserJs, $wgRequest, $wgTitle;

		//"timestamp"		=> date('Y-m-d H:i:s', $this->mTimestamp),
		$tUrl = array(
		    "city_id"		=> $this->mCity_id,
			"user_id"		=> $wgUser->mId,
			"host"			=> $_SERVER['HTTP_HOST'],
			"referer"		=> isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : "",
			"destination"	=> htmlspecialchars("http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']),
			"agent"			=> (array_key_exists('HTTP_USER_AGENT', $_SERVER)) ? htmlspecialchars($_SERVER['HTTP_USER_AGENT']) : "",
			"script_name"	=> htmlspecialchars($_SERVER['PHP_SELF']),
			"server_name"	=> $_SERVER['SERVER_NAME'],
			"remote_addr"	=> wfGetIP(),
			"query_param"	=> htmlspecialchars($_SERVER['QUERY_STRING']),
			"request_uri"	=> htmlspecialchars($_SERVER['REQUEST_URI']),
			// 0 - anonymous, 1 - registered and logged-in, 2 - registered and logged-out
			"islogged"		=> (empty($tSkin->loggedin))?0:(!empty($_COOKIE['wikicitiesUserID']))?1:2,
			"namespace"		=> $wgTitle->getNamespace(),
			"article_id"	=> $wgTitle->getArticleId(),
			"isallowjs"		=> $wgAllowUserJs,
			"session"		=> (!empty($_COOKIE['wikicities_session']))?$_COOKIE['wikicities_session']:"",
			"token"			=> (!empty($wgUser->mToken))?$wgUser->mToken:"",
			"unique_key"	=> 0,//$this->makeUniqueToken()
		);

		return base64_encode(serialize($tUrl));
	}

	/**
	 *
	 * generate unique id (token)
	 *
	 */
	 public function makeUniqueToken() {

		// FIXME
		// we must not start session on every page view
		return 0;

		if (!session_id()) {
			session_start();
		}

	 	if (empty($_SESSION['wgWikiaUniqueBrowserId'])) {
	 		$unique_id = uniqid(md5(rand()), true);
	 		$_SESSION['wgWikiaUniqueBrowserId'] = $unique_id;
	 	} else {
	 		$unique_id = $_SESSION['wgWikiaUniqueBrowserId'];
	 	}

	 	return $unique_id;
	 }
}

?>
