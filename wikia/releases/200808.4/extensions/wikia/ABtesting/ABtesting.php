<?php
if(!defined('MEDIAWIKI')) {
	exit(1);
}

$wgExtensionCredits['other'][] = array(
	'name' => 'A/B testing',
	'author' => 'Inez Korczynski'
);

$ABtests = array();

$ABtests['exampleTest'] = array();
$ABtests['exampleTest']['variants'] = array(1, 3, 6);

function getABtest($name) {
	global $ABtests;

	if(empty($ABtests[$name])) {
		return 0;
	}

	if(empty($ABtests[$name]['variant'])) {
		if(empty($_COOKIE['ab'.$name])) {
			global $wgCookiePath, $wgCookieDomain, $wgCookieSecure;
			$limit = array_sum($ABtests[$name]['variants']);
			$number = rand(1, $limit);

			$j = 0;
			for($i = 0; $i < count($ABtests[$name]['variants']); $i++) {
				if($number <= $ABtests[$name]['variants'][$i] + $j) {
					$ABtests[$name]['variant'] = $i;
					break;
				}
				$j += $ABtests[$name]['variants'][$i];
			}

			$exp = time()+60*60*24*60; // 60 days
			setcookie('ab'.$name, $ABtests[$name]['variant'], $exp, $wgCookiePath, $wgCookieDomain, $wgCookieSecure);
		} else {
			$ABtests[$name]['variant'] = (int) $_COOKIE['ab'.$name];
		}
	}
	return $ABtests[$name]['variant'];
}