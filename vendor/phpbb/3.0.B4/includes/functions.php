<?php
/** 
*
* @package phpBB3
* @version $Id: functions.php,v 1.503 2006/11/27 21:33:49 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

// Common global functions

/**
* set_var
*
* Set variable, used by {@link request_var the request_var function}
*
* @access private
*/
function set_var(&$result, $var, $type, $multibyte = false)
{
	settype($var, $type);
	$result = $var;

	if ($type == 'string')
	{
		$result = trim(htmlspecialchars(str_replace(array("\r\n", "\r"), array("\n", "\n"), $result), ENT_COMPAT, 'UTF-8'));

		if (!empty($result))
		{
			// Make sure multibyte characters are wellformed
			if ($multibyte)
			{
				if (!preg_match('/^./u', $result))
				{
					$result = '';
				}
			}
			else
			{
				// no multibyte, allow only ASCII (0-127)
				$result = preg_replace('/[\x80-\xFF]/', '?', $result);
			}
		}

		$result = (STRIP) ? stripslashes($result) : $result;
	}
}

/**
* request_var
*
* Used to get passed variable
*/
function request_var($var_name, $default, $multibyte = false, $cookie = false)
{
	if (!$cookie && isset($_COOKIE[$var_name]))
	{
		if (!isset($_GET[$var_name]) && !isset($_POST[$var_name]))
		{
			return (is_array($default)) ? array() : $default;
		}
		$_REQUEST[$var_name] = isset($_POST[$var_name]) ? $_POST[$var_name] : $_GET[$var_name];
	}

	if (!isset($_REQUEST[$var_name]) || (is_array($_REQUEST[$var_name]) && !is_array($default)) || (is_array($default) && !is_array($_REQUEST[$var_name])))
	{
		return (is_array($default)) ? array() : $default;
	}

	$var = $_REQUEST[$var_name];
	if (!is_array($default))
	{
		$type = gettype($default);
	}
	else
	{
		list($key_type, $type) = each($default);
		$type = gettype($type);
		$key_type = gettype($key_type);
	}

	if (is_array($var))
	{
		$_var = $var;
		$var = array();

		foreach ($_var as $k => $v)
		{
			if (is_array($v))
			{
				foreach ($v as $_k => $_v)
				{
					set_var($k, $k, $key_type);
					set_var($_k, $_k, $key_type);
					set_var($var[$k][$_k], $_v, $type, $multibyte);
				}
			}
			else
			{
				set_var($k, $k, $key_type);
				set_var($var[$k], $v, $type, $multibyte);
			}
		}
	}
	else
	{
		set_var($var, $var, $type, $multibyte);
	}

	return $var;
}

/**
* Set config value. Creates missing config entry.
*/
function set_config($config_name, $config_value, $is_dynamic = false)
{
	global $db, $cache, $config;

	$sql = 'UPDATE ' . CONFIG_TABLE . "
		SET config_value = '" . $db->sql_escape($config_value) . "'
		WHERE config_name = '" . $db->sql_escape($config_name) . "'";
	$db->sql_query($sql);

	if (!$db->sql_affectedrows() && !isset($config[$config_name]))
	{
		$sql = 'INSERT INTO ' . CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'config_name'	=> $config_name,
			'config_value'	=> $config_value,
			'is_dynamic'	=> ($is_dynamic) ? 1 : 0));
		$db->sql_query($sql);
	}

	$config[$config_name] = $config_value;

	if (!$is_dynamic)
	{
		$cache->destroy('config');
	}
}

/**
* Generates an alphanumeric random string of given length
*/
function gen_rand_string($num_chars = 8)
{
	$rand_str = unique_id();
	$rand_str = str_replace('0', 'Z', strtoupper(base_convert($rand_str, 16, 35)));

	return substr($rand_str, 0, $num_chars);
}

/**
* Return unique id
* @param string $extra additional entropy
*/
function unique_id($extra = 'c')
{
	static $dss_seeded = false;
	global $config;

	$val = $config['rand_seed'] . microtime();
	$val = md5($val);
	$config['rand_seed'] = md5($config['rand_seed'] . $val . $extra);

	if ($dss_seeded !== true && ($config['rand_seed_last_update'] < time() - rand(1,10)))
	{
		set_config('rand_seed', $config['rand_seed'], true);
		set_config('rand_seed_last_update', time(), true);
		$dss_seeded = true;
	}

	return substr($val, 4, 16);
}

/**
* Generate sort selection fields
*/
function gen_sort_selects(&$limit_days, &$sort_by_text, &$sort_days, &$sort_key, &$sort_dir, &$s_limit_days, &$s_sort_key, &$s_sort_dir, &$u_sort_param)
{
	global $user;

	$sort_dir_text = array('a' => $user->lang['ASCENDING'], 'd' => $user->lang['DESCENDING']);

	// Check if the key is selectable. If not, we reset to the first key found.
	// This ensures the values are always valid.
	if (!isset($limit_days[$sort_days]))
	{
		@reset($limit_days);
		$sort_days = key($limit_days);
	}

	if (!isset($sort_by_text[$sort_key]))
	{
		@reset($sort_by_text);
		$sort_key = key($sort_by_text);
	}

	if (!isset($sort_dir_text[$sort_dir]))
	{
		@reset($sort_dir_text);
		$sort_dir = key($sort_dir_text);
	}

	$s_limit_days = '<select name="st">';
	foreach ($limit_days as $day => $text)
	{
		$selected = ($sort_days == $day) ? ' selected="selected"' : '';
		$s_limit_days .= '<option value="' . $day . '"' . $selected . '>' . $text . '</option>';
	}
	$s_limit_days .= '</select>';

	$s_sort_key = '<select name="sk">';
	foreach ($sort_by_text as $key => $text)
	{
		$selected = ($sort_key == $key) ? ' selected="selected"' : '';
		$s_sort_key .= '<option value="' . $key . '"' . $selected . '>' . $text . '</option>';
	}
	$s_sort_key .= '</select>';

	$s_sort_dir = '<select name="sd">';
	foreach ($sort_dir_text as $key => $value)
	{
		$selected = ($sort_dir == $key) ? ' selected="selected"' : '';
		$s_sort_dir .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
	}
	$s_sort_dir .= '</select>';

	$u_sort_param = "st=$sort_days&amp;sk=$sort_key&amp;sd=$sort_dir";

	return;
}

/**
* Generate Jumpbox
*/
function make_jumpbox($action, $forum_id = false, $select_all = false, $acl_list = false)
{
	global $config, $auth, $template, $user, $db;

	if (!$config['load_jumpbox'])
	{
		return;
	}

	$sql = 'SELECT forum_id, forum_name, parent_id, forum_type, left_id, right_id
		FROM ' . FORUMS_TABLE . '
		ORDER BY left_id ASC';
	$result = $db->sql_query($sql, 600);

	$right = $padding = 0;
	$padding_store = array('0' => 0);
	$display_jumpbox = false;
	$iteration = 0;

	// Sometimes it could happen that forums will be displayed here not be displayed within the index page
	// This is the result of forums not displayed at index, having list permissions and a parent of a forum with no permissions.
	// If this happens, the padding could be "broken"

	while ($row = $db->sql_fetchrow($result))
	{
		if ($row['left_id'] < $right)
		{
			$padding++;
			$padding_store[$row['parent_id']] = $padding;
		}
		else if ($row['left_id'] > $right + 1)
		{
			$padding = $padding_store[$row['parent_id']];
		}

		$right = $row['right_id'];

		if ($row['forum_type'] == FORUM_CAT && ($row['left_id'] + 1 == $row['right_id']))
		{
			// Non-postable forum with no subforums, don't display
			continue;
		}

		if (!$auth->acl_get('f_list', $row['forum_id']))
		{
			// if the user does not have permissions to list this forum skip
			continue;
		}

		if ($acl_list && !$auth->acl_gets($acl_list, $row['forum_id']))
		{
			continue;
		}

		if (!$display_jumpbox)
		{
			$template->assign_block_vars('jumpbox_forums', array(
				'FORUM_ID'		=> ($select_all) ? 0 : -1,
				'FORUM_NAME'	=> ($select_all) ? $user->lang['ALL_FORUMS'] : $user->lang['SELECT_FORUM'],
				'S_FORUM_COUNT'	=> $iteration)
			);

			$iteration++;
			$display_jumpbox = true;
		}

		$template->assign_block_vars('jumpbox_forums', array(
			'FORUM_ID'		=> $row['forum_id'],
			'FORUM_NAME'	=> $row['forum_name'],
			'SELECTED'		=> ($row['forum_id'] == $forum_id) ? ' selected="selected"' : '',
			'S_FORUM_COUNT'	=> $iteration,
			'S_IS_CAT'		=> ($row['forum_type'] == FORUM_CAT) ? true : false,
			'S_IS_LINK'		=> ($row['forum_type'] == FORUM_LINK) ? true : false,
			'S_IS_POST'		=> ($row['forum_type'] == FORUM_POST) ? true : false)
		);

		for ($i = 0; $i < $padding; $i++)
		{
			$template->assign_block_vars('jumpbox_forums.level', array());
		}
		$iteration++;
	}
	$db->sql_freeresult($result);
	unset($padding_store);

	$template->assign_vars(array(
		'S_DISPLAY_JUMPBOX'	=> $display_jumpbox,
		'S_JUMPBOX_ACTION'	=> $action)
	);

	return;
}


// Compatibility functions

if (!function_exists('array_combine'))
{
	/**
	* A wrapper for the PHP5 function array_combine()
	* @param array $keys contains keys for the resulting array
	* @param array $values contains values for the resulting array
	*
	* @return Returns an array by using the values from the keys array as keys and the
	* 	values from the values array as the corresponding values. Returns false if the
	* 	number of elements for each array isn't equal or if the arrays are empty.
	*/
	function array_combine($keys, $values)
	{
		$keys = array_values($keys);
		$values = array_values($values);

		$n = sizeof($keys);
		$m = sizeof($values);
		if (!$n || !$m || ($n != $m))
		{
			return false;
		}

		$combined = array();
		for ($i = 0; $i < $n; $i++)
		{
			$combined[$keys[$i]] = $values[$i];
		}
		return $combined;
	}
}

if (!function_exists('str_split'))
{
	/**
	* A wrapper for the PHP5 function str_split()
	* @param array $string contains the string to be converted
	* @param array $split_length contains the length of each chunk
	*
	* @return  Converts a string to an array. If the optional split_length parameter is specified,
	*  	the returned array will be broken down into chunks with each being split_length in length,
	*  	otherwise each chunk will be one character in length. FALSE is returned if split_length is
	*  	less than 1. If the split_length length exceeds the length of string, the entire string is
	*  	returned as the first (and only) array element. 
	*/
	function str_split($string, $split_length = 1)
	{
		if ($split_length < 1)
		{
			return false;
		}
		else if ($split_length >= strlen($string))
		{
			return array($string);
		}
		else
		{
			preg_match_all('#.{1,' . $split_length . '}#s', $string, $matches);
			return $matches[0];
		}
	}
}

if (!function_exists('stripos'))
{
	/**
	* A wrapper for the PHP5 function stripos
	* Find position of first occurrence of a case-insensitive string
	*
	* @param string $haystack is the string to search in
	* @param string $needle is the string to search for
	*
	* @return mixed Returns the numeric position of the first occurrence of needle in the haystack  string. Unlike strpos(), stripos() is case-insensitive.
	* Note that the needle may be a string of one or more characters.
	* If needle is not found, stripos() will return boolean FALSE. 
	*/
	function stripos($haystack, $needle)
	{
		if (preg_match('#' . preg_quote($needle, '#') . '#i', $haystack, $m))
		{
			return strpos($haystack, $m[0]);
		}

		return false;
	}
}

