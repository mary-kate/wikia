<?php

/**
 * CovertOps
 *
 * Lets privlidged users edit wikis without leaving a visible trace
 * in RecentChanges and logs. Used for contests.
 *
 * @author Łukasz Garczewski (TOR) <tor@wikia.com>
 * @date 2008-08-18
 * @copyright Copyright (C) 2008 Łukasz Garczewski, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @package MediaWiki
 * @subpackage SpecialPage
 */

if (!defined('MEDIAWIKI')) {
	echo "This is MediaWiki extension named SiteWideMessages.\n";
	exit(1) ;
}

class CovertOps extends SpecialPage {
	/**
	 * contructor
	 */
	function  __construct() {
		parent::__construct('CovertOps' /*class*/, 'covertops' /*restriction*/);
	}

	function execute($subpage) {
		global $wgUser, $wgOut, $wgRequest, $wgTitle, $wgParser;
		wfLoadExtensionMessages('CovertOps');

		$template = 'editor';	//default template

		//handle different submit buttons in one form
		if ($wgRequest->getVal('coEdit', false)) {
			$action = 'edit';
		} elseif ($wgRequest->getVal('coPreview', false)) {
			$action = 'preview';
		} elseif ($wgRequest->getVal('coSave', false)) {
			$action = 'save';
		} else {
			$action = 'select';
		}

		if(!$wgUser->isAllowed('covertops')) {
			$wgOut->permissionRequired('covertops');
			return;
		}

		$mTitle = $wgRequest->getText('mTitle');
		$formData['mTitle'] = $mTitle;

		switch ($action) {

			case 'select':
				$wgOut->SetPageTitle(wfMsg('cops-page-title-select'));
				$template = "selector";	
				break;
			case 'save':
				$mText = $wgRequest->getText('mContent');
				$mArticle = new Article ( Title::newFromText( $mTitle ) );
				$dbw =& wfGetDb( DB_MASTER );

				$old_id = $dbw->selectField ( 'revision', 
					'rev_text_id',
					array( 'rev_id' => $mArticle->getLatest() ) );


				/* backup current rev text */

				$dbw->query("CREATE TABLE IF NOT EXISTS `covertops_text` (
					`old_id` int(8) unsigned NOT NULL auto_increment,
					`old_namespace` tinyint(2) unsigned NOT NULL default '0',
					`old_title` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
					`old_text` mediumtext NOT NULL,
					`old_comment` tinyblob NOT NULL,
					`old_user` int(5) unsigned NOT NULL default '0',
					`old_user_text` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
					`old_timestamp` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
					`old_minor_edit` tinyint(1) NOT NULL default '0',
					`old_flags` tinyblob NOT NULL,
					`inverse_timestamp` varchar(14) character set latin1 collate latin1_bin NOT NULL default '',
					PRIMARY KEY  (`old_id`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1;"
				);

				$dbw->query ("INSERT INTO `covertops_text` (old_id, old_namespace, old_text, old_user, old_flags)
					SELECT old_id, old_namespace, old_text, old_user, old_flags FROM `text` WHERE old_id = $old_id");
				

				/* replace current rev text */
				$dbw->update(
					$dbw->tableName( 'text' ),
					array( 
						'old_text' => $mText,
						'old_flags' => ''
					),
					array( 'old_id' => $old_id )
				);

				$dbw->insert(
					'page_restrictions',
					array(
						'pr_page' => $mArticle->getId(),
						'pr_type' => 'edit',
						'pr_level' => 'sysop',
						'pr_cascade' => 0,	
						'pr_user' => NULL,
						'pr_expiry' => 'infinity',
						'pr_id' => NULL	
						)
				);
				

				$title = Title::newFromText($mTitle);
				$redirect = $title->getLocalUrl('action=purge');
				$wgOut->redirect($redirect, 200);
				return;
				break;

                       case 'preview':
                                $formData['messageContent'] = $wgRequest->getText('mContent');
                                if ($formData['messageContent'] != '') {
                                        global $wgUser, $wgParser;
                                        $title = Title::newFromText(uniqid('tmp'));
                                        $options = ParserOptions::newFromUser($wgUser);

                                        //Parse some wiki markup [eg. ~~~~]
                                        $formData['messageContent'] = $wgParser->preSaveTransform($formData['messageContent'], $title, $wgUser, $options);
                                }

                                $formData['messagePreview'] = $wgOut->parse($formData['messageContent']);
                                $wgOut->SetPageTitle(wfMsg('cops-page-title-preview'));
                                break;

			case 'edit':
				$mTitle = $wgRequest->getText('mTitle');
				$article = new Article ( Title::newFromText( $mTitle ) );
				$formData['mTitle'] = $mTitle;
				$formData['messageContent'] = !empty($article) ? $article->getContent() : null;
				//no break - go to 'default' => editor

			default:	//editor
				$wgOut->SetPageTitle(wfMsg('cops-page-title-editor'));
		}

		$oTmpl = new EasyTemplate(dirname( __FILE__ ) . '/templates/');
		$oTmpl->set_vars( array(
				'title' => $wgTitle,
				'formData' => $formData,
				'mTitle' => $mTitle
			));
		$wgOut->addHTML($oTmpl->execute($template));
	}

}
