<?php
/** 
*
* @package mcp
* @version $Id: mcp_topic.php,v 1.36 2006/11/21 18:14:57 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* View topic in MCP
*/
function mcp_topic_view($id, $mode, $action)
{
	global $phpEx, $phpbb_root_path, $config;
	global $template, $db, $user, $auth;

	$url = append_sid("{$phpbb_root_path}mcp.$phpEx?" . extra_url());

	$user->add_lang('viewtopic');

	$topic_id = request_var('t', 0);
	$topic_info = get_topic_data(array($topic_id));

	if (!sizeof($topic_info))
	{
		trigger_error($user->lang['TOPIC_NOT_EXIST']);
	}

	$topic_info = $topic_info[$topic_id];

	// Set up some vars
	$icon_id		= request_var('icon', 0);
	$subject		= utf8_normalize_nfc(request_var('subject', '', true));
	$start			= request_var('start', 0);
	$to_topic_id	= request_var('to_topic_id', 0);
	$to_forum_id	= request_var('to_forum_id', 0);
	$post_id_list	= request_var('post_id_list', array(0));
	
	// Split Topic?
	if ($action == 'split_all' || $action == 'split_beyond')
	{
		split_topic($action, $topic_id, $to_forum_id, $subject);
		$action = 'split';
	}

	// Merge Posts?
	if ($action == 'merge_posts')
	{
		merge_posts($topic_id, $to_topic_id);
		$action = 'merge';
	}

	if ($action == 'split' && !$subject)
	{
		$subject = $topic_info['topic_title'];
	}

	// Jumpbox, sort selects and that kind of things
	make_jumpbox($url . "&amp;i=$id&amp;mode=forum_view", $topic_info['forum_id'], false, 'm_');
	$where_sql = ($action == 'reports') ? 'WHERE post_reported = 1 AND ' : 'WHERE';

	$sort_days = $total = 0;
	$sort_key = $sort_dir = '';
	$sort_by_sql = $sort_order_sql = array();
	mcp_sorting('viewtopic', $sort_days, $sort_key, $sort_dir, $sort_by_sql, $sort_order_sql, $total, $topic_info['forum_id'], $topic_id, $where_sql);

	$limit_time_sql = ($sort_days) ? 'AND t.topic_last_post_time >= ' . (time() - ($sort_days * 86400)) : '';

	if ($total == -1)
	{
		$total = $topic_info['topic_replies'] + 1;
	}

	$posts_per_page = max(0, request_var('posts_per_page', intval($config['posts_per_page'])));
	if ($posts_per_page == 0)
	{
		$posts_per_page = $total;
	}

	$sql = 'SELECT u.username, u.user_colour, p.*
		FROM ' . POSTS_TABLE . ' p, ' . USERS_TABLE . ' u
		WHERE ' . (($action == 'reports') ? 'p.post_reported = 1 AND ' : '') . '
			p.topic_id = ' . $topic_id . ' ' .
			((!$auth->acl_get('m_approve', $topic_info['forum_id'])) ? ' AND p.post_approved = 1 ' : '') . '
			AND p.poster_id = u.user_id
		ORDER BY ' . $sort_order_sql;
	$result = $db->sql_query_limit($sql, $posts_per_page, $start);

	$rowset = array();
	$bbcode_bitfield = '';
	while ($row = $db->sql_fetchrow($result))
	{
		$rowset[] = $row;
		$bbcode_bitfield = $bbcode_bitfield | base64_decode($row['bbcode_bitfield']);
	}
	$db->sql_freeresult($result);

	if ($bbcode_bitfield !== '')
	{
		include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx);
		$bbcode = new bbcode(base64_encode($bbcode_bitfield));
	}

	foreach ($rowset as $i => $row)
	{
		$has_unapproved_posts = false;

		$message = $row['post_text'];
		$post_subject = ($row['post_subject'] != '') ? $row['post_subject'] : $topic_info['topic_title'];
		$message = str_replace("\n", '<br />', $message);

		if ($row['bbcode_bitfield'])
		{
			$bbcode->bbcode_second_pass($message, $row['bbcode_uid'], $row['bbcode_bitfield']);
		}

		$message = smiley_text($message);

		if (!$row['post_approved'])
		{
			$has_unapproved_posts = true;
		}

		$template->assign_block_vars('postrow', array(
			'POST_AUTHOR_FULL'		=> get_username_string('full', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),
			'POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),
			'POST_AUTHOR'			=> get_username_string('username', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),
			'U_POST_AUTHOR'			=> get_username_string('profile', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),

			'POST_DATE'		=> $user->format_date($row['post_time']),
			'POST_SUBJECT'	=> $post_subject,
			'MESSAGE'		=> $message,
			'POST_ID'		=> $row['post_id'],
			'RETURN_TOPIC'	=> sprintf($user->lang['RETURN_TOPIC'], '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", 't=' . $topic_id) . '">', '</a>'),

			'MINI_POST_IMG'		=> ($row['post_time'] > $user->data['user_lastvisit'] && $user->data['is_registered']) ? $user->img('icon_post_target_unread', $user->lang['NEW_POST']) : $user->img('icon_post_target', $user->lang['POST']),

			'S_POST_REPORTED'	=> ($row['post_reported']) ? true : false,
			'S_POST_UNAPPROVED'	=> ($row['post_approved']) ? false : true,
			'S_CHECKED'			=> ($post_id_list && in_array(intval($row['post_id']), $post_id_list)) ? true : false,

			'U_POST_DETAILS'	=> "$url&amp;i=$id&amp;p={$row['post_id']}&amp;mode=post_details",
			'U_MCP_APPROVE'		=> ($auth->acl_get('m_approve', $topic_info['forum_id'])) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue&amp;mode=approve_details&amp;f=' . $topic_info['forum_id'] . '&amp;p=' . $row['post_id']) : '',
			'U_MCP_REPORT'		=> ($auth->acl_get('m_report', $topic_info['forum_id'])) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=reports&amp;mode=report_details&amp;f=' . $topic_info['forum_id'] . '&amp;p=' . $row['post_id']) : '')
		);

		unset($rowset[$i]);
	}

	// Display topic icons for split topic
	$s_topic_icons = false;

	if ($auth->acl_get('m_split', $topic_info['forum_id']))
	{
		include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
		$s_topic_icons = posting_gen_topic_icons('', $icon_id);

		// Has the user selected a topic for merge?
		if ($to_topic_id)
		{
			$to_topic_info = get_topic_data(array($to_topic_id), 'm_merge');

			if (!sizeof($to_topic_info))
			{
				$to_topic_id = 0;
			}
			else
			{
				$to_topic_info = $to_topic_info[$to_topic_id];
			}

			if (!$to_topic_info['enable_icons'])
			{
				$s_topic_icons = false;
			}
		}
	}

	$template->assign_vars(array(
		'TOPIC_TITLE'		=> $topic_info['topic_title'],
		'U_VIEW_TOPIC'		=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $topic_info['forum_id'] . '&amp;t=' . $topic_info['topic_id']),

		'TO_TOPIC_ID'		=> $to_topic_id,
		'TO_TOPIC_INFO'		=> ($to_topic_id) ? sprintf($user->lang['YOU_SELECTED_TOPIC'], $to_topic_id, '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $to_topic_info['forum_id'] . '&amp;t=' . $to_topic_id) . '">' . $to_topic_info['topic_title'] . '</a>') : '',

		'SPLIT_SUBJECT'		=> $subject,
		'POSTS_PER_PAGE'	=> $posts_per_page,
		'ACTION'			=> $action,

		'REPORTED_IMG'		=> $user->img('icon_topic_reported', 'POST_REPORTED', false, true),
		'UNAPPROVED_IMG'	=> $user->img('icon_topic_unapproved', 'POST_UNAPPROVED', false, true),

		'S_MCP_ACTION'		=> "$url&amp;i=$id&amp;mode=$mode&amp;action=$action&amp;start=$start",
		'S_FORUM_SELECT'	=> ($to_forum_id) ? make_forum_select($to_forum_id, false, false, true, true, true) : make_forum_select($topic_info['forum_id'], false, false, true, true, true),
		'S_CAN_SPLIT'		=> ($auth->acl_get('m_split', $topic_info['forum_id'])) ? true : false,
		'S_CAN_MERGE'		=> ($auth->acl_get('m_merge', $topic_info['forum_id'])) ? true : false,
		'S_CAN_DELETE'		=> ($auth->acl_get('m_delete', $topic_info['forum_id'])) ? true : false,
		'S_CAN_APPROVE'		=> ($has_unapproved_posts && $auth->acl_get('m_approve', $topic_info['forum_id'])) ? true : false,
		'S_CAN_LOCK'		=> ($auth->acl_get('m_lock', $topic_info['forum_id'])) ? true : false,
		'S_CAN_REPORT'		=> ($auth->acl_get('m_report', $topic_info['forum_id'])) ? true : false,
		'S_REPORT_VIEW'		=> ($action == 'reports') ? true : false,
		'S_MERGE_VIEW'		=> ($action == 'merge') ? true : false,

		'S_SHOW_TOPIC_ICONS'	=> $s_topic_icons,
		'S_TOPIC_ICON'			=> $icon_id,

		'U_SELECT_TOPIC'	=> "$url&amp;i=$id&amp;mode=forum_view&amp;action=merge_select",

		'RETURN_TOPIC'		=> sprintf($user->lang['RETURN_TOPIC'], '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f={$topic_info['forum_id']}&amp;t={$topic_info['topic_id']}&amp;start=$start") . '">', '</a>'),
		'RETURN_FORUM'		=> sprintf($user->lang['RETURN_FORUM'], '<a href="' . append_sid("{$phpbb_root_path}viewforum.$phpEx", "f={$topic_info['forum_id']}&amp;start=$start") . '">', '</a>'),

		'PAGE_NUMBER'		=> on_page($total, $posts_per_page, $start),
		'PAGINATION'		=> (!$posts_per_page) ? '' : generate_pagination(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=$id&amp;t={$topic_info['topic_id']}&amp;mode=$mode&amp;action=$action&amp;to_topic_id=$to_topic_id&amp;posts_per_page=$posts_per_page&amp;st=$sort_days&amp;sk=$sort_key&amp;sd=$sort_dir"), $total, $posts_per_page, $start),
		'TOTAL'				=> $total)
	);
}

/**
* Split topic
*/
function split_topic($action, $topic_id, $to_forum_id, $subject)
{
	global $db, $template, $user, $phpEx, $phpbb_root_path, $auth;

	$post_id_list	= request_var('post_id_list', array(0));
	$forum_id		= request_var('forum_id', 0);
	$start			= request_var('start', 0);

	if (!sizeof($post_id_list))
	{
		$template->assign_var('MESSAGE', $user->lang['NO_POST_SELECTED']);
		return;
	}

	if (!check_ids($post_id_list, POSTS_TABLE, 'post_id', array('m_split')))
	{
		return;
	}

	$post_id = $post_id_list[0];
	$post_info = get_post_data(array($post_id));

	if (!sizeof($post_info))
	{
		$template->assign_var('MESSAGE', $user->lang['NO_POST_SELECTED']);
		return;
	}

	$post_info = $post_info[$post_id];
	$subject = trim($subject);

	// Make some tests
	if (!$subject)
	{
		$template->assign_var('MESSAGE', $user->lang['EMPTY_SUBJECT']);
		return;
	}

	if ($to_forum_id <= 0)
	{
		$template->assign_var('MESSAGE', $user->lang['NO_DESTINATION_FORUM']);
		return;
	}

	$forum_info = get_forum_data(array($to_forum_id), 'm_split');

	if (!sizeof($forum_info))
	{
		$template->assign_var('MESSAGE', $user->lang['NOT_MODERATOR']);
		return;
	}

	$forum_info = $forum_info[$to_forum_id];

	if ($forum_info['forum_type'] != FORUM_POST)
	{
		$template->assign_var('MESSAGE', $user->lang['FORUM_NOT_POSTABLE']);
		return;
	}

	$redirect = request_var('redirect', $user->data['session_page']);

	$s_hidden_fields = build_hidden_fields(array(
		'i'				=> 'main',
		'post_id_list'	=> $post_id_list,
		'f'				=> $forum_id,
		'mode'			=> 'topic_view',
		'start'			=> $start,
		'action'		=> $action,
		't'				=> $topic_id,
		'redirect'		=> $redirect,
		'subject'		=> $subject,
		'to_forum_id'	=> $to_forum_id,
		'icon'			=> request_var('icon', 0))
	);
	$success_msg = $return_link = '';

	if (confirm_box(true))
	{
		if ($action == 'split_beyond')
		{
			$sort_days = $total = 0;
			$sort_key = $sort_dir = '';
			$sort_by_sql = $sort_order_sql = array();
			mcp_sorting('viewtopic', $sort_days, $sort_key, $sort_dir, $sort_by_sql, $sort_order_sql, $total, $forum_id, $topic_id);

			$limit_time_sql = ($sort_days) ? 'AND t.topic_last_post_time >= ' . (time() - ($sort_days * 86400)) : '';

			if ($sort_order_sql[0] == 'u')
			{
				$sql = 'SELECT p.post_id, p.forum_id, p.post_approved
					FROM ' . POSTS_TABLE . ' p, ' . USERS_TABLE . " u
					WHERE p.topic_id = $topic_id
						AND p.poster_id = u.user_id
						$limit_time_sql
					ORDER BY $sort_order_sql";
			}
			else
			{
				$sql = 'SELECT p.post_id, p.forum_id, p.post_approved
					FROM ' . POSTS_TABLE . " p
					WHERE p.topic_id = $topic_id
						$limit_time_sql
					ORDER BY $sort_order_sql";
			}
			$result = $db->sql_query_limit($sql, 0, $start);

			$store = false;
			$post_id_list = array();
			while ($row = $db->sql_fetchrow($result))
			{
				// If splitted from selected post (split_beyond), we split the unapproved items too.
				if (!$row['post_approved'] && !$auth->acl_get('m_approve', $row['forum_id']))
				{
//					continue;
				}

				// Start to store post_ids as soon as we see the first post that was selected
				if ($row['post_id'] == $post_id)
				{
					$store = true;
				}

				if ($store)
				{
					$post_id_list[] = $row['post_id'];
				}
			}
			$db->sql_freeresult($result);
		}

		if (!sizeof($post_id_list))
		{
			trigger_error($user->lang['NO_POST_SELECTED']);
		}

		$icon_id = request_var('icon', 0);

		$sql_ary = array(
			'forum_id'		=> $to_forum_id,
			'topic_title'	=> $subject,
			'icon_id'		=> $icon_id,
			'topic_approved'=> 1
		);

		$sql = 'INSERT INTO ' . TOPICS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);

		$to_topic_id = $db->sql_nextid();
		move_posts($post_id_list, $to_topic_id);

		// Change topic title of first post
		$sql = 'UPDATE ' . POSTS_TABLE . "
			SET post_subject = '" . $db->sql_escape($subject) . "'
			WHERE post_id = {$post_id_list[0]}";
		$db->sql_query($sql);

		$success_msg = 'TOPIC_SPLIT_SUCCESS';

		// Link back to both topics
		$return_link = sprintf($user->lang['RETURN_TOPIC'], '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $post_info['forum_id'] . '&amp;t=' . $post_info['topic_id']) . '">', '</a>') . '<br /><br />' . sprintf($user->lang['RETURN_NEW_TOPIC'], '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $to_forum_id . '&amp;t=' . $to_topic_id) . '">', '</a>');
	}
	else
	{
		confirm_box(false, ($action == 'split_all') ? 'SPLIT_TOPIC_ALL' : 'SPLIT_TOPIC_BEYOND', $s_hidden_fields);
	}

	$redirect = request_var('redirect', "index.$phpEx");
	$redirect = reapply_sid($redirect);

	if (!$success_msg)
	{
		return;
	}
	else
	{
		meta_refresh(3, append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$to_forum_id&amp;t=$to_topic_id"));
		trigger_error($user->lang[$success_msg] . '<br /><br />' . $return_link);
	}
}

/**
* Merge selected posts into selected topic
*/
function merge_posts($topic_id, $to_topic_id)
{
	global $db, $template, $user, $phpEx, $phpbb_root_path, $auth;

	if (!$to_topic_id)
	{
		$template->assign_var('MESSAGE', $user->lang['NO_FINAL_TOPIC_SELECTED']);
		return;
	}

	$topic_data = get_topic_data(array($to_topic_id), 'm_merge');

	if (!sizeof($topic_data))
	{
		$template->assign_var('MESSAGE', $user->lang['NO_FINAL_TOPIC_SELECTED']);
		return;
	}

	$topic_data = $topic_data[$to_topic_id];

	$post_id_list	= request_var('post_id_list', array(0));
	$start			= request_var('start', 0);

	if (!sizeof($post_id_list))
	{
		$template->assign_var('MESSAGE', $user->lang['NO_POST_SELECTED']);
		return;
	}

	if (!check_ids($post_id_list, POSTS_TABLE, 'post_id', array('m_merge')))
	{
		return;
	}

	$redirect = request_var('redirect', $user->data['session_page']);

	$s_hidden_fields = build_hidden_fields(array(
		'i'				=> 'main',
		'post_id_list'	=> $post_id_list,
		'to_topic_id'	=> $to_topic_id,
		'mode'			=> 'topic_view',
		'action'		=> 'merge_posts',
		'start'			=> $start,
		'redirect'		=> $redirect,
		't'				=> $topic_id)
	);
	$success_msg = $return_link = '';

	if (confirm_box(true))
	{
		$to_forum_id = $topic_data['forum_id'];

		move_posts($post_id_list, $to_topic_id);
		add_log('mod', $to_forum_id, $to_topic_id, 'LOG_MERGE', $topic_data['topic_title']);

		// Message and return links
		$success_msg = 'POSTS_MERGED_SUCCESS';

		// Does the original topic still exist? If yes, link back to it
		$topic_data = get_topic_data(array($topic_id));

		if (sizeof($topic_data))
		{
			$return_link .= sprintf($user->lang['RETURN_TOPIC'], '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $topic_data['forum_id'] . '&amp;t=' . $topic_id) . '">', '</a>');
		}

		// Link to the new topic
		$return_link .= (($return_link) ? '<br /><br />' : '') . sprintf($user->lang['RETURN_NEW_TOPIC'], '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $to_forum_id . '&amp;t=' . $to_topic_id) . '">', '</a>');
	}
	else
	{
		confirm_box(false, 'MERGE_POSTS', $s_hidden_fields);
	}

	$redirect = request_var('redirect', "index.$phpEx");
	$redirect = reapply_sid($redirect);

	if (!$success_msg)
	{
		return;
	}
	else
	{
		meta_refresh(3, append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$to_forum_id&amp;t=$to_topic_id"));
		trigger_error($user->lang[$success_msg] . '<br /><br />' . $return_link);
	}
}

?>