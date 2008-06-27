<?php
/**
 * DB accessable external objects, all revisions from all databases are merged
 * in one table.
 */


#CREATE TABLE `revisions` (
#  `id` int(10) NOT NULL auto_increment,
#  `rev_wikia_id` int(8) unsigned NOT NULL,
#  `rev_id` int(10) unsigned NOT NULL,
#  `rev_page_id` int(10) unsigned NOT NULL,
#  `rev_namespace` int(10) unsigned NOT NULL default '0',
#  `rev_user` int(10) unsigned NOT NULL default '0',
#  `rev_user_text` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
#  `rev_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
#  `rev_text` mediumtext NOT NULL,
#  PRIMARY KEY  (`id`),
#  KEY `rev_page_id` (`rev_wikia_id`,`rev_page_id`,`rev_id`),
#  KEY `rev_namespace` (`rev_wikia_id`,`rev_page_id`,`rev_namespace`),
#  KEY `rev_user` (`rev_wikia_id`,`rev_user`,`rev_timestamp`),
#  KEY `rev_user_text` (`rev_wikia_id`,`rev_user_text`,`rev_timestamp`)
#) ENGINE=InnoDB DEFAULT CHARSET=utf8
#
#
#CREATE TABLE `pages` (
#  `page_wikia_id` int(8) unsigned NOT NULL,
#  `page_id` int(10) unsigned NOT NULL,
#  `page_namespace` int(10) unsigned NOT NULL default '0',
#  `page_title` varchar(255) NOT NULL,
#  `page_counter` int(8) unsigned NOT NULL default '0',
#  `page_edits` int(10) unsigned NOT NULL default '0',
#  PRIMARY KEY (`page_wikia_id`,`page_id`),
#  KEY `page_namespace` (`page_wikia_id`,`page_namespace`,`page_title`)
#) ENGINE=InnoDB DEFAULT CHARSET=utf8

$wgHooks[ "RevisionAfterInsertOn" ][] = 'wfExternalStoreMergedUpdateRevId';
$wgDefaultExternalStore = "merged://archive";
$wgExternalStores = array( "merged" );

global $wgExternalBlobCache;
$wgExternalBlobCache = array();

global $wgExternalMergeBalancers;
$wgExternalMergeBalancers = array();


/**
 * Hook called as "RevisionAfterInsertOn"
 *
 * @access public
 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
 *
 * @param object	$revision	Revision object
 * @param string	$url		Saved url to external blob
 */
function wfExternalStoreMergedUpdateRevId( $revision, $url ) {

	wfProfileIn( __METHOD__ );

	$path = explode( "/", $url );
	$store    = $path[0];
	$cluster  = $path[2];
	$id	      = $path[3];
	$rev_id   = $revision->getId();

	$ExStorage = new ExternalStoreMerged();
	$ExStorage->updateRevisionId( $cluster, $id, $rev_id );

	wfProfileOut( __METHOD__ );

	return true;
}

/**
 * @name ExternalStoreMerged
 */
class ExternalStoreMerged {

	private $mDBbname	= "dataware";

	/** @todo Document.*/
	function &getLoadBalancer( $cluster ) {
		global $wgExternalServers, $wgExternalMergeBalancers;
		if( !array_key_exists( $cluster, $wgExternalMergeBalancers ) ) {
			$wgExternalMergeBalancers[ $cluster ] = LoadBalancer::newFromParams( $wgExternalServers[ $cluster ] );
		}
		$wgExternalMergeBalancers[ $cluster ]->allowLagged(true);
		return $wgExternalMergeBalancers[ $cluster ];
	}

	/** @todo Document.*/
	function &getSlave( $cluster ) {
		$lb = $this->getLoadBalancer( $cluster );
		return $lb->getConnection( DB_SLAVE );
	}

	/** @todo Document.*/
	function &getMaster( $cluster ) {
		$lb = $this->getLoadBalancer( $cluster );
		return $lb->getConnection( DB_MASTER );
	}

	/** @todo Document.*/
	function getTable( &$db, $name ) {
		$table = $db->getLBInfo( "{$this->mDBbname} {$name}" );
		if ( is_null( $table ) ) {
			$table = $name;
		}
		return $table;
	}

	/**
	 * Fetch data from given URL
	 * @param string $url An url of the form DB://cluster/id or DB://cluster/id/itemid for concatened storage.
	 */
	function fetchFromURL($url) {
		$path = explode( '/', $url );
		$cluster  = $path[2];
		$id	  = $path[3];
		if ( isset( $path[4] ) ) {
			$itemID = $path[4];
		} else {
			$itemID = false;
		}

		$ret = $this->fetchBlob( $cluster, $id, $itemID );

		if ( $itemID !== false && $ret !== false ) {
			return $ret->getItem( $itemID );
		}
		return $ret;
	}

