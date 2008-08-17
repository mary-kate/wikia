<?php

/**
 * CovertOps
 *
 * Lets privlidged users edit wikis without leaving a visible trace
 * in RecentChanges and logs. Used for contests.
 *
 * @author Łukasz Garczewski (TOR) <tor@wikia.com>
 * @date 2008-08-18
 * @copyright Copyright (C) 2008 Łukasz Garczewski, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @package MediaWiki
 * @subpackage SpecialPage
 */

if (!defined('MEDIAWIKI')) {
	echo "This is MediaWiki extension named SiteWideMessages.\n";
	exit(1) ;
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'SiteWideMessages',
	'author' => '[http://www.wikia.com/wiki/User:Marooned Maciej Błaszkowski (Marooned)]',
	'description' => 'This extension provides an interface for sending messages seen on all wikis.'
);
//Allow group STAFF to use this extension.
$wgAvailableRights[] = 'covertops';
$wgGroupPermissions['*']['covertops'] = false;
$wgGroupPermissions['staff']['covertops'] = true;

$wgExtensionFunctions[] = 'CovertOpsInit';
$wgExtensionMessagesFiles['CovertOps'] = dirname(__FILE__) . '/SpecialCovertOps.i18n.php';

//Register special page
if (!function_exists('extAddSpecialPage')) {
	require("$IP/extensions/ExtensionFunctions.php");
}
extAddSpecialPage(dirname(__FILE__) . '/SpecialCovertOps_body.php', 'CovertOps', 'CovertOps');
/**
 * Initialize hooks
 *
 */
function CovertOpsInit() {

	/* ... */
	
}


/**
 * Load JS/CSS for extension
 *
 */
function SiteWideMessagesIncludeJSCSS( $skin, & $bottomScripts) {
	global $wgExtensionsPath, $wgStyleVersion;

	$bottomScripts .= "<script type=\"text/javascript\" src=\"$wgExtensionsPath/wikia/SiteWideMessages/SpecialSiteWideMessages.js?$wgStyleVersion\"></script>";

	return true;
}


/**
 * Return a content of all user's messages and add CSS styles
 *
 */
function SiteWideMessagesGetUserMessagesContent($dismissLink = true, $parse = true, $useForDiff = false, $addJSandCSS = true) {
	global $wgExtensionsPath, $wgStyleVersion, $wgOut, $wgUser, $wgRequest;
	if ($wgRequest->getText('diff') == '' || $useForDiff) {
		wfLoadExtensionMessages('SpecialSiteWideMessages');

		if ($addJSandCSS) {
			global $wgHooks;
			$wgHooks['SkinAfterBottomScripts'][] = 'SiteWideMessagesIncludeJSCSS';
			$wgOut->AddScript("<link rel=\"stylesheet\" type=\"text/css\" href=\"$wgExtensionsPath/wikia/SiteWideMessages/SpecialSiteWideMessages.css?$wgStyleVersion\" />");
		}

		$content = SiteWideMessages::getAllUserMessages($wgUser, $dismissLink);
		return $parse ? $wgOut->Parse($content) : $content;
	} else {
		return '';
	}
}
