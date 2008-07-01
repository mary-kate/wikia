<!-- s:<?= __FILE__ ?> -->
<!-- CHART TABLE -->
<!-- MAIN STATISTICS TABLE -->
<div class="ws-chart-table">
<?
	$G = 1000 * 1000 * 1000;
	$M = 1000 * 1000;
	$K = 1000;	
	$S = 100;	
	$T = 10;
	$GB = 1024 * 1024 * 1024;
	$MB = 1024 * 1024;
	$KB = 1024;	
	if (!empty($city_id))
	{
?>
	<input type="hidden" id="wk-stats-city-id" value="<?=$cityId?>">
<?	
	}
	if (!empty($mainTitle))
	{
?>
	<div class="panel-top"><?= str_replace("<br />", "", $mainTitle) ?></div><br />
<?
	}
	$tableStyle = "padding:15px 10px 15px 3px;width:auto;font-family:verdana,arial;font-size:10px;color:".$chartSettings['legend-font-color'].";background-color:".$chartSettings['chart-bg-color'];
?>
<table cellspacing="0" cellpadding="0" border="0" style="<?=$tableStyle?>">
<tr><td align='center' style="padding:0px 5px 20px 5px;" colspan="<?= (count($data)+1) ?>"><?= wfMsg("wikiastats_mainstats_column_$column") ?></td></tr>
<tr>
<?
	$chartData = array();
	$iMax = 1; $sum = 0;
	$useByte = 0;
	foreach ($data as $date => $out)
	{
		if ( in_array($column, array('J', 'K')) ) //percent
			$_tmp = $out * 100;
		else
			$_tmp = $out;		
		#---			
		if ($_tmp > $iMax) $iMax = $_tmp;
		#---
		$sum += $_tmp;
		#---
		$dateArr = explode("-",$date);
		$stamp = mktime(0,0,0,$dateArr[1],1,$dateArr[0]);
		$new_date = substr(wfMsg(strtolower(date("F",$stamp))), 0, 3) . " " . $dateArr[0];
		#---
		if ($column == 'H') $value = sprintf("%0.1f", $out);
		elseif ($column == 'I') $value = sprintf("%0.0f", $out);
		elseif (($column == 'J') || ($column == 'K')) $value = sprintf("%0d%%", $out * 100);
		elseif ($column == 'M') 
		{
			$useByte = 1;
			if (intval($out) > $GB) $value = sprintf("%0.1f GB", intval($out)/$GB);
			elseif (intval($out) > $MB) $value = sprintf("%0.1f MB", intval($out)/$MB);
			elseif (intval($out) > $KB) $value = sprintf("%0.1f KB", intval($out)/$KB);
			else $value = sprintf("%0.0f", intval($out));
		}
		else
		{
			if (intval($out) > $G) $value = sprintf("%0.1f G", intval($out)/$G);
			elseif (intval($out) > $M) $value = sprintf("%0.1f M", intval($out)/$M);
			elseif (intval($out) > $K) $value = sprintf("%0.1f k", intval($out)/$K);
			else $value = sprintf("%0d", intval($out));
		}
		#---
		$chartData[$date] = array("date" => $new_date, "value" => (!empty($out))?$value:"&nbsp;");
	}

	$height = $chartSettings['maxsize'];
	$ratio = $height/10; if ($iMax > 10) $ratio = $height/$iMax;
	
	$td_height = $height . $chartSettings['barunit'];
	$td_width = $chartSettings['barwidth'] . $chartSettings['barunit'];
	#---
?>
<td valign="bottom" align="right" style="height:<?=$td_height?>; width:<?=$td_width?>;border-right:1px solid <?=$chartSettings['chart-line-color']?>">&nbsp;</td>
<?
#---
	if (empty($sum)) $sum = 1; $i = 0;
	foreach ($data as $date => $out)
	{
		$color = $chartSettings['colors'][$i%2];
		if ( in_array($column, array('J', 'K')) ) //percent
			$_tmp = $out * 100;
		else
			$_tmp = $out;		

		$div_height = intval($ratio * $_tmp);
		$pad = $chartSettings['padding-bar'];
?>
	<td valign="bottom" align="center" style="padding:0px <?=$pad?>px 0px <?=$pad?>px;font-size:<?= $chartSettings['fontsize'] ?>;">
		<div style="font-family:verdana,arial;font-weight:normal;color:<?=$chartSettings['text-color']?>"><?= $chartData[$date]['value'] ?></div>
		<div style="height:<?=$div_height?>px;width:<?=$td_width?>;background-color:<?=$color?>;">&nbsp;</div>
	</td>
<?	
		$i++;
	}
?>
</tr>
<tr style="background-color:<?=$chartSettings['chart-bg-color']?>">
<td></td>
<!-- MONTHS -->
<?
	$dateYear = array();
	$prev_year = 0;
	foreach ($chartData as $date => $values)
	{
		$dateArr = explode(" ",$values['date']);
		if (!array_key_exists($dateArr[1], $dateYear)) {
		    $dateYear[$dateArr[1]] = 0;
        }
		$dateYear[$dateArr[1]]++;
		$addStyle = "";
		if ($prev_year != $dateArr[1])
		{
			$addStyle = "border-left:1px dotted".$chartSettings['chart-line-color'].";";
			$prev_year = $dateArr[1];
		}
		$tableStyle = $addStyle;
		$tableStyle .= "border-top:1px solid ".$chartSettings['chart-line-color'].";";
		$tableStyle .= "font-family:verdana,arial;";
		$tableStyle .= "font-size:".$chartSettings['legendsize'].";";
		$tableStyle .= "color:".$chartSettings['legend-font-color'].";";
?>	
	<td valign='bottom' align='center' style="<?=$tableStyle?>"><?= $dateArr[0] ?></td>
<?
	}
?>	
</tr>
<!-- YEARS -->
<tr style="background-color:<?=$chartSettings['chart-bg-color']?>">
<td></td>
<?
	foreach ($dateYear as $year => $cnt)
	{
		$tableStyle = "border-left:1px dotted ".$chartSettings['chart-line-color']."; border-top:1px dotted ".$chartSettings['chart-line-color'].";";
		$tableStyle .= "font-family:verdana,arial;";
		$tableStyle .= "font-size:".$chartSettings['legendsize'].";";
		$tableStyle .= "color:".$chartSettings['legend-font-color'].";";
?>
	<td align='center' colspan="<?=$cnt?>" style="<?=$tableStyle?>"><?= $year ?></td>
<?
	}
?>
</tr>
</table>
</div>
<br /><br />
<!-- END OF PAGE EDITED DETAILS TABLE -->
<!-- e:<?= __FILE__ ?> -->
