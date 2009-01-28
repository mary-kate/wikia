<?php

/**
 * WikiaApiQueryPopularPages - get list of popular pages for selected namespace
 *
 * @author David Pean <david.pean@wikia.com>
 *
 * @todo 
 *
 */

class WikiaApiQueryPagesyByCategory extends WikiaApiQuery {
    /**
     * constructor
     */

	public function __construct($query, $moduleName) {
		parent :: __construct($query, $moduleName);
	}

    /**
     * main function
     */
	public function execute() {
		global $wgUser;

		switch ($this->getActionName()) {
			case parent::INSERT :
								{
									// to do
									break;
								}
			case parent::UPDATE :
								{
									// to do
									break;
								}
			case parent::DELETE :
								{
									// to do
									break;
								}
			default: // query
								{
									$this->getPagesByCategory ();
									break;
								}
		}
	}

	#---
	private function getPagesByCategory() {
		global $wgDBname;
		
        #--- blank variables
        $category = null;
	$order = null;
	
        #--- initial parameters (dbname, limit, offset ...)
		extract($this->getInitialParams());

        #--- request parameters ()
		extract($this->extractRequestParams());

		$this->initCacheKey($lcache_key, __METHOD__);
	
		try {
			#--- database instance
			$db =& $this->getDB();
			$db->selectDB( (!defined(WIKIA_API_QUERY_DBNAME)) ? WIKIA_API_QUERY_DBNAME : $wgDBname );

			if ( is_null($db) ) {
				//throw new DBConnectionError($db, 'Connection error');
				throw new WikiaApiQueryError(0);
			}
			
			$category_db = Title::newFromText( $category );
			$category_db = $category_db->getDbKey();
			$this->addTables( array( "page", "categorylinks" ) );
			$this->addFields( array(
				'page_id',
				'page_namespace',
				'page_title'
				));
			$this->addWhere ( "page_id=cl_from" );
			$this->addWhere ( array("cl_to" => $category_db)  );
		
			$this->setCacheKey ($lcache_key, 'C', $category);
			
			#--- limit
			if ( !empty($limit)  ) { //WikiaApiQuery::DEF_LIMIT
				if ( !$this->isInt($limit) ) {
					throw new WikiaApiQueryError(1);
				}
				$this->addOption( "LIMIT", $limit );
				$this->setCacheKey ($lcache_key, 'L', $limit);
			}

			#--- offset
			if ( !empty($offset)  ) { //WikiaApiQuery::DEF_LIMIT_COUNT
				if ( !$this->isInt($offset) ) {
					throw new WikiaApiQueryError(1);
				}
				$this->addOption( "OFFSET", $offset );
				$this->setCacheKey ($lcache_key, 'LO', $limit);
			}
			
			$order_field = "page_id";
			if ( !empty($order)  ) { //WikiaApiQuery::DEF_LIMIT_COUNT

				switch( $order ){
					case "edit":
						$order_field = "page_touched";
						break;
					case "random":
						$this->addWhere ( "page_random >= " . wfRandom() );
						break;
				}
				$this->setCacheKey ($lcache_key, 'ORD', $order);
				
			}
		
			$this->addOption( "ORDER BY", "{$order_field} desc" );
			
			#--- group by
			#$this->addOption( "GROUP BY", "article_id" );

			$data = array();
			// check data from cache ...
			$cached = $this->getDataFromCache($lcache_key);
			if (!is_array($cached)) {
				$res = $this->select(__METHOD__);
				while ($row = $db->fetchObject($res)) {
					$data[$row->page_id] = array(
						"id"			=> $row->page_id,
						"namespace"		=> $row->page_namespace,
						"title"			=> $row->page_title,
						"url"			=> Title::makeTitle( $row->page_namespace, $row->page_title)->escapeFullURL()
					);
					ApiResult :: setContent( $data[$row->page_id], $row->page_title);
				}
				$db->freeResult($res);
				$this->saveCacheData($lcache_key, $data, $ctime);
			} else {
				// ... cached
				$data = $cached;
			}		
		} catch (WikiaApiQueryError $e) {
			// getText();
		} catch (DBQueryError $e) {
			$e = new WikiaApiQueryError(0, 'Query error: '.$e->getText());
		} catch (DBConnectionError $e) {
			$e = new WikiaApiQueryError(0, 'DB connection error: '.$e->getText());
		} catch (DBError $e) {
			$e = new WikiaApiQueryError(0, 'Error in database: '.$e->getLogMessage());
		}

		// is exception
		if ( isset($e) ) {
			$data = $e->getText();
			$this->getResult()->setIndexedTagName($data, 'fault');
		}
		else
		{
			$this->getResult()->setIndexedTagName($data, 'item');
		}

		$this->getResult()->addValue('query', $this->getModuleName(), $data);
	}

	/*
	 * 
	 * Description's functions
	 * 
	 */ 
	#---
	protected function getQueryDescription() {
		return 'Get Wikia pages by category';
	}

	/*
	 * 
	 * Description's parameters
	 * 
	 */ 
	#---
	protected function getParamQueryDescription() {
		return 	array (
			'category' => 'category to get pages for',
		);
	}

	/*
	 * 
	 * Allowed parameters
	 * 
	 */ 
	
	#---
	protected function getAllowedQueryParams() {
		return array (
			"category" => array (
				ApiBase :: PARAM_TYPE => 'string'
			),
			"order" => array (
				ApiBase :: PARAM_TYPE => 'string'
			)
		);
	} 

	/*
	 * 
	 * Examples
	 * 
	 */ 
	#---
	protected function getQueryExamples() {
		return array (
			'api.php?action=query&list=wkpagesincat',
			'api.php?action=query&list=wkpagesincat&wkcategory=fun',
		);
	}

	/*
	 * 
	 * Version
	 * 
	 */ 
	#---
	public function getVersion() {
		return __CLASS__ . ': $Id: '.__CLASS__.'.php '.filesize(dirname(__FILE__)."/".__CLASS__.".php").' '.strftime("%Y-%m-%d %H:%M:%S", time()).'Z wikia $';
	}
};

?>
