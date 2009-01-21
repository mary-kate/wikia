<?
$wgExtensionCredits['other'][] = array(
        'name' => 'EditEnhancements',
        'version' => '1.0',
        'author' => array('[http://pl.wikia.com/wiki/User:Macbre Maciej Brencz]', 'Christian Williams')
);

$wgExtensionFunctions[] = 'wfEditEnhancementsInit';

function wfEditEnhancementsInit() {
	global $wgRequest, $wgUser;

	$action = $wgRequest->getVal('action', null);

	if ($action == 'edit' || $action == 'submit') {
		if(get_class($wgUser->getSkin()) == 'SkinMonaco') {
			require( dirname(__FILE__) . '/EditEnhancements.class.php' );
			$instance = new EditEnhancements($action);
		}
	}
}


