<?php
/**
 * InterwikiDispatcher - see ticket #2954
 *
 * @author Maciej B³aszkowski (Marooned) <marooned@wikia.com>
 * @date 2008-07-08
 * @copyright Copyright (C) 2008 Maciej B³aszkowski, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @package MediaWiki
 * @subpackage SpecialPage
 *
 * To activate this functionality, place this file in your extensions/
 * subdirectory, and add the following line to LocalSettings.php:
 *     require_once("$IP/extensions/wikia/InterwikiDispatcher/SpecialInterwikiDispatcher.php");
 */

if (!defined('MEDIAWIKI')) {
	echo "This is MediaWiki extension named InterwikiDispatcher.\n";
	exit(1) ;
}

class InterwikiDispatcher extends SpecialPage {
	/**
	 * contructor
	 */
	function  __construct() {
		parent::__construct('InterwikiDispatcher' /*class*/);
	}

	function execute($subpage) {
		global $wgOut, $wgRequest, $wgNotAValidWikia;

		wfLoadExtensionMessages('SpecialInterwikiDispatcher');

		$redirect = $wgNotAValidWikia;
		$url = $wgRequest->getText('wikia');
		$art = $wgRequest->getText('article');

		if (!empty($url)) {
			$DBr = wfGetDB(DB_SLAVE);
			$dbResult = $DBr->Query (
				  'SELECT city_id'
				. ' FROM ' . wfSharedTable('city_domains')
				. ' WHERE city_domain = ' . $DBr->AddQuotes("$url.wikia.com")
				. ' LIMIT 1'
				. ';'
				, __METHOD__
			);

			if ($row = $DBr->FetchObject($dbResult)) {	//wiki exists
				$redirect = "http://$url.wikia.com/";
				if (empty($art)) {	//no article set - redir to the main page
					exec ("'echo Title::newMainPage();' | SERVER_ID={$row->city_id} /opt/wikia/php/bin/php /usr/wikia/source/wiki/maintenance/eval.php --conf /usr/wikia/docroot/wiki.factory/LocalSettings.php", $output);
					if (count($output)) {
						$redirect .= 'index.php?title=' . $output[0];
					}
				} else {	//article provided
					$redirect .= 'index.php?title=' . $art;
				}
			}
			$DBr->FreeResult($dbResult);
		}
//		$wgOut->SetPageTitle(wfMsg('interwikidispatcher'));
		$wgOut->redirect($redirect, 301);
	}
}
?>