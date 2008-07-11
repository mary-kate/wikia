<?php
require_once(dirname(__FILE__) . '/httpclient.php');

class clsWikiReadr {
	
  var $http_client;	
  
  function clsWikiReadr(){
	$this->http_client = new wikia_http( HTTP_V10, false);
  }
  
  function get_page($params){

      $xml = $this->get_url_content( $params['url'] ,'GET' ,'' , $params['url']);
	  $redirect = $this->my_preg_match("'#REDIRECT \[\\[(.*?)\]\\]'si",$xml);
 
  	  if($redirect!=''){
	    $xml = $this->get_url_content( $params['base'].'Special:Export/'.$redirect ,'GET' ,'' , $params['url']); 
	  }
	  
	  $title = $this->my_preg_match("'<title>(.*?)</title>'si", $xml);
	  
	  if($title==''){
	   if($params['ismedia']){
	    //could be in commons
	    $commurl = str_replace('en.wikipedia','commons.wikimedia',$params['url']);
	   	$xml = $this->get_url_content( $commurl ,'GET' ,'' , $commurl );

	   	$title = $this->my_preg_match("'<title>(.*?)</title>'si", $xml);
		
		if($title == ''){
	     $res = array('content' => '','pages' => array(),'images' => array(), 'imagekey' => '', 'talkcontent' => '', 'title' => ''); 
	      return $res ;		
	   	}
	   	
	   }else{
	    $res = array('content' => '','pages' => array(),'images' => array(), 'imagekey' => '', 'talkcontent' => '', 'title' => ''); 
	    return $res ;
	   }
	  }	
	
	    $lic = '';
		  
	  if(!empty($params['ismedia'])){ // license info required
	
	    $lic = $this->get_lic($xml);
	  	if($lic == '' ) {
	  		return array('content' => '','pages' => array(),'images' => array(), 'imagekey' => '', 'talkcontent' => '', 'title' => '', 'nolicence' => '1');
	  	}
	  }
	
	if( ( $params['remred'] !== false ) && ( $params['ismedia'] !== true ) ){
		
	  //clean all template data we dont use it
      $xml = preg_replace("'{{{(.*?)}}}'si",'',$xml);
      preg_match_all("/\{\{(?:[^{}]+|(?R))*\}\}/",$xml,$matches);
      
      if(isset($matches[0])&&(count($matches[0]>0))){
       $rm = array();
        //find the longest templates, so when removing nested will not break
        foreach($matches[0] as $k=>$v){
          $rm[$k]=strlen($v);
        } 	
      	
      	arsort($rm);
      	
        foreach($rm as $k => $v){
         if(isset($matches[$k])){
       	   $xml = str_replace($matches[$k],"",$xml);
         }
        }
      }
	 
	 }
	
  	  $xml =  preg_replace("'\[\\[Category:(.*?)\]\\]'si",'',$xml);	
  	   
      //find local namespaces
  	  $image = $this->my_preg_match("'<namespace key=\"6\">(.*?)</namespace>'si", $xml);
  	  $talk = $this->my_preg_match("'<namespace key=\"1\">(.*?)</namespace>'si", $xml);
  	   
  	  $pages = array();
  	  $images = array();
  	  $langlinks = array();
  	  $categories = array();
  	  $extlinks = array();

      $url = $params['api'].'api.php?action=query&prop=revisions|links|images|langlinks|extlinks&format=php&pllimit=500&rvprop=user&rvlimit=1&redirects&titles='.urlencode(str_replace(' ','_',$title));
      $res = unserialize($this->get_url_content( $url ,'GET' ,'' , $url));
      
      $url = $params['api'].'api.php?action=query&prop=revisions&format=php&rvprop=user&rvlimit=500&redirects&titles='.urlencode(str_replace(' ','_',$title));
      $usr = unserialize($this->get_url_content( $url ,'GET' ,'' , $url));
      $userlist = array();
        
       foreach($usr['query']['pages'] as $key => $value){
   	    if(empty($value['links'])){
	      $value['links'] = array();	
	    }
	   	  	
	    foreach($value['revisions'] as $zkey => $zvalue){
	     if( ( !$this->IsValidIp( $zvalue['user'] ) ) && ( strpos( $zvalue['user'], 'Bot' ) == false) && ( trim($zvalue['user']) !='' ) )	{
	      $userlist[] = $zvalue['user'];
	     } 
	    }
      }  
	  
	  $userlist = array_unique($userlist);
	  
	  $xml =  preg_replace("'<contributor>(.*?)</contributor>'si",'<contributor>Wikireadr</contributor>',$xml);
	  
      foreach($res['query']['pages'] as $key => $value){
   	  
	   	  if(empty($value['links'])){
	   	   $value['links'] = array();	
	   	  }
	   	  	
	      foreach($value['links'] as $zkey => $zvalue){
	  	    if($zvalue['ns']==0){	
	  	      $pages[] = $zvalue['title'];
	  	    }   
	  	   }
	  	   
	  	   if(empty($value['images'])){
	   	    $value['images'] = array();	
	   	   }
	   	  
	  	   foreach($value['images'] as $zkey => $zvalue){
	 	 	if($zvalue['ns']==6){	
	  	      $images[] = $zvalue['title'];
	  	    } 
	  	   }
	  	   
	  	   if(empty($value['langlinks'])){
	   	    $value['langlinks'] = array();	
	   	   }
	   	  
	  	   foreach($value['langlinks'] as $zkey => $zvalue){
		 	  $langlinks[] = '[['.$zvalue['lang'].':'.$zvalue['*'].']]';
	  	   }
	  	   
	  	   if(empty($value['extlinks'])){
	   	    $value['extlinks'] = array();	
	   	   }
	   	  
	  	   foreach($value['extlinks'] as $zkey => $zvalue){
	  	     $extlinks[] = $zvalue['*'];
	  	   }
  	  }
  	  
  	  //delete lang links we dont use em
  	  $xml = str_replace($langlinks,array(""),$xml);
  	  //delete templates we dont use them
  	  
  	   //remove if needed and items are not in escape list and require removal
  	  foreach($pages as $key => $value){
  	  
  	    if( ($params['remred'] == true)&&( !in_array(str_replace('_',' ',ucfirst(trim($value['title']))), $params['wslinks'] ) ) ) {
  	   	  
  	   	  $link = $this->my_preg_match("'\[\\[".$value."(.*?)\]\\]'si",$xml);
  	   	  
  	   	  if($link==''){
  	   	  	//just replace dropping brackets
  	   	  	$xml = preg_replace("'\[\\[".$this->esc($value)."(.*?)\]\\]'si",$value,$xml);
  	   	  }else{
  	   	    //find tags	
			$linkmeta = explode('|',$link);
			if(is_array($linkmeta)&&(!empty($linkmeta[1]))){
			  $xml = preg_replace("'\[\\[".$this->esc($value)."(.*?)\]\\]'si",$linkmeta[1],$xml);
			}else{
			  $xml = preg_replace("'\[\\[".$this->esc($value)."(.*?)\]\\]'si",$value,$xml);
			}  	   	  
  	   	  }
  	   	}
  	   }
  
  	 
	  	$xml = preg_replace("'{{(.*?)}}'si",'',$xml);
	  	 
	  	$cnt=0;
	  	$xml =  preg_replace("'<comment>(.*?)</comment>'si",'<comment>This page uses content from Wikipedia. The original article was/is at [[Wikipedia:' . $title . ']].  The following Wikipedia users contributed to this page: ' . implode( ',', $userlist ) . '</comment>',$xml,-1,$cnt);
		  
		if($cnt==0){
			$xml = str_replace("<contributor>Wikireadr</contributor>","<contributor>Wikireadr</contributor>\n<comment>This page uses content from Wikipedia. The original article was/is at [[Wikipedia:$title]]. The following users contributed to this page: " . implode( ', ', $userlist ) . "</comment>",$xml);	  	
		}
		
  	 if( ( !empty( $params['ismedia']) ) && ( $params['ismedia'] === true ) ){
	    //put additional attribution flag
	    $ul = '';
	    
	    if(count($userlist)>0){
	     $ul = " The following users contributed to this page: " . implode( ', ', $userlist ); 	
	    }
  	    
  	    $xml = str_replace('<text xml:space="preserve">','<text xml:space="preserve">'."\n== Attribution: == \nThis page uses content from Wikipedia. The original content was/is at [[Wikipedia:$title]]." . $lic . "\n" .  $ul  . "\n",$xml);	  	
 	 }  	 

	  		
	  $xml = str_replace("&lt;ref&gt;&lt;/ref&gt;","",$xml);
  	  //clean all template data we dont use it
      preg_match_all("'&lt;ref(.*?)&lt;/ref&gt;'si",$xml,$m);
      
      if(isset($m[0])&&(count($m[0]>0))){
        $xml = str_replace($m[0],array(""),$xml);
      }
  	  
  	  $upages = array_unique($pages);
  	  $uimages = array_unique($images);
  	  $talkcontent = $xml;
	 
	  if(empty($params['ismedia'])){
	   $xml = str_replace('</text>','{{wikipedia|'.$title.'}}'.$lic.'</text>',$xml);
	  }
	  	
  	  $talkcontent =  preg_replace("'<title>(.*?)</title>'si",'<title>'.$talk.':'.$title.'</title>',$talkcontent); 	
  	  $talkcontent =  preg_replace("'<revision>(.*?)</revision>'si",'<revision><text xml:space="preserve">{{wikipedia|'.$title.'}}</text></revision>',$talkcontent);
 	  $res = array('content' => $xml,'pages' => $upages,'images' => $uimages, 'imagekey' => urlencode($image), 'talkcontent' => $talkcontent, 'title' => $title );  
	  
	  return $res ;
  }
  
