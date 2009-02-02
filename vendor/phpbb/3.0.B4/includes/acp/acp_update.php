<?php
/** 
*
* @package acp
* @version $Id: acp_update.php,v 1.1 2006/09/02 13:39:21 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package acp
*/
class acp_update
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$user->add_lang('install');

		$this->tpl_name = 'acp_update';
		$this->page_title = 'ACP_UPDATE';

		// Get current and latest version
		$errstr = '';
		$errno = 0;

		$info = get_remote_file('www.phpbb.com', '/updatecheck', '30x.txt', $errstr, $errno);

		if ($info === false)
		{
			trigger_error($errstr . adm_back_link($this->u_action));
		}

		$info = explode("\n", $info);
		$latest_version = trim($info[0]);

		$announcement_url = trim($info[1]);
		$update_archive_link = 'http://www.phpbb.com/files/releases/phpBB-' . $config['version'] . '_to_' . $latest_version . '.zip';
		$update_link = append_sid($phpbb_root_path . 'install/index.' . $phpEx, 'mode=update');

		$up_to_date = (version_compare(strtolower($config['version']), strtolower($latest_version), '<')) ? false : true;

		$template->assign_vars(array(
			'S_UP_TO_DATE'		=> $up_to_date,
			'S_VERSION_CHECK'	=> true,
			'U_ACTION'			=> $this->u_action,

			'LATEST_VERSION'	=> $latest_version,
			'CURRENT_VERSION'	=> $config['version'],

			'UPDATE_INSTRUCTIONS'	=> sprintf($user->lang['UPDATE_INSTRUCTIONS'], $announcement_url, $update_archive_link, $update_link),
		));
	}
}

?>