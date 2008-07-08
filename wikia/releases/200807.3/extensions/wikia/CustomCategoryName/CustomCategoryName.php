<?php
/**
 * @package MediaWiki
 * @subpackage CustomCategoryName
 *
 * @author Maciej Błaszkowski <marooned at wikia.com> [code by Inez]
 */

if(!defined('MEDIAWIKI')) {
	exit( 1 );
}

$wgHooks['SkinTemplateOutputPageBeforeExec'][] = 'CustomCategoryNameHook';

function CustomCategoryNameHook($skintpl, $tpl) {
	global $wgRequest;
	if ($skintpl->mTitle->mNamespace == NS_CATEGORY && $wgRequest->getVal('action', 'view') == 'view') {
		global $wgSitename;
		$tpl->set('title', $skintpl->mTitle->getNsText() . ': ' . $wgSitename . ' ' . $skintpl->mTitle->getText());
	}
	return true;
}