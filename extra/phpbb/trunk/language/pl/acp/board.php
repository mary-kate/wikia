<?php
/**
*
* acp_board [Polski]
*
* @package language
* @version $Id: board.php,v 1.101.4 2008/02/21 Ronnie Exp $
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

if (empty($lang) || !is_array($lang))
{
	$lang = array();
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

// Ustawienia forum
$lang = array_merge($lang, array(
	'ACP_BOARD_SETTINGS_EXPLAIN'	=> 'Tutaj możesz ustawić podstawowe ustawienia Twojego forum, nadać mu pasującą nazwę i opis, ustawić domyślną strefę czasową, język itp.',
	'CUSTOM_DATEFORMAT'				=> 'Inna…',
	'DEFAULT_DATE_FORMAT'			=> 'Format daty',
	'DEFAULT_DATE_FORMAT_EXPLAIN'	=> 'Format daty jest taki sam, jak w funkcji PHP <code>date()</code>.',
	'DEFAULT_LANGUAGE'				=> 'Domyślny język',
	'DEFAULT_STYLE'					=> 'Domyślny styl',
	'DISABLE_BOARD'					=> 'Wyłącz forum',
	'DISABLE_BOARD_EXPLAIN'			=> 'Spowoduje niedostępność forum dla użytkowników. Jeśli chcesz, możesz też wpisać krótką (255 znaków) wiadomość do wyświetlenia.',
	'OVERRIDE_STYLE'				=> 'Nadpisz styl użytkownika',
	'OVERRIDE_STYLE_EXPLAIN'		=> 'Zamienia styl wybrany przez użytkownika na domyślny.',
	'SITE_DESC'						=> 'Opis forum',
	'SITE_NAME'						=> 'Nazwa forum',
	'SYSTEM_DST'					=> 'Włącz czas letni/<abbr title="Daylight Saving Time">DST</abbr>',
	'SYSTEM_TIMEZONE'				=> 'Strefa czasowa',
	'WARNINGS_EXPIRE'				=> 'Czas trwania ostrzeżeń',
	'WARNINGS_EXPIRE_EXPLAIN'		=> 'Liczba dni do momentu, w którym ostrzeżenie wygaśnie.',
));

// Funkcje forum
$lang = array_merge($lang, array(
	'ACP_BOARD_FEATURES_EXPLAIN'	=> 'Tutaj możesz włączyć/wyłączyć niektóre funkcje forum.',

	'ALLOW_ATTACHMENTS'			=> 'Włącz załączniki',
	'ALLOW_BIRTHDAYS'			=> 'Włącz urodziny',
	'ALLOW_BIRTHDAYS_EXPLAIN'	=> 'Pozwól na podawanie daty urodzin i wyświetlanie wieku w profilu. Zauważ, że lista urodzin na stronie głównej forum jest kontrolowana przez osobne ustawienie obciążenia serwera.',
	'ALLOW_BOOKMARKS'			=> 'Włącz zakładki',
	'ALLOW_BOOKMARKS_EXPLAIN'	=> 'Pozwól użytkownikom przechowywać własne zakładki.',
	'ALLOW_BBCODE'				=> 'Włącz BBCode',
	'ALLOW_FORUM_NOTIFY'		=> 'Włącz obserwowanie działów',
	'ALLOW_NAME_CHANGE'			=> 'Włącz możliwość zmiany loginu',
	'ALLOW_NO_CENSORS'			=> 'Włącz blokowanie cenzora słów',
	'ALLOW_NO_CENSORS_EXPLAIN'	=> 'Pozwól użytkownikom wybrać czy zablokować automatyczne cenzorowanie słów dla postów i prywatnych wiadomości.',
	'ALLOW_PM_ATTACHMENTS'		=> 'Włącz załączniki w prywatnych wiadomościach',
	'ALLOW_SIG'					=> 'Włącz podpisy',
	'ALLOW_SIG_BBCODE'			=> 'Włącz BBCode’y w podpisach użytkowników',
	'ALLOW_SIG_FLASH'			=> 'Włącz użycie BBCode’a <code>[FLASH]</code> w podpisach użytkowników',
	'ALLOW_SIG_IMG'				=> 'Włącz użycie BBCode’a <code>[IMG]</code> w podpisach użytkowników',
	'ALLOW_SIG_LINKS'			=> 'Włącz użycie linków w podpisach użytkowników',
	'ALLOW_SIG_LINKS_EXPLAIN'	=> 'Jeśli wyłączone, BBCode <code>[URL]</code> i automatyczne wykrywanie URLi zostają zablokowane.',
	'ALLOW_SIG_SMILIES'			=> 'Włącz użycie uśmieszków w podpisach użytkowników',
	'ALLOW_SMILIES'				=> 'Włącz uśmieszki',
	'ALLOW_TOPIC_NOTIFY'		=> 'Włącz obserwowanie wątków',
	'BOARD_PM'					=> 'Prywatne wiadomości',
	'BOARD_PM_EXPLAIN'			=> 'Włącz/wyłącz prywatne wiadomości dla wszystkich użytkowników.',
));

// Avatary
$lang = array_merge($lang, array(
	'ACP_AVATAR_SETTINGS_EXPLAIN'	=> 'Avatary to małe, unikalne obrazki, które użytkownicy mają prawo ustawić w swoich profilach. Zależnie od stylu, zazwyczaj są wyświetlane pod nazwą użytkownika przy wyświetlaniu wątków. Tutaj możesz określić jak użytkownicy mogą ustawiać swoje avatary. Zauważ, że aby wgrywać avatary na serwer, musi istnieć katalog o nazwie podanej niżej i serwer WWW musi mieć możliwość zapisywania go. Zauważ też, że limity wielkości plików są wymuszane tylko na wgranych avatarach.',

	'ALLOW_LOCAL'					=> 'Włącz galerię avatarów',
	'ALLOW_REMOTE'					=> 'Włącz zdalne avatary',
	'ALLOW_REMOTE_EXPLAIN'			=> 'Zdalne avatary, to avatary znajdujące się na innym serwerze.',
	'ALLOW_UPLOAD'					=> 'Włącz wgrywanie avatarów',
	'AVATAR_GALLERY_PATH'			=> 'Ścieżka do galerii avatarów',
	'AVATAR_GALLERY_PATH_EXPLAIN'	=> 'Ścieżka z głównego katalogu phpBB, np. <samp>images/avatars/gallery</samp>.',
	'AVATAR_STORAGE_PATH'			=> 'Ścieżka przechowywania avatarów',
	'AVATAR_STORAGE_PATH_EXPLAIN'	=> 'Ścieżka z głównego katalogu phpBB, np. <samp>images/avatars/upload</samp>.',
	'MAX_AVATAR_SIZE'				=> 'Maksymalne wymiary avatara',
	'MAX_AVATAR_SIZE_EXPLAIN'		=> 'Wysokość x szerokość w pikselach.',
	'MAX_FILESIZE'					=> 'Maksymalny rozmiar pliku z avatarem',
	'MAX_FILESIZE_EXPLAIN'			=> 'Tylko dla wgrywanych avatarów.',
	'MIN_AVATAR_SIZE'				=> 'Minimalne wymiary avatara',
	'MIN_AVATAR_SIZE_EXPLAIN'		=> 'Wysokość x szerokość w pikselach.',
));

// Prywatne wiadomości
$lang = array_merge($lang, array(
	'ACP_MESSAGE_SETTINGS_EXPLAIN'		=> 'Tutaj możesz zmienić ustawnienia prywatnych wiadomości.',

	'ALLOW_BBCODE_PM'			=> 'Włącz BBCode’y',
	'ALLOW_FLASH_PM'			=> 'Włącz użycie BBCode’a <code>[FLASH]</code>',
	'ALLOW_FLASH_PM_EXPLAIN'	=> 'Zauważ, że możliwość używania flasha w prywatnych wiadomościach, jeśli jest włączona, zależy też od uprawnień.',
	'ALLOW_FORWARD_PM'			=> 'Włącz przesyłanie dalej prywatnych wiadomości',
	'ALLOW_IMG_PM'				=> 'Włącz użycie BBCode’a <code>[IMG]</code>',
	'ALLOW_MASS_PM'				=> 'Włącz wysyłanie prywatnych wiadomości do wielu użytkowników i grup',
	'ALLOW_PRINT_PM'			=> 'Włącz możliwość drukowania',
	'ALLOW_QUOTE_PM'			=> 'Włącz cytowanie',
	'ALLOW_SIG_PM'				=> 'Włącz podpisy',
	'ALLOW_SMILIES_PM'			=> 'Włącz uśmieszki',
	'BOXES_LIMIT'				=> 'Maksymalna liczba prywatnych wiadomości w folderze',
	'BOXES_LIMIT_EXPLAIN'		=> 'Po przekroczeniu tego limitu do folderu nie będzie mogła trafić żadna nowa wiadomość. Ustaw na 0 dla braku limitu.',
	'BOXES_MAX'					=> 'Maksymalna liczba folderów na prywatne wiadomości',
	'BOXES_MAX_EXPLAIN'			=> 'Standardowo użytkownicy mogą tworzyć tyle folderów na prywatne wiadomości.',
	'ENABLE_PM_ICONS'			=> 'Włącz użycie ikonek',
	'FULL_FOLDER_ACTION'		=> 'Domyślna akcja podejmowana, gdy folder jest pełny',
	'FULL_FOLDER_ACTION_EXPLAIN'=> 'Podejmowana jest, gdy folder jest pełny, a akcja wybrana przez użytkownika (jeśli została wybrana) nie może zostać zastosowana. Jedyny wyjątek to folder „Wysłane”, gdzie domyślną akcją jest kasowanie starych wiadomości.',
	'HOLD_NEW_MESSAGES'			=> 'Wstrzymaj przyjmowanie nowych wiadomości',
	'PM_EDIT_TIME'				=> 'Limit czasu edytowania',
	'PM_EDIT_TIME_EXPLAIN'		=> 'Ogranicza czas, przez który można edytować niedostarczoną prywatną wiadomość. Wartość 0 spowoduje zablokowanie tej funkcji.',
));

// Posty
$lang = array_merge($lang, array(
	'ACP_POST_SETTINGS_EXPLAIN'			=> 'Tutaj możesz zmienić ustawnienia postów.',
	'ALLOW_POST_LINKS'					=> 'Włącz linki w postach i <strong>prywatnych wiadomościach</strong>',
	'ALLOW_POST_LINKS_EXPLAIN'			=> 'Jeśli wyłączone, BBCode <code>[URL]</code> i automatyczne wykrywanie URLi zostają zablokowane.',
	'ALLOW_POST_FLASH'					=> 'Włącz użycie BBCode’a <code>[FLASH]</code>',
	'ALLOW_POST_FLASH_EXPLAIN'			=> 'Jeśli wyłączone, BBCode <code>[FLASH]</code> zostanie zablokowany. W przeciwnym razie, to system uprawnień kontroluje, którzy użytkownicy mogą korzystać z BBCode’a <code>[FLASH]</code>.',

	'BUMP_INTERVAL'					=> 'Interwał przesuwania w górę',
	'BUMP_INTERVAL_EXPLAIN'			=> 'Liczba minut, godzin lub dni, między datą napisania ostatniego postu w wątku, a możliwością przesunięcia wątku w górę.',
	'CHAR_LIMIT'					=> 'Maksymalna liczba znaków w poście',
	'CHAR_LIMIT_EXPLAIN'			=> 'Ustaw na 0 dla braku limitu.',
	'DISPLAY_LAST_EDITED'			=> 'Wyświetl informację o ostatniej edycji',
	'DISPLAY_LAST_EDITED_EXPLAIN'	=> 'Wybierz, czy informacja o tym, kto ostatnio edytował post i kiedy to zrobił, ma być wyświetlana w postach.',
	'EDIT_TIME'						=> 'Limit czasu edycji',
	'EDIT_TIME_EXPLAIN'				=> 'Ogranicza czas, przez który można przeedytować nowy post. Wartość 0 spowoduje zablokowanie tej funkcji.',
	'FLOOD_INTERVAL'				=> 'Interwał floodowania',
	'FLOOD_INTERVAL_EXPLAIN'		=> 'Liczba sekund, jaką użytkownik musi odczekać między wysyłaniem nowych wiadomości. Aby pozwolić użytkownikom ignorować to ustawienie, zmodyfikuj ich uprawnienia.',
	'HOT_THRESHOLD'					=> 'Oznaczenie popularnych wątków',
	'HOT_THRESHOLD_EXPLAIN'			=> 'Liczba postów w wątku wymagana do oznaczenia go jako popularny. Ustaw na 0, aby zablokować popularne wątki.',
	'MAX_POLL_OPTIONS'				=> 'Maksymalna liczba opcji ankiety',
	'MAX_POST_FONT_SIZE'			=> 'Maksymalny rozmiar czcionki',
	'MAX_POST_FONT_SIZE_EXPLAIN'	=> 'Ustaw na 0 dla braku limitu.',
	'MAX_POST_IMG_HEIGHT'			=> 'Maksymalna wysokość obrazka',
	'MAX_POST_IMG_HEIGHT_EXPLAIN'	=> 'Ustaw na 0 dla braku limitu.',
	'MAX_POST_IMG_WIDTH'			=> 'Maksymalna szerokość obrazka',
	'MAX_POST_IMG_WIDTH_EXPLAIN'	=> 'Ustaw na 0 dla braku limitu.',
	'MAX_POST_URLS'					=> 'Maksymalna liczba linków',
	'MAX_POST_URLS_EXPLAIN'			=> 'Ustaw na 0 dla braku limitu.',
	'POSTING'						=> 'Posty',
	'POSTS_PER_PAGE'				=> 'Postów na stronę',
	'QUOTE_DEPTH_LIMIT'				=> 'Maksymalna liczba zagnieżdżonych cytatów',
	'QUOTE_DEPTH_LIMIT_EXPLAIN'		=> 'Ustaw na 0 dla braku limitu.',
	'SMILIES_LIMIT'					=> 'Maksymalna liczba uśmieszków',
	'SMILIES_LIMIT_EXPLAIN'			=> 'Ustaw na 0 dla braku limitu.',
	'TOPICS_PER_PAGE'				=> 'Wątków na stronę',
));

// Podpisy
$lang = array_merge($lang, array(
	'ACP_SIGNATURE_SETTINGS_EXPLAIN'	=> 'Tutaj możesz zmienić ustawnienia podpisów.',

	'MAX_SIG_FONT_SIZE'				=> 'Maksymalny rozmiar czcionki',
	'MAX_SIG_FONT_SIZE_EXPLAIN'		=> 'Ustaw na 0 dla braku limitu.',
	'MAX_SIG_IMG_HEIGHT'			=> 'Maksymalna wysokość obrazka',
	'MAX_SIG_IMG_HEIGHT_EXPLAIN'	=> 'Ustaw na 0 dla braku limitu.',
	'MAX_SIG_IMG_WIDTH'				=> 'Maksymala szerokość obrazka',
	'MAX_SIG_IMG_WIDTH_EXPLAIN'		=> 'Ustaw na 0 dla braku limitu.',
	'MAX_SIG_LENGTH'				=> 'Maksymalna liczba znaków',
	'MAX_SIG_LENGTH_EXPLAIN'		=> 'Ustaw na 0 dla braku limitu.',
	'MAX_SIG_SMILIES'				=> 'Maksymalna liczba uśmieszków',
	'MAX_SIG_SMILIES_EXPLAIN'		=> 'Ustaw na 0 dla braku limitu.',
	'MAX_SIG_URLS'					=> 'Maksymalna liczba linków',
	'MAX_SIG_URLS_EXPLAIN'			=> 'Ustaw na 0 dla braku limitu.',
));

// Rejestracja
$lang = array_merge($lang, array(
	'ACP_REGISTER_SETTINGS_EXPLAIN'		=> 'Tutaj możesz zmienić ustawnienia rejestracji.',

	'ACC_ACTIVATION'			=> 'Aktywacja konta',
	'ACC_ACTIVATION_EXPLAIN'	=> 'Określa, czy użytkownicy będą mieli natychmiastowy dostęp do forum, czy wymagane jest potwierdzenie. Możesz też kompletnie zablokować rejestracje.',
	'ACC_ADMIN'					=> 'Przez admina',
	'ACC_DISABLE'				=> 'Zablokuj',
	'ACC_NONE'					=> 'Brak',
	'ACC_USER'					=> 'Przez usera',
//	'ACC_USER_ADMIN'			=> 'Użytkownik + Admin',
	'ALLOW_EMAIL_REUSE'			=> 'Pozwól na ponowne użycie adresu e-mail',
	'ALLOW_EMAIL_REUSE_EXPLAIN'	=> 'Różni użytkownicy będą mogli zarejestrować się posiadając ten sam adres e-mail.',
	'COPPA'						=> 'COPPA',
	'COPPA_FAX'					=> 'Numer faxu COPPA',
	'COPPA_MAIL'				=> 'Adres pocztowy COPPA',
	'COPPA_MAIL_EXPLAIN'		=> 'Jest to adres, pod który rodzice wyślą formularze rejestracji COPPA.',
	'ENABLE_COPPA'				=> 'Włącz COPPA',
	'ENABLE_COPPA_EXPLAIN'		=> 'Wymaga od użytkowników deklaracji, czy mają 13 lub więcej lat dla zgodności z U.S. COPPA. W Polsce to prawo nie obowiązuje. W przypadku wyłączenia tej opcji, grupa Zarejestrowani użytkownicy (COPPA) nie będzie wyświetlana.',
	'MAX_CHARS'					=> 'Maks',
	'MIN_CHARS'					=> 'Min',
	'MIN_TIME_REG'				=> 'Minimalny czas rejestracji',
	'MIN_TIME_REG_EXPLAIN'		=> 'Formularz nie może zostać wysłany przed upłynięciem tego czasu.',
	'MIN_TIME_TERMS'			=> 'Minimalny czas na akceptację warunków rejestracji',
	'MIN_TIME_TERMS_EXPLAIN'	=> 'Strona z warunkami rejestracji nie może zostać opuszczona przed upłynięciem tego czasu.',
	'NO_AUTH_PLUGIN'			=> 'Nie znaleziono odpowiedniego pluginu uwierzytelniania.',
	'PASSWORD_LENGTH'			=> 'Długość hasła',
	'PASSWORD_LENGTH_EXPLAIN'	=> 'Minimalna i maksymalna liczba znaków w haśle.',
	'REG_LIMIT'					=> 'Próby rejestracji',
	'REG_LIMIT_EXPLAIN'			=> 'Limit prób wpisania kodu potwierdzającego przy rejestracji po przekroczeniu którego możliwość rejestracji jest blokowana dla aktualnej sesji.',
	'USERNAME_ALPHA_ONLY'		=> 'Litery alfabetu łacińskiego i cyfry',
	'USERNAME_ALPHA_SPACERS'	=> 'Litery alfabetu łacińskiego, cyfry, odstępy',
	'USERNAME_ASCII'			=> 'Znaki ASCII (bez znaków unicode)',
	'USERNAME_LETTER_NUM'		=> 'Litery i cyfry',
	'USERNAME_LETTER_NUM_SPACERS'	=> 'Litery, cyfry, odstępy',
	'USERNAME_CHARS'			=> 'Dozwolone znaki nazwy użytkownika',
	'USERNAME_CHARS_ANY'		=> 'Dowolne znaki',
	'USERNAME_CHARS_EXPLAIN'	=> 'Ogranicz typy znaków możliwych do użycia w nazwach użytkowników. Odstępy to: spacja, „_”, „+”, „-”, „[” i „]”.',
	'USERNAME_LENGTH'			=> 'Długość nazwy użytkownika',
	'USERNAME_LENGTH_EXPLAIN'	=> 'Minimalna i maksymalna liczba znaków w nazwie użytkownika.',
));

// Potwierdzenie wizualne
$lang = array_merge($lang, array(
	'ACP_VC_SETTINGS_EXPLAIN'		=> 'Tutaj możesz zmienić ustawnienia potwierdzenia wizualnego i CAPTCHA’y.',

	'CAPTCHA_GD'							=> 'CAPTCHA GD',
	'CAPTCHA_GD_FOREGROUND_NOISE'			=> 'Szum pierwszoplanowy CAPTCHA’y GD',
	'CAPTCHA_GD_EXPLAIN'					=> 'Użyj GD, aby stworzyć bardziej zaawansowaną CAPTCHA’ę.',
	'CAPTCHA_GD_FOREGROUND_NOISE_EXPLAIN'	=> 'Użyj szumu pierwszoplanowego, aby utrudnić zrozumienie CAPTCHA’y bazującej na GD.',
	'CAPTCHA_GD_X_GRID'						=> 'Szum drugoplanowy CAPTCHA’y GD na osi x',
	'CAPTCHA_GD_X_GRID_EXPLAIN'				=> 'Im niższa liczba, tym trudniej zrozumieć CAPTCHA’ę. 0 zablokuje szum drugoplanowy CAPTCHA’y GD na osi x.',
	'CAPTCHA_GD_Y_GRID'						=> 'Szum drugoplanowy CAPTCHA’y GD na osi y',
	'CAPTCHA_GD_Y_GRID_EXPLAIN'				=> 'Im niższa liczba, tym trudniej zrozumieć CAPTCHA’ę. 0 zablokuje szum drugoplanowy CAPTCHA’y GD na osi y.',

	'CAPTCHA_PREVIEW_MSG'					=> 'Twoje zmiany w potwierdzeniu wizualnym nie zostały zapisane. To jest tylko podgląd.',
	'CAPTCHA_PREVIEW_EXPLAIN'				=> 'Tak będzie wyglądać CAPTCHA używająca aktualnych ustawień. Użyj przycisku „Podgląd”, aby odświeżyć obrazek. Zauważ, że potwierdzenia wizualne są losowane i będą się różnić między jednym wyświetleniem a następnym.',
	'VISUAL_CONFIRM_POST'					=> 'Włącz potwierdzenie wizualne dla gości przy pisaniu',
	'VISUAL_CONFIRM_POST_EXPLAIN'			=> 'Wymaga od gości podania losowego kodu z obrazka, aby zablokować masowe spamowanie.',
	'VISUAL_CONFIRM_REG'					=> 'Włącz potwierdzenie wizualne przy rejestracji',
	'VISUAL_CONFIRM_REG_EXPLAIN'			=> 'Wymaga od nowych użytkowników podania losowego kodu z obrazka, aby zablokować masowe rejestracje.',
));

// Ciasteczka
$lang = array_merge($lang, array(
	'ACP_COOKIE_SETTINGS_EXPLAIN'		=> 'Tutaj możesz zmienić dane używane do wysyłania ciasteczek do przeglądarek użytkowników. W większości przypadków domyślne wartości powinny być odpowiednie. Jeśli musisz coś zmienić, rób to uważnie, ponieważ nieprawidłowe ustawienia mogą uniemożliwić logowanie użytkownikom.',

	'COOKIE_DOMAIN'				=> 'Domena ciasteczka',
	'COOKIE_NAME'				=> 'Nazwa ciasteczka',
	'COOKIE_PATH'				=> 'Ścieżka ciasteczka',
	'COOKIE_SECURE'				=> 'Bezpieczne ciasteczka',
	'COOKIE_SECURE_EXPLAIN'		=> 'Jeśli Twój serwer korzysta z SSL, włącz tę opcję, jeśli nie - pozostaw ją wyłączoną. Włączenie jej i nie korzystanie z SSL spowoduje błędy serwera w czasie przekierowań.',
	'ONLINE_LENGTH'				=> 'Czas wyświetlania użytkowników na liście przeglądających forum',
	'ONLINE_LENGTH_EXPLAIN'		=> 'Liczba minut, po jakiej nieaktywni użytkownicy nie będą wyświetlani na liście „Kto przegląda forum”. Im wyższa wartość, tym więcej pracy serwer musi wykonać, aby wyświetlić listę.',
	'SESSION_LENGTH'			=> 'Długość sesji',
	'SESSION_LENGTH_EXPLAIN'	=> 'Sesje wygasną po upływie tego czasu, w sekundach.',
));

// Obciążenie serwera
$lang = array_merge($lang, array(
	'ACP_LOAD_SETTINGS_EXPLAIN'	=> 'Tutaj możesz włączyć i wyłączyć różne funkcje serwera aby zmniejszyć ilość pracy jaką musi wykonać. Na większości serwerów nie ma potrzeby blokować żadnych funkcji. Jednak na niektórych systemach albo na hostingach współdzielonych zablokowanie funkcji które nie są niezbędne może być przydatne. Możesz też określić limity obciążenia serwera i aktywnych sesji po przekroczeniu których forum zostanie wyłączone.',

	'CUSTOM_PROFILE_FIELDS'			=> 'Własne pola profilu',
	'LIMIT_LOAD'					=> 'Limit obciążenia systemu',
	'LIMIT_LOAD_EXPLAIN'			=> 'Jeśli średnie minutowe obciążenie systemu przekroczy tę wartość, forum automatycznie zostanie wyłączone. Wartość 1.0 oznacza około stuprocentowe wykorzystanie jednego procesora. To ustawienie działa tylko na serwerach bazujących na UNIXie i gdzie ta informacja jest dostępna. Wartość zostaje zresetowana na 0, jeśli phpBB nie może zdobyć informacji o obciążeniu systemu.',
	'LIMIT_SESSIONS'				=> 'Limit sesji',
	'LIMIT_SESSIONS_EXPLAIN'		=> 'Jeśli liczba sesji przekroczy tę wartość w czasie jednej minuty, forum automatycznie zostanie wyłączone. Ustaw na 0 dla braku limitu.',
	'LOAD_CPF_MEMBERLIST'			=> 'Pozwól stylom wyświetlać własne pola profilu na liście użytkowników',
	'LOAD_CPF_VIEWPROFILE'			=> 'Wyświetl własne pola profilu w profilach użytkowników',
	'LOAD_CPF_VIEWTOPIC'			=> 'Wyświetl własne pola profilu w wątkach',
	'LOAD_USER_ACTIVITY'			=> 'Wyświetlaj aktywność użytkownika',
	'LOAD_USER_ACTIVITY_EXPLAIN'	=> 'Wyświetla informację o wątku/dziale, w którym użytkownik napisał najwięcej postów w profilu i Panelu Zarządzania Kontem. Zalecane jest wyłączenie tej funkcji na forach mających ponad milion postów.',
	'RECOMPILE_STYLES'				=> 'Ponowna kompilacja nieaktualnych elementów stylów',
	'RECOMPILE_STYLES_EXPLAIN'		=> 'Sprawdź czy nie zmodyfikowano używanych komponentów stylu w systemie plików i ponownie skompiluj je, jeśli zostały zmienione.',
	'YES_ANON_READ_MARKING'			=> 'Włącz oznaczanie wątków dla gości',
	'YES_ANON_READ_MARKING_EXPLAIN'	=> 'Zapisuje informację, czy wątki są przeczytane, czy nie, przez gości. Jeśli wyłączone, wątki są zawsze wyświetlane jako nieprzeczytane.',
	'YES_BIRTHDAYS'					=> 'Włącz listę urodzin',
	'YES_BIRTHDAYS_EXPLAIN'			=> 'Aby ta funkcja działała musisz włączyć funkcję urodzin.',
	'YES_JUMPBOX'					=> 'Włącz wyświetlanie listy „Skocz do”',
	'YES_MODERATORS'				=> 'Włącz wyświetlanie moderatorów',
	'YES_ONLINE'					=> 'Włącz wyświetlanie listy przeglądających forum',
	'YES_ONLINE_EXPLAIN'			=> 'Wyświetla listę przeglądających forum na stronie głównej oraz listę przeglądających dział na stronach działów i wątków.',
	'YES_ONLINE_GUESTS'				=> 'Włącz wyświetlanie gości na szczegółowej liście przeglądających forum',
	'YES_ONLINE_GUESTS_EXPLAIN'		=> '',
	'YES_ONLINE_TRACK'				=> 'Włącz wyświetlanie informacji online/offline',
	'YES_ONLINE_TRACK_EXPLAIN'		=> 'Wyświetla informację czy dany użytkownik przegląda teraz forum w jego profilu i na stronie wątku.',
	'YES_POST_MARKING'				=> 'Włącz oznaczanie wątków',
	'YES_POST_MARKING_EXPLAIN'		=> 'Informuje czy użytkownik pisał w danym wątku.',
	'YES_READ_MARKING'				=> 'Włącz zapisywanie informacji o (nie)przeczytaniu tematu po stronie serwera',
	'YES_READ_MARKING_EXPLAIN'		=> 'Zapisuje informację, czy wątki są przeczytane, czy nie, w bazie danych forum, zamiast w ciasteczku.',
));

// Uwierzytelnianie
$lang = array_merge($lang, array(
	'ACP_AUTH_SETTINGS_EXPLAIN'	=> 'phpBB korzysta z modułów uwierzytelniania. Mają one za zadanie uwierzytelniać użytkowników w czasie logowania. phpBB zawiera standardowo trzy moduły: DB, LDAP i Apache. Możesz wybrać jeden z nich. Nie wszystkie moduły potrzebują dodatkowych informacji, więc wypełnij tylko pola odpowiednie dla modułu, który wybrałeś.',

	'AUTH_METHOD'				=> 'Wybierz moduł uwierzytelniania',

	'APACHE_SETUP_BEFORE_USE'	=> 'Musisz skonfigurować uwierzytelnianie Apache, zanim przestawisz na nie phpBB. Pamiętaj, że login używany przez Ciebie przy uwierzytelnianiu Apache musi być taki sam, jak Twoja nazwa użytkownika phpBB. Uwierzytelnianie Apache może być używane tylko wtedy, gdy mod_php (nie w wersji CGI) i safe_mode są wyłączone.',
// TODO: Przetłumaczyć gdy dowiem się co to jest LDAP :P
	'LDAP_DN'						=> 'LDAP base <var>dn</var>',
	'LDAP_DN_EXPLAIN'				=> 'This is the Distinguished Name, locating the user information, e.g. <samp>o=My Company,c=US</samp>.',
	'LDAP_EMAIL'					=> 'LDAP e-mail attribute',
	'LDAP_EMAIL_EXPLAIN'			=> 'Set this to the name of your user entry e-mail attribute (if one exists) in order to automatically set the e-mail address for new users. Leaving this empty results in empty e-mail address for users who log in for the first time.',
	'LDAP_INCORRECT_USER_PASSWORD'	=> 'Binding to LDAP server failed with specified user/password.',
	'LDAP_NO_EMAIL'					=> 'The specified e-mail attribute does not exist.',
	'LDAP_NO_IDENTITY'				=> 'Could not find a login identity for %s.',
	'LDAP_PASSWORD'					=> 'LDAP password',
	'LDAP_PASSWORD_EXPLAIN'			=> 'Leave blank to use anonymous binding. Else fill in the password for the above user.  Required for Active Directory Servers. <strong>WARNING:</strong> This password will be stored as plain text in the database visible to everybody who can access your database or who can view this configuration page.',
	'LDAP_PORT'						=> 'LDAP server port',
	'LDAP_PORT_EXPLAIN'				=> 'Optionally you can specify a port which should be used to connect to the LDAP server instead of the default port 389.',
	'LDAP_SERVER'					=> 'LDAP server name',
	'LDAP_SERVER_EXPLAIN'			=> 'If using LDAP this is the hostname or IP address of the LDAP server. Alternatively you can specify an URL like ldap://hostname:port/',
	'LDAP_UID'						=> 'LDAP <var>uid</var>',
	'LDAP_UID_EXPLAIN'				=> 'This is the key under which to search for a given login identity, e.g. <var>uid</var>, <var>sn</var>, etc.',
	'LDAP_USER'						=> 'LDAP user <var>dn</var>',
	'LDAP_USER_EXPLAIN'				=> 'Leave blank to use anonymous binding. If filled in phpBB uses the specified distinguished name on login attempts to find the correct user, e.g. <samp>uid=Username,ou=MyUnit,o=MyCompany,c=US</samp>. Required for Active Directory Servers.',
	'LDAP_USER_FILTER'				=> 'LDAP user filter',
	'LDAP_USER_FILTER_EXPLAIN'		=> 'Optionally you can further limit the searched objects with additional filters. For example <samp>objectClass=posixGroup</samp> would result in the use of <samp>(&(uid=$username)(objectClass=posixGroup))</samp>',
));

// Serwer
$lang = array_merge($lang, array(
	'ACP_SERVER_SETTINGS_EXPLAIN'	=> 'Tutaj możesz zmienić ustawienia zależne od serwera i domeny. Upewnij się, że wpisujesz prawdłowe dane, błędy spowodują wysyłanie e-maili z nieprawidłowymi informacjami.',

	'ENABLE_GZIP'				=> 'Włącz kompresję GZip',
	'ENABLE_GZIP_EXPLAIN'		=> 'Wygenerowana zawartość zostanie skompresowana przed wysłaniem jej do użytkownika. W ten sposób można zmniejszyć transfer ale i powiększyć użycie CPU na serwerze i po stronie użytkownika.',
	'FORCE_SERVER_VARS'			=> 'Wymuś ustawienia URLi serwera',
	'FORCE_SERVER_VARS_EXPLAIN'	=> 'Jeśli włączone, ustawienia podane tutaj zostaną użyte zamiast określonych automatycznie.',
	'ICONS_PATH'				=> 'Ścieżka do ikon postów',
	'ICONS_PATH_EXPLAIN'		=> 'Ścieżka z głównego katalogu phpBB, np. <samp>images/icons</samp>.',
	'PATH_SETTINGS'				=> 'Ścieżki',
	'RANKS_PATH'				=> 'Ścieżka do obrazków rang',
	'RANKS_PATH_EXPLAIN'		=> 'Ścieżka z głównego katalogu phpBB, np. <samp>images/ranks</samp>.',
	'SCRIPT_PATH'				=> 'Ścieżka skryptu',
	'SCRIPT_PATH_EXPLAIN'		=> 'Ścieżka z głównego katalogu domeny, np. <samp>/phpBB3</samp>.',
	'SERVER_NAME'				=> 'Domena',
	'SERVER_NAME_EXPLAIN'		=> 'Nazwa domeny, pod którą to forum funkcjonuje (np. <samp>www.example.com</samp>).',
	'SERVER_PORT'				=> 'Port serwera',
	'SERVER_PORT_EXPLAIN'		=> 'Port serwera, na którym działa Twoje forum, standardowo 80, zmień tylko, jeśli inny.',
	'SERVER_PROTOCOL'			=> 'Protokół serwera',
	'SERVER_PROTOCOL_EXPLAIN'	=> 'Używany jako protokół serwera, gdy te ustawienia są wymuszone. Jeśli pusty albo niewymuszany, protokół jest określany na podstawie ustawień ciasteczek (<samp>http://</samp> lub <samp>https://</samp>).',
	'SERVER_URL_SETTINGS'		=> 'Ustawienia URLi serwera',
	'SMILIES_PATH'				=> 'Ścieżka do uśmieszków',
	'SMILIES_PATH_EXPLAIN'		=> 'Ścieżka z głównego katalogu phpBB, np. <samp>images/smilies</samp>.',
	'UPLOAD_ICONS_PATH'			=> 'Ścieżka do ikon grup rozszerzeń',
	'UPLOAD_ICONS_PATH_EXPLAIN'	=> 'Ścieżka z głównego katalogu phpBB, np. <samp>images/upload_icons</samp>.',
));

// Zabezpieczenia
$lang = array_merge($lang, array(
	'ACP_SECURITY_SETTINGS_EXPLAIN'		=> 'Tutaj możesz zmienić ustawienia związane z sesjami i logowaniem.',

	'ALL'							=> 'Całość',
	'ALLOW_AUTOLOGIN'				=> 'Włącz automatyczne logowanie',
	'ALLOW_AUTOLOGIN_EXPLAIN'		=> 'Pozwala użytkownikom na włączenie automatycznego logowania przy każdym następnym odwiedzeniu forum.',
	'AUTOLOGIN_LENGTH'				=> 'Czas wygasania klucza automatycznego logowania (w dniach)',
	'AUTOLOGIN_LENGTH_EXPLAIN'		=> 'Liczba dni, po której klucze automatycznego logowania są usuwane. Ustaw na 0, aby nigdy nie usuwać kluczy.',
	'BROWSER_VALID'					=> 'Sprawdź przeglądarkę',
	'BROWSER_VALID_EXPLAIN'			=> 'Porównuje przeglądarkę użytkownika z przeglądarką sesji, aby zwiększyć bezpieczeństwo.',
	'CHECK_DNSBL'					=> 'Sprawdź IP na DNS Blackhole List',
	'CHECK_DNSBL_EXPLAIN'			=> 'Jeśli włączone, adres IP użytkownika jest sprawdzany w poniższych serwisach DNSBL przy rejestracji i pisaniu: <a href="http://spamcop.net">spamcop.net</a>, <a href="http://dsbl.org">dsbl.org</a> i <a href="http://www.spamhaus.org">www.spamhaus.org</a>. Sprawdzanie może zająć pewien czas, zależnie od konfiguracji serwera. Jeśli forum zwalnia lub jest zgłaszane zbyt dużo fałszywych pozytywów, zalecane jest wyłączenie tej funkcji.',
	'CLASS_B'						=> 'A.B',
	'CLASS_C'						=> 'A.B.C',
	'EMAIL_CHECK_MX'				=> 'Sprawdź czy domena e-maila posiada prawidłowy rekord MX',
	'EMAIL_CHECK_MX_EXPLAIN'		=> 'Jeśli włączone, skrypt sprawdza przy rejestracji czy domena podanego adresu e-mail posiada prawidłowy rekord MX.',
	'FORCE_PASS_CHANGE'				=> 'Wymagaj zmiany hasła',
	'FORCE_PASS_CHANGE_EXPLAIN'		=> 'Wymagaj od użytkowników zmiany hasła po wybranej liczbie dni. Ustaw na 0, aby zablokować tę funkcję.',
	'FORM_TIME_MAX'					=> 'Maksymalny czas na wysłanie formularza',
	'FORM_TIME_MAX_EXPLAIN'			=> 'Czas, jaki ma użytkownik na wysłanie formularza. Ustaw na -1, aby zablokować tę funkcję. Pamiętaj, że formularz może stać się nieprawidłowy, jeśli sesja wygaśnie, niezależnie od tego ustawienia.',
	'FORM_TIME_MIN'					=> 'Minimalny czas na wysłanie formularza',
	'FORM_TIME_MIN_EXPLAIN'			=> 'Formularze wysłane szybciej zostaną zignorowane przez forum. Ustaw na 0, aby zablokować tę funkcję.',
	'FORM_SID_GUESTS'				=> 'Powiąż formularze z sesjami gości',
	'FORM_SID_GUESTS_EXPLAIN'		=> 'Jeśli włączone, tokeny formularzy gości będą inne dla każdej sesji. Może to spowodować problemy z niektórymi dowstawcami internetu.',
	'FORWARDED_FOR_VALID'			=> 'Sprawdź nagłówek <var>X_FORWARDED_FOR</var>',
	'FORWARDED_FOR_VALID_EXPLAIN'	=> 'Porównuje nagłówek <var>X_FORWARDED_FOR</var> użytkownika z tym samym nagłówkiem sesji. Oprócz tego, powoduje wyszukiwanie zbanowanych adresów IP w tym nagłówku.',
	'IP_VALID'						=> 'Sprawdzanie IP',
	'IP_VALID_EXPLAIN'				=> 'Określa jaka część adresu IP użytkownika jest porównywana z adresem IP sesji. <samp>Całość</samp> porównuje cały adres, <samp>A.B.C</samp> pierwsze x.x.x, <samp>A.B</samp> pierwsze x.x, <samp>Nic</samp> blokuje sprawdzanie. Przy adresach IPv6 <samp>A.B.C</samp> porównuje pierwsze 4 bloku, a <samp>A.B</samp> - pierwsze 3 bloki.',
	'MAX_LOGIN_ATTEMPTS'			=> 'Maksymalna liczba prób logowania',
	'MAX_LOGIN_ATTEMPTS_EXPLAIN'	=> 'Po przekroczeniu tej liczby użytkownik musi dodatkowo potwierdzić swoje logowanie wizualnie (potwierdzenie wizualne).',
	'NO_IP_VALIDATION'				=> 'Nic',
	'PASSWORD_TYPE'					=> 'Złożoność hasła',
	'PASSWORD_TYPE_EXPLAIN'			=> 'Określa jak złożone musi być ustawiane hasło. Kolejne opcje zawierają w sobie poprzednie.',
	'PASS_TYPE_ALPHA'				=> 'Musi zawierać litery i liczby',
	'PASS_TYPE_ANY'					=> 'Brak wymagań',
	'PASS_TYPE_CASE'				=> 'Musi zawierać małe i duże litery',
	'PASS_TYPE_SYMBOL'				=> 'Musi zawierać symbole',
	'TPL_ALLOW_PHP'					=> 'Zezwól na stosowanie PHP w szablonach',
	'TPL_ALLOW_PHP_EXPLAIN'			=> 'Jeśli opcja ta jest włączona, tagi <code><!-- PHP --></code> i <code><!-- INCLUDEPHP --></code> w szablonach zostaną rozpoznane i przeparsowane w szablonach.',
));

// E-maile
$lang = array_merge($lang, array(
	'ACP_EMAIL_SETTINGS_EXPLAIN'	=> 'Informacje te są używane, gdy forum wysyła e-maile do użytkowników. Upewnij się, że adres -email, który podajesz, jest prawidłowy, ponieważ wszystkie odbite i niedostarczone wiadomości zostaną wysłane na ten adres. Jeśli Twój host nie posiada natywnego (bazującego na PHP) serwisu e-mail, możesz wysyłać wiadomości poprzez SMTP. Wymaga to adresu odpowiedniego serwera. Jeśli serwer wymaga uwierzytelniania (i tylko jeśli wymaga), podaj odpowiedni login, hasło i metodę uwierzytelniania.',

	'ADMIN_EMAIL'					=> 'Zwrotny adres e-mail',
	'ADMIN_EMAIL_EXPLAIN'			=> 'Ten adres zostanie użyty jako zwrotny adres e-mail we wszystkich e-mailach oraz adres kontaktowy w sprawach technicznych. Będzie podany w nagłówkach e-maili jako adres zwrotny i adres nadawcy (nagłówki <samp>Return-Path</samp> i <samp>Sender</samp>).',
	'BOARD_EMAIL_FORM'				=> 'Użytkownicy mogą wysyłać e-maile poprzez forum',
	'BOARD_EMAIL_FORM_EXPLAIN'		=> 'Zamiast wyświetlania użytkownikom adresów e-mail, forum umożliwia im wysyłanie e-maili poprzez formularz.',
	'BOARD_HIDE_EMAILS'				=> 'Ukrywaj adresy e-mail',
	'BOARD_HIDE_EMAILS_EXPLAIN'		=> 'Funkcja ta powoduje, że adresy e-mail są całowcie prywatne.',
	'CONTACT_EMAIL'					=> 'Kontaktowy adres e-mail',
	'CONTACT_EMAIL_EXPLAIN'			=> 'Ten adres zostanie użyty, jeżeli zainstnieje potrzeba kontaktu, np. w sprawie spamu, błędów itp. Będzie on widoczny w polach <samp>From</samp> i <samp>Reply-To</samp> e-maila.',
	'EMAIL_FUNCTION_NAME'			=> 'Nazwa funkcji wysyłającej e-maile',
	'EMAIL_FUNCTION_NAME_EXPLAIN'	=> 'Nazwa funkcji używanej do wysyłania e-maili poprzez PHP.',
	'EMAIL_PACKAGE_SIZE'			=> 'Rozmiar paczki e-maili',
	'EMAIL_PACKAGE_SIZE_EXPLAIN'	=> 'Maksymalna liczba e-maili wysyłanych w jednej paczce. Ustawienie to jest stosowane w wewnętrznej kolejce e-maili. Ustaw na 0, jeśli masz problemy z niedoręczonymi e-mailami.',
	'EMAIL_SIG'						=> 'Podpis w e-mailach',
	'EMAIL_SIG_EXPLAIN'				=> 'Ten tekst zostanie dołączony do wszystkich e-maili wysyłanych przez forum.',
	'ENABLE_EMAIL'					=> 'Włącz e-maile',
	'ENABLE_EMAIL_EXPLAIN'			=> 'Jeśli ta opcja zostanie wyłączona, przez forum nie będą wysyłane żadne e-maile.',
	'SMTP_AUTH_METHOD'				=> 'Metoda uwierzytelniania SMTP',
	'SMTP_AUTH_METHOD_EXPLAIN'		=> 'Używane tylko wtedy, gdy login i hasło są ustawione, zapytaj właściciela serwera SMTP jeśli nie wiesz której metody użyć.',
	'SMTP_CRAM_MD5'					=> 'CRAM-MD5',
	'SMTP_DIGEST_MD5'				=> 'DIGEST-MD5',
	'SMTP_LOGIN'					=> 'LOGIN',
	'SMTP_PASSWORD'					=> 'Hasło SMTP',
	'SMTP_PASSWORD_EXPLAIN'			=> 'Musisz wpisać hasło tylko wtedy, jeśli Twój serwer SMTP go wymaga.',
	'SMTP_PLAIN'					=> 'Bez uwierzytelniania',
	'SMTP_POP_BEFORE_SMTP'			=> 'POP-PRZED-SMTP',
	'SMTP_PORT'						=> 'Port serwera SMTP',
	'SMTP_PORT_EXPLAIN'				=> 'Zmień tylko wtedy, jeśli wiesz, że serwer SMTP działa na innym porcie.',
	'SMTP_SERVER'					=> 'Adres serwera SMTP',
	'SMTP_SETTINGS'					=> 'Ustawienia SMTP',
	'SMTP_USERNAME'					=> 'Login SMTP',
	'SMTP_USERNAME_EXPLAIN'			=> 'Musisz wpisać login tylko wtedy, jeśli Twój serwer SMTP go wymaga.',
	'USE_SMTP'						=> 'Użyj serwera SMTP dla wysyłania e-maili',
	'USE_SMTP_EXPLAIN'				=> 'Wybierz „Tak”, jeśli chcesz lub musisz wysyłać e-maile przez wybrany serwer SMTP zamiast przez lokalną funkcję mail.',
));

// Jabber
$lang = array_merge($lang, array(
	'ACP_JABBER_SETTINGS_EXPLAIN'	=> 'Tutaj możesz włączyć i skonfigurować użycie Jabbera do wysyłania wiadomości błyskawicznych i powiadomień z forum. Jabber/XMPP to otwarty protokół i przez to może go używać każdy. Niektóre serwery Jabber posiadają bramy i transporty pozwalające kontaktować się z innymi sieciami. Pamiętaj, żeby podać szczegóły konta już zarejetrowanego - phpBB po prostu użyje dnaych takich, jakie tu są.',

	'JAB_ENABLE'				=> 'Włącz Jabbera',
	'JAB_ENABLE_EXPLAIN'		=> 'Umożliwia wysyłanie wiadomości i powiadomień poprzez Jabbera.',
	'JAB_GTALK_NOTE'			=> 'GTalk nie będzie działał, ponieważ funkcja <samp>dns_get_record</samp> nie została znaleziona. Nie jest ona dostępna w PHP4 i nie została zaimplementowana na platformie Windows. Obecnie nie działa na systemach bazujących na BSD, w tym na Mac OSie.',
	'JAB_PACKAGE_SIZE'			=> 'Rozmiar paczki wiadomości Jabber',
	'JAB_PACKAGE_SIZE_EXPLAIN'	=> 'Maksymalna liczba wiadomości Jabber wysyłanych w jednej paczce. Ustaw na 0, aby wysyłać wiadomości natychmiast i nie kolejkować ich do późniejszego wysłania.',
	'JAB_PASSWORD'				=> 'Hasło konta Jabber',
	'JAB_PORT'					=> 'Port serwera Jabber',
	'JAB_PORT_EXPLAIN'			=> 'Pozostaw pusty, chyba, że wiesz, że nie jest to port 5222.',
	'JAB_SERVER'				=> 'Serwer Jabbera',
	'JAB_SERVER_EXPLAIN'		=> 'Na stronie %sjabber.org%s znaduje się lista serwerów.',
	'JAB_SETTINGS_CHANGED'		=> 'Ustawienia Jabbera zostały zmienione.',
	'JAB_USE_SSL'				=> 'Użyj SSL przy łączeniu',
	'JAB_USE_SSL_EXPLAIN'		=> 'Jeśli włączone, phpBB próbuje nawiązać bezpieczne połączenie. W takim przypadku, zamiast portu 5222, jest używany port 5223.',
	'JAB_USERNAME'				=> 'Login konta Jabber',
	'JAB_USERNAME_EXPLAIN'		=> 'Wybierz zarejestrowany login. Jego poprawność nie zostanie sprawdzona.',
));

?>