if (!function_exists('realpath'))
{
	if (substr(PHP_OS, 0, 3) != 'WIN' && !(bool) ini_get('safe_mode') && function_exists('shell_exec') && trim(`realpath .`))
	{
		/**
		* @author Chris Smith <chris@project-minerva.org>
		* @copyright 2006 Project Minerva Team
		* @param string $path The path which we should attempt to resolve.
		* @return mixed
		* @ignore
		*/
		function phpbb_realpath($path)
		{
			$arg = escapeshellarg($path);
			return trim(`realpath '$arg'`);
		}
	}
	else
	{
		/**
		* Checks if a path ($path) is absolute or relative
		*
		* @param string $path Path to check absoluteness of
		* @return boolean
		*/
		function is_absolute($path)
		{
			return ($path[0] == '/' || (substr(PHP_OS, 0, 3) == 'WIN' && preg_match('#^[a-z]:/#i', $path))) ? true : false;
		}

		/**
		* @author Chris Smith <chris@project-minerva.org>
		* @copyright 2006 Project Minerva Team
		* @param string $path The path which we should attempt to resolve.
		* @return mixed
		*/
		function phpbb_realpath($path)
		{
			// Now to perform funky shizzle

			// Switch to use UNIX slashes
			$path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
			$path_prefix = '';

			// Determine what sort of path we have
			if (is_absolute($path))
			{
				$absolute = true;

				if ($path[0] == '/')
				{
					// Absolute path, *NIX style
					$path_prefix = '';
				}
				else
				{
					// Absolute path, Windows style
					// Remove the drive letter and colon
					$path_prefix = $path[0] . ':';
					$path = substr($path, 2);
				}
			}
			else
			{
				// Relative Path
				// Prepend the current working directory
				if (function_exists('getcwd'))
				{
					// This is the best method, hopefully it is enabled!
					$path = str_replace(DIRECTORY_SEPARATOR, '/', getcwd()) . '/' . $path;
					$absolute = true;
					if (preg_match('#^[a-z]:#i', $path))
					{
						$path_prefix = $path[0] . ':';
						$path = substr($path, 2);
					}
					else
					{
						$path_prefix = '';
					}
				}
				else if (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME']))
				{
					// Warning: If chdir() has been used this will lie!
					// Warning: This has some problems sometime (CLI can create them easily)
					$path = str_replace(DIRECTORY_SEPARATOR, '/', dirname($_SERVER['SCRIPT_FILENAME'])) . '/' . $path;
					$absolute = true;
					$path_prefix = '';
				}
				else
				{
					// We have no way of getting the absolute path, just run on using relative ones.
					$absolute = false;
					$path_prefix = '.';
				}
			}

			// Remove any repeated slashes
			$path = preg_replace('#/{2,}#', '/', $path);

			// Remove the slashes from the start and end of the path
			$path = trim($path, '/');

			// Break the string into little bits for us to nibble on
			$bits = explode('/', $path);

			// Remove any . in the path
			$bits = array_diff($bits, array('.'));

			// Lets get looping, run over and resolve any .. (up directory)
			for ($i = 0, $max = sizeof($bits); $i < $max; $i++)
			{
				// @todo Optimise
				if ($bits[$i] == '..' )
				{
					if (isset($bits[$i - 1]))
					{
						if ($bits[$i - 1] != '..')
						{
							// We found a .. and we are able to traverse upwards, lets do it!
							unset($bits[$i]);
							unset($bits[$i - 1]);
							$i -= 2;
							$max -= 2;
							$bits = array_values($bits);
						}
					}
					else if ($absolute) // ie. !isset($bits[$i - 1]) && $absolute
					{
						// We have an absolute path trying to descend above the root of the filesystem
						// ... Error!
						return false;
					}
				}
			}

			// Prepend the path prefix
			array_unshift($bits, $path_prefix); 

			$resolved = '';

			$max = sizeof($bits) - 1;

			// Check if we are able to resolve symlinks, Windows cannot.
			$symlink_resolve = (function_exists('readlink')) ? true : false;

			foreach ($bits as $i => $bit)
			{
				if (@is_dir("$resolved/$bit") || ($i == $max && @is_file("$resolved/$bit")))
				{
					// Path Exists
					if ($symlink_resolve && is_link("$resolved/$bit") && ($link = readlink("$resolved/$bit")))
					{
						// Resolved a symlink.
						$resolved = $link . (($i == $max) ? '' : '/');
						continue;
					}
				}
				else
				{
					// Something doesn't exist here!
					// This is correct realpath() behaviour but sadly open_basedir and safe_mode make this problematic
					// return false;
				}
				$resolved .= $bit . (($i == $max) ? '' : '/');
			}

			// @todo If the file exists fine and open_basedir only has one path we should be able to prepend it
			// because we must be inside that basedir, the question is where...
			// @internal The slash in is_dir() gets around an open_basedir restriction
			if (!@file_exists($resolved) || (!is_dir($resolved . '/') && !is_file($resolved)))
			{
				return false;
			}

			// Put the slashes back to the native operating systems slashes
			$resolved = str_replace('/', DIRECTORY_SEPARATOR, $resolved);

			return $resolved; // We got here, in the end!
		}
	}
}
else
{
	/**
	* A wrapper for realpath
	* @ignore
	*/
	function phpbb_realpath($path)
	{
		return realpath($path);
	}
}

if (!function_exists('htmlspecialchars_decode'))
{
	/**
	* A wrapper for htmlspecialchars_decode
	* @ignore
	*/
	function htmlspecialchars_decode($string, $quote_style = ENT_COMPAT)
	{
		return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
	}
}

// functions used for building option fields

/**
* Pick a language, any language ...
*/
function language_select($default = '')
{
	global $db;

	$sql = 'SELECT lang_iso, lang_local_name
		FROM ' . LANG_TABLE . '
		ORDER BY lang_english_name';
	$result = $db->sql_query($sql, 600);

	$lang_options = '';
	while ($row = $db->sql_fetchrow($result))
	{
		$selected = ($row['lang_iso'] == $default) ? ' selected="selected"' : '';
		$lang_options .= '<option value="' . $row['lang_iso'] . '"' . $selected . '>' . $row['lang_local_name'] . '</option>';
	}
	$db->sql_freeresult($result);

	return $lang_options;
}

/** 
* Pick a template/theme combo,
*/
function style_select($default = '', $all = false)
{
	global $db;

	$sql_where = (!$all) ? 'WHERE style_active = 1 ' : '';
	$sql = 'SELECT style_id, style_name
		FROM ' . STYLES_TABLE . "
		$sql_where
		ORDER BY style_name";
	$result = $db->sql_query($sql);

	$style_options = '';
	while ($row = $db->sql_fetchrow($result))
	{
		$selected = ($row['style_id'] == $default) ? ' selected="selected"' : '';
		$style_options .= '<option value="' . $row['style_id'] . '"' . $selected . '>' . $row['style_name'] . '</option>';
	}
	$db->sql_freeresult($result);

	return $style_options;
}

/**
* Pick a timezone
*/
function tz_select($default = '', $truncate = false)
{
	global $user;

	$tz_select = '';
	foreach ($user->lang['tz_zones'] as $offset => $zone)
	{
		if ($truncate)
		{
			$zone_trunc = truncate_string($zone, 50, false, '...');
		}
		else
		{
			$zone_trunc = $zone;
		}

		if (is_numeric($offset))
		{
			$selected = ($offset == $default) ? ' selected="selected"' : '';
			$tz_select .= '<option title="'.$zone.'" value="' . $offset . '"' . $selected . '>' . $zone_trunc . '</option>';
		}
	}

	return $tz_select;
}

// Functions handling topic/post tracking/marking

/**
* Marks a topic/forum as read
* Marks a topic as posted to
*
* @param int $user_id can only be used with $mode == 'post'
*/
function markread($mode, $forum_id = false, $topic_id = false, $post_time = 0, $user_id = 0)
{
	global $db, $user, $config;

	if ($mode == 'all')
	{
		if ($forum_id === false || !sizeof($forum_id))
		{
			if ($config['load_db_lastread'] && $user->data['is_registered'])
			{
				// Mark all forums read (index page)
				$db->sql_query('DELETE FROM ' . TOPICS_TRACK_TABLE . " WHERE user_id = {$user->data['user_id']}");
				$db->sql_query('DELETE FROM ' . FORUMS_TRACK_TABLE . " WHERE user_id = {$user->data['user_id']}");
				$db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_lastmark = ' . time() . " WHERE user_id = {$user->data['user_id']}");
			}
			else if ($config['load_anon_lastread'] || $user->data['is_registered'])
			{
				$tracking_topics = (isset($_COOKIE[$config['cookie_name'] . '_track'])) ? ((STRIP) ? stripslashes($_COOKIE[$config['cookie_name'] . '_track']) : $_COOKIE[$config['cookie_name'] . '_track']) : '';
				$tracking_topics = ($tracking_topics) ? unserialize($tracking_topics) : array();

				unset($tracking_topics['tf']);
				unset($tracking_topics['t']);
				unset($tracking_topics['f']);
				$tracking_topics['l'] = base_convert(time() - $config['board_startdate'], 10, 36);
	
				$user->set_cookie('track', serialize($tracking_topics), time() + 31536000);
				unset($tracking_topics);

				if ($user->data['is_registered'])
				{
					$db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_lastmark = ' . time() . " WHERE user_id = {$user->data['user_id']}");
				}
			}
		}

		return;
	}
	else if ($mode == 'topics')
	{
		// Mark all topics in forums read
		if (!is_array($forum_id))
		{
			$forum_id = array($forum_id);
		}

		// Add 0 to forums array to mark global announcements correctly
		$forum_id[] = 0;

		if ($config['load_db_lastread'] && $user->data['is_registered'])
		{
			$sql = 'DELETE FROM ' . TOPICS_TRACK_TABLE . " 
				WHERE user_id = {$user->data['user_id']}
					AND " . $db->sql_in_set('forum_id', $forum_id);
			$db->sql_query($sql);

			$sql = 'SELECT forum_id
				FROM ' . FORUMS_TRACK_TABLE . "
				WHERE user_id = {$user->data['user_id']}
					AND " . $db->sql_in_set('forum_id', $forum_id);
			$result = $db->sql_query($sql);

			$sql_update = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$sql_update[] = $row['forum_id'];
			}
			$db->sql_freeresult($result);

			if (sizeof($sql_update))
			{
				$sql = 'UPDATE ' . FORUMS_TRACK_TABLE . '
					SET mark_time = ' . time() . "
					WHERE user_id = {$user->data['user_id']}
						AND " . $db->sql_in_set('forum_id', $sql_update);
				$db->sql_query($sql);
			}

			if ($sql_insert = array_diff($forum_id, $sql_update))
			{
				$sql_ary = array();
				foreach ($sql_insert as $f_id)
				{
					$sql_ary[] = array(
						'user_id'	=> $user->data['user_id'],
						'forum_id'	=> $f_id,
						'mark_time'	=> time()
					);
				}

				$db->sql_multi_insert(FORUMS_TRACK_TABLE, $sql_ary);
			}
		}
		else if ($config['load_anon_lastread'] || $user->data['is_registered'])
		{
			$tracking = (isset($_COOKIE[$config['cookie_name'] . '_track'])) ? ((STRIP) ? stripslashes($_COOKIE[$config['cookie_name'] . '_track']) : $_COOKIE[$config['cookie_name'] . '_track']) : '';
			$tracking = ($tracking) ? unserialize($tracking) : array();

			foreach ($forum_id as $f_id)
			{
				$topic_ids36 = (isset($tracking['tf'][$f_id])) ? $tracking['tf'][$f_id] : array();

				if (isset($tracking['tf'][$f_id]))
				{
					unset($tracking['tf'][$f_id]);
				}

				foreach ($topic_ids36 as $topic_id36)
				{
					unset($tracking['t'][$topic_id36]);
				}

				if (isset($tracking['f'][$f_id]))
				{
					unset($tracking['f'][$f_id]);
				}

				$tracking['f'][$f_id] = base_convert(time() - $config['board_startdate'], 10, 36);
			}

			$user->set_cookie('track', serialize($tracking), time() + 31536000);
			unset($tracking);
		}

		return;
	}
	else if ($mode == 'topic')
	{
		if ($topic_id === false || $forum_id === false)
		{
			return;
		}

		if ($config['load_db_lastread'] && $user->data['is_registered'])
		{
			$sql = 'UPDATE ' . TOPICS_TRACK_TABLE . '
				SET mark_time = ' . (($post_time) ? $post_time : time()) . "
				WHERE user_id = {$user->data['user_id']}
					AND topic_id = $topic_id";
			$db->sql_query($sql);

			// insert row
			if (!$db->sql_affectedrows())
			{
				$db->sql_return_on_error(true);

				$sql_ary = array(
					'user_id'		=> $user->data['user_id'],
					'topic_id'		=> $topic_id,
					'forum_id'		=> (int) $forum_id,
					'mark_time'		=> ($post_time) ? $post_time : time(),
				);

				$db->sql_query('INSERT INTO ' . TOPICS_TRACK_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

				$db->sql_return_on_error(false);
			}
		}
		else if ($config['load_anon_lastread'] || $user->data['is_registered'])
		{
			$tracking = (isset($_COOKIE[$config['cookie_name'] . '_track'])) ? ((STRIP) ? stripslashes($_COOKIE[$config['cookie_name'] . '_track']) : $_COOKIE[$config['cookie_name'] . '_track']) : '';
			$tracking = ($tracking) ? unserialize($tracking) : array();

			$topic_id36 = base_convert($topic_id, 10, 36);

			if (!isset($tracking['t'][$topic_id36]))
			{
				$tracking['tf'][$forum_id][$topic_id36] = true;
			}

			$post_time = ($post_time) ? $post_time : time();
			$tracking['t'][$topic_id36] = base_convert($post_time - $config['board_startdate'], 10, 36);

			// If the cookie grows larger than 10000 characters we will remove the smallest value
			// This can result in old topics being unread - but most of the time it should be accurate...
			if (isset($_COOKIE[$config['cookie_name'] . '_track']) && strlen($_COOKIE[$config['cookie_name'] . '_track']) > 10000)
			{
				//echo 'Cookie grown too large' . print_r($tracking, true);

				// We get the ten most minimum stored time offsets and its associated topic ids
				$time_keys = array();
				for ($i = 0; $i < 10 && sizeof($tracking['t']); $i++)
				{
					$min_value = min($tracking['t']);
					$m_tkey = array_search($min_value, $tracking['t']);
					unset($tracking['t'][$m_tkey]);

					$time_keys[$m_tkey] = $min_value;
				}

				// Now remove the topic ids from the array...
				foreach ($tracking['tf'] as $f_id => $topic_id_ary)
				{
					foreach ($time_keys as $m_tkey => $min_value)
					{
						if (isset($topic_id_ary[$m_tkey]))
						{
							$tracking['f'][$f_id] = $min_value;
							unset($tracking['tf'][$f_id][$m_tkey]);
						}
					}
				}

				if ($user->data['is_registered'])
				{
					$user->data['user_lastmark'] = intval(base_convert(max($time_keys) + $config['board_startdate'], 36, 10));
					$db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_lastmark = ' . $user->data['user_lastmark'] . " WHERE user_id = {$user->data['user_id']}");
				}
				else
				{
					$tracking['l'] = max($time_keys);
				}
			}

			$user->set_cookie('track', serialize($tracking), time() + 31536000);
		}

		return;
	}
	else if ($mode == 'post')
	{
		if ($topic_id === false)
		{
			return;
		}

		$use_user_id = (!$user_id) ? $user->data['user_id'] : $user_id;

		if ($config['load_db_track'] && $use_user_id != ANONYMOUS)
		{
			$db->sql_return_on_error(true);

			$sql_ary = array(
				'user_id'		=> $use_user_id,
				'topic_id'		=> $topic_id,
				'topic_posted'	=> 1
			);

			$db->sql_query('INSERT INTO ' . TOPICS_POSTED_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

			$db->sql_return_on_error(false);
		}

		return;
	}
}

/**
* Get topic tracking info by using already fetched info
*/
function get_topic_tracking($forum_id, $topic_ids, &$rowset, $forum_mark_time, $global_announce_list = false)
{
	global $config, $user;

	$last_read = array();

	if (!is_array($topic_ids))
	{
		$topic_ids = array($topic_ids);
	}

	foreach ($topic_ids as $topic_id)
	{
		if (!empty($rowset[$topic_id]['mark_time']))
		{
			$last_read[$topic_id] = $rowset[$topic_id]['mark_time'];
		}
	}

	$topic_ids = array_diff($topic_ids, array_keys($last_read));

	if (sizeof($topic_ids))
	{
		$mark_time = array();

		// Get global announcement info
		if ($global_announce_list && sizeof($global_announce_list))
		{
			if (!isset($forum_mark_time[0]))
			{
				global $db;

				$sql = 'SELECT mark_time
					FROM ' . FORUMS_TRACK_TABLE . "
					WHERE user_id = {$user->data['user_id']}
						AND forum_id = 0";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if ($row)
				{
					$mark_time[0] = $row['mark_time'];
				}
			}
			else
			{
				if ($forum_mark_time[0] !== false)
				{
					$mark_time[0] = $forum_mark_time[0];
				}
			}
		}

		if (!empty($forum_mark_time[$forum_id]) && $forum_mark_time[$forum_id] !== false)
		{
			$mark_time[$forum_id] = $forum_mark_time[$forum_id];
		}
			
		$user_lastmark = (isset($mark_time[$forum_id])) ? $mark_time[$forum_id] : $user->data['user_lastmark'];

		foreach ($topic_ids as $topic_id)
		{
			if ($global_announce_list && isset($global_announce_list[$topic_id]))
			{
				$last_read[$topic_id] = (isset($mark_time[0])) ? $mark_time[0] : $user_lastmark;
			}
			else
			{
				$last_read[$topic_id] = $user_lastmark;
			}
		}
	}

	return $last_read;
}

/**
* Get topic tracking info from db (for cookie based tracking only this function is used)
*/
function get_complete_topic_tracking($forum_id, $topic_ids, $global_announce_list = false)
{
	global $config, $user;

	$last_read = array();

	if (!is_array($topic_ids))
	{
		$topic_ids = array($topic_ids);
	}

	if ($config['load_db_lastread'] && $user->data['is_registered'])
	{
		global $db;

		$sql = 'SELECT topic_id, mark_time
			FROM ' . TOPICS_TRACK_TABLE . "
			WHERE user_id = {$user->data['user_id']}
				AND " . $db->sql_in_set('topic_id', $topic_ids);
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$last_read[$row['topic_id']] = $row['mark_time'];
		}
		$db->sql_freeresult($result);
	
		$topic_ids = array_diff($topic_ids, array_keys($last_read));

		if (sizeof($topic_ids))
		{
			$sql = 'SELECT forum_id, mark_time 
				FROM ' . FORUMS_TRACK_TABLE . "
				WHERE user_id = {$user->data['user_id']}
					AND forum_id " . 
					(($global_announce_list && sizeof($global_announce_list)) ? "IN (0, $forum_id)" : "= $forum_id");
			$result = $db->sql_query($sql);
		
			$mark_time = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$mark_time[$row['forum_id']] = $row['mark_time'];
			}
			$db->sql_freeresult($result);

			$user_lastmark = (isset($mark_time[$forum_id])) ? $mark_time[$forum_id] : $user->data['user_lastmark'];

			foreach ($topic_ids as $topic_id)
			{
				if ($global_announce_list && isset($global_announce_list[$topic_id]))
				{
					$last_read[$topic_id] = (isset($mark_time[0])) ? $mark_time[0] : $user_lastmark;
				}
				else
				{
					$last_read[$topic_id] = $user_lastmark;
				}
			}
		}
	}
	else if ($config['load_anon_lastread'] || $user->data['is_registered'])
	{
		global $tracking_topics;

		if (!isset($tracking_topics) || !sizeof($tracking_topics))
		{
			$tracking_topics = (isset($_COOKIE[$config['cookie_name'] . '_track'])) ? ((STRIP) ? stripslashes($_COOKIE[$config['cookie_name'] . '_track']) : $_COOKIE[$config['cookie_name'] . '_track']) : '';
			$tracking_topics = ($tracking_topics) ? unserialize($tracking_topics) : array();
		}

		if (!$user->data['is_registered'])
		{
			$user_lastmark = (isset($tracking_topics['l'])) ? base_convert($tracking_topics['l'], 36, 10) + $config['board_startdate'] : 0;
		}
		else
		{
			$user_lastmark = $user->data['user_lastmark'];
		}

		foreach ($topic_ids as $topic_id)
		{
			$topic_id36 = base_convert($topic_id, 10, 36);

			if (isset($tracking_topics['t'][$topic_id36]))
			{
				$last_read[$topic_id] = base_convert($tracking_topics['t'][$topic_id36], 36, 10) + $config['board_startdate'];
			}
		}

		$topic_ids = array_diff($topic_ids, array_keys($last_read));

		if (sizeof($topic_ids))
		{
			$mark_time = array();
			if ($global_announce_list && sizeof($global_announce_list))
			{
				if (isset($tracking_topics['f'][0]))
				{
					$mark_time[0] = base_convert($tracking_topics['f'][0], 36, 10) + $config['board_startdate'];
				}
			}

			if (isset($tracking_topics['f'][$forum_id]))
			{
				$mark_time[$forum_id] = base_convert($tracking_topics['f'][$forum_id], 36, 10) + $config['board_startdate'];
			}

			$user_lastmark = (isset($mark_time[$forum_id])) ? $mark_time[$forum_id] : $user_lastmark;

			foreach ($topic_ids as $topic_id)
			{
				if ($global_announce_list && isset($global_announce_list[$topic_id]))
				{
					$last_read[$topic_id] = (isset($mark_time[0])) ? $mark_time[0] : $user_lastmark;
				}
				else
				{
					$last_read[$topic_id] = $user_lastmark;
				}
			}
		}
	}

	return $last_read;
}

