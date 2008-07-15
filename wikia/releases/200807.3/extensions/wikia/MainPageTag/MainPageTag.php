<?php
if(!defined('MEDIAWIKI')) {
	die(1);
}

$wgExtensionFunctions[] = 'wfMainPageTag';

function wfMainPageTag() {
	global $wgParser;

	$wgParser->setHook('mainpage-rightcolumn-start', 'wfMainPageTag_rcs' );
	$wgParser->setHook('mainpage-leftcolumn-start', 'wfMainPageTag_lcs' );
	$wgParser->setHook('mainpage-endcolumn', 'wfMainPageTag_ec');
}

function wfMainPageTag_rcs($input, $args, $parser) {
	return ('<div style="position: relative; width: 300px; float: right; clear: right;">');
}

function wfMainPageTag_lcs($input, $args, $parser) {
	if (!isset($args['gutter'])) { $args['gutter'] = '10px'; }
	return ('<div style="overflow: hidden; height: 1%; padding-right: '. $args['gutter'] .'">');
}

function wfMainPageTag_ec($input, $args, $parser) {
	return ('</div>');
}
