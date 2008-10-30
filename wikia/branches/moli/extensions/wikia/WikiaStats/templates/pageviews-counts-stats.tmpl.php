<!-- s:<?= __FILE__ ?> -->
<!-- PAGEVISITS COUNTS TABLE -->
<div id="ws-page-views-table-stats">
<br />
<?php
if (!empty($statsCount))
{
  $Kb = 1000;
  $Mb = $Kb * $Kb;
  $Gb = $Kb * $Kb * $Kb;
  $aNamespaces = $statsCount['namespaces'];
  ksort($aNamespaces, SORT_NUMERIC);
  
  $rows = array();
  if (!empty($statsCount['months'])) {
  	foreach ($statsCount['months'] as $date => $values) {
  		$row = "";
		$dateArr = explode("-",$date);
		error_log ("date: $date \n", 3, "/tmp/moli.log");
		$is_month = 0;
		if (!isset($dateArr[2])) {
			$is_month = 1;						
		}
		$stamp = mktime(23,59,59,$dateArr[1],($is_month)?1:$dateArr[2],$dateArr[0]);
		$out = $wgLang->sprintfDate(($is_month)?"M Y":WikiaGenericStats::getStatsDateFormat(1), wfTimestamp(TS_MW, $stamp));
		if ($is_month) {
			$row .= "<tr bgcolor=\"#ffdead\"><td colspan=\"".(count($aNamespaces)+2)."\" style=\"line-height:0.1em;\">&nbsp;</td></tr>";
		}
  		$row .= "<tr><td class=\"cb\" style=\"white-space:nowrap;background-color:#ffdead\">".$out."</td>";
  		$all = 0;
  		foreach ($aNamespaces as $id => $value) {
  			$_tmp = (isset($values[$id])) ? $values[$id] : 0;
  			$row .= "<td class=\"eb\" style=\"font-size:7pt;\">".WikiaGenericStats::getNumberFormat($_tmp)."</td>";
  			$all += $_tmp;
		}
		$row .= "<td class=\"cb\" style=\"white-space:nowrap;background-color:#ffdead\">".WikiaGenericStats::getNumberFormat($all)."</td>";
		$row .= "</tr>";
  		$rows[] = $row;
	} 
  }
?>	
<div style="float:left; padding-bottom: 5px; width:100%; max-width:100%; max-height:600px;overflow-y:auto;">
<table cellspacing="0" cellpadding="0" border="1" id="table_page_edited_stats" style="width:auto; font-family: arial,sans-serif,helvetica; font-size:9pt;background-color:#ffffdd;">
<tr bgcolor="#ffdead">
	<td class="cb" rowspan="2"><?=wfMsg('wikiastats_date')?></td>
	<td class="cb" colspan="<?=count($aNamespaces) + 1?>"><?=wfMsg('wikiastats_namespace')?></td>
</tr>	
<tr bgcolor="#ffeecc">
<? foreach ($aNamespaces as $id => $value) { ?>	
	<td class="cb" style="font-size:7pt;"><?= ($id == 0) ? $wgLang->ucfirst(wfMsg('wikiastats_main_namespace')) : str_replace("_","<br />",$canonicalNamespace[$id])?></td>
<? } ?>
	<td class="cb" style="font-size:7pt;">#</td>
</tr>
<?= implode("", $rows)?>
<tr bgcolor="#ffdead">
	<td class="cb" rowspan="3"><?=wfMsg('wikiastats_date')?></td>
<? $all = 0; foreach ($aNamespaces as $id => $value) { 
	$all += intval($value); 
?>	
	<td class="cb"><?= WikiaGenericStats::getNumberFormat($value) ?></td>
<? } ?>
	<td class="cb"><?= WikiaGenericStats::getNumberFormat($all)?></td>
</tr>
<tr bgcolor="#ffeecc">
<? foreach ($aNamespaces as $id => $value) { ?>	
	<td class="cb" style="font-size:7pt;"><?= ($id == 0) ? $wgLang->ucfirst(wfMsg('wikiastats_main_namespace')) : str_replace("_","<br />",$canonicalNamespace[$id])?></td>
<? } ?>
	<td class="cb" style="font-size:7pt;">#</td>
</tr>
<tr bgcolor="#ffdead">
	<td class="cb" colspan="<?=count($aNamespaces) + 1?>"><?=wfMsg('wikiastats_namespace')?></td>
</tr>	
</table>
</div>
<?
}
?>
</div>
<!-- END OF PAGEVISITS COUNT TABLE -->
<!-- e:<?= __FILE__ ?> -->