  function get_image($params){
    global $wgRequest;	
    
  	$rs = $this->get_url_content( $params['url'] ,'GET' ,'' , $params['url']);
    $rimage = $this->my_preg_match("'<a href=\"http://(.*?)".urlencode($params['imagename'])."\">'si", $rs);

   	if( $rimage ){
  	 $image = "http://".$rimage.urlencode($params['imagename']) ;
   	 
  	 $wgRequest->data['wpUploadFileURL'] = $image;
  	 $wgRequest->data['wpIgnoreWarning'] = 'true';
  	 $wgRequest->data['wpSourceType'] = 'web';
  	 $wgRequest->data['action'] = 'submit';
  	 $wgRequest->data['wpDestFile'] = $params['imagename'];
  	 $_SERVER['REQUEST_METHOD'] = 'POST';
  	 $f = new UploadForm($wgRequest);
	 
	 $f->execute();
  	}
   return;
  }
  
  function get_url_content( $_url, $method='GET', $formdata = array() , $referer='', $headers = false ) {
  //fake browser fake	
    
    $url = parse_url( $_url );
    
	$this->http_client->host = $url['host'];
	    
	if( !empty($url['query']) ){
	    $query = $url['path'] . '?' . $url['query']; 
	  }else{
	    $query = $url['path'];
	}
	
	$query = str_replace(' ','_',$query);

	//do get
	if( $method == 'GET' ){
	    
		if ( $this->http_client->get( $query, true, $referer ) == HTTP_STATUS_OK ){
		    if( $headers ){
			  $rs = $this->http_client->get_response();	
		      return $rs;	
		    }else{
		      $rs = $this->http_client->get_response_body();
			  return $rs;	
		    } 
		  }else{
		    return false;
		}
	}
	
	if( $method == 'POST' ){
	  if ( $this->http_client->post( $query, $formdata ) == HTTP_STATUS_OK ){
		$rs = $this->http_client->get_response_body();
		    if($headers){
			  $rs = $this->http_client->get_response();	
		      return $rs;	
		    }else{
		      $rs = $this->http_client->get_response_body();
			  return $rs;	
		    } 
	  }else{
	    return false;
	  }
	}
  }	
  
