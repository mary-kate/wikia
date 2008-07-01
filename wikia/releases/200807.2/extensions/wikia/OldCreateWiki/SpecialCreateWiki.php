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
    echo "This is MediaWiki extension named CreateWiki.\n";
    exit( 1 ) ;
}

$wgExtensionCredits['specialpage'][] = array(
    "name" => "CreateWiki",
    "description" => "Create Wiki from Request:Wiki data",
    "author" => "Krzysztof Krzyżaniak (eloy) <eloy@wikia.com>"
);

#--- messages file
require_once( dirname(__FILE__) . '/SpecialCreateWiki.i18n.php' );
#--- helper file
require_once( dirname(__FILE__) . '/SpecialCreateWiki_helper.php' );

#--- permissions
$wgAvailableRights[] = 'createtwiki';
$wgGroupPermissions['staff']['createtwiki'] = true;

#--- register special page (MW 1.10 way)
if ( !function_exists( 'extAddSpecialPage' ) ) {
    require( "$IP/extensions/ExtensionFunctions.php" );
}
extAddSpecialPage( dirname(__FILE__) . '/SpecialCreateWiki_body.php', 'CreateWiki', 'CreateWikiForm' );

?>
