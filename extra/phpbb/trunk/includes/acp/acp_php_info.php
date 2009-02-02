<?php
/** 
*
* @package acp
* @version $Id: acp_php_info.php,v 1.8 2006/08/28 15:50:31 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package acp
*/
class acp_php_info
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		if ($mode != 'info')
		{
			trigger_error('NO_MODE', E_USER_ERROR);
		}

		$this->tpl_name = 'acp_php_info';
		$this->page_title = 'ACP_PHP_INFO';
		
		ob_start(); 
		phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES | INFO_VARIABLES); 
		$phpinfo = ob_get_contents(); 
		ob_end_clean(); 

		// Here we play around a little with the PHP Info HTML to try and stylise
		// it along phpBB's lines ... hopefully without breaking anything. The idea
		// for this was nabbed from the PHP annotated manual
		preg_match_all('#<body[^>]*>(.*)</body>#si', $phpinfo, $output); 

		$output = $output[1][0];
		$output = preg_replace('#<tr class="v"><td>(.*?<a[^>]*><img[^>]*></a>)(.*?)</td></tr>#s', '<tr class="row1"><td><table class="type2"><tr><td>\2</td><td>\1</td></tr></table></td></tr>', $output);
		$output = preg_replace('#<table[^>]+>#i', '<table>', $output);
		$output = preg_replace('#<img border="0"#i', '<img', $output);
		$output = str_replace(array('class="e"', 'class="v"', 'class="h"', '<hr />', '<font', '</font>'), array('class="row1"', 'class="row2"', '', '', '<span', '</span>'), $output);
		
		preg_match_all('#<div class="center">(.*)</div>#siU', $output, $output); 
		$output = $output[1][0];

		$template->assign_var('PHPINFO', $output);
	}
}

?>