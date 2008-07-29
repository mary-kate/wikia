<?php

/**
 * includes common for all wikis
 */
require_once ( $IP."/includes/wikia/Defines.php" );
require_once ( $IP."/includes/wikia/GlobalFunctions.php" );

global $wgDBname, $wgKennisnet;
if($wgDBname != 'uncyclo' && !$wgKennisnet) {
	include_once( "$IP/extensions/wikia/SkinChooser/SkinChooser.php" );
}

/**
 * autoload classes
 */
global $wgAutoloadClasses;

/**
 * custom wikia classes
 */
$wgAutoloadClasses["EasyTemplate"]  =  $GLOBALS["IP"]."/includes/wikia/EasyTemplate.php";
$wgAutoloadClasses["Wikia"] = "includes/wikia/Wikia.php";
$wgAutoloadClasses["WikiFactory"] = $GLOBALS["IP"]."/extensions/wikia/WikiFactory/WikiFactory.php";
$wgAutoloadClasses["WikiMover"] = $GLOBALS["IP"]."/extensions/wikia/WikiFactory/Mover/WikiMover.php";
$wgAutoloadClasses["WikiFactoryHub"] = $GLOBALS["IP"]."/extensions/wikia/WikiFactory/Hubs/WikiFactoryHub.php";;

/**
 * API classes
 */

$wgAutoloadClasses["WikiaApiQuery"] = "extensions/wikia/WikiaApi/WikiaApiQuery.php";
$wgAutoloadClasses["WikiaApiQueryConfGroups"] = "extensions/wikia/WikiaApi/WikiaApiQueryConfGroups.php";
$wgAutoloadClasses["WikiaApiQueryDomains"] = "extensions/wikia/WikiaApi/WikiaApiQueryDomains.php";
$wgAutoloadClasses["WikiaApiQueryPopularPages"]  = "extensions/wikia/WikiaApi/WikiaApiQueryPopularPages.php";
$wgAutoloadClasses["WikiaApiFormatTemplate"]  = "extensions/wikia/WikiaApi/WikiaApiFormatTemplate.php";
$wgAutoloadClasses["WikiaApiQueryVoteArticle"] = "extensions/wikia/WikiaApi/WikiaApiQueryVoteArticle.php";
$wgAutoloadClasses["WikiaApiQueryWrite"] = "extensions/wikia/WikiaApi/WikiaApiQueryWrite.php";
$wgAutoloadClasses["WikiaApiQueryMostAccessPages"] = "extensions/wikia/WikiaApi/WikiaApiQueryMostAccessPages.php";
$wgAutoloadClasses["WikiaApiQueryLastEditPages"] = "extensions/wikia/WikiaApi/WikiaApiQueryLastEditPages.php";
$wgAutoloadClasses["WikiaApiQueryTopEditUsers"] = "extensions/wikia/WikiaApi/WikiaApiQueryTopEditUsers.php";
$wgAutoloadClasses["WikiaApiQueryMostVisitedPages"] = "extensions/wikia/WikiaApi/WikiaApiQueryMostVisitedPages.php";
$wgAutoloadClasses["WikiaApiQueryReferers"] = "extensions/wikia/WikiaApi/WikiaApiQueryReferers.php";
$wgAutoloadClasses["ApiFeaturedContent"] = "extensions/wikia/FeaturedContent/ApiFeaturedContent.php";
$wgAutoloadClasses["ApiPartnerWikiConfig"] = "extensions/wikia/FeaturedContent/ApiPartnerWikiConfig.php";
$wgAutoloadClasses["WikiaApiAjaxLogin"] = "extensions/wikia/WikiaApi/WikiaApiAjaxLogin.php";
$wgAutoloadClasses["ApiImageThumb"] = $GLOBALS["IP"]."/extensions/wikia/Our404Handler/ApiImageThumb.php";
//$wgAutoloadClasses["ApiRecentChangesCombined"] = "extensions/wikia/RecentChangesCombined/ApiRecentChangesCombined.php";


/**
 * registered API methods
 */
global $wgApiQueryListModules;
$wgApiQueryListModules["wkconfgroups"] = "WikiaApiQueryConfGroups";
$wgApiQueryListModules["wkdomains"] = "WikiaApiQueryDomains";
$wgApiQueryListModules["wkpoppages"] = "WikiaApiQueryPopularPages";
$wgApiQueryListModules["wkvoteart"] = "WikiaApiQueryVoteArticle";
$wgApiQueryListModules["wkaccessart"] = "WikiaApiQueryMostAccessPages";
$wgApiQueryListModules["wkeditpage"] = "WikiaApiQueryLastEditPages";
$wgApiQueryListModules["wkedituser"] = "WikiaApiQueryTopEditUsers";
$wgApiQueryListModules["wkmostvisit"] = "WikiaApiQueryMostVisitedPages";
$wgApiQueryListModules["wkreferer"] = "WikiaApiQueryReferers";

/**
 * registered Ajax methods
 */
global $wgAjaxExportList;


/**
 * registered Format names
 */
global $wgApiMainListFormats;
$wgApiMainListFormats["wktemplate"] = "WikiaApiFormatTemplate";

/*
 * reqistered API modules
 */
global $wgAPIModules;
$wgAPIModules["insert"] = "WikiaApiQueryWrite";
$wgAPIModules["update"] = "WikiaApiQueryWrite";
$wgAPIModules["delete"] = "WikiaApiQueryWrite";
//$wgAPIModules["recentchangescombined"] = "ApiRecentChangesCombined";
$wgAPIModules["featuredcontent"] = "ApiFeaturedContent";
$wgAPIModules["partnerwikiconfig"] = "ApiPartnerWikiConfig";
$wgAPIModules["ajaxlogin"] = "WikiaApiAjaxLogin";
$wgAPIModules["imagethumb"] = "ApiImageThumb";

/*
 * Widget FrameWork declarations
 */
global $wgWidgetFrameWork;
if ( $wgWidgetFrameWork) {
    require_once ( 'widgetFrameWork/lib/widgetConfig.php' );
}
