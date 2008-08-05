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

/*
table structure

CREATE TABLE `complex_data` (
  `city_id` int(9) NOT NULL,
  `article_id` int(10) NOT NULL,
  `rev_id` int(10) NOT NULL,
  `data` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`city_id`,`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
*/
require_once('../commandLine.inc');

if (isset($options['help'])) {
	die("Parse every article in current wiki.\n
		 Usage: php complexity_analysis.php

		 --help     you are reading it right now");
}

$time_start = microtime(true);

$db = wfGetDB(DB_SLAVE);

$lastRevision = '';
$sql = 'SELECT max(rev_id) AS rev_id FROM ' . wfSharedTable('complex_data') . ';';
$res = $db->query($sql);
$row = $db->fetchObject($res);
if (!empty($row->rev_id)) {
        $lastRevision = "AND rev_id > {$row->rev_id}";
}

$nameSpaces = 'AND page_namespace IN (' . implode(',', $wgContentNamespaces) . ')';
$sql = "SELECT rev_id, page_id, rev_id FROM page, revision WHERE rev_id = page_latest $nameSpaces $lastRevision ORDER BY rev_id;";
$res = $db->query($sql);
$countAll = 0;
while ($row = $db->fetchObject($res)) {
	$title = Title::newFromID($row->page_id);
	if (is_object($title)) {
		$revision = Revision::newFromTitle($title);
		if(is_object($revision)) {
			wfCountWikiElement("*special:start:{$wgCityId}|{$row->page_id}|{$row->rev_id}");
			$wgOut->parse($revision->getText());
			wfCountWikiElement("*special:stop:{$wgCityId}|{$row->page_id}|{$row->rev_id}");
			$countAll++;
		}
	}
}
$time = microtime(true) - $time_start;
echo "Parsed $countAll articles. Execution time: $time seconds\n";
?>