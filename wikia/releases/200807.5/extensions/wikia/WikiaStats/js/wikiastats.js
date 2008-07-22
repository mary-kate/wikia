function StatsPageLoaderShow() { YAHOO.pageloader.container.wait.show(); };
function StatsPageLoaderHide() { YAHOO.pageloader.container.wait.hide(); };
function XLSPanelClose() { XLSCancel(); }
function XLSClearCitiesList() { var checklist = document.XLSCompareForm.wscid; var is_checked = 0; var checked_list = ""; for (i = 1; i < checklist.length; i++) checklist[i].checked = false; }
function XLSIframeStatusChanged() { /* StatsPageLoaderHide */ }
function XLSIframeLoaded(panel, statistics) { StatsPageLoaderHide(); }
function XLSGenerate(statistics, others) { 
	var params 	= "&rsargs[0]=" + wk_stats_city_id + "&rsargs[1]=" + statistics;
    YD.get("ws-xls-div").innerHTML = "";
	if (others != '') params += "&rsargs[2]=" + others;
	//----
    var baseurl = "/index.php?action=ajax&rs=axWStatisticsXLS" + params;
    if (window.frames['ws_frame_xls_'+wk_stats_city_id+'_'+statistics]) {
    	delete window.frames['ws_frame_xls_'+wk_stats_city_id+'_'+statistics];
	}
	YD.get("ws-xls-div").innerHTML = "<iframe name=\"ws_frame_xls_"+wk_stats_city_id+"_"+statistics+"\" id=\"ws_frame_xls\" src=\""+baseurl+"\" onLoad=\"javascript:XLSIframeLoaded('ws-xls-div'," + statistics + ");\" style=\"width:0px;height:0px\" frameborder=\"0\"></iframe>";
	window.frames['ws_frame_xls_'+wk_stats_city_id+'_'+statistics].onreadystatechange = XLSIframeStatusChanged();
}
function XLSCancel() { YD.get("ws-xls-div").innerHTML = ""; }
function XLSShowMenu(city) { YAHOO.util.Dom.get("wk-stats-panel").style.display = (city == 0) ? "none" : "block"; YAHOO.util.Dom.get("ws-main-xls-stats").style.display = "block"; }
function WikiaStatsGetInfo(panel, city) {
	WikiaInfoCallback = { success: function( oResponse ) { YD.get(panel).innerHTML = oResponse.responseText; }, failure: function( oResponse ) { YD.get(panel).innerHTML = ""; } };
	YD.get(panel).innerHTML = "<div class=\"wk-progress-stats-panel\"><center><img src=\"/extensions/wikia/WikiaStats/images/ajax_indicators.gif\" border=\"0\"></center></div>";
	var baseurl = "/index.php?action=ajax&rs=axWStatisticsWikiaInfo&rsargs[0]=" + city;
	YAHOO.util.Connect.asyncRequest( "GET", baseurl, WikiaInfoCallback);	
};
function pageLoaderInit() {
	YAHOO.namespace("pageloader.container");
	if (!YAHOO.pageloader.container.wait) {
		YAHOO.pageloader.container.wait = new YAHOO.widget.Panel("wait", {width:"350px",fixedcenter:true,close:false,draggable:false,zindex:99999,modal:true,visible:false,});
		YAHOO.pageloader.container.wait.setHeader("Statistics are generated ... please wait ...");
		YAHOO.pageloader.container.wait.setBody("<center><img src=\"/extensions/wikia/WikiaStats/images/ajax_indicators.gif\"/></center>");
		YAHOO.pageloader.container.wait.render(document.body);
	}
};
function redirectToStats() { var city  = parseInt(document.getElementById("ws-city-list").value); var charts = document.getElementById("ws-show-charts").checked; StatsPageLoaderShow();
    if (charts) { document.location = "/index.php?title=Special:WikiaStats&action=citycharts&city=" + city; } else { document.location = "/index.php?title=Special:WikiaStats&action=citystats&city=" + city; }
}
function showXLSCompareDialog(statistics) {
	compare_stats = statistics;
	//----
	if (statistics == 9) { StatsPageLoaderShow(); wk_stats_city_id = 0; XLSGenerate(statistics, ''); }
	else {
		CitiesListCallback = { 
			success: function( oResponse ) { YD.get("ws-div-scroll").innerHTML = oResponse.responseText; },
			failure: function( oResponse ) { YD.get("ws-div-scroll").innerHTML = ""; }
		};

		YAHOO.Wikia.Statistics.compareStatsDialog.show();
		YD.get("compareStatsDialog_c").style.display = "block";
		var city_list = document.getElementById( "ws-div-scroll" );
		if (city_list.innerHTML == "") {
			city_list.innerHTML = "<div class=\"wk-progress-stats-panel\"><center><img src=\"/extensions/wikia/WikiaStats/images/ajax_indicators.gif\" border=\"0\"></center></div>";
			YAHOO.util.Connect.asyncRequest( "GET", "/index.php?action=ajax&rs=axWStatisticsWikiaList", CitiesListCallback);
		}
	}
}
function XLSStats(id) { StatsPageLoaderShow(); wk_stats_city_id = parseInt(document.getElementById("ws-city-list").value); XLSGenerate(id, ''); }
function ShowCompareStats(id) { StatsPageLoaderShow(); document.location.href="/index.php?title=Special:WikiaStats&action=compare&table=" + id; }
