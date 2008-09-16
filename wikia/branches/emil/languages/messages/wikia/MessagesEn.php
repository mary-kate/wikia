<?php

$messages = array_merge( $messages, array(
'addsection' => 'Leave message',
'alreadyrolled' => 'Cannot roll back edit to [[:$1]] by [[User:$2|$2]] ([[User talk:$2|talk]] · [[special:blockip/$2|block]] · [[Special:Contributions/$2|contribs]]) because someone else has edited the page.

The last revision was by [[User:$3|$3]] ([[User talk:$3|talk]] · [[Special:Contributions/$3|contribs]]).',
'anoneditwarning' => '{| align=center width=75% cellpadding=5 style="background: #D3E1F2; border: 1px solid #aaa; color:black;"
|-
| rowspan=2 | http://images.wikia.com/messaging/images//6/68/Login.png
| valign=top colspan=2 | \'\'\'Have you forgotten to log in?\'\'\' A user name helps you track your contributions and communicate with other users. If you don\'t log in, your IP address will show up in the page history.
|-
| class=plainlinks align=center | [{{FULLURL:Special:Userlogin}} http://images.wikia.com/messaging/images//f/f1/Greenbutton.png] \'\'\'[[Special:Userlogin|Click here to log in]]\'\'\'
| class=plainlinks align=left | [{{SERVER}}/index.php?title=Special:Userlogin&type=signup http://images.wikia.com/messaging/images//f/f1/Greenbutton.png] \'\'\'[{{SERVER}}/index.php?title=Special:Userlogin&type=signup Create a user name]\'\'\'
|}
<br />',
'anontalkpagetext' => '<br style="clear:both;" />
----
{| id="anontalktext" class="plainlinks noeditsection" style="font-size:90%; border: 1px solid #B8B8B8; margin:1em 1em 0em 1em; padding:0.25em 1em 0.25em 1em; clear: both;" 
| \'\'\'This is the discussion page for an anonymous user  who has not created an account yet or who does not use it, identified by the user\'s numerical [[wikipedia:IP address|IP address]].\'\'\'

Some IP addresses change periodically, and may be shared by several users. If you are an anonymous user, you may [[{{ns:Special}}:Userlogin|create an account or log in]] to avoid future confusion with other anonymous users. Registering also hides your IP address.

\'\'\'Help:\'\'\' [[w:c:help:Help:Why create an account|Why create an account?]] &bull; [[w:c:help:Help:Create an account|How to create an account]] &bull; [http://samspade.org/whois?query={{PAGENAMEE}} WHOIS]
|}',
'articleexists' => '\'\'\'The page could not be moved:\'\'\' a page of that name already exists, or the name you have chosen is not valid. Please choose another name, or ask an [[Project:Administrators|administrator]] to help you with the move.<br/>Please do not manually move the article by copying and pasting it; the page history must be moved along with the article text.',
'autosumm-blank' => 'Removing all content from page',
'badarticleerror' => 'This action cannot be performed on this page. This page may have been deleted since your request was submitted.',
'blockiptext' => 'Use the form below to block write access from a specific IP address or username.<br/>\'\'This should be done only to prevent vandalism, and in accordance with the wiki\'s [[{{MediaWiki:policy-url}}|policy]].\'\' It is also a good idea to give a user warnings before resorting to blocking them.<br/>Fill in specific reasons for the block below (for example, citing particular pages that were vandalized).',
'captchahelp-text' => 'Web sites that accept postings from the public, like this wiki, are often abused by spammers who use automated tools to post their links to many sites. While these spam links can be removed, they are a significant nuisance.

Sometimes, especially when adding new web links to a page, the wiki may show you an image of colored or distorted text and ask you to type the words shown. Since this is a task that\'s hard to automate, it will allow most real humans to make their posts while stopping most spammers and other robotic attackers.

Unfortunately this may inconvenience users with limited vision or using text-based or speech-based browsers. At the moment we do not have an audio alternative available. Please contact Wikia using the [[Special:Contact|contact form]] if you need assistance.

Hit the \'back\' button in your browser to return to the page editor.',
'category-media-count' => 'There {{PLURAL:$1|is one file|are $1 files}} in this section of this category.',
'clearyourcache' => '\'\'\'Note:\'\'\' After saving, you may have to bypass your browser\'s cache to see the changes.
*\'\'\'Mozilla / Firefox / Safari:\'\'\' hold down \'\'Shift\'\' while clicking \'\'Reload\'\', or press \'\'Ctrl-Shift-R\'\' (\'\'Cmd-Shift-R\'\' on Apple Mac)
*\'\'\'Internet Explorer:\'\'\' hold \'\'Ctrl\'\' while clicking \'\'Refresh\'\', or press \'\'Ctrl-F5\'\'
*\'\'\'Konqueror:\'\'\' simply click the \'\'Reload\'\' button, or press \'\'F5\'\'
*\'\'\'Opera\'\'\' users may need to completely clear the cache in \'\'Tools→Preferences\'\'.',
'common.css' => '/***** CSS placed here will be applied to all skins on the entire site. *****/

/* Mark redirects in Special:Allpages and Special:Watchlist */
.allpagesredirect {
   font-style: italic;
}
.watchlistredir {
   font-style: italic;
}

/* Giving headers and TOC a little extra space */
h2 {
   margin-top: 20px;
}
.toc {
   margin-top: 20px;
}

/* Infobox template style */
.infobox {
   border: 1px solid #aaaaaa;
   background-color: #f9f9f9;
   color: black;
   margin-bottom: 0.5em;
   margin-left: 1em;
   padding: 0.2em;
   float: right;
   clear: right;
}
.infobox td,
.infobox th {
   vertical-align: top;
}
.infobox caption {
   font-size: larger;
   margin-left: inherit;
}
.infobox.bordered {
   border-collapse: collapse;
}
.infobox.bordered td,
.infobox.bordered th {
   border: 1px solid #aaaaaa;
}
.infobox.bordered .borderless td,
.infobox.bordered .borderless th {
   border: 0;
}

/* Forum formatting (by -Algorithm & -Splaka) */
.forumheader {
   border: 1px solid #aaa;
   margin-top: 1em;
   padding: 12px;
}
.forumlist td.forum_edited a {
   color: black;
   text-decoration: none;
}
.forumlist td.forum_title a {
   padding-left: 20px;
}
.forumlist td.forum_title a.forum_new {
   font-weight: bold;
   background: url(/images/4/4e/Forum_new.gif) center left no-repeat;
   padding-left: 20px;
}
.forumlist td.forum_title a.forum_new:visited {
   font-weight: normal;
   background: none;
   padding-left: 20px;
}
.forumlist th.forum_title {
   padding-left: 20px;
}

/* Recent changes byte indicators */
.mw-plusminus-pos {
   color: #006500;
}
.mw-plusminus-neg {
   color: #8B0000;
}

/* Image frame fix */
div.tright, div.tleft {
   border: 1px solid silver;
}
div.thumb {
   margin: 2px;
   margin-left: 6px;
}

div.thumbinner {
   background: inherit;
   border: none;
}
#article div.thumb {
   color:inherit;
}

/* === Babel === */

div.babelbox {
   float: right;
   margin-left: 1em;
   margin-bottom: 0.5em;
   width: 246px;
   border: 1px solid #99B3FF;
   padding: 2px 0 2px 0;
}
.lang-blockN, .lang-block0, .lang-block1, .lang-block2, .lang-block3 {
   margin: 2px 4px 2px 4px; /* t, l, b, r */
   width:238px;
   border-collapse: collapse;
}
td.lang-codeN, td.lang-code0, td.lang-code1, td.lang-code2, td.lang-code3 {
   text-align:center;
   font-size:14pt;
   width:45px;
   height:45px;
}
td.lang-descriptionN, td.lang-description0, td.lang-description1,
td.lang-description2, td.lang-description3 {
  font-size:8pt;
  padding:4pt;
  line-height:1.25em;
}

.lang-block0 {
   border:1px solid #FFB3B3;
}
td.lang-code0 {
  background-color: #FFB3B3;
  color: black;
}
td.lang-description0 {
  background-color: #FFE0E8;
  color: black;
}

.lang-block1,  .lang-block2, .lang-block3  {
   border:1px solid #99B3FF;
}
td.lang-code1, td.lang-code2, td.lang-code3 {
  background-color: #99B3FF;
  color: black;
}
td.lang-description1,  td.lang-description2, td.lang-description3 {
  background-color: #E0E8FF;
  color: black;
}

.lang-blockN {
   border:1px solid #6EF7A7;
}
td.lang-codeN {
  background-color: #6EF7A7;
  color: black;
}
td.lang-descriptionN {
  background-color: #C5FCDC;
  color: black;
}',
'confirmemail_body' => 'Hi $2,

Thank you for registering with Wikia! 

Please fully activate your new Wikia account by confirming your email address.

Click on the link below or paste it into your browser:

$3

Note: This confirmation link will expire in 7 days.

(For your information, this username was created from the following address: $1)


With thousands of communities on Wikia, there are many ways to have fun here. Spend some time getting to know Wikia by visiting the home page (www.wikia.com), taking a tutorial (<http://www.wikia.com/wiki/Help:Tutorial_1>), reading interesting and cool articles, writing content on your favorite subjects, or meeting other members of the community.

We look forward to seeing you on Wikia!
The Wikia Community Team
www.wikia.com',
'confirmemail_loggedin' => '\'\'\'Your e-mail address has now been confirmed.\'\'\'

Welcome!

With thousands of communities on Wikia, there are many ways to have fun here. 

Spend some time getting to know Wikia by [[w:Help:Tutorial 1|taking a tutorial]], reading interesting and cool articles, writing content on your favorite subjects, or meeting other members of the community.

We look forward to seeing you on Wikia!

The Wikia Community Team',
'confirmemail_subject' => 'Please confirm your Wikia registration',
'confirmrecreate' => 'User [[User:$1|$1]] ([[User talk:$1|talk]]) deleted this article after you started editing it, with a reason of:
: \'\'$2\'\'
Please confirm that you really want to recreate this article.',
'contactintro' => '<p>Please read the <a href="http://www.wikia.com/wiki/Report_a_problem">Report a problem</a> page for information on reporting problems and using this contact form.</p>

<p>You can contact the Wikia community at the <a href="http://www.wikia.com/wiki/Forum:Index">Central Forum</a> and report software bugs at <a href="http://inside.wikia.com/forum">Inside Wikia forum</a>.</p>

<p>If you prefer your message to <a href=http://www.wikia.com/wiki/Wikia>Wikia</a> to be private, please use the contact form below. <i>All fields are optional</i>.</p>

<p>This form is currently working slowly but please press <i>send</i> <b>only once</b>.</p>',
'contributions' => 'Contributions',
'copyrightwarning' => '{| style="width:100%; padding: 5px; font-size: 95%;"
|- valign="top"
|
All contributions to {{SITENAME}} are considered to be released under the  $2 (see $1 for details).<br/>
Your changes will be visible immediately. \'\'\'Please enter a summary of your changes below.\'\'\'

<div style="font-weight: bold; font-size: 120%;">Do not submit copyrighted images or text without permission!</div>

| NOWRAP |
* \'\'\'[[Special:Upload|Upload]]\'\'\' images to the wiki.
* Don\'t forget to \'\'\'[[Special:Categories|categorize]] pages\'\'\'!
* For testing, please use a sandbox.\'\'\'
<div><small>\'\'[[MediaWiki:Copyrightwarning|View this template]]\'\'</small></div>
|}',
'createarticle' => 'Create page',
'createpage_about_info' => 'This is the simplified editor. To find out more go to [[w:c:help:Help:CreatePage|Wikia Help]].',
'createplate-Blank' => '<!---blanktemplate--->

Insert text here',
'createplate-list' => 'Blank|Blank',
'deletedwhileediting' => '<div id="mw-deletedwhileediting" class="plainlinks" style="margin: 0 0 1em; padding-left: .5em; border: solid #aaaaaa 1px">
<span style="color: red">\'\'\'Warning\'\'\'</span>: an administrator deleted this page since you started editing it. You might want to check the [{{fullurl:Special:Log/delete|page={{FULLPAGENAMEE}}}} deletion log] to see why.
</div>',
'deletereason-dropdown' => '*Vandalism and problems
** Copyright violation
** Spam
** Vandalism
*Maintenance
** Author request
** Housekeeping
** Marked for deletion
*Redirects
** Broken redirect
** Unused redirect
** Redirect left from pagemove',
'dellogpagetext' => 'Below is a list of the most recent deletions. If you don\'t agree with a deletion or think that a page was valid content, contact the [[Project:Administrators|administrator]] who deleted it.',
'description' => '{{SITENAME}} is a database that anyone can edit.',
'disclaimerpage' => '{{#ifexist:Project:General disclaimer|Project:General disclaimer| }}',
'edit' => 'Edit this page',
'editSimilar-Categories' => '* Stub
* Stubs
* Article stubs
* Überarbeitungsanträge',
'editcomment' => 'The edit summary was: "<i>$1</i>".',
'editingTips' => '= How to style text =

You can style text with \'wikimarkup\' or HTML.

<br />
<code><nowiki>\'\'italic\'\'</nowiki></code> => \'\'italic\'\'

<br />
<code><nowiki>\'\'\'bold\'\'\'</nowiki></code> => \'\'\'bold\'\'\'

<br />
<code><nowiki>\'\'\'\'\'italic and bold\'\'\'\'\'</nowiki></code> => \'\'\'\'\'italic and bold\'\'\'\'\'

----

<br />
<code><nowiki><s>strike</s></nowiki></code> => <s>strike</s>

<br />
<code><nowiki><u>underline</u></nowiki></code> => <u>underline</u>

<br />
<code><nowiki><span style="color:red;">red text</span></nowiki></code> => <span style="color:red;">red text</span>

= How to make links =

Links are created with one or two square brackets.

<br />
\'\'\'A simple internal link:\'\'\'<br />
<code><nowiki>[[Article name]]</nowiki></code>

<br />
\'\'\'An internal link with link text:\'\'\'<br />
<code><nowiki>[[Article name | text you want]]</nowiki></code>

<br />
----

<br />
\'\'\'A numbered external link:\'\'\'<br />
<code><nowiki>[http://www.site.com]</nowiki></code>

<br />
\'\'\'An external link with link text:\'\'\'

<code><nowiki>[http://www.site.com link text]</nowiki></code>

=How to add headings=

Headings use equals signs.  The more "=", the smaller the heading.

<br />
<span style="font-size: 1.6em;"><code><nowiki>==</nowiki></code>Heading 2<code><nowiki>==</nowiki></code></span>

<br />
<span style="font-size: 1.3em;"><code><nowiki>===</nowiki></code>\'\'\'Heading 3\'\'\'<code><nowiki>===</nowiki></code></span>

<br />
<code><nowiki>====</nowiki></code>\'\'\'Heading 4\'\'\'<code><nowiki>====</nowiki></code>

=How to indent text=

Indents can be created plain, with bullets, or numbered.

<br />
<code>:</code> indent<br />
<code>:</code> indent<br />
<code>::</code> more indent<br />
<code>:::</code> even more indent

<br />
<code>*</code> bullet<br />
<code>*</code> bullet<br />
<code>**</code> sub-bullet<br />
<code>*</code> bullet

<br />
<code>#</code> numbered list<br />
<code>#</code> numbered list<br />
<code>##</code> sub-list<br />
<code>#</code> numbered list

=How to insert images=

Images are added and formatted in a similar way to links.

<br />
<code><nowiki>[[Image:Name.jpg]]</nowiki></code>

<br />
\'\'\'To add alt. text\'\'\'<br />
<code><nowiki>[[Image:Name.jpg | alt text]]</nowiki></code>

<br />
\'\'\'To make a thumbnail\'\'\'<br />
<code><nowiki>[[Image:Name.jpg | thumb | ]]</nowiki></code>

<br />
\'\'\'To specify the size of the image\'\'\'<br />
<code><nowiki>[[Image:Name.jpg | 200px | ]]</nowiki></code>

<br />
\'\'\'To align the image\'\'\'<br />
<code><nowiki>[[Image:Name.jpg | right|]]</nowiki></code>

<br />
You can combine these attributes by putting a pipe symbol "<code>|</code>" between them. Remember, anything after the last pipe is text.

=More help and video demos=

Want more detailed help?

<br />
Visit [[w:c:help|Wikia Help]]!

<br />
For video walkthroughs:

<br/>
Visit [[w:c:help:Help:Videos|Video demos]]!',
'editinginterface' => '\'\'\'Warning:\'\'\' You are editing a page which is used to provide interface text for the software. Changes to this page will affect the appearance of the user interface for other users.',
'editingtips_enter_widescreen' => 'Enter Widescreen',
'editingtips_exit_widescreen' => 'Exit Widescreen',
'editingtips_hide' => 'Hide Editing Tips',
'editingtips_show' => 'Show Editing Tips',
'editsimilar-thanks' => 'Thanks for your edit. Can you improve any of these related articles? $1.',
'editsimilar-thanks-notsimilar' => 'Thank you for your edit. Can you improve any of these articles? $1.',
'editsimilar-thanks-notsimilar-singleresult' => 'Thank you for your edit. Can you improve this article? $1.',
'editsimilar-thanks-singleresult' => 'Thanks for your edit. Can you improve this related article? $1.',
'edittools' => '<!-- Text here will be shown below edit and upload forms. -->
<div style="margin-top: 2em; margin-bottom:1em;">Below are some commonly used wiki markup codes. Simply click on what you want to use and it will appear in the edit box above.</div>

<div id="editpage-specialchars" class="plainlinks" style="border-width: 1px; border-style: solid; border-color: #aaaaaa; padding: 2px;">
<span id="edittools_main">\'\'\'Insert:\'\'\' <charinsert>– — … ° ≈ ≠ ≤ ≥ ± − × ÷ ← → · § </charinsert></span><span id="edittools_name">&nbsp;&nbsp;\'\'\'Sign your username:\'\'\' <charinsert>~~&#126;~</charinsert></span>
----
<small><span id="edittools_wikimarkup">\'\'\'Wiki markup:\'\'\'
<charinsert><nowiki>{{</nowiki>+<nowiki>}}</nowiki> </charinsert> &nbsp;
<charinsert><nowiki>|</nowiki></charinsert> &nbsp;
<charinsert>[+]</charinsert> &nbsp;
<charinsert>[[+]]</charinsert> &nbsp;
<charinsert>[[Category:+]]</charinsert> &nbsp;
<charinsert>#REDIRECT&#32;[[+]]</charinsert> &nbsp;
<charinsert><s>+</s></charinsert> &nbsp;
<charinsert><sup>+</sup></charinsert> &nbsp;
<charinsert><sub>+</sub></charinsert> &nbsp;
<charinsert><code>+</code></charinsert> &nbsp;
<charinsert><blockquote>+</blockquote></charinsert> &nbsp;
<charinsert><ref>+</ref></charinsert> &nbsp;
<charinsert><nowiki>{{</nowiki>Reflist<nowiki>}}</nowiki></charinsert> &nbsp;
<charinsert><references/></charinsert> &nbsp;
<charinsert><includeonly>+</includeonly></charinsert> &nbsp;
<charinsert><noinclude>+</noinclude></charinsert> &nbsp;
<charinsert><nowiki>{{</nowiki>DEFAULTSORT:+<nowiki>}}</nowiki></charinsert> &nbsp;
<charinsert>&lt;nowiki>+</nowiki></charinsert> &nbsp;
<charinsert><nowiki><!-- </nowiki>+<nowiki> --></nowiki></charinsert>&nbsp;
<charinsert><nowiki><span class="plainlinks"></nowiki>+<nowiki></span></nowiki></charinsert><br/></span>
<span id="edittools_symbols">\'\'\'Symbols:\'\'\' <charinsert> ~ | ¡ ¿ † ‡ ↔ ↑ ↓ • ¶</charinsert> &nbsp;
<charinsert> # ¹ ² ³ ½ ⅓ ⅔ ¼ ¾ ⅛ ⅜ ⅝ ⅞ ∞ </charinsert> &nbsp;
<charinsert> ‘ “ ’ ” «+»</charinsert> &nbsp;
<charinsert> ¤ ₳ ฿ ₵ ¢ ₡ ₢ $ ₫ ₯ € ₠ ₣ ƒ ₴ ₭ ₤ ℳ ₥ ₦ № ₧ ₰ £ ៛ ₨ ₪ ৳ ₮ ₩ ¥ </charinsert> &nbsp;
<charinsert> ♠ ♣ ♥ ♦ </charinsert><br/></span>
<!-- Extra characters, hidden by default
<span id="edittools_characters">\'\'\'Characters:\'\'\'
<span class="latinx">
<charinsert> Á á Ć ć É é Í í Ĺ ĺ Ń ń Ó ó Ŕ ŕ Ś ś Ú ú Ý ý Ź ź </charinsert> &nbsp;
<charinsert> À à È è Ì ì Ò ò Ù ù </charinsert> &nbsp;
<charinsert> Â â Ĉ ĉ Ê ê Ĝ ĝ Ĥ ĥ Î î Ĵ ĵ Ô ô Ŝ ŝ Û û Ŵ ŵ Ŷ ŷ </charinsert> &nbsp;
<charinsert> Ä ä Ë ë Ï ï Ö ö Ü ü Ÿ ÿ </charinsert> &nbsp;
<charinsert> ß </charinsert> &nbsp;
<charinsert> Ã ã Ẽ ẽ Ĩ ĩ Ñ ñ Õ õ Ũ ũ Ỹ ỹ</charinsert> &nbsp;
<charinsert> Ç ç Ģ ģ Ķ ķ Ļ ļ Ņ ņ Ŗ ŗ Ş ş Ţ ţ </charinsert> &nbsp;
<charinsert> Đ đ </charinsert> &nbsp;
<charinsert> Ů ů </charinsert> &nbsp;
<charinsert> Ǎ ǎ Č č Ď ď Ě ě Ǐ ǐ Ľ ľ Ň ň Ǒ ǒ Ř ř Š š Ť ť Ǔ ǔ Ž ž </charinsert> &nbsp;
<charinsert> Ā ā Ē ē Ī ī Ō ō Ū ū Ȳ ȳ Ǣ ǣ </charinsert> &nbsp;
<charinsert> ǖ ǘ ǚ ǜ </charinsert> &nbsp;
<charinsert> Ă ă Ĕ ĕ Ğ ğ Ĭ ĭ Ŏ ŏ Ŭ ŭ </charinsert> &nbsp;
<charinsert> Ċ ċ Ė ė Ġ ġ İ ı Ż ż </charinsert> &nbsp;
<charinsert> Ą ą Ę ę Į į Ǫ ǫ Ų ų </charinsert> &nbsp;
<charinsert> Ḍ ḍ Ḥ ḥ Ḷ ḷ Ḹ ḹ Ṃ ṃ Ṇ ṇ Ṛ ṛ Ṝ ṝ Ṣ ṣ Ṭ ṭ </charinsert> &nbsp;
<charinsert> Ł ł </charinsert> &nbsp;
<charinsert> Ő ő Ű ű </charinsert> &nbsp;
<charinsert> Ŀ ŀ </charinsert> &nbsp;
<charinsert> Ħ ħ </charinsert> &nbsp;
<charinsert> Ð ð Þ þ </charinsert> &nbsp;
<charinsert> Œ œ </charinsert> &nbsp;
<charinsert> Æ æ Ø ø Å å </charinsert> &nbsp;
<charinsert> Ə ə </charinsert></span>&nbsp;<br/></span>
<span id="edittools_greek">\'\'\'Greek:\'\'\'
<charinsert> Ά ά Έ έ Ή ή Ί ί Ό ό Ύ ύ Ώ ώ </charinsert> &nbsp; 
<charinsert> Α α Β β Γ γ Δ δ </charinsert> &nbsp;
<charinsert> Ε ε Ζ ζ Η η Θ θ </charinsert> &nbsp;
<charinsert> Ι ι Κ κ Λ λ Μ μ </charinsert> &nbsp;
<charinsert> Ν ν Ξ ξ Ο ο Π π </charinsert> &nbsp;
<charinsert> Ρ ρ Σ σ ς Τ τ Υ υ </charinsert> &nbsp;
<charinsert> Φ φ Χ χ Ψ ψ Ω ω </charinsert> &nbsp;<br/></span>
<span id="edittools_cyrillic">\'\'\'Cyrillic:\'\'\' <charinsert> А а Б б В в Г г </charinsert> &nbsp;
<charinsert> Ґ ґ Ѓ ѓ Д д Ђ ђ </charinsert> &nbsp;
<charinsert> Е е Ё ё Є є Ж ж </charinsert> &nbsp;
<charinsert> З з Ѕ ѕ И и І і </charinsert> &nbsp;
<charinsert> Ї ї Й й Ј ј К к </charinsert> &nbsp;
<charinsert> Ќ ќ Л л Љ љ М м </charinsert> &nbsp;
<charinsert> Н н Њ њ О о П п </charinsert> &nbsp;
<charinsert> Р р С с Т т Ћ ћ </charinsert> &nbsp;
<charinsert> У у Ў ў Ф ф Х х </charinsert> &nbsp;
<charinsert> Ц ц Ч ч Џ џ Ш ш </charinsert> &nbsp;
<charinsert> Щ щ Ъ ъ Ы ы Ь ь </charinsert> &nbsp;
<charinsert> Э э Ю ю Я я </charinsert> &nbsp;<br/></span>
<span id="edittools_ipa">\'\'\'IPA:\'\'\' <span title="Pronunciation in IPA" class="IPA"><charinsert>t̪ d̪ ʈ ɖ ɟ ɡ ɢ ʡ ʔ </charinsert> &nbsp;
<charinsert> ɸ ʃ ʒ ɕ ʑ ʂ ʐ ʝ ɣ ʁ ʕ ʜ ʢ ɦ </charinsert> &nbsp;
<charinsert> ɱ ɳ ɲ ŋ ɴ </charinsert> &nbsp;
<charinsert> ʋ ɹ ɻ ɰ </charinsert> &nbsp;
<charinsert> ʙ ʀ ɾ ɽ </charinsert> &nbsp;
<charinsert> ɫ ɬ ɮ ɺ ɭ ʎ ʟ </charinsert> &nbsp;
<charinsert> ɥ ʍ ɧ </charinsert> &nbsp;
<charinsert> ɓ ɗ ʄ ɠ ʛ </charinsert> &nbsp;
<charinsert> ʘ ǀ ǃ ǂ ǁ </charinsert> &nbsp;
<charinsert> ɨ ʉ ɯ </charinsert> &nbsp;
<charinsert> ɪ ʏ ʊ </charinsert> &nbsp;
<charinsert> ɘ ɵ ɤ </charinsert> &nbsp;
<charinsert> ə ɚ </charinsert> &nbsp;
<charinsert> ɛ ɜ ɝ ɞ ʌ ɔ </charinsert> &nbsp;
<charinsert> ɐ ɶ ɑ ɒ </charinsert> &nbsp;
<charinsert> ʰ ʷ ʲ ˠ ˤ ⁿ ˡ </charinsert> &nbsp;
<charinsert> ˈ ˌ ː ˑ  ̪ </charinsert>&nbsp;</span><br/></span>
-->
</small></div>
<span style="float:right;"><small>\'\'[[MediaWiki:Edittools|View this template]]\'\'</small></span>',
'enotif_body' => 'Dear $WATCHINGUSERNAME,

There has been an edit to a page you are watching on {{SITENAME}}.

See $PAGETITLE_URL for the current version.

$NEWPAGE

The summary of the edit is: "$PAGESUMMARY"

Please visit and edit often...

{{SITENAME}}

___________________________________________
* Have you checked out our hubs? <http://www.wikia.com/wiki/Category:Hubs>

* Want to control which emails you receive? 
Go to: {{fullurl:{{ns:special}}:Preferences}}.',
'exif-fnumber' => 'F Number',
'filedelete-reason-dropdown' => '*Vandalism and problems
** Copyright violation
** Spam/vandalism
*Maintenance
** Author request
** Housekeeping
** Duplicated/superseded file
** Misnamed file',
'flickr4' => '{| class="toccolours" style="width:100%" cellpadding="2"
! style="background:#8da5cc; text-align:right; vertical-align:middle; padding-right:0.5em; width:15%;" | Description
| \'\'This image was uploaded through the [[Special:ImportFreeImages|ImportFreeImages]] special page. A detailed description could be found on flickr. – Please add more information manually, if needed.\'\'
|-
! style="background:#8da5cc; text-align:right; vertical-align:middle; padding-right:0.5em; white-space:nowrap;" | Date
| \'\'See the information on flickr (cf. "Source"). Add the date of creation manually, if needed.\'\'
|-
! style="background:#8da5cc; text-align:right; vertical-align:middle; padding-right:0.5em;" | Author
| [http://flickr.com/people/{{{3}}}/ {{{3}}}]
|-
! style="background:#8da5cc; text-align:right; vertical-align:middle; padding-right:0.5em;" | Source
| [http://flickr.com/photos/{{{2}}}/{{{1}}}/ This file on \'\'\'flickr.com\'\'\']
|-
! style="background:#8da5cc; text-align:right; vertical-align:middle; padding-right:0.5em;" | License
| \'\'\'CC-by-2.0\'\'\' – \'\'[[Media:{{PAGENAME}}|This file]]  is licensed under [[Wikipedia:Creative Commons|Creative Commons]] [http://creativecommons.org/licenses/by/2.0/ Attribution 2.0] License.\'\'
|}<includeonly>[[Category:CC-BY files|{{PAGENAME}}]]</includeonly>',
'flickr5' => '{| class="toccolours" style="width:100%" cellpadding="2"
! style="background:#8da5cc; text-align:right; vertical-align:middle; padding-right:0.5em; width:15%;" | Description
| \'\'This image was uploaded through the [[Special:ImportFreeImages|ImportFreeImages]] special page. A detailed description could be found on flickr. – Please add more information manually, if needed.\'\'
|-
! style="background:#8da5cc; text-align:right; vertical-align:middle; padding-right:0.5em; white-space:nowrap;" | Date
| \'\'See the information on flickr (cf. "Source"). Add the date of creation manually, if needed.\'\'
|-
! style="background:#8da5cc; text-align:right; vertical-align:middle; padding-right:0.5em;" | Author
| [http://flickr.com/people/{{{3}}}/ {{{3}}}]
|-
! style="background:#8da5cc; text-align:right; vertical-align:middle; padding-right:0.5em;" | Source
| [http://flickr.com/photos/{{{2}}}/{{{1}}}/ This file on \'\'\'flickr.com\'\'\']
|-
! style="background:#8da5cc; text-align:right; vertical-align:middle; padding-right:0.5em;" | License
| \'\'\'CC-by-sa-2.0\'\'\' – \'\'[[Media:{{PAGENAME}}|This file]]  is licensed under [[Wikipedia:Creative Commons|Creative Commons]] [http://creativecommons.org/licenses/by-sa/2.0/ Attribution ShareAlike 2.0] License.\'\'
|}<includeonly>[[Category:CC-BY-SA files|{{PAGENAME}}]]</includeonly>',
'group-janitor-member' => 'Janitor',
'grouppage-checkuser' => 'w:c:help:Help:CheckUser',
'grouppage-helper' => 'w:Wikia Helper Group',
'grouppage-janitor' => 'w:Wikia Janitors',
'highest_ratings' => 'Highest voted',
'imghistlegend' => 'Legend: (cur) = this is the current file, (del) = delete this old version, (rev) = revert to this old version.<br />
<i>Click on date to download the file or see the image uploaded on that date</i>.',
'insertimagetitle' => 'Upload image',
'ipboptions' => '2 hours:2 hours,1 day:1 day,3 days:3 days,1 week:1 week,2 weeks:2 weeks,1 month:1 month,3 months:3 months,6 months:6 months,1 year:1 year',
'istemplate' => 'transclusion',
'loginlanguagelinks' => '* English|en
* Deutsch|de
* Español|es
* Français|fr
* Italiano|it
* 中文|zh
* 日本語|ja',
'me_hide' => 'Ausblenden',
'me_tip' => 'Tip: you can add new sections, simply by typing their names with double equality signs in the existing sections, eg. == New Section ==',
'miniupload' => 'Simplified upload',
'monaco-category-list' => '*w:Category:Hubs|more wikis
**w:Gaming|Gaming
**w:Entertainment|Entertainment
**w:Sci-Fi|Science Fiction
**w:Big_wikis|Biggest wikis
**w:Hobbies|Hobbies
**w:Technology|Technology',
'monaco-footer-wikia-links' => '*http://www.wikia.com/wiki/About_Wikia|About Wikia
*http://www.wikia.com/wiki/Contact_us|Contact Wikia
*http://www.wikia.com/wiki/Terms_of_use|Terms
*http://www.mediawiki.org/|MediaWiki
*http://www.gnu.org/copyleft/fdl.html|GFDL
*http://www.wikia.com/wiki/Advertising|Advertise on Wikia
*http://www.wikia.com/wiki/Terms_of_use#Collection_of_personal_information|<b>Privacy</b>',
'monaco-jade' => 'Jade',
'monaco-related-communities' => '*w:Entertainment|Entertainment|TV shows, movies, cartoons and comics.
*w:Gaming|Gaming|Get your game on with Wikia\'s video game wikis.
*w:Sci-Fi|Science Fiction|Explore the world of the future.
*w:Big_wikis|Biggest Wikis|See Wikia\'s biggest wikis.
*w:Hubs|See all...',
'monaco-related-communities-2' => '*w:c:ssb|Super Smash Bros|Check out the Brawl
*w:c:aoc|Age of Conan Wiki|Age of Conan
*w:c:wow|WoWWiki|World of Warcraft
*w:c:gta|Grand Theft Wiki|Grand Theft Auto 4
*w:c:guildwars|GuildWiki|Guild Wars
*w:c:yugioh|Yu-Gi-Oh!
*w:c:Runescape|Runescape',
'monaco-sidebar' => '*mainpage|{{SITENAME}}
*mainpage|Top Content
**#popular#|most_popular
**#visited#|most_visited
**#voted#|highest_ratings
**#newlychanged#|newly_changed
*portal-url|Community
**#topusers#|top_users
**portal-url|portal
**forum-url|forum
*#category1#
*#category2#',
'monaco-toolbox' => '* upload-url|Upload image
* specialpages-url|specialpages
* helppage|help
* recentchanges-url|recentchanges
* randompage-url|randompage
* whatlinkshere|whatlinkshere',
'most_popular' => 'Editor\'s pick',
'movelogpagetext' => 'Below is a list of page moves.',
'movepagetext' => '<div id="MW-movepagetext">
Using the form below will rename a page, moving all of its history to the new name. The old title will become a redirect page to the new title. Links to the old page title will not be changed; be sure to check for double or broken redirects. You are responsible for making sure that links continue to point where they are supposed to go.

Note that the page will \'\'\'not\'\'\' be moved if there is already a page at the new title, unless it is empty or a redirect and has no past edit history. This means that you can rename a page back to where it was just renamed from if you make a mistake, and you cannot overwrite an existing page.

<b>WARNING!</b> This can be a drastic and unexpected change for a popular page; please be sure you understand the consequences of this before proceeding.
</div>',
'multiupload' => 'Multiple upload',
'multiupload-toolbox' => 'Upload multiple files',
'navlinks' => '* 1
** mainpage|Home
** Forum:Index|forum
** Special:Randompage|randompage',
'newarticletext' => '<div style="float:right;"><small>\'\'[[MediaWiki:Newarticletext|View this template]]\'\'</small></div>
\'\'\'You are starting a brand new article.\'\'\' 
* Check out \'\'\'[[Help:Editing]]\'\'\' for more information on how to edit wiki pages.
* Don\'t forget to \'\'\'categorize articles\'\'\' by adding <nowiki>[[Category:Name]]</nowiki> to the bottom of the page! A list of categories can be found on [[Special:Categories]].<br/><br/>',
'noarticletext' => '\'\'\'Oops! {{SITENAME}} does not have a {{NAMESPACE}} page with this exact name.\'\'\'

* \'\'\'<span class="plainlinks">[{{fullurl:{{FULLPAGENAMEE}}|action=edit}} Click here to start this page]</span>\'\'\'  or  \'\'\'<span class="plainlinks">[{{fullurl:Special:Search|search={{PAGENAMEE}}}} here to search for this phrase on the wiki]</span>\'\'\'.
* If a page previously existed at this exact title, please check the \'\'\'<span class="plainlinks">[{{fullurl:Special:Log/delete|page={{FULLPAGENAMEE}}}} deletion log]</span>\'\'\'.',
'nospecialpagetext' => 'You have requested a special page that is not recognized by {{SITENAME}}. A list of all recognized special pages may be found at [[{{ns:-1}}:Specialpages]].',
'nosuchuser' => 'There is no user by the name "$1". User names are case sensitive. Please check your spelling, or use the link below to create a new user account.',
'nstab-main' => 'Article',
'pagetitle' => '#wikiapagetitle#',
'pr_introductory_text' => 'Most pages on this wiki are editable, and you are welcome to edit the page and correct mistakes yourself! If you need help doing that, see [[Help:Editing|how to edit]] and [[Help:Reverting|how to revert vandalism]].

To contact staff or to report copyright problems, please see [[w:contact us|Wikia\'s "contact us" page]].<br/>Wiki software bugs can also be discussed on the [[w:Forum:Help_desk|Central forums]].

Reports made on this wiki will be displayed on [[Special:ProblemReports]].',
'pr_mailer_tmp_info' => 'You can add templated responses [[MediaWiki:ProblemReportsResponses|here]].<br/>Find out more on [[w:c:help:Help:ProblemReports|Help:ProblemReports]].',
'pr_view_staff' => 'Show reports that need staff help',
'pr_what_problem_software_bug' => 'there is a bug in the wiki software',
'prefs-help-realname' => 'Real name is optional and, if you choose to provide it, will be used for giving you attribution for your work.  This field must be filled in to use Wikia Search.',
'previewnote' => '<strong>Preview mode: no changes saved yet! Scroll down to continue editing.</strong>',
'prlogheader' => 'Logs of [[Special:ProblemReports|reported problems]] and changes of their status',
'problemReportsResponses' => '-',
'protect-unchain' => 'Unlock move permissions selection box.',
'rcnote' => 'Below {{PLURAL:$1|is \'\'\'1\'\'\' change|are the last \'\'\'$1\'\'\' changes}} in the last {{PLURAL:$2|day|\'\'\'$2\'\'\' days}}, as of $3.',
'recentchangeslinked-summary' => 'This special page lists the last changes on pages that are linked. Pages on your watchlist are \'\'\'bold\'\'\'.',
'recentchangestext' => '<span style="float:right;"><small>\'\'[[MediaWiki:Recentchangestext|View this template]]\'\'</small></span>
This special page lets you track the most recent changes to the wiki.

{| class="plainlinks" style="background: transparent; margin-left:0.5em; margin-bottom:0.5em;" cellpadding="0" cellspacing="0"
|-valign="top"
|align="right"|\'\'\'Logs&nbsp;:&nbsp;\'\'\'
|align="left" |[[Special:Newpages|New pages]] - [[Special:Newimages|New files]] - [[Special:Log/delete|Deletions]] - [[Special:Log/move|Moves]] - [[Special:Log/upload|Uploads]] - [[Special:Log/block|Blocks]] - [[Special:Log|more logs...]]
|-valign="top"
|align="right"|\'\'\'Special pages&nbsp;:&nbsp;\'\'\'
|align="left" |[[Special:Wantedpages|Wanted pages]] - [[Special:Longpages|Long pages]] - [[Special:Uncategorizedimages|Uncategorized images]] - [[Special:Uncategorizedpages|Uncategorized pages]] - [[Special:Specialpages|more special pages...]]
|-valign="top"
|align="right"|\'\'\'Find users&nbsp;:&nbsp;\'\'\'
|align="left" |[[Special:Listusers/sysop|Administrators]] - [[Special:Listusers/helper|Helpers]] - [[Special:Listusers/janitor|Janitors]] - [[Special:Listusers/staff|Wikia Staff]] - [[Special:Listusers|more users...]]
|}',
'revertpage' => 'Reverted edits by [[Special:Contributions/$2|$2]] ([[User talk:$2|talk]]) to last version by [[User:$1|$1]]',
'selfmove' => '\'\'\'Source and destination titles are the same; can\'t move a page over itself.\'\'\' Please check that you didn\'t enter the destination title into the "reason" field instead of the "new title" field.',
'session_fail_preview' => '<strong>Sorry! We could not process your edit due to a loss of session data. Please try again. If you are still seeing this message, try logging out and logging back in.</strong> This may occur when an edit is left unsaved for a long time, or during server maintenance. If it continues for an extended period, please [[Special:Contact|contact Wikia]].',
'sitestatstext' => '__NOTOC__
{| class="plainlinks" align="top" width="100%"
| valign="top" width="50%" | 
===Page statistics===
\'\'\'$1 [[Special:Allpages|total pages]]\'\'\' on  {{SITENAME}} ([[Special:Newpages|new pages]]):

*\'\'\'$2 legitimate content pages:\'\'\'
**[[Special:Allpages|main namespace]] pages
**must include one internal link
**may be [[Special:Shortpages|short]] or [[Special:Longpages|long pages]]
**may be [[Special:Lonelypages|orphaned]]

*plus additional non-content pages, such as:
**most pages outside the main namespace<br/>(e.g. templates and discussion pages)
**[[Special:Listredirects|redirects]] ([[Special:BrokenRedirects|broken]]/[[Special:DoubleRedirects|double]])
**[[Special:Deadendpages|dead ends]]
**[[Special:Disambiguations|disambiguations]]

| valign="top" width="50%" |

===Other statistics===
*\'\'\'$8 [[Special:Imagelist|images]]\'\'\' ([[Special:Newimages|new images]])
*\'\'\'$4\'\'\' page edits / \'\'\'$1\'\'\' pages = \'\'\'$5\'\'\' edits/page ([[Special:Mostrevisions|most revisions]])

=== Job queue ===
*The current [http://meta.wikimedia.org/wiki/Help:Job_queue job queue] length is \'\'\'$7\'\'\'

=== More info ===
* [[Special:Specialpages|Special]] pages
* [[Special:Allmessages|MediaWiki]] messages

Readers may also be interested in the much more detailed statistics linked from \'\'\'[http://wikistats.wikia.com WikiStats]\'\'\'.
|}',
'skinchooser-customcss' => 'For custom themes, select the "custom" option in the menu above and specify custom CSS in [[MediaWiki:Monaco.css]].',
'sp-contributions-footer-anon' => '{| id="anontalktext" class="plainlinks noeditsection" style="font-size:90%; border: 1px solid #B8B8B8; margin:1em 1em 0em 1em; padding:0.25em 1em 0.25em 1em; clear: both;" 
| \'\'\'This is the contributions page for an anonymous user  who has not created an account yet or who does not use it, identified by the user\'s numerical [[wikipedia:IP address|IP address]].\'\'\'

Some IP addresses change periodically, and may be shared by several users. If you are an anonymous user, you may [[{{ns:Special}}:Userlogin|create an account or log in]] to avoid future confusion with other anonymous users. Registering also hides your IP address. [[w:c:help:Help:Why create an account|Why create an account?]] ([[w:c:help:Help:Create an account|How to create an account]])

\'\'\'IP info:\'\'\' [http://samspade.org/whois?query=$1 WHOIS] • [http://openrbl.org/query?$1 RDNS] • [http://www.robtex.com/rbls/$1.html RBLs] • [http://www.dnsstuff.com/tools/tracert.ch?ip=$1 Traceroute] • [http://www.as3344.net/is-tor/?args=$1 TOR check] &mdash; [[wikipedia:Regional Internet registry|RIR]]s: [http://ws.arin.net/whois/?queryinput=$1 America] &bull; [http://www.ripe.net/fcgi-bin/whois?searchtext=$1 Europe] · [http://www.afrinic.net/cgi-bin/whois?query=$1 Africa] · [http://www.apnic.net/apnic-bin/whois.pl?searchtext=$1 Asia-Pacific] · [http://www.lacnic.net/cgi-bin/lacnic/whois?lg=EN&query=$1 Latin America/Caribbean]
|}',
'specialpages-url' => 'Special:Specialpages',
'spoiler-endshere' => 'Spoiler ends here.',
'spoiler-showhide-label' => 'Click Here to Show/Hide Spoiler Information',
'spoiler-warning' => 'Spoiler warning: Quest and/or game spoiling details follow.',
'stf_abuse' => 'This email was sent via Wikia.
If you think this was sent in error, please let us know at support@wikia.com',
'stf_ctx_invite' => 'More than one? Separate with commas - up to $1!',
'subcategorycount' => 'There {{PLURAL:$1|is one subcategory|are $1 subcategories}} in this category, which {{PLURAL:$1|is|are}} shown below.  {{PLURAL:$1||More may be shown on subsequent pages.}}',
'summary-preview' => 'Preview of edit summary',
'swm-label-mode-group-hint' => '<i>Note that this is more time consuming and will be queued in TaskManager.</i>',
'talkexists' => '\'\'\'The page itself was moved successfully, but the talk page could not be moved because one already exists at the new title. Contact an [[Project:Administrators|administrator]], but do not just copy and paste the contents.\'\'\'',
'talkpagetext' => '<div style="margin: 0 0 1em; padding: .5em 1em; vertical-align: middle; border: solid #999 1px;">\'\'\'This is a talk page. Please remember to sign your posts using four tildes (<code><nowiki>~~~~</nowiki></code>).\'\'\'</div>',
'templatesused' => '\'\'\'Pages transcluded onto the current version of this page:\'\'\'',
'timezonetext' => 'The number of hours your local time differs from server time (UTC).<br/><small>This will adjust the times shown on your watchlist and recent changes, but not in signatures on talk pages, which will always be in UTC.</small>',
'tips' => '* Admins: you can add tips here by editing [[MediaWiki:Tips]]',
'tog-editsectiononrightclick' => 'Enable section editing by right clicking on section titles (JavaScript)',
'tog-externaleditor' => 'Use external editor by default (<a href="http://en.wikipedia.org/wiki/Wikipedia:Text_editor_support">more information</a>)',
'tog-fancysig' => '<b>Raw signature</b> (If unchecked, the contents of the box above will be treated as your nickname and link automatically to your user page.  If checked, the contents should be formatted with Wiki markup, including all links.)',
'tog-previewonfirst' => 'Show preview on first edit (see <a href="http://help.wikia.com/wiki/Help:Edit_conflict">help</a>)',
'tog-skinoverwrite' => '<b>Let the admins override my skin choice.</b> (recommended)<br>Setting this means the system will <b><i>ignore</i></b> your preferred skin and show the skin picked by each wiki\'s admins. Some wiki administrators take a lot of time to customize the look of their wikis. Check this box to see wikis with full customization.<br>
<b>This setting will follow you across Wikia.</b>',
'tooltip-ca-history' => 'Past versions of this page',
'top_users' => 'Featured users',
'ue-EditArticle' => 'This is ue_EditSimilar event <a href="http://www.example.co.uk/files/map.pdf" onClick="javascript:urchinTracker(\'some value\'); ">now click here</a>',
'ue-NewToWikia' => 'This is ue_NewToWikia Event <a href="http://www.example.co.uk/files/map.pdf" onClick="javascript:urchinTracker(\'some value\'); ">now click here</a>',
'ue-VisitN1' => 'Welcome to %SITENAME% - New user? Please <a href="/index.php?title=Special:Userlogin&type=signup" id="ue-Visit1_1">create an account (free).</a>',
'ue-VisitN10' => 'To save your edits and watch pages, please <a href="/index.php?title=Special:Userlogin&type=signup" onClick="javascript:urchinTracker(\'/reg/ue-Visit10_10\'); ">create an account (free)</a>',
'ue-VisitN2' => 'Welcome to %SITENAME% - Track updates to this page by <a href="/index.php?title=Special:Userlogin&type=signup" id="ue-Visit2_1">creating an account (free)</a>',
'ue-VisitN3' => 'Welcome to %SITENAME% - New user? Please <a href="/index.php?title=Special:Userlogin&type=signup" id="ue-Visit3_1">create an account (free).</a>',
'ue-VisitN4' => 'Welcome to %SITENAME%! To track all your favorite pages <a href="/index.php?title=Special:Userlogin&type=signup" id="ue-Visit4_1">Create an account (free)</a>',
'ue-VisitN5' => 'Welcome to %SITENAME%! To save your edits and watch pages, please <a href="/index.php?title=Special:Userlogin&type=signup" id="ue-Visit5_1">create an account (free)</a>',
'ue-VisitN6' => 'Did you know? Registered users of %SITENAME% can watch this page for updates -- <a href="/index.php?title=Special:Userlogin&type=signup"id="ue-Visit6_1">create a free account now!</a>',
'ue-VisitN7' => 'Get your free %SITENAME% User ID - <a href="/index.php?title=Special:Userlogin&type=signup" id="ue-Visit7_1">create an account now!</a>',
'ue-VisitN8' => 'Join %SITENAME% for free -- <a href="/index.php?title=Special:Userlogin&type=signup" id="ue-Visit8_1">create an account now!</a>',
'ue-VisitN9' => 'Welcome to %SITENAME%! Please <a href="/index.php?title=Special:Userlogin&type=signup" id="ue-Visit9_1">create an account (free)</a>',
'ue-VisitNX1' => 'Welcome to %SITENAME% <a href="http://www.wikia.com/index.php?title=Special:Userlogin&type=signup" id="ue-Visit1_1">Create an account for $1!</a>',
'ue-VisitNfp005.sjc.wikia-inc.com2' => 'my super message for fp005 gee ffff',
'ue-WatchPage' => 'This is Ue-WatchPage event <a href="http://www.example.co.uk/files/map.pdf" onClick="javascript:urchinTracker(\'some value\'); ">now click here</a>',
'ue-WelcomeMessage' => 'Hello and thank you for tripping on ue-WelcomeMessage',
'ue_VisitN1' => 'Whaz up dog message Visit 1',
'unblocked' => '[[User:$1|$1]] has been unblocked ([[Special:Blockip/$1|re-block]]).',
'unblockiptext' => 'Use the form below to restore write access to a blocked IP address or username. Remember, there was probably a good reason for the person to be blocked. Please discuss the block with the blocking administrator before unblocking.',
'undeleteextrahelp' => 'To restore the entire page and its history, leave all checkboxes deselected and click \'\'\'\'\'Restore\'\'\'\'\'. To perform a selective restoration, check the boxes corresponding to the revisions to be restored and click \'\'\'\'\'Restore\'\'\'\'\'. Selecting a box, then shift selecting another will fill all boxes in between in many browsers. Clicking \'\'\'\'\'Reset\'\'\'\'\' will clear the comment field and all checkboxes. Please make sure that you leave a summary in the comment box.',
'undeletehistory' => 'By default, if you restore the page, all previous revisions will be restored to the page\'s history. If you do not want to restore all revisions, select only the checkboxes beside the revisions you do want to restore. If a new page with the same name has been created since the deletion, the restored revisions will be merged with the new page\'s history, and the current revision of the live page will not be automatically replaced. Be careful not to do this unless you specifically intend to merge the histories of the two pages. \'\'\'WARNING:\'\'\' Any protection on the page will be lost when deleting and restoring revisions! If you wish this page to be protected, you must protect it immediately after restoration.',
'undo-success' => '\'\'\'The edit can be undone. Please check the comparison below to verify that this is what you want to do, and then save the changes below to finish undoing the edit.\'\'\'

\'\'If you are undoing an edit that is not vandalism, explain the reason in the edit summary rather than using only the default message.\'\'',
'undo-summary' => 'Undo revision $1 by [[Special:Contributions/$2|$2]] ([[User talk:$2|talk]])',
'upload' => 'Upload image/file',
'upload-url' => 'Special:Upload',
'uploadscripted' => 'This file contains HTML or script code that may be erroneously interpreted by a web browser.',
'uploadvirus' => 'The file may contain a virus! Details: $1',
'user_Register_Text' => '===What is {{SITENAME}}?===
   
{{SITENAME}} is the best place to learn everything about the game we all love.  The {{SITENAME}} community is powered by die-hard fans.  We live, breathe, and die the game.

===Why is {{SITENAME}} awesome?===
 
We\'re a wiki!  What this means is that you could be an important author on {{SITENAME}}. Are you an expert who knows all the tricks and nuances of the game?  Are you a casual player who found something really interesting? Help us create the ultimate resource!

===How much does it cost?===
 
{{SITENAME}} is absolutely free.

===We respect your privacy===
 
We are serious about keeping your private information private.  We do not disclose your email address to third parties.

__NOTOC__ __NOEDITSECTION__',
'userstatstext' => '<!--see Sitestatstext for what precedes this-->
There are \'\'\'$1\'\'\' registered users on all of Wikia. Of the registered users of {{SITENAME}} \'\'\'$2\'\'\' are administrators (see $3).',
'var_logheader' => '&nbsp;',
'var_logtext' => 'Settings log',
'var_set' => 'set the $2 to "$3"',
'variantname-zh-cn' => '简体',
'variantname-zh-tw' => '正體',
'welcomecreation' => '== Welcome, $1! ==

Your account has been created.

\'\'\'[[User:$1|Click here to create your user page!]]\'\'\'

You can also change your {{SITENAME}} [[Special:Preferences|preferences]].',
'wikicities-nav' => 'wikia',
'yournick' => 'Signature:',
) );
