<?php
global $wgStyleVersion, $wgExtensionsPath, $wgTitle;


if ($wgTitle->getNamespace() == NS_USER || $wgTitle->getNamespace() == NS_USER_TALK) {
	global $wgTitle;
	$userMastheadName = $wgTitle;
}
if ($wgTitle == 'Special:Watchlist') {
	global $wgUser;
	$userMastheadName = $wgUser->getName();
}

?>

<div id="user_masthead" class="reset clearfix">
	<?php echo $avatar->getLinkTag( 50, 50 ) ?>
	<h2><?=$data['userspace']?></h2>
	<?
	if(!empty($nav_urls['blockip'])) {
		echo '<a href="'. $nav_urls['blockip']['href'] .'">'. wfMsg('blockip') .'</a>';
	}
	?>
	<ul>
		<?
		foreach( $data['nav_links'] as $navLink ) {
			echo "<li ". ( ( $current  == $navLink[ "dbkey" ]) ? 'class="selected">' : ">" ) . '<a href="'. $navLink['href'] .'">'. $navLink['text'] .'</a></li>';
		}
		?>
	</ul>
</div>
