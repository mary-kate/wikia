<!-- s:<?= __FILE__ ?> -->
<script type="text/javascript" src="/extensions/wikia/WikiaStats/js/wikiastats.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
<?
$xlsMenuHeader = addslashes(wfMsg("wikiastats_select_statistics"));
#---
$loop = 0;
$wikia_rows = "<select name=\"ws-city-list\" id=\"ws-city-list\" onChange=\"XLSShowMenu(this.value); WikiaStatsGetInfo('wk-stats-info-panel', this.value);\">";
$y = 0;
foreach ($cityStats as $id => $cityId) {
	if (!empty($cityList[$cityId])) { 
		$loop++;
		$selected = ($wgCityId == $cityId) ? "selected" : "";
		$wikia_rows .= "<option value=\"{$cityId}\" $selected>".( ($cityId != 0) ? ucfirst($cityList[$cityId]['dbname']): wfMsg('wikiastats_trend_all_wikia_text')) ."</option>";
	}
}
$wikia_rows .= "</select>";
?>
var YD = YAHOO.util.Dom;
var YE = YAHOO.util.Event;
var xlsMenuHeader = "<?=addslashes(wfMsg("wikiastats_select_wikia_statistics"))?>";
var wk_stats_city_id = 0;
var background_color = "";
var compare_stats = 0;

YAHOO.namespace("Wikia.Statistics");
(function() { 
    YAHOO.Wikia.Statistics = 
    {
	    init: function() {
            YD.get("compareStatsDialog").style.display = "block";
            if (!YAHOO.Wikia.Statistics.compareStatsDialog) { 
                YAHOO.Wikia.Statistics.handleSubmit = function() {
                    XLSCancel(); 
                    YD.get("compareStatsDialog_c").style.display = "none";
                    this.cancel(); 
                	StatsPageLoaderShow();
                    var checklist = document.XLSCompareForm.wscid;
                    var is_checked = 0; var checked_list = "";
                    for (i = 0; i < checklist.length; i++) { if (checklist[i].checked) { checked_list += checklist[i].value + ";"; is_checked++; } }
                    if (is_checked > <?= ($MAX_NBR + 1)?>) { alert(YAHOO.tools.printf("<?=addslashes(wfMsg('wikiastats_xls_generate_info'))?>", (<?=$MAX_NBR + 1?>))); return false; }
                    XLSGenerate(compare_stats, checked_list);
                };
                YAHOO.Wikia.Statistics.handleCancel = function() { 
                    XLSCancel(); 
                    YD.get("compareStatsDialog_c").style.display = "none";
                    StatsPageLoaderHide();
                    this.cancel(); 
                };
                // Instantiate the Dialog
                YAHOO.Wikia.Statistics.compareStatsDialog = new YAHOO.widget.Dialog("compareStatsDialog", {
                    width:"500px",fixedcenter:true,visible:false,draggable:false,zindex:9000,constraintoviewport:true,
                    buttons : [ { text:"<?=wfMsg('wikiastats_xls_generate')?>", handler:YAHOO.Wikia.Statistics.handleSubmit, isDefault:true },
                                { text:"<?=wfMsg('wikiastats_panel_close_btn')?>", handler:YAHOO.Wikia.Statistics.handleCancel } ]
                });
                // Render the Dialog
                YAHOO.Wikia.Statistics.compareStatsDialog.render(document.body);
				YD.get("compareStatsDialog_c").style.display = "none";
            }
            XLSShowMenu('<?=intval($wgCityId)?>');
            WikiaStatsGetInfo('wk-stats-info-panel', '<?=intval($wgCityId)?>');
        }
    }
    YE.onDOMReady(YAHOO.Wikia.Statistics.init, YAHOO.Wikia.Statistics, true); 
}
)();
YE.addListener("ws-check-cities", "click", XLSClearCitiesList);
pageLoaderInit('<?=wfMsg('wikiastats_generate_stats_msg')?>');
/*]]>*/
</script>
<!-- Statistics dialog -->
<div id="compareStatsDialog">
<div class="hd" id="ws-stats-dialog-hd"><?=wfMsg('wikiastats_comparision')?></div>
<div class="bd">
	<form name="XLSCompareForm" action="/" method="post">
	<div id="wk-select-cities-panel">
		<fieldset class="ws-frame-border">
		<legend class="normal"><?= wfMsg('wikiastats_mainstats_info') ?></legend>
			<div class="ws-div-scroll" id="ws-div-scroll"></div>
			<div class="clear"></div>
			<div class="ws-btn-panel">
				<span class="button-group">
					<button name="ws-check-cities" id="ws-check-cities" type="button"><?=wfMsg('wikiastats_xls_uncheck_list')?></button>
					<?=wfMsg('wikiastats_xls_press_uncheck')?>
				</span>
			</div>
		</fieldset>
	</div>
	</form>
