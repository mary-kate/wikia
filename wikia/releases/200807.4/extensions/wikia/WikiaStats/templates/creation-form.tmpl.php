<!-- s:<?= __FILE__ ?> -->
<div class="stats-subtitle"><?= wfMsg('wikiastats_creation_wikia_text') ?></div>
<div class="wk-select-class-clear">
<?=wfMsg('wikiastats_creation_legend')?>
0 ≤ <font color="#ee8636">Xxx</font> < 5 ≤ <font color="#BDB76B">Xxx</font> < 50 ≤ <font color="#32CD32">Xxx</font> < 500 ≤ <font color=\"#77bbff\">Xxx</font>
</div>
<br />
<div id="ws-main-table">
<!-- CREATION's STATISTICS -->
<div class="medium" style="float:left; width:auto;">
<div class="wk-info-clear"><?= wfMsg('wikiastats_mainstats_short_column_A'); ?></div>
<div class="wk-select-class-clear"><?= wfMsg('wikiastats_mainstats_column_A'); ?></div>
<!-- CREATION WIKIANS -->
<?
if (!empty($dWikians) && is_array($dWikians))
{
?>
<div class="ws-div-history" style="background-color:#ffffdd;width:<?= $max_wikians * 100 ?>px;">
<!--<table style="width:auto" class="ws-trend-table">-->
<?
$loop = 0;
foreach ($dWikians as $id => $date)
{
	$dateArr = explode("-", $date);
	#---
	$stamp = mktime(0,0,0,$dateArr[1],1,$dateArr[0]);
	$outDate = substr(wfMsg(strtolower(date("F",$stamp))), 0, 3) . " ".$dateArr[0];
?>
<tr>
	<div class="div-eb-trend"><?=$outDate?></div>
	
<?
	$url = "";
	//$width = 100 * count($wikians[$date]);
	if ( !empty($wikians) && !empty($wikians[$date]) )
	{
		foreach ($wikians[$date] as $id => $wikiaInfo)
		{
			$out = $wikiaInfo['average'];
			$dbname = (!empty($wikiaInfo['city_id']) && array_key_exists($wikiaInfo['city_id'], $cityList)) ? $cityList[$wikiaInfo['city_id']]['dbname'] : "";
			
			#---
			$color = "#77bbff";
			if (($out >= 0) && ($out < 5)) $color = "#ee8636";
			elseif (($out >= 5) && ($out < 50)) $color = "#BDB76B";
			elseif (($out >= 50) && ($out < 500)) $color = "#32CD32";
			
			$url .= "<span class=\"ws-spam-box\" style=\"background-color:$color;\">{<a href=\"/index.php?title=Special:WikiaStats&action=citystats&city=".$wikiaInfo['city_id']."\"><strong>".$dbname."</strong></a>} " . $wikiaInfo['cnt'] . "</span>";
		}
		$loop++;
	}
?>
<div class="div-eb-trend-trend" ><?=$url?></div>
<div class="div-eb-trend-clear"></div>
<?				
}
?>
</div>	
<?
}
?>
<div class="wk-clear-hr">&nbsp;</div>
<div class="wk-info-clear"><?= wfMsg('wikiastats_mainstats_short_column_E'); ?></div>
<div class="wk-select-class-clear"><?= wfMsg('wikiastats_mainstats_column_E'); ?></div>
<!-- CREATION WIKIANS -->
<?
if (!empty($dArticles) && is_array($dArticles))
{
?>
<div class="ws-div-history" style="background-color:#ffffdd;width:<?= $max_articles * 100 ?>px;">
<table style="width:auto" class="ws-trend-table">
<?
foreach ($dArticles as $id => $date)
{
	$dateArr = explode("-", $date);
	#---
	$stamp = mktime(0,0,0,$dateArr[1],1,$dateArr[0]);
	$outDate = substr(wfMsg(strtolower(date("F",$stamp))), 0, 3) . " ".$dateArr[0];
?>
<tr>
	<td class="eb-trend" nowrap><?=$outDate?></td>
	<td class="eb-trend-trend" nowrap>
<?
	if ( !empty($article) && !empty($article[$date]) )
	{
		foreach ($article[$date] as $id => $wikiaInfo)
		{
			$out = $wikiaInfo['average'];
			$dbname = (!empty($wikiaInfo['city_id']) && array_key_exists($wikiaInfo['city_id'], $cityList)) ? $cityList[$wikiaInfo['city_id']]['dbname'] : "";
			
			$color = "#77bbff";
			if (($out >= 0) && ($out < 5)) $color = "#ee8636";
			elseif (($out >= 5) && ($out < 50)) $color = "#BDB76B";
			elseif (($out >= 50) && ($out < 500)) $color = "#32CD32";
			#---				
			$url = "<spam class=\"ws-spam-box\" style=\"background-color:$color;\">{<a href=\"/index.php?title=Special:WikiaStats&action=citystats&city=".$wikiaInfo['city_id']."\"><strong>".$dbname."</strong></a>} " . $wikiaInfo['cnt'] . "</spam>";
?>
		<?=$url?>
<?				
		}
	}
?>		
	&nbsp;</td>
</tr>
<?		
}
?>
</table>
</div>	
<?
}
?>
<br />
</div>
</div>
<!-- e:<?= __FILE__ ?> -->