/**
* Check for read forums and update topic tracking info accordingly
*
* @param int $forum_id the forum id to check
* @param int $forum_last_post_time the forums last post time
* @param int $f_mark_time the forums last mark time if user is registered and load_db_lastread enabled
* @param int $mark_time_forum false if the mark time needs to be obtained, else the last users forum mark time
*
* @return true if complete forum got marked read, else false.
*/
function update_forum_tracking_info($forum_id, $forum_last_post_time, $f_mark_time = false, $mark_time_forum = false)
{
	global $db, $tracking_topics, $user, $config;

	// Determine the users last forum mark time if not given.
	if ($mark_time_forum === false)
	{
		if ($config['load_db_lastread'] && $user->data['is_registered'])
		{
			$mark_time_forum = (!empty($f_mark_time)) ? $f_mark_time : $user->data['user_lastmark'];
		}
		else if ($config['load_anon_lastread'] || $user->data['is_registered'])
		{
			if (!isset($tracking_topics) || !sizeof($tracking_topics))
			{
				$tracking_topics = (isset($_COOKIE[$config['cookie_name'] . '_track'])) ? ((STRIP) ? stripslashes($_COOKIE[$config['cookie_name'] . '_track']) : $_COOKIE[$config['cookie_name'] . '_track']) : '';
				$tracking_topics = ($tracking_topics) ? unserialize($tracking_topics) : array();
			}

			if (!$user->data['is_registered'])
			{
				$user->data['user_lastmark'] = (isset($tracking_topics['l'])) ? (int) (base_convert($tracking_topics['l'], 36, 10) + $config['board_startdate']) : 0;
			}

			$mark_time_forum = (isset($tracking_topics['f'][$forum_id])) ? (int) (base_convert($tracking_topics['f'][$forum_id], 36, 10) + $config['board_startdate']) : $user->data['user_lastmark'];
		}
	}

	// Check the forum for any left unread topics.
	// If there are none, we mark the forum as read.
	if ($config['load_db_lastread'] && $user->data['is_registered'])
	{
		if ($mark_time_forum >= $forum_last_post_time)
		{
			// We do not need to mark read, this happened before. Therefore setting this to true
			$row = true;
		}
		else
		{
			$sql = 'SELECT t.forum_id FROM ' . TOPICS_TABLE . ' t
				LEFT JOIN ' . TOPICS_TRACK_TABLE . ' tt ON (tt.topic_id = t.topic_id AND tt.user_id = ' . $user->data['user_id'] . ')
				WHERE t.forum_id = ' . $forum_id . '
					AND t.topic_last_post_time > ' . $mark_time_forum . '
					AND t.topic_moved_id = 0
					AND tt.topic_id IS NULL
				GROUP BY t.forum_id';
			$result = $db->sql_query_limit($sql, 1);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		}
	}
	else if ($config['load_anon_lastread'] || $user->data['is_registered'])
	{
		// Get information from cookie
		$row = false;

		if (!isset($tracking_topics['tf'][$forum_id]))
		{
			// We do not need to mark read, this happened before. Therefore setting this to true
			$row = true;
		}
		else
		{
			$sql = 'SELECT topic_id
				FROM ' . TOPICS_TABLE . '
				WHERE forum_id = ' . $forum_id . '
					AND topic_last_post_time > ' . $mark_time_forum . '
					AND topic_moved_id = 0';
			$result = $db->sql_query($sql);

			$check_forum = $tracking_topics['tf'][$forum_id];
			$unread = false;
			while ($row = $db->sql_fetchrow($result))
			{
				if (!in_array(base_convert($row['topic_id'], 10, 36), array_keys($check_forum)))
				{
					$unread = true;
					break;
				}
			}
			$db->sql_freeresult($result);

			$row = $unread;
		}
	}
	else
	{
		$row = true;
	}

	if (!$row)
	{
		markread('topics', $forum_id);
		return true;
	}

	return false;
}

// Pagination functions

/**
* Pagination routine, generates page number sequence
* tpl_prefix is for using different pagination blocks at one page
*/
function generate_pagination($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = false, $tpl_prefix = '')
{
	global $template, $user;

	$seperator = $user->theme['pagination_sep'];
	$total_pages = ceil($num_items/$per_page);

	if ($total_pages == 1 || !$num_items)
	{
		return false;
	}

	$on_page = floor($start_item / $per_page) + 1;
	$page_string = ($on_page == 1) ? '<strong>1</strong>' : '<a href="' . $base_url . '">1</a>';

	if ($total_pages > 5)
	{
		$start_cnt = min(max(1, $on_page - 4), $total_pages - 5);
		$end_cnt = max(min($total_pages, $on_page + 4), 6);

		$page_string .= ($start_cnt > 1) ? ' ... ' : $seperator;

		for ($i = $start_cnt + 1; $i < $end_cnt; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "&amp;start=" . (($i - 1) * $per_page) . '">' . $i . '</a>';
			if ($i < $end_cnt - 1)
			{
				$page_string .= $seperator;
			}
		}

		$page_string .= ($end_cnt < $total_pages) ? ' ... ' : $seperator;
	}
	else
	{
		$page_string .= $seperator;

		for ($i = 2; $i < $total_pages; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "&amp;start=" . (($i - 1) * $per_page) . '">' . $i . '</a>';
			if ($i < $total_pages)
			{
				$page_string .= $seperator;
			}
		}
	}

	$page_string .= ($on_page == $total_pages) ? '<strong>' . $total_pages . '</strong>' : '<a href="' . $base_url . '&amp;start=' . (($total_pages - 1) * $per_page) . '">' . $total_pages . '</a>';

	if ($add_prevnext_text)
	{
		if ($on_page != 1) 
		{
			$page_string = '<a href="' . $base_url . '&amp;start=' . (($on_page - 2) * $per_page) . '">' . $user->lang['PREVIOUS'] . '</a>&nbsp;&nbsp;' . $page_string;
		}

		if ($on_page != $total_pages)
		{
			$page_string .= '&nbsp;&nbsp;<a href="' . $base_url . '&amp;start=' . ($on_page * $per_page) . '">' . $user->lang['NEXT'] . '</a>';
		}
	}

	$template->assign_vars(array(
		$tpl_prefix . 'BASE_URL'	=> $base_url,
		$tpl_prefix . 'PER_PAGE'	=> $per_page,

		$tpl_prefix . 'PREVIOUS_PAGE'	=> ($on_page == 1) ? '' : $base_url . '&amp;start=' . (($on_page - 2) * $per_page),
		$tpl_prefix . 'NEXT_PAGE'		=> ($on_page == $total_pages) ? '' : $base_url . '&amp;start=' . ($on_page * $per_page))
	);

	return $page_string;
}

/**
* Return current page (pagination)
*/
function on_page($num_items, $per_page, $start)
{
	global $template, $user;

	$on_page = floor($start / $per_page) + 1;

	$template->assign_vars(array(
		'ON_PAGE'	=> $on_page)
	);

	return sprintf($user->lang['PAGE_OF'], $on_page, max(ceil($num_items / $per_page), 1));
}

// Server functions (building urls, redirecting...)

/**
* Append session id to url
*
* @param string $url The url the session id needs to be appended to (can have params)
* @param mixed $params String or array of additional url parameters
* @param bool $is_amp Is url using &amp; (true) or & (false)
* @param string $session_id Possibility to use a custom session id instead of the global one
*
* Examples:
* <code>
* append_sid("{$phpbb_root_path}viewtopic.$phpEx?t=1&amp;f=2");
* append_sid("{$phpbb_root_path}viewtopic.$phpEx", 't=1&amp;f=2');
* append_sid("{$phpbb_root_path}viewtopic.$phpEx", 't=1&f=2', false);
* append_sid("{$phpbb_root_path}viewtopic.$phpEx", array('t' => 1, 'f' => 2));
* </code>
*/
function append_sid($url, $params = false, $is_amp = true, $session_id = false)
{
	global $_SID, $_EXTRA_URL;

	// Assign sid if session id is not specified
	if ($session_id === false)
	{
		$session_id = $_SID;
	}

	$amp_delim = ($is_amp) ? '&amp;' : '&';
	$url_delim = (strpos($url, '?') === false) ? '?' : $amp_delim;

	// Appending custom url parameter?
	$append_url = (!empty($_EXTRA_URL)) ? implode($amp_delim, $_EXTRA_URL) : '';

	// Use the short variant if possible ;)
	if ($params === false)
	{
		// Append session id
		return (!$session_id) ? $url . (($append_url) ? $url_delim . $append_url : '') : $url . (($append_url) ? $url_delim . $append_url . $amp_delim : $url_delim) . 'sid=' . $session_id;
	}

	// Build string if parameters are specified as array
	if (is_array($params))
	{
		$output = array();

		foreach ($params as $key => $item)
		{
			if ($item === NULL)
			{
				continue;
			}

			$output[] = $key . '=' . $item;
		}

		$params = implode($amp_delim, $output);
	}

	// Append session id and parameters (even if they are empty)
	// If parameters are empty, the developer can still append his/her parameters without caring about the delimiter
	return $url . (($append_url) ? $url_delim . $append_url . $amp_delim : $url_delim) . $params . ((!$session_id) ? '' : $amp_delim . 'sid=' . $session_id);
}

