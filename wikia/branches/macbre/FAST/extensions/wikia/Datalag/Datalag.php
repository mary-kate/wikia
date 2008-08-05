<?php
/**
 * A Special Page extension that displays Wiki Google Webtools stats.
 * This page can be accessed from Special:Datalag
 * @addtogroup Extensions
 *
 * @author Andrew Yasinsky <andrewy@wikia.com>
 */

if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install this extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/wikia/Datalag/Datalag.php" );
EOT;
        exit( 1 );
}

$wgExtensionFunctions[] = 'wfSpecialDatalag';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Datalag',
	'author' => 'Andrew Yasinsky',
	'description' => 'Database Lag Status',
);

#--- permissions
$wgAvailableRights[] = 'datalag';
$wgGroupPermissions['staff']['datalag'] = true;
$wgSpecialPages['datalag'] = array('Datalag', 'datalag', false,false);
$wgAjaxExportList[] = 'datalagAjax';

function datalagAjax() {
  global $wgLoadBalancer;
  $lag = 0;
  $host = 'none';
    
  if( count( $wgLoadBalancer->mServers) > 1){  	

  list( $host, $lag ) = $wgLoadBalancer->getMaxLag();
    $name = @gethostbyaddr( $host );
	
	if ( $name !== false ) {
		$host = $name;
	}
   
  }
	
	$response = array('maxlag_host'=>$host, 'maxlag_sec'=>$lag);
	return new AjaxResponse( Wikia::json_encode( $response ) );
}