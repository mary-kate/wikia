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
.error wikilad {color:red; font-weight:bold;}
.hide {display:none;}
.show {display:;}
.hrow { background:lightyellow;}
.bddarker { background:lightgreen;
			overflow:hidden;
			padding:4px;
		  }
#wsoverview {background:lightgray; height:25px;}
#wsoverview TD { border: 1px solid lightgray !important; height:25px;}
/*]]>*/
</style>

<script type="text/javascript">
/*<![CDATA[*/
// Instantiate the Dialog
function procDetail(id){

  // Define various event handlers for Dialog
  var handleSubmit = function() {
    dialog.submit();
  };

  var handleCancel = function() {
  	this.cancel();
  };
  
  var handleSuccess = function(o) {
  	var response = o.responseText;
  	YAHOO.util.Dom.addClass('art_'+id,"hrow")
	deleteArticle(id);
   };

  var handleFailure = function(o) {
    YAHOO.util.Dom.removeClass('art_'+id,"hrow")
  }; 

  dialog = new YAHOO.widget.Dialog("dialog1", 
	{ width : "500px",
	  modal : false,
	  zIndex : 99999,
	  draggable : true,
	  visible : false, 
	  fixedcenter : false,
	  constraintoviewport : true,
	  buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true },
				  { text:"Cancel", handler:handleCancel } ]
	} );
  
  dialog.callback.success = handleSuccess;
  dialog.callback.failure = handleFailure;

  dialog.render();
  dialog.show();
  
}

function procArticle(id){  var handleSuccess = function(o){
	if(o.responseText !== undefined){
	  id = o.argument;
	  if(id){
	  	var response = YAHOO.Tools.JSONParse(o.responseText);
	  	var rmsg = response['response'];
    	
    	if(rmsg!=''){
         document.getElementById('dialog_form').innerHTML = rmsg;
	     procDetail(id); 
	    } 
	  }  
	} 
  };
  
  var handleFailure = function(o){
	    if(o.responseText !== undefined){
	      id = o.argument;	
	      if(id){
	      }
		}
	};
	
  var callback =
	{
	  success:handleSuccess,
	  failure:handleFailure,
	  argument:id,
	  timeout:50000
	};
	
  var ajaxpath = "<?php echo $GLOBALS["wgScriptPath"]."/index.php";  ?>";	
  var postData = "?action=ajax&rs=wikiladAjax&getArticleDetails="+id;
  var request = YAHOO.util.Connect.asyncRequest('POST', ajaxpath+postData, callback, postData);
  
}

function deleteArticle(id){  var handleSuccess = function(o){
	if(o.responseText !== undefined){
	  id = o.argument;
	  if(id){
	  	if(tr = document.getElementById('art_'+id)){
	  	 tr.parentNode.removeChild(tr) ;
	    } 
	  }  
	} 
  };
  
  var handleFailure = function(o){
	    if(o.responseText !== undefined){
	      id = o.argument;	
	      if(id){
	      }
		}
	};
	
  var callback =
	{
	  success:handleSuccess,
	  failure:handleFailure,
	  argument:id,
	  timeout:50000
	};
	
  var ajaxpath = "<?php echo $GLOBALS["wgScriptPath"]."/index.php";  ?>";	
  var postData = "?action=ajax&rs=wikiladAjax&removeArticle="+id;
  var request = YAHOO.util.Connect.asyncRequest('POST', ajaxpath+postData, callback, postData);
  
}

		
/*]]>*/
</script>

<table>
<tr><td valign="top"><?= wfMsg( 'wl.info.i.1' ) ?></td></tr>
<tr><td valign="top"><span class="error wikilad"><?= $data['err'] ?></span></td></tr> 

<tr><td valign="top">
<form id="get_job_id" name="get_job_id" method="post" action="<?= $GLOBALS['wgTitle']->getLocalUrl() ?>">
<table id="pages">
<tr><td></td><td colspan="3">
<b><?= wfMsg( 'wl.lbl.availablearticles' ) ?></b>&nbsp;
<SELECT name="job_id" id="job_id">
<?
	foreach($data['joblist'] as $key => $value){
	  if( $value[ 'job_id'] == $data['job_id_selected'] ) {
	  	echo '<OPTION VALUE="' . $value[ 'job_id'].'" SELECTED> ' . date("M j, Y", strtotime( $value[ 'job_name'] ) ) . '  (' . $value[ 'cnt'] . ' articles) </OPTION>'; 
	  }else{
	  	echo '<OPTION VALUE="' . $value[ 'job_id'].'"> ' . date("M j, Y", strtotime( $value[ 'job_name'] ) ) . '  (' . $value[ 'cnt'] . ' articles) </OPTION>';
	  }  	
	}
?>
</SELECT>
&nbsp;
<input id="btn_action" class="btn-action" type="Submit" value="<?= wfMsg( 'wl.lbl.getarticles' ) ?>" />
</form>
&nbsp;<br/>&nbsp;

</td></tr>
</table>
<table id="pages" class="sortable" width="95%">
<tr id="wsoverview"><th class="unsortable">&nbsp;</th><th><b><?= wfMsg( 'wl.lbl.title' ) ?></b><img src="http://images.wikia.com/common/progress_bar.gif" width="1" height="1" border="0" /></th><th><b><?= wfMsg( 'wl.lbl.words' ) ?></b></th><th><b><?= wfMsg( 'wl.lbl.age' ) ?></b></th><th class="unsortable"><b><?= wfMsg( 'wl.lbl.todo' ) ?></b></th></tr>
<?


foreach($data['articles'] as $key => $value){
?>
 <tr id="art_<?= $value['article_id'] ?>">
 <td>&nbsp</td>
 <td><a href="http://start-your-own.wikia.com/wiki/<?= urlencode(str_replace(' ','_', $value['article_title'])) ?>" target="_new"><?= $value['article_title'] ?></a></td>
 <td align="right"><?= $value['wordcount'] ?></td>
 <td align="center"><?= $value['started'] ?></td>
 <td align="center"><span id="proc_<?= $value['article_id'] ?>"><a href="javascript:procArticle('<?= $value['article_id'] ?>');" title="<?= wfMsg( 'wl.lbl.procthisarticle' ) ?>:<?= $value['article_id'] ?>"><img src="http://images.wikia.com/common/common/editicon.png" width="16" height="16" /></a> </span> | <a href="javascript:deleteArticle('<?= $value['article_id'] ?>');" title="<?= wfMsg( 'wl.lbl.deletethisarticle' ) ?>:<?= $value['article_id'] ?>"><img src="http://images.wikia.com/common/common/deleteIcon.png" width="16" height="16" /></a></td></tr>
<?
}
?>
</table>
</td></tr>
</table>

<div id="dialog1">
<form id="dialog_form" name="dialog_form" method="POST" action="<?php global $wgScriptPath; echo "$wgScriptPath?action=ajax&rs=wikiladAjax&notifyUsers=1"; ?>">

</form>
</div>

<!-- e:<?= __FILE__ ?> -->
