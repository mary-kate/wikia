<?php
/**
* Authentication plugin for phpBB3 using MediaWiki's userbase.
* 
* Original author: Dariusz Siedlecki (Datrio)
*/

function login_mediawiki(&$username, &$password)
{
  // We only want to get logged in through MediaWiki, so quit this.
  
  header("Location: /index.php?title=Special:Userlogin");
  die();
}

function autologin_mediawiki()
{
  global $db, $user, $wgUser;
  
  if ($wgUser->isAnon())
	return array();
  
  $sql = 'SELECT * FROM ' . USERS_TABLE . " WHERE username = '" . $db->sql_escape( $wgUser->getName() ) . "'";
  $result = $db->sql_query($sql);
  $row = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);
  
  if ($row)
    return ($row['user_type'] == USER_INACTIVE || $row['user_type'] == USER_IGNORE) ? array() : $row;
    
  // Since we don't care about the user passwords in phpBB's database, we can even input blank passwords there. Sneaky.
  if (!function_exists('user_add'))
  {
    global $phpbb_root_path, $phpEx;
    
    include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
  }
  
  // create the user if he does not exist yet
  user_add(user_row_mediawiki($wgUser->getName(), " "));
   
  $sql = 'SELECT * FROM ' . USERS_TABLE . " WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($wgUser->getName())) . "'";
  $result = $db->sql_query($sql);
  $row = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);
  
  if ($row)
    return $row;
  else
	return array();
}

/**
* This function generates an array which can be passed to the user_add function in order to create a user
*/
function user_row_mediawiki($username, $password)
{
  global $db, $config, $user;
  
  // first retrieve default group id
  $sql = 'SELECT group_id FROM ' . GROUPS_TABLE . " WHERE group_name = '" . $db->sql_escape('REGISTERED') . "' AND group_type = " . GROUP_SPECIAL;
  $result = $db->sql_query($sql);
  $row = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);
  
  if (!$row)
    trigger_error('NO_GROUP');
  
  // generate user account data
  return array(
	'username'		=> $username,
	'user_password'	=> md5($password),
	'user_email'	=> '',
	'group_id'		=> (int) $row['group_id'],
	'user_type'		=> USER_NORMAL,
	'user_ip'		=> $user->ip,
  );
}

/**
* The session validation function checks whether the user is still logged in
*
* @return boolean true if the given user is authenticated or false if the session should be closed
*/
function validate_session_mediawiki(&$user_data)
{
  global $user, $session, $auth, $wgUser;

  if ($wgUser->isAnon())
	return false;
  
  if ($user_data['username'] != $wgUser->getName()) {
	// Okay, we have a problem - log him out and run autologin.
	
	$user->session_kill();
	 
	$ret = autologin_mediawiki();
	
	if (empty($ret))
	  return false;
	else {
	  $user->session_create($ret['user_id'], 0, false, 1);
	  
	  return true;
	}
  }
  
  return true;
}
?>
