<?php
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install this extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/wikia/Wikireader/SpecialWikireadr.php" );
EOT;
        exit( 1 );
}

require_once(dirname(__FILE__) . '/classes/wikireadr.php');

class Wikireadr extends SpecialPage {
	/**
	 * Constructor
	 */
	function Wikireadr() {
		SpecialPage::SpecialPage( 'Wikireadr','wikireadr',false );
		$this->includable( true );
	}

	/**
	 * main()
	 */
	function execute( $par = null ) {
		global $wgUser, $wgOut, $wgRequest;
		
		if( $wgUser->isBlocked() ) {
            $wgOut->blockedPage();
            return;
        }

        if( wfReadOnly() ) {
            $wgOut->readOnlyPage();
            return;
        }
       
        if( !$wgUser->isAllowed( 'wikireadr' ) ) {
            $this->displayRestrictionError();
            return;
        }
		
		//this is action controller 
		
		$wgOut->setPageTitle( wfMsg('wikireadr') );
		
	    $wgOut->setRobotpolicy( 'noindex,nofollow' );
		
	    $oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		
		if( empty( $_REQUEST['action'] ) ){
			$a = '';
		}else{
			$a = $_REQUEST['action'];
		}
		switch ($a){
		 
		 case "preview":
		    $out = array( 'initurl' => $_REQUEST['initurl'], 'err' => '' );
		    
		    if(!$r = $this->getPreviews($out)){
			
				//errors encountered
				
				$out['err'] = wfMsg( 'ws.status.badurl' );
				
			  	$oTmpl->set_vars( array(
	               "data" => $out               
	            ));
	            
	            $wgOut->addHTML( $oTmpl->execute("init") );
			    break;	
							
			}else{
			   //got pages back list em 
			 		  
			$out['preview'] = $r;
			   
			$oTmpl->set_vars( array(
               "data"  => $out               
            ));
             $wgOut->addHTML( $oTmpl->execute("preview") );
			}
			
		  	break; 	
		 
		 default:
		  	$out = array( 'err' => '', 'initurl' => '' );
			
		  	$oTmpl->set_vars( array(
               "data" => $out               
            ));
            
            $wgOut->addHTML( $oTmpl->execute("init") );
		    break;	
		} 
	}
	
	function WikiReadrGetArticle($title, $ns) {
		$res = "";
		$titleObj = Title::newFromText($title, $ns);
		if ( !is_object($titleObj) ) {
			return $res;
		}
		$revision = Revision::newFromTitle( $titleObj );
		if(is_object($revision)) {
		  if($revision->getText()!=''){	
			return $title;
		  }else{
		  	return $res;
		  }	
		}
		return $res;
	}
	
	function getPreviews($params){
		global $wgTitle;
		
		$pages = array();
		//this method retrieves all apges linked to current one.
		$url = parse_url($params['initurl']);
		$prefix = '/wiki/';
		if(is_array($url)){
		
		 $sd = explode('.',$url['host']);
		   if($sd[count($sd)-2] . '.' . $sd[count($sd)-1] != 'wikipedia.org'){
		 	 return false;
		   }
		}else{
		  //bad url
		  return false;	
		}
		
		$export_url = $url['scheme'].'://'.$url['host'].'/wiki/Special:Export/';
		$api = $url['scheme'].'://'.$url['host'].'/w/';
		$base = $url['scheme'].'://'.$url['host'].'/wiki/';
		
		$p = urldecode(substr_replace($url['path'],'',0,strlen($prefix)));
		$page = array('page' => $p);
		$page['source'] = $export_url.$p;
		$page['pageurl'] = $url['scheme'].'://'.$url['host'].'/wiki/' . $p;
		$page['pageexist'] = $this->WikiReadrGetArticle( trim( $p ), NS_MAIN ); 
		$pages[] = $page;
		
		//parse and collect links from this page up to the limit in rownd robin
		$seed = new clsWikiReadr();
		$res = $seed->get_page(array('url' => $export_url.$p, 'media' => 'false','wslinks' => array(), 'remred' => false,'api' => $api,'base' => $base) ); 
		
		if(count($res['pages'])==0){
		//try if redirection
		  $redirect = $seed->my_preg_match("'#REDIRECT \[\\[(.*?)\]\\]'si",$res['content']);
  		  if($redirect!=''){
	 	   $res = $seed->get_page(array('url' => $export_url.$redirect, 'media' => 'false','wslinks' => array(), 'remred' => false,'api' => $api,'base' => $base) ); 
		  }
		}
		
		if ( !is_object($wgTitle) ) {
		  $wgTitle = new Title();
		}
    	
		foreach($res['pages'] as $key => $value ){
			
			$page = array('page' => $value);
			$page['source'] = $export_url.$value;
			$page['pageurl'] = $url['scheme'].'://'.$url['host'].'/wiki/' . $value;
			$page['pageexist'] = $this->WikiReadrGetArticle( trim( $value ), NS_MAIN ); 
			$pages[] = $page;
		}
	
		return $pages;
	}	
	
}//class end