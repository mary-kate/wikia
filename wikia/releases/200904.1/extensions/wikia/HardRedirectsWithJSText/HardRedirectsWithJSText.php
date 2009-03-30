<?php
/**
 * This extension enables Hard Redirects (301), and implements the
 * "Redirected From" text with JavaScript. This has SEO benefits,
 * there is only one page for each article  (minimize duplicate content)
 *
 * 301 redirects alone are less than ideal, because the user loses the
 * "Redirected From" text, which is both disorienting and it prevents users
 * from editing a redirect once it is created.
 *  
 * Note this requires a patch to the core to add an extra Hook call. Add this
 * line on about line 267 of Wiki.php, immediately before the "if( is_object( $target ) ) {" line:
 
	wfRunHooks('BeforeRedirect', array( &$wgTitle, &$target ) );

  Note: I'm working on getting this into the core code. It may be there
  by the time you read this.

 * Requires $wgEnableHardRedirectsWithJSText to be set to true
 *
 * @file
 * @ingroup Extensions
 * @version 1.0
 * @author Nick Sullivan <nick at wikia-inc.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 *
 * Code Review Notes:
 * Confirm that cacheability is the same as it was before.
 * Test "Moved" pages
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo 'To install this extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/wikia/HardRedirectsWithJSText/HardRedirectsWithJSText.php" );';
	exit( 1 );
}

$wgExtensionCredits['other'][] = array(
	'name' => 'HardRedirectsWithJSText',
	'version' => '1.0',
	'author' => 'Nick Sullivan',
	'description' => 'Enables Hard Redirects (301) and implements the "Redirected From" text with JavaScript. The benefit is for SEO, there is only one page for each article (minimize duplicate content)'
);

// Only support for Monaco skin at the moment
$wgHooks['ArticleViewHeader'][] = 'jsRedirectedFromDiv';
$wgHooks['BeforeRedirect'][] = 'hardRedirectWithCookie'; // Note this hook does not exist in core. You must add it to Wiki.php (see header)
$wgHooks['BeforePageDisplay'][] = 'jsRedirectedFromText';

/**
 * With hard redirects enabled, we always always print the redirectMsg div, and
 * then use JavaScript to check for a cookie to display it or not. Note that we
 * always display it so that the MD5 of the page is equal for Google's duplicate
 * content check
 */
function jsRedirectedFromDiv( $article, $outputDone, $pcache ){
	global $wgUser, $wgOut, $wgEnableHardRedirectsWithJSText;

	// If the extension isn't enabled in site configuration, bail out
	if( empty($wgEnableHardRedirectsWithJSText) ){
		return true;
	}

	// Only Monaco skin is supported at the moment
	if( get_class( $wgUser->getSkin() ) != 'SkinMonaco' ) {
		return true;
	}

	// Set up the subtitle "Redirected From"
	$wgOut->setSubtitle( '<div id="redirectMsg" class="redirectMsg" style="display:none"></div>' );
	return true;
}

