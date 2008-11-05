<?php

/* register as a parser function {{BLOGTPL_TAG}} and a tag <BLOGTPL_TAG> */ 
$wgExtensionFunctions[] = array("BlogTemplateClass", "setup");
$wgHooks['LanguageGetMagic'][] = "BlogTemplateClass::setMagicWord";

define ("BLOGS_TIMESTAMP", "20081101000000");
define ("BLOGS_XML_REGEX", "/\<(.*?)\>(.*?)\<\/(.*?)\>/si");

class BlogTemplateClass {
	/*
	 * Tag options
	 */ 	
	private static $aBlogParams = array(
		/*
		 * <category>Cat11</category>
		 * <category>Cat12</category>
		 * ....
		 * 
		 * or 
		 * 
		 * <category>
		 * Cat11
		 * Cat12
		 * Cat13
		 * ....
		 * </category>
		 * 
		 * type: 	string 
		 * default: null (all categories)
		 */
		'category' 		=> array ( 
			'type' 		=> 'string',
			'default' 	=> null 
		),
		
        /*
		 * <author>Author1</author>
		 * <author>Author2</author>
		 * ....
		 * 
		 * or 
		 * 
		 * <author>
		 * Author1
		 * Author2
		 * Author3
		 * ....
		 * </author>
		 * 
		 * type: 	string 
         * default: "" (all authors)
         */
		'author' 		=> array ( 
			'type' 		=> 'string', 
			'default' 	=> '' 
		),
		
        /*
         * order = date (or title or author)
		 * 
		 * type: 	element of predefined list (date, title, author)
         * default: timestamp
         */
		'order' 		=> array ( 
			'type' 		=> 'list', 
			'default' 	=> 'date', 
			'pattern'	=> array(
				'date' 	=> 'page_touched', 
				'title' => 'page_title', 
				'author'=> 'rev_user_text'
			) 
		),
		
		/*
		 * ordertype = descending (or ascending)
		 * 
		 * type: 	predefined list (descending, ascending)
		 * default: descending
		 */
		'ordertype' 	=> array ( 
			'type' 		=> 'list', 
			'default' 	=> 'descending', 
			'pattern'	=> array('descending', 'ascending') 
		),
		
		/*
		 * max of results to display.
		 * count = /^\d*$/
		 * 
		 * type: 	number
		 * default: 5
		 */
		'count' 		=> array ( 
			'type' 		=> 'number', 
			'default' 	=> '5', 
			'pattern' 	=> '/^\d*$/'
		),
		
		/*
		 * number of results which shall be skipped before display starts.
		 * offset = /^\d*$/
		 * 
		 * type: 	number
		 * default: 0
		 */
		'offset' 		=> array (
			'default' 	=> '0', 
			'pattern' 	=> '/^\d*$/'
		),
		
		/*
		 * show date of blog creation
		 * showtimestamp = false (or true)
		 * 
		 * type: 	boolean,
		 * default: false
		 */
		'showtimestamp' => array (
			'type' 		=> 'boolean',
			'default' 	=> false
		),		

		/*
		 * show summary 
		 * showsummary = false (or true)
		 * 
		 * type: 	boolean,
		 * default: false
		 */
		'showsummary' 	=> array (
			'type' 		=> 'boolean',
			'default' 	=> false
		),

		/*
		 * number of characters in summary
		 * summarylength = /^\d*$/
		 * 
		 * type: 	number,
		 * default: 200
		 */
		'summarylength' 	=> array (
			'type' 		=> 'number',
			'default' 	=> '200', 
			'pattern' 	=> '/^\d*$/'
		)
	);

	private static $aTables		= array( );
	private static $aWhere 		= array( );
	private static $aOptions	= array( );
	
	public static function setup() {
		global $wgParser, $wgMessageCache;
		wfProfileIn( __METHOD__ );
		
		// variant as a parser tag: <BLOGTPL_TAG>
		$wgParser->setHook( BLOGTPL_TAG, array( __CLASS__, "parseTag" ) );
		// variant as a parser function: {{#BLOGTPL_TAG}}
		$wgParser->setFunctionHook( BLOGTPL_TAG, array( __CLASS__, "parseTagFunction" ) );
		
		require_once( "BlogArticle.i18n.php" );
		foreach( $wgBlogArticleMessages as $sLang => $aMsgs ) {
			$wgMessageCache->addMessages( $aMsgs, $sLang );
		}
		wfProfileOut( __METHOD__ );
	}
	
