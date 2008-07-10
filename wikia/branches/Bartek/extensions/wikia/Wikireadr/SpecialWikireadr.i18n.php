<?php

/**
 * Internationalisation file for CountEdits extension
 *
 * @addtogroup Extensions
 * @author Bertrand GRONDIN <bertrand.grondinr@tiscali.fr>
 */

function efSpecialWikireadrMessages() {
	$messages = array(
// english add your own
	'en' => array(
	'wikireadr' => 'Wikireadr [BETA]',
	'ws.ok' => ' Ok ',
	'ws.cancel' => 'Cancel',
	'ws.step1.h.1' => 'Starting Wikipedia page URL',
	'ws.step1.i.1' => 'This tool will read articles from Wikipedia.com -- but without installed templates and external links.<br /> You can specify number of related pages to retrieve (maximum depth is one level below the main article).<br /> Copy and paste entire url of a Wikipedia page as it appears in your browser\'s address line!',
	'ws.step2.i.1' => 'The following pages have been found. <br /> Be sure to review the page names. If your wiki has a page with the same name, on import it WILL BE OVERWRITTEN!',
	'ws.step2.i.2' => ' related pages can be imported.',
	'ws.status.h' => 'Import',
	'ws.status.badurl' => 'No pages to import here! :( We only support valid Wikipedia URLs',
	'ws.status.success' => 'Done!',
	'ws.status.include' => 'Include',
	'ws.status.failure' => 'Failed. Try Later',
	'ws.overwrite.confirm' => 'Some of the pages already exist on target wiki. Is it okay to overwrite them?',
	'ws.status.incomplete' => 'Incomplete Import',
	'ws.status.timeout' => 'Timeout. Try Later',
	'ws.status.preview' => 'Preview',
	'ws.pagename.h' => 'Page Name',
	'ws.link.h' => '(link)',
	'ws.existingpage.h' => 'Existing Page',
	'ws.startover.h' => ' Start Over ',
	'ws.action.h' => 'Status',
	'ws.status.includeall' => 'Include All',
	'ws.status.excludeall' => 'Exclude All',
	'ws.status.remred' => 'remove all dead link code from imported pages',
	'ws.status.import' => 'Import'

	)
);

$messages['ar'] = $messages['en'];
$messages['bcl'] = $messages['en'];
$messages['bn'] = $messages['en'];
$messages['br'] = $messages['en'];
$messages['ca'] = $messages['en'];
$messages['de'] = $messages['en'];
$messages['ext'] = $messages['en'];
$messages['fr'] = $messages['en'];
$messages['hsb'] = $messages['en'];
$messages['id'] = $messages['en'];
$messages['it'] = $messages['en'];
$messages['la'] = $messages['en'];
$messages['nl'] = $messages['en'];
$messages['no'] = $messages['en'];
$messages['oc'] = $messages['en'];
$messages['pms'] = $messages['en'];
$messages['pt'] = $messages['en'];
$messages['sk'] = $messages['en'];
$messages['sr'] = $messages['en'];
$messages['sr-ec'] = $messages['en'];
$messages['sr-el'] = $messages['en'];
$messages['zh-hans'] = $messages['en'];
$messages['yue'] = $messages['en'];
$messages['zh-hans'] = $messages['en'];
$messages['zh-hant'] = $messages['en'];
$messages['zh'] = $messages['en'];
$messages['zh-cn'] = $messages['en'];
$messages['zh-hk'] = $messages['en'];
$messages['zh-cn'] = $messages['en'];
$messages['zh-tw'] = $messages['en'];
$messages['zh-yue'] = $messages['en'];

	return $messages;
}
