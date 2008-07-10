<!-- s:<?= __FILE__ ?> -->
<?php
if (!empty($cityInfo))
{
    $outDate = "";
	$created = $cityInfo->city_created;
	if (!empty($created))
	{
		$dateTime = explode(" ", $created);
		#---
		$dateArr = explode("-", $dateTime[0]);
		#---
		$stamp = mktime(0,0,0,$dateArr[1],$dateArr[2],$dateArr[0]);
		$outDate = substr(wfMsg(strtolower(date("F",$stamp))), 0, 3) . " " . $dateArr[2] .", ". $dateArr[0]. " ".$dateTime[1];
	}
    
    #--- dbdumps ---
    $full_url = "http://wikistats.wikia.com/dbdumps/".$cityInfo->city_dbname."/pages_full.xml.gz";
    $current_url = "http://wikistats.wikia.com/dbdumps/".$cityInfo->city_dbname."/pages_current.xml.gz";

    $full_dump_time = WikiaGenericStats::getFileMTimeRemove($full_url);
    $outFullDumpTime = substr(wfMsg(strtolower(date("F",$full_dump_time))), 0, 3) . " " . date("d", $full_dump_time) .", ". date("Y", $full_dump_time). " ".date("H:i:s", $full_dump_time);
    $current_dump_time = WikiaGenericStats::getFileMTimeRemove($current_url);
    $outCurrentDumpTime = substr(wfMsg(strtolower(date("F",$current_dump_time))), 0, 3) . " " . date("d", $current_dump_time) .", ". date("Y", $current_dump_time). " ".date("H:i:s", $current_dump_time);

    $full_dump_size = WikiaGenericStats::getUrlFilesize($full_url);
    $current_dump_size = WikiaGenericStats::getUrlFilesize($current_url);

?>
<!-- WIKI's INFORMATION -->
<table cellspacing="0" cellpadding="2" border="0" style="width:auto; font-family: arial,sans-serif,helvetica;">
<tr>
<td class="cityinfo" nowrap align="left">
	<strong><?= wfMsg('wikiastats_wikiname') ?></strong> <?= ucfirst($cityInfo->city_title) ?> (id: <?= $cityInfo->city_id ?>)
</td>
<td class="cityinfo" nowrap align="center">
	<strong><?= wfMsg('wikiastats_wikiurl') ?></strong> <a target="new" href="<?= $cityInfo->city_url ?>"><?= $cityInfo->city_url ?></a>
</td>
<td class="cityinfo" align="center" nowrap>
	<strong><?= wfMsg('wikiastats_wikicreated') ?></strong> <?= $outDate ?>
</td>
</tr>
<tr>
<td colspan="3" width="100%" class="cityinfo" align="left">
	<strong><?= wfMsg('wikiastats_dbdumps_stats') ?>:</strong> 
	<?=wfMsg('wikiastats_full_dump_stats')?>: <font style="color:gray; font-size: small; clear: none;"><?=wfMsg('wikiastats_size').": ".$full_dump_size?>, <?= wfMsg('wikiastats_dbdump_generated') ?><?=$outFullDumpTime?> </font>, 
	<?=wfMsg('wikiastats_current_dump_stats')?>: <font style="color:gray; font-size: small; clear: none;"><?=wfMsg('wikiastats_size').": ".$current_dump_size?>, <?= wfMsg('wikiastats_dbdump_generated') ?><?=$outCurrentDumpTime?> </font>
</td>
</tr>
<tr>
<td colspan="3" width="100%" class="cityinfo" align="left">
	<strong><?= wfMsg('wikiastats_see_MW_stats') ?></strong> <a href="http://wikistats.wikia.com/EN/TablesWikia<?=strtoupper($cityInfo->city_dbname)?>.htm" target="new">http://wikistats.wikia.com/EN/TablesWikia<?=strtoupper($cityInfo->city_dbname)?>.htm</a> 
</td>
</tr>	
</table>
<!-- END OF WIKI's INFORMATION -->
<?php
}
?>
<table cellspacing="1" cellpadding="0" border="0" width="500">
<tr><td id="ws-hide-table" class="panel" width="100"></td></tr>
</table>
<!-- MAIN STATISTICS TABLE -->
<input type="hidden" id="wk-stats-city-id" value="<?=$cityId?>">
<div id="ws-main-table-stats">
<table cellspacing="0" cellpadding="0" border="1" id="table_stats" style="width:auto; font-family: arial,sans-serif,helvetica; font-size:9pt;background-color:#ffffdd;">
<tr bgcolor="#ffdead">
	<td class="cb"><b><?= wfMsg('wikiastats_date') ?></b></td>
	<td colspan="4" class="cb">
		<div class="hide"><a href="javascript:void(0);" alt="<?= wfMsg('wikiastats_hide') ?>" title="<?= wfMsg('wikiastats_hide') ?>" onClick="javascript:visible_column(1,4,0,'<?= wfMsg('wikiastats_wikians') ?>');">X</a></div>
		<b><?= wfMsg('wikiastats_wikians') ?></b>
	</td>
	<td colspan="7" class="cb">
		<div class="hide"><a href="javascript:void(0);" alt="<?= wfMsg('wikiastats_hide') ?>" title="<?= wfMsg('wikiastats_hide') ?>" onClick="javascript:visible_column(5,11,0,'<?= wfMsg('wikiastats_articles') ?>');">X</a></div>
		<b><?= wfMsg('wikiastats_articles') ?></b>
	</td>
	<td colspan="3" class="cb">
		<div class="hide"><a href="javascript:void(0);" alt="<?= wfMsg('wikiastats_hide') ?>" title="<?= wfMsg('wikiastats_hide') ?>" onClick="javascript:visible_column(12,14,0,'<?= wfMsg('wikiastats_database') ?>');">X</a></div>
		<b><?= wfMsg('wikiastats_database') ?></b>
	</td>
	<td colspan="5" class="cb">
		<div class="hide"><a href="javascript:void(0);" alt="<?= wfMsg('wikiastats_hide') ?>" title="<?= wfMsg('wikiastats_hide') ?>" onClick="javascript:visible_column(15,19,0,'<?= wfMsg('wikiastats_links') ?>');">X</a></div>
		<b><?= wfMsg('wikiastats_links') ?></b>
	</td>
	<td colspan="2" class="cb">
		<div class="hide"><a href="javascript:void(0);" alt="<?= wfMsg('wikiastats_hide') ?>" title="<?= wfMsg('wikiastats_hide') ?>" onClick="javascript:visible_column(20,21,0,'<?= wfMsg('wikiastats_daily_usage') ?>');">X</a></div>
		<b><?= wfMsg('wikiastats_daily_usage') ?></b>
	</td>
