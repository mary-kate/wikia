<?php
/**
 * @package MediaWiki
 * @addtopackage maintenance
 */

ini_set( "include_path", dirname(__FILE__)."/.." );
require_once( "commandLine.inc" );


$exclude = array(
"keroro" => true, "dcanimated" => true, "harrypotterfanon" => true,
"jaeq2" => true, "dragonball" => true, "solarcooking" => true,
"pirates" => true, "jalotro" => true, "jagundam" => true,
"government" => true, "masseffect" => true, "furry" => true,
"dauncyclopedia" => true, "locopedia" => true, "callofduty" => true,
"alienresearch" => true, "bionic" => true, "creativesci_fi" => true,
"kylexy" => true, "clubpenguin" => true, "ratchet" => true, "quest" => true,
"eberron" => true, "lost" => true, "icehockey" => true, "apocalipse" => true,
"tmnt" => true, "deflyff" => true, "marvelcomicsfanon" => true,
"christianity" => true, "guns" => true, "foreverknight" => true,
"ruhistory" => true, "baseball" => true, "starcraft" => true, "fantendo" => true,
"nlstarwars" => true, "desencyclopedie" => true, "nwn" => true, "plwim" => true,
"mk" => true, "banjokazooie" => true, "cristianismo" => true,
"runescapefanfiction" => true, "tractors" => true, "filmguide" => true,
"christianmusic" => true, "plharrypotter" => true, "burnoutparadise" => true,
"swg" => true, "nationstates" => true, "transfanon" => true, "demario" => true,
"entravel" => true, "nfl" => true, "potbs" => true, "pikmin" => true,
"gundam" => true, "frdesign" => true, "egyptology" => true, "stad" => true,
"defaerun" => true, "particracy" => true, "schools" => true, "guilds" => true,
"enmemoryalpha" => true, "zoids" => true, "gilmoregirls" => true, "simpsons" => true,
"sealonline" => true, "absurdopedia" => true, "suikoden" => true, "beamer" => true,
"degta" => true, "babyish" => true, "plshadowrun" => true, "ruwriters" => true,
"aoc" => true, "de" => true, "fireemblem" => true, "kingdomhearts" => true,
"lotr" => true, "tor4" => true, "metro" => true, "indianajones" => true,
"smallville" => true, "uncyclopedia_de" => true, "trams" => true, "familyguy" => true,
"esstarwars" => true, "jagame" => true, "onepiece" => true, "novelas" => true,
"uktransport" => true, "enkirby" => true, "buckethead" => true, "nlmemoryalpha" => true,
"futurama" => true, "darkhorse" => true, "lawandorder" => true, "residentevil" => true,
"spore" => true, "twewy" => true, "paltin" => true, "planastacia" => true,
"ikariam" => true, "ptsimpsons" => true, "ennintendo" => true, "jagcc" => true,
"howto" => true, "sca21" => true, "esgta" => true, "transformers" => true,
"deffxi" => true, "vim" => true, "zhuncyclopedia" => true, "necyklopedie" => true,
"enrohan" => true, "bucuresti" => true, "illogicopedia" => true, "powerrangers" => true,
"eincyclopedia" => true, "dememoryalpha" => true, "cybernations" => true,
"24" => true, "endcdatabase" => true, "twelvesands" => true,
"fightingfantasy" => true, "whitewolf" => true, "scratchpad" => true, "ffxi" => true,
"tardis" => true, "swfanon" => true, "egamia" => true, "frguildwars" => true,
"finalfantasy" => true, "tibiawiki" => true, "spongebob" => true, "psychology" => true,
"annex" => true, "eq2i" => true, "startrek" => true, "lgbt" => true, "ruscience" => true,
"ceramica" => true, "starwarsexodus" => true, "ptpoesia" => true,
"stexpanded" => true, "nonciclopedia" => true, "gwguild" => true,
"forgottenrealms" => true, "inciclopedia" => true, "runescape" => true, "nonsensopedia" => true,
"uncyclo" => true, "proteins" => true, "enmarveldatabase" => true
);
$cities = array();

$dbr = wfGetDB( DB_SLAVE );
$res = $dbr->select(
	wfSharedTable( "city_list" ),
	array( "city_id", "city_dbname" ),
	array( "city_public"  => 1 ),
	__FILE__,
	array( "ORDER BY" => "city_id" )
);
$matched = 0;
while ( $row = $dbr->fetchObject( $res ) ) {
	if( isset( $exclude[ strtolower( $row->city_dbname ) ] ) && $exclude[ strtolower( $row->city_dbname ) ] ) {
		echo "removing {$row->city_dbname} from list.\n";
		unset( $exclude[ strtolower( $row->city_dbname ) ] );
		$matched++;
	}
	else {
		$cities[] = $row->city_id;
	}
}
echo "Removed {$matched} (" . count($exclude ) . "), will set ". count($cities)." wikis.\n";
foreach( $cities as $city_id ) {
	WikiFactory::setVarByName( "wgEnableNewParser", $city_id, true );
	WikiFactory::clearCache( $city_id );
}
$dbr->close();