</div>
</div>
<!-- end of statistics dialog -->
<div id="ws-xls-div"></div>
<div id="ws-main-table" style="height:100%">
<!-- WIKI's INFORMATION -->
<table style="width:auto; font-family: arial,sans-serif,helvetica;" height="100%" valign="top">
 <tr>
    <td class="panel-bootom-big" nowrap align="left">
        <strong><?= wfMsg('wikiastats_wikia') ?> <!--(<?=$loop?> <?= wfMsg('wikiastats_records') ?>)--></strong> 
    </td>
    <td nowrap align="left" style="width:30px;">&nbsp;</td>
    <td class="panel-bootom-big" nowrap align="left">
        <strong><?= wfMsg('wikiastats_comparision') ?></strong>
    </td>
 </tr>
<!-- main tables -->
 <tr>
    <td nowrap align="left" valign="top" id="tdMenu" valign="top" height="100%">
	  	<fieldset style="margin:2px 0pt 2px 0pt">
		<div style="width:auto;padding:0px 1px;clear:both;margin-top:5pt;margin-bottom:2pt:margin-left:auto;margin-right:auto;">
		  <?=$wikia_rows?>
		</div>  
		<div class="wk-stats-main-panel" id="wk-stats-info-panel" style="clear:both"></div>
  		<div class="wk-select-class" style="float:right;width:auto;padding:10px 2px 5px 2px;clear:both;font-size:9.5pt;">
			<span style="padding-left: 10px;"><input type="checkbox" id="ws-show-charts" value="1" name="ws-show-charts">&nbsp; <?= wfMsg("wikiastats_showcharts") ?></span></span>
			<span style="padding-left: 10px;"><input type="button" name="ws-show-stats" value="<?= wfMsg("wikiastats_showstats_btn") ?>" onClick="redirectToStats()"></span>
		</div>
		</fieldset>

		<div style="float:left; width:100%; padding: 0px 1px;clear:both;" id="ws-main-xls-stats">
	  	<fieldset style="margin:0px">
	  	<legend><?=wfMsg('wikiastats_generate_XLS_file_title')?></legend>
		<div class="wk-stats-main-panel" id="wk-stats-main-panel">
			<ul><li id="wk-xls-pagetitle"><a href="javascript:void(0);" onClick="XLSStats('1');"><?=wfMsg("wikiastats_pagetitle")?></a></li></ul>
		</div>
		<div class="wk-stats-main-panel" id="wk-stats-panel">
			<ul>
			<li><a href="javascript:void(0);" onClick="XLSStats('2');"><?=wfMsg("wikiastats_distrib_article")?></a></li>
			<li><a href="javascript:void(0);" onClick="XLSStats('3');"><?=wfMsg("wikiastats_active_absent_wikians")?></a></li>
			<li><a href="javascript:void(0);" onClick="XLSStats('4');"><?=wfMsg("wikiastats_anon_wikians")?></a></li>
			<li><a href="javascript:void(0);" onClick="XLSStats('5');"><?=wfMsg("wikiastats_article_one_link")?></a></li>
			<li><a href="javascript:void(0);" onClick="XLSStats('6');"><?=wfMsg("wikiastats_namespace_records")?></a></li>
			<li><a href="javascript:void(0);" onClick="XLSStats('7');"><?=wfMsg("wikiastats_page_edits")?></a></li>
			</ul>
		</div>
		</fieldset>
		</div>
    </td>
    <td nowrap align="left" valign="top">&nbsp;</td>
    <td nowrap align="left" valign="top">
       <table style="width: 400px;line-height:11pt" cellpadding="0" cellspacing="0">
<? $k = 7; for ($i=1; $i<=23; $i++) { $l = $k + $i; ?>	
        <tr><td class="wstab"><?= wfMsg("wikiastats_comparisons_table_$i") ?></td>
        <td class="wstabopt"><a href="javascript:void(0);" onClick="showXLSCompareDialog('<?=$l?>');"><?= wfMsg('wikiastats_xls_files_stats') ?></a>&nbsp;-&nbsp;<a href="javascript:void(0);" onClick="ShowCompareStats('<?=$i?>');"><?= wfMsg('wikiastats_tables') ?></a></td></tr>
<? if ($i == 2) { ?>
        <tr><td class="wstabbot" style="line-height:7pt" colspan="2">&nbsp;</tr>
<? } } ?>	
       </table>
    </td>
 </tr>
</table>
</div>
<!-- e:<?= __FILE__ ?> -->
