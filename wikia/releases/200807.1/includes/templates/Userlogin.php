<?php
/**
 * @addtogroup Templates
 */
if( !defined( 'MEDIAWIKI' ) ) die( -1 );

/** */
require_once( 'includes/SkinTemplate.php' );

/**
 * HTML template for Special:Userlogin form
 * @addtogroup Templates
 */
class UserloginTemplate extends QuickTemplate {
	function execute() {
		if( $this->data['message'] ) {
?>
	<div class="<?php $this->text('messagetype') ?>box">
		<?php if ( $this->data['messagetype'] == 'error' ) { ?>
			<h2><?php $this->msg('loginerror') ?>:</h2>
		<?php } ?>
		<?php $this->html('message') ?>
	</div>
	<div class="visualClear"></div>
<?php } ?>

<div id="userloginForm">
<form name="userlogin" method="post" action="<?php $this->text('action') ?>">
	<h2><?php $this->msg('login') ?></h2>
	<p id="userloginlink"><?php $this->html('link') ?></p>
	<?php $this->html('header'); /* pre-table point for form plugins... */ ?>
	<div id="userloginprompt"><?php  $this->msgWiki('loginprompt') ?></div>
	<?php if( @$this->haveData( 'languages' ) ) { ?><div id="languagelinks"><p><?php $this->html( 'languages' ); ?></p></div><?php } ?>
	<table>
		<tr>
			<td class="mw-label"><label for='wpName1'><?php $this->msg('yourname') ?></label></td>
			<td class="mw-input">
				<input type='text' class='loginText' name="wpName" id="wpName1"
					tabindex="1"
					value="<?php $this->text('name') ?>" size='20' />
			</td>
		</tr>
		<tr>
			<td class="mw-label"><label for='wpPassword1'><?php $this->msg('yourpassword') ?></label></td>
			<td class="mw-input">
				<input type='password' class='loginPassword' name="wpPassword" id="wpPassword1"
					tabindex="2"
					value="" size='20' />
			</td>
		</tr>
	<?php if( $this->data['usedomain'] ) {
		$doms = "";
		foreach( $this->data['domainnames'] as $dom ) {
			$doms .= "<option>" . htmlspecialchars( $dom ) . "</option>";
		}
	?>
		<tr>
			<td class="mw-label"><?php $this->msg( 'yourdomainname' ) ?></td>
			<td class="mw-input">
				<select name="wpDomain" value="<?php $this->text( 'domain' ) ?>"
					tabindex="3">
					<?php echo $doms ?>
				</select>
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td></td>
			<td class="mw-input">
				<input type='checkbox' name="wpRemember"
					tabindex="4"
					value="1" id="wpRemember"
					<?php if( $this->data['remember'] ) { ?>checked="checked"<?php } ?>
					/> <label for="wpRemember"><?php $this->msg('remembermypassword') ?></label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="mw-submit">
				<input type='submit' name="wpLoginattempt" id="wpLoginattempt" tabindex="5" value="<?php $this->msg('login') ?>" />&nbsp;<?php if( $this->data['useemail'] && $this->data['canreset']) { ?><input type='submit' name="wpMailmypassword" id="wpMailmypassword"
					tabindex="6"
									value="<?php $this->msg('mailmypassword') ?>" />
				<?php } ?>
			</td>
		</tr>
	</table>
<?php if( @$this->haveData( 'uselang' ) ) { ?><input type="hidden" name="uselang" value="<?php $this->text( 'uselang' ); ?>" /><?php } ?>
</form>
</div>
<div id="loginend"  style="clear: both;"><?php $this->msgWiki( 'loginend' ); ?></div>
<?php

	}
}

/**
 * @addtogroup Templates
 */
class UsercreateTemplate extends QuickTemplate {
	function execute() {
	    if (!array_key_exists('message', $this->data)) {
	        $this->data['message'] = "";
        }
	    if (!array_key_exists('ajax', $this->data)) {
	        $this->data['ajax'] = "";
        }
		if( $this->data['message'] && !$this->data['ajax'] ) {
?>
	<div class="<?php $this->text('messagetype') ?>box">
		<?php if ( $this->data['messagetype'] == 'error' ) { ?>
			<h2><?php $this->msg('loginerror') ?>:</h2>
		<?php } ?>
		<?php $this->html('message') ?>
	</div>
	<div class="visualClear"></div>
<?php } ?>
<div id="userlogin<?php if ($this->data['ajax']) { ?>Ajax<?php } ?>">
<form name="userlogin2" id="userlogin2" method="post" action="<?php $this->text('action') ?>">
	<h2><?php $this->msg('createaccount') ?></h2>
<?php		if( $this->data['message'] && $this->data['ajax'] ) { ?>
	<div class="<?php $this->text('messagetype') ?>box" style="margin:0px">
		<?php if ( $this->data['messagetype'] == 'error' ) { ?>
			<h2><?php $this->msg('loginerror') ?>:</h2>
		<?php } ?>
		<?php $this->html('message') ?>
	</div>
	<div class="visualClear"></div>
<?php } ?>
	<p id="userloginlink"><?php $this->html('link') ?></p>
	<?php $this->html('header'); /* pre-table point for form plugins... */ ?>
	<?php if( @$this->haveData( 'languages' ) ) { ?><div id="languagelinks"><p><?php $this->html( 'languages' ); ?></p></div><?php } ?>
	<table>
		<tr>
			<td class="mw-label"><label for='wpName2'><?php $this->msg('yourname') ?></label></td>
			<td class="mw-input">
				<input type='text' class='loginText' name="wpName" id="wpName2"
					tabindex="1"
					value="<?php $this->text('name') ?>" size='20' />
			</td>
		</tr>
		<tr>
			<td class="mw-label"><label for='wpPassword2'><?php $this->msg('yourpassword') ?></label></td>
			<td class="mw-input">
				<input type='password' class='loginPassword' name="wpPassword" id="wpPassword2"
					tabindex="2"
					value="" size='20' />
			</td>
		</tr>
	<?php if( $this->data['usedomain'] ) {
		$doms = "";
		foreach( $this->data['domainnames'] as $dom ) {
			$doms .= "<option>" . htmlspecialchars( $dom ) . "</option>";
		}
	?>
		<tr>
			<td class="mw-label"><?php $this->msg( 'yourdomainname' ) ?></td>
			<td class="mw-input">
				<select name="wpDomain" value="<?php $this->text( 'domain' ) ?>"
					tabindex="3">
					<?php echo $doms ?>
				</select>
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td class="mw-label"><label for='wpRetype'><?php $this->msg('yourpasswordagain') ?></label></td>
			<td class="mw-input">
				<input type='password' class='loginPassword' name="wpRetype" id="wpRetype"
					tabindex="4"
					value=""
					size='20' />
			</td>
		</tr>
		<tr>
			<?php if( $this->data['useemail'] ) { ?>
				<td class="mw-label"><label for='wpEmail'><?php $this->msg('youremail') ?></label></td>
				<td class="mw-input">
					<input type='text' class='loginText' name="wpEmail" id="wpEmail"
						tabindex="5"
						value="<?php $this->text('email') ?>" size='20' />
					<div class="prefsectiontip">
						<?php $this->msgWiki('prefs-help-email'); ?>
					</div>
				</td>
			<?php } ?>
			<?php if( $this->data['userealname'] ) { ?>
				</tr>
				<tr>
					<td class="mw-label"><label for='wpRealName'><?php $this->msg('yourrealname') ?></label></td>
					<td class="mw-input">
						<input type='text' class='loginText' name="wpRealName" id="wpRealName"
							tabindex="6"
							value="<?php $this->text('realname') ?>" size='20' />
						<div class="prefsectiontip">
							<?php $this->msgWiki('prefs-help-realname'); ?>
						</div>
					</td>
			<?php } ?>
		</tr>
		<tr>
			<td></td>
			<td class="mw-input">
				<input type='checkbox' name="wpRemember"
					tabindex="7"
					value="1" id="wpRemember"
					<?php if( $this->data['remember'] ) { ?>checked="checked"<?php } ?>
					/> <label for="wpRemember"><?php $this->msg('remembermypassword') ?></label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="mw-submit">
				<input type='submit' name="wpCreateaccount" id="wpCreateaccount"
					tabindex="8"
					value="<?php $this->msg('createaccount') ?>" />
				<?php if( $this->data['createemail'] ) { ?>
				<input type='submit' name="wpCreateaccountMail" id="wpCreateaccountMail"
					tabindex="9"
					value="<?php $this->msg('createaccountmail') ?>" />
				<?php } ?>
			</td>
		</tr>
	</table>

	<?php
	#>> cx (23.01.2007 @ #476)
	global $wgValidateUserName;
	if ($wgValidateUserName)
	{
		?>

	        <script type="text/javascript" id="validate_login_code">
	        var cxServer = '<?php Global $wgServer; Echo $wgServer;?>';
		var cxScript = '<?php Global $wgScriptPath; Echo $wgScriptPath;?>';
	        var liveValidationAjaxURI = cxServer + cxScript + '/?action=ajax&rs=cxValidateUserName&rsargs=';
	        var liveValidationPrevState = '';

		function login_formhandler (x)
		{
			// MW 1.6 awful error handling... :|
			//x = x.substr (2);

			x = x.responseText; //MW 1.9 handling response
			if (x == 'OK')
			{
				document.getElementById ('wpName2').style.borderColor = '#dfd';
				document.getElementById ('wpName2').style.backgroundColor = '#dfd';
			}
			else
			{
				document.getElementById ('wpName2').style.borderColor = '#fdd';
				document.getElementById ('wpName2').style.backgroundColor = '#fdd';
			}
                        //document.getElementById ("wpCreateaccount2").disabled = (x == 'OK');
                        //document.getElementById ("wpCreateaccountMail2").disabled  = (x == 'OK');
		}
		function login_eventhandler (x)
		{
			document.getElementById ('wpName2').style.borderColor = null;
                        document.getElementById ('wpName2').style.backgroundColor = null;

			sajax_do_call ('cxValidateUserName', Array (this.value), login_formhandler);
		}
		
		document.getElementById ('wpName2').onblur = login_eventhandler;
		</script>
	
	        <?php
	    }
	#<< cx
    ?>	
	
<?php if( @$this->haveData( 'uselang' ) ) { ?><input type="hidden" name="uselang" value="<?php $this->text( 'uselang' ); ?>" /><?php } ?>
</form>
</div>
<div id="signupend" style="clear: both;"><?php $this->msgWiki( 'signupend' ); ?></div>
<?php

	}
}

?>
