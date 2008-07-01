<?php

$wgAjaxExportList [] = 'wfGetVimeoAPIJSON';
function wfGetVimeoAPIJSON($api_key, $format, $jsoncallback, $method, $query) {
	
	$super_secret = "db0d89200";
	
	$api_sig_raw = $super_secret . "api_key" . $api_key . "format" . $format . "jsoncallback" . $jsoncallback . "method" . $method . "query" . $query;
	$api_sig = md5($api_sig_raw);
	
	$q_string = "?api_key=" . $api_key . "&format=" . $format . "&jsoncallback=" . $jsoncallback . "&method=" . $method . "&query=" . urlencode($query) . "&api_sig=" . $api_sig;
	
	$base_url="http://www.vimeo.com/api/rest";
	
	$handle = fopen($base_url . $q_string, "r");
	$contents = stream_get_contents($handle);
	fclose($handle);
	
	return $contents;

}


?>
