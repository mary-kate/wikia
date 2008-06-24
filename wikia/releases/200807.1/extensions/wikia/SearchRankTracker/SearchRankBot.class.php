<?php
/**
 * SearchRankBot - part of SearchRankTracker extension
 * 
 * (Based on code originaly written by Tomek Klim) 
 * 
 * !IMPORTANT! Please see SearchRankTracker.sql for db shema !IMPORTANT! 
 * 
 * @author Adrian 'ADi' Wieczorek <adi(at)wikia.com> 
 * 
 */
 
class SearchRankBot {

	private $mDebugMode = false;
	private $mCacheDir = false;
	private $mCacheExpire = 4; // in hours
	private $mMaxResultFetched = 1000;
	private $mUserAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322)';
	private $mCurlEngine = null;

	public function __construct($sCacheDir = '', $sProxyUrl = '', $bDebugMode = false) {
		$this->mDebugMode = $bDebugMode;
		$this->mCacheDir = $sCacheDir;
		
		if(class_exists('WikiCurl')) {
			$this->mCurlEngine = new WikiCurl();
			$this->mCurlEngine->setTimeout(90);
			$this->mCurlEngine->setCookies('cookies');
			$this->mCurlEngine->setAgent($this->mUserAgent);
			if(!empty($sProxyUrl)) {
				$this->mCurlEngine->setProxy($sProxyUrl);
			}
		
			if($this->mCacheDir) {
				$this->mCurlEngine->setCacheDir($this->mCacheDir);
				$this->mCurlEngine->setCachePeriod(3600 * $this->mCacheExpire);
				$this->mCurlEngine->buildCacheTree();
			}
		}
		else {
			print "SearchRankBot ERROR: WikiCurl extension required.\n";
			exit(1);
		}

	}
	
	public function __destruct() {
		unset($this->mCurlEngine);
	}
	
	public function setCacheExpire($iHours) {
		$this->mCacheExpire = $iHours;
	}
	
	public function setUsetAgent($sUserAgent) {
		$this->mUserAgent = $sUserAgent;
	}
	
	public function setMaxResultFetched($iValue) {
		$this->mMaxResultFetched = $iValue;
	}
	
	public function setProxy($sProxyUrl) {
		if($this->mCurlEngine != null) {
			$this->mCurlEngine->setProxy($sProxyUrl);			
		}
	}
	
	
	public function run($bVerbose = true) {
		global $wgSharedDB, $wgSearchRankTrackerConfig;
		$this->printDebug("Starting SearchRankBot ...", $bVerbose);
		
		$sCurrentTime = date('Y-m-d H:i:s');
		$iCurrentYear = date('Y', strtotime($sCurrentTime));
		$iCurrentMonth = date('m', strtotime($sCurrentTime));
		$iCurrentDay = date('d', strtotime($sCurrentTime));
		
		$dbr = wfGetDB(DB_SLAVE);
		$dbr->selectDB($wgSharedDB);

		$oResource = $dbr->query("SELECT * FROM rank_entry ORDER BY ren_city_id, ren_id");
		
		if($oResource) {
			while($oResultRow = $dbr->fetchObject($oResource)) {
				// get entry object
				$oSearchEntry = new SearchRankEntry(0, $oResultRow);
				$this->printDebug("-> Checking rank for URL: " . $oSearchEntry->getPageUrl() . ", Phrase: \"" . $oSearchEntry->getPhrase() . "\" (ren_id: " . $oSearchEntry->getId() . ")", $bVerbose);
				foreach($wgSearchRankTrackerConfig['searchEngines'] as $sEngineName => $aEngineConfig) {
					$oRankResults = $oSearchEntry->getRankResults($sEngineName, $iCurrentYear, $iCurrentMonth, $iCurrentDay);
					if(!$oRankResults) {
						$iRank = $this->getRank($sEngineName, $oSearchEntry->getPageUrl(), $oSearchEntry->getPhrase());
					 $iResultInsertId = $oSearchEntry->setRankResult($sEngineName, $sCurrentTime, $iRank);
					 if($iResultInsertId) {
					 	$this->printDebug("=> ($sEngineName) Rank result saved. (rre_id: $iResultInsertId)");
					 }
					 else {
					 	$this->printDebug("=> ($sEngineName) ERROR: Rank result save failed.");
					 }
					}
					else {
						$this->printDebug("   ($sEngineName) Rank result exists for current date.", $bVerbose);
					}
				}
			}
		}
		else {
			$this->printDebug("No serach entries were found.", $bVerbose);
		}
		
		$this->printDebug("Done.", $bVerbose);			
	}
	
	public function getRank($sSearchEngine, $sUrl, $sPhrase ) {
		$iRank = 0;
		switch($sSearchEngine) {
			case 'google':     
				$iRank = $this->rankGoogle($sUrl, $sPhrase);  
				break;
			case 'yahoo':      
				$iRank = $this->rankYahoo($sUrl, $sPhrase);  
				break;
			case 'MSN':      
				$iRank = $this->rankMsn($sUrl, $sPhrase);  
				break;
			case 'altavista':   
				$iRank = $this->rankAltavista($sUrl, $sPhrase);  
				break;
		}
		return $iRank;
	}
	
	private function rankGoogle($sUrl, $sPhrase) {
		$iRank = 0;
		$iCount = 0;
		$iOffset = 0;
		
		$this->mCurlEngine->setReferer('http://www.google.com/');

		while($iRank == 0) {
			$sResult = $this->mCurlEngine->get('http://www.google.com/search', 
				array( 
					'q'     => $sPhrase,
				 'num'   => '100',
				 'start' => $iOffset,
				 'hl'    => 'en',
				 'lr'    => '',
				 'sa'    => 'N' 
				));

			preg_match_all( '/<h2 class=r><a href="(.*?)" class=l/si', $sResult, $aLinks, PREG_SET_ORDER );
			
			if(count($aLinks)) {
				foreach($aLinks as $sLink) {
					$iCount++;
					// filter out trailing slashes
					$sLinkFiltered = ( substr( $sLink[1], -1, 1 ) == '/' ? substr( $sLink[1], 0, -1 ) : $sLink[1] );
	
					if(strcmp($sLinkFiltered, $sUrl) == 0) {
						$iRank = $iCount;
						$this->printDebug("=> (google) Phrase: \"$sPhrase\", URL: $sUrl - found at position: $iCount");
						break;
					}
				}				
			}
			else {
				// no links were found, end of results or invalid pattern.
				$this->printDebug("=> (google) No links were found (invalid pattern?) - offset: $iOffset");				
			}

   $iOffset += 100;
   if($iOffset >= $this->mMaxResultFetched) {  
				// we've ran out of Google results
				break;
			}
	 }
		return $iRank;		
	}

	private function rankYahoo($sUrl, $sPhrase ) {
		$iRank = 0;
		$iCount = 0;
		$iOffset = 1;

		$this->mCurlEngine->setReferer( 'http://www.yahoo.com/' );

		while($iRank == 0) {
			$sResult = $this->mCurlEngine->get('http://search.yahoo.com/search', 
				array( 
					'p'      => $sPhrase,
					'sm'     => 'Yahoo! Search',
					'fr'     => 'FP-tab-web-t',
					'b'      => $iOffset,
					'toggle' => '1',
					'cop'    => 'mss',
					'ei'     => 'UTF-8' 
				));
				
			// in Yahoo mode, we're not scanning real links, but the green, human-readable addresses
			$sResult = str_replace( '<wbr />', '', $sResult );
			$sResult = str_replace( '</b>' , '', $sResult );
			$sResult = str_replace( '<b>'  , '', $sResult );

			preg_match_all( '/<span class=url>(.*?)<\/span>/si', $sResult, $aLinks, PREG_SET_ORDER );
			
			if(count($aLinks)) {
				foreach ( $aLinks as $link ) {
					$iCount++;
					if(strcmp(("http://" . $link[1]), $sUrl) == 0) {
						$iRank = $iCount;
						$this->printDebug("=> (yahoo) Phrase: \"$sPhrase\", URL: $sUrl - found at position: $iCount" );
						break;
					}
				}				
			}
			else {
				// no links were found, end of results or invalid pattern.
				$this->printDebug("=> (yahoo) No links were found (invalid pattern?) - offset: $iOffset");
			}

			$iOffset += 10;
			if($iOffset >= $this->mMaxResultFetched) {  
				// we've ran out of Yahoo results
				break;  
			}
		}
		return $iRank;
	}

	private function rankMsn($sUrl, $sPhrase) {
		$iRank = 0;
		$iCount = 0;
		$iOffset = 1;
		
		$this->mCurlEngine->setReferer('http://www.msn.com/');

		while($iRank == 0) {
			$sResult = $this->mCurlEngine->get('http://search.msn.com/results.aspx', 
				array(
					'q'     => $sPhrase,
					'first' => $iOffset,
					'FORM'  => 'PERE' 
				));

			$sResult = str_replace( '<strong>', '', $sResult );
			$sResult = str_replace( '</strong>' , '', $sResult );

			preg_match_all( '/<li><cite>(.*?)<\/cite>/si', $sResult, $aLinks, PREG_SET_ORDER );
			
			if(count($aLinks)) {
				foreach($aLinks as $link) {
					$iCount++;
					// filter out trailing slashes
					$sLinkFiltered = ( substr( $link[1], -1, 1 ) == '/' ? substr( $link[1], 0, -1 ) : $link[1] );
					
					if(strcmp(('http://' . $sLinkFiltered), $sUrl) == 0) {
						$iRank = $iCount;
						$this->printDebug("=> (MSN) Phrase: \"$sPhrase\", URL: $sUrl - found at position: $iCount");
						break;
					}
				}
			}
			else {
				// no links were found, end of results or invalid pattern.
				$this->printDebug("=> (MSN) No links were found (invalid pattern?) - offset: $iOffset");
			}

			$iOffset += 10;
	  if($iOffset >= 820) { 
				// we've ran out of MSN results
	  	break;  
	  }
		}
		return $iRank;
	}

	private function rankAltavista($sUrl, $sPhrase) {
		$iRank = 0;
		$iCount = 0;
		$iOffset = 0;

		$this->mCurlEngine->setReferer('http://www.altavista.com/');

		while($iRank == 0) {
			$sResult = $this->mCurlEngine->get('http://www.altavista.com/web/results',
				array(
					'q'    => $sPhrase,
					'kgs'  => '0',
					'kls'  => '0',
					'stq'  => $iOffset,
					'itag' => 'ody' 
				));

			preg_match_all('/<span class=ngrn>(.*?) <\/span>/si', $sResult, $aLinks, PREG_SET_ORDER);

			if(count($aLinks)) {
				foreach($aLinks as $link) {
					$iCount++;
					if(strcmp(('http://' . $link[1]), $sUrl) == 0) {
						$iRank = $iCount;
						$this->printDebug("=> (altavista) Phrase: \"$sPhrase\", URL: $sUrl - found at position: $iCount");
						break;
					}
				}
			}
			else {
				// no links were found, end of results or invalid pattern.
				$this->printDebug("=> (altavista) No links were found (invalid pattern?) - offset: $iOffset");
			}
				
			$iOffset += 10;
			if($iOffset >= $this->mMaxResultFetched) {  
				// we've ran out of AltaVista results
				break;  
			}  
		}
		return $iRank;
	}

	private function printDebug($sMessage, $bForceDebugMode = false) {
		if($this->mDebugMode || $bForceDebugMode) {  
			print "[SearchRankBot] " . $sMessage . "\n";  
		}
	}
	
}
