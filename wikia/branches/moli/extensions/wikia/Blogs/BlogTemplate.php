<?php

/* register as a parser function {{BLOGTPL_TAG}} and a tag <BLOGTPL_TAG> */ 
$wgExtensionFunctions[] = array("BlogTemplateClass", "setup");
$wgHooks['LanguageGetMagic'][] = "BlogTemplateClass::setMagicWord";

class BlogTemplateClass {
	/*
	 * Tag options
	 */ 	
	public static $aOptions = array(
		/*
		 * category= Cat11 | Cat12 | ...
		 * category= Cat21 | Cat22 | ...
		 * 
		 * type: 	string 
		 * default: null (all categories)
		 */
		'category' 		=> array ( 
			'type' 		=> 'string', 
			'default' 	=> null 
		),
		
        /*
         * author = Author1 | Author 2 | ....
		 * 
		 * type: 	string 
         * default: "" (all authors)
         */
		'author' 		=> array ( 
			'type' 		=> 'string', 
			'default' 	=> '' 
		),
		
        /*
         * order = timestamp (or title or author)
		 * 
		 * type: 	element of predefined list (timestamp, title, author)
         * default: timestamp
         */
		'order' 		=> array ( 
			'type' 		=> 'list', 
			'default' 	=> 'timestamp', 
			'patern'	=> array('timestamp', 'title', 'author') 
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
			'patern'	=> array('descending', 'ascending') 
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
		wfDebugLog( __METHOD__, "parse input parameters: ".$input."\n" );
		$aParams = self::__clearInput($input);
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
    private static function __parse( $aInput, $params, &$parser ) {
    	wfProfileIn( __METHOD__ );
    	$result = "";
        try {
			if ( is_null($aInput) ) {
				throw new Exception(wfMsg('blog_invalidparams'));
			}
			/* parse input parameters */
			wfDebugLog( __METHOD__, "parse ".count($aInput)." parameters\n" );
			foreach ($aInput as $iParam => $sParam) {
				$aParam = explode( '=', $sParam, 2 );
				if ( count( $aParam ) < 2 ) {
					if ( trim( $aParam[0] ) != '' ) {
						throw new Exception(wfMsg('blog_useoneparam', array_keys(self::$aOptions)));
					} else {
						throw new Exception(wfMsg('blog_invalidparam', array_keys(self::$aOptions)));
					}
				}
				$sType = trim($aParam[0]);
				$sArg = trim($aParam[1]);
				
				// to be continued 
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
	
	private static function __clearInput ($input) {
		wfProfileIn( __METHOD__ );
		/* the symbol is utf8-escaped */
		$input = str_replace('Â»','>',$input);
		$input = str_replace('Â«','<',$input);
		
		/* use the ¦ as a general alias for | */
		$input = str_replace('Â¦','|',$input); 
		
		/* split */
		$aParams = explode("\n", $input);
		
		wfProfileOut( __METHOD__ );
		return $aParams;
	}
	
}
