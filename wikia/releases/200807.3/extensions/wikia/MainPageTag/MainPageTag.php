<?php
/**
 * @author Christian Williams
 * This extension provides parser tags to properly render the main column layout for mainpages. One column requires two div elements, so two div elements have been added to all.
*/
if( !defined( 'MEDIAWIKI' ) ) {
	die( 1 );
}

$wgExtensionFunctions[] = 'wfMainPageTag';
$colOrder = null;

function wfMainPageTag() {
	global $wgParser;

	$wgParser->setHook( 'mainpage-rightcolumn-start', 'wfMainPageTag_rcs' );
	$wgParser->setHook( 'mainpage-leftcolumn-start', 'wfMainPageTag_lcs' );
	$wgParser->setHook( 'mainpage-endcolumn', 'wfMainPageTag_ec' );
}

function wfMainPageTag_rcs( $input, $args, $parser ) {
	error_log("INEZS TEST: wfMainPageTag_rcs called");

	global $colOrder;
	$html = '<div style="position: relative; width: 300px; float: right; clear: right;"><div>';
	$colOrder = 'right';
	return $html;
}

function wfMainPageTag_lcs( $input, $args, $parser ) {
	error_log("INEZS TEST: wfMainPageTag_lcs called");

	global $colOrder;
	if( !isset( $args['gutter'] ) ) {
		$args['gutter'] = '10px';
	}
	if( $colOrder ) {
		error_log("INEZS TEST: colOrder set");

		$html = '<div style="overflow: hidden; height: 1%; padding-right: '. $args['gutter'] .'"><div>';
	} else {
		error_log("INEZS TEST: colOrder !set");

		$gutter = 300 + trim($args['gutter'], 'px');
		$html = '<div style="float: left; margin-right: -'. $gutter .'px; width: 100%; position: relative;"><div style="margin-right: '. $gutter .'px;">';
	}
	$colOrder = 'left';
	return $html;
}

function wfMainPageTag_ec( $input, $args, $parser ) {
	error_log("INEZS TEST: wfMainPageTag_ec called");

	$html = '</div></div>';
	return $html;
}
