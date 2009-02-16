<?php

/**
 * @package MediaWiki
 * @subpackage SpecialPage
 * @author Piotr Molski (moli@wikia.com)
 * @version: $Id$
 */

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "This is MediaWiki extension and cannot be used standalone.\n";
    exit( 1 ) ;
}

define ("STATS_TREND_MONTH", 5);
define ("STATS_TREND_CITY_NBR", 20);
define ("STATS_WIKIANS_RANK_NBR", 100);
define ("STATS_COLUMN_CITY_NBR", 59);
define ("MIN_STATS_DATE", '2001-01');
define ("STATS_COLUMN_PREFIX", "m_");
define ("MIN_STATS_YEAR", '2008');
define ("MIN_STATS_MONTH", '01');
define ("RANGE_STATS_MIN", 'A');
define ("RANGE_STATS_MAX", 'X');
define ("STATS_EMPTY_LINE_TAG", "_empty_%s");
define ("DEFAULT_WIKIA_XLS_FILENAME", "wikia_xls_%d");
define ("MAX_CHART_HEIGHT", '180');
define ("CHART_BAR_WIDTH", '14');
define ("CHART_BAR_WIDTH_UNIT", 'px');
define ("CENTRAL_WIKIA_ID", 'wikicities');

$wgExtensionCredits['specialpage'][] = array(
    "name" => "[http://help.wikia.com/wiki/Help:WikiaStats WikiaStats]",
    "description" => "Gathers a wide array of statistics for this wiki and adds an GUI to browse them. Inspired by and partially based on the original [http://meta.wikimedia.org/wiki/Wikistats WikiStats] written by Erik Zachte",
    "author" => "Piotr Molski (moli) <moli@wikia-inc.com>"
);

$wgStatsExcludedNonSpecialGroup = array();
$wgStatsSpecialGroupUser = false;

#--- messages file
$wgExtensionMessagesFiles["WikiaStats"] = dirname(__FILE__) . '/SpecialWikiaStats.i18n.php';

#--- helper file
require_once( dirname(__FILE__) . '/SpecialWikiaStats_helper.php' );

#--- ajax's method file
require_once( dirname(__FILE__) . '/SpecialWikiaStats_ajax.php' );

#--- xls method file
require_once( dirname(__FILE__) . '/SpecialWikiaStats_xls.php' );

#--- register special page (MW 1.10 way)
if ( !function_exists( 'extAddSpecialPage' ) ) {
    require( "$IP/extensions/ExtensionFunctions.php" );
}

extAddSpecialPage( dirname(__FILE__) . '/SpecialWikiaStats_body.php', 'WikiaStats', 'WikiaStatsClass' );

?>
