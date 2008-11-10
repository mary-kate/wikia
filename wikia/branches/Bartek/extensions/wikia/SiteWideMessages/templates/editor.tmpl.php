<!-- s:<?= __FILE__ ?> -->
<div id="PaneNav">
	<a href="<?= $title->getLocalUrl('action=list') ?>"><?= wfMsg('swm-page-title-list') ?></a>
</div>

<div id="PanePreview"<?= empty($formData['messagePreview']) ? ' style="display:none"' : '' ?>>
	<fieldset>
	<legend><?= wfMsg('swm-label-preview') ?></legend>
		<div id="WikiTextPreview">
			<?= empty($formData['messagePreview']) ? '' : $formData['messagePreview'] ?>
		</div>
	</fieldset>
</div>

<div id="PaneCompose">
	<fieldset>
		<legend><?= wfMsg('swm-label-edit') ?></legend>
		<div id="PaneError"><?= isset($formData['errMsg']) ? Wikia::errormsg($formData['errMsg']) : '' ?></div>
		<form method="post" id="msgForm" action="<?= $title->getLocalUrl() ?>">
			<input type="hidden" name="editMsgId" value="<?= $editMsgId ?>" />
			<?php if (!$editMsgId) { ?>
			<fieldset>
				<legend><?= wfMsg('swm-label-recipient') ?></legend>
				<fieldset>
					<legend><?= wfMsg('swm-label-recipient-wikis') ?></legend>
					<table id="sendModeWikisTable">
						<tr>
							<td width="25">
								<input name="mSendModeWikis" id="mSendModeWikisA" type="radio" value="ALL"<?= $formData['sendModeWikis'] == 'ALL' ? ' checked="checked"' : ''?>/>
							</td>
							<td width="180">
								<label for="mSendModeWikisA"><?= wfMsg('swm-label-mode-wikis-all') ?></label>
							</td>
						</tr>

						<tr>
							<td>
								<input name="mSendModeWikis" id="mSendModeWikisH" type="radio" value="HUB"<?= $formData['sendModeWikis'] == 'HUB' ? ' checked="checked"' : ''?>/>
							</td>
							<td>
								<label for="mSendModeWikisH"><?= wfMsg('swm-label-mode-wikis-hub') ?></label>
							</td>
							<td>
								<select name="mHubId" id="mHubId" style="width:314px">
								<?php
								foreach ($formData['hubNames'] as $hubId => $hubName) {
									$selected = $hubId == $formData['hubId'] ? ' selected="selected"' : '';
									echo "\t\t\t\t\t\t\t\t<option value=\"$hubId\"$selected>$hubName</option>\n";
								}
								?>
								</select>
							</td>
						</tr>

						<tr>
							<td>
								<input name="mSendModeWikis" id="mSendModeWikisW" type="radio" value="WIKI"<?= $formData['sendModeWikis'] == 'WIKI' ? ' checked="checked"' : ''?>/>
							</td>
							<td>
								<label for="mSendModeWikisW"><?= wfMsg('swm-label-mode-wikis-wiki') ?></label>
							</td>
							<td>
								<input name="mWikiName" id="mWikiName" type="text" size="48" value="<?= $formData['wikiName'] ?>"/>
							</td>
						</tr>
					</table>
				</fieldset>

				<fieldset>
					<legend><?= wfMsg('swm-label-recipient-users') ?></legend>
					<table id="sendModeUsersTable">
						<tr>
							<td width="25">
								<input name="mSendModeUsers" id="mSendModeUsersA" type="radio" value="ALL"<?= $formData['sendModeUsers'] == 'ALL' ? ' checked="checked"' : ''?>/>
							</td>
							<td width="180">
								<label for="mSendModeUsersA"><?= wfMsg('swm-label-mode-users-all') ?></label>
							</td>
						</tr>

						<tr>
							<td width="25">
								<input name="mSendModeUsers" id="mSendModeUsersC" type="radio" value="ACTIVE"<?= $formData['sendModeUsers'] == 'ACTIVE' ? ' checked="checked"' : ''?>/>
							</td>
							<td width="180">
								<label for="mSendModeUsersC"><?= wfMsg('swm-label-mode-users-active') ?></label>
							</td>
						</tr>

						<tr>
							<td>
								<input name="mSendModeUsers" id="mSendModeUsersG" type="radio" value="GROUP"<?= $formData['sendModeUsers'] == 'GROUP' ? ' checked="checked"' : ''?>/>
							</td>
							<td>
								<label for="mSendModeUsersG"><?= wfMsg('swm-label-mode-users-group') ?></label>
							</td>
							<td>
								<select name="mGroupNameS" id="mGroupNameS" style="width:116px">
								<?php
								foreach ($formData['groupNames'] as $groupName) {
									$groupName = htmlspecialchars($groupName);
									$selected = $groupName == $formData['groupNameS'] ? ' selected="selected"' : '';
									echo "\t\t\t\t\t\t\t\t<option value=\"$groupName\"$selected>$groupName</option>\n";
								}
								?>
								</select>
								<input name="mGroupName" id="mGroupName" type="text" size="28" value="<?= $formData['groupName'] ?>"/>
							</td>
							<td>
								<?= wfMsg('swm-label-mode-users-group-hint') ?>
							</td>
						</tr>

						<tr>
							<td>
								<input name="mSendModeUsers" id="mSendModeUsersU" type="radio" value="USER"<?= $formData['sendModeUsers'] == 'USER' ? ' checked="checked"' : ''?>/>
							</td>
							<td>
								<label for="mSendModeUsersU"><?= wfMsg('swm-label-mode-users-user') ?></label>
							</td>
							<td>
								<input name="mUserName" id="mUserName" type="text" size="48" value="<?= $formData['userName'] ?>"/>
							</td>
							<td>
								<?= wfMsg('swm-label-mode-users-user-hint') ?>
							</td>
						</tr>
					</table>
				</fieldset>
			</fieldset>

			<fieldset>
				<legend><?= wfMsg('swm-label-expiration') ?></legend>
				<select name="mExpireTime" id="mExpireTime">
				<?php
				$days = explode(',', wfMsg('swm-days'));
				$expireOptions = explode(',', wfMsg('swm-expire-options'));
				foreach ($expireOptions as $expireOption) {
					if (ctype_digit($expireOption)) {
						if ($expireOption === '0') {
							$expireText = $days[0];
						} else {
							$expireText = "$expireOption " . ($days[min($expireOption, 2)+2]);
						}
					} elseif (preg_match('/^\d+h$/', $expireOption)) {
						$expireValue = substr($expireOption, 0, -1);
						$expireText = "$expireValue " . ($days[min($expireValue, 2)]);
					} else {
						//wrong entry - go to the next one
						continue;
					}
					$selected = $expireOption == $formData['expireTime'] ? ' selected="selected"' : '';
					echo "\t\t\t\t\t<option value=\"$expireOption\"$selected>$expireText</option>\n";
				}
				?>
				</select>
			</fieldset>
			<?php } //do not show this info when editing ?>

			<fieldset>
				<legend><?= wfMsg('swm-label-content') ?></legend>
				<textarea name="mContent" id="mContent" cols="30" rows="10"><?= empty($formData['messageContent']) ? '' : $formData['messageContent'] ?></textarea>
			</fieldset>

			<div id="PaneButtons">
				<input name="mAction" type="submit" value="<?= wfMsg('swm-button-preview') ?>" id="fPreview"/>
				<input name="mAction" type="submit" value="<?= $editMsgId ? wfMsg('swm-button-save') : wfMsg('swm-button-send') ?>" id="fSend"/>
				<input name="mAction" type="reset" value="<?= wfMsg('swm-button-new') ?>" id="fNew"/>
				<?= wfMsg('swm-taskmanager-hint') ?>
			</div>
		</form>
	</fieldset>
</div>

<script type="text/javascript">
function $(id) {
	return document.getElementById(id);
}
function grayOut(e) {
	var source = YAHOO.util.Event.getTarget(e);
	switch (source.id) {
		case 'mSendModeWikisH':
		case 'mSendModeWikisW':
			$('mSendModeUsersA').disabled = true;
			if ($('mSendModeUsersA').checked)
				$('mSendModeUsersC').checked = true;
			break;
		case 'mSendModeUsersU':
			$('mSendModeWikisA').disabled = true;
			$('mSendModeWikisH').disabled = true;
			$('mSendModeWikisW').disabled = true;
			break;
		default:
			if ($('mSendModeWikisA').checked)
				$('mSendModeUsersA').disabled = false;
			$('mSendModeWikisA').disabled = false;
			$('mSendModeWikisH').disabled = false;
			$('mSendModeWikisW').disabled = false;
	}
}
var radio = ['mSendModeWikisA', 'mSendModeWikisH', 'mSendModeWikisW', 'mSendModeUsersA', 'mSendModeUsersC', 'mSendModeUsersG', 'mSendModeUsersU'];
YAHOO.util.Event.addListener(radio, 'click', grayOut);
</script>
<!-- e:<?= __FILE__ ?> -->