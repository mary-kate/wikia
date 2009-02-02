<?php
/** 
*
* @package mcp
* @version $Id: mcp_queue.php,v 1.62 2006/11/26 14:55:17 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* mcp_queue
* Handling the moderation queue
* @package mcp
*/
class mcp_queue
{
	var $p_master;
	var $u_action;

	function mcp_main(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($id, $mode)
	{
		global $auth, $db, $user, $template;
		global $config, $phpbb_root_path, $phpEx, $action;

		include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

		$forum_id = request_var('f', 0);
		$start = request_var('start', 0);

		$this->page_title = 'MCP_QUEUE';

		switch ($action)
		{
			case 'approve':
			case 'disapprove':
				include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

				$post_id_list = request_var('post_id_list', array(0));

				if (!sizeof($post_id_list))
				{
					trigger_error('NO_POST_SELECTED');
				}

				if ($action == 'approve')
				{
					approve_post($post_id_list, $mode);
				}
				else
				{
					disapprove_post($post_id_list, $mode);
				}

			break;
		}

		switch ($mode)
		{
			case 'approve_details':

				$user->add_lang('posting');

				$post_id = request_var('p', 0);
				$topic_id = request_var('t', 0);

				if ($topic_id)
				{
					$topic_info = get_topic_data(array($topic_id), 'm_approve');
					if (isset($topic_info[$topic_id]['topic_first_post_id']))
					{
						$post_id = (int) $topic_info[$topic_id]['topic_first_post_id'];
					}
					else
					{
						$topic_id = 0;
					}
				}

				$post_info = get_post_data(array($post_id), 'm_approve');

				if (!sizeof($post_info))
				{
					trigger_error('NO_POST_SELECTED');
				}

				$post_info = $post_info[$post_id];

				if ($post_info['topic_first_post_id'] != $post_id && topic_review($post_info['topic_id'], $post_info['forum_id'], 'topic_review', 0, false))
				{
					$template->assign_vars(array(
						'S_TOPIC_REVIEW'	=> true,
						'TOPIC_TITLE'		=> $post_info['topic_title'])
					);
				}

				// Process message, leave it uncensored
				$message = $post_info['post_text'];
				$message = str_replace("\n", '<br />', $message);
				if ($post_info['bbcode_bitfield'])
				{
					include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx);
					$bbcode = new bbcode($post_info['bbcode_bitfield']);
					$bbcode->bbcode_second_pass($message, $post_info['bbcode_uid'], $post_info['bbcode_bitfield']);
				}
				$message = smiley_text($message);

				$template->assign_vars(array(
					'S_MCP_QUEUE'			=> true,
					'S_APPROVE_ACTION'		=> append_sid("{$phpbb_root_path}mcp.$phpEx", "i=queue&amp;p=$post_id&amp;f=$forum_id"),
					'S_CAN_VIEWIP'			=> $auth->acl_get('m_info', $post_info['forum_id']),
					'S_POST_REPORTED'		=> $post_info['post_reported'],
					'S_POST_UNAPPROVED'		=> !$post_info['post_approved'],
					'S_POST_LOCKED'			=> $post_info['post_edit_locked'],
					'S_USER_NOTES'			=> true,

					'U_EDIT'				=> ($auth->acl_get('m_edit', $post_info['forum_id'])) ? append_sid("{$phpbb_root_path}posting.$phpEx", "mode=edit&amp;f={$post_info['forum_id']}&amp;p={$post_info['post_id']}") : '',
					'U_MCP_APPROVE'			=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue&amp;mode=approve_details&amp;f=' . $post_info['forum_id'] . '&amp;p=' . $post_id),
					'U_MCP_REPORT'			=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=reports&amp;mode=report_details&amp;f=' . $post_info['forum_id'] . '&amp;p=' . $post_id),
					'U_MCP_USER_NOTES'		=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=notes&amp;mode=user_notes&amp;u=' . $post_info['user_id']),
					'U_MCP_WARN_USER'		=> ($auth->acl_getf_global('m_warn')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=warn&amp;mode=warn_user&amp;u=' . $post_info['user_id']) : '',
					'U_VIEW_POST'			=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $post_info['forum_id'] . '&amp;p=' . $post_info['post_id'] . '#p' . $post_info['post_id']),
					'U_VIEW_TOPIC'			=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $post_info['forum_id'] . '&amp;t=' . $post_info['topic_id']),

					'RETURN_QUEUE'			=> sprintf($user->lang['RETURN_QUEUE'], '<a href="' . append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue' . (($topic_id) ? '&amp;mode=unapproved_topics' : '&amp;mode=unapproved_posts')) . "&amp;start=$start\">", '</a>'),
					'REPORTED_IMG'			=> $user->img('icon_topic_reported', $user->lang['POST_REPORTED']),
					'UNAPPROVED_IMG'		=> $user->img('icon_topic_unapproved', $user->lang['POST_UNAPPROVED']),
					'EDIT_IMG'				=> $user->img('icon_post_edit', $user->lang['EDIT_POST']),

					'POST_AUTHOR_FULL'		=> get_username_string('full', $post_info['user_id'], $post_info['username'], $post_info['user_colour'], $post_info['post_username']),
					'POST_AUTHOR_COLOUR'	=> get_username_string('colour', $post_info['user_id'], $post_info['username'], $post_info['user_colour'], $post_info['post_username']),
					'POST_AUTHOR'			=> get_username_string('username', $post_info['user_id'], $post_info['username'], $post_info['user_colour'], $post_info['post_username']),
					'U_POST_AUTHOR'			=> get_username_string('profile', $post_info['user_id'], $post_info['username'], $post_info['user_colour'], $post_info['post_username']),

					'POST_PREVIEW'			=> $message,
					'POST_SUBJECT'			=> $post_info['post_subject'],
					'POST_DATE'				=> $user->format_date($post_info['post_time']),
					'POST_IP'				=> $post_info['poster_ip'],
					'POST_IPADDR'			=> @gethostbyaddr($post_info['poster_ip']),
					'POST_ID'				=> $post_info['post_id'])
				);

				$this->tpl_name = 'mcp_post';

			break;

			case 'unapproved_topics':
			case 'unapproved_posts':
				$topic_id = request_var('t', 0);
				$forum_info = array();

				if ($topic_id)
				{
					$topic_info = get_topic_data(array($topic_id));

					if (!sizeof($topic_info))
					{
						trigger_error($user->lang['TOPIC_NOT_EXIST']);
					}

					$topic_info = $topic_info[$topic_id];
					$forum_id = $topic_info['forum_id'];
				}

				$forum_list_approve = get_forum_list('m_approve', false, true);

				if (!$forum_id)
				{
					$forum_list = array();
					foreach ($forum_list_approve as $row)
					{
						$forum_list[] = $row['forum_id'];
					}

					if (!sizeof($forum_list))
					{
						trigger_error('NOT_MODERATOR');
					}

					$global_id = $forum_list[0];

					$forum_list = implode(', ', $forum_list);

					$sql = 'SELECT SUM(forum_topics) as sum_forum_topics
						FROM ' . FORUMS_TABLE . "
						WHERE forum_id IN (0, $forum_list)";
					$result = $db->sql_query($sql);
					$forum_info['forum_topics'] = (int) $db->sql_fetchfield('sum_forum_topics');
					$db->sql_freeresult($result);
				}
				else
				{
					$forum_info = get_forum_data(array($forum_id), 'm_approve');

					if (!sizeof($forum_info))
					{
						trigger_error('NOT_MODERATOR');
					}

					$forum_info = $forum_info[$forum_id];
					$forum_list = $forum_id;
					$global_id = $forum_id;
				}

				$forum_options = '<option value="0"' . (($forum_id == 0) ? ' selected="selected"' : '') . '>' . $user->lang['ALL_FORUMS'] . '</option>';
				foreach ($forum_list_approve as $row)
				{
					$forum_options .= '<option value="' . $row['forum_id'] . '"' . (($forum_id == $row['forum_id']) ? ' selected="selected"' : '') . '>' . $row['forum_name'] . '</option>';
				}

				$sort_days = $total = 0;
				$sort_key = $sort_dir = '';
				$sort_by_sql = $sort_order_sql = array();
				mcp_sorting($mode, $sort_days, $sort_key, $sort_dir, $sort_by_sql, $sort_order_sql, $total, $forum_id, $topic_id);

				$forum_topics = ($total == -1) ? $forum_info['forum_topics'] : $total;
				$limit_time_sql = ($sort_days) ? 'AND t.topic_last_post_time >= ' . (time() - ($sort_days * 86400)) : '';

				$forum_names = array();

				if ($mode == 'unapproved_posts')
				{
					$sql = 'SELECT p.post_id
						FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t' . (($sort_order_sql[0] == 'u') ? ', ' . USERS_TABLE . ' u' : '') . "
						WHERE p.forum_id IN (0, $forum_list)
							AND p.post_approved = 0
							" . (($sort_order_sql[0] == 'u') ? 'AND u.user_id = p.poster_id' : '') . '
							' . (($topic_id) ? 'AND p.topic_id = ' . $topic_id : '') . "
							AND t.topic_id = p.topic_id
							AND t.topic_first_post_id <> p.post_id
							$limit_time_sql
						ORDER BY $sort_order_sql";
					$result = $db->sql_query_limit($sql, $config['topics_per_page'], $start);

					$i = 0;
					$post_ids = array();
					while ($row = $db->sql_fetchrow($result))
					{
						$post_ids[] = $row['post_id'];
						$row_num[$row['post_id']] = $i++;
					}
					$db->sql_freeresult($result);

					if (sizeof($post_ids))
					{
						$sql = 'SELECT t.topic_id, t.topic_title, t.forum_id, p.post_id, p.post_subject, p.post_username, p.poster_id, p.post_time, u.username, u.user_colour
							FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t, ' . USERS_TABLE . ' u
							WHERE ' . $db->sql_in_set('p.post_id', $post_ids) . '
								AND t.topic_id = p.topic_id
								AND u.user_id = p.poster_id';
						$result = $db->sql_query($sql);

						$post_data = $rowset = array();
						while ($row = $db->sql_fetchrow($result))
						{
							if ($row['forum_id'])
							{
								$forum_names[] = $row['forum_id'];
							}
							$post_data[$row['post_id']] = $row;
						}
						$db->sql_freeresult($result);

						foreach ($post_ids as $post_id)
						{
							$rowset[] = $post_data[$post_id];
						}
						unset($post_data, $post_ids);
					}
					else
					{
						$rowset = array();
					}
				}
				else
				{
					$sql = 'SELECT t.forum_id, t.topic_id, t.topic_title, t.topic_title AS post_subject, t.topic_time AS post_time, t.topic_poster AS poster_id, t.topic_first_post_id AS post_id, t.topic_first_poster_name AS username, t.topic_first_poster_colour AS user_colour
						FROM ' . TOPICS_TABLE . " t
						WHERE forum_id IN (0, $forum_list)
							AND topic_approved = 0
							$limit_time_sql
						ORDER BY $sort_order_sql";
					$result = $db->sql_query_limit($sql, $config['topics_per_page'], $start);

					$rowset = array();
					while ($row = $db->sql_fetchrow($result))
					{
						if ($row['forum_id'])
						{
							$forum_names[] = $row['forum_id'];
						}
						$rowset[] = $row;
					}
					$db->sql_freeresult($result);
				}

				if (sizeof($forum_names))
				{
					// Select the names for the forum_ids
					$sql = 'SELECT forum_id, forum_name
						FROM ' . FORUMS_TABLE . '
						WHERE ' . $db->sql_in_set('forum_id', $forum_names);
					$result = $db->sql_query($sql, 3600);

					$forum_names = array();
					while ($row = $db->sql_fetchrow($result))
					{
						$forum_names[$row['forum_id']] = $row['forum_name'];
					}
					$db->sql_freeresult($result);
				}

				foreach ($rowset as $row)
				{
					$global_topic = ($row['forum_id']) ? false : true;
					if ($global_topic)
					{
						$row['forum_id'] = $global_id;
					}

					if (empty($row['post_username']))
					{
						$row['post_username'] = $user->lang['GUEST'];
					}

					$template->assign_block_vars('postrow', array(
						'U_VIEWFORUM'		=> (!$global_topic) ? append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $row['forum_id']) : '',
						'U_VIEWPOST'		=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $row['forum_id'] . '&amp;p=' . $row['post_id']) . (($mode == 'unapproved_posts') ? '#p' . $row['post_id'] : ''),
						'U_VIEW_DETAILS'	=> append_sid("{$phpbb_root_path}mcp.$phpEx", "i=queue&amp;start=$start&amp;mode=approve_details&amp;f={$row['forum_id']}&amp;p={$row['post_id']}" . (($mode == 'unapproved_topics') ? "&amp;t={$row['topic_id']}" : '')),

						'POST_AUTHOR_FULL'		=> get_username_string('full', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),
						'POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),
						'POST_AUTHOR'			=> get_username_string('username', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),
						'U_POST_AUTHOR'			=> get_username_string('profile', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),

						'POST_ID'		=> $row['post_id'],
						'FORUM_NAME'	=> (!$global_topic) ? $forum_names[$row['forum_id']] : $user->lang['GLOBAL_ANNOUNCEMENT'],
						'POST_SUBJECT'	=> $row['post_subject'],
						'POST_TIME'		=> $user->format_date($row['post_time']))
					);
				}
				unset($rowset, $forum_names);

