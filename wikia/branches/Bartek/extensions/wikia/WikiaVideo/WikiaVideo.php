<?php
if(!defined('MEDIAWIKI')) {
	exit(1);
}

$wgHooks['ParserBeforeStrip'][] = 'WikiaVideoParserBeforeStrip';

function WikiaVideoParserBeforeStrip($parser, $text, $strip_state) {
	// TODO
}