	/**
	 * Fetch a blob item out of the database; a cache of the last-loaded
	 * blob will be kept so that multiple loads out of a multi-item blob
	 * can avoid redundant database access and decompression.
	 * @param $cluster
	 * @param $id
	 * @param $itemID
	 * @return mixed
	 *
	 * @access private
	 */
	function &fetchBlob( $cluster, $id, $itemID ) {
		global $wgExternalBlobCache;
		$cacheID = ( $itemID === false ) ? "$cluster/$id" : "$cluster/$id/";
		if( isset( $wgExternalBlobCache[ $cacheID ] ) ) {
			wfDebug( __METHOD__.": cache hit on $cacheID\n" );
			return $wgExternalBlobCache[ $cacheID ];
		}

		wfDebug( __METHOD__.": cache miss on $cacheID\n" );

		/**
		 * get revision from slave
		 */
		$dbr = $this->getSlave( $cluster );
		$Row = $dbr->selectRow(
			$this->getTable( $dbr, "revisions" ),
			array( "rev_text" ),
			array( "id" => $id ),
			__METHOD__
		);
		$ret = isset( $Row->rev_text ) ? $Row->rev_text : false;

		if ( $ret === false ) {
			#--- get revision from master
			$dbw = $this->getMaster( $cluster );
			$Row = $dbw->selectRow(
				$this->getTable( $dbw, "revisions" ),
				array( "rev_text" ),
				array( "id" => $id ),
				__METHOD__
			);
			$ret = isset( $Row->rev_text ) ? $Row->rev_text : false;
		}

		if( $itemID !== false && $ret !== false ) {
			// Unserialise object; caller extracts item
			$ret = unserialize( $ret );
		}

		$wgExternalBlobCache = array( $cacheID => &$ret );
		return $ret;
	}

	/**
	 * Insert a data item into a given cluster
	 *
	 * @param $cluster String: the cluster name
	 * @param $data String: the data item
	 * @param Revision	$revision	Revision object
	 *
	 * @return string URL
	 */
	function store( $cluster, $data, &$revision ) {
		global $wgCityId;

		if( !isset( $wgCityId ) ) {
			/**
			 * it's not wiki factory cluster
			 */
			return false;
		}
		$dbw = $this->getMaster( $cluster );
		$page = $revision->getPage();
		$Title = Title::newFromID( $page );
		$dbw->begin();

		/**
		 * fill revisions table
		 */
		$ret = $dbw->insert(
			$this->getTable( $dbw, "revisions" ),
			array(
				"id" => null,
				"rev_wikia_id" => $wgCityId,
				"rev_id" => 0, #--- we don't know id
				"rev_page_id" => $page,
				"rev_namespace" => 0,
				"rev_user" => $revision->getUser(),
				"rev_user_text" => $revision->getUserText(),
				"rev_text" => $data
			)
		);
		$store_id = $dbw->insertId();

		if( $ret ) {
			/**
			 * insert or update
			 */
			$Row = $dbw->selectRow(
				$this->getTable( $dbw, "pages" ),
				array( "page_id" ),
				array( "page_id" => $page ),
				__METHOD__
			);
			if( isset( $Row->page_id ) && !empty( $Row->page_id ) ) {
				/**
				 * update
				 */
				$dbw->update(
					$this->getTable( $dbw, "pages" ),
					array(
						"page_wikia_id"  => $wgCityId,
						"page_namespace" => $Title->getNamespace(),
						"page_title"     => $Title->getText(),
						"page_counter"   => 0,
						"page_edits"     => 0,
					),
					array(
						"page_id"        => $page,
					)
				);
			}
			else {
				/**
				 * insert
				 */
				$dbw->insert(
					$this->getTable( $dbw, "pages" ),
					array(
						"page_wikia_id"  => $wgCityId,
						"page_id"        => $page,
						"page_namespace" => $Title->getNamespace(),
						"page_title"     => $Title->getText(),
						"page_counter"   => 0,
						"page_edits"     => 0,
					)
				);
			}
		}
		else {
			$dbw->rollback();
			return false;
		}
		$dbw->commit();
		return "merged://$cluster/$store_id";
	}

	/**
	 * Fetch a blob item out of the database; a cache of the last-loaded
	 * blob will be kept so that multiple loads out of a multi-item blob
	 * can avoid redundant database access and decompression.
	 *
	 * @access public
	 * @author Krzysztof Krzyżaniak <eloy@wikia.com>

	 * @param string	$cluster	cluster name
	 * @param integer	$id			blob id in cluster
	 * @param integer	$rev_id		revision id for revisions table
	 *
	 * @return boolean
	 */
	public function updateRevisionId( $cluster, $id, $rev_id ) {

		$dbw = $this->getMaster( $cluster );

		error_log( "{$cluster} {$id} {$rev_id}" );

		return $dbw->update(
			$this->getTable( $dbw, "revisions" ),
			array( "rev_id" => $rev_id ),
			array( "id" => $id ),
			__METHOD__
		);
	}
}
