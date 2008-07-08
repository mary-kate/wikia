<?php

/**
 * @package MediaWiki
 * @subpackage SpecialPage
 * @author Krzysztof Krzyżaniak <eloy@wikia.com> for Wikia.com
 * @copyright (C) 2007, Wikia Inc.
 * @licence GNU General Public Licence 2.0 or later
 * @version: $Id$
 */

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "This is MediaWiki extension named RequestWiki.\n";
    exit( 1 ) ;
}

$wgExtensionCredits['specialpage'][] = array(
    "name" => "RequestWiki",
    "description" => "Request Wiki for creation",
    "author" => "Krzysztof Krzyżaniak (eloy) <eloy@wikia.com>"
);

#--- messages file
require_once( dirname(__FILE__) . '/SpecialRequestWiki.i18n.php' );
#--- helper file
require_once( dirname(__FILE__) . '/SpecialRequestWiki_helper.php' );

#--- permissions
$wgAvailableRights[] = 'requestwiki';
$wgGroupPermissions['*']['requestwiki'] = false;
$wgGroupPermissions['user']['requestwiki'] = true;
$wgGroupPermissions['staff']['requestwiki'] = true;

#--- register special page (MW 1.10 way)
if ( !function_exists( 'extAddSpecialPage' ) ) {
    require( "$IP/extensions/ExtensionFunctions.php" );
}
extAddSpecialPage( dirname(__FILE__) . '/SpecialRequestWiki_body.php', 'RequestWiki', 'RequestWikiPage' );

?>
