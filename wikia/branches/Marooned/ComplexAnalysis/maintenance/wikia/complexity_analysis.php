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
require_once(dirname(__FILE__) . '/../commandLine.inc');

if (isset($options['help'])) {
	die("Parse every article in current wiki.\n
		 Usage: php complexity_analysis.php

		 --help     you are reading it right now");
}

$wgPossibleElements = array(
	//meta data
	'city_id', 'article_id', 'rev_id',
	//wiki elements
	'b', 'i', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'image', 'image with link', 'table', 'category', 'category with sortkey', 'internal link', 'internal link: media', 'internal link: special page', 'internal link: file', 'external link', 'self link', 'list: unordered', 'list: ordered', 'list: definition: description', 'list: definition: term', 'magic: variable', 'magic: function', 'special: template with parameters', 'special: template without parameters', 'extra tags: core', 'extra tags: parser hook', 'extra tags: transparent parser hook',
	//html elements
	'html: abbr', 'html: acronym', 'html: b', 'html: big', 'html: blockquote', 'html: br', 'html: caption', 'html: center', 'html: cite', 'html: code', 'html: dd', 'html: del', 'html: div', 'html: dl', 'html: dt', 'html: em', 'html: font', 'html: h1', 'html: h2', 'html: h3', 'html: h4', 'html: h5', 'html: h6', 'html: hr', 'html: i', 'html: ins', 'html: li', 'html: ol', 'html: p', 'html: pre', 'html: q', 'html: rb', 'html: rp', 'html: rt', 'html: ruby', 'html: s', 'html: small', 'html: span', 'html: strike', 'html: strong', 'html: sub', 'html: sup', 'html: table', 'html: td', 'html: th', 'html: tr', 'html: tt', 'html: u', 'html: ul', 'html: var'
);

$dataTable = wfSharedTable('complex_data');
if (isset($options['csv'])) {
	if (!empty($wgComplexDataFile)) {
		$fp = fopen($wgComplexDataFile, 'w');
		fputcsv($fp, $wgPossibleElements);

		$db = wfGetDB(DB_SLAVE);
		$sql = "SELECT city_id, article_id, rev_id, data FROM $dataTable;";
		$res = $db->query($sql);
		while ($row = $db->fetchObject($res)) {
			$wgElementsCount = unserialize($row->data);
			fputcsv($fp, array_merge(array_fill_keys($wgPossibleElements, 0), $wgElementsCount, array('city_id' => $row->city_id, 'article_id' => $row->article_id, 'rev_id' => $row->rev_id)));
		}

		fclose($fp);
	} else {
		echo 'Please set the variable $wgComplexDataFile so it points to the output file.';
	}
	exit;
}
echo "Parsing articles started: CityID = $wgCityId, DB name = $wgDBname\n";
$time_start = microtime(true);

$db = wfGetDB(DB_SLAVE);

$lastRevision = '';
$sql = "SELECT max(rev_id) AS rev_id FROM $dataTable WHERE city_id = $wgCityId;";
$res = $db->query($sql);
$row = $db->fetchObject($res);
if (!empty($row->rev_id)) {
        $lastRevision = "AND rev_id > {$row->rev_id}";
}

$nameSpaces = 'AND page_namespace IN (' . implode(',', $wgContentNamespaces) . ')';
$sql = "SELECT page_id, rev_id FROM page, revision WHERE rev_id = page_latest $nameSpaces $lastRevision ORDER BY rev_id;";
$res = $db->query($sql);
$countAll = 0;
while ($row = $db->fetchObject($res)) {
	$wgTitle = Title::newFromID($row->page_id);	//setting global wgTitle - some parser functions require this
	if (is_object($wgTitle)) {
		$revision = Revision::newFromTitle($wgTitle);
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