				// Now display the page
				$template->assign_vars(array(
					'L_DISPLAY_ITEMS'		=> ($mode == 'unapproved_posts') ? $user->lang['DISPLAY_POSTS'] : $user->lang['DISPLAY_TOPICS'],
					'L_EXPLAIN'				=> ($mode == 'unapproved_posts') ? $user->lang['MCP_QUEUE_UNAPPROVED_POSTS_EXPLAIN'] : $user->lang['MCP_QUEUE_UNAPPROVED_TOPICS_EXPLAIN'],
					'L_TITLE'				=> ($mode == 'unapproved_posts') ? $user->lang['MCP_QUEUE_UNAPPROVED_POSTS'] : $user->lang['MCP_QUEUE_UNAPPROVED_TOPICS'],
					'L_ONLY_TOPIC'			=> ($topic_id) ? sprintf($user->lang['ONLY_TOPIC'], $topic_info['topic_title']) : '',

					'S_FORUM_OPTIONS'		=> $forum_options,
					'S_MCP_ACTION'			=> build_url(array('t', 'f', 'sd', 'st', 'sk')),
					'S_TOPICS'				=> ($mode == 'unapproved_posts') ? false : true,

					'PAGINATION'			=> generate_pagination($this->u_action . "&amp;f=$forum_id", $total, $config['topics_per_page'], $start),
					'PAGE_NUMBER'			=> on_page($total, $config['topics_per_page'], $start),
					'TOPIC_ID'				=> $topic_id,
					'TOTAL'					=> $total)
				);