</tr>
<tr bgcolor="#ffeecc">
	<td class="cb" rowspan="2">&nbsp;</td>
	<td valign="top" rowspan="2" class="cb">total</td>
	<td valign="top" rowspan="2" class="cb">new</td>
	<td colspan="2" class="cb">edits</td>
	<td colspan="2" class="cb">count</td>
	<td valign="top" rowspan="2" class="cb">new<br/>per day</td>
	<td colspan="2" class="cb">mean</td>
	<td colspan="2" class="cb">larger than</td>
	<td valign="top" rowspan="2" class="cb">edits</td>
	<td valign="top" rowspan="2" class="cb">size</td>
	<td valign="top" rowspan="2" class="cb">words</td>
	<td valign="top" rowspan="2" class="cb">internal</td>
	<td valign="top" rowspan="2" class="cb">interwiki</td>
	<td valign="top" rowspan="2" class="cb">image</td>
	<td valign="top" rowspan="2" class="cb">external</td>
	<td valign="top" rowspan="2" class="cb">redirects</td>
	<td valign="top" rowspan="2" class="cb">page<br/>requests</td>
	<td valign="top" rowspan="2" class="cb">visits</td>
</tr>
<tr bgcolor="#ffeecc">
	<td class="cb">&gt;5</td>
	<td class="cb">&gt;100</td>
	<td class="cb">official</td>
	<td class="cb">&gt;200 ch</td>
	<td class="cb">edits</td>
	<td class="cb">bytes</td>
	<td class="cb" nowrap>0.5 Kb</td>
	<td class="cb" nowrap>2 Kb</td>
