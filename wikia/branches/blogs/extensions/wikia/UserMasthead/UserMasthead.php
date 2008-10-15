<?php
$wgHooks['MonacoBeforePageBar'][] = 'userMasthead';

function userMasthead() {
	global $wgTitle, $wgUser, $userMasthead;
	$namespace = $wgTitle->getNamespace();
	if ($namespace == NS_USER || $namespace == NS_USER_TALK || ($namespace == NS_SPECIAL && $wgTitle->getDBkey() == 'Watchlist') || ($namespace == NS_SPECIAL && $wgTitle->getDBkey() == 'EmailUser')) {
		$userMasthead = true; //hides article/talk tabs in Monaco.php
		$out = array();
		//DEFINE USERSPACE - THE USERNAME THAT BELONGS ON THE MASTHEAD
		if ($namespace == NS_USER || $namespace == NS_USER_TALK) {
			$userspace = $wgTitle->getDBkey();
		}
		if ($wgTitle == 'Special:Watchlist') {
			$userspace = $wgUser->getName();
		}
		$out['userspace'] = $userspace;

		$out['nav_links'] = array (
			array('text' => wfMsg('nstab-user'), 'href' => $wgTitle),
			array('text' => wfMsg('talkpagelinktext'), 'href' => 'http://www.framezero.com'),
			array('text' => wfMsg('Contributions'), 'href' => 'http://www.framezero.com'),
			array('text' => wfMsg('blog'), 'href' => 'http://www.framezero.com')
		);

		if ( $wgUser->isLoggedIn() && $wgUser->getName() == $userspace) {
			$out['nav_links'][] = array('text' => wfMsg('watchlist'), 'href' => 'Special:Watchlist/'. $wgUser->getName());
			$out['nav_links'][] = array('text' => wfMsg('tooltip-pt-preferences'), 'href' => 'http://www.framezero.com');
		} else {
			$out['nav_links'][] = array('text' => 'email user', 'href' => $wgTitle);
		}
		
		$tmpl = new EasyTemplate(dirname( __FILE__ ));
		$tmpl->set_vars( array(
			'data' => $out
		));
		echo $tmpl->execute('UserMasthead');
	}
	return true;
}

?>