// Fill in the text in the div created above. In a separate hook so the javascript is at the bottom of the page.
function jsRedirectedFromText( $out ){
	global $wgUser, $wgEnableHardRedirectsWithJSText, $wgCookiePrefix, $wgTitle;
	global $wgCookiePath, $wgCookieDomain, $wgCookieSecure;

	// If the extension isn't enabled in site configuration, bail out
	if( empty($wgEnableHardRedirectsWithJSText) ){
		return true;
	}

	// Only Monaco skin is supported at the moment
	if( get_class( $wgUser->getSkin() ) != 'SkinMonaco' ) {
		return true;
	}

	// the RedirectedFrom suffix set as MD5 of target on previous page
	$out->addInlineScript('
		var jsrdCookie="' . addslashes( $wgCookiePrefix ) . 'RedirectedFrom_' . md5( $wgTitle->getText() ) . '";
		var jsrdText="' . addslashes( wfMsg( 'redirectedfrom' ) ) . '";
		var jsrdVal=YAHOO.util.Cookie.get(jsrdCookie);

		if( jsrdVal != null ){
			var rdVals=jsrdVal.split("|"); // RedirectFrom cookie has $url|$linktext
			var rdLink="<a href=\"" + rdVals[0] + "?redirect=no\">" + rdVals[1].replace(/\+/g, " ") + "</a></span>";
			YAHOO.util.Dom.get("redirectMsg").innerHTML=jsrdText.replace(/\$1/, rdLink);
			YAHOO.util.Dom.setStyle("redirectMsg", "display", "");
			YAHOO.util.Cookie.remove(jsrdCookie, {
				domain: "' . $wgCookieDomain . '",
				path: "' . $wgCookiePath . '",
				secure: "' . $wgCookieSecure . '"
				}
			);
		}');

	return true;
}

/**
 * Hard redirect them to the new url with 301.
 * Append the query string if there is one.
 * Set a cookie for the "Redirected From_MD%(urloftarget)" text.
 */
function hardRedirectWithCookie( $source, $target ){
	global $wgEnableHardRedirectsWithJSText,$wgEnableHardRedirectsWithJSTextLimit, $wgUser;

	// Only Monaco skin is supported at the moment
	if( get_class( $wgUser->getSkin() ) != 'SkinMonaco' ) {
		return true;
	}

	// Set the number of sequential redirects if not already set in site configuration
	if( empty( $wgEnableHardRedirectsWithJSTextLimit ) ){
		$wgEnableHardRedirectsWithJSTextLimit = 3;
	}

	$doredirect	= true;

	// Only take action if the extension has been enabled in site configuration, again
	if( $wgEnableHardRedirectsWithJSText ){
		global $wgOut, $wgCookiePrefix, $wgCookiePath, $wgCookieDomain, $wgCookieSecure, $wgRequest;
			if( $wgRequest->getVal( 'redirect' ) != 'no' ){

				if ( ( $source !== false ) && ( $source instanceof Title ) && ( $target !== false ) && ( $target instanceof Title ) ) {
						// Only set the cookie if they are not on a 'redirect=no' page.
					   	$targetUrlMd = md5( $target->getFullURL() );
						$sourceUrlMd = md5( $source->getFullURL() );

						// Add namespaces except for main
						$linktext = $source->getText();
						if( ( $source->getNamespace() != NS_MAIN ) && ( stripos( $linktext, $source->getNsText() . ':' ) === false ) ){
							$linktext = str_replace( '_', ' ', $source->getNsText() ) . ':' . $linktext;							
						}

						setcookie( $wgCookiePrefix . 'RedirectedFrom_' . md5( $target->getText() ),
								$source->getLocalUrl() . '|' . $linktext,
								time() + 30, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );
				}

				// Get cookie if one exists
				$redirectsequence = array();

				if( !empty( $_COOKIE[$wgCookiePrefix.'RedirectedTrail'] ) ){
					$redirectsequence = explode( '|', $_COOKIE[$wgCookiePrefix.'RedirectedTrail'] );
				}

				if( ( $target !== false ) && ( $target instanceof Title ) ) {
					setcookie( $wgCookiePrefix.'RedirectedTrail',
								implode('|', $redirectsequence) . '|' . $targetUrlMd,
								time() + 30, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );
				}

				// check if current md5 exists delimited with | or we went through redirect limit
				if( in_array( $targetUrlMd, $redirectsequence ) || ( count( $redirectsequence ) > $wgEnableHardRedirectsWithJSTextLimit ) ){
					$doredirect	= false;
				}

				// check if it is redirect to itself
				if( $targetUrlMd == $sourceUrlMd ){
					$doredirect	= false;
				}

				if( ( $target !== false ) && ( $target instanceof Title ) && ( $doredirect ) ) {
					$wgOut->redirect( $target->getFullURL(), '301' );
				} else {
					//no need to redirect
					//drop the cookie for trail and reset the count.. user need to refresh the page 
					setcookie( $wgCookiePrefix.'RedirectedTrail',
							'', time() - 30, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );
				}
			}
		}
	return true;
}
