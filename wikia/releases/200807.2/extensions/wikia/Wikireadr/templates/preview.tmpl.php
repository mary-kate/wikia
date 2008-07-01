<!-- s:<?= __FILE__ ?> -->
<style type="text/css">
/*<![CDATA[*/
.btn-action { width:80px; }
#pages { width:100%; cellpadding:10px;border-spacing:0px;}
#pages TD { border-bottom: 1px solid lightgray; height:25px;}
#wscheckall {font-size:smaller;} 
.wsmore {font-size:smaller;}
.ok {color:darkgreen;}
.notok {color:darkred;}
.error wikireadr {color:red; font-weight:bold;}
.hide {display:none;}
.show {display:;}
#wsoverview {background:lightgray; height:25px;}
#wsoverview TD { border: 1px solid lightgray !important; height:25px;}
/*]]>*/
</style>

<script type="text/javascript">
/*<![CDATA[*/
    
    function makestart(url,id){
    	var div = document.getElementById('wsdone_'+id); 
		div.innerHTML = '<img src="http://images.wikia.com/common/progress_bar.gif" width="80" height="20" border="0" />';
    	document.getElementById('initurl').value = url;
    	document.wikireadr.submit();
    }  
		
	function checkall(st){
		if(st){
		 for (i=1;i<=<?= count($data['preview']) ?>;i++)
		 {	
		  if(x = document.getElementById('wschk_'+i)){
		   if(x.checked){
		     document.getElementById('wschk_'+i).checked = false;
		   } 	
		  }
		 }	
		
		 document.getElementById('wscheckall').innerHTML = '(<a href="javascript:checkall(false);"><?= wfMsg( 'ws.status.includeall' ) ?></a>)';	
		}else{
		 
		 for (i=1;i<=<?= count($data['preview']) ?>;i++)
		 {	
		  if(x = document.getElementById('wschk_'+i)){
		   if(!x.checked){
		     document.getElementById('wschk_'+i).checked = true;
		   } 	
		  }
		 }	
	     document.getElementById('wscheckall').innerHTML = '(<a href="javascript:checkall(true);"><?= wfMsg( 'ws.status.excludeall' ) ?></a>)';	  	
		}
	}

	function goSeed(){
		t=0;
		er=0;
		pe=0;
		
		for (i=1;i<=<?= count($data['preview']) ?>;i++){	
		   if(x = document.getElementById('wschk_'+i)){
		    if((x.checked)&&(document.getElementById('wsdst_'+i).value!='')){
		     pe = pe + parseInt(document.getElementById('pe_'+i).value);
		    } 	
		   }
		  }
		
		p = true;
		
		if(pe > 0){
		  if(confirm("<?= wfMsg( 'ws.overwrite.confirm' ) ?>")){
			p=true;
		  }else{
			p=false;;
		  }		
		}
		
	if(p){
		
		var lk = '';
	    var div = document.getElementById('lbl_wsaction'); 
		div.innerHTML = '<img src="http://images.wikia.com/common/progress_bar.gif" width="80" height="20" border="0" />';
		YAHOO.util.Dom.setStyle('lbl_wsaction', 'display', '');
		YAHOO.util.Dom.setStyle('btn_wsaction', 'display', 'none');	
	
		if(rr = document.getElementById('remred')){
		 if(rr.checked){
		  for (i=1;i<=<?= count($data['preview']) ?>;i++){	
		   if(x = document.getElementById('wschk_'+i)){
		    if((x.checked)&&(document.getElementById('wsdst_'+i).value!='')){
		     lk = lk + '||' + document.getElementById('wsdst_'+i).value;
		    } 	
		   }
		  }
		  
		  document.getElementById('wslinks').value = lk;
		 }
		}
		
			for (i=1;i<=<?= count($data['preview']) ?>;i++)
			{	
			  if(x = document.getElementById('wschk_'+i)){
			   if(x.checked){
			     submitSeed(document.getElementById('wssrc_'+i).value,i)
			     t++;
			   } 	
			  }
			}
		
		ot = t;	
		
		if(t==0){
		 onedone(false);
		}	

        var div = document.getElementById('action_1'); 
		div.innerHTML = ' 1 of '+ ot; 	
	  
	  }//only fire p	  	
	
	function onedone(rs,id){
	  if(!rs){
	  	er++;
	  }	
	
	  t = t-1;
	  if((t==1)||(t==0)){
	  	YAHOO.util.Dom.setStyle('lbl_wsaction', 'display', 'none');
		YAHOO.util.Dom.setStyle('btn_wsaction', 'display', '');
  	    var div = document.getElementById('action_1').innerHTML = '';	
		
	  }else{
	  	var div = document.getElementById('action_1'); 
		div.innerHTML = (ot-t) + ' of ' + ot; 	
	  }	
	}
	
		
	function submitSeed(pageUrl,id){
	
	  var handleSuccess = function(o){
		if(o.responseText !== undefined){
		   id = o.argument;
		   
		   if(id){
		    var div = document.getElementById('wsdone_'+id);
			var response = YAHOO.Tools.JSONParse(o.responseText);
    		var title = response['title'];
    				
		    if( title == '' ){
		      title = document.getElementById('wsdst_'+id).value;	
		    }
		  
		    div.innerHTML = '<img src="http://images.wikia.com/common/common/tickIcon.gif" width="16" height="16" border="0" /> <a href="<?= $GLOBALS['wgServer'] .'/wiki/'?>'+title+'" target="_new"><?= wfMsg( 'ws.status.success' ) ?></a>';
		   }
		   onedone(true,id);
		}
	  };
	
	var handleFailure = function(o){
	    if(o.responseText !== undefined){
	      id = o.argument;	
	      if(id){
	        var div = document.getElementById('wsstatus_'+id);
		    div.innerHTML = "<?= wfMsg( 'ws.status.failure' ) ?>";
		  }
		  onedone(false,id);
		  
		}else{
	      id = o.argument;		
		  if(id){
		   var div = document.getElementById('wsstatus_'+id);
		   div.innerHTML = "<?= wfMsg( 'ws.status.timeout' ) ?>";
		  }
		  onedone(false,id);	
		}
		
	};
	
	var callback =
	{
	  success:handleSuccess,
	  failure:handleFailure,
	  argument:id,
	  timeout: <?= 50000 + (10000 * count($data['preview'])) ?>
	};
	
		var ajaxpath = "<?php echo $GLOBALS["wgScriptPath"]."/index.php";  ?>";	
		var postData = "?action=ajax&rs=wikireadrAjax&pageUrl="+pageUrl+"&inclLinks="+document.getElementById('wslinks').value;
		var request = YAHOO.util.Connect.asyncRequest('POST', ajaxpath+postData, callback, postData);
	};
  }
			