	public static function setMagicWord( &$magicWords, $langCode ) {
		wfProfileIn( __METHOD__ );
		/* add the magic word */
		$magicWords[BLOGTPL_TAG] = array( 0, BLOGTPL_TAG );
		wfProfileOut( __METHOD__ );
		return true;
	}
    
	public static function parseTag( $input, $params, &$parser ) {
		wfProfileIn( __METHOD__ );
		error_log ("parseTag: input : ".$input."\n", 3, "/tmp/moli.log");
		error_log ("parseTag: params : ".print_r($params, true)."\n", 3, "/tmp/moli.log");
		/* parse input parameters */
		$matches = array();
		$aParams = self::__parseXMLTag($input);
		wfDebugLog( __METHOD__, "parse input parameters\n" );
		/* parse all and return result */
		$res = self::__parse($aParams, $params, $parser);
		wfProfileOut( __METHOD__ );
		return "parseTag";
	}
	
	public static function parseTagFunction(&$parser) {
		wfProfileIn( __METHOD__ );
		error_log ("parseTagFunction: parser : ".print_r($parser, true)."\n", 3, "/tmp/moli.log");
		wfProfileOut( __METHOD__ );
		return "parseTagFunction";
	}

	/*
	 * private method 
	 */
	
	private static function __parseXMLTag($string) { 
		wfProfileIn( __METHOD__ );
		$aResult = array();
		$aRes = $aTags = array();
		if (preg_match_all(BLOGS_XML_REGEX, $string, $aTags)) { 
			list (, $sStartTags, $sTexts, $sEndTags) = $aTags;
			wfDebugLog( __METHOD__, "found ".count($sStartTags)." tags\n" );
			foreach ($sStartTags as $id => $sStartTag) {
				/* allow this tag? */
				if ( in_array($sStartTag, array_keys(self::$aBlogParams)) ) {
					/* <TAG> = </TAG> */
					$sStartTag = trim($sStartTag);
					$sEndTags[$id] = trim($sEndTags[$id]);
					if ($sStartTag == $sEndTags[$id]) {
						$aRes[$sStartTag][] = trim($sTexts[$id]);
					}
				}
			}
			wfDebugLog( __METHOD__, "allowed tags : ".count($aRes)."\n" );
			if ( !empty($aRes) )  {
				$string = "";
				foreach ($aRes as $sParamName => $aParamValues) {
					if ( !empty($aParamValues) ) {
						foreach ($aParamValues as $id => $sParamValue) {
							if ( strpos( $sParamValue, "\n" ) !== FALSE ) {
								$aResult[$sParamName] = array_merge( (array)$aResult[$sParamName], array_map( 'trim', explode( "\n", $sParamValue) ) );
							} else {
								$aResult[$sParamName][] = $sParamValue;
							}
						}
					}
				}
			}
		}
		wfProfileOut( __METHOD__ );
		return $aResult; 
	}

	
	private static function __setDefault() {
		/* set default options */
		/* default tables */
		if ( !in_array( "page", self::$aTables) ) {
			self::$aTables[] = "page";
		} 
		/* default conditions */
		if ( !in_array("page_namespace", array_keys( self::$aWhere )) ) {
			self::$aWhere["page_namespace"] = NS_BLOG_ARTICLE;
		}
		if ( BLOGS_TIMESTAMP ) {
			self::$aWhere[] = "page_timestamp >= '".BLOGS_TIMESTAMP."'";
		}
		/* default options */
		if ( !isset(self::$aOptions['ORDER BY']) ) {
			self::$aOptions['ORDER BY'] = self::$aBlogParams['order']['pattern'][self::$aBlogParams['order']['default']];
		}
		if ( !isset(self::$aOptions['SORT']) ) {
			self::$aOptions['SORT'] = self::$aBlogParams['ordertype']['default'];
		}
		if ( !isset(self::$aOptions['LIMIT']) ) {
			self::$aOptions['LIMIT'] = self::$aBlogParams['count']['default'];
		}
		if ( !isset(self::$aOptions['OFFSET']) ) {
			self::$aOptions['OFFSET'] = self::$aBlogParams['offset']['default'];
		}
	}
	
