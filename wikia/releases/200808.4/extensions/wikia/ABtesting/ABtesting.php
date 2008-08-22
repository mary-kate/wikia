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

	if(!empty($ABtests[$name]['variant'])) {
		return $ABtests[$name]['variant'];
	}

	$limit = array_sum($ABtests[$name]['variants']);
	$number = rand(1, $limit);

	//echo "The number is: $number<br/>";

	$j = 0;
	for($i = 0; $i < count($ABtests[$name]['variants']); $i++) {
		if($number <= $ABtests[$name]['variants'][$i] + $j) {
			//echo "The variant is: $i<br/><br/>";
			return;
		}
		$j += $ABtests[$name]['variants'][$i];
	}

	//echo '<br/>';
}