<?php
/**
 * DB accessable external objects, all revisions from all databases are merged
 * in one table.
 */

/**+ tables definition
CREATE TABLE `revisions` (
  `id` int(10) NOT NULL auto_increment,
  `rev_wikia_id` int(8) unsigned NOT NULL,
  `rev_id` int(10) unsigned default NULL,
  `rev_page_id` int(10) unsigned NOT NULL,
  `rev_namespace` int(10) unsigned NOT NULL default '0',
  `rev_user` int(10) unsigned NOT NULL default '0',
  `rev_user_text` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `rev_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `rev_text` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `rev_page_id` (`rev_wikia_id`,`rev_page_id`,`rev_id`),
  KEY `rev_namespace` (`rev_wikia_id`,`rev_page_id`,`rev_namespace`),
  KEY `rev_user` (`rev_wikia_id`,`rev_user`,`rev_timestamp`),
  KEY `rev_user_text` (`rev_wikia_id`,`rev_user_text`,`rev_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8


CREATE TABLE `pages` (
  `page_wikia_id` int(8) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `page_namespace` int(10) unsigned NOT NULL default '0',
  `page_title` varchar(255) NOT NULL,
  `page_counter` int(8) unsigned NOT NULL default '0',
  `page_edits` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`page_wikia_id`,`page_id`),
  KEY `page_namespace` (`page_wikia_id`,`page_namespace`,`page_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
**/

$wgHooks[ "RevisionInsertOnAfter" ][] = array( "ExternalStorageUpdate::addDeferredUpdate") ;

class ExternalStorageUpdate {

	private $mId, $mUrl, $mPageId, $mRevision;

	public function __construct( $url, $revision ) {
		$this->mUrl = $url;
		$this->mRevision = $revision;
		$this->mPageId = $revision->getPage();
	}


	public function doUpdate() {
		$path = explode( "/", $this->mUrl );
		$store    = $path[0];
		$cluster  = $path[2];
		$id	      = $path[3];

		$title    = $Title = Title::newFromID( $this->mPageId );

		/**
		 * we should not call this directly, we'll use new loadbalancer factory
		 * when 1.13 will be alive
		 */
		$external = new ExternalStoreDB();
		$dbw = $external->getMaster();

		/**
		 * explicite transaction
		 */
		$dbw->begin();
		$ret = $dbw->insert(
			array( "revisions" ),
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
		if( $store_id ) {
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

		$dbw->commit();
	}

	static public function addDeferredUpdate( &$revision, &$url, &$flags ) {
		global $wgDeferredUpdateList;

		$u = new ExternalStorageUpdate( $url, $revision );
		array_push( $wgDeferredUpdateList, $u );

		error_log( __METHOD__ . ": bangladesz" );

		return true;
	}
};