/**
* Generate board url (example: http://www.foo.bar/phpBB)
* @param bool $without_script_path if set to true the script path gets not appended (example: http://www.foo.bar)
*/
function generate_board_url($without_script_path = false)
{
	global $config, $user;

	$server_name = (!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : getenv('SERVER_NAME');
	$server_port = (!empty($_SERVER['SERVER_PORT'])) ? (int) $_SERVER['SERVER_PORT'] : (int) getenv('SERVER_PORT');

	// Forcing server vars is the only way to specify/override the protocol
	if ($config['force_server_vars'] || !$server_name)
	{
		$server_protocol = ($config['server_protocol']) ? $config['server_protocol'] : (($config['cookie_secure']) ? 'https://' : 'http://');
		$server_name = $config['server_name'];
		$server_port = (int) $config['server_port'];

		$url = $server_protocol . $server_name;
	}
	else
	{
		// Do not rely on cookie_secure, users seem to think that it means a secured cookie instead of an encrypted connection
		$cookie_secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;
		$url = (($cookie_secure) ? 'https://' : 'http://') . $server_name;
	}

	if ($server_port && (($config['cookie_secure'] && $server_port <> 443) || (!$config['cookie_secure'] && $server_port <> 80)))
	{
		$url .= ':' . $server_port;
	}

	if ($without_script_path)
	{
		return $url;
	}

	// Strip / from the end
	return $url . substr($user->page['root_script_path'], 0, -1);
}

/**
* Redirects the user to another page then exits the script nicely
*/
function redirect($url, $return = false)
{
	global $db, $cache, $config, $user, $phpbb_root_path;

	if (empty($user->lang))
	{
		$user->add_lang('common');
	}

	if (!$return)
	{
		garbage_collection();
	}

	// Make sure no &amp;'s are in, this will break the redirect
	$url = str_replace('&amp;', '&', $url);

	// Make sure no linebreaks are there... to prevent http response splitting for PHP < 4.4.2
	if (strpos(urldecode($url), "\n") !== false || strpos(urldecode($url), "\r") !== false)
	{
		trigger_error('Tried to redirect to potentially insecure url.', E_USER_ERROR);
	}

	// Determine which type of redirect we need to handle...
	$url_parts = parse_url($url);

	if ($url_parts === false)
	{
		// Malformed url, redirect to current page...
		$url = generate_board_url() . '/' . $user->page['page'];
	}
	else if (!empty($url_parts['scheme']) && !empty($url_parts['host']))
	{
		// Full URL
	}
	else if ($url[0] == '/')
	{
		// Absolute uri, prepend direct url...
		$url = generate_board_url(true) . $url;
	}
	else
	{
		// Relative uri
		$pathinfo = pathinfo($url);

		// Is the uri pointing to the current directory?
		if ($pathinfo['dirname'] == '.')
		{
			if ($user->page['page_dir'])
			{
				$url = generate_board_url() . '/' . $user->page['page_dir'] . '/' . str_replace('./', '', $url);
			}
			else
			{
				$url = generate_board_url() . '/' . str_replace('./', '', $url);
			}
		}
		else
		{
			// Used ./ before, but $phpbb_root_path is working better with urls within another root path
			$root_dirs = explode('/', str_replace('\\', '/', phpbb_realpath($phpbb_root_path)));
			$page_dirs = explode('/', str_replace('\\', '/', phpbb_realpath($pathinfo['dirname'])));
			$intersection = array_intersect_assoc($root_dirs, $page_dirs);

			$root_dirs = array_diff_assoc($root_dirs, $intersection);
			$page_dirs = array_diff_assoc($page_dirs, $intersection);

			$dir = str_repeat('../', sizeof($root_dirs)) . implode('/', $page_dirs);

			if ($dir && substr($dir, -1, 1) == '/')
			{
				$dir = substr($dir, 0, -1);
			}

			$url = $dir . '/' . str_replace($pathinfo['dirname'] . '/', '', $url);
			$url = generate_board_url() . '/' . $url;
		}
	}

	// Make sure no linebreaks are there... to prevent http response splitting for PHP < 4.4.2
	if (strpos(urldecode($url), "\n") !== false || strpos(urldecode($url), "\r") !== false || strpos($url, ';') !== false)
	{
		trigger_error('Tried to redirect to potentially insecure url.', E_USER_ERROR);
	}

	if ($return)
	{
		return $url;
	}

	// Redirect via an HTML form for PITA webservers
	if (@preg_match('#Microsoft|WebSTAR|Xitami#', getenv('SERVER_SOFTWARE')))
	{
		header('Refresh: 0; URL=' . $url);
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><meta http-equiv="refresh" content="0; url=' . $url . '"><title>Redirect</title></head><body><div align="center">' . sprintf($user->lang['URL_REDIRECT'], '<a href="' . $url . '">', '</a>') . '</div></body></html>';

		exit;
	}

	// Behave as per HTTP/1.1 spec for others
	header('Location: ' . $url);
	exit;
}

/**
* Re-Apply session id after page reloads
*/
function reapply_sid($url)
{
	global $phpEx, $phpbb_root_path;

	if ($url === "index.$phpEx")
	{
		return append_sid("index.$phpEx");
	}
	else if ($url === "{$phpbb_root_path}index.$phpEx")
	{
		return append_sid("{$phpbb_root_path}index.$phpEx");
	}

	// Remove previously added sid
	if (strpos($url, '?sid=') !== false)
	{
		$url = preg_replace('/(\?)sid=[a-z0-9]+(&amp;|&)?/', '\1', $url);
	}
	else if (strpos($url, '&sid=') !== false)
	{
		$url = preg_replace('/&sid=[a-z0-9]+(&)?/', '\1', $url);
	}
	else if (strpos($url, '&amp;sid=') !== false)
	{
		$url = preg_replace('/&amp;sid=[a-z0-9]+(&amp;)?/', '\1', $url);
	}

	return append_sid($url);
}

/**
* Returns url from the session/current page with an re-appended SID with optionally stripping vars from the url
*/
function build_url($strip_vars = false)
{
	global $user, $phpbb_root_path;

	// Append SID
	$redirect = (($user->page['page_dir']) ? $user->page['page_dir'] . '/' : '') . $user->page['page_name'] . (($user->page['query_string']) ? "?{$user->page['query_string']}" : '');
	$redirect = append_sid($redirect, false, false);

	// Add delimiter if not there...
	if (strpos($redirect, '?') === false)
	{
		$redirect .= '?';
	}

	// Strip vars...
	if ($strip_vars !== false && strpos($redirect, '?') !== false)
	{
		if (!is_array($strip_vars))
		{
			$strip_vars = array($strip_vars);
		}

		$query = $_query = array();

		$args = substr($redirect, strpos($redirect, '?') + 1);
		$args = ($args) ? explode('&', $args) : array();
		$redirect = substr($redirect, 0, strpos($redirect, '?'));

		foreach ($args as $argument)
		{
			$arguments = explode('=', $argument);
			$key = $arguments[0];
			unset($arguments[0]);

			$query[$key] = implode('=', $arguments);
		}

		// Strip the vars off
		foreach ($strip_vars as $strip)
		{
			if (isset($query[$strip]))
			{
				unset($query[$strip]);
			}
		}
	
		// Glue the remaining parts together... already urlencoded
		foreach ($query as $key => $value)
		{
			$_query[] = $key . '=' . $value;
		}
		$query = implode('&', $_query);

		$redirect .= ($query) ? '?' . $query : '';
	}

	return $phpbb_root_path . str_replace('&', '&amp;', $redirect);
}

/**
* Meta refresh assignment
*/
function meta_refresh($time, $url)
{
	global $template;

	$url = redirect($url, true);

	$template->assign_vars(array(
		'META' => '<meta http-equiv="refresh" content="' . $time . ';url=' . $url . '" />')
	);
}

// Message/Login boxes

/**
* Build Confirm box
* @param boolean $check True for checking if confirmed (without any additional parameters) and false for displaying the confirm box
* @param string $title Title/Message used for confirm box.
*		message text is _CONFIRM appended to title. 
*		If title can not be found in user->lang a default one is displayed
*		If title_CONFIRM can not be found in user->lang the text given is used.
* @param string $hidden Hidden variables
* @param string $html_body Template used for confirm box
* @param string $u_action Custom form action
*/
function confirm_box($check, $title = '', $hidden = '', $html_body = 'confirm_body.html', $u_action = '')
{
	global $user, $template, $db;
	global $phpEx, $phpbb_root_path;

	if (isset($_POST['cancel']))
	{
		return false;
	}

	$confirm = false;
	if (isset($_POST['confirm']))
	{
		// language frontier
		if ($_POST['confirm'] == $user->lang['YES'])
		{
			$confirm = true;
		}
	}

	if ($check && $confirm)
	{
		$user_id = request_var('user_id', 0);
		$session_id = request_var('sess', '');
		$confirm_key = request_var('confirm_key', '');

		if ($user_id != $user->data['user_id'] || $session_id != $user->session_id || !$confirm_key || !$user->data['user_last_confirm_key'] || $confirm_key != $user->data['user_last_confirm_key'])
		{
			return false;
		}

		// Reset user_last_confirm_key
		$sql = 'UPDATE ' . USERS_TABLE . " SET user_last_confirm_key = ''
			WHERE user_id = " . $user->data['user_id'];
		$db->sql_query($sql);

		return true;
	}
	else if ($check)
	{
		return false;
	}

	$s_hidden_fields = build_hidden_fields(array(
		'user_id'	=> $user->data['user_id'],
		'sess'		=> $user->session_id,
		'sid'		=> $user->session_id)
	);

	// generate activation key
	$confirm_key = gen_rand_string(10);

	if (defined('IN_ADMIN') && isset($user->data['session_admin']) && $user->data['session_admin'])
	{
		adm_page_header((!isset($user->lang[$title])) ? $user->lang['CONFIRM'] : $user->lang[$title]);
	}
	else
	{
		page_header((!isset($user->lang[$title])) ? $user->lang['CONFIRM'] : $user->lang[$title]);
	}

	$template->set_filenames(array(
		'body' => $html_body)
	);

	// If activation key already exist, we better do not re-use the key (something very strange is going on...)
	if (request_var('confirm_key', ''))
	{
		// This should not occur, therefore we cancel the operation to safe the user
		return false;
	}

	// re-add sid / transform & to &amp; for user->page (user->page is always using &)
	$use_page = ($u_action) ? $phpbb_root_path . $u_action : $phpbb_root_path . str_replace('&', '&amp;', $user->page['page']);
	$u_action = reapply_sid($use_page);
	$u_action .= ((strpos($u_action, '?') === false) ? '?' : '&amp;') . 'confirm_key=' . $confirm_key;

	$template->assign_vars(array(
		'MESSAGE_TITLE'		=> (!isset($user->lang[$title])) ? $user->lang['CONFIRM'] : $user->lang[$title],
		'MESSAGE_TEXT'		=> (!isset($user->lang[$title . '_CONFIRM'])) ? $title : $user->lang[$title . '_CONFIRM'],

		'YES_VALUE'			=> $user->lang['YES'],
		'S_CONFIRM_ACTION'	=> $u_action,
		'S_HIDDEN_FIELDS'	=> $hidden . $s_hidden_fields)
	);

	$sql = 'UPDATE ' . USERS_TABLE . " SET user_last_confirm_key = '" . $db->sql_escape($confirm_key) . "'
		WHERE user_id = " . $user->data['user_id'];
	$db->sql_query($sql);

	if (defined('IN_ADMIN') && isset($user->data['session_admin']) && $user->data['session_admin'])
	{
		adm_page_footer();
	}
	else
	{
		page_footer();
	}
}

/**
* Generate login box or verify password
*/
function login_box($redirect = '', $l_explain = '', $l_success = '', $admin = false, $s_display = true)
{
	global $db, $user, $template, $auth, $phpEx, $phpbb_root_path, $config;

	$err = '';

	// Make sure user->setup() has been called
	if (empty($user->lang))
	{
		$user->setup();
	}

	// Print out error if user tries to authenticate as an administrator without having the privileges...
	if ($admin && !$auth->acl_get('a_'))
	{
		// Not authd
		// anonymous/inactive users are never able to go to the ACP even if they have the relevant permissions
		if ($user->data['is_registered'])
		{
			add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
		}
		trigger_error('NO_AUTH_ADMIN');
	}

	if (isset($_POST['login']))
	{
		$username	= request_var('username', '', true);
		$password	= request_var('password', '', true);
		$autologin	= (!empty($_POST['autologin'])) ? true : false;
		$viewonline = (!empty($_POST['viewonline'])) ? 0 : 1;
		$admin 		= ($admin) ? 1 : 0;

		// Check if the supplied username is equal to the one stored within the database if re-authenticating
		if ($admin && utf8_clean_string($username) != utf8_clean_string($user->data['username']))
		{
			// We log the attempt to use a different username...
			add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
			trigger_error('NO_AUTH_ADMIN_USER_DIFFER');
		}

		// If authentication is successful we redirect user to previous page
		$result = $auth->login($username, $password, $autologin, $viewonline, $admin);

		// If admin authentication and login, we will log if it was a success or not...
		// We also break the operation on the first non-success login - it could be argued that the user already knows 
		if ($admin)
		{
			if ($result['status'] == LOGIN_SUCCESS)
			{
				add_log('admin', 'LOG_ADMIN_AUTH_SUCCESS');
			}
			else
			{
				// Only log the failed attempt if a real user tried to.
				// anonymous/inactive users are never able to go to the ACP even if they have the relevant permissions
				if ($user->data['is_registered'])
				{
					add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
				}
			}
		}

		// The result parameter is always an array, holding the relevant information...
		if ($result['status'] == LOGIN_SUCCESS)
		{
			$redirect = request_var('redirect', "{$phpbb_root_path}index.$phpEx");
			$message = ($l_success) ? $l_success : $user->lang['LOGIN_REDIRECT'];
			$l_redirect = ($admin) ? $user->lang['PROCEED_TO_ACP'] : (($redirect === "{$phpbb_root_path}index.$phpEx") ? $user->lang['RETURN_INDEX'] : $user->lang['RETURN_PAGE']);

			// append/replace SID (may change during the session for AOL users)
			$redirect = reapply_sid($redirect);

			meta_refresh(3, $redirect);
			trigger_error($message . '<br /><br />' . sprintf($l_redirect, '<a href="' . $redirect . '">', '</a>'));
		}

		// Something failed, determine what...
		if ($result['status'] == LOGIN_BREAK)
		{
			trigger_error($result['error_msg'], E_USER_ERROR);
		}

		// Special cases... determine
		switch ($result['status'])
		{
			case LOGIN_ERROR_ATTEMPTS:

				// Show confirm image
				$sql = 'DELETE FROM ' . CONFIRM_TABLE . "
					WHERE session_id = '" . $db->sql_escape($user->session_id) . "'
						AND confirm_type = " . CONFIRM_LOGIN;
				$db->sql_query($sql);

				// Generate code
				$code = gen_rand_string(mt_rand(5, 8));
				$confirm_id = md5(unique_id($user->ip));

				$sql = 'INSERT INTO ' . CONFIRM_TABLE . ' ' . $db->sql_build_array('INSERT', array(
					'confirm_id'	=> (string) $confirm_id,
					'session_id'	=> (string) $user->session_id,
					'confirm_type'	=> (int) CONFIRM_LOGIN,
					'code'			=> (string) $code)
				);
				$db->sql_query($sql);

				$template->assign_vars(array(
					'S_CONFIRM_CODE'			=> true,
					'CONFIRM_ID'				=> $confirm_id,
					'CONFIRM_IMAGE'				=> '<img src="' . append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=confirm&amp;id=' . $confirm_id . '&amp;type=' . CONFIRM_LOGIN) . '" alt="" title="" />',
					'L_LOGIN_CONFIRM_EXPLAIN'	=> sprintf($user->lang['LOGIN_CONFIRM_EXPLAIN'], '<a href="mailto:' . htmlspecialchars($config['board_contact']) . '">', '</a>'),
				));

				$err = $user->lang[$result['error_msg']];

			break;

			// Username, password, etc...
			default:
				$err = $user->lang[$result['error_msg']];

				// Assign admin contact to some error messages
				if ($result['error_msg'] == 'LOGIN_ERROR_USERNAME' || $result['error_msg'] == 'LOGIN_ERROR_PASSWORD')
				{
					$err = (!$config['board_contact']) ? sprintf($user->lang[$result['error_msg']], '', '') : sprintf($user->lang[$result['error_msg']], '<a href="mailto:' . htmlspecialchars($config['board_contact']) . '">', '</a>');
				}
			break;
		}
	}

	if (!$redirect)
	{
		// We just use what the session code determined...
		// If we are not within the admin directory we use the page dir...
		$redirect = '';

		if (!$admin)
		{
			$redirect .= ($user->page['page_dir']) ? $user->page['page_dir'] . '/' : '';
		}

		$redirect .= $user->page['page_name'] . (($user->page['query_string']) ? '?' . $user->page['query_string'] : '');
	}

	$s_hidden_fields = build_hidden_fields(array('redirect' => $redirect, 'sid' => $user->session_id));

	$template->assign_vars(array(
		'LOGIN_ERROR'		=> $err,
		'LOGIN_EXPLAIN'		=> $l_explain,

		'U_SEND_PASSWORD' 		=> ($config['email_enable']) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=sendpassword') : '',
		'U_RESEND_ACTIVATION'	=> ($config['require_activation'] != USER_ACTIVATION_NONE && $config['email_enable']) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=resend_act') : '',
		'U_TERMS_USE'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=terms'),
		'U_PRIVACY'				=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=privacy'),

		'S_DISPLAY_FULL_LOGIN'	=> ($s_display) ? true : false,
		'S_AUTOLOGIN_ENABLED'	=> ($config['allow_autologin']) ? true : false,
		'S_LOGIN_ACTION'		=> (!$admin) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login') : append_sid("index.$phpEx", false, true, $user->session_id), // Needs to stay index.$phpEx because we are within the admin directory
		'S_HIDDEN_FIELDS' 		=> $s_hidden_fields,

		'S_ADMIN_AUTH'			=> $admin,
		'USERNAME'				=> ($admin) ? $user->data['username'] : '')
	);

	page_header($user->lang['LOGIN']);

	$template->set_filenames(array(
		'body' => 'login_body.html')
	);
	make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

	page_footer();
}

/**
* Generate forum login box
*/
function login_forum_box($forum_data)
{
	global $db, $config, $user, $template, $phpEx;

	$password = request_var('password', '', true);

	$sql = 'SELECT forum_id
		FROM ' . FORUMS_ACCESS_TABLE . '
		WHERE forum_id = ' . $forum_data['forum_id'] . '
			AND user_id = ' . $user->data['user_id'] . "
			AND session_id = '" . $db->sql_escape($user->session_id) . "'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if ($row)
	{
		return true;
	}

	if ($password)
	{
		// Remove expired authorised sessions
		$sql = 'SELECT session_id
			FROM ' . SESSIONS_TABLE;
		$result = $db->sql_query($sql);

		if ($row = $db->sql_fetchrow($result))
		{
			$sql_in = array();
			do
			{
				$sql_in[] = (string) $row['session_id'];
			}
			while ($row = $db->sql_fetchrow($result));

			// Remove expired sessions
			$sql = 'DELETE FROM ' . FORUMS_ACCESS_TABLE . '
				WHERE ' . $db->sql_in_set('session_id', $sql_in, true);
			$db->sql_query($sql);
		}
		$db->sql_freeresult($result);

		if ($password == $forum_data['forum_password'])
		{
			$sql_ary = array(
				'forum_id'		=> (int) $forum_data['forum_id'],
				'user_id'		=> (int) $user->data['user_id'],
				'session_id'	=> (string) $user->session_id,
			);

			$db->sql_query('INSERT INTO ' . FORUMS_ACCESS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

			return true;
		}

		$template->assign_var('LOGIN_ERROR', $user->lang['WRONG_PASSWORD']);
	}

	page_header($user->lang['LOGIN']);

	$template->assign_vars(array(
		'S_HIDDEN_FIELDS'		=> build_hidden_fields(array('f' => $forum_data['forum_id'])))
	);

	$template->set_filenames(array(
		'body' => 'login_forum.html')
	);
	
	page_footer();
}

// Content related functions

/**
* Bump Topic Check - used by posting and viewtopic
*/
function bump_topic_allowed($forum_id, $topic_bumped, $last_post_time, $topic_poster, $last_topic_poster)
{
	global $config, $auth, $user;

	// Check permission and make sure the last post was not already bumped
	if (!$auth->acl_get('f_bump', $forum_id) || $topic_bumped)
	{
		return false;
	}

	// Check bump time range, is the user really allowed to bump the topic at this time?
	$bump_time = ($config['bump_type'] == 'm') ? $config['bump_interval'] * 60 : (($config['bump_type'] == 'h') ? $config['bump_interval'] * 3600 : $config['bump_interval'] * 86400);

	// Check bump time
	if ($last_post_time + $bump_time > time())
	{
		return false;
	}

	// Check bumper, only topic poster and last poster are allowed to bump
	if ($topic_poster != $user->data['user_id'] && $last_topic_poster != $user->data['user_id'] && !$auth->acl_get('m_', $forum_id))
	{
		return false;
	}

	// A bump time of 0 will completely disable the bump feature... not intended but might be useful.
	return $bump_time;
}

/**
* Generates a text with approx. the specified length which contains the specified words and their context
*
* @param	string	$text	The full text from which context shall be extracted
* @param	string	$words	An array of words which should be contained in the result, has to be a valid part of a PCRE pattern (escape with preg_quote!)
* @param	int		$length	The desired length of the resulting text, however the result might be shorter or longer than this value
*
* @return	string			Context of the specified words seperated by "..."
*/
function get_context($text, $words, $length = 400)
{
	// first replace all whitespaces with single spaces
	$text = preg_replace('/\s+/u', ' ', $text);

	$word_indizes = array();
	if (sizeof($words))
	{
		$match = '';
		// find the starting indizes of all words
		foreach ($words as $word)
		{
			if (preg_match('#(?:[^\w]|^)(' . $word . ')(?:[^\w]|$)#i', $text, $match))
			{
				$pos = strpos($text, $match[1]);
				if ($pos !== false)
				{
					$word_indizes[] = $pos;
				}
			}
		}
		unset($match);

		if (sizeof($word_indizes))
		{
			$word_indizes = array_unique($word_indizes);
			sort($word_indizes);

			$wordnum = sizeof($word_indizes);
			// number of characters on the right and left side of each word
			$sequence_length = (int) ($length / (2 * $wordnum)) - 2;
			$final_text = '';
			$word = $j = 0;
			$final_text_index = -1;

			// cycle through every character in the original text
			for ($i = $word_indizes[$word], $n = strlen($text); $i < $n; $i++)
			{
				// if the current position is the start of one of the words then append $sequence_length characters to the final text
				if (isset($word_indizes[$word]) && ($i == $word_indizes[$word]))
				{
					if ($final_text_index < $i - $sequence_length - 1)
					{
						$final_text .= '... ' . preg_replace('#^([^ ]*)#', '', substr($text, $i - $sequence_length, $sequence_length));
					}
					else
					{
						// if the final text is already nearer to the current word than $sequence_length we only append the text
						// from its current index on and distribute the unused length to all other sequenes
						$sequence_length += (int) (($final_text_index - $i + $sequence_length + 1) / (2 * $wordnum));
						$final_text .= substr($text, $final_text_index + 1, $i - $final_text_index - 1);
					}
					$final_text_index = $i - 1;

					// add the following characters to the final text (see below)
					$word++;
					$j = 1;
				}

				if ($j > 0)
				{
					// add the character to the final text and increment the sequence counter
					$final_text .= $text[$i];
					$final_text_index++;
					$j++;

					// if this is a whitespace then check whether we are done with this sequence
					if ($text[$i] == ' ')
					{
						// only check whether we have to exit the context generation completely if we haven't already reached the end anyway
						if ($i + 4 < $n)
						{
							if (($j > $sequence_length && $word >= $wordnum) || strlen($final_text) > $length)
							{
								$final_text .= ' ...';
								break;
							}
						}
						else
						{
							// make sure the text really reaches the end
							$j -= 4;
						}

						// stop context generation and wait for the next word
						if ($j > $sequence_length)
						{
							$j = 0;
						}
					}
				}
			}
			return $final_text;
		}
	}

	if (!sizeof($words) || !sizeof($word_indizes))
	{
		return (strlen($text) >= $length + 3) ? substr($text, 0, $length) . '...' : $text;
	}
}

/**
* Decode text whereby text is coming from the db and expected to be pre-parsed content
* We are placing this outside of the message parser because we are often in need of it...
*/
function decode_message(&$message, $bbcode_uid = '')
{
	global $config;

	if ($bbcode_uid)
	{
		$match = array('<br />', "[/*:m:$bbcode_uid]", ":u:$bbcode_uid", ":o:$bbcode_uid", ":$bbcode_uid");
		$replace = array("\n", '', '', '', '');
	}
	else
	{
		$match = array('<br />');
		$replace = array("\n");
	}

	$message = str_replace($match, $replace, $message);

	$match = get_preg_expression('bbcode_htm');
	$replace = array('\1', '\2', '\1', '', '');

	$message = preg_replace($match, $replace, $message);
}

/**
* Strips all bbcode from a text and returns the plain content
*/
function strip_bbcode(&$text, $uid = '')
{
	if (!$uid)
	{
		$uid = '[0-9a-z]{5,}';
	}

	$text = preg_replace("#\[\/?[a-z0-9\*\+\-]+(?:=.*?)?(?::[a-z])?(\:?$uid)\]#", ' ', $text);

	$match = get_preg_expression('bbcode_htm');
	$replace = array('\1', '\2', '\1', '', '');
	
	$text = preg_replace($match, $replace, $text);
}

/**
* For display of custom parsed text on user-facing pages
* Expects $text to be the value directly from the database (stored value)
*/
function generate_text_for_display($text, $uid, $bitfield, $flags)
{
	static $bbcode;

	if (!$text)
	{
		return '';
	}

	$text = str_replace("\n", '<br />', censor_text($text));

	// Parse bbcode if bbcode uid stored and bbcode enabled
	if ($uid && ($flags & OPTION_FLAG_BBCODE))
	{
		if (!class_exists('bbcode'))
		{
			global $phpbb_root_path, $phpEx;
			include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
		}

		if (empty($bbcode))
		{
			$bbcode = new bbcode($bitfield);
		}
		else
		{
			$bbcode->bbcode($bitfield);
		}
		
		$bbcode->bbcode_second_pass($text, $uid);
	}

	$text = smiley_text($text, !($flags & OPTION_FLAG_SMILIES));

	return $text;
}

/**
* For parsing custom parsed text to be stored within the database.
* This function additionally returns the uid and bitfield that needs to be stored.
* Expects $text to be the value directly from request_var() and in it's non-parsed form
*/
function generate_text_for_storage(&$text, &$uid, &$bitfield, &$flags, $allow_bbcode = false, $allow_urls = false, $allow_smilies = false)
{
	global $phpbb_root_path, $phpEx;

	$uid = $bitfield = '';

	if (!$text)
	{
		return;
	}

	if (!class_exists('parse_message'))
	{
		include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
	}

	$message_parser = new parse_message($text);
	$message_parser->parse($allow_bbcode, $allow_urls, $allow_smilies);

	$text = $message_parser->message;
	$uid = $message_parser->bbcode_uid;

	// If the bbcode_bitfield is empty, there is no need for the uid to be stored.
	if (!$message_parser->bbcode_bitfield)
	{
		$uid = '';
	}

	$flags = (($allow_bbcode) ? 1 : 0) + (($allow_smilies) ? 2 : 0) + (($allow_urls) ? 4 : 0);
	$bitfield = $message_parser->bbcode_bitfield;

	return;
}

/**
* For decoding custom parsed text for edits as well as extracting the flags
* Expects $text to be the value directly from the database (pre-parsed content)
*/
function generate_text_for_edit($text, $uid, $flags)
{
	global $phpbb_root_path, $phpEx;

	decode_message($text, $uid);

	return array(
		'allow_bbcode'	=> ($flags & OPTION_FLAG_BBCODE) ? 1 : 0,
		'allow_smilies'	=> ($flags & OPTION_FLAG_SMILIES) ? 1 : 0,
		'allow_urls'	=> ($flags & OPTION_FLAG_LINKS) ? 1 : 0,
		'text'			=> $text
	);
}

/**
* make_clickable function
*
* Replace magic urls of form http://xxx.xxx., www.xxx. and xxx@xxx.xxx.
* Cuts down displayed size of link if over 50 chars, turns absolute links
* into relative versions when the server/script path matches the link
*/
function make_clickable($text, $server_url = false)
{
	if ($server_url === false)
	{
		$server_url = generate_board_url();
	}

	static $magic_url_match;
	static $magic_url_replace;

	if (!is_array($magic_url_match))
	{
		$magic_url_match = $magic_url_replace = array();
		// Be sure to not let the matches cross over. ;)

		// relative urls for this board
		$magic_url_match[] = '#(^|[\n\t (])(' . preg_quote($server_url, '#') . ')/(([^[ \t\n\r<"\'\)&]+|&(?!lt;|quot;))*)#ie';
		$magic_url_replace[] = "'\$1<!-- l --><a href=\"\$2/' . preg_replace('/(&amp;|\?)sid=[0-9a-f]{32}/', '\\1', '\$3') . '\">' . preg_replace('/(&amp;|\?)sid=[0-9a-f]{32}/', '\\1', '\$3') . '</a><!-- l -->'";

		// matches a xxxx://aaaaa.bbb.cccc. ...
		$magic_url_match[] = '#(^|[\n\t (])([\w]+:/{2}.*?([^[ \t\n\r<"\'\)&]+|&(?!lt;|quot;))*)#ie';
		$magic_url_replace[] = "'\$1<!-- m --><a href=\"\$2\">' . ((strlen('\$2') > 55) ? substr(str_replace('&amp;', '&', '\$2'), 0, 39) . ' ... ' . substr(str_replace('&amp;', '&', '\$2'), -10) : '\$2') . '</a><!-- m -->'";

		// matches a "www.xxxx.yyyy[/zzzz]" kinda lazy URL thing
		$magic_url_match[] = '#(^|[\n\t (])(w{3}\.[\w\-]+\.[\w\-.\~]+(?:[^[ \t\n\r<"\'\)&]+|&(?!lt;|quot;))*)#ie';
		$magic_url_replace[] = "'\$1<!-- w --><a href=\"http://\$2\">' . ((strlen('\$2') > 55) ? substr(str_replace('&amp;', '&', '\$2'), 0, 39) . ' ... ' . substr(str_replace('&amp;', '&', '\$2'), -10) : '\$2') . '</a><!-- w -->'";

		// matches an email@domain type address at the start of a line, or after a space or after what might be a BBCode.
		$magic_url_match[] = '/(^|[\n\t )])(' . get_preg_expression('email') . ')/ie';
		$magic_url_replace[] = "'\$1<!-- e --><a href=\"mailto:\$2\">' . ((strlen('\$2') > 55) ? substr('\$2', 0, 39) . ' ... ' . substr('\$2', -10) : '\$2') . '</a><!-- e -->'";
	}

	return preg_replace($magic_url_match, $magic_url_replace, $text);
}

/**
* Censoring
*/
function censor_text($text)
{
	static $censors;
	global $cache;

	if (!isset($censors) || !is_array($censors))
	{
		// obtain_word_list is taking care of the users censor option and the board-wide option
		$censors = $cache->obtain_word_list();
	}

	if (sizeof($censors))
	{
		return preg_replace($censors['match'], $censors['replace'], $text);
	}

	return $text;
}

/**
* Smiley processing
*/
function smiley_text($text, $force_option = false)
{
	global $config, $user, $phpbb_root_path;

	if ($force_option || !$config['allow_smilies'] || !$user->optionget('viewsmilies'))
	{
		return preg_replace('#<!\-\- s(.*?) \-\-><img src="\{SMILIES_PATH\}\/.*? \/><!\-\- s\1 \-\->#', '\1', $text);
	}
	else
	{
		return str_replace('<img src="{SMILIES_PATH}', '<img src="' . $phpbb_root_path . $config['smilies_path'], $text);
	}
}

/**
* Inline Attachment processing
*/
function parse_inline_attachments(&$text, &$attachments, &$update_count, $forum_id = 0, $preview = false)
{
	global $config, $user;

	if (!function_exists('display_attachments'))
	{
		global $phpbb_root_path, $phpEx;
		include("{$phpbb_root_path}includes/functions_display.$phpEx");
	}

	$attachments = display_attachments($forum_id, NULL, $attachments, $update_count, false, true);
	$tpl_size = sizeof($attachments);

	$unset_tpl = array();

	preg_match_all('#<!\-\- ia([0-9]+) \-\->(.*?)<!\-\- ia\1 \-\->#', $text, $matches, PREG_PATTERN_ORDER);

	$replace = array();
	foreach ($matches[0] as $num => $capture)
	{
		// Flip index if we are displaying the reverse way
		$index = ($config['display_order']) ? ($tpl_size-($matches[1][$num] + 1)) : $matches[1][$num];

		$replace['from'][] = $matches[0][$num];
		$replace['to'][] = (isset($attachments[$index])) ? $attachments[$index] : sprintf($user->lang['MISSING_INLINE_ATTACHMENT'], $matches[2][array_search($index, $matches[1])]);

		$unset_tpl[] = $index;
	}

	if (isset($replace['from']))
	{
		$text = str_replace($replace['from'], $replace['to'], $text);
	}

	return array_unique($unset_tpl);
}

/**
* Check if extension is allowed to be posted within forum X (forum_id 0 == private messaging)
*/
function extension_allowed($forum_id, $extension, &$extensions)
{
	if (!sizeof($extensions))
	{
		global $cache;
		$extensions = $cache->obtain_attach_extensions();
	}

	if (!isset($extensions['_allowed_'][$extension]))
	{
		return false;
	}

	$check = $extensions['_allowed_'][$extension];

	if (is_array($check))
	{
		// Check for private messaging AND all forums allowed
		if (sizeof($check) == 1 && $check[0] == 0)
		{
			return true;
		}

		return (!in_array($forum_id, $check)) ? false : true;
	}

	return ($forum_id == 0) ? false : true;
}

// Little helpers

/**
* Little helper for the build_hidden_fields function
*/
function _build_hidden_fields($key, $value, $specialchar, $stripslashes)
{
	$hidden_fields = '';

	if (!is_array($value))
	{
		$value = ($stripslashes) ? stripslashes($value) : $value;
		$value = ($specialchar) ? htmlspecialchars($value, ENT_COMPAT, 'UTF-8') : $value;

		$hidden_fields .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />' . "\n";
	}
	else
	{
		foreach ($value as $_key => $_value)
		{
			$_key = ($stripslashes) ? stripslashes($_key) : $_key;
			$_key = ($specialchar) ? htmlspecialchars($_key, ENT_COMPAT, 'UTF-8') : $_key;

			$hidden_fields .= _build_hidden_fields($key . '[' . $_key . ']', $_value, $specialchar, $stripslashes);
		}
	}

	return $hidden_fields;
}

/**
* Build simple hidden fields from array
*
* @param array $field_ary an array of values to build the hidden field from
* @param bool $specialchar if true, keys and values get specialchared
* @param bool $stripslashes if true, keys and values get stripslashed
*
* @return string the hidden fields
*/
function build_hidden_fields($field_ary, $specialchar = false, $stripslashes = false)
{
	$s_hidden_fields = '';

	foreach ($field_ary as $name => $vars)
	{
		$name = ($stripslashes) ? stripslashes($name) : $name;
		$name = ($specialchar) ? htmlspecialchars($name, ENT_COMPAT, 'UTF-8') : $name;

		$s_hidden_fields .= _build_hidden_fields($name, $vars, $specialchar, $stripslashes);
	}

	return $s_hidden_fields;
}

/**
* Parse cfg file
*/
function parse_cfg_file($filename, $lines = false)
{
	$parsed_items = array();

	if ($lines === false)
	{
		$lines = file($filename);
	}

	foreach ($lines as $line)
	{
		$line = trim($line);

		if (!$line || $line[0] == '#' || ($delim_pos = strpos($line, '=')) === false)
		{
			continue;
		}

		// Determine first occurrence, since in values the equal sign is allowed
		$key = strtolower(trim(substr($line, 0, $delim_pos)));
		$value = trim(substr($line, $delim_pos + 1));

		if (in_array($value, array('off', 'false', '0')))
		{
			$value = false;
		}
		else if (in_array($value, array('on', 'true', '1')))
		{
			$value = true;
		}
		else if (!trim($value))
		{
			$value = '';
		}
		else if (($value[0] == "'" && $value[sizeof($value) - 1] == "'") || ($value[0] == '"' && $value[sizeof($value) - 1] == '"'))
		{
			$value = substr($value, 1, sizeof($value)-2);
		}
	
		$parsed_items[$key] = $value;
	}
	
	return $parsed_items;
}

/**
* Add log event
*/
function add_log()
{
	global $db, $user;

	$args = func_get_args();

	$mode			= array_shift($args);
	$reportee_id	= ($mode == 'user') ? intval(array_shift($args)) : '';
	$forum_id		= ($mode == 'mod') ? intval(array_shift($args)) : '';
	$topic_id		= ($mode == 'mod') ? intval(array_shift($args)) : '';
	$action			= array_shift($args);
	$data			= (!sizeof($args)) ? '' : serialize($args);

	$sql_ary = array(
		'user_id'		=> (empty($user->data)) ? ANONYMOUS : $user->data['user_id'],
		'log_ip'		=> $user->ip,
		'log_time'		=> time(),
		'log_operation'	=> $action,
		'log_data'		=> $data,
	);
	
	switch ($mode)
	{
		case 'admin':
			$sql_ary['log_type'] = LOG_ADMIN;
		break;
		
		case 'mod':
			$sql_ary += array(
				'log_type'	=> LOG_MOD,
				'forum_id'	=> $forum_id,
				'topic_id'	=> $topic_id
			);
		break;

		case 'user':
			$sql_ary += array(
				'log_type'		=> LOG_USERS,
				'reportee_id'	=> $reportee_id
			);
		break;

		case 'critical':
			$sql_ary['log_type'] = LOG_CRITICAL;
		break;
		
		default:
			return false;
	}

	$db->sql_query('INSERT INTO ' . LOG_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));

	return $db->sql_nextid();
}

/**
* Return a nicely formatted backtrace (parts from the php manual by diz at ysagoon dot com)
*/
function get_backtrace()
{
	global $phpbb_root_path;

	$output = '<div style="font-family: monospace;">';
	$backtrace = debug_backtrace();
	$path = phpbb_realpath($phpbb_root_path);

	foreach ($backtrace as $number => $trace)
	{
		// We skip the first one, because it only shows this file/function
		if ($number == 0)
		{
			continue;
		}

		// Strip the current directory from path
		$trace['file'] = str_replace(array($path, '\\'), array('', '/'), $trace['file']);
		$trace['file'] = substr($trace['file'], 1);
		$args = array();

		// If include/require/include_once is not called, do not show arguments - they may contain sensible information
		if (!in_array($trace['function'], array('include', 'require', 'include_once')))
		{
			unset($trace['args']);
		}
		else
		{
			// Path...
			if (!empty($trace['args'][0]))
			{
				$argument = htmlspecialchars($trace['args'][0]);
				$argument = str_replace(array($path, '\\'), array('', '/'), $argument);
				$argument = substr($argument, 1);
				$args[] = "'{$argument}'";
			}
		}

		$trace['class'] = (!isset($trace['class'])) ? '' : $trace['class'];
		$trace['type'] = (!isset($trace['type'])) ? '' : $trace['type'];

		$output .= '<br />';
		$output .= '<b>FILE:</b> ' . htmlspecialchars($trace['file']) . '<br />';
		$output .= '<b>LINE:</b> ' . $trace['line'] . '<br />';

		$output .= '<b>CALL:</b> ' . htmlspecialchars($trace['class'] . $trace['type'] . $trace['function']) . '(' . ((sizeof($args)) ? implode(', ', $args) : '') . ')<br />';
	}
	$output .= '</div>';
	return $output;
}

/**
* This function returns a regular expression pattern for commonly used expressions
* Use with / as delimiter for email mode
* mode can be: email|bbcode_htm
*/
function get_preg_expression($mode)
{
	switch ($mode)
	{
		case 'email':
			return '[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*[a-z]+';
		break;

		case 'bbcode_htm':
			return array(
				'#<!\-\- e \-\-><a href="mailto:(.*?)">.*?</a><!\-\- e \-\->#',
				'#<!\-\- (l|m|w) \-\-><a href="(.*?)">.*?</a><!\-\- \1 \-\->#',
				'#<!\-\- s(.*?) \-\-><img src="\{SMILIES_PATH\}\/.*? \/><!\-\- s\1 \-\->#',
				'#<!\-\- .*? \-\->#s',
				'#<.*?>#s',
			);
		break;
	}

	return '';
}

/**
* Truncates string while retaining special characters if going over the max length
* The default max length is 60 at the moment
*/
function truncate_string($string, $max_length = 60, $allow_reply = true, $append = '')
{
	$chars = array();

	$strip_reply = false;
	$stripped = false;
	if ($allow_reply && strpos($string, 'Re: ') === 0)
	{
		$strip_reply = true;
		$string = substr($string, 4);
	}

	$_chars = utf8_str_split(htmlspecialchars_decode($string));
	$chars = array_map('htmlspecialchars', $_chars);

	// Now check the length ;)
	if (sizeof($chars) > $max_length)
	{
		// Cut off the last elements from the array
		$string = implode('', array_slice($chars, 0, $max_length));
		$stripped = true;		
	}

	if ($strip_reply)
	{
		$string = 'Re: ' . $string;
	}
	
	if ($append != '' && $stripped)
	{
		$string = $string . $append;
	}

	return $string;
}

/**
* Get username details for placing into templates.
*
* @param string $mode Can be profile (for getting an url to the profile), username (for obtaining the username), colour (for obtaining the user colour) or full (for obtaining a html string representing a coloured link to the users profile).
* @param int $user_id The users id
* @param string $username The users name
* @param string $username_colour The users colour
* @param string $guest_username optional parameter to specify the guest username. It will be used in favor of the GUEST language variable then.
* @param string $custom_profile_url optional parameter to specify a profile url. The user id get appended to this url as &amp;u={user_id}
*
* @return string A string consisting of what is wanted based on $mode.
*/
function get_username_string($mode, $user_id, $username, $username_colour = '', $guest_username = false, $custom_profile_url = false)
{
	global $phpbb_root_path, $phpEx, $user;

	$full_string = $profile_url = '';
	$username_colour = ($username_colour) ? '#' . $username_colour : '';

	if ($guest_username === false)
	{
		$username = ($username) ? $username : $user->lang['GUEST'];
	}
	else
	{
		$username = ($user_id && $user_id != ANONYMOUS) ? $username : ((!empty($guest_username)) ? $guest_username : $user->lang['GUEST']);
	}

	// Only show the link if not anonymous
	if ($user_id && $user_id != ANONYMOUS)
	{
		$profile_url = ($custom_profile_url !== false) ? $custom_profile_url : append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile');
		$profile_url .= '&amp;u=' . (int) $user_id;
	}
	else
	{
		$profile_url = '';
	}

	switch ($mode)
	{
		case 'profile':
			return $profile_url;
		break;

		case 'username':
			return $username;
		break;

		case 'colour':
			return $username_colour;
		break;

		case 'full':
		default:

			$tpl = '';
			if (!$profile_url && !$username_colour)
			{
				$tpl = '{USERNAME}';
			}
			else if (!$profile_url &&  $username_colour)
			{
				$tpl = '<span style="color: {USERNAME_COLOUR}; font-weight: bold;">{USERNAME}</span>';
			}
			else if ($profile_url && !$username_colour)
			{
				$tpl = '<a href="{PROFILE_URL}">{USERNAME}</a>';
			}
			else if ($profile_url && $username_colour)
			{
				$tpl = '<a href="{PROFILE_URL}" style="color: {USERNAME_COLOUR}; font-weight: bold;">{USERNAME}</a>';
			}

			return str_replace(array('{PROFILE_URL}', '{USERNAME_COLOUR}', '{USERNAME}'), array($profile_url, $username_colour, $username), $tpl);
		break;
	}
}

/**
* Wrapper for php's checkdnsrr function.
*
* The windows failover is from the php manual
* Please make sure to check the return value for === true and === false, since NULL could
* be returned too.
* 
* @return true if entry found, false if not, NULL if this function is not supported by this environment
*/
function phpbb_checkdnsrr($host, $type = '')
{
	$type = (!$type) ? 'MX' : $type;

	if (strpos(PHP_OS, 'WIN') !== false)
	{
		if (!function_exists('exec'))
		{
			return NULL;
		}

		@exec('nslookup -type=' . escapeshellarg($type) . ' ' . escapeshellarg($host), $output);

		// If output is empty, the nslookup failed
		if (empty($output))
		{
			return NULL;
		}

		foreach ($output as $line)
		{
			if (!trim($line))
			{
				continue;
			}

			// Valid records begin with host name:
			if (strpos($line, $host) === 0)
			{
				return true;
			}
		}

		return false;
	}
	else if (function_exists('checkdnsrr'))
	{
		return (checkdnsrr($host, $type)) ? true : false;
	}

	return NULL;
}

// Handler, header and footer

/**
* Error and message handler, call with trigger_error if reqd
*/
function msg_handler($errno, $msg_text, $errfile, $errline)
{
	global $cache, $db, $auth, $template, $config, $user;
	global $phpEx, $phpbb_root_path, $starttime, $msg_title, $msg_long_text;

	// Message handler is stripping text. In case we need it, we are possible to define long text...
	if (isset($msg_long_text) && $msg_long_text && !$msg_text)
	{
		$msg_text = $msg_long_text;
	}

	switch ($errno)
	{
		case E_NOTICE:
		case E_WARNING:

			// Check the error reporting level and return if the error level does not match
			// Additionally do not display notices if we suppress them via @
			// If DEBUG is defined the default level is E_ALL
			if (($errno & ((defined('DEBUG') && error_reporting()) ? E_ALL : error_reporting())) == 0)
			{
				return;
			}

			if (strpos($errfile, 'cache') === false && strpos($errfile, 'template.') === false)
			{
				// remove complete path to installation, with the risk of changing backslashes meant to be there
				$errfile = str_replace(array(phpbb_realpath($phpbb_root_path), '\\'), array('', '/'), $errfile);
				$msg_text = str_replace(array(phpbb_realpath($phpbb_root_path), '\\'), array('', '/'), $msg_text);

				echo '<b>[phpBB Debug] PHP Notice</b>: in file <b>' . $errfile . '</b> on line <b>' . $errline . '</b>: <b>' . $msg_text . '</b><br />' . "\n";
			}

		break;

		case E_USER_ERROR:

			garbage_collection();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
			echo '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">';
			echo '<head>';
			echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
			echo '<title>' . $msg_title . '</title>';
			echo '<link href="' . $phpbb_root_path . 'adm/style/admin.css" rel="stylesheet" type="text/css" media="screen" />';
			echo '</head>';
			echo '<body id="errorpage">';
			echo '<div id="wrap">';
			echo '	<div id="page-header">';
			echo '		<a href="' . $phpbb_root_path . '">Return to forum index</a>';
			echo '	</div>';
			echo '	<div id="page-body">';
			echo '		<div class="panel">';
			echo '			<span class="corners-top"><span></span></span>';
			echo '			<div id="content">';
			echo '				<h1>General Error</h1>';
			
			echo '				<h2>' . $msg_text . '</h2>';
			
			if (!empty($config['board_contact']))
			{
				echo '				<p>Please notify the board administrator or webmaster: <a href="mailto:' . $config['board_contact'] . '">' . $config['board_contact'] . '</a></p>';
			}
			
			echo '			</div>';
			echo '			<span class="corners-bottom"><span></span></span>';
			echo '		</div>';
			echo '	</div>';
			echo '	<div id="page-footer">';
			echo '		Powered by phpBB &copy; ' . date('Y') . ' <a href="http://www.phpbb.com/">phpBB Group</a>';
			echo '	</div>';
			echo '</div>';
			echo '</body>';
			echo '</html>';
			
			exit;
		break;

		case E_USER_WARNING:
		case E_USER_NOTICE:

			define('IN_ERROR_HANDLER', true);

			if (empty($user->data))
			{
				$user->session_begin();
			}

			// We re-init the auth array to get correct results on login/logout
			$auth->acl($user->data);

			if (empty($user->lang))
			{
				$user->setup();
			}

			$msg_text = (!empty($user->lang[$msg_text])) ? $user->lang[$msg_text] : $msg_text;
			$msg_title = (!isset($msg_title)) ? $user->lang['INFORMATION'] : ((!empty($user->lang[$msg_title])) ? $user->lang[$msg_title] : $msg_title);

			if (!defined('HEADER_INC'))
			{
				if (defined('IN_ADMIN') && isset($user->data['session_admin']) && $user->data['session_admin'])
				{
					adm_page_header($msg_title);
				}
				else
				{
					page_header($msg_title);
				}
			}

			$template->set_filenames(array(
				'body' => 'message_body.html')
			);

			$template->assign_vars(array(
				'MESSAGE_TITLE'		=> $msg_title,
				'MESSAGE_TEXT'		=> $msg_text,
				'S_USER_WARNING'	=> ($errno == E_USER_WARNING) ? true : false,
				'S_USER_NOTICE'		=> ($errno == E_USER_NOTICE) ? true : false)
			);

			// We do not want the cron script to be called on error messages
			define('IN_CRON', true);
			
			if (defined('IN_ADMIN') && isset($user->data['session_admin']) && $user->data['session_admin'])
			{
				adm_page_footer();
			}
			else
			{
				page_footer();
			}

			exit;
		break;
	}
}

/**
* Generate page header
*/
function page_header($page_title = '', $display_online_list = true)
{
	global $db, $config, $template, $SID, $_SID, $user, $auth, $phpEx, $phpbb_root_path;

	if (defined('HEADER_INC'))
	{
		return;
	}
	
	define('HEADER_INC', true);

	// gzip_compression
	if ($config['gzip_compress'])
	{
		if (@extension_loaded('zlib') && !headers_sent())
		{
			ob_start('ob_gzhandler');
		}
	}

	// Generate logged in/logged out status
	if ($user->data['user_id'] != ANONYMOUS)
	{
		$u_login_logout = append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=logout');
		$l_login_logout = sprintf($user->lang['LOGOUT_USER'], $user->data['username']);
	}
	else
	{
		$u_login_logout = append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login');
		$l_login_logout = $user->lang['LOGIN'];
	}

	// Last visit date/time
	$s_last_visit = ($user->data['user_id'] != ANONYMOUS) ? $user->format_date($user->data['session_last_visit']) : '';

	// Get users online list ... if required
	$l_online_users = $online_userlist = $l_online_record = '';

	if ($config['load_online'] && $config['load_online_time'] && $display_online_list)
	{
		$userlist_ary = $userlist_visible = array();
		$logged_visible_online = $logged_hidden_online = $guests_online = $prev_user_id = 0;
		$prev_session_ip = $reading_sql = '';

		if (!empty($_REQUEST['f']))
		{
			$f = request_var('f', 0);

			// Do not change this (it is defined as _f_={forum_id}x within session.php)
			$reading_sql = " AND s.session_page LIKE '%\_f\_={$f}x%'";

			// Specify escape character for MSSQL
			if ($db->sql_layer == 'mssql' || $db->sql_layer == 'mssql_odbc')
			{
				$reading_sql .= " ESCAPE '\\' ";
			}
		}

		// Get number of online guests
		if (!$config['load_online_guests'])
		{
			if ($db->sql_layer === 'sqlite')
			{
				$sql = 'SELECT COUNT(session_ip) as num_guests
					FROM (
						SELECT DISTINCT s.session_ip
							FROM ' . SESSIONS_TABLE . ' s
							WHERE s.session_user_id = ' . ANONYMOUS . '
								AND s.session_time >= ' . (time() - ($config['load_online_time'] * 60)) . 
								$reading_sql .
					')';
			}
			else
			{
				$sql = 'SELECT COUNT(DISTINCT s.session_ip) as num_guests
					FROM ' . SESSIONS_TABLE . ' s
					WHERE s.session_user_id = ' . ANONYMOUS . '
						AND s.session_time >= ' . (time() - ($config['load_online_time'] * 60)) . 
					$reading_sql;
			}
			$result = $db->sql_query($sql);
			$guests_online = (int) $db->sql_fetchfield('num_guests');
			$db->sql_freeresult($result);
		}

		$sql = 'SELECT u.username, u.user_id, u.user_type, u.user_allow_viewonline, u.user_colour, s.session_ip, s.session_viewonline
			FROM ' . USERS_TABLE . ' u, ' . SESSIONS_TABLE . ' s
			WHERE s.session_time >= ' . (time() - (intval($config['load_online_time']) * 60)) . 
				$reading_sql .
				((!$config['load_online_guests']) ? ' AND s.session_user_id <> ' . ANONYMOUS : '') . '
				AND u.user_id = s.session_user_id 
			ORDER BY u.username_clean ASC, s.session_ip ASC';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			// User is logged in and therefore not a guest
			if ($row['user_id'] != ANONYMOUS)
			{
				// Skip multiple sessions for one user
				if ($row['user_id'] != $prev_user_id)
				{
					if ($row['user_colour'])
					{
						$user_colour = ' style="color:#' . $row['user_colour'] . '"';
						$row['username'] = '<strong>' . $row['username'] . '</strong>';
					}
					else
					{
						$user_colour = '';
					}

					if ($row['user_allow_viewonline'] && $row['session_viewonline'])
					{
						$user_online_link = $row['username'];
						$logged_visible_online++;
					}
					else
					{
						$user_online_link = '<em>' . $row['username'] . '</em>';
						$logged_hidden_online++;
					}

					if (($row['user_allow_viewonline'] && $row['session_viewonline']) || $auth->acl_get('u_viewonline'))
					{
						if ($row['user_type'] <> USER_IGNORE)
						{
							$user_online_link = '<a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']) . '"' . $user_colour . '>' . $user_online_link . '</a>';
						}
						else
						{
							$user_online_link = ($user_colour) ? '<span' . $user_colour . '>' . $user_online_link . '</span>' : $user_online_link;
						}

						$online_userlist .= ($online_userlist != '') ? ', ' . $user_online_link : $user_online_link;
					}
				}

				$prev_user_id = $row['user_id'];
			}
			else
			{
				// Skip multiple sessions for one user
				if ($row['session_ip'] != $prev_session_ip)
				{
					$guests_online++;
				}
			}

			$prev_session_ip = $row['session_ip'];
		}
		$db->sql_freeresult($result);

		if (!$online_userlist)
		{
			$online_userlist = $user->lang['NO_ONLINE_USERS'];
		}

		if (empty($_REQUEST['f']))
		{
			$online_userlist = $user->lang['REGISTERED_USERS'] . ' ' . $online_userlist;
		}
		else
		{
			$l_online = ($guests_online == 1) ? $user->lang['BROWSING_FORUM_GUEST'] : $user->lang['BROWSING_FORUM_GUESTS'];
			$online_userlist = sprintf($l_online, $online_userlist, $guests_online);
		}

		$total_online_users = $logged_visible_online + $logged_hidden_online + $guests_online;

		if ($total_online_users > $config['record_online_users'])
		{
			set_config('record_online_users', $total_online_users, true);
			set_config('record_online_date', time(), true);
		}

		// Build online listing
		$vars_online = array(
			'ONLINE'	=> array('total_online_users', 'l_t_user_s'),
			'REG'		=> array('logged_visible_online', 'l_r_user_s'),
			'HIDDEN'	=> array('logged_hidden_online', 'l_h_user_s'),
			'GUEST'		=> array('guests_online', 'l_g_user_s')
		);

		foreach ($vars_online as $l_prefix => $var_ary)
		{
			switch (${$var_ary[0]})
			{
				case 0:
					${$var_ary[1]} = $user->lang[$l_prefix . '_USERS_ZERO_TOTAL'];
				break;

				case 1:
					${$var_ary[1]} = $user->lang[$l_prefix . '_USER_TOTAL'];
				break;

				default:
					${$var_ary[1]} = $user->lang[$l_prefix . '_USERS_TOTAL'];
				break;
			}
		}
		unset($vars_online);

		$l_online_users = sprintf($l_t_user_s, $total_online_users);
		$l_online_users .= sprintf($l_r_user_s, $logged_visible_online);
		$l_online_users .= sprintf($l_h_user_s, $logged_hidden_online);
		$l_online_users .= sprintf($l_g_user_s, $guests_online);

		$l_online_record = sprintf($user->lang['RECORD_ONLINE_USERS'], $config['record_online_users'], $user->format_date($config['record_online_date']));

		$l_online_time = ($config['load_online_time'] == 1) ? 'VIEW_ONLINE_TIME' : 'VIEW_ONLINE_TIMES';
		$l_online_time = sprintf($user->lang[$l_online_time], $config['load_online_time']);
	}
	else
	{
		$l_online_time = '';
	}

	$l_privmsgs_text = $l_privmsgs_text_unread = '';
	$s_privmsg_new = false;

	// Obtain number of new private messages if user is logged in
	if (isset($user->data['is_registered']) && $user->data['is_registered'])
	{
		if ($user->data['user_new_privmsg'])
		{
			$l_message_new = ($user->data['user_new_privmsg'] == 1) ? $user->lang['NEW_PM'] : $user->lang['NEW_PMS'];
			$l_privmsgs_text = sprintf($l_message_new, $user->data['user_new_privmsg']);

			if (!$user->data['user_last_privmsg'] || $user->data['user_last_privmsg'] > $user->data['session_last_visit'])
			{
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_last_privmsg = ' . $user->data['session_last_visit'] . '
					WHERE user_id = ' . $user->data['user_id'];
				$db->sql_query($sql);

				$s_privmsg_new = true;
			}
			else
			{
				$s_privmsg_new = false;
			}
		}
		else
		{
			$l_privmsgs_text = $user->lang['NO_NEW_PM'];
			$s_privmsg_new = false;
		}

		$l_privmsgs_text_unread = '';

		if ($user->data['user_unread_privmsg'] && $user->data['user_unread_privmsg'] != $user->data['user_new_privmsg'])
		{
			$l_message_unread = ($user->data['user_unread_privmsg'] == 1) ? $user->lang['UNREAD_PM'] : $user->lang['UNREAD_PMS'];
			$l_privmsgs_text_unread = sprintf($l_message_unread, $user->data['user_unread_privmsg']);
		}
	}

	// Which timezone?
	$tz = ($user->data['user_id'] != ANONYMOUS) ? strval(doubleval($user->data['user_timezone'])) : strval(doubleval($config['board_timezone']));
	
	// The following assigns all _common_ variables that may be used at any point in a template.
	$template->assign_vars(array(
		'SITENAME'						=> $config['sitename'],
		'SITE_DESCRIPTION'				=> $config['site_desc'],
		'PAGE_TITLE'					=> $page_title,
		'SCRIPT_NAME'					=> str_replace('.' . $phpEx, '', $user->page['page_name']),
		'LAST_VISIT_DATE'				=> sprintf($user->lang['YOU_LAST_VISIT'], $s_last_visit),
		'CURRENT_TIME'					=> sprintf($user->lang['CURRENT_TIME'], $user->format_date(time(), false, true)),
		'TOTAL_USERS_ONLINE'			=> $l_online_users,
		'LOGGED_IN_USER_LIST'			=> $online_userlist,
		'RECORD_USERS'					=> $l_online_record,
		'PRIVATE_MESSAGE_INFO'			=> $l_privmsgs_text,
		'PRIVATE_MESSAGE_INFO_UNREAD'	=> $l_privmsgs_text_unread,

		'SID'				=> $SID,
		'_SID'				=> $_SID,
		'SESSION_ID'		=> $user->session_id,
		'ROOT_PATH'			=> $phpbb_root_path,

		'L_LOGIN_LOGOUT'	=> $l_login_logout,
		'L_INDEX'			=> $user->lang['FORUM_INDEX'],
		'L_ONLINE_EXPLAIN'	=> $l_online_time,

		'U_PRIVATEMSGS'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&amp;folder=inbox'),
		'U_RETURN_INBOX'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&amp;folder=inbox'),
		'UA_RETURN_INBOX'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&folder=inbox', false),
		'U_POPUP_PM'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&amp;mode=popup'),
		'UA_POPUP_PM'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&mode=popup', false),
		'U_MEMBERLIST'			=> append_sid("{$phpbb_root_path}memberlist.$phpEx"),
		'U_MEMBERSLIST'			=> append_sid("{$phpbb_root_path}memberlist.$phpEx"),
		'U_VIEWONLINE'			=> append_sid("{$phpbb_root_path}viewonline.$phpEx"),
		'U_LOGIN_LOGOUT'		=> $u_login_logout,
		'U_INDEX'				=> append_sid("{$phpbb_root_path}index.$phpEx"),
		'U_SEARCH'				=> append_sid("{$phpbb_root_path}search.$phpEx"),
		'U_REGISTER'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=register'),
		'U_PROFILE'				=> append_sid("{$phpbb_root_path}ucp.$phpEx"),
		'U_MODCP'				=> append_sid("{$phpbb_root_path}mcp.$phpEx", false, true, $user->session_id),
		'U_FAQ'					=> append_sid("{$phpbb_root_path}faq.$phpEx"),
		'U_SEARCH_SELF'			=> append_sid("{$phpbb_root_path}search.$phpEx", 'search_id=egosearch'),
		'U_SEARCH_NEW'			=> append_sid("{$phpbb_root_path}search.$phpEx", 'search_id=newposts'),
		'U_SEARCH_UNANSWERED'	=> append_sid("{$phpbb_root_path}search.$phpEx", 'search_id=unanswered'),
		'U_SEARCH_ACTIVE_TOPICS'=> append_sid("{$phpbb_root_path}search.$phpEx", 'search_id=active_topics'),
		'U_DELETE_COOKIES'		=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=delete_cookies'),
		'U_TEAM'				=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=leaders'),
		'U_RESTORE_PERMISSIONS'	=> ($user->data['user_perm_from'] && $auth->acl_get('a_switchperm')) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=restore_perm') : '',

		'S_USER_LOGGED_IN'		=> ($user->data['user_id'] != ANONYMOUS) ? true : false,
		'S_BOARD_DISABLED'		=> ($config['board_disable'] && !defined('IN_LOGIN') && !$auth->acl_gets('a_', 'm_')) ? true : false,
		'S_REGISTERED_USER'		=> $user->data['is_registered'],
		'S_IS_BOT'				=> $user->data['is_bot'],
		'S_USER_PM_POPUP'		=> $user->optionget('popuppm'),
		'S_USER_LANG'			=> $user->lang['USER_LANG'],
		'S_USER_BROWSER'		=> (isset($user->data['session_browser'])) ? $user->data['session_browser'] : $user->lang['UNKNOWN_BROWSER'],
		'S_USERNAME'			=> $user->data['username'],
		'S_CONTENT_DIRECTION'	=> $user->lang['DIRECTION'],
		'S_CONTENT_ENCODING'	=> 'UTF-8',
		'S_CONTENT_DIR_LEFT'	=> $user->lang['LEFT'],
		'S_CONTENT_DIR_RIGHT'	=> $user->lang['RIGHT'],
		'S_TIMEZONE'			=> ($user->data['user_dst'] || ($user->data['user_id'] == ANONYMOUS && $config['board_dst'])) ? sprintf($user->lang['ALL_TIMES'], $user->lang['tz'][$tz], $user->lang['tz']['dst']) : sprintf($user->lang['ALL_TIMES'], $user->lang['tz'][$tz], ''),
		'S_DISPLAY_ONLINE_LIST'	=> ($l_online_time) ? 1 : 0,
		'S_DISPLAY_SEARCH'		=> (!$config['load_search']) ? 0 : (isset($auth) ? ($auth->acl_get('u_search') && $auth->acl_getf_global('f_search')) : 1),
		'S_DISPLAY_PM'			=> ($config['allow_privmsg'] && $user->data['is_registered']) ? 1 : 0,
		'S_DISPLAY_MEMBERLIST'	=> (isset($auth)) ? $auth->acl_get('u_viewprofile') : 0,
		'S_NEW_PM'				=> ($s_privmsg_new) ? 1 : 0,

		'T_THEME_PATH'			=> "{$phpbb_root_path}styles/" . $user->theme['theme_path'] . '/theme',
		'T_TEMPLATE_PATH'		=> "{$phpbb_root_path}styles/" . $user->theme['template_path'] . '/template',
		'T_IMAGESET_PATH'		=> "{$phpbb_root_path}styles/" . $user->theme['imageset_path'] . '/imageset',
		'T_IMAGESET_LANG_PATH'	=> "{$phpbb_root_path}styles/" . $user->theme['imageset_path'] . '/imageset/' . $user->data['user_lang'],
		'T_IMAGES_PATH'			=> "{$phpbb_root_path}images/",
		'T_SMILIES_PATH'		=> "{$phpbb_root_path}{$config['smilies_path']}/",
		'T_AVATAR_PATH'			=> "{$phpbb_root_path}{$config['avatar_path']}/",
		'T_AVATAR_GALLERY_PATH'	=> "{$phpbb_root_path}{$config['avatar_gallery_path']}/",
		'T_ICONS_PATH'			=> "{$phpbb_root_path}{$config['icons_path']}/",
		'T_RANKS_PATH'			=> "{$phpbb_root_path}{$config['ranks_path']}/",
		'T_UPLOAD_PATH'			=> "{$phpbb_root_path}{$config['upload_path']}/",
		'T_STYLESHEET_LINK'		=> (!$user->theme['theme_storedb']) ? "{$phpbb_root_path}styles/" . $user->theme['theme_path'] . '/theme/stylesheet.css' : "{$phpbb_root_path}style.$phpEx?sid=$user->session_id&amp;id=" . $user->theme['style_id'] . '&amp;lang=' . $user->data['user_lang'],
		'T_STYLESHEET_NAME'		=> $user->theme['theme_name'],
		'T_THEME_DATA'			=> (!$user->theme['theme_storedb']) ? '' : $user->theme['theme_data'],

		'SITE_LOGO_IMG'			=> $user->img('site_logo'))
	);

	if ($config['send_encoding'])
	{
		header('Content-type: text/html; charset=UTF-8');
	}
	header('Cache-Control: private, no-cache="set-cookie"');
	header('Expires: 0');
	header('Pragma: no-cache');

	return;
}

