<?php
/**
 * Main file for the Wikiwyg extension that loads all other stuff
 *
 * @ingroup Extensions
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 );
}

require_once("$IP/extensions/wikiwyg/share/MediaWiki/extensions/MediaWikiWyg.php");
require_once("$IP/extensions/wikiwyg/share/MediaWiki/extensions/WikiwygEditing/WikiwygEditing.php");
require_once("$IP/extensions/wikia/CreatePage/SpecialCreatePage.php");
require_once("$IP/extensions/wikia/CreatePage/CreatePageCore.php");

$dir = dirname(__FILE__);
$wgExtensionMessagesFiles['Wikiwyg'] = $dir . '/Wikiwyg.i18n.php';
