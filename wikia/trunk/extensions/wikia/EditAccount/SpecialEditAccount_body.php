<?php

/**
 * EditAccount
 *
 * This extension is used by Wikia Staff to manage essential user account information
 * in the case of a lost password and/or invalid e-mail submitted during registration.
 *
 * @author Łukasz Garczewski (TOR) <tor@wikia-inc.com>
 * @date 2008-09-17
 * @copyright Copyright (C) 2008 Łukasz Garczewski, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @package MediaWiki
 * @subpackage SpecialPage
 */

if (!defined('MEDIAWIKI')) {
	echo "This is MediaWiki extension named EditAccount.\n";
	exit(1) ;
}

class EditAccount extends SpecialPage {
	var $mUser = null;
	var $mStatus = null;
	var $mLog = true;
	var $mStatusMsg;

	function EditAccount() {
                SpecialPage::SpecialPage('EditAccount', 'editaccount');
                wfLoadExtensionMessages('EditAccount');
        }
          
	function execute() {
		global $wgOut, $wgUser, $wgRequest;
		$this->setHeaders();

		// quit early if user is not permited to access
		if(!$wgUser->isAllowed('editaccount')) {
                        $wgOut->permissionRequired('editaccount');
                        return;
		}

		$action = $wgRequest->getVal('wpAction');
		$userName = $wgRequest->getVal('wpUserName');

		// check if user name is an existing user
		if (!empty($userName)) {
			$this->mUser = User::newFromName( $userName );
			if ($this->mUser->idFromName( $userName ) === 0) {
				$this->mStatus = false;
				$this->mStatusMsg = wfMsg('editaccount-nouser', $userName);
				$action = '';
			}
		} else {
			$action = '';
		}

		switch ($action) {
			case 'setemail':
				$newEmail = $wgRequest->getVal('wpNewEmail');
				$this->mStatus = $this->setEmail($newEmail);
				$template = $this->mStatus ? 'selectuser' : 'displayuser';
				break;
			case 'setpass':
				$newPass = $wgRequest->getVal('wpNewPass');
				$this->mStatus = $this->setPassword($newPass);
				$template = $this->mStatus ? 'selectuser' : 'displayuser';
				break;
			case 'closeaccount':
				$this->mStatus = $this->closeAccount();
				$template = $this->mStatus ? 'selectuser' : 'displayuser';
				break;
			case 'displayuser':
				$template = 'displayuser';
				break;
			default:
				$template = 'selectuser';
		}	

		$wgOut->setPageTitle(wfMsg('editaccount-title'));

		$oTmpl = new EasyTemplate(dirname( __FILE__ ) . '/templates/');
		$oTmpl->set_Vars( array(
				'status'    => $this->mStatus,
				'statusMsg' => $this->mStatusMsg,
				'user'      => $userName,
				'userEmail' => is_object($this->mUser) ? $this->mUser->getEmail() : null
			));
		$wgOut->addHTML($oTmpl->execute($template));
	}

	function setEmail($email) {
		$oldEmail = $this->mUser->getEmail();
		if ($this->mUser->isValidEmailAddr( $email )) {
			$this->mUser->setEmail( $email );
			$this->mUser->saveSettings();

			// Check if everything went through OK, just in case
			if ($this->mUser->getEmail() == $email) {
				global $wgUser, $wgTitle;

				if ($this->mLog) {
					$log = new LogPage('editaccnt');
					$log->addEntry('mailchange', $wgTitle, '', array($this->mUser->getUserPage()));
				}

				$this->mStatusMsg = wfMsg('editaccount-success-email', $this->mUser->mName, $email);
				return true;
			} else {
				$this->mStatusMsg = wfMsg('editaccount-error-email', $this->mUser->mName);
				return false;
			}
		} else {
			$this->mStatusMsg = wfMsg('editaccount-invalid-email', $email);
			return false;
		}
	}

	function setPassword($pass) {
		if ($this->mUser>setPassword($pass)) {
			global $wgUser, $wgTitle;

			$this->mUser->saveSettings();

			if ($this->mLog) {
				$log = new LogPage('editaccnt');
				$log->addEntry('passchange', $wgTitle, '', array($this->mUser->getUserPage()));
			}

			$this->mStatusMsg = wfMsg('editaccount-success-pass', $this->mUser->mName);
			return true;
		} else {
			$this->mStatusMsg = wfMsg('editaccount-error-pass', $this->mUser->mName);
			return false;
		}
	}

	function closeAccount() {
		$this->mLog = false;

		# scramble the user's password
		$this->setPassword( wfGenerateToken() );

		# remove any email address attached to the account
		$this->setEmail( '' );

		# show on the user's contributions pages "this account is disabled
		# TODO: update user table for external hook

		# remove the nick from Special:ListUsers
		# TODO: update colum in dataware

		return true;
	}
}