/**
* Generate page footer
*/
function page_footer($run_cron = true)
{
	global $db, $config, $template, $user, $auth, $cache, $messenger, $starttime, $phpbb_root_path, $phpEx;

	// Output page creation time
	if (defined('DEBUG'))
	{
		$mtime = explode(' ', microtime());
		$totaltime = $mtime[0] + $mtime[1] - $starttime;

		if (!empty($_REQUEST['explain']) && $auth->acl_get('a_') && defined('DEBUG_EXTRA') && method_exists($db, 'sql_report'))
		{
			$db->sql_report('display');
		}

		$debug_output = sprintf('Time : %.3fs | ' . $db->sql_num_queries() . ' Queries | GZIP : ' .  (($config['gzip_compress']) ? 'On' : 'Off') . (($user->load) ? ' | Load : ' . $user->load : ''), $totaltime);

		if ($auth->acl_get('a_') && defined('DEBUG_EXTRA'))
		{
			if (function_exists('memory_get_usage'))
			{
				if ($memory_usage = memory_get_usage())
				{
					global $base_memory_usage;
					$memory_usage -= $base_memory_usage;
					$memory_usage = ($memory_usage >= 1048576) ? round((round($memory_usage / 1048576 * 100) / 100), 2) . ' ' . $user->lang['MB'] : (($memory_usage >= 1024) ? round((round($memory_usage / 1024 * 100) / 100), 2) . ' ' . $user->lang['KB'] : $memory_usage . ' ' . $user->lang['BYTES']);

					$debug_output .= ' | Memory Usage: ' . $memory_usage;
				}
			}

			$debug_output .= ' | <a href="' . build_url() . '&amp;explain=1">Explain</a>';
		}
	}

	$template->assign_vars(array(
		'DEBUG_OUTPUT'			=> (defined('DEBUG')) ? $debug_output : '',
		'TRANSLATION_INFO'		=> (!empty($user->lang['TRANSLATION_INFO'])) ? $user->lang['TRANSLATION_INFO'] : '',

		'U_ACP' => ($auth->acl_get('a_') && $user->data['is_registered']) ? append_sid("{$phpbb_root_path}adm/index.$phpEx", '', true, $user->session_id) : '')
	);

	// Call cron-type script
	if (!defined('IN_CRON') && $run_cron)
	{
		$cron_type = '';
	
		if (time() - $config['queue_interval'] > $config['last_queue_run'] && !defined('IN_ADMIN') && file_exists($phpbb_root_path . 'cache/queue.' . $phpEx))
		{
			// Process email queue
			$cron_type = 'queue';
		}
		else if (method_exists($cache, 'tidy') && time() - $config['cache_gc'] > $config['cache_last_gc'])
		{
			// Tidy the cache
			$cron_type = 'tidy_cache';
		}
		else if (time() - $config['warnings_gc'] > $config['warnings_last_gc'])
		{
			$cron_type = 'tidy_warnings';
		}
		else if (time() - $config['database_gc'] > $config['database_last_gc'])
		{
			// Tidy the database
			$cron_type = 'tidy_database';
		}
		else if (time() - $config['search_gc'] > $config['search_last_gc'])
		{
			// Tidy the search
			$cron_type = 'tidy_search';
		}
		else if (time() - $config['session_gc'] > $config['session_last_gc'])
		{
			$cron_type = 'tidy_sessions';
		}

		if ($cron_type)
		{
			$template->assign_var('RUN_CRON_TASK', '<img src="' . append_sid($phpbb_root_path . 'cron.' . $phpEx, 'cron_type=' . $cron_type) . '" width="1" height="1" alt="cron" />');
		}
	}

	$template->display('body');

	garbage_collection();

	exit;
}

