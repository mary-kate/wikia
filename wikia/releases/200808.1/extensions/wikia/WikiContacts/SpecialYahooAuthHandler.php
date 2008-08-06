<?php
class YahooAuthHandler extends SpecialPage {
	
	function YahooAuthHandler(){
		UnlistedSpecialPage::UnlistedSpecialPage("YahooAuthHandler");
	}




	function execute(){
		
		global $IP, $wgOut, $wgYahooAPISecret;
		
		$wgOut->setArticleBodyOnly(true);

		$appid = $_GET["appid"];  // my application ID, obtained at registration
		$token = $_GET["token"];
		$ts = time();
		
		$sig = md5( "/WSLogin/V1/wspwtoken_login?appid=$appid&token=$token&ts=$ts" . "$wgYahooAPISecret" );
		$url = "https://api.login.yahoo.com/WSLogin/V1/wspwtoken_login?appid=$appid&token=$token&ts=$ts&sig=$sig";
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$xml = curl_exec( $ch );
		curl_close( $ch );
		
		if (  preg_match( "/(Y=.*)/", $xml, $match_array ) == 1 ) {
			$Y_COOKIE = $match_array[1];
		}
		if (  preg_match( "/<WSSID>(.+)<\/WSSID>/", $xml, $match_array ) == 1 ) {
			$WSSID = $match_array[1];
		}
		if (  preg_match( "/<Timeout>(.+)<\/Timeout>/", $xml, $match_array ) == 1 ) {
			$timeout = $match_array[1];
		}
	
	    
		if ($WSSID) {
		    $COOKIETTL = time() + (10 * 365 * 24 * 60 * 60);
		    setcookie("yahoo_wssid", $WSSID, $COOKIETTL, "/");
		    setcookie("yahoo_cookie", $Y_COOKIE, $COOKIETTL, "/");
		
		    // Prepare the message to display the consent token contents.
		    $message_html = "<script>
		    window.location='http://re.search.wikia.com/JSON/yform.html'
		    //window.opener.get_ms_contacts()
		    //window.close();
		    </script>";
		 
		}else{
			$message_html = "Authentication failed";
		}
		
		$wgOut->addHTML( $message_html );

	}
}


?>