  function my_preg_match ( $pattern, $subject ) {
   preg_match( $pattern, $subject, $out );
	if( isset( $out[1] ) ) {
		return trim( $out[1] );
	} else {
		return '';
	}
  }
  
  function utf_prepare(&$array) {

    foreach($array AS $key => &$value) {

      if (is_array($value)) {
        $this->utf_prepare($value);
      } else {
        $value = utf8_encode($value);
      }

    }

  }




function esc($str){
 $raw = array( "'", '"' );	
 
 $esc = array( "\'", '\"');
 return str_replace($raw,$esc,quotemeta($str));
}  
  
function html_to_utf8 ($data)
    {
    return preg_replace("/\\&\\#([0-9]{3,10})\\;/e", '$this->_html_to_utf8("\\1")', $data);
    }

function _html_to_utf8 ($data)
    {
    if ($data > 127)
        {
        $i = 5;
        while (($i--) > 0)
            {
            if ($data != ($a = $data % ($p = pow(64, $i))))
                {
                $ret = chr(base_convert(str_pad(str_repeat(1, $i + 1), 8, "0"), 2, 10) + (($data - $a) / $p));
                for ($i; $i > 0; $i--)
                    $ret .= chr(128 + ((($data % pow(64, $i)) - ($data % ($p = pow(64, $i - 1)))) / $p));
                break;
                }
            }
        }
        else
        $ret = "&#$data;";
    return $ret;
    }

function get_lic($str){

	$lic = array(
	"{{Copyrighted free use}}",
	"{{self",
	"{{BSD}}",
	"{{BSDu",
	"{{cc-by",
	"{{ABr}}",
	"{{cc-sa}}",
	"{{FAL}}", 
	"{{GFDL",
	"{{wikipedia-screenshot}}",
	"{{GPL}}",
	"{{GPL-2}}",
	"{{LGPL}}",
	"{{Affero}}",
	"{{MPL}}",
	"{{MTL}}", 
	"{{OldOS}}",
	"{{PD-user",
	"{{PD-self}}",
	"{{PD-flag",
	"{{PD-font}}",
	"{{PD-ineligible}}",
	"{{PD-old}}",
	"{{PD-US}}",
	"{{PD-US-1923-abroad}}",
	"{{PD-US-1996}}",
	"{{PD-old-50}}",
	"{{PD-old-70}}",
	"{{PD-stamp}}",
	"{{PD-Pre1978}}",
	"{{PD-Pre1964}}",
	"{{PD-EU-no author disclosure}}",
	"{{money-US}}",
	"{{PD-art-US}}",
	"{{PD-US-flag}}",
	"{{PD-US}}",
	"{{PD-Pre1978}}",
	"{{PD-US-patent}}",
	"{{PD-USGov}}",
	"{{PD-USGov-AID}}",
	"{{PD-USGov-Atlas}}",
	"{{PD-USGov-CIA}}",
	"{{PD-USGov-CIA-WF}}",
	"{{PD-USGov-Congress}}",
	"{{PD-USGov-Congress-Bio}}",
	"{{PD-USGov-DHS}}",
	"{{PD-USGov-DHS-CG}}",
	"{{PD-USGov-DOC}}",
	"{{PD-USGov-DOC-Census}}",
	"{{PD-USGov-DOC-NOAA}}",
	"{{PD-USGov-Military}}",
	"{{PD-USGov-DOE}}",
	"{{PD-USGov-DOJ}}",
	"{{PD-USGov-DOT}}",
	"{{PD-USGov-DOT-FAA}}",
	"{{PD-USGov-DOL}}",
	"{{PD-USGov-Education}}",
	"{{PD-USGov-EPA}}",
	"{{PD-USGov-FBI}}",
	"{{PD-USGov-FDA}}",
	"{{PD-USGov-FEMA}}",
	"{{PD-USGov-HHS}}",
	"{{PD-USGov-HHS-CDC}}",
	"{{PD-USGov-HHS-NIH}}",
	"{{PD-USGov-NCBI-scienceprimer}}",
	"{{PD-USGov-Interior}}",
	"{{PD-USGov-Interior-BLM}}",
	"{{PD-USGov-Interior-FWS}}",
	"{{PD-USGov-Interior-HABS}}",
	"{{PD-USGov-Interior-NPS}}",
	"{{PD-USGov-Interior-USGS}}",
	"{{PD-USGov-Interior-USGS-Minerals}}",
	"{{PD-USGov-Interior-USBR}}",
	"{{LOC-image}}",
	"{{NARA-image}}",
	"{{PD-USGov-NASA}}",
	"{{PD-WorldWind}}",
	"{{PD-USGov-NRO}}",
	"{{PD-USGov-NSA}}",
	"{{PD-USGov-NTSB}}",
	"{{PD-USGov-OWI}}",
	"{{PD-USGov-POTUS}}",
	"{{PD-USGov-State}}",
	"{{PD-USGov-Treasury}}",
	"{{PD-USGov-VA}}",
	"{{PD-USGov-USDA}}",
	"{{PD-USGov-USDA-ARS}}",
	"{{PD-USGov-USDA-FS}}",
	"{{PD-USGov-USDA-NRCS}}",
	"{{PD-USGov-TVA}}",
	"{{PD-USGov-Military}}",
	"{{PD-USGov-Military-DVIC}}",
	"{{PD-USGov-Military-Air Force}}",
	"{{PD-USGov-Military-Air Force-Aux}}",
	"{{PD-USGov-Military-Army}}",
	"{{PD-USGov-Military-Army-USACMH}}",
	"{{PD-USGov-Military-Army-USAIOH}}",
	"{{PD-USGov-Military-Army-USAMHI}}",
	"{{PD-USGov-Military-Army-USACE}}",
	"{{PD-USGov-Military-Award}}",
	"{{PD-USGov-Military-Badge}}",
	"{{PD-USGov-Military-Marines}}",
	"{{PD-USGov-Military-Navy}}",
	"{{PD-USGov-Military-JCS}}"
	);
	
	foreach ($lic as $key => $val){
		if(stripos($str, $val)!==false){
			$val = str_replace('}}','',$val) . '}}';
			return $val;	
		}
	}
	
	return '';
}

function IsValidIp($str){
  $parts = explode('.',$str);
  
  if(count($parts) < 4){
  	return false;
  }else{
  	foreach($parts as $ip_parts)
    {
      if(intval($ip_parts)>255 || intval($ip_parts)<0)
      return false; //if number is not within range of 0-255
    }
    return true;
  }
  	
}    

}//class end

?>