				$this->tpl_name = 'mcp_queue';
			break;
		}
	}
}

/**
* Approve Post/Topic
*/
function approve_post($post_id_list, $mode)
{
	global $db, $template, $user, $config;
	global $phpEx, $phpbb_root_path;

	if (!check_ids($post_id_list, POSTS_TABLE, 'post_id', array('m_approve')))
	{
		trigger_error('NOT_AUTHORIZED');
	}

	$redirect = request_var('redirect', build_url(array('_f_')));
	$success_msg = '';

	$s_hidden_fields = build_hidden_fields(array(
		'i'				=> 'queue',
		'mode'			=> $mode,
		'post_id_list'	=> $post_id_list,
		'action'		=> 'approve',
		'redirect'		=> $redirect)
	);

	if (confirm_box(true))
	{
		$notify_poster = (isset($_REQUEST['notify_poster'])) ? true : false;

		$post_info = get_post_data($post_id_list, 'm_approve');

		// If Topic -> total_topics = total_topics+1, total_posts = total_posts+1, forum_topics = forum_topics+1, forum_posts = forum_posts+1
		// If Post -> total_posts = total_posts+1, forum_posts = forum_posts+1, topic_replies = topic_replies+1

		$total_topics = $total_posts = 0;
		$forum_topics_posts = $topic_approve_sql = $topic_replies_sql = $post_approve_sql = $topic_id_list = $forum_id_list = array();

		$update_forum_information = false;

		foreach ($post_info as $post_id => $post_data)
		{
			$topic_id_list[$post_data['topic_id']] = 1;

			if ($post_data['forum_id'])
			{
				$forum_id_list[$post_data['forum_id']] = 1;
			}

			// Topic or Post. ;)
			if ($post_data['topic_first_post_id'] == $post_id)
			{
				if ($post_data['forum_id'])
				{
					if (!isset($forum_topics_posts[$post_data['forum_id']]))
					{
						$forum_topics_posts[$post_data['forum_id']] = array(
							'forum_posts'	=> 0,
							'forum_topics'	=> 0
						);
					}

					$total_topics++;
					$forum_topics_posts[$post_data['forum_id']]['forum_topics']++;
				}

				$topic_approve_sql[] = $post_data['topic_id'];
			}
			else
			{
				if (!isset($topic_replies_sql[$post_data['topic_id']]))
				{
					$topic_replies_sql[$post_data['topic_id']] = 0;
				}
				$topic_replies_sql[$post_data['topic_id']]++;
			}

			if ($post_data['forum_id'])
			{
				if (!isset($forum_topics_posts[$post_data['forum_id']]))
				{
					$forum_topics_posts[$post_data['forum_id']] = array(
						'forum_posts'	=> 0,
						'forum_topics'	=> 0
					);
				}

				$total_posts++;
				$forum_topics_posts[$post_data['forum_id']]['forum_posts']++;
			}

			$post_approve_sql[] = $post_id;

			// If the post is newer than the last post information stored we need to update the forum information
			if ($post_data['post_time'] >= $post_data['forum_last_post_time'])
			{
				$update_forum_information = true;
			}
		}

		if (sizeof($topic_approve_sql))
		{
			$sql = 'UPDATE ' . TOPICS_TABLE . '
				SET topic_approved = 1
				WHERE ' . $db->sql_in_set('topic_id', $topic_approve_sql);
			$db->sql_query($sql);
		}

		if (sizeof($post_approve_sql))
		{
			$sql = 'UPDATE ' . POSTS_TABLE . '
				SET post_approved = 1
				WHERE ' . $db->sql_in_set('post_id', $post_approve_sql);
			$db->sql_query($sql);
		}

		if (sizeof($topic_replies_sql))
		{
			foreach ($topic_replies_sql as $topic_id => $num_replies)
			{
				$sql = 'UPDATE ' . TOPICS_TABLE . "
					SET topic_replies = topic_replies + $num_replies
					WHERE topic_id = $topic_id";
				$db->sql_query($sql);
			}
		}

		if (sizeof($forum_topics_posts))
		{
			foreach ($forum_topics_posts as $forum_id => $row)
			{
				$sql = 'UPDATE ' . FORUMS_TABLE . '
					SET ';
				$sql .= ($row['forum_topics']) ? "forum_topics = forum_topics + {$row['forum_topics']}" : '';
				$sql .= ($row['forum_topics'] && $row['forum_posts']) ? ', ' : '';
				$sql .= ($row['forum_posts']) ? "forum_posts = forum_posts + {$row['forum_posts']}" : '';
				$sql .= " WHERE forum_id = $forum_id";

				$db->sql_query($sql);
			}
		}

		if ($total_topics)
		{
			set_config('num_topics', $config['num_topics'] + $total_topics, true);
		}

		if ($total_posts)
		{
			set_config('num_posts', $config['num_posts'] + $total_posts, true);
		}
		unset($topic_approve_sql, $topic_replies_sql, $post_approve_sql);

		update_post_information('topic', array_keys($topic_id_list));

		if ($update_forum_information)
		{
			update_post_information('forum', array_keys($forum_id_list));
		}
		unset($topic_id_list, $forum_id_list);

		$messenger = new messenger();

		// Notify Poster?
		if ($notify_poster)
		{
			foreach ($post_info as $post_id => $post_data)
			{
				if ($post_data['poster_id'] == ANONYMOUS)
				{
					continue;
				}

				$email_template = ($post_data['post_id'] == $post_data['topic_first_post_id'] && $post_data['post_id'] == $post_data['topic_last_post_id']) ? 'topic_approved' : 'post_approved';

				$messenger->template($email_template, $post_data['user_lang']);

				$messenger->replyto($config['board_email']);
				$messenger->to($post_data['user_email'], $post_data['username']);
				$messenger->im($post_data['user_jabber'], $post_data['username']);

				$messenger->assign_vars(array(
					'USERNAME'		=> htmlspecialchars_decode($post_data['username']),
					'POST_SUBJECT'	=> htmlspecialchars_decode(censor_text($post_data['post_subject'])),
					'TOPIC_TITLE'	=> htmlspecialchars_decode(censor_text($post_data['topic_title'])),

					'U_VIEW_TOPIC'	=> generate_board_url() . "/viewtopic.$phpEx?f={$post_data['forum_id']}&t={$post_data['topic_id']}&e=0",
					'U_VIEW_POST'	=> generate_board_url() . "/viewtopic.$phpEx?f={$post_data['forum_id']}&t={$post_data['topic_id']}&p=$post_id&e=$post_id")
				);

				$messenger->send($post_data['user_notify_type']);
			}
		}

		$messenger->save_queue();

		// Send out normal user notifications
		$email_sig = str_replace('<br />', "\n", "-- \n" . $config['board_email_sig']);

		foreach ($post_info as $post_id => $post_data)
		{
			if ($post_id == $post_data['topic_first_post_id'] && $post_id == $post_data['topic_last_post_id'])
			{
				// Forum Notifications
				user_notification('post', $post_data['topic_title'], $post_data['topic_title'], $post_data['forum_name'], $post_data['forum_id'], $post_data['topic_id'], $post_id);
			}
			else
			{
				// Topic Notifications
				user_notification('reply', $post_data['post_subject'], $post_data['topic_title'], $post_data['forum_name'], $post_data['forum_id'], $post_data['topic_id'], $post_id);
			}
		}
		unset($post_info);

		if ($total_topics)
		{
			$success_msg = ($total_topics == 1) ? 'TOPIC_APPROVED_SUCCESS' : 'TOPICS_APPROVED_SUCCESS';
		}
		else
		{
			$success_msg = (sizeof($post_id_list) == 1) ? 'POST_APPROVED_SUCCESS' : 'POSTS_APPROVED_SUCCESS';
		}
	}
	else
	{
		$template->assign_vars(array(
			'S_NOTIFY_POSTER'	=> true,
			'S_APPROVE'			=> true)
		);

		confirm_box(false, 'APPROVE_POST' . ((sizeof($post_id_list) == 1) ? '' : 'S'), $s_hidden_fields, 'mcp_approve.html');
	}

	$redirect = request_var('redirect', "index.$phpEx");
	$redirect = reapply_sid($redirect);

	if (!$success_msg)
	{
		redirect($redirect);
	}
	else
	{
		meta_refresh(3, $redirect);
		trigger_error($user->lang[$success_msg] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], "<a href=\"$redirect\">", '</a>'));
	}
}