</tr>
<?php
foreach ($monthlyStats as $date => $columnsData)
{
	#---
	if ($columnsData['visible'] === 1)
	{
		$dateArr = explode("-", $date);
		$stamp = mktime(0,0,0,$dateArr[1],1,$dateArr[0]);
		$outDate = substr(wfMsg(strtolower(date("F",$stamp))), 0, 3) . " " . $dateArr[0];

?>
<tr>
	<td class="db" nowrap><?= $outDate ?></td>
<?
		foreach ($columns as $column)
		{
			if ( in_array($column, array('date')) ) continue;
			#---
			$out = $columnsData[$column];
			$class = "rb";
			if ( in_array($column, array('B','H','I','J','K')) )
			{
				$out = "&nbsp;";
			}
			elseif (empty($columnsData[$column]) || ($columnsData[$column] == 0))
			{
				$out = "&nbsp;";
			}
			else
			{
				if ($columnsData[$column] < 0)
				{
					$out = "<font color=\"#800000\">".sprintf("%0.0f%%", $columnsData[$column])."</font>";
				}
				elseif (($columnsData[$column] > 0) && ($columnsData[$column] < 25))
				{
					$out = "<font color=\"#000000\">".sprintf("+%0.0f%%", $columnsData[$column])."</font>";
				}
				elseif (($columnsData[$column] > 25) && ($columnsData[$column] < 75))
				{
					$out = "<font color=\"#008000\">".sprintf("+%0.0f%%", $columnsData[$column])."</font>";
				}
				elseif (($columnsData[$column] > 75) && ($columnsData[$column] < 100))
				{
					$out = "<font color=\"#008000\"><u>".sprintf("+%0.0f%%", $columnsData[$column])."</u></font>";
				}
				elseif ($columnsData[$column] >= 100)
				{
					$out = "&nbsp;";
				}
			}
?>
	<td class="rb"><?= $out ?></td>
<?php			
		}
?>
</tr>
<?php
	}
}
?>
</tr>
<tr bgcolor="#ffeecc">
<?php 
foreach ($columns as $column)
{
	if ($column == "date") $column = "&nbsp;";
?>
	<td class="cb" title="<?= wfMsg("wikiastats_mainstats_column_".$column) ?>"><?= $column ?></td>
<?	
}
?>
<?php
foreach ($statsData as $date => $columnsData)
{
?>
<tr>
<?php 
	$G = 1000 * 1000 * 1000;
	$M = 1000 * 1000;
	$K = 1000;	
	$GB = 1024 * 1024 * 1024;
	$MB = 1024 * 1024;
	$KB = 1024;	
	foreach ($columns as $column)
	{
		$out = $columnsData[$column];
		$class = "rb";
		if (empty($columnsData[$column]) || ($columnsData[$column] == 0))
		{
			$out = "&nbsp;";
		}
		else
		{
			if ($column == 'date')
			{
				$class = "db";
				$dateArr = explode("-",$columnsData[$column]);
				$stamp = mktime(0,0,0,$dateArr[1],1,$dateArr[0]);
				$out = substr(wfMsg(strtolower(date("F",$stamp))), 0, 3) . " " . $dateArr[0];
				if ($columnsData[$column] == $today)
				{
				    $stamp = (!empty($today_day)) ? $today_day : $stamp;
					$out = substr(wfMsg(strtolower(date("F",$stamp))), 0, 3) . " " . date("d", $stamp) . ", " . date("Y", $stamp);
				}
			}
			elseif ($column == 'A')
				$out = sprintf("%0d", $columnsData[$column]);
			elseif ($column == 'H')
				$out = sprintf("%0.1f", $columnsData[$column]);
			elseif ($column == 'I')
				$out = sprintf("%0.0f", $columnsData[$column]);
			elseif (($column == 'J') || ($column == 'K'))
				$out = sprintf("%0d%%", $columnsData[$column] * 100);
			elseif ($column == 'M')
			{
				if (intval($columnsData[$column]) > $GB)
					$out = sprintf("%0.1f GB", intval($columnsData[$column])/$GB);
				elseif (intval($columnsData[$column]) > $MB)
					$out = sprintf("%0.1f MB", intval($columnsData[$column])/$MB);
				elseif ($columnsData[$column] > $KB)
					$out = sprintf("%0.1f KB", intval($columnsData[$column])/$KB);
				else
					$out = sprintf("%0d", intval($columnsData[$column]));
			}
			else
			{
				if (intval($columnsData[$column]) > $G)
					$out = sprintf("%0.1f G", intval($columnsData[$column])/$G);
				elseif (intval($columnsData[$column]) > $M)
					$out = sprintf("%0.1f M", intval($columnsData[$column])/$M);
				elseif ($columnsData[$column] > $K)
					$out = sprintf("%0.1f k", intval($columnsData[$column])/$K);
				else
					$out = sprintf("%0d", intval($columnsData[$column]));
			}
		}
		
?>
	<td class="<?= $class ?>" nowrap><?= $out ?></td>
<?	
	}
	
	if ($date == $today)
	{
?>
</tr><tr>
<? 
		foreach ($columns as $column) 
		{ 
?>
	<td bgcolor="#ffeecc" class="cb_small">&nbsp;</td>
<? 
		} 
	} 
?>	
</tr>
<?
}
?>
</tr>
<tr bgcolor="#ffdead">
	<td class="cb"><b><?= wfMsg('wikiastats_date') ?></b></td>
	<td colspan="4" class="cb"><b><?= wfMsg('wikiastats_wikians') ?></b></td>
	<td colspan="7" class="cb"><b><?= wfMsg('wikiastats_articles') ?></b></td>
	<td colspan="3" class="cb"><b><?= wfMsg('wikiastats_database') ?></b></td>
	<td colspan="5" class="cb"><b><?= wfMsg('wikiastats_links') ?></b></td>
	<td colspan="2" class="cb"><b><?= wfMsg('wikiastats_daily_usage') ?></b></td>
