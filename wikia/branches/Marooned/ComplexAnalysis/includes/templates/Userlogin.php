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
				<input type='text' class='loginText' name="wpName" id="wpName1" value="<?php $this->text('name') ?>" size='20' />
			</td>
		</tr>
		<tr>
			<td class="mw-label"><label for='wpPassword1'><?php $this->msg('yourpassword') ?></label></td>
			<td class="mw-input">
				<input type='password' class='loginPassword' name="wpPassword" id="wpPassword1" value="" size='20' />
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
					<?php echo $doms ?>
				</select>
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td></td>
			<td class="mw-input">
				<input type='checkbox' name="wpRemember"
					value="1" id="wpRemember"
					<?php if( $this->data['remember'] ) { ?>checked="checked"<?php } ?>
					/> <label for="wpRemember"><?php $this->msg('remembermypassword') ?></label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="mw-submit">
				<input type='submit' name="wpLoginattempt" id="wpLoginattempt" value="<?php $this->msg('login') ?>" />&nbsp;<?php if( $this->data['useemail'] && $this->data['canreset']) { ?><input type='submit' name="wpMailmypassword" id="wpMailmypassword" value="<?php $this->msg('mailmypassword') ?>" />
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

		global $wgOut, $wgStylePath, $wgStyleVersion, $wgValidateUserName;

		$wgOut->addScript('<link rel="stylesheet" type="text/css" href="'. $wgStylePath . '/wikia/common/NewUserRegister.css?' . $wgStyleVersion . "\" />\n");

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
<form name="userlogin2" id="userlogin2" method="post" action="<?php $this->text('action') ?>" onsubmit="return checkForm();">
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
	<script type="text/javascript">
		var errorNick = false;	//nick checking can be disabled
		var errorEmail = errorPass = errorRetype = errorDate = true;
		var dateAccessed = 0;

		function checkForm() {
			dateAccessed = 2;	//check date on submit
			checkDate();
			checkEmail();
			checkPass();
			checkRetype();
			if (errorNick) {
				document.getElementById('wpName2error').style.display = 'inline';
			}
			if (errorDate) {
				document.getElementById('wpBirtherror').style.display = 'inline';
			}
			if (errorNick || errorEmail || errorPass || errorRetype || errorDate) {
				document.getElementById('wpFormerror').style.display = 'inline';
			}
			return !(errorNick || errorEmail || errorPass || errorRetype || errorDate);
		}

		function update_day_field() {
			var year = document.getElementById('wpBirthYear').value;
			var month = document.getElementById('wpBirthMonth').value;
			var day = document.getElementById('wpBirthDay');
			var day_length = day.length;
			var max_days = (year == -1 || month == -1) ? 31 : new Date(year, month, 0).getDate();
			var day_diff = max_days - (day_length - 1);
			if (day_diff > 0) {
				for(n=0; n<day_diff; n++) {
					var newOption = document.createElement('option');
					newOption.value = newOption.innerHTML = day_length + n;
					day.appendChild(newOption);
				}
			} else if (day_diff < 0) {
				for(n=0; n<-day_diff; n++) {
					day.remove(day.length - 1);
				}
			}
		}
	</script>
	<table width="100%">
		<colgroup>
			<col width="180" />
			<col width="*" />
		</colgroup>
		<tr>
			<td class="mw-label"><label for='wpName2'><?php $this->msg('yourname') ?></label></td>
			<td class="mw-input" id="wpNameTD">
				<input type='text' class='loginText' name="wpName" id="wpName2"	value="<?php $this->text('name') ?>" size='20' />
				<span id="wpName2error" class="inputError"><?= wfMsg('noname') ?></span>
			</td>
		</tr>
		<tr>
			<td class="mw-label"><label for='wpBirthYear'><?php $this->msg('yourbirthdate') ?></label></td>
			<td class="mw-input" id="wpBirthDateTD">
				<select name="wpBirthYear" id="wpBirthYear">
					<option value="-1"><?php $this->msg('userlogin-choose-year') ?></option>
					<?php
					$maxYear = date('Y');
					for($year=$maxYear; $year>=1900; $year--) {
						echo "\t\t\t\t\t<option value=\"$year\">$year</option>";
					}
					?>
				</select>
				<select name="wpBirthMonth" id="wpBirthMonth">
					<option value="-1"><?php $this->msg('userlogin-choose-month') ?></option>
					<?php
					for($month=1; $month<=12; $month++) {
						echo "\t\t\t\t\t<option value=\"$month\">$month</option>";
					}
					?>
				</select>
				<select name="wpBirthDay" id="wpBirthDay">
					<option value="-1"><?php $this->msg('userlogin-choose-day') ?></option>
					<?php
					for($day=1; $day<=31; $day++) {
						echo "\t\t\t\t\t<option value=\"$day\">$day</option>";
					}
					?>
				</select>
				<span id="wpBirtherror" class="inputError"><?= wfMsg('userlogin-bad-birthday') ?></span>
			</td>
		</tr>
		<tr>
			<?php if( $this->data['useemail'] ) { ?>
			<td class="mw-label"><label for='wpEmail'><?php $this->msg('youremail') ?></label></td>
			<td class="mw-input" id="wpEmailTD">
				<input type='text' class='loginText' name="wpEmail" id="wpEmail" value="<?php $this->text('email') ?>" size='20' />
			</td>
			<?php } ?>
		</tr>
		<tr>
			<td class="mw-label"><label for='wpPassword2'><?php $this->msg('yourpassword') ?></label></td>
			<td class="mw-input" id="wpPasswordTD">
				<input type='password' class='loginPassword' name="wpPassword" id="wpPassword2" value="" size='20' />
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
				<select name="wpDomain" value="<?php $this->text( 'domain' ) ?>">
					<?php echo $doms ?>
				</select>
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td class="mw-label"><label for='wpRetype'><?php $this->msg('yourpasswordagain') ?></label></td>
			<td class="mw-input" id="wpRetypeTD">
				<input type='password' class='loginPassword' name="wpRetype" id="wpRetype" value="" size='20' />
			</td>
		</tr>
		<tr>
			<?php if( $this->data['userealname'] ) { ?>
			<td class="mw-label"><label for='wpRealName'><?php $this->msg('yourrealname') ?></label></td>
			<td class="mw-input">
				<input type='text' class='loginText' name="wpRealName" id="wpRealName" value="<?php $this->text('realname') ?>" size='20' />
				<div class="prefsectiontip">
					<?php $this->msgWiki('prefs-help-realname'); ?>
				</div>
			</td>
			<?php } ?>
		</tr>
	<?php if($this->haveData('captcha')) { ?>
		<tr>
			<td class="mw-label"><label for='wpCaptchaWord'><?php $this->msg('userlogin-captcha-label') ?></label></td>
			<td class="mw-input">
				<?php $this->html('captcha'); ?>
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td></td>
			<td class="mw-input">
				<input type='checkbox' name="wpRemember" value="1" id="wpRemember" <?php if( $this->data['remember'] ) { ?>checked="checked"<?php } ?>/>
				<label for="wpRemember"><?php $this->msg('remembermypassword') ?></label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="mw-submit">
				<input type='submit' name="wpCreateaccount" id="wpCreateaccount" value="<?php $this->msg('createaccount') ?>" />
				<?php if( $this->data['createemail'] ) { ?>
				<input type='submit' name="wpCreateaccountMail" id="wpCreateaccountMail" value="<?php $this->msg('createaccountmail') ?>" />
				<?php } ?>
				<span id="wpFormerror" class="inputError"><?= wfMsg('userlogin-form-error') ?></span>

			</td>
		</tr>
		<?php if( $this->data['useemail'] ) { ?>
		<tr>
			<td></td>
			<td class="mw-input" id="wpEmailTD">
				<div class="prefsectiontip">
					<?php $this->msgWiki('prefs-help-email'); ?>
				</div>
			</td>
		</tr>
		<?php } ?>
	</table>
	<script type="text/javascript">
		function checkEmail() {
			var email_elem = document.getElementById('wpEmail') ;
			if (email_elem) {				
				var email = email_elem.value;
				if (email == '') {
					YAHOO.util.Dom.removeClass('wpEmailTD', 'mw-input-error');
					YAHOO.util.Dom.removeClass('wpEmailTD', 'mw-input-ok');
					errorEmail = false;
				} else if (email.match(/^[a-z0-9._%+-]+@(?:[a-z0-9\-]+\.)+[a-z]{2,4}$/m)) {
					YAHOO.util.Dom.removeClass('wpEmailTD', 'mw-input-error');
					YAHOO.util.Dom.addClass('wpEmailTD', 'mw-input-ok');
					errorEmail = false;
				} else {
					YAHOO.util.Dom.addClass('wpEmailTD', 'mw-input-error');
					YAHOO.util.Dom.removeClass('wpEmailTD', 'mw-input-ok');
					errorEmail = true;		
				}
			} else {
				errorEmail = false ;
			}
		}
		function checkPass() {
			var passLen = document.getElementById('wpPassword2').value.length;
			if (passLen >= <?php global $wgMinimalPasswordLength; echo $wgMinimalPasswordLength; ?>) {
				YAHOO.util.Dom.removeClass('wpPasswordTD', 'mw-input-error');
				YAHOO.util.Dom.addClass('wpPasswordTD', 'mw-input-ok');
				errorPass = false;
			} else {
				YAHOO.util.Dom.addClass('wpPasswordTD', 'mw-input-error');
				YAHOO.util.Dom.removeClass('wpPasswordTD', 'mw-input-ok');
				errorPass = true;
			}
		}
		function checkRetype() {
			var pass = document.getElementById('wpPassword2').value;
			var pass2= document.getElementById('wpRetype').value;
			if (pass == pass2) {
				YAHOO.util.Dom.removeClass('wpRetypeTD', 'mw-input-error');
				YAHOO.util.Dom.addClass('wpRetypeTD', 'mw-input-ok');
				errorRetype = false;
			} else {
				YAHOO.util.Dom.addClass('wpRetypeTD', 'mw-input-error');
				YAHOO.util.Dom.removeClass('wpRetypeTD', 'mw-input-ok');
				errorRetype = true;
			}
		}
		function checkDate() {
			update_day_field();	//add/remove days according to the year & month
			var year = document.getElementById('wpBirthYear').value;
			var month = document.getElementById('wpBirthMonth').value;
			var day = document.getElementById('wpBirthDay').value;
			document.getElementById('wpBirtherror').style.display = 'none';
			if (dateAccessed == 1) {
				YAHOO.util.Dom.removeClass('wpBirthDateTD', 'mw-input-error');
				YAHOO.util.Dom.removeClass('wpBirthDateTD', 'mw-input-ok');
			} else if (dateAccessed == 2 && year != -1 && month != -1 && day != -1) {
				YAHOO.util.Dom.removeClass('wpBirthDateTD', 'mw-input-error');
				YAHOO.util.Dom.addClass('wpBirthDateTD', 'mw-input-ok');
				errorDate = false;
			} else if(dateAccessed == 2) {
				YAHOO.util.Dom.addClass('wpBirthDateTD', 'mw-input-error');
				YAHOO.util.Dom.removeClass('wpBirthDateTD', 'mw-input-ok');
				errorDate = true;
			}
		}
		document.getElementById('wpBirthYear').onfocus = function(){dateAccessed = 1;};
		document.getElementById('wpBirthMonth').onfocus = function(){dateAccessed = 1;};
		document.getElementById('wpBirthDay').onfocus = function(){dateAccessed = 1;};

		document.getElementById('wpName2').onfocus = function(){if (dateAccessed == 1) {dateAccessed = 2; checkDate();}};
		if (document.getElementById('wpEmail')) {
			document.getElementById('wpEmail').onfocus = function(){if (dateAccessed == 1) {dateAccessed = 2; checkDate();}};
			document.getElementById('wpEmail').onblur = checkEmail;		
		}
		document.getElementById('wpPassword2').onfocus = function(){if (dateAccessed == 1) {dateAccessed = 2; checkDate();}};
		document.getElementById('wpRetype').onfocus = function(){if (dateAccessed == 1) {dateAccessed = 2; checkDate();}};
		document.getElementById('wpRealName').onfocus = function(){if (dateAccessed == 1) {dateAccessed = 2; checkDate();}};

		document.getElementById('wpName2').onkeyup = function(){
			YAHOO.util.Dom.removeClass('wpNameTD', 'mw-input-ok');
			YAHOO.util.Dom.removeClass('wpNameTD', 'mw-input-error');
			document.getElementById('wpName2error').style.display = 'none';
		};

		document.getElementById('wpBirthYear').onchange = checkDate;
		document.getElementById('wpBirthMonth').onchange = checkDate;
		document.getElementById('wpBirthDay').onchange = checkDate;
		document.getElementById('wpPassword2').onblur = checkPass;
		document.getElementById('wpRetype').onblur = checkRetype;
	</script>
	<?php
	#>> cx (23.01.2007 @ #476) + Marooned [2008-05-27]
	if ($wgValidateUserName) {
		?>
		<script type="text/javascript" id="validate_login_code">
		var cxServer = '<?php Global $wgServer; Echo $wgServer;?>';
		var cxScript = '<?php Global $wgScriptPath; Echo $wgScriptPath;?>';
		var liveValidationAjaxURI = cxServer + cxScript + '/?action=ajax&rs=cxValidateUserName&rsargs=';
		var liveValidationPrevState = '';

		function login_formhandler (x) {
			x = x.responseText; //MW 1.9 handling response
			YAHOO.util.Dom.removeClass('wpNameTD', 'mw-progress');
			if (x == 'OK') {
				YAHOO.util.Dom.removeClass('wpNameTD', 'mw-input-error');
				YAHOO.util.Dom.addClass('wpNameTD', 'mw-input-ok');
				errorNick = false;
			} else {
				YAHOO.util.Dom.addClass('wpNameTD', 'mw-input-error');
				YAHOO.util.Dom.removeClass('wpNameTD', 'mw-input-ok');
				errorNick = true;
			}
		}
		function login_eventhandler (x) {
			YAHOO.util.Dom.removeClass('wpNameTD', 'mw-input-ok');
			YAHOO.util.Dom.removeClass('wpNameTD', 'mw-input-error');
			YAHOO.util.Dom.addClass('wpNameTD', 'mw-progress');
			sajax_do_call('cxValidateUserName', Array (this.value), login_formhandler);
		}

		document.getElementById('wpName2').onblur = login_eventhandler;
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
