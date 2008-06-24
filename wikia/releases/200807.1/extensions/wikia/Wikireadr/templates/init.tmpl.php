<!-- s:<?= __FILE__ ?> -->
<style type="text/css">
/*<![CDATA[*/
.btn-action { width:80px; }
#pages { width:100%; cellpadding:10px;}
#pages TD { border-bottom: 1px solid lightgray; height:25px;}
.ok {color:darkgreen;}
.notok {color:darkred;}
.error wikireadr {color:red; font-weight:bold;}

/*]]>*/
</style>

<table>
<tr><td valign="top"><?= wfMsg( 'ws.step1.i.1' ) ?></td></tr>
<tr><td valign="top"><span class="error wikireadr"><?= $data['err'] ?></span></td></tr> 
<tr><td valign="top">
<form id="wikiread" method="post" action="<?= $GLOBALS['wgTitle']->getLocalUrl() ?>">
<input type="hidden" name="action" value="preview">	  
<b><?= wfMsg( 'ws.step1.h.1' ) ?></b><br/>
&nbsp;<input type="text" name="initurl" value="<?= $data['initurl'] ?>" size="60"><br/>
&nbsp;<br/>
<input type="submit" value="<?= wfMsg( 'ws.ok' ) ?>"> <input type="reset" value="<?= wfMsg( 'ws.cancel' ) ?>"><br/>
</form>
</td></tr>
</table>
<!-- e:<?= __FILE__ ?> -->
