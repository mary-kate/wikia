<?php
if (!empty($city_row))
{
    $outDate = "";
	$created = $city_row->city_created;
	if (!empty($created) && ($created != "0000-00-00 00:00:00"))
	{
		$dateTime = explode(" ", $created);
		#---
		$dateArr = explode("-", $dateTime[0]);
		#---
		$stamp = mktime(0,0,0,$dateArr[1],$dateArr[2],$dateArr[0]);
		$outDate = substr(wfMsg(strtolower(date("F",$stamp))), 0, 3) . " " . $dateArr[2] .", ". $dateArr[0]. " ".$dateTime[1];
	}
    
    #--- dbdumps ---
    $full_url = "http://wikistats.wikia.com/dbdumps/".$city_row->city_dbname."/pages_full.xml.gz";
    $current_url = "http://wikistats.wikia.com/dbdumps/".$city_row->city_dbname."/pages_current.xml.gz";

    $full_dump_time = WikiaGenericStats::getFileMTimeRemove($full_url);
    $outFullDumpTime = substr(wfMsg(strtolower(date("F",$full_dump_time))), 0, 3) . " " . date("d", $full_dump_time) .", ". date("Y", $full_dump_time). " ".date("H:i:s", $full_dump_time);
    $current_dump_time = WikiaGenericStats::getFileMTimeRemove($current_url);
    $outCurrentDumpTime = substr(wfMsg(strtolower(date("F",$current_dump_time))), 0, 3) . " " . date("d", $current_dump_time) .", ". date("Y", $current_dump_time). " ".date("H:i:s", $current_dump_time);

    $full_dump_size = WikiaGenericStats::getUrlFilesize($full_url);
    $current_dump_size = WikiaGenericStats::getUrlFilesize($current_url);

?>
<!-- s:<?= __FILE__ ?> -->
<!-- WIKI's INFORMATION -->	
<table cellspacing="0" cellpadding="2" border="0" style="width:auto; font-family: arial,sans-serif,helvetica;">
<tr>
<td class="cityinfo" nowrap align="left">
	<ul style="font-size:8.5pt">
	 <li><strong><?= wfMsg('wikiastats_wikiname') ?></strong> <?= ucfirst($city_row->city_title) ?> (id: <?= $city_row->city_id ?>)</li>
	 <li><strong><?= wfMsg('wikiastats_wikiurl') ?></strong> <a target="new" href="<?= $city_row->city_url ?>"><?= $city_row->city_url ?></a></li>
<? if (!empty($outDate)) { ?>
	 <li><strong><?= wfMsg('wikiastats_wikicreated') ?></strong> <?= $outDate ?></li>
<? } ?>
	 <li><strong><?= wfMsg('wikiastats_dbdumps_stats') ?>:</strong>
		<ul>
		<li><strong><?=wfMsg('wikiastats_full_dump_stats')?>:</strong> <font style="color:gray;"><?=wfMsg('wikiastats_size').": ".$full_dump_size?>, <?= wfMsg('wikiastats_dbdump_generated') ?><?=$outFullDumpTime?> </font></li> 
		<li><strong><?=wfMsg('wikiastats_current_dump_stats')?>:</strong> <font style="color:gray;"><?=wfMsg('wikiastats_size').": ".$current_dump_size?>, <?= wfMsg('wikiastats_dbdump_generated') ?><?=$outCurrentDumpTime?> </font></li>
		</ul>
	 </li>	
	</ul> 
</td>
</tr>	
</table>
<!-- END OF WIKI's INFORMATION -->
<?php
} 
?>
