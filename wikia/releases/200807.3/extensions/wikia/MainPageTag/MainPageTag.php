<?php
/**
 * @author Christian Williams
 * This extension provides parser tags to properly render the main column layout for mainpages. One column requires two div elements, so two div elements have been added to all.
*/
if( !defined( 'MEDIAWIKI' ) ) {
	die( 1 );
}

$wgExtensionFunctions[] = 'wfMainPageTag';
$MainPageTagState = 0;

function wfMainPageTag() {
	global $wgParser;

	$wgParser->setHook( 'mainpage-rightcolumn-start', 'wfMainPageTag_rcs' );
	$wgParser->setHook( 'mainpage-leftcolumn-start', 'wfMainPageTag_lcs' );
	$wgParser->setHook( 'mainpage-endcolumn', 'wfMainPageTag_ec' );
}

function wfMainPageTag_rcs( $input, $args, $parser ) {
	global $MainPageTagState;
	$html = '<div style="position: relative; width: 300px; float: right; clear: right;"><div>';
	$MainPageTagState = 1;
	return $html;
}

function wfMainPageTag_lcs( $input, $args, $parser ) {
	global $MainPageTagState;
	if(!isset($args['gutter'])) {
		$args['gutter'] = '10';
	}
	$args['gutter'] = str_replace('px', '', $args['gutter']);
	if($MainPageTagState === 1) {
		$html = '<div style="overflow: hidden; height: 1%; padding-right: '. $args['gutter'] .'px"><div>';
		$MainPageTagState = 0;
	} else {
		$gutter = 300 + $args['gutter'];
		$html = '<div style="float: left; margin-right: -'. $gutter .'px; width: 100%; position: relative;"><div style="margin-right: '. $gutter .'px;">';
	}
	return $html;
}

function wfMainPageTag_ec( $input, $args, $parser ) {
	$html = '</div></div>';
	return $html;
}