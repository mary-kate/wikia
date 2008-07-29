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

$lastRevision = '';
$sql = 'SELECT max(rev_id) AS rev_id FROM ' . wfSharedTable('complex_data') . ';';
$res = $db->query($sql);
if ($row = $db->fetchObject($res)) {
	$lastRevision = "AND rev_id > {$row->rev_id}";
}

//$sql = 'SELECT page_id, page_title FROM page WHERE page_namespace = 0;';
$sql = "SELECT rev_id, page_id, rev_id, page_title FROM page, revision WHERE rev_id = page_latest $lastRevision ORDER BY rev_id";
$res = $db->query($sql);
$countAll = 0;
while ($row = $db->fetchObject($res)) {
	$title = Title::newFromText($row->page_title);
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

echo "Parsed $countAll articles.\n";
/*
mysql> select rev_id, page_id, page_title from page, revision where rev_id = page_latest and rev_id > 2291 order by rev_id limit 15;
+--------+---------+---------------------+
| rev_id | page_id | page_title          |
+--------+---------+---------------------+
|   2293 |      90 | Urthwyte.jpg        |
|   2298 |      95 | Orlando.jpg         |
|   2300 |      97 | Auma.jpg            |
|   2303 |     100 | Cregga.jpg          |
|   2305 |     102 | Russano.jpg         |
|   2308 |     105 | Fortunata2.jpg      |
|   2309 |     106 | Mw-cover-uk.gif     |
|   2312 |     109 | Mw-alt.jpg          |
|   2313 |     110 | Sm-softcover-us.gif |
|   2315 |     112 | Mr-softcover-us.jpg |
|   2316 |     113 | OORUK.jpg           |
|   2317 |     114 | Or-cover-us.gif     |
|   2324 |     121 | Samkim.jpg          |
|   2326 |     123 | Mattimeo.jpg        |
|   2327 |     124 | Ironbeak.jpg        |
+--------+---------+---------------------+
15 rows in set (0.73 sec)

 CREATE TABLE `wikia112`.`complex_data` (
`city_id` INT( 9 ) NOT NULL ,
`article_id` INT( 10 ) NOT NULL ,
`data` TEXT NOT NULL ,
PRIMARY KEY ( `city_id` , `article_id` )
) ENGINE = InnoDB 

*/
?>