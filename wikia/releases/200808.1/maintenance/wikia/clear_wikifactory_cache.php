<?php
/**
 * @package MediaWiki
 * @addtopackage maintenance
 *
 * clear wikifactory variables cache
 *
 * Usage:
 * (particular wiki)
 * maintenance/wikia/clear_wikifactory_cache.php -i <city_id_from_city_list>
 *
 * or
 * (whole cache, all wikis)
 * maintenance/wikia/clear_wikifactory_cache.php
 */

ini_set( "include_path", dirname(__FILE__)."/.." );
require_once( "commandLine.inc" );

$wikiId = isset( $options['i'] ) ? $options['i'] : null;

if( is_null( $wikiId ) ) {
    WikiFactory::clearCache( $wikiId );
}
else {
    $dbw = wfGetDB( DB_SLAVE );

    $oRes = $dbw->select(
    	wfSharedTable("city_list"),
    	array( "city_id", "city_dbname" ),
    	array( "city_public = 1"),
    	__FILE__,
    	array( "ORDER BY" => "city_id" )
    );

    $aWikis = array();

    while ( $oRow = $dbw->fetchObject( $oRes ) ) {
    	WikiFactory::clearCache( $oRow->city_id );
    	echo "{$oRow->city_id}\t{$oRow->city_dbname}\tremoved from cache\n";
    }

    $dbw->freeResult( $oRes );
    $dbw->close();
}
