<?php
/**
 * Run wgOut->parse() for every article in current wiki. Used for ticket #3090: Conduct analysis of wikitext used on our article pages.
 *
 * @package MediaWiki
 * @subpackage Maintanance
 *
 * @author: Maciej Błaszkowski (Marooned) <marooned@wikia.com>
 *
 * @copyright Copyright (C) 2008 Maciej Błaszkowski (Marooned), Wikia, Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 *
 */

require_once('../commandLine.inc');

if (isset($options['help'])) {
	die("Parse every article in current wiki.\n
		 Usage: php complexity_analysis.php

		 --help     you are reading it right now");
}

$db = wfGetDB(DB_SLAVE);
//$db->selectDB($wgSharedDB);

$sql = 'SELECT page_id, page_title FROM page WHERE page_namespace = 0;';
$res = $db->query($sql);
$countAll = 0;
wfElementCount("*special:start:{$wgCityId}|{$row->page_id}|{$row->page_title}");
while ($row = $db->fetchObject($res)) {
	$title = Title::newFromText($row->page_title);
	if (is_object($title)) {
		$revision = Revision::newFromTitle($title);
		if(is_object($revision)) {
			$wgOut->parse($revision->getText());
			$countAll++;
		}
	}
}
wfElementCount("*special:stop:{$wgCityId}|{$row->page_id}|{$row->page_title}");

//if (isset($options['verbose'])) print($sql. "\n");
//if (!isset($options['dryrun'])) $db->query($sql);

echo "Parsed $countAll articles.\n";
?>