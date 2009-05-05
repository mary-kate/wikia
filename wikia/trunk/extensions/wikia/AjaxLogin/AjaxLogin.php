<?php
/*
 * Author: Inez Korczynski
 */

$wgExtensionCredits['other'][] = array(
	'name' => 'AjaxLogin',
	'description' => 'Dynamic box which allow users to login and remind password',
	'author' => 'Inez Korczyński'
);

$wgHooks['GetHTMLAfterBody'][] = 'GetAjaxLoginForm';

function GetAjaxLoginForm($skin) {
	global $wgTitle, $wgUser;

	// different approach for Lean Monaco
	if (get_class($skin) == 'AwesomeTemplate') {
		return true;
	}

	if ($wgUser->isAnon() && $wgTitle->getNamespace() != 8 && $wgTitle->getDBkey() != 'Userlogin') {
		$tmpl = new EasyTemplate(dirname( __FILE__ ));
		echo $tmpl->execute('AjaxLogin');
	}
	return true;
}

$wgAjaxExportList[] = 'GetAjaxLogin';
function GetAjaxLogin() {
	$tmpl = new EasyTemplate(dirname( __FILE__ ));
	return new AjaxResponse( $tmpl->execute('AwesomeAjaxLogin') );
}
