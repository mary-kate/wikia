<?php
/**
 * @package MediaWiki
 * @subpackage Maintenance

  Copyright: Wikia, Inc
  @author Łukasz "Egon" Matysiak; egon@wikia.com

  This script generates local table of users containing users wich have 
  adleast one contribution on Wiki, or belong to any group.
*/

//print ("Building local users, table\n");

require_once( dirname(__FILE__).'/../commandLine.inc' );

print ("Building local users, table for database: wgSharedDB='$wgSharedDB', wgDBname='$wgDBname' with user='$wgDBuser'\n");
//print ("wgDBuser=$wgDBuser, wgDBpass=$wgDBpassword\n");
//print ("wgSharedDB=$wgSharedDB, wgDBname=$wgDBname\n");

$local_users_table = 'local_users';

$db =& wfGetDB( DB_MASTER );

$user = $db->tableName('user');

unset($wgSharedDB);

list ($user_groups,$revision) = $db->tableNamesN('user_groups','revision');

print ("got variables: user='$user', user_groups='$user_groups', revision='$revision'\n");

$db->query("use `$wgDBname`;");

$db->query("drop table if exists user_rev_cnt;");

$db->query( "CREATE TABLE user_rev_cnt 
(rev_user int(5) unsigned primary key,
rev_cnt int );" );

$sql0 = "insert into `user_rev_cnt` 
(select rev_user, count(*) as rev_cnt 
from $revision 
group by rev_user)
ON DUPLICATE KEY UPDATE rev_cnt=values(rev_cnt);";

$db->query("drop table if exists $local_users_table;");

$sql1 = "CREATE TABLE $local_users_table 
(SELECT  user_name, MAX(user_id) AS user_id, COUNT(ug_group) AS numgroups, MAX(ug_group) AS singlegroup, rev_cnt 
FROM  $user
LEFT JOIN $user_groups ON user_id=ug_user 
JOIN user_rev_cnt ON user_id=rev_user
GROUP BY user_name ORDER BY user_name);";


$sql2 = "INSERT INTO $local_users_table 
SELECT user_name, MAX(user_id) AS user_id, COUNT(ug_GROUP) AS  numgroups, MAX(ug_group) AS singlegroup, rev_cnt 
FROM  $user
JOIN $user_groups ON user_id=ug_user 
LEFT JOIN user_rev_cnt ON user_id=rev_user
GROUP BY user_name ORDER BY user_name
ON DUPLICATE KEY UPDATE rev_cnt=values(rev_cnt);";

//print $sql ."\n";
$result0 = $db->query($sql0);
$result1 = $db->query($sql1);
$db->query("CREATE UNIQUE INDEX user_id_index ON $local_users_table (user_id);");
$db->query("CREATE UNIQUE INDEX user_name_index ON $local_users_table (user_name);");
$result2 = $db->query($sql2);

print ("Result='$result0,$result1,$result2'; Done\n");

if ( function_exists( 'wfWaitForSlaves' ) ) {
	wfWaitForSlaves( 10 );
} else {
	sleep( 1 );
}
?>
