<?php
/**
 * Allow programs to request this object from WebRequest::response()
 * and handle all outputting (or lack of outputting) via it.
 */
class WebResponse {

	/** Output a HTTP header */
	function header($string, $replace=true) {
		if( headers_sent( $filename, $linenum ) ) {
			error_log( $_SERVER['HTTP_HOST'] . '-' . $_SERVER['REQUEST_URI']." - Headers already sent in $filename on line $linenum. Cant send another header - $string" );
		}
		header($string,$replace);
	}

	/** Set the browser cookie */
	function setcookie($name, $value, $expire) {
		global $wgCookiePath, $wgCookieDomain, $wgCookieSecure;
		setcookie($name,$value,$expire, $wgCookiePath, $wgCookieDomain, $wgCookieSecure);
	}
}
