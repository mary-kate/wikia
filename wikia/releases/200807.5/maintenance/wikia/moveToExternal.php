<?php

ini_set( "include_path", dirname(__FILE__)."/../" );

if ( !defined( 'MEDIAWIKI' ) ) {
	$optionsWithArgs = array( 'm', 's' );

	require_once( dirname(__FILE__) . '/../commandLine.inc' );
	require_once( 'ExternalStoreDB.php' );
	require_once( 'maintenance/storage/resolveStubs.php' );

	$fname = 'moveToExternal';

	if ( !isset( $args[0] ) ) {
		print "Usage: php moveToExternal.php <cluster>\n";
		exit;
	}

	$cluster = $args[0];
	moveToExternal( $cluster );
}



function moveToExternal( $cluster ) {
	$fname = 'moveToExternal';
	$dbw = wfGetDB( DB_MASTER );
	$dbr = wfGetDB( DB_SLAVE );

	$ext = new ExternalStoreDB;
	$numMoved = 0;
	$numStubs = 0;

	$res = $dbr->query(
		"SELECT * FROM revision r1 FORCE INDEX (PRIMARY), text t2
		WHERE old_id = rev_text_id
		AND old_flags NOT LIKE '%external%'
		ORDER BY rev_timestamp, rev_id",
		$fname
	);

	while ( $row = $dbr->fetchObject( $res ) ) {
		$text = $row->old_text;
		$id = $row->old_id;
		if ( $row->old_flags === '' ) {
			$flags = 'external';
		} else {
			$flags = "{$row->old_flags},external";
		}

		if ( strpos( $flags, 'object' ) !== false ) {
			$obj = unserialize( $text );
			$className = strtolower( get_class( $obj ) );
			if ( $className == 'historyblobstub' ) {
				#resolveStub( $id, $row->old_text, $row->old_flags );
				#$numStubs++;
				continue;
			} elseif ( $className == 'historyblobcurstub' ) {
				$text = gzdeflate( $obj->getText() );
				$flags = 'utf-8,gzip,external';
			} elseif ( $className == 'concatenatedgziphistoryblob' ) {
				// Do nothing
			} else {
				print "Warning: unrecognised object class \"$className\"\n";
				continue;
			}
		} else {
			$className = false;
		}

		$ext = new ExternalStoreDB;
		$url = $ext->store( $cluster, $text );
		if ( !$url ) {
			print "Error writing to external storage\n";
			exit;
		}

		print "Storing "  . strlen( $text ) . " bytes to $url\n";
		print "old_id=$id\n";

		$dbw->update(
			'text',
			array( 'old_flags' => $flags, 'old_text' => $url ),
			array( 'old_id' => $id ),
			$fname
		);

		$revision = Revision::newFromId( $row->rev_id );
		if( $revision ) {
			$extUpdate = new ExternalStorageUpdate( $url, $revision );
			$extUpdate->doUpdate();
		}
		else {
			echo "Cannot load revision by id = {$row->rev_id}\n";
		}
		$numMoved++;
	}
	$dbr->freeResult( $res );
}
