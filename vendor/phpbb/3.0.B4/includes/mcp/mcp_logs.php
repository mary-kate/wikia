<?php
/** 
*
* @package mcp
* @version $Id: mcp_logs.php,v 1.19 2006/11/24 14:58:07 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* mcp_logs
* Handling warning the users
* @package mcp
*/
class mcp_logs
{
	var $u_action;
	var $p_master;

	function mcp_main(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($id, $mode)
	{
		global $auth, $db, $user, $template;
		global $config, $phpbb_root_path, $phpEx;

		$user->add_lang('acp/common');

		$action = request_var('action', array('' => ''));

		if (is_array($action))
		{
			list($action, ) = each($action);
		}
		else
		{
			$action = request_var('action', '');
		}

		// Set up general vars
		$start		= request_var('start', 0);
		$deletemark = ($action == 'del_marked') ? true : false;
		$deleteall	= ($action == 'del_all') ? true : false;
		$marked		= request_var('mark', array(0));

		// Sort keys
		$sort_days	= request_var('st', 0);
		$sort_key	= request_var('sk', 't');
		$sort_dir	= request_var('sd', 'd');

		$this->tpl_name = 'mcp_logs';
		$this->page_title = 'MCP_LOGS';

		$forum_id = $topic_id = 0;
		switch ($mode)
		{
			case 'front':
				$where_sql = '';
			break;

			case 'forum_logs':
				$forum_id = request_var('f', 0);
				$where_sql = " AND forum_id = $forum_id";
			break;

			case 'topic_logs':
				$topic_id = request_var('t', 0);
				$where_sql = " AND topic_id = $topic_id";
			break;
		}

		// Delete entries if requested and able
		if (($deletemark || $deleteall) && $auth->acl_get('a_clearlogs'))
		{
			if ($deletemark && $marked)
			{
				$sql_in = array();
				foreach ($marked as $mark)
				{
					$sql_in[] = $mark;
				}

				$where_sql = ' AND ' . $db->sql_in_set('log_id', $sql_in);
				unset($sql_in);
			}

			if ($where_sql || $deleteall)
			{
				$sql = 'DELETE FROM ' . LOG_TABLE . '
					WHERE log_type = ' . LOG_MOD . "
					$where_sql";
				$db->sql_query($sql);

				add_log('admin', 'LOG_CLEAR_MOD');
			}
		}

		// Sorting
		$limit_days = array(0 => $user->lang['ALL_ENTRIES'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);
		$sort_by_text = array('u' => $user->lang['SORT_USERNAME'], 't' => $user->lang['SORT_DATE'], 'i' => $user->lang['SORT_IP'], 'o' => $user->lang['SORT_ACTION']);
		$sort_by_sql = array('u' => 'u.username_clean', 't' => 'l.log_time', 'i' => 'l.log_ip', 'o' => 'l.log_operation');

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

		// Define where and sort sql for use in displaying logs
		$sql_where = ($sort_days) ? (time() - ($sort_days * 86400)) : 0;
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		// Grab log data
		$log_data = array();
		$log_count = 0;
		view_log('mod', $log_data, $log_count, $config['topics_per_page'], $start, $forum_id, $topic_id, 0, $sql_where, $sql_sort);

		$template->assign_vars(array(
			'PAGE_NUMBER'		=> on_page($log_count, $config['topics_per_page'], $start),
			'TOTAL'				=> ($log_count == 1) ? $user->lang['TOTAL_LOG'] : sprintf($user->lang['TOTAL_LOGS'], $log_count),
			'PAGINATION'		=> generate_pagination($this->u_action . "&amp;$u_sort_param", $log_count, $config['topics_per_page'], $start),

			'L_TITLE'			=> $user->lang['MCP_LOGS'],

			'U_POST_ACTION'			=> $this->u_action,
			'S_CLEAR_ALLOWED'		=> ($auth->acl_get('a_clearlogs')) ? true : false,
			'S_SELECT_SORT_DIR'		=> $s_sort_dir,
			'S_SELECT_SORT_KEY'		=> $s_sort_key,
			'S_SELECT_SORT_DAYS'	=> $s_limit_days,
			'S_LOGS'				=> ($log_count > 0),
			)
		);

		foreach ($log_data as $row)
		{
			$data = array();
				
			$checks = array('viewtopic', 'viewforum');
			foreach ($checks as $check)
			{
				if (isset($row[$check]) && $row[$check])
				{
					$data[] = '<a href="' . $row[$check] . '">' . $user->lang['LOGVIEW_' . strtoupper($check)] . '</a>';
				}
			}

			$template->assign_block_vars('log', array(
				'USERNAME'		=> $row['username_full'],
				'IP'			=> $row['ip'],
				'DATE'			=> $user->format_date($row['time']),
				'ACTION'		=> $row['action'],
				'DATA'			=> (sizeof($data)) ? implode(' | ', $data) : '',
				'ID'			=> $row['id'],
				)
			);
		}
	}
}

?>