/*]]>*/
</script>

<form id="wikireadr" name="wikireadr" method="post" action="<?= $GLOBALS['wgTitle']->getLocalUrl() ?>">
<input type="hidden" id="action" name="action" value="preview">
<input type="hidden" id="initurl" name="initurl" value="">	  
</form>

<table>
<tr><td valign="top"><?= wfMsg( 'ws.step2.i.1' ) ?></td></tr>
<tr><td valign="top"><span class="error wikireadr"><?= $data['err'] ?></span></td></tr> 
<tr><td valign="top">
<form id="pages">
<input type="hidden" id="wslinks" name="wslinks" value="" />
<table id="pages">
<tr id="wsoverview"><td></td><td colspan="2"><?= count($data['preview'])-1 . ' ' . wfMsg( 'ws.step2.i.2' ) ?><br /><input type="checkbox" name="remred" id="remred"> <?= wfMsg( 'ws.status.remred' ) ?></td><td><div id="lbl_wsaction"></div><div id="btn_wsaction"><input id="btn_action" class="btn-action" type="button" value="<?= wfMsg( 'ws.status.import' ) ?>" onclick="goSeed();" /></div><div id="action_1"></div></td></tr>
<tr><td>#</td><td><b><?= wfMsg( 'ws.pagename.h' ) ?></b><img src="http://images.wikia.com/common/progress_bar.gif" width="1" height="1" border="0" /></td><td><b><?= wfMsg( 'ws.existingpage.h' ) ?></b></td><td width="150"><b><?= wfMsg( 'ws.status.h' ) ?></b>&nbsp;&nbsp; <span id="wscheckall">(<a href="javascript:checkall(false);"><?= wfMsg( 'ws.status.includeall' ) ?></a>)</span></td></tr>
<?