    private static function __parse( $aInput, $params, &$parser ) {
    	wfProfileIn( __METHOD__ );
    	$result = "";

		/* default settings for query */
    	self::__setDefault();
        try {
			/* database connect */
			$dbr = wfGetDB( DB_SLAVE, 'dpl' );
			/* parse input parameters */
			wfDebugLog( __METHOD__, "parse ".count($aInput)." parameters\n" );
			error_log ("aInput: ".print_r($aInput, true)."\n", 3, "/tmp/moli.log");
			foreach ($aInput as $sParamName => $aParamValues) {
				/* ignore empty lines */
				if ( empty($aParamValues) ) {
					wfDebugLog( __METHOD__, "ignore empty param: ".$sKey." \n" );
					continue;
				}
				/* invalid name of parameter or empty name */
				if ( !in_array($sParamName, array_keys(self::$aBlogParams)) ) {
					throw new Exception(wfMsg('blog_invalidparam', $sParamName, array_keys(self::$aBlogParams)));
				} elseif ( trim($sParamName) == '' ) {
					throw new Exception(wfMsg('blog_emptyparam'));						
				} 

				/* ignore comment lines */
				if ($sParamName[0] == '#') { 
					wfDebugLog( __METHOD__, "ignore comment line: ".$iKey." \n" );
					continue;
				}
			
				/* parse value of parameter */
				switch ($sParamName) {
					case 'category'		: 
						if ( !empty($aParamValues) ) {
							self::$aTables[] = 'categorylinks';
							self::$aWhere[] = "cl_to in (" . $dbr->makeList( $aParamValues ) . ")";
							error_log ( "category: " . print_r($aParamValues, true) . "\n", 3, "/tmp/moli.log" );
						}
						break;
					case 'author'		: 
						if ( !empty($aParamValues) ) {
							$sRevisionTable = 'revision';
							if ( !in_array($aTables, $sRevisionTable) ) {
								self::$aTables[] = $sRevisionTable;
							}
							self::$aWhere[] = "rev_user_text in (" . $dbr->makeList( $aParamValues ) . ")";
							error_log ( "author: " . print_r($aParamValues, true) . "\n", 3, "/tmp/moli.log" );
						}
						break;
					case 'order'		: 
						if ( !empty($aParamValues) ) {
							if ( in_array( $aParamValues, self::$aBlogParams[$sParamName]['pattern'] ) ) {
								$_aTmp = array();
								foreach ($aParamValues as $id => $sParamValue) {
									switch ($sParamValue) { 
										case 'title'	: 
											self::$aOptions['ORDER BY'] = 'page_title';
											break;
										case 'author'	: 
											$sRevisionTable = 'revision';
											if ( !in_array(self::$aTables, $sRevisionTable) ) {
												self::$aTables[] = $sRevisionTable;
											}
											self::$aOptions['ORDER BY'] = 'rev_user_text';
											break;
										default 		: /* date */
											self::$aOptions['ORDER BY'] = 'page_touched';
											break;
									}
								}
							}
						} 
						break;
					case 'ordertype'	: 
						break;
					case 'count'		: break;
					case 'offset'		: break;
					case 'showtimestamp': break;
					case 'showsummary'	: break;
					case 'summarylength': break;
				}
				
				error_log ("tables: " . print_r(self::$aTables, true) . "\n", 3, "/tmp/moli.log");
				error_log ("where: " . print_r(self::$aWhere, true) . "\n", 3, "/tmp/moli.log");
				error_log ("options: " . print_r(self::$aOptions, true) . "\n", 3, "/tmp/moli.log" );
			}
        }
		catch (Exception $e) {
			wfDebugLog( __METHOD__, "parse error: ".$e->getMessage()."\n" );
			wfProfileOut( __METHOD__ );
			return $e->getMessage();
		}
    	
    	wfProfileOut( __METHOD__ );
    	return $result;
	}	

}
