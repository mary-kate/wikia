<?php
function efWikiaPoke() {

	return array(
	
	'en' => array(
		'poke_subject' => '$2 has poked you on {{SITENAME}}!',
		'poke_body' => 'Hi $1:

You have been nudged by $5 on Wikia Search.

To nudge $5 back or to see all of your notifications, click the link below:

{{PROFILEJSONPATH}}notifications.html

To view $5\'s profile, click the link below:

{{PROFILEJSONPATH}}profile.html?user={{#BULLETINNAME:$2}}

Thanks

---

Hey, want to stop getting emails from us?  

Click $4 
and change your settings to disable email notifications.',
		'poke_back_subject' => '$2 has poked you back on {{SITENAME}}!',
		'poke_back_body' => 'Hi $1:

$5 nudged you back on Wikia Search.

To nudge $5 back again or to see all of your notifications, click the link below:

{{PROFILEJSONPATH}}notifications.html


To view $5\'s profile, click the link below:

{{PROFILEJSONPATH}}profile.html?user={{#BULLETINNAME:$2}}

Thanks

---

Hey, want to stop getting emails from us?  

Click $4 
and change your settings to disable email notifications.',
		),
	); 
}

?>