/**
* Disapprove Post/Topic
*/
function disapprove_post($post_id_list, $mode)
{
	global $db, $template, $user, $config;
	global $phpEx, $phpbb_root_path;

	if (!check_ids($post_id_list, POSTS_TABLE, 'post_id', array('m_approve')))
	{
		trigger_error('NOT_AUTHORIZED');
	}

	$redirect = request_var('redirect', build_url(array('t', 'mode', '_f_')) . '&amp;mode=unapproved_topics');
	$reason = request_var('reason', '', true);
	$reason_id = request_var('reason_id', 0);
	$success_msg = $additional_msg = '';

	$s_hidden_fields = build_hidden_fields(array(
		'i'				=> 'queue',
		'mode'			=> $mode,
		'post_id_list'	=> $post_id_list,
		'action'		=> 'disapprove',
		'redirect'		=> $redirect)
	);

	$notify_poster = (isset($_REQUEST['notify_poster'])) ? true : false;
	$disapprove_reason = '';

	if ($reason_id)
	{
		$sql = 'SELECT reason_title, reason_description
			FROM ' . REPORTS_REASONS_TABLE . "
			WHERE reason_id = $reason_id";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$row || (!$reason && strtolower($row['reason_title']) == 'other'))
		{
			$additional_msg = $user->lang['NO_REASON_DISAPPROVAL'];
			unset($_POST['confirm']);
		}
		else
		{
			// If the reason is defined within the language file, we will use the localized version, else just use the database entry...
			$disapprove_reason = (strtolower($row['reason_title']) != 'other') ? ((isset($user->lang['report_reasons']['DESCRIPTION'][strtoupper($row['reason_title'])])) ? $user->lang['report_reasons']['DESCRIPTION'][strtoupper($row['reason_title'])] : $row['reason_description']) : '';
			$disapprove_reason .= ($reason) ? "\n\n" . $reason : '';
		}
	}

	if (confirm_box(true))
	{
		$post_info = get_post_data($post_id_list, 'm_approve');

		// If Topic -> forum_topics_real -= 1
		// If Post -> topic_replies_real -= 1

		$num_disapproved = 0;
		$forum_topics_real = $topic_id_list = $forum_id_list = $topic_replies_real_sql = $post_disapprove_sql = array();

		foreach ($post_info as $post_id => $post_data)
		{
			$topic_id_list[$post_data['topic_id']] = 1;

			if ($post_data['forum_id'])
			{
				$forum_id_list[$post_data['forum_id']] = 1;
			}

			// Topic or Post. ;)
			if ($post_data['topic_first_post_id'] == $post_id && $post_data['topic_last_post_id'] == $post_id)
			{
				if ($post_data['forum_id'])
				{
					if (!isset($forum_topics_real[$post_data['forum_id']]))
					{
						$forum_topics_real[$post_data['forum_id']] = 0;
					}
					$forum_topics_real[$post_data['forum_id']]++;
					$num_disapproved++;
				}
			}
			else
			{
				if (!isset($topic_replies_real_sql[$post_data['topic_id']]))
				{
					$topic_replies_real_sql[$post_data['topic_id']] = 0;
				}
				$topic_replies_real_sql[$post_data['topic_id']]++;
			}

			$post_disapprove_sql[] = $post_id;
		}

		if (sizeof($forum_topics_real))
		{
			foreach ($forum_topics_real as $forum_id => $topics_real)
			{
				$sql = 'UPDATE ' . FORUMS_TABLE . "
					SET forum_topics_real = forum_topics_real - $topics_real
					WHERE forum_id = $forum_id";
				$db->sql_query($sql);
			}
		}

		if (sizeof($topic_replies_real_sql))
		{
			foreach ($topic_replies_real_sql as $topic_id => $num_replies)
			{
				$sql = 'UPDATE ' . TOPICS_TABLE . "
					SET topic_replies_real = topic_replies_real - $num_replies
					WHERE topic_id = $topic_id";
				$db->sql_query($sql);
			}
		}

		if (sizeof($post_disapprove_sql))
		{
			if (!function_exists('delete_posts'))
			{
				include_once($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
			}

			// We do not check for permissions here, because the moderator allowed approval/disapproval should be allowed to delete the disapproved posts
			delete_posts('post_id', $post_disapprove_sql);
		}
		unset($post_disapprove_sql, $topic_replies_real_sql);

		update_post_information('topic', array_keys($topic_id_list));

		if (sizeof($forum_id_list))
		{
			update_post_information('forum', array_keys($forum_id_list));
		}
		unset($topic_id_list, $forum_id_list);

		$messenger = new messenger();

		// Notify Poster?
		if ($notify_poster)
		{
			foreach ($post_info as $post_id => $post_data)
			{
				if ($post_data['poster_id'] == ANONYMOUS)
				{
					continue;
				}

				$email_template = ($post_data['post_id'] == $post_data['topic_first_post_id'] && $post_data['post_id'] == $post_data['topic_last_post_id']) ? 'topic_disapproved' : 'post_disapproved';

				$messenger->template($email_template, $post_data['user_lang']);

				$messenger->replyto($config['board_email']);
				$messenger->to($post_data['user_email'], $post_data['username']);
				$messenger->im($post_data['user_jabber'], $post_data['username']);

				$messenger->assign_vars(array(
					'USERNAME'		=> htmlspecialchars_decode($post_data['username']),
					'REASON'		=> htmlspecialchars_decode($disapprove_reason),
					'POST_SUBJECT'	=> htmlspecialchars_decode(censor_text($post_data['post_subject'])),
					'TOPIC_TITLE'	=> htmlspecialchars_decode(censor_text($post_data['topic_title'])))
				);

				$messenger->send($post_data['user_notify_type']);
			}
		}
		unset($post_info, $disapprove_reason);

		$messenger->save_queue();

		if (sizeof($forum_topics_real))
		{
			$success_msg = ($num_disapproved == 1) ? 'TOPIC_DISAPPROVED_SUCCESS' : 'TOPICS_DISAPPROVED_SUCCESS';
		}
		else
		{
			$success_msg = (sizeof($post_id_list) == 1) ? 'POST_DISAPPROVED_SUCCESS' : 'POSTS_DISAPPROVED_SUCCESS';
		}
	}
	else
	{
		include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

		display_reasons($reason_id);

		$template->assign_vars(array(
			'S_NOTIFY_POSTER'	=> true,
			'S_APPROVE'			=> false,
			'REASON'			=> $reason,
			'ADDITIONAL_MSG'	=> $additional_msg)
		);

		confirm_box(false, 'DISAPPROVE_POST' . ((sizeof($post_id_list) == 1) ? '' : 'S'), $s_hidden_fields, 'mcp_approve.html');
	}

	$redirect = request_var('redirect', "index.$phpEx");
	$redirect = reapply_sid($redirect);

	if (!$success_msg)
	{
		redirect($redirect);
	}
	else
	{
		meta_refresh(3, $redirect);
		trigger_error($user->lang[$success_msg] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], "<a href=\"$redirect\">", '</a>'));
	}
}

?>