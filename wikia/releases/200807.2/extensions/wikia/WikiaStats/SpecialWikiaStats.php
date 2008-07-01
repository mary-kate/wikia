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
define ("STATS_TREND_CITY_NBR", 23);
define ("STATS_COLUMN_CITY_NBR", 29);
define ("MIN_STATS_DATE", '2001-01');
define ("STATS_COLUMN_PREFIX", "m_");
define ("MIN_STATS_YEAR", '2004');
define ("MIN_STATS_MONTH", '01');
define ("STATS_EMPTY_LINE_TAG", "_empty_%s");
define ("DEFAULT_WIKIA_XLS_FILENAME", "wikia_xls_%d");

$wgExtensionCredits['specialpage'][] = array(
    "name" => "WikiaStats",
    "description" => "Wikia Statistics",
    "author" => "Piotr Molski (moli) <moli@wikia.com>"
);

#--- messages file
require_once( dirname(__FILE__) . '/SpecialWikiaStats.i18n.php' );

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