</tr>
<tr bgcolor="#ffeecc">
	<td class="cb" rowspan="2">&nbsp;</td>
	<td valign="top" rowspan="2" class="cb">total</td>
	<td valign="top" rowspan="2" class="cb">new</td>
	<td colspan="2" class="cb">edits</td>
	<td colspan="2" class="cb">count</td>
	<td valign="top" rowspan="2" class="cb">new<br/>per day</td>
	<td colspan="2" class="cb">mean</td>
	<td colspan="2" class="cb">larger than</td>
	<td valign="top" rowspan="2" class="cb">edits</td>
	<td valign="top" rowspan="2" class="cb">size</td>
	<td valign="top" rowspan="2" class="cb">words</td>
	<td valign="top" rowspan="2" class="cb">internal</td>
	<td valign="top" rowspan="2" class="cb">interwiki</td>
	<td valign="top" rowspan="2" class="cb">image</td>
	<td valign="top" rowspan="2" class="cb">external</td>
	<td valign="top" rowspan="2" class="cb">redirects</td>
	<td valign="top" rowspan="2" class="cb">page<br/>requests</td>
	<td valign="top" rowspan="2" class="cb">visits</td>
</tr>
<tr bgcolor="#ffeecc">
	<td class="cb">&gt;5</td>
	<td class="cb">&gt;100</td>
	<td class="cb">official</td>
	<td class="cb">&gt;200 ch</td>
	<td class="cb">edits</td>
	<td class="cb">bytes</td>
	<td class="cb" nowrap>0.5 Kb</td>
	<td class="cb" nowrap>2 Kb</td>
</tr>
</table>
</div>
<!-- END OF MAIN STATISTICS TABLE -->
<!-- MAIN STATISTICS NOTES -->
<div id="wk-stats-legend">
<?= wfMsg('wikiastats_note_mainstats') ?><br />
<span id="wk-stats-legend-values"><font color="#800000"><?= wfMsg('wikiastats_history_mainstats_value1'); ?></font></span>
<span id="wk-stats-legend-values"><font color="#000000"><?= wfMsg('wikiastats_history_mainstats_value2'); ?></font></span>
<span id="wk-stats-legend-values"><font color="#008000"><?= wfMsg('wikiastats_history_mainstats_value3'); ?></font></span>
<span id="wk-stats-legend-values"><font color="#008000"><u><?= wfMsg('wikiastats_history_mainstats_value4'); ?></u></font></span>
<br />
<div id="wk-stats-legend-columns">
<?php 
$i = 0;
foreach ($columns as $column)
{
	if ($column == "date") continue;
	if ($i == 0) {
?>		
<span id="wk-column-group"><?= wfMsg("wikiastats_wikians") ?></span><br />
<?		
	} elseif ($i == 4) {
?>		
<span id="wk-column-group"><?= wfMsg("wikiastats_articles") ?></span><br />
<?		
	} elseif ($i == 11) {
?>		
<span id="wk-column-group"><?= wfMsg("wikiastats_database") ?></span><br />
<?		
	} elseif ($i == 14) {
?>		
<span id="wk-column-group"><?= wfMsg("wikiastats_links") ?></span><br />
<?	
	} elseif ($i == 19) {
?>		
<span id="wk-column-group"><?= wfMsg("wikiastats_daily_usage") ?></span><br />
<?
	}
	$i++;
?>
<span id="wk-column-<?=$column?>"><?=$column?>: <?= wfMsg("wikiastats_mainstats_column_".$column) ?></span><br />
<?	
}
?>
</div>
</div>
<!-- END OF MAIN STATISTICS NOTES -->
<!-- e:<?= __FILE__ ?> -->
