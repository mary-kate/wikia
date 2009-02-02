<?php
/** 
*
* acp_attachments [English]
*
* @package language
* @version $Id: attachments.php,v 1.15 2006/10/29 09:08:36 dhn2 Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_ATTACHMENT_SETTINGS_EXPLAIN'	=> 'Here you can configure the main settings for attachments and the associated special categories.',
	'ACP_EXTENSION_GROUPS_EXPLAIN'		=> 'Here you can add, delete, modify or disable your extension groups. Further options include the assignment of a special category to them, changing the download mechanism and defining an upload icon which will be displayed in front of the attachment which belongs to the group.',
	'ACP_MANAGE_EXTENSIONS_EXPLAIN'		=> 'Here you can manage your allowed extensions. To activate your extensions, please refer to the extension groups management panel. We strongly recommend not to allow scripting extensions (such as <code>php</code>, <code>php3</code>, <code>php4</code>, <code>phtml</code>, <code>pl</code>, <code>cgi</code>, <code>py</code>, <code>rb</code>, <code>asp</code>, <code>aspx</code>, and so forth…)',
	'ACP_ORPHAN_ATTACHMENTS_EXPLAIN'	=> 'Here you are able to see files within the attachments upload directory but not assigned to posts. This happens mostly if users are attaching files but not submitting the post. You are able to delete the files or attach them to existing posts. Attaching to posts requires a valid post ID, you have to determine this id by yourself, this feature is mainly for those people wanting to upload files with another program and assigning those (mostly large) files to an existing post.',
	'ADD_EXTENSION'						=> 'Add extension',
	'ADD_EXTENSION_GROUP'				=> 'Add extension group',
	'ADMIN_UPLOAD_ERROR'				=> 'Errors while trying to attach file: %s',
	'ALLOWED_FORUMS'					=> 'Allowed forums',
	'ALLOWED_FORUMS_EXPLAIN'			=> 'Able to post the assigned extensions at the selected (or all if selected) forums',
	'ALLOW_ATTACHMENTS'					=> 'Allow attachments',
	'ALLOW_ALL_FORUMS'					=> 'Allow all forums',
	'ALLOW_IN_PM'						=> 'Allowed in private messaging',
	'ALLOW_PM_ATTACHMENTS'				=> 'Allow attachments in private messages',
	'ALLOW_SELECTED_FORUMS'				=> 'Only forums selected below',
	'ASSIGNED_EXTENSIONS'				=> 'Assigned extensions',
	'ASSIGNED_GROUP'					=> 'Assigned extension group',
	'ATTACH_EXTENSIONS_URL'				=> 'Extensions',
	'ATTACH_EXT_GROUPS_URL'				=> 'Extension groups',
	'ATTACH_MAX_FILESIZE'				=> 'Maximum filesize',
	'ATTACH_MAX_FILESIZE_EXPLAIN'		=> 'Maximum size of each file, 0 is unlimited.',
	'ATTACH_MAX_PM_FILESIZE'			=> 'Maximum filesize messaging',
	'ATTACH_MAX_PM_FILESIZE_EXPLAIN'	=> 'Maximum drive space available per user for private message attachments, 0 is unlimited.',
	'ATTACH_ORPHAN_URL'					=> 'Orphan attachments',
	'ATTACH_POST_ID'					=> 'Post ID',
	'ATTACH_QUOTA'						=> 'Total attachment quota',
	'ATTACH_QUOTA_EXPLAIN'				=> 'Maximum drive space available for attachments in total, 0 is unlimited.',
	'ATTACH_TO_POST'					=> 'Attach file to post',

	'CAT_FLASH_FILES'			=> 'Flash Files',
	'CAT_IMAGES'				=> 'Images',
	'CAT_QUICKTIME_FILES'		=> 'Quicktime Media',
	'CAT_RM_FILES'				=> 'Real Media Streams',
	'CAT_WM_FILES'				=> 'Window Media Streams',
	'CREATE_GROUP'				=> 'Create new group',
	'CREATE_THUMBNAIL'			=> 'Create thumbnail',
	'CREATE_THUMBNAIL_EXPLAIN'	=> 'Create a thumbnail in all possible situations.',

	'DEFINE_ALLOWED_IPS'			=> 'Define allowed IPs/hostnames',
	'DEFINE_DISALLOWED_IPS'			=> 'Define disallowed IPs/hostnames',
	'DOWNLOAD_ADD_IPS_EXPLAIN'		=> 'To specify several different IPs or hostnames enter each on a new line. To specify a range of IP addresses separate the start and end with a hyphen (-), to specify a wildcard use *',
	'DOWNLOAD_MODE'					=> 'Download mode',
	'DOWNLOAD_MODE_EXPLAIN'			=> 'If you experience problems downloading files, set this to “physical”, the user will be directed to the file directly. Do not set it to physical if not really needed, it discloses the filename.',
	'DOWNLOAD_REMOVE_IPS_EXPLAIN'	=> 'You can remove (or un-exclude) multiple IP addresses in one go using the appropriate combination of mouse and keyboard for your computer and browser. Excluded IPs have a blue background.',
	'DISPLAY_INLINED'				=> 'Display images inline',
	'DISPLAY_INLINED_EXPLAIN'		=> 'If set to No image attachments will show as a link.',
	'DISPLAY_ORDER'					=> 'Attachment display order',
	'DISPLAY_ORDER_EXPLAIN'			=> 'Display attachments ordered by time.',
	
	'EDIT_EXTENSION_GROUP'			=> 'Edit extension group',
	'EXCLUDE_ENTERED_IP'			=> 'Enable this to exclude the entered IP/hostname.',
	'EXCLUDE_FROM_ALLOWED_IP'		=> 'Exclude IP from allowed IPs/hostnames',
	'EXCLUDE_FROM_DISALLOWED_IP'	=> 'Exclude IP from disallowed IPs/hostnames',
	'EXTENSIONS_UPDATED'			=> 'Extensions successfully updated',
	'EXTENSION_EXIST'				=> 'The extension %s already exist',
	'EXTENSION_GROUP'				=> 'Extension group',
	'EXTENSION_GROUPS'				=> 'Extension groups',
	'EXTENSION_GROUP_DELETED'		=> 'Extension group successfully deleted.',
	'EXTENSION_GROUP_EXIST'			=> 'The extension group %s already exist',

	'GO_TO_EXTENSIONS'		=> 'Go to extension management screen',
	'GROUP_NAME'			=> 'Group name',

	'IMAGE_LINK_SIZE'			=> 'Image link dimensions',
	'IMAGE_LINK_SIZE_EXPLAIN'	=> 'Display image attachment as link if image is larger than this, set to 0px by 0px to disable.',
	'IMAGICK_PATH'				=> 'Imagemagick path',
	'IMAGICK_PATH_EXPLAIN'		=> 'Full path to the imagemagick convert application, e.g. <samp>/usr/bin/</samp>',

	'MAX_ATTACHMENTS'				=> 'Max attachments per post',
	'MAX_ATTACHMENTS_PM'			=> 'Max attachments per message',
	'MAX_EXTGROUP_FILESIZE'			=> 'Maximum filesize',
	'MAX_IMAGE_SIZE'				=> 'Maximum image dimensions',
	'MAX_IMAGE_SIZE_EXPLAIN'		=> 'Maximum size of image attachments, 0px by 0px disables image attachments.',
	'MAX_THUMB_WIDTH'				=> 'Maximum thumbnail width in pixel',
	'MAX_THUMB_WIDTH_EXPLAIN'		=> 'A generated thumbnail will not exceed the width set here',
	'MIN_THUMB_FILESIZE'			=> 'Minimum thumbnail filesize',
	'MIN_THUMB_FILESIZE_EXPLAIN'	=> 'Do not create a thumbnail for images smaller than this.',
	'MODE_INLINE'					=> 'Inline',
	'MODE_PHYSICAL'					=> 'Physical',

	'NOT_ALLOWED_IN_PM'			=> 'Not allowed in private messages',
	'NOT_ASSIGNED'				=> 'Not assigned',
	'NO_EXT_GROUP'				=> 'None',
	'NO_EXT_GROUP_NAME'			=> 'No group name entered',
	'NO_EXT_GROUP_SPECIFIED'	=> 'No extension group specified.',
	'NO_FILE_CAT'				=> 'None',
	'NO_IMAGE'					=> 'No image',
	'NO_THUMBNAIL_SUPPORT'		=> 'Thumbnail support has been disabled because there is no supported GD library available and the imagemagick executable could not be found.',
	'NO_UPLOAD_DIR'				=> 'The upload directory you specified does not exist.',
	'NO_WRITE_UPLOAD'			=> 'The upload directory you specified cannot be written to. Please alter the permissions to allow the webserver to write to it.',

	'ORDER_ALLOW_DENY'		=> 'Allow',
	'ORDER_DENY_ALLOW'		=> 'Deny',

	'REMOVE_ALLOWED_IPS'		=> 'Remove or un-exclude allowed IPs/hostnames',
	'REMOVE_DISALLOWED_IPS'		=> 'Remove or un-exclude disallowed IPs/hostnames',

	'SEARCH_IMAGICK'				=> 'Search for Imagemagick',
	'SECURE_ALLOW_DENY'				=> 'Allow/Deny list',
	'SECURE_ALLOW_DENY_EXPLAIN'		=> 'Allow or Deny the list of addresses, this setting only applies to downloading files',
	'SECURE_DOWNLOADS'				=> 'Enable secure downloads',
	'SECURE_DOWNLOADS_EXPLAIN'		=> 'With this option enabled, downloads are limited to IP’s/hostnames you defined.',
	'SECURE_DOWNLOAD_NOTICE'		=> 'Secure Downloads are not enabled. The settings below will be applied after enabling secure downloads.',
	'SECURE_DOWNLOAD_UPDATE_SUCCESS'=> 'The IP list has been updated successfully.',
	'SECURE_EMPTY_REFERRER'			=> 'Allow empty referrer',
	'SECURE_EMPTY_REFERRER_EXPLAIN'	=> 'Secure downloads are based on referrers. Do you want to allow downloads for those ommitting the referrer?',
	'SETTINGS_CAT_IMAGES'			=> 'Image category settings',
	'SPECIAL_CATEGORY'				=> 'Special Category',
	'SPECIAL_CATEGORY_EXPLAIN'		=> 'Special Categories differ between the way presented within posts.',
	'SUCCESSFULLY_UPLOADED'			=> 'Succeessfully uploaded',
	'SUCCESS_EXTENSION_GROUP_ADD'	=> 'Extension group successfully added',
	'SUCCESS_EXTENSION_GROUP_EDIT'	=> 'Extension group successfully updated',

	'UPLOADING_FILES'				=> 'Uploading files',
	'UPLOADING_FILE_TO'				=> 'Uploading file “%1$s” to Post Number %2$d…',
	'UPLOAD_DENIED_FORUM'			=> 'You do not have the permission to upload files to forum “%s”',
	'UPLOAD_DIR'					=> 'Upload directory',
	'UPLOAD_DIR_EXPLAIN'			=> 'Storage path for attachments.',
	'UPLOAD_ICON'					=> 'Upload icon',
	'UPLOAD_NOT_DIR'				=> 'The upload location you specified does not appear to be a directory.',
));

?>