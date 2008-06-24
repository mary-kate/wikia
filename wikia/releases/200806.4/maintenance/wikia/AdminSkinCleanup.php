<?php
require_once( '../commandLine.inc' );

echo "================================================================================\n";
echo "AdminSkin cleanup for {$wgCityId} / {$wgDBname} / {$wgServer}\n";

$articleTitle = Title::newFromText ("AdminSkin", NS_MEDIAWIKI);
$article = new Article ($articleTitle);
if($article->exists()) {
	$article->doDeleteArticle("This article is not used anymore");
	echo "MediaWiki:AdmiSkin deleted\n";
}
