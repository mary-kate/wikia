<?php
/**
*
* help_faq [Polski]
*
* @package language
* @version $Id: help_faq.php,v 1.42.1 2007/12/29 Ronnie Exp $
* @copyright (c) 2007 phpBB3.PL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

// INFORMACJA
//
// Wszystkie pliki językowe powinny używać kodowania UTF-8 i nie powinny zawierać znaku BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$help = array(
	array(
		0 => '--',
		1 => 'Problemy z logowaniem i rejestracją'
	),
	array(
		0 => 'Dlaczego nie mogę się zalogować?',
		1 => 'Powodów może być kilka. Po pierwsze: Czy w ogóle zarejestrowałeś się na tym forum? Jeżeli nie, to musisz wiedzieć, że rejestracja jest konieczna aby móc się logować. A jeżeli się zarejestrowałeś i mimo to nie możesz się zalogować, to upewnij się, że Twoja nazwa użytkownika i Twoje hasło są prawidłowe. Jeżeli są, to skontaktuj się z administratorem forum, żeby upewnić się, że nie zostałeś zbanowany. Jest też możliwe, że problem powoduje błędna konfiguracja forum.'
	),
	array(
		0 => 'Dlaczego w ogóle muszę się rejestrować?',
		1 => 'Możliwe, że nie musisz, to od administratora forum zależy czy aby pisać wiadomości konieczna jest rejestracja. W każdym razie rejestracja da Ci dostęp do dodatkowych możliwości niedostępnych dla gości takich jak własny avatar, wysyłanie prywatnych wiadomości i e-maili do innych użytkowników, należenie do grup użytkowników itp. Rejestracja zajmuje tylko chwilę, więc jest zalecane, abyś ją wykonał.'
	),
	array(
		0 => 'Dlaczego jestem automatycznie wylogowywany?',
		1 => 'Jeżeli nie zaznaczysz opcji <em>Zaloguj mnie automatycznie przy każdej wizycie</em> w czasie logowania, forum zachowa informację o tym, że jesteś zalogowany tylko przez ustawiony przez administratora czas. To zapobiega przejęciu dostępu do Twojego konta przez kogoś innego. Aby pozostać zalogowanym, zaznacz opcję „Zaloguj mnie automatycznie przy każdej wizycie” podczas logowania się. Jest to stanowczo niezalecane, jeżeli korzystasz z forum ze współdzielonego z kimś komputera, np. w bibliotece, kawiarence internetowej, sali komputerowej w szkole / na uczelni itp. Jeżeli nie widzisz tej opcji, to oznacza to, że administrator zablokował tę funkcję.'
	),
	array(
		0 => 'W jaki sposób mogę zapobiec wyświetlaniu mojej nazwy użytkownika na liście użytkowników przeglądających forum?',
		1 => 'W swoim Panelu Zarządzania Kontem, w „Ustawieniach forum”, znajdziesz opcję <em>Nie pokazuj, że jestem online</em>. Włącz tę opcję i Twoja nazwa użytkownika będzie wyświetlana tylko dla administratorów, moderatorów i Ciebie. Będziesz liczony jako ukryty użytkownik.'
	),
	array(
		0 => 'Straciłem swoje hasło!',
		1 => 'Nie panikuj! Twoje hasło wprawdzie nie może zostać odzyskane, ale bez problemu może zostać zresetowane. Przejdź na stronę logowania i kliknij na link <em>Zapomniałem hasła</em>. Postępuj zgodnie z instrukcjami, a prawdopodobnie niedługo znów będziesz mógł się zalogować.'
	),
	array(
		0 => 'Zarejestrowałem się, ale nie mogę się zalogować!',
		1 => 'Po pierwsze, sprawdź, czy wpisujesz prawidłową nazwę użytkownika i hasło. Jeśli tak, to nastąpiła jedna z tych dwóch rzeczy: Jeśli COPPA jest włączone, a Ty poinformowałeś skrypt w czasie rejestracji, że masz mniej niż 13 lat, to będziesz musiał wykonać instrukcje wysłane na Twój adres e-mail. Niektóre fora wymagają też, żeby nowe rejestracje były aktywowane przez Ciebie albo przez administratora, zanim będziesz mógł się zalogować; informacja o tym została wyświetlona podczas rejestracji. Jeżeli został do Ciebie wysłany e-mail, postępuj zgodnie z instrukcjami w nim zawartymi. Jeżeli nie otrzymałeś żadnego e-maila, prawdopodobnie podałeś nieprawidłowy adres e-mail lub e-mail został zaklasyfikowany jako spam przez filtr antyspamowy. Jeśli jesteś pewien, że podany przez Ciebie adres e-mail jest prawidłowy, spróbuj skontaktować się z administratorem.'
	),
	array(
		0 => 'Zarejestrowałem się jakiś czas temu, ale nie mogę się teraz zalogować!?!',
		1 => 'Spróbuj znaleźć adres e-mail wysłany do Ciebie gdy się rejestrowałeś pierwszy raz, sprawdź w nim swój login i hasło, a potem spróbuj zalogować się jeszcze raz. Możliwe też, że administrator deaktywował lub usunął Twoje konto z jakiegoś powodu. Wiele for systematycznie usuwa użytkowników, którzy nic nie pisali przez dłuższy czas, żeby zmniejszyć wielkość bazy danych. Jeżeli to nastąpiło, spróbuj zarejestrować się jeszcze raz i być bardziej zaangażowanym w dyskusje.'
	),
	array(
		0 => 'Co to COPPA?',
		1 => 'COPPA, albo Child Online Privacy and Protection Act of 1998 to prawo obowiązujące w USA, wymagajace, aby strony internetowe mogące potencjalnie zbierać informacje od ludzi mających mniej niż 13 lat, miały piśmienną zgodę rodziców lub prawnych opiekunów na zbieranie informacji prywatnych od osoby mającej mniej niż 13 lat. Jeżeli nie jesteś pewien czy to dotyczy Ciebie jako kogoś próbującego zarejestrować się na stronie WWW, skontaktuj się z prawnikiem.',
	),
	array(
		0 => 'Dlaczego nie mogę się zarejestrować?',
		1 => 'Możliwe jest, że właściciel strony zbanował Twój adres IP lub zabronił nazwy użytkownika, którą próbujesz zarejestrować. Właściciel strony mógł też zablokować rejestracje, skontaktuj się z nim, żeby dowiedzieć się więcej na ten temat.',
	),
	array(
		0 => 'Co robi funkcja „Usuń wszystkie ciasteczka”?',
		1 => 'Funkcja „Usuń wszystkie ciasteczka” usuwa wszystkie ciasteczka utworzone przez skrypt phpBB, które powodują, że jesteś nadal zalogowany na forum. Dostarczają one również funkcji takich, jak pamiętanie, co przeczytałeś, a czego nie, jeżeli zostały one włączone przez administratora forum. Jeżeli masz problemy z (wy)logowaniem się, usunięcie ciasteczek może pomóc.',
	),
	array(
		0 => '--',
		1 => 'Preferencje i ustawienia użytkowników'
	),
	array(
		0 => 'Jak zmienić moje ustawienia?',
		1 => 'Jeżeli jesteś zarejestrowanym użytkownikiem, wszystkie Twoje ustawienia są zapisywane w bazie danych forum. Żeby je zmienić, zajrzyj do swojego Panelu Zarządzania Kontem; link zazwyczaj znajduje się na górze stron forum. Ten panel pozwoli Ci zmienić swoje ustawienia i preferencje.'
	),
	array(
		0 => 'Czasy są nieprawidłowe!',
		1 => 'Możliwe, że wyświetlany czas pochodzi z innej strefy czasowej, niż ta, w której się znajdujesz. Jeżeli właśnie to jest problemem, przejdź do swojego Panelu Zarządzania Kontem i zmień swoją strefę czasową aby zgadzała się z Twoim położeniem, np. w Europie Centralnej wybierz Środkowoeuropejski Czas Standardowy. Weź pod uwagę, że zmiana strefy czasowej, tak jak większości ustawień, może zostać wykonana tylko przez zarejestrowanych użytkowników. Jeżeli nie jesteś zarejestrowany, to teraz jest dobry moment na to, żeby się zarejestrować.'
	),
	array(
		0 => 'Zmieniłem strefę czasową, a wyświetlany czas nadal jest zły!',
		1 => 'Jeżeli jesteś pewien, że ustawiłeś strefę czasową i czas letni/DST prawidłowo, a czas nadal jest wyświetlany nieprawidłowo, to znaczy, że czas serwera jest nieprawidłowy. Poinformuj o tym administratora, aby naprawił problem.'
	),
	array(
		0 => 'Mojego języka nie ma na liście!',
		1 => 'Albo administrator nie zainstalował Twojego języka albo nikt nie przetłumaczył phpBB3 na Twój język. Spróbuj spytać administratora forum czy może zainstalować pakiet językowy, którego potrzebujesz. Jeżeli pakiet językowy nie istnieje, nie krępuj się ze stworzeniem nowego tłumaczenia. Więcej informacji na ten temat można znaleźć na stronie WWW phpBB (link na dole stron forum).'
	),
	array(
		0 => 'Jak mogę wyświetlić obrazek pod moją nazwą użytkownika?',
		1 => 'Istnieją dwa rodzaje obrazków wyświetlanych (zazwyczaj) pod nazwa użytkownika w czasie przeglądania postów. Jeden z nich to obrazki związane z rangą użytkownika, zazwyczaj w formie gwiazdek, bloczków czy kropek, pokazujących jak dużo postów użytkownik napisał lub jaki jest status użytkownika na forum. Drugi, zazwyczaj większy obrazek, jest znany jako avatar i jest unikalny dla każdego użytkownika. Możesz go ustawić w Panelu Zarządzania Kontem, pod warunkiem, że administrator forum właczył funkcje avatarów, a Ty masz wystarczające uprawnienia. Jeżeli nie masz możliwości używania avatarów, skontaktuj się z administratorem i zapytaj czym to jest spowodowane.'
	),
	array(
		0 => 'Co to jest ranga i jak mogę ją zmienić?',
		1 => 'Rangi, wyświetlane pod nazwami użytkowników, oznaczają zazwyczaj ile postów ten użytkownik napisał lub jaki ma status na forum, np. moderator czy administrator. Ogólnie to nie możesz tak po prostu zmienić wyglądu żadnych rang na forum, ponieważ ustawia je administrator forum. Nie pisz postów tylko po to, żeby zwiększyć swój licznik postów i przez to swoją rangę. Większość forów nie toleruje takich działań i moderator lub administrator po prostu obniży Twój licznik postów albo przyzna Ci ostrzeżenie.'
	),
	array(
		0 => 'Gdy próbuję wysłać wiadomość e-mail do użytkownika, forum każe mi się zalogować. Dlaczego?',
		1 => 'Tylko zarejestrowani użytkownicy mogą wysyłać e-maile do innych użytkowników przez wbudowany formularz wysyłania e-maili i to tylko wtedy, jeżeli administrator włączył tę funkcję. Ma to zabezpieczać przed nieprawidłowym używaniem systemu e-maili przez anonimowych użytkowników.'
	),
	array(
		0 => '--',
		1 => 'Problemy z pisaniem'
	),
	array(
		0 => 'Jak stworzyć nowy wątek na forum?',
		1 => 'Aby stworzyć nowy wątek, kliknij na właściwy przycisk przy wyświetlaniu wybranego działu forum. Możliwe, że przed stworzeniem wątku będziesz musiał się zarejestrować. Lista Twoich uprawnień w każdym dziale jest wyświetlana pod listą wątków. Przykłady: Możesz tworzyć nowy wątek, Możesz głosować w ankietach itp.'
	),
	array(
		0 => 'Jak przeedytować lub usunąć post?',
		1 => 'Jeśli nie jesteś administratorem lub moderator forum, możesz edytować i usuwać tylko swoje posty i to tylko wtedy, jeżeli administrator w ten sposób ustawił uprawnienia. Możesz przeedytować post klikając na przycisk „edytuj” przy wybranym poście, czasami tylko przez pewien czas po jego napisaniu. Jeżeli ktoś już odpowiedział na ten post, pod Twoim postem pojawi się informacja, ile razy go edytowałeś i kiedy zrobiłeś to ostatni raz. Informacja ta wyświetli się tylko wtedy, jeśli ktoś odpowiedział; nie pojawi się jeśli moderator lub administrator edytował post, choć oni mogą zostawić notatkę z informacją dlaczego go przeedytowali. Zauważ, że zwykli użytkownicy nie mogą usuwać postów, gdy ktoś już na nie odpowiedział.'
	),
	array(
		0 => 'How do I add a signature to my post?',
		1 => 'To add a signature to a post you must first create one via your User Control Panel. Once created, you can check the <em>Add signature</em> box on the posting form to add your signature. You can also add a signature by default to all your posts by checking the appropriate radio button in your profile. If you do so, you can still prevent a signature being added to individual posts by un-checking the add signature box within the posting form.'
	),
	array(
		0 => 'How do I create a poll?',
		1 => 'When posting a new topic or editing the first post of a topic, click the “Poll creation” tab below the main posting form; if you cannot see this, you do not have appropriate permissions to create polls. Enter a title and at least two options in the appropriate fields, making sure each option is on a separate line in the textarea. You can also set the number of options users may select during voting under “Options per user”, a time limit in days for the poll (0 for infinite duration) and lastly the option to allow users to amend their votes.'
	),
	array(
		0 => 'Why can’t I add more poll options?',
		1 => 'The limit for poll options is set by the board administrator. If you feel you need to add more options to your poll then the allowed amount, contact the board administrator.'
	),
	array(
		0 => 'How do I edit or delete a poll?',
		1 => 'As with posts, polls can only be edited by the original poster, a moderator or an administrator. To edit a poll, click to edit the first post in the topic; this always has the poll associated with it. If no one has cast a vote, users can delete the poll or edit any poll option. However, if members have already placed votes, only moderators or administrators can edit or delete it. This prevents the poll’s options from being changed mid-way through a poll.'
	),
	array(
		0 => 'Why can’t I access a forum?',
		1 => 'Some forums may be limited to certain users or groups. To view, read, post or perform another action you may need special permissions. Contact a moderator or board administrator to grant you access.'
	),
	array(
		0 => 'Why can’t I add attachments?',
		1 => 'Attachment permissions are granted on a per forum, per group, or per user basis. The board administrator may not have allowed attachments to be added for the specific forum you are posting in, or perhaps only certain groups can post attachments. Contact the board administrator if you are unsure about why you are unable to add attachments.'
	),
	array(
		0 => 'Why did I receive a warning?',
		1 => 'Each board administrator has their own set of rules for their site. If you have broken a rule, you may issued a warning. Please note that this is the board administrator’s decision, and the phpBB Group has nothing to do with the warnings on the given site. Contact the board administrator if you are unsure about why you were issued a warning.'
	),
	array(
		0 => 'How can I report posts to a moderator?',
		1 => 'If the board administrator has allowed it, you should see a button for reporting posts next to the post you wish to report. Clicking this will walk you through the steps necessary to report the post.'
	),
	array(
		0 => 'What is the “Save” button for in topic posting?',
		1 => 'This allows you to save passages to be completed and submitted at a later date. To reload a saved passage, visit the User Control Panel.'
	),
	array(
		0 => 'Why does my post need to be approved?',
		1 => 'The board administrator may have decided that posts in the forum you are posting to require review before submission. It is also possible that the administrator has placed you in a group of users whose posts require review before submission. Please contact the board administrator for further details.'
	),
	array(
		0 => 'How do I bump my topic?',
		1 => 'By clicking the “Bump topic” link when you are viewing it, you can “bump” the topic to the top of the forum on the first page. However, if you do not see this, then topic bumping may be disabled or the time allowance between bumps has not yet been reached. It is also possible to bump the topic simply by replying to it, however, be sure to follow the board rules when doing so.'
	),
	array(
		0 => '--',
		1 => 'Formatting and Topic Types'
	),
	array(
		0 => 'What is BBCode?',
		1 => 'BBCode is a special implementation of HTML, offering great formatting control on particular objects in a post. The use of BBCode is granted by the administrator, but it can also be disabled on a per post basis from the posting form. BBCode itself is similar in style to HTML, but tags are enclosed in square brackets [ and ] rather than &lt; and &gt;. For more information on BBCode see the guide which can be accessed from the posting page.'
	),
	array(
		0 => 'Can I use HTML?',
		1 => 'No. It is not possible to post HTML on this board and have it rendered as HTML. Most formatting which can be carried out using HTML can be applied using BBCode instead.'
	),
	array(
		0 => 'What are Smilies?',
		1 => 'Smilies, or Emoticons, are small images which can be used to express a feeling using a short code, e.g. :) denotes happy, while :( denotes sad. The full list of emoticons can be seen in the posting form. Try not to overuse smilies, however, as they can quickly render a post unreadable and a moderator may edit them out or remove the post altogether. The board administrator may also have set a limit to the number of smilies you may use within a post.'
	),
	array(
		0 => 'Can I post images?',
		1 => 'Yes, images can be shown in your posts. If the administrator has allowed attachments, you may be able to upload the image to the board. Otherwise, you must link to an image stored on a publicly accessible web server, e.g. http://www.example.com/my-picture.gif. You cannot link to pictures stored on your own PC (unless it is a publicly accessible server) nor images stored behind authentication mechanisms, e.g. hotmail or yahoo mailboxes, password protected sites, etc. To display the image use the BBCode [img] tag.'
	),
	array(
		0 => 'What are global announcements?',
		1 => 'Global announcements contain important information and you should read them whenever possible. They will appear at the top of every forum and within your User Control Panel. Global announcement permissions are granted by the board administrator.'
	),
	array(
		0 => 'What are announcements?',
		1 => 'Announcements often contain important information for the forum you are currently reading and you should read them whenever possible. Announcements appear at the top of every page in the forum to which they are posted. As with global announcements, announcement permissions are granted by the board administrator.'
	),
	array(
		0 => 'What are sticky topics?',
		1 => 'Sticky topics within the forum appear below announcements and only on the first page. They are often quite important so you should read them whenever possible. As with announcements and global announcements, sticky topic permissions are granted by the board administrator.'
	),
	array(
		0 => 'What are locked topics?',
		1 => 'Locked topics are topics where users can no longer reply and any poll it contained was automatically ended. Topics may be locked for many reasons and were set this way by either the forum moderator or board administrator. You may also be able to lock your own topics depending on the permissions you are granted by the board administrator.'
	),
	array(
		0 => 'What are topic icons?',
		1 => 'Topic icons are author chosen images associated with posts to indicate their content. The ability to use topic icons depends on the permissions set by the board administrator.'
	),
	array(
		0 => '--',
		1 => 'User Levels and Groups'
	),
	array(
		0 => 'What are Administrators?',
		1 => 'Administrators are members assigned with the highest level of control over the entire board. These members can control all facets of board operation, including setting permissions, banning users, creating usergroups or moderators, etc., dependent upon the board founder and what permissions he or she has given the other administrators. They may also have full moderator capabilities in all forums, depending on the settings put forth by the board founder.'
	),
	array(
		0 => 'What are Moderators?',
		1 => 'Moderators are individuals (or groups of individuals) who look after the forums from day to day. They have the authority to edit or delete posts and lock, unlock, move, delete and split topics in the forum they moderate. Generally, moderators are present to prevent users from going off-topic or posting abusive or offensive material.'
	),
	array(
		0 => 'What are usergroups?',
		1 => 'Usergroups are groups of users that divide the community into manageable sections board administrators can work with. Each user can belong to several groups and each group can be assigned individual permissions. This provides an easy way for administrators to change permissions for many users at once, such as changing moderator permissions or granting users access to a private forum.'
	),
	array(
		0 => 'Where are the usergroups and how do I join one?',
		1 => 'You can view all usergroups via the “Usergroups” link within your User Control Panel. If you would like to join one, proceed by clicking the appropriate button. Not all groups have open access, however. Some may require approval to join, some may be closed and some may even have hidden memberships. If the group is open, you can join it by clicking the appropriate button. If a group requires approval to join you may request to join by clicking the appropriate button. The user group leader will need to approve your request and may ask why you want to join the group. Please do not harass a group leader if they reject your request; they will have their reasons.'
	),
	array(
		0 => 'How do I become a usergroup leader?',
		1 => 'A usergroup leader is usually assigned when usergroups are initially created by a board administrator. If you are interested in creating a usergroup, your first point of contact should be an administrator; try sending a private message.',
	),
	array(
		0 => 'Why do some usergroups appear in a different colour?',
		1 => 'It is possible for the board administrator to assign a colour to the members of a usergroup to make it easy to identify the members of this group.'
	),
	array(
		0 => 'What is a “Default usergroup”?',
		1 => 'If you are a member of more than one usergroup, your default is used to determine which group colour and group rank should be shown for you by default. The board administrator may grant you permission to change your default usergroup via your User Control Panel.'
	),
	array(
		0 => 'What is “The team” link?',
		1 => 'This page provides you with a list of board staff, including board administrators and moderators and other details such as the forums they moderate.'
	),
	array(
		0 => '--',
		1 => 'Private Messaging'
	),
	array(
		0 => 'I cannot send private messages!',
		1 => 'There are three reasons for this; you are not registered and/or not logged on, the board administrator has disabled private messaging for the entire board, or the board administrator has prevented you from sending messages. Contact a board administrator for more information.'
	),
	array(
		0 => 'I keep getting unwanted private messages!',
		1 => 'You can block a user from sending you private messages by using message rules within your User Control Panel. If you are receiving abusive private messages from a particular user, inform a board administrator; they have the power to prevent a user from sending private messages.'
	),
	array(
		0 => 'I have received a spamming or abusive e-mail from someone on this board!',
		1 => 'We are sorry to hear that. The e-mail form feature of this board includes safeguards to try and track users who send such posts, so e-mail the board administrator with a full copy of the e-mail you received. It is very important that this includes the headers that contain the details of the user that sent the e-mail. The board administrator can then take action.'
	),
	array(
		0 => '--',
		1 => 'Friends and Foes'
	),
	array(
		0 => 'What are my Friends and Foes lists?',
		1 => 'You can use these lists to organise other members of the board. Members added to your friends list will be listed within your User Control Panel for quick access to see their online status and to send them private messages. Subject to template support, posts from these users may also be highlighted. If you add a user to your foes list, any posts they make will be hidden by default.'
	),
	array(
		0 => 'How can I add / remove users to my Friends or Foes list?',
		1 => 'You can add users to your list in two ways. Within each user’s profile, there is a link to add them to either your Friend or Foe list. Alternatively, from your User Control Panel, you can directly add users by entering their member name. You may also remove users from your list using the same page.'
	),
	array(
		0 => '--',
		1 => 'Searching the Forums'
	),
	array(
		0 => 'How can I search a forum or forums?',
		1 => 'Enter a search term in the search box located on the index, forum or topic pages. Advanced search can be accessed by clicking the “Advance Search” link which is available on all pages on the forum. How to access the search may depend on the style used.'
	),
	array(
		0 => 'Why does my search return no results?',
		1 => 'Your search was probably too vague and included many common terms which are not indexed by phpBB3. Be more specific and use the options available within Advanced search.'
	),
	array(
		0 => 'Why does my search return a blank page!?',
		1 => 'Your search returned too many results for the webserver to handle. Use “Advanced search” and be more specific in the terms used and forums that are to be searched.'
	),
	array(
		0 => 'How do I search for members?',
		1 => 'Visit to the “Members” page and click the “Find a member” link.'
	),
	array(
		0 => 'How can I find my own posts and topics?',
		1 => 'Your own posts can be retrieved either by clicking the “Search user’s posts” within the User Control Panel or via your own profile page. To search for your topics, use the Advanced search page and fill in the various options appropriately.'
	),
	array(
		0 => '--',
		1 => 'Topic Subscriptions and Bookmarks'
	),
	array(
		0 => 'What is the difference between bookmarking and subscribing?',
		1 => 'Bookmarking in phpBB3 is much like bookmarking in your web browser. You aren’t alerted when there’s an update, but you can come back to the topic later. Subscribing, however, will notify you when there is an update to the topic or forum on the board via your preferred method or methods.'
	),
	array(
		0 => 'How do I subscribe to specific forums or topics?',
		1 => 'To subscribe to a specific forum, click the “Subscribe forum” link upon entering the forum. To subscribe to a topic, reply to the topic with the subscribe checkbox checked or click the “Subscribe topic” link within the topic itself.'
	),
	array(
		0 => 'How do I remove my subscriptions?',
		1 => 'To remove your subscriptions, go to your User Control Panel and follow the links to your subscriptions.'
	),
	array(
		0 => '--',
		1 => 'Attachments'
	),
	array(
		0 => 'What attachments are allowed on this board?',
		1 => 'Each board administrator can allow or disallow certain attachment types. If you are unsure what is allowed to be uploaded, contact the board administrator for assistance.'
	),
	array(
		0 => 'How do I find all my attachments?',
		1 => 'To find your list of attachments that you have uploaded, go to your User Control Panel and follow the links to the attachments section.'
	),
	array(
		0 => '--',
		1 => 'Sprawy phpBB3'
	),
	array(
		0 => 'Kto napisał ten skrypt?',
		1 => 'Ten skrypt (w jego niezmodyfikowanej formie) należy do oraz jest tworzony i wydawany przez <a href="http://www.phpbb.com/">phpBB Group</a>. Jest wydawany na licencji GNU General Public License i może być wolno (w sensie wolności a nie prędkości) rozpowszechniany. Kliknij na link, aby dowiedzieć się więcej.'
	),
	array(
		0 => 'Dlaczego funkcja X nie jest dostępna?',
		1 => 'Ten skrypt został stworzony przez i należy do phpBB Group. Jeżeli uważasz, że brakuje w nim jakiejś funkcji, odwiedź stronę www.phpbb.com i sprawdź, co phpBB Group ma do powiedzenia na ten temat. Nie wysyłaj zgłoszeń funkcji na forum na phpbb.com, phpBB Group używa SourceForge do zarządzania nowymi funkcjami. Przejrzyj forum i sprawdź jakie jest, jeśli w ogóle jest, nasze stanowisko na ten temat i podążaj za wskazówkami podanymi tutaj.'
	),
	array(
		0 => 'Z kim się skontaktować w sprawie nadużyć prawnych związanych z tym forum?',
		1 => 'Powinieneś skontaktować się z jednym z administratorów wyświetlonych na liście ekipy. Jeżeli nie otrzymasz odpowiedzi, to powinieneś skontaktować się z właścicielem domeny (wykonaj <a href="http://www.google.com/search?q=whois">whois lookup</a>) lub, jeśli działa ono w darmowym serwisie (np. Yahoo!, free.fr, f2s.com, itp.), z wydziałem zarządzania lub nadużyć tego serwisu. Zauważ, że phpBB Group has <strong>absolutely no jurisdiction</strong> and cannot in any way be held liable over how, where or by whom this board is used. Do not contact the phpBB Group in relation to any legal (cease and desist, liable, defamatory comment, etc.) matter <strong>not directly related</strong> to the phpBB.com website or the discrete software of phpBB itself. If you do e-mail phpBB Group <strong>about any third party</strong> use of this software then you should expect a terse response or no response at all.'
	)
);

?>