foreach($data['preview'] as $key => $value){
  $pageexist = '';
  $pe = 0;	
  if($value['pageexist']!=''){
    $pageexist = '<a href="'.$value['pageexist']. '" target="_new"><b><span class="notok">'.wfMsg( 'ws.link.h' ).'</span></b></a>';
    $pe=1;
  }
	
  if(!$key){
?>	
 <tr><td><?= $key+1 ?></td><td><a href="<?= $value['pageurl'] ?>" target="_new"><b><?= str_replace('_',' ',$value['page']) ?></b></a></td><td><?=$pageexist ?></td><td><div id="wsdone_<?= $key + 1 ?>"><input id="wsdst_<?= $key + 1 ?>" name="wsdst_<?= $key + 1 ?>" type="hidden" value="<?= str_replace('_',' ',$value['page']) ?>" /> <input id="pe_<?= $key + 1 ?>" name="pe_<?= $key + 1 ?>" type="hidden" value="<?= $pe ?>" /> <input id="wssrc_<?= $key + 1 ?>" name="wssrc_<?= $key + 1 ?>" type="hidden" value="<?= $value['source'] ?>" /><input id="wschk_<?= $key + 1 ?>" name="wschk_<?= $key + 1 ?>" type="checkbox" value="<?= $key+1 ?>" checked="1" /><?= wfMsg( 'ws.status.include' ) ?></div> <span id="wsstatus_<?= $key+1 ?>"></span></td></tr>
 <?	}else{	?> 
 <tr id="wsdetails_<?= $key+1 ?>"><td><?= $key+1 ?></td><td><a href="javascript:makestart('<?= $value['pageurl'] ?>',<?= $key + 1 ?>);"><img src="http://images.wikia.com/common/common/search.gif" width="16" height="16" /></a>&nbsp;<a href="<?= $value['pageurl'] ?>" target="_new"><?= str_replace('_',' ',$value['page']) ?></a></td><td><?= $pageexist ?></td><td><div id="wsdone_<?= $key + 1 ?>"> <input id="pe_<?= $key + 1 ?>" name="pe_<?= $key + 1 ?>" type="hidden" value="<?= $pe ?>" /> <input id="wsdst_<?= $key + 1 ?>" name="wsdst_<?= $key + 1 ?>" type="hidden" value="<?= str_replace('_',' ',$value['page']) ?>" /><input id="wssrc_<?= $key + 1 ?>" name="wssrc_<?= $key + 1 ?>" type="hidden" value="<?= $value['source'] ?>" /><input id="wschk_<?= $key + 1 ?>" name="wschk_<?= $key + 1 ?>" type="checkbox" value="<?= $key+1 ?>" /><?= wfMsg( 'ws.status.include' ) ?></div> <span id="wsstatus_<?= $key+1 ?>"></span></td></tr>
<?
  }
}
?>
</table>
</form>

</td></tr>
<tr><td valign="top"><center><a href="<?= $GLOBALS['wgTitle']->getLocalUrl() ?>"><b><< <?= wfMsg( 'ws.startover.h' ) ?></b></a></center>
</td></tr>
</table>
<!-- e:<?= __FILE__ ?> -->