<?php
/**
 * @package MediaWiki
 * @addtopackage maintenance
 *
 * clear wikifactory variables cache
 *
 * Usage:
 * (particular wiki)
 * maintenance/wikia/clear_wikifactory_cache.php --city=<city_id_from_city_list>
 *
 * or
 * (whole cache, all wikis)
 * maintenance/wikia/clear_wikifactory_cache.php
 */

ini_set( "include_path", dirname(__FILE__)."/.." );
require_once( "commandLine.inc" );

$optionsWithArgs = array( "city" );
print_r( $options );
$city_id = isset( $options[ "city" ] ) ? $options[ "city" ] : false;
echo $city_id."\n";
$condition = ( $city_id )
	? array( "city_public"  => 1, "city_id" => $city_id )
	: array( "city_public"  => 1 );


$dbr = wfGetDB( DB_SLAVE );

$res = $dbr->select(
	wfSharedTable( "city_list" ),
	array( "city_id", "city_dbname" ),
	$condition,
	__FILE__,
	array( "ORDER BY" => "city_id" )
);

while ( $row = $dbr->fetchObject( $res ) ) {
	WikiFactory::clearCache( $row->city_id );
	printf("%s removing %5d:%s from cache\n", wfTimestamp( TS_DB, time() ), $row->city_id, $row->city_dbname  );
}
$dbr->close();