/**
* Closing the cache object and the database
* Cool function name, eh? We might want to add operations to it later
*/
function garbage_collection()
{
	global $cache, $db;

	// Unload cache, must be done before the DB connection if closed
	if (!empty($cache))
	{
		$cache->unload();
	}

	// Close our DB connection.
	if (!empty($db))
	{
		$db->sql_close();
	}
}

/**
* @package phpBB3
*/
class bitfield
{
	var $data;

	function bitfield($bitfield = '')
	{
		$this->data = base64_decode($bitfield);
	}

	/**
	*/
	function get($n)
	{
		// Get the ($n / 8)th char
		$byte = $n >> 3;

		if (!isset($this->data[$byte]))
		{
			// Of course, if it doesn't exist then the result if FALSE
			return false;
		}

		$c = $this->data[$byte];

		// Lookup the ($n % 8)th bit of the byte
		$bit = 7 - ($n & 7);
		return (bool) (ord($c) & (1 << $bit));
	}

	function set($n)
	{
		$byte = $n >> 3;
		$bit = 7 - ($n & 7);

		if (isset($this->data[$byte]))
		{
			$this->data[$byte] = $this->data[$byte] | chr(1 << $bit);
		}
		else
		{
			if ($byte - strlen($this->data) > 0)
			{
				$this->data .= str_repeat("\0", $byte - strlen($this->data));
			}
			$this->data .= chr(1 << $bit);
		}
	}

	function clear($n)
	{
		$byte = $n >> 3;

		if (!isset($this->data[$byte]))
		{
			return;
		}

		$bit = 7 - ($n & 7);
		$this->data[$byte] = $this->data[$byte] &~ chr(1 << $bit);
	}

	function get_blob()
	{
		return $this->data;
	}

	function get_base64()
	{
		return base64_encode($this->data);
	}

	function get_bin()
	{
		$bin = '';
		$len = strlen($this->data);

		for ($i = 0; $i < $len; ++$i)
		{
			$bin .= str_pad(decbin(ord($this->data[$i])), 8, '0', STR_PAD_LEFT);
		}

		return $bin;
	}

	function get_all_set()
	{
		return array_keys(array_filter(str_split($this->get_bin())));
	}

	function merge($bitfield)
	{
		$this->data = $this->data | $bitfield->get_blob();
	}
}

?>