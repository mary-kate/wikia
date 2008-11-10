<?php

/**
 * @package MediaWiki
 * @subpackage WikiFactory
 *
 * @author Krzysztof Krzyżaniak (eloy) <eloy@wikia.com> for Wikia Inc.
 */

/**
 * Singleton class for handling Hubs
 */
class WikiFactoryHub {

    #--- static variables
    private static $mInstance = false;
	private static $mCategories = array();

    /**
	 * private constructor
	 *
     * @access private
     * @author Krzysztof Krzyżaniak <eloy@wikia.com>
     *
     */
    private function __construct( ) {
		self::$mCategories = $this->loadCategories();
    }

	/**
	 * getInstance
	 *
	 * get singleton instance of class
	 *
	 * @access public
	 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
	 *
	 * @return object	WikiFactoryHub object
	 */
	public static function getInstance() {
		if( self::$mInstance === false ) {
			self::$mInstance = new WikiFactoryHub();
		}
		return self::$mInstance;
	}

	/**
	 * getBreadCrumb
	 *
	 * get category structure using global function
	 *
	 * @access public
	 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
	 * @author Emil Podlaszewski <emil@wikia.com>
	 *
	 * @return array	array with categories
	 */
	public function getBreadCrumb( $city_id ) {

		wfProfileIn( __METHOD__ );

		$dbr = wfGetDB( DB_SLAVE );
		$cat_id = $dbr->selectField(
			wfSharedTable("city_cat_mapping"),
			"cat_id",
			array( "city_id" => $city_id )
		);

		$cats = array();
		while( !empty( $cat_id ) ) {
			$res = $dbr->select(
				array( wfSharedTable("city_cat_structure"), wfSharedTable("city_cats") ),
				array( "cat_name", "cat_url", "cat_parent_id" ),
				array( wfSharedTable("city_cat_structure").".cat_id=".wfSharedTable("city_cats").".cat_id", wfSharedTable("city_cat_structure").".cat_id=$cat_id" )
			);
			if( $row = $dbr->fetchObject( $res ) ) {
				$cats[] = array( "name" => $row->cat_name, "url" => $row->cat_url, "id" => intval( $cat_id ), "parentId" => intval( $row->cat_parent_id ) );
				$cat_id = $row->cat_parent_id;
			}
		}
		wfProfileOut( __METHOD__ );

		$cats = array_reverse( $cats );

		return $cats;
	}

    /**
     * return HTML select for category choosing
     */
    public function getForm( $city_id, &$title = null ) {
		global $wgTitle;
		if( is_null( $title ) ) {
			$title = $wgTitle;
		}
		$cat_id = $this->getCategoryId( $city_id );
        $oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
        $oTmpl->set_vars( array(
			"title"			=> $title,
			"cat_id"		=> $cat_id,
            "city_id"	 	=> $city_id,
            "categories" 	=> self::$mCategories
        ));

        return $oTmpl->execute("categories");
    }

    /**
     * return HTML select for category choosing
     */
    public function getSelect( $cat_id ) {

        $oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
        $oTmpl->set_vars( array(
			"title"			=> null,
			"cat_id"		=> $cat_id,
            "categories" 	=> self::$mCategories
        ));

        return $oTmpl->execute("categories");
    }

	/**
	 * getCategories
	 *
	 * get category identifier for given wiki
	 *
	 * @access public
	 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
	 *
	 * @return array	category names and ids from
	 */
    public function getCategories( ) {
		return self::$mCategories;
	}

	/**
	 * getCategoryId
	 *
	 * get category identifier for given wiki
	 *
	 * @access public
	 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
	 *
	 * @return integer	category id from city_cat_mapping table
	 */
    public function getCategoryId( $city_id ) {

        wfProfileIn( __METHOD__ );

        $dbr = wfGetDB( DB_SLAVE );

        $oRow = $dbr->selectRow(
            array( wfSharedTable("city_cat_mapping") ),
            array( "cat_id" ),
            array( "city_id" => $city_id ),
            __METHOD__
        );

        wfProfileOut( __METHOD__ );

        return isset( $oRow->cat_id ) ? $oRow->cat_id : null ;
    }

    /**
     * get category name
     */
    public function getCategoryName( $city_id ) {
		$cat_id = $this->getCategoryId( $city_id );
		return isset( self::$mCategories[ $cat_id ] )
			? self::$mCategories[ $cat_id ]
			: null;
    }
    /**
	 * loadCategories
	 *
     * load data from database: wikicities.city_cats
     *
     * @author Krzysztof Krzyżaniak <eloy@wikia.com>
     *
	 * @return array	array with category maps id => name
     */
    private function loadCategories() {
      global $wgSharedDB ;
	$tmp = array();


	if( !$wgSharedDB ) {
	  return array();
	}
        wfProfileIn( __METHOD__ );
        $dbr = wfGetDB( DB_SLAVE );

        $oRes = $dbr->select(
            array( wfSharedTable("city_cats") ),
            array( "*" ),
            null,
            __METHOD__,
            array( "ORDER BY" => "cat_name" )
        );

        while ( $oRow = $dbr->fetchObject( $oRes ) ) {
            $tmp[ $oRow->cat_id ] = $oRow->cat_name;
        }

        $dbr->freeResult( $oRes );

        wfProfileOut( __METHOD__ );

        return $tmp;
    }

	/**
	 * getIdByName
	 *
	 * get category id for hub using category name
	 *
	 * @access public
	 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
	 *
	 * @return integer	category id
	 */
    public function getIdByName( $name ) {

        wfProfileIn( __METHOD__ );

        $dbr = wfGetDB( DB_SLAVE );

        $oRow = $dbr->selectRow(
            array( wfSharedTable("city_cats") ),
            array( "cat_id" ),
            array( "cat_name" => htmlspecialchars( $name ) ),
            __METHOD__
        );

		$id = isset( $oRow->cat_id ) ? $oRow->cat_id : false;

        wfProfileOut( __METHOD__ );

        return $id;
    }

    /**
	 * setCategory
	 *
     * remove previous value in database and insert new one
     *
     * @param integer   $city_id    identifier from city_list
     * @param integer   $cat_id     category identifier
     */
    public function setCategory( $city_id, $cat_id ) {

        wfProfileIn( __METHOD__ );

        $dbw = wfGetDB( DB_MASTER );
        $dbw->begin();
        $dbw->delete( wfSharedTable("city_cat_mapping"), array( "city_id" => $city_id ), __METHOD__ );
        $dbw->insert( wfSharedTable("city_cat_mapping"), array( "city_id" => $city_id, "cat_id" => $cat_id ), __METHOD__  );

		$categories = $this->getCategories();
		WikiFactory::log( WikiFactory::LOG_CATEGORY, "Category changed to {$categories[$cat_id]}", $city_id );

        $dbw->commit();

        wfProfileOut( __METHOD__ );
    }
}
