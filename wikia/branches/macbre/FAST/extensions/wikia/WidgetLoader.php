<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Wikia Widgets loader
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Tomasz Klim <tomek@wikia.com>
 * @copyright Copyright (C) 2007 Tomasz Klim, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */


$wgExtensionCredits['other'][] = array(
	'name' => 'WidgetLoader',
	'description' => 'widget bootstrap loader',
	'author' => 'Tomasz Klim'
);


global $IP, $widgetFiles;

require_once( "$IP/extensions/wikia/WikiaWidgets/WidgetConfig.php" );
require_once( "$IP/extensions/wikia/WikiaWidgets/WidgetManager.php" );
require_once( "$IP/extensions/wikia/WikiaWidgets/WidgetRenderer.php" );
require_once( "$IP/extensions/wikia/WikiaWidgets/BaseWidget.php" );
require_once( "$IP/extensions/wikia/WikiaWidgets/BaseRSSWidget.php" );

require_once( "$IP/extensions/wikia/DataProvider/DataProvider.php" );

foreach ( $widgetFiles as $file ) {
	include_once( $file );
}

?>
