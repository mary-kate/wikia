<?php

$magicWords = array(
	'en' => array(
		'ogg_noplayer' => array( 0, 'noplayer' ),
		'ogg_noicon' => array( 0, 'noicon' ),
		'ogg_thumbtime' => array( 0, 'thumbtime=$1' ),
	),
);

$messages = array();

$messages['en'] = array(
	'ogg-desc'             => 'Handler for Ogg Theora and Vorbis files, with JavaScript player',
	'ogg-short-audio'      => 'Ogg $1 sound file, $2',
	'ogg-short-video'      => 'Ogg $1 video file, $2',
	'ogg-short-general'    => 'Ogg $1 media file, $2',
	'ogg-long-audio'       => '(Ogg $1 sound file, length $2, $3)',
	'ogg-long-video'       => '(Ogg $1 video file, length $2, $4×$5 pixels, $3)',
	'ogg-long-multiplexed' => '(Ogg multiplexed audio/video file, $1, length $2, $4×$5 pixels, $3 overall)',
	'ogg-long-general'     => '(Ogg media file, length $2, $3)',
	'ogg-long-error'       => '(Invalid ogg file: $1)',
	'ogg-play'             => 'Play',
	'ogg-pause'            => 'Pause',
	'ogg-stop'             => 'Stop',
	'ogg-play-video'       => 'Play video',
	'ogg-play-sound'       => 'Play sound',
	'ogg-no-player'        => 'Sorry, your system does not appear to have any supported player software. ' .
		'Please <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">download a player</a>.',
	'ogg-no-xiphqt'        => 'You do not appear to have the XiphQT component for QuickTime. QuickTime cannot play ' .
		'Ogg files without this component. Please ' .
		'<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">download XiphQT</a> or choose another player.',

	'ogg-player-videoElement' => '<video> element',
	'ogg-player-oggPlugin' => 'Ogg plugin',
	'ogg-player-cortado'   => 'Cortado (Java)', # only translate this message to other languages if you have to change it
	'ogg-player-vlc-mozilla' => 'VLC', # only translate this message to other languages if you have to change it
	'ogg-player-vlc-activex' => 'VLC (ActiveX)', # only translate this message to other languages if you have to change it
	'ogg-player-quicktime-mozilla' => 'QuickTime', # only translate this message to other languages if you have to change it
	'ogg-player-quicktime-activex' => 'QuickTime (ActiveX)', # only translate this message to other languages if you have to change it
	'ogg-player-thumbnail' => 'Still image only',
	'ogg-player-soundthumb' => 'No player',
	'ogg-player-selected'  => '(selected)',
	'ogg-use-player'       => 'Use player: ',
	'ogg-more'             => 'More...',
	'ogg-dismiss'          => 'Close',
	'ogg-download'         => 'Download file',
	'ogg-desc-link'        => 'About this file',
);

$messages['af'] = array(
	'ogg-more' => 'Meer...',
);

/** Arabic (العربية)
 * @author Meno25
 * @author Alnokta
 */
$messages['ar'] = array(
	'ogg-desc'                     => 'متحكم لملفات أو جي جي ثيورا وفوربيس، مع لاعب جافاسكريبت',
	'ogg-short-audio'              => 'Ogg $1 ملف صوت، $2',
	'ogg-short-video'              => 'Ogg $1 ملف فيديو، $2',
	'ogg-short-general'            => 'Ogg $1 ملف ميديا، $2',
	'ogg-long-audio'               => '(Ogg $1 ملف صوت، الطول $2، $3)',
	'ogg-long-video'               => '(Ogg $1 ملف فيديو، الطول $2، $4×$5 بكسل، $3)',
	'ogg-long-multiplexed'         => '(ملف Ogg مالتي بليكسد أوديو/فيديو، $1، الطول $2، $4×$5 بكسل، $3 إجمالي)',
	'ogg-long-general'             => '(ملف ميديا Ogg، الطول $2، $3)',
	'ogg-long-error'               => '(ملف Ogg غير صحيح: $1)',
	'ogg-play'                     => 'عرض',
	'ogg-pause'                    => 'إيقاف مؤقت',
	'ogg-stop'                     => 'إيقاف',
	'ogg-play-video'               => 'عرض الفيديو',
	'ogg-play-sound'               => 'عرض الصوت',
	'ogg-no-player'                => 'معذرة ولكن يبدو أنه لا يوجد لديك برنامج عرض مدعوم. من فضلك ثبت <a href="http://www.java.com/en/download/manual.jsp">الجافا</a>.',
	'ogg-no-xiphqt'                => 'لا يبدو أنك تملك مكون XiphQT لكويك تايم. كويك تايم لا يمكنه عرض ملفات Ogg بدون هذا المكون. من فضلك <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">حمل XiphQT</a> أو اختر برنامجا آخر.',
	'ogg-player-videoElement'      => '<video> عنصر',
	'ogg-player-oggPlugin'         => 'إضافة Ogg',
	'ogg-player-cortado'           => 'كور تادو (جافا)',
	'ogg-player-vlc-mozilla'       => 'في إل سي',
	'ogg-player-vlc-activex'       => 'في إل سي (أكتيف إكس)',
	'ogg-player-quicktime-mozilla' => 'كويك تايم',
	'ogg-player-quicktime-activex' => 'كويك تايم (أكتيف إكس)',
	'ogg-player-thumbnail'         => 'مازال صورة فقط',
	'ogg-player-soundthumb'        => 'لا برنامج',
	'ogg-player-selected'          => '(مختار)',
	'ogg-use-player'               => 'استخدم البرنامج:',
	'ogg-more'                     => 'المزيد...',
	'ogg-dismiss'                  => 'غلق',
	'ogg-download'                 => 'نزل الملف',
	'ogg-desc-link'                => 'حول هذا الملف',
);

/** Asturian (Asturianu)
 * @author Esbardu
 */
$messages['ast'] = array(
	'ogg-short-audio'         => 'Archivu de soníu ogg $1, $2',
	'ogg-short-video'         => 'Archivu de videu ogg $1, $2',
	'ogg-short-general'       => 'Archivu multimedia ogg $1, $2',
	'ogg-long-audio'          => '(Archivu de soníu ogg $1, llonxitú $2, $3)',
	'ogg-long-video'          => '(Archivu de videu ogg $1, llonxitú $2, $4×$5 píxeles, $3)',
	'ogg-long-multiplexed'    => "(Archivu d'audiu/videu ogg multiplexáu, $1, llonxitú $2, $4×$5 píxeles, $3)",
	'ogg-long-general'        => '(Archivu multimedia ogg, llonxitú $2, $3)',
	'ogg-long-error'          => '(Archivu ogg non válidu: $1)',
	'ogg-play'                => 'Reproducir',
	'ogg-pause'               => 'Pausar',
	'ogg-stop'                => 'Aparar',
	'ogg-play-video'          => 'Reproducir videu',
	'ogg-play-sound'          => 'Reproducir soníu',
	'ogg-no-player'           => 'Sentímoslo, el to sistema nun paez tener nengún de los reproductores soportaos. Por favor <a
href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarga un reproductor</a>.',
	'ogg-no-xiphqt'           => 'Paez que nun tienes el componente XiphQT pa QuickTime. QuickTime nun pue reproducr archivos ogg ensin esti componente. Por favor <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarga XiphQT</a> o escueyi otru reproductor.',
	'ogg-player-videoElement' => 'elementu <video>',
	'ogg-player-oggPlugin'    => 'Plugin ogg',
	'ogg-player-thumbnail'    => 'Namái imaxe en pausa',
	'ogg-player-soundthumb'   => 'Nun hai reproductor',
	'ogg-player-selected'     => '(seleicionáu)',
	'ogg-use-player'          => 'Utilizar el reproductor:',
	'ogg-more'                => 'Más...',
	'ogg-dismiss'             => 'Zarrar',
	'ogg-download'            => 'Descargar archivu',
	'ogg-desc-link'           => 'Tocante a esti archivu',
);

/** Bikol Central*/
$messages['bcl'] = array(
	'ogg-more' => 'Dakol pa..',
	'ogg-dismiss' => 'Isara',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'ogg-short-audio' => 'Ogg $1 звуков файл, $2',
	'ogg-short-video' => 'Ogg $1 видео файл, $2',
	'ogg-long-audio'  => '(Ogg $1 звуков файл, продължителност $2, $3)',
	'ogg-long-video'  => '(Ogg $1 видео файл, продължителност $2, $4×$5 пиксела, $3)',
	'ogg-long-error'  => '(Невалиден ogg файл: $1)',
	'ogg-play'        => 'Пускане',
	'ogg-pause'       => 'Пауза',
	'ogg-stop'        => 'Спиране',
	'ogg-more'        => 'Повече...',
	'ogg-dismiss'     => 'Затваряне',
	'ogg-download'    => 'Изтеглене на файла',
	'ogg-desc-link'   => 'Информация за файла',
);

/** Bengali (বাংলা)
 * @author Zaheen
 */
$messages['bn'] = array(
	'ogg-short-audio'         => 'অগ $1 সাউন্ড ফাইল, $2',
	'ogg-short-video'         => 'অগ $1 ভিডিও ফাইল, $2',
	'ogg-short-general'       => 'অগ $1 মিডিয়া ফাইল, $2',
	'ogg-long-audio'          => '(অগ $1 সাউন্ড ফাইল, দৈর্ঘ্য $2, $3)',
	'ogg-long-video'          => '(অগ $1 ভিডিও ফাইল, দৈর্ঘ্য $2, $4×$5 পিক্সেল, $3)',
	'ogg-long-multiplexed'    => '(অগ মাল্টিপ্লেক্সকৃত অডিও/ভিডিও ফাইল, $1, দৈর্ঘ্য $2, $4×$5 পিক্সেল, $3 সামগ্রিক)',
	'ogg-long-general'        => '(অগ মিডিয়া ফাইল, দৈর্ঘ্য $2, $3)',
	'ogg-long-error'          => '(অবৈধ অগ ফাইল: $1)',
	'ogg-play'                => 'চালানো হোক',
	'ogg-pause'               => 'বিরতি',
	'ogg-stop'                => 'বন্ধ',
	'ogg-play-video'          => 'ভিডিও চালানো হোক',
	'ogg-play-sound'          => 'অডিও চালানো হোক',
	'ogg-no-player'           => 'দুঃখিত, আপনার কম্পিউটারে ফাইলটি চালনার জন্য কোন সফটওয়্যার নেই। অনুগ্রহ করে <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">চালনাকারী সফটওয়্যার ডাউনলোড করুন</a>।',
	'ogg-no-xiphqt'           => 'আপনার কুইকটাইম সফটওয়্যারটিতে XiphQT উপাদানটি নেই। এই উপাদানটি ছাড়া কুইকটাইম অগ ফাইল চালাতে পারবে না। অনুগ্রহ করে <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT ডাউনলোড করুন</a> অথবা অন্য একটি চালনাকারী সফটওয়্যার ব্যবহার করুন।',
	'ogg-player-videoElement' => '<video> উপাদান',
	'ogg-player-oggPlugin'    => 'অগ প্লাগ-ইন',
	'ogg-player-thumbnail'    => 'শুধুমাত্র স্থির চিত্র',
	'ogg-player-soundthumb'   => 'কোন চালনাকারী সফটওয়্যার নেই',
	'ogg-player-selected'     => '(নির্বাচিত)',
	'ogg-use-player'          => 'এই চালনাকারী সফটওয়্যার ব্যবহার করুন:',
	'ogg-more'                => 'আরও...',
	'ogg-dismiss'             => 'বন্ধ করা হোক',
	'ogg-download'            => 'ফাইল ডাউনলোড করুন',
	'ogg-desc-link'           => 'এই ফাইলের বৃত্তান্ত',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'ogg-more'    => "Muioc'h...",
	'ogg-dismiss' => 'Serriñ',
);

/** Catalan (Català)
 * @author SMP
 */
$messages['ca'] = array(
	'ogg-short-audio'       => "Arxiu OGG d'àudio $1, $2",
	'ogg-short-video'       => 'Arxiu OGG de vídeo $1, $2',
	'ogg-short-general'     => 'Arxiu multimèdia OGG $1, $2',
	'ogg-long-audio'        => '(Ogg $1 arxiu de so, llargada $2, $3)',
	'ogg-long-video'        => '(Arxiu OGG de vídeo $1, llargada $2, $4×$5 píxels, $3)',
	'ogg-long-multiplexed'  => '(Arxiu àudio/vídeo multiplex, $1, llargada $2, $4×$5 píxels, $3 de mitjana)',
	'ogg-long-general'      => '(Arxiu multimèdia OGG, llargada $2, $3)',
	'ogg-long-error'        => '(Arxiu OGG invàlid: $1)',
	'ogg-pause'             => 'Pausa',
	'ogg-play-video'        => 'Reprodueix vídeo',
	'ogg-play-sound'        => 'Reprodueix so',
	'ogg-no-player'         => 'No teniu instal·lat cap reproductor acceptat. Podeu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarregar-ne</a> un.',
	'ogg-no-xiphqt'         => 'No disposeu del component XiphQT al vostre QuickTime. Aquest component és imprescindible per a que el QuickTime pugui reproduir fitxers OGG. Podeu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarregar-lo</a> o escollir un altre reproductor.',
	'ogg-player-thumbnail'  => 'Només un fotograma',
	'ogg-player-soundthumb' => 'Cap reproductor',
	'ogg-player-selected'   => '(seleccionat)',
	'ogg-use-player'        => 'Usa el reproductor:',
	'ogg-more'              => 'Més...',
	'ogg-dismiss'           => 'Tanca',
	'ogg-download'          => "Descarrega l'arxiu",
	'ogg-desc-link'         => "Informació de l'arxiu",
);

/** Czech (Česky)
 * @author Li-sung
 * @author Matěj Grabovský
 */
$messages['cs'] = array(
	'ogg-desc'                => 'Obsluha souborů Ogg Theora a Vorbis s JavaScriptovým přehrávačem',
	'ogg-short-audio'         => 'Zvukový soubor ogg $1, $2',
	'ogg-short-video'         => 'Videosoubor ogg $1, $2',
	'ogg-short-general'       => 'Soubor média ogg $1, $2',
	'ogg-long-audio'          => '(Zvukový soubor ogg $1, délka $2, $3)',
	'ogg-long-video'          => '(Videosoubor $1, délka $2, $4×$5 pixelů, $3)',
	'ogg-long-multiplexed'    => '(Audio/video soubor ogg, $1, délka $2, $4×$5 pixelů, $3)',
	'ogg-long-general'        => '(Soubor média ogg, délka $2, $3)',
	'ogg-long-error'          => '(Chybný soubor ogg: $1)',
	'ogg-play'                => 'Přehrát',
	'ogg-pause'               => 'Pozastavit',
	'ogg-stop'                => 'Zastavit',
	'ogg-play-video'          => 'Přehrát video',
	'ogg-play-sound'          => 'Přehrát zvuk',
	'ogg-no-player'           => 'Váš systém zřejmě neobsahuje žádný podporovaný přehrávač. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Váš systém zřejmě neobsahuje žádný podporovaný přehrávač. </a>.',
	'ogg-no-xiphqt'           => 'Nemáte doplněk XiphQT pro QuickTime. QuickTime nemůže přehrávat soubory ogg bez tohoto doplňku. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Stáhněte XiphQT</a> nebo vyberte jiný přehrávač.',
	'ogg-player-videoElement' => 'element &lt;video&gt;',
	'ogg-player-oggPlugin'    => 'Zásuvný modul Ogg',
	'ogg-player-thumbnail'    => 'Pouze snímek náhledu',
	'ogg-player-soundthumb'   => 'Žádný přehrávač',
	'ogg-player-selected'     => '(zvoleno)',
	'ogg-use-player'          => 'Vyberte přehrávač:',
	'ogg-more'                => 'Více...',
	'ogg-dismiss'             => 'Zavřít',
	'ogg-download'            => 'Stáhnout soubor',
	'ogg-desc-link'           => 'O tomto souboru',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'ogg-desc'                => 'Steuerungsprogramm für Ogg Theora- und Vorbis-Dateien, inklusive einer JavaScript-Abspielsoftware',
	'ogg-short-audio'         => 'Ogg-$1-Audiodatei, $2',
	'ogg-short-video'         => 'Ogg-$1-Videodatei, $2',
	'ogg-short-general'       => 'Ogg-$1-Mediadatei, $2',
	'ogg-long-audio'          => '(Ogg-$1-Audiodatei, Länge: $2, $3)',
	'ogg-long-video'          => '(Ogg-$1-Videodatei, Länge: $2, $4×$5 Pixel, $3)',
	'ogg-long-multiplexed'    => '(Ogg-Audio-/Video-Datei, $1, Länge: $2, $4×$5 Pixel, $3)',
	'ogg-long-general'        => '(Ogg-Mediadatei, Länge: $2, $3)',
	'ogg-long-error'          => '(Ungültige Ogg-Datei: $1)',
	'ogg-play'                => 'Start',
	'ogg-pause'               => 'Pause',
	'ogg-stop'                => 'Stop',
	'ogg-play-video'          => 'Video abspielen',
	'ogg-play-sound'          => 'Audio abspielen',
	'ogg-no-player'           => 'Dein System scheint über keine Abspielsoftware zu verfügen. Bitte installiere <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">eine Abspielsoftware</a>.',
	'ogg-no-xiphqt'           => 'Dein System scheint nicht über die XiphQT-Komponente für QuickTime zu verfügen. QuickTime kann ohne diese Komponente keine Ogg-Dateien abspielen.' .
		'Bitte <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">lade XiphQT</a> oder wähle eine andere Abspielsoftware.',
	'ogg-player-videoElement' => '<video>-Element',
	'ogg-player-oggPlugin'    => 'Ogg-Plugin',
	'ogg-player-thumbnail'    => 'Zeige Vorschaubild',
	'ogg-player-soundthumb'   => 'Kein Player',
	'ogg-player-selected'     => '(ausgewählt)',
	'ogg-use-player'          => 'Abspielsoftware: ',
	'ogg-more'                => 'Optionen …',
	'ogg-dismiss'             => 'Schließen',
	'ogg-download'            => 'Datei speichern',
	'ogg-desc-link'           => 'Über diese Datei',
);

/** German - formal address (Deutsch - förmliche Anrede)
 * @author Raimond Spekking
 */
$messages['de-formal'] = array(
	'ogg-no-player'           => 'Ihr System scheint über keine Abspielsoftware zu verfügen. Bitte installieren Sie <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">eine Abspielsoftware</a>.',
	'ogg-no-xiphqt'           => 'Ihr System scheint nicht über die XiphQT-Komponente für QuickTime zu verfügen. QuickTime kann ohne diese Komponente keine Ogg-Dateien abspielen.' .
		'Bitte <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">laden Sie XiphQT</a> oder wählen Sie eine andere Abspielsoftware.',
);

$messages['el'] = array(
	'ogg-pause' => 'Παύση',
	'ogg-more' => 'Περισσότερα...',
	'ogg-download' => 'Κατεβάστε το αρχείο',
	'ogg-desc-link' => 'Σχετικά με αυτό τα αρχείο',
);

$messages['es'] = array(
	'ogg-more' => 'Opciones...',
	'ogg-dismiss' => 'Cerrar',
	'ogg-download' => 'Bajar archivo',
);

/** فارسی (فارسی)
 * @author Huji
 */
$messages['fa'] = array(
	'ogg-desc'                => 'به دست گیرندهٔ پرونده‌های Ogg Theora و Vorbis، با پخش‌کنندهٔ مبتنی بر JavaScript',
	'ogg-short-audio'         => 'پرونده صوتی Ogg $1، $2',
	'ogg-short-video'         => 'پرونده تصویری Ogg $1، $2',
	'ogg-short-general'       => 'پرونده Ogg $1، $2',
	'ogg-long-audio'          => '(پرونده صوتی Ogg $1، مدت $2، $3)',
	'ogg-long-video'          => '(پرونده تصویری Ogg $1، مدت $2 ، $4×$5 پیکسل، $3)',
	'ogg-long-multiplexed'    => '(پرونده صوتی/تصویری پیچیده Ogg، $1، مدت $2، $4×$5 پیکسل، $3 در مجموع)',
	'ogg-long-general'        => '(پرونده Ogg، مدت $2، $3)',
	'ogg-long-error'          => '(پرونده Ogg غیرمجاز: $1)',
	'ogg-play'                => 'پخش',
	'ogg-pause'               => 'توقف',
	'ogg-stop'                => 'قطع',
	'ogg-play-video'          => 'پخش تصویر',
	'ogg-play-sound'          => 'پخش صوت',
	'ogg-no-player'           => 'متاسفانه دستگاه شما نرم‌افزار پخش‌کنندهٔ مناسب ندارد. لطفاً <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">یک برنامهٔ پخش‌کننده بارگیری کنید</a>.',
	'ogg-no-xiphqt'           => 'به نظر نمی‌سرد که شما جزء XiphQT از برنامهٔ QuickTime را داشته باشید. برنامهٔ QuickTime بدون این جزء توان پخش پرونده‌های Ogg را ندارد. لطفاً <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT را بارگیری کنید</a> یا از یک پخش‌کنندهٔ دیگر استفاده کنید.',
	'ogg-player-videoElement' => 'عنصر <تصویری>',
	'ogg-player-oggPlugin'    => 'افزونهٔ Ogg',
	'ogg-player-thumbnail'    => 'فقط تصاویر ثابت',
	'ogg-player-soundthumb'   => 'فاقد پخش‌کننده',
	'ogg-player-selected'     => '(انتخاب شده)',
	'ogg-use-player'          => 'استفاده از پخش‌کننده:',
	'ogg-more'                => 'بیشتر...',
	'ogg-dismiss'             => 'بستن',
	'ogg-download'            => 'بارگیری پرونده',
	'ogg-desc-link'           => 'دربارهٔ این پرونده',

);

/** Finnish (Suomi)
 * @author Nike
 * @author Crt
 */
$messages['fi'] = array(
	'ogg-short-audio'         => 'Ogg $1 -äänitiedosto, $2',
	'ogg-short-video'         => 'Ogg $1 -videotiedosto, $2',
	'ogg-short-general'       => 'Ogg $1 -mediatiedosto, $2',
	'ogg-long-audio'          => '(Ogg $1 -äänitiedosto, $2, $3)',
	'ogg-long-video'          => '(Ogg $1 -videotiedosto, $2, $4×$5, $3)',
	'ogg-long-multiplexed'    => '(Ogg-tiedosto (limitetty kuva ja ääni), $1, $2, $4×$5, $3)',
	'ogg-long-general'        => '(Ogg-tiedosto, $2, $3)',
	'ogg-long-error'          => '(Kelvoton ogg-tiedosto: $1)',
	'ogg-play'                => 'Soita',
	'ogg-pause'               => 'Tauko',
	'ogg-stop'                => 'Pysäytä',
	'ogg-play-video'          => 'Toista video',
	'ogg-play-sound'          => 'Soita ääni',
	'ogg-no-player'           => 'Järjestelmästäsi ei löytynyt mitään tuetuista soitinohjelmista. Voit ladata sopivan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">soitinohjelman</a>.',
	'ogg-no-xiphqt'           => 'Tarvittavaa QuickTimen XiphQT-komponenttia ei löytynyt. QuickTime ei voi toistaa Ogg-tiedostoja ilman tätä komponenttia. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Lataa XiphQT</a> tai valitse toinen soitin.',
	'ogg-player-videoElement' => '<video>-elementti',
	'ogg-player-oggPlugin'    => 'Ogg-liitännäinen',
	'ogg-player-thumbnail'    => 'Pysäytyskuva',
	'ogg-player-soundthumb'   => 'Ei soitinta',
	'ogg-player-selected'     => '(valittu)',
	'ogg-use-player'          => 'Soitin:',
	'ogg-more'                => 'Lisää…',
	'ogg-dismiss'             => 'Sulje',
	'ogg-download'            => 'Lataa',
	'ogg-desc-link'           => 'Tiedoston tiedot',
);

$messages['fo'] = array(
	'ogg-more' => 'Meira...',
);

/** French (Français)
 * @author Seb35
 * @author Sherbrooke
 * @author Urhixidur
 * @author Grondin
 */
$messages['fr'] = array(
	'ogg-desc'                => 'Support pour les fichiers Ogg Theora et Vorbis, avec un lecteur Javascript',
	'ogg-short-audio'         => 'Fichier son Ogg $1, $2',
	'ogg-short-video'         => 'Fichier vidéo Ogg $1, $2',
	'ogg-short-general'       => 'Fichier média Ogg $1, $2',
	'ogg-long-audio'          => '(Fichier son Ogg $1, durée $2, $3)',
	'ogg-long-video'          => '(Fichier vidéo Ogg $1, durée $2, $4×$5 pixels, $3)',
	'ogg-long-multiplexed'    => '(Fichier multiplexé audio/vidéo Ogg, $1, durée $2, $4×$5 pixels, $3)',
	'ogg-long-general'        => '(Fichier média Ogg, durée $2, $3)',
	'ogg-long-error'          => '(Fichier Ogg invalide : $1)',
	'ogg-play'                => 'Lecture',
	'ogg-pause'               => 'Pause',
	'ogg-stop'                => 'Arrêt',
	'ogg-play-video'          => 'Lire la vidéo',
	'ogg-play-sound'          => 'Lire le son',
	'ogg-no-player'           => 'Désolé, votre système ne possède apparemment aucun des lecteurs supportés. Veuillez installer <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">un des lecteurs supportés</a>.',
	'ogg-no-xiphqt'           => 'Vous n\'avez apparemment pas le composant XiphQT pour Quicktime. Quicktime ne peut pas lire les fichiers Ogg sans ce composant. Veuillez <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">télécharger XiphQT</a> ou choisir un autre lecteur.',
	'ogg-player-videoElement' => 'Élément <video>',
	'ogg-player-oggPlugin'    => 'Plugiciel Ogg',
	'ogg-player-thumbnail'    => 'Image statique seulement',
	'ogg-player-soundthumb'   => 'Aucun lecteur',
	'ogg-player-selected'     => '(sélectionné)',
	'ogg-use-player'          => 'Utiliser le lecteur :',
	'ogg-more'                => 'Plus…',
	'ogg-dismiss'             => 'Fermer',
	'ogg-download'            => 'Télécharger le fichier',
	'ogg-desc-link'           => 'À propos de ce fichier',
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'ogg-desc'                => 'Supôrt por los fichiérs Ogg Theora et Vorbis, avouéc un liésor JavaScript',
	'ogg-short-audio'         => 'Fichiér son Ogg $1, $2',
	'ogg-short-video'         => 'Fichiér vidèô Ogg $1, $2',
	'ogg-short-general'       => 'Fichiér multimèdia Ogg $1, $2',
	'ogg-long-audio'          => '(Fichiér son Ogg $1, durâ $2, $3)',
	'ogg-long-video'          => '(Fichiér vidèô Ogg $1, durâ $2, $4×$5 pixèles, $3)',
	'ogg-long-multiplexed'    => '(Fichiér multiplèxo ôdiô/vidèô Ogg, $1, durâ $2, $4×$5 pixèles, $3)',
	'ogg-long-general'        => '(Fichiér multimèdia Ogg, durâ $2, $3)',
	'ogg-long-error'          => '(Fichiér Ogg envalido : $1)',
	'ogg-play'                => 'Liére',
	'ogg-pause'               => 'Pousa',
	'ogg-stop'                => 'Arrét',
	'ogg-play-video'          => 'Liére la vidèô',
	'ogg-play-sound'          => 'Liére lo son',
	'ogg-no-player'           => 'Dèsolâ, voutron sistèmo at aparament pas yon des liésors sotegnus. Volyéd enstalar <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">yon des liésors sotegnus</a>.',
	'ogg-no-xiphqt'           => 'Aparament vos avéd pas lo composent XiphQT por QuickTime. QuickTime pôt pas liére los fichiérs Ogg sen cél composent. Volyéd <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">tèlèchargiér XiphQT</a> ou ben chouèsir/cièrdre un ôtro liésor.',
	'ogg-player-videoElement' => 'Èlèment <video>',
	'ogg-player-oggPlugin'    => 'Plugin Ogg',
	'ogg-player-thumbnail'    => 'Émâge statica solament',
	'ogg-player-soundthumb'   => 'Nion liésor',
	'ogg-player-selected'     => '(sèlèccionâ)',
	'ogg-use-player'          => 'Utilisar lo liésor :',
	'ogg-more'                => 'De ples...',
	'ogg-dismiss'             => 'Cllôre',
	'ogg-download'            => 'Tèlèchargiér lo fichiér',
	'ogg-desc-link'           => 'A propôs de ceti fichiér',
);

/** Irish (Gaeilge)
 * @author Spacebirdy
 */
$messages['ga'] = array(
	'ogg-dismiss' => 'Dún',
);

$messages['gl'] = array(
	'ogg-short-audio' => 'Ficheiro de son Ogg $1, $2',
	'ogg-short-video' => 'Ficheiro de vídeo Ogg $1, $2',
	'ogg-short-general' => 'Ficheiro multimedia Ogg $1, $2',
	'ogg-long-audio' => '(Ficheiro de son Ogg $1, duración $2, $3)',
	'ogg-long-video' => '(Ficheiro de vídeo Ogg $1, duración $2, $4×$5 píxeles, $3)',
	'ogg-long-multiplexed' => '(Ficheiro de audio/vídeo Ogg multiplex, $1, duración $2, $4×$5 píxeles, $3 total)',
	'ogg-long-general' => 'Ficheiro multimedia Ogg, duración $2, $3)',
	'ogg-long-error' => '(Ficheiro ogg non válido: $1)',
	'ogg-play' => 'Reproducir',
	'ogg-pause' => 'Deter',
	'ogg-stop' => 'Parar',
	'ogg-play-video' => 'Reproducir vídeo',
	'ogg-play-sound' => 'Reproducir son',
	'ogg-no-player' => 'Parece que o seu sistema non dispón de software de reprodución axeitado. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Instale un reprodutor</a>.',
	'ogg-no-xiphqt' => 'Parece que non dispón do compoñente XiphQT para QuickTime. QuickTime non pode reproducir ficheiros Ogg sen este componente. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Instale XiphQT</a> ou escolla outro reprodutor.',
	'ogg-player-videoElement' => 'elemento <video>',
	'ogg-player-oggPlugin' => 'Extensión Ogg',
	'ogg-player-thumbnail' => 'Só instantánea',
	'ogg-player-soundthumb' => 'Ningún reprodutor',
	'ogg-player-selected' => '(seleccionado)',
	'ogg-use-player' => 'Usar o reprodutor:',
	'ogg-more' => 'Máis...',
	'ogg-dismiss' => 'Fechar',
	'ogg-download' => 'Baixar ficheiro',
	'ogg-desc-link' => 'Acerca deste ficheiro',
);

$messages['he'] = array(
	'ogg-short-audio'         => 'קובץ שמע $1 של Ogg, $2',
	'ogg-short-video'         => 'קובץ וידאו $1 של Ogg, $2',
	'ogg-short-general'       => 'קובץ מדיה $1 של Ogg, $2',
	'ogg-long-audio'          => '(קובץ שמע $1 של Ogg, באורך $2, $3)',
	'ogg-long-video'          => '(קובץ וידאו $1 של Ogg, באורך $2, $4×$5 פיקסלים, $3)',
	'ogg-long-multiplexed'    => '(קובץ מורכב של שמע/וידאו בפורמט Ogg, $1, באורך $2, $4×$5 פיקסלים, $3 בסך הכל)',
	'ogg-long-general'        => '(קובץ מדיה של Ogg, באורך $2, $3)',
	'ogg-long-error'          => '(קובץ ogg בלתי תקין: $1)',
	'ogg-play'                => 'נגן',
	'ogg-pause'               => 'הפסק',
	'ogg-stop'                => 'עצור',
	'ogg-play-video'          => 'נגן וידאו',
	'ogg-play-sound'          => 'נגן שמע',
	'ogg-no-player'           => 'מצטערים, נראה שהמערכת שלכם אינה כוללת תוכנת נגן נתמכת. אנא <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">הורידו נגן</a>.',
	'ogg-no-xiphqt'          => 'נראה שלא התקנתם את רכיב XiphQT של QuickTime, אך QuickTime אינו יכול לנגן קבצי Ogg בלי רכיב זה. אנא <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">הורידו את XiphQT</a> או בחרו נגן אחר.',

	'ogg-player-videoElement' => 'רכיב <video>',
	'ogg-player-oggPlugin'    => 'תוסף Ogg',
	'ogg-player-thumbnail'    => 'עדיין תמונה בלבד',
	'ogg-player-soundthumb'   => 'אין נגן',
	'ogg-player-selected'     => '(נבחר)',
	'ogg-use-player'          => 'השתמש בנגן: ',
	'ogg-more'                => 'עוד...',
	'ogg-dismiss'             => 'סגירה',
	'ogg-download'            => 'הורדת הקובץ',
	'ogg-desc-link'           => 'אודות הקובץ',
);

$messages['hr'] = array(
	'ogg-short-audio' => 'Ogg $1 zvučna datoteka, $2',
	'ogg-short-video' => 'Ogg $1 video datoteka, $2',
	'ogg-short-general' => 'Ogg $1 medijska datoteka, $2',
	'ogg-long-audio' => '(Ogg $1 zvučna datoteka, duljine $2, $3)',
	'ogg-long-video' => '(Ogg $1 video datoteka, duljine $2, $4x$5 piksela, $3)',
	'ogg-long-multiplexed' => '(Ogg multipleksirana zvučna/video datoteka, $1, duljine $2, $4×$5 piksela, $3 ukupno)',
	'ogg-long-general' => '(Ogg medijska datoteka, duljine $2, $3)',
	'ogg-long-error' => '(nevaljana ogg datoteka: $1)',
	'ogg-play' => 'Pokreni',
	'ogg-pause' => 'Pauziraj',
	'ogg-stop' => 'Zaustavi',
	'ogg-play-video' => 'Pokreni video',
	'ogg-play-sound' => 'Sviraj zvuk',
	'ogg-no-player' => 'Oprostite, izgleda da Vaš operacijski sustav nema instalirane medijske preglednike. Molimo <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">instalirajte medijski preglednik (\'\'player\'\')</a>.',
	'ogg-no-xiphqt' => 'Nemate instaliranu XiphQT komponentu za QuickTime (ili je neispravno instalirana). QuickTime ne može pokretati Ogg datoteke bez ove komponente. Molimo <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">instalirajte XiphQT</a> ili izaberite drugi preglednik (\'\'player\'\').',
	'ogg-player-videoElement' => '<slikovni> element',
	'ogg-player-oggPlugin' => 'Ogg plugin',#identical but defined
	'ogg-player-vlc-activex' => 'VLC (ActiveX kontrola)',#optional
	'ogg-player-thumbnail' => 'Samo (nepokretne) slike',
	'ogg-player-soundthumb' => 'Nema preglednika',
	'ogg-player-selected' => '(odabran)',
	'ogg-use-player' => 'Rabi preglednik (\'\'player\'\'):',
	'ogg-more' => 'Više...',
	'ogg-dismiss' => 'Zatvori',
	'ogg-download' => 'Snimi datoteku',
	'ogg-desc-link' => 'O ovoj datoteci',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 * @author Dundak
 */
$messages['hsb'] = array(
	'ogg-desc'                => 'Wodźenski program za dataje Ogg Theora a Vorbis, z JavaScriptowym wothrawakom',
	'ogg-short-audio'         => 'Awdiodataja Ogg $1, $2',
	'ogg-short-video'         => 'Widejodataja Ogg $1, $2',
	'ogg-short-general'       => 'Ogg medijowa dataja $1, $2',
	'ogg-long-audio'          => '(Ogg-awdiodataja $1, dołhosć: $2, $3)',
	'ogg-long-video'          => '(Ogg-widejodataja $1, dołhosć: $2, $4×$5 pikselow, $3)',
	'ogg-long-multiplexed'    => 'Ogg awdio-/widejodataja, $1, dołhosć: $2, $4×$5 pikselow, $3)',
	'ogg-long-general'        => '(Ogg medijowa dataja, dołhosć: $2, $3)',
	'ogg-long-error'          => '(Njepłaćiwa ogg-dataja: $1)',
	'ogg-play'                => 'Wothrać',
	'ogg-pause'               => 'Přestawka',
	'ogg-stop'                => 'Stój',
	'ogg-play-video'          => 'Widejo wothrać',
	'ogg-play-sound'          => 'Zynk wothrać',
	'ogg-no-player'           => 'Bohužel twój system po wšěm zdaću nima wothrawansku software. Prošu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">sćehń wothrawak</a>.',
	'ogg-no-xiphqt'           => 'Po wšěm zdaću nimaš komponentu XiphQT za QuickTime. QuickTime njemóže Ogg-dataje bjez tuteje komponenty wothrawać. Prošu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">sćehń XiphQT</a> abo wubjer druhi wothrawak.',
	'ogg-player-videoElement' => 'Element <video>',
	'ogg-player-oggPlugin'    => 'Tykač Ogg',
	'ogg-player-thumbnail'    => 'Napohlad pokazać',
	'ogg-player-soundthumb'   => 'Žadyn wothrawak',
	'ogg-player-selected'     => '(wubrany)',
	'ogg-use-player'          => 'Wothrawak wubrać:',
	'ogg-more'                => 'Wjace ...',
	'ogg-dismiss'             => 'Začinić',
	'ogg-download'            => 'Dataju sćahnyć',
	'ogg-desc-link'           => 'Wo tutej dataji',
);

/** Haitian (Kreyòl ayisyen)
 * @author Masterches
 */
$messages['ht'] = array(
	'ogg-play'  => 'Jwe',
	'ogg-pause' => 'Poz',
	'ogg-stop'  => 'Stope',
);

/** Hungarian (Magyar)
 * @author Bdanee
 * @author Tgr
 */
$messages['hu'] = array(
	'ogg-desc'                => 'JavaScript nyelven írt lejátszó Ogg Theora és Vorbis fájlokhoz',
	'ogg-short-audio'         => 'Ogg $1 hangfájl, $2',
	'ogg-short-video'         => 'Ogg $1 videofájl, $2',
	'ogg-short-general'       => 'Ogg $1 médiafájl, $2',
	'ogg-long-audio'          => '(Ogg $1 hangfájl, hossza: $2, $3)',
	'ogg-long-video'          => '(Ogg $1 videófájl, hossza $2, $4×$5 képpont, $3)',
	'ogg-long-multiplexed'    => '(Ogg egyesített audió- és videófájl, $1, hossz: $2, $4×$5 képpont, $3 összesen)',
	'ogg-long-general'        => '(Ogg médiafájl, hossza: $2, $3)',
	'ogg-long-error'          => '(Érvénytelen ogg fájl: $1)',
	'ogg-play'                => 'Lejátszás',
	'ogg-pause'               => 'Szüneteltetés',
	'ogg-stop'                => 'Állj',
	'ogg-play-video'          => 'Videó lejátszása',
	'ogg-play-sound'          => 'Hang lejátszása',
	'ogg-no-player'           => 'Sajnáljuk, de úgy tűnik, hogy nem rendelkezel a megfelelő lejátszóval. Amennyiben le szeretnéd játszani, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">tölts le egyet</a>.',
	'ogg-no-xiphqt'           => 'Úgy tűnik, nem rendelkezel a QuickTime-hoz való XiphQT összetevővel. Enélkül a QuickTime nem tudja lejátszani az Ogg fájlokat. A lejátszáshoz tölts le egyet <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">innen</a>, vagy válassz másik lejátszót.',
	'ogg-player-videoElement' => '<video> elem',
	'ogg-player-oggPlugin'    => 'Ogg beépülő modul',
	'ogg-player-thumbnail'    => 'Csak állókép',
	'ogg-player-soundthumb'   => 'Nincs lejátszó',
	'ogg-player-selected'     => '(kiválasztott)',
	'ogg-use-player'          => 'Lejátszó:',
	'ogg-more'                => 'Tovább...',
	'ogg-dismiss'             => 'Bezárás',
	'ogg-download'            => 'Fájl letöltése',
	'ogg-desc-link'           => 'Fájlinformációk',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 * @author Spacebirdy
 */
$messages['is'] = array(
	'ogg-play'              => 'Spila',
	'ogg-pause'             => 'gera hlé',
	'ogg-stop'              => 'Stöðva',
	'ogg-play-video'        => 'Spila myndband',
	'ogg-play-sound'        => 'Spila hljóð',
	'ogg-player-soundthumb' => 'Enginn spilari',
	'ogg-player-selected'   => '(valið)',
	'ogg-use-player'        => 'Nota spilara:',
	'ogg-more'              => 'Meira...',
	'ogg-dismiss'           => 'Loka',
	'ogg-download'          => 'Sækja skrá',
);

/** Italian (Italiano)
 * @author BrokenArrow
 * @author .anaconda
 */
$messages['it'] = array(
	'ogg-desc'                => 'Gestore per i file Ogg Theora e Vorbis, con programma di riproduzione in JavaScript',
	'ogg-short-audio'         => 'File audio Ogg $1, $2',
	'ogg-short-video'         => 'File video Ogg $1, $2',
	'ogg-short-general'       => 'File multimediale Ogg $1, $2',
	'ogg-long-audio'          => '(File audio Ogg $1, durata $2, $3)',
	'ogg-long-video'          => '(File video Ogg $1, durata $2, dimensioni $4×$5 pixel, $3)',
	'ogg-long-multiplexed'    => '(File audio/video multiplexed Ogg $1, durata $2, dimensioni $4×$5 pixel, complessivamente $3)',
	'ogg-long-general'        => '(File multimediale Ogg, durata $2, $3)',
	'ogg-long-error'          => '(File ogg non valido: $1)',
	'ogg-play'                => 'Riproduci',
	'ogg-pause'               => 'Pausa',
	'ogg-stop'                => 'Ferma',
	'ogg-play-video'          => 'Riproduci il filmato',
	'ogg-play-sound'          => 'Riproduci il file sonoro',
	'ogg-no-player'           => 'Siamo spiacenti, ma non risulta installato alcun software di riproduzione compatibile. Si prega di <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">scaricare un lettore</a> adatto.',
	'ogg-no-xiphqt'           => 'Non risulta installato il componente XiphQT di QuickTime. Senza tale componente non è possibile la riproduzione di file Ogg con QuickTime. Si prega di <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">scaricare XiphQT</a> o scegliere un altro lettore.',
	'ogg-player-videoElement' => 'elemento <video>',
	'ogg-player-oggPlugin'    => 'Plugin ogg',
	'ogg-player-thumbnail'    => 'Solo immagini fisse',
	'ogg-player-soundthumb'   => 'Nessun lettore',
	'ogg-player-selected'     => '(selezionato)',
	'ogg-use-player'          => 'Usa il lettore:',
	'ogg-more'                => 'Altro...',
	'ogg-dismiss'             => 'Chiudi',
	'ogg-download'            => 'Scarica il file',
	'ogg-desc-link'           => 'Informazioni su questo file',
);

/** Japanese (日本語)
 * @author Kahusi
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'ogg-desc'                => 'Theora及びVorbis形式のOggファイルハンドラとJavaScriptプレイヤー',
	'ogg-short-audio'         => 'Ogg $1 音声ファイル、$2',
	'ogg-short-video'         => 'Ogg $1 動画ファイル、$2',
	'ogg-short-general'       => 'Ogg $1 メディアファイル、$2',
	'ogg-long-audio'          => '(Ogg $1 音声ファイル、長さ $2、$3)',
	'ogg-long-video'          => '(Ogg $1 動画ファイル、長さ $2、$4×$5px、$3)',
	'ogg-long-multiplexed'    => '(Ogg 多重音声/動画ファイル、$1、長さ$2、$4×$5px, 凡そ$3)',
	'ogg-long-general'        => '(Ogg メディアファイル、長さ $2、$3)',
	'ogg-long-error'          => '(無効なOggファイル: $1)',
	'ogg-play'                => '再生',
	'ogg-pause'               => '一時停止',
	'ogg-stop'                => '停止',
	'ogg-play-video'          => '動画を再生',
	'ogg-play-sound'          => '音声を再生',
	'ogg-no-player'           => '申し訳ありません、あなたのシステムには対応する再生ソフトウェアがインストールされていないようです。<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ここからダウンロードしてください</a>。',
	'ogg-no-xiphqt'           => 'QuickTime用XiphQTコンポーネントがインストールされていないようです。QuickTimeでOggファイルを再生するには、このコンポーネントが必要です。<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ここからXiphQTをダウンロードする</a>か、別の再生ソフトをインストールしてください。',
	'ogg-player-videoElement' => '<video> element',
	'ogg-player-oggPlugin'    => 'Oggプラグイン',
	'ogg-player-thumbnail'    => '静止画像のみ',
	'ogg-player-soundthumb'   => 'プレーヤー無し',
	'ogg-player-selected'     => '(選択)',
	'ogg-use-player'          => '利用するプレーヤー:',
	'ogg-more'                => 'その他……',
	'ogg-dismiss'             => '閉じる',
	'ogg-download'            => 'ファイルをダウンロード',
	'ogg-desc-link'           => 'ファイルの詳細',
);

$messages['kk-arab'] = array(
	'ogg-short-audio'      => 'Ogg $1 دىبىس فايلى, $2',
	'ogg-short-video'      => 'Ogg $1 بەينە فايلى, $2',
	'ogg-short-general'    => 'Ogg $1 تاسپا فايلى, $2',
	'ogg-long-audio'       => '(Ogg $1 دىبىس فايلى, ۇزاقتىعى $2, $3)',
	'ogg-long-video'       => '(Ogg $1 بەينە فايلى, ۇزاقتىعى $2, $4 × $5 پىيكسەل, $3)',
	'ogg-long-multiplexed' => '(Ogg قۇرامدى دىبىس/بەينە فايلى, $1, ۇزاقتىعى $2, $4 × $5 پىيكسەل, $3 نە بارلىعى)',
	'ogg-long-general'     => '(Ogg تاسپا فايلى, ۇزاقتىعى $2, $3)',
	'ogg-long-error'       => '(جارامسىز ogg فايلى: $1)',
	'ogg-play'             => 'ويناتۋ',
	'ogg-pause'            => 'ايالداتۋ',
	'ogg-stop'             => 'توقتاتۋ',
	'ogg-play-video'       => 'بەينەنى ويناتۋ',
	'ogg-play-sound'       => 'دىبىستى ويناتۋ',
	'ogg-no-player'           => 'عافۋ ەتىڭىز, جۇيەڭىزدە ەش سۇيەمەلدەگەن ويناتۋ باعدارلامالىق قامتاماسىزداندىرعىش ورناتىلماعان. <a href="http://www.java.com/en/download/manual.jsp">Java</a> بۋماسىن ورناتىپ شىعىڭىز.',
	'ogg-no-xiphqt'           => 'QuickTime ويناتقىشىڭىزدىڭ XiphQT دەگەن قۇراشى جوق سىيياقتى. بۇل قۇراشىسىز Ogg فايلدارىن QuickTime ويناتا المايدى. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT قۇراشىن</a> نە باسقا ويناتقىشتى جۇكتەڭىز.',
	'ogg-player-videoElement' => '<video> داناسى',
	'ogg-player-oggPlugin' => 'Ogg قوسىمشا باعدارلاماسى',
	'ogg-player-thumbnail' => 'تەك ستوپ-كادر',
	'ogg-player-soundthumb' => 'ويناتقىشسىز',
	'ogg-player-selected'  => '(بولەكتەلگەن)',
	'ogg-use-player'       => 'ويناتقىش پايدالانۋى: ',
	'ogg-more'             => 'كوبىرەك...',
	'ogg-dismiss'          => 'جابۋ',
	'ogg-download'         => 'فايلدى جۇكتەۋ',
	'ogg-desc-link'        => 'بۇل فايل تۋرالى',
);

$messages['kk-cyrl'] = array(
	'ogg-short-audio'         => 'Ogg $1 дыбыс файлы, $2',
	'ogg-short-video'         => 'Ogg $1 бейне файлы, $2',
	'ogg-short-general'       => 'Ogg $1 таспа файлы, $2',
	'ogg-long-audio'          => '(Ogg $1 дыбыс файлы, ұзақтығы $2, $3)',
	'ogg-long-video'          => '(Ogg $1 бейне файлы, ұзақтығы $2, $4 × $5 пиксел, $3)',
	'ogg-long-multiplexed'    => '(Ogg құрамды дыбыс/бейне файлы, $1, ұзақтығы $2, $4 × $5 пиксел, $3 не барлығы)',
	'ogg-long-general'        => '(Ogg таспа файлы, ұзақтығы $2, $3)',
	'ogg-long-error'          => '(Жарамсыз ogg файлы: $1)',
	'ogg-play'                => 'Ойнату',
	'ogg-pause'               => 'Аялдату',
	'ogg-stop'                => 'Тоқтату',
	'ogg-play-video'          => 'Бейнені ойнату',
	'ogg-play-sound'          => 'Дыбысты ойнату',
	'ogg-no-player'           => 'Ғафу етіңіз, жүйеңізде еш сүйемелдеген ойнату бағдарламалық қамтамасыздандырғыш орнатылмаған. <a href="http://www.java.com/en/download/manual.jsp">Java</a> бумасын орнатып шығыңыз.',
	'ogg-no-xiphqt'           => 'QuickTime ойнатқышыңыздың XiphQT деген құрашы жоқ сияқты. Бұл құрашысыз Ogg файлдарын QuickTime ойната алмайды. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT құрашын</a> не басқа ойнатқышты жүктеңіз.',
	'ogg-player-videoElement' => '<video> данасы',
	'ogg-player-oggPlugin'    => 'Ogg қосымша бағдарламасы',
	'ogg-player-thumbnail'    => 'Тек стоп-кадр',
	'ogg-player-soundthumb'   => 'Ойнатқышсыз',
	'ogg-player-selected'     => '(бөлектелген)',
	'ogg-use-player'          => 'Ойнатқыш пайдалануы: ',
	'ogg-more'                => 'Көбірек...',
	'ogg-dismiss'             => 'Жабу',
	'ogg-download'            => 'Файлды жүктеу',
	'ogg-desc-link'           => 'Бұл файл туралы',
);

$messages['kk-latn'] = array(
	'ogg-short-audio'         => 'Ogg $1 dıbıs faýlı, $2',
	'ogg-short-video'         => 'Ogg $1 beýne faýlı, $2',
	'ogg-short-general'       => 'Ogg $1 taspa faýlı, $2',
	'ogg-long-audio'          => '(Ogg $1 dıbıs faýlı, uzaqtığı $2, $3)',
	'ogg-long-video'          => '(Ogg $1 beýne faýlı, uzaqtığı $2, $4 × $5 pïksel, $3)',
	'ogg-long-multiplexed'    => '(Ogg quramdı dıbıs/beýne faýlı, $1, uzaqtığı $2, $4 × $5 pïksel, $3 ne barlığı)',
	'ogg-long-general'        => '(Ogg taspa faýlı, uzaqtığı $2, $3)',
	'ogg-long-error'          => '(Jaramsız ogg faýlı: $1)',
	'ogg-play'                => 'Oýnatw',
	'ogg-pause'               => 'Ayaldatw',
	'ogg-stop'                => 'Toqtatw',
	'ogg-play-video'          => 'Beýneni oýnatw',
	'ogg-play-sound'          => 'Dıbıstı oýnatw',
	'ogg-no-player'           => 'Ğafw etiñiz, jüýeñizde eş süýemeldegen oýnatw bağdarlamalıq qamtamasızdandırğış ornatılmağan. <a href="http://www.java.com/en/download/manual.jsp">Java</a> bwmasın ornatıp şığıñız.',
	'ogg-no-xiphqt'           => 'QuickTime oýnatqışıñızdıñ XiphQT degen quraşı joq sïyaqtı. Bul quraşısız Ogg faýldarın QuickTime oýnata almaýdı. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT quraşın</a> ne basqa oýnatqıştı jükteñiz.',
	'ogg-player-videoElement' => '<video> danası',
	'ogg-player-oggPlugin'    => 'Ogg qosımşa bağdarlaması',
	'ogg-player-thumbnail'    => 'Tek stop-kadr',
	'ogg-player-soundthumb'   => 'Oýnatqışsız',
	'ogg-player-selected'     => '(bölektelgen)',
	'ogg-use-player'          => 'Oýnatqış paýdalanwı: ',
	'ogg-more'                => 'Köbirek...',
	'ogg-dismiss'             => 'Jabw',
	'ogg-download'            => 'Faýldı jüktew',
	'ogg-desc-link'           => 'Bul faýl twralı',
);

$messages['la'] = array(
	'ogg-more' => 'Plus...',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'ogg-short-audio'         => 'Ogg-$1-Tounfichier, $2',
	'ogg-short-video'         => 'Ogg-$1-Videofichier, $2',
	'ogg-short-general'       => 'Ogg-$1-Mediefichier, $2',
	'ogg-long-audio'          => '(Ogg-$1-Tounfichier, Dauer: $2, $3)',
	'ogg-long-video'          => '(Ogg-$1-Videofichier, Dauer: $2, $4×$5 Pixel, $3)',
	'ogg-long-multiplexed'    => '(Ogg-Toun-/Video-Fichier, $1, Dauer: $2, $4×$5 Pixel, $3)',
	'ogg-play'                => 'Ofspillen',
	'ogg-pause'               => 'Paus',
	'ogg-stop'                => 'Stopp',
	'ogg-play-video'          => 'Video ofspillen',
	'ogg-play-sound'          => 'Tounfichier ofspillen',
	'ogg-player-videoElement' => '<video>-Element',
	'ogg-player-oggPlugin'    => 'Ogg-Plugin',
	'ogg-more'                => 'Méi ...',
	'ogg-dismiss'             => 'Zoumaachen',
	'ogg-download'            => 'Fichier eroflueden',
	'ogg-desc-link'           => 'Iwwer dëse Fichier',
);

/** Limburgish (Limburgs)
 * @author Ooswesthoesbes
 * @author Matthias
 */
$messages['li'] = array(
	'ogg-desc'                => "Handelt Ogg Theora- en Vorbis-bestande aaf met 'n JavaScript-mediaspeler",
	'ogg-short-audio'         => 'Ogg $1 geluidsbestandj, $2',
	'ogg-short-video'         => 'Ogg $1 videobestandj, $2',
	'ogg-short-general'       => 'Ogg $1 mediabestandj, $2',
	'ogg-long-audio'          => '(Ogg $1 geluidsbestandj, lingdje $2, $3)',
	'ogg-long-video'          => '(Ogg $1 videobestandj, lingdje $2, $4×$5 pixels, $3)',
	'ogg-long-multiplexed'    => '(Ogg gemultiplexeerd geluids-/videobestandj, $1, lingdje $2, $4×$5 pixels, $3 totaal)',
	'ogg-long-general'        => '(Ogg mediabestandj, lingdje $2, $3)',
	'ogg-long-error'          => '(Óngeljig oggg-bestandj: $1)',
	'ogg-play'                => 'Aafspele',
	'ogg-pause'               => 'Óngerbraeke',
	'ogg-stop'                => 'Oetsjeije',
	'ogg-play-video'          => 'Video aafspele',
	'ogg-play-sound'          => 'Geluid aafspele',
	'ogg-no-player'           => 'Sorry, uch systeem haet gein van de ongersteunde mediaspelers. Installeer estebleef <a href="http://www.java.com/nl/download/manual.jsp">Java</a>.',
	'ogg-no-xiphqt'           => "'t Liek d'r op det geer 't component XiphQT veur QuickTime neet haet. QuickTime kin Ogg-bestenj neet aafspele zonger dit component. Download <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">XiphQT</a> estebleef of kees 'ne angere speler.",
	'ogg-player-videoElement' => '<video> element',
	'ogg-player-oggPlugin'    => 'Ogg-plugin',
	'ogg-player-thumbnail'    => 'Allein stilstaondj beild',
	'ogg-player-soundthumb'   => 'Geine mediaspeler',
	'ogg-player-selected'     => '(geselectieërdj)',
	'ogg-use-player'          => 'Gebroek speler:',
	'ogg-more'                => 'Mieë...',
	'ogg-dismiss'             => 'Sloet',
	'ogg-download'            => 'Bestandj downloade',
	'ogg-desc-link'           => 'Euver dit bestandj',
);

/** Lithuanian (Lietuvių)
 * @author Matasg
 */
$messages['lt'] = array(
	'ogg-short-audio'         => 'Ogg $1 garso byla, $2',
	'ogg-short-video'         => 'Ogg $1 video byla, $2',
	'ogg-short-general'       => 'Ogg $1 medija byla, $2',
	'ogg-long-audio'          => '(Ogg $1 garso byla, ilgis $2, $3)',
	'ogg-long-video'          => '(Ogg $1 video byla, ilgis $2, $4×$5 pikseliai, $3)',
	'ogg-long-multiplexed'    => '(Ogg sutankinta audio/video byla, $1, ilgis $2, $4×$5 pikseliai, $3 viso)',
	'ogg-long-general'        => '(Ogg media byla, ilgis $2, $3)',
	'ogg-long-error'          => '(Bloga ogg byla: $1)',
	'ogg-play'                => 'Groti',
	'ogg-pause'               => 'Pauzė',
	'ogg-stop'                => 'Sustabdyti',
	'ogg-play-video'          => 'Groti video',
	'ogg-play-sound'          => 'Groti garsą',
	'ogg-no-player'           => 'Atsiprašome, neatrodo, kad jūsų sistema turi palaikomą grotuvą. Prašome <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">jį atsisiųsti</a>.',
	'ogg-no-xiphqt'           => 'Neatrodo, kad jūs turite XiphQT komponentą QuickTime grotuvui. QuickTime negali groti Ogg bylų be šio komponento. Prašome <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">atsisiųsti XiphQT</a> arba pasirinkti kitą grotuvą.',
	'ogg-player-videoElement' => '<video> elementas',
	'ogg-player-oggPlugin'    => 'Ogg priedas',
	'ogg-player-thumbnail'    => 'Tik paveikslėlis',
	'ogg-player-soundthumb'   => 'Nėra grotuvo',
	'ogg-player-selected'     => '(pasirinkta)',
	'ogg-use-player'          => 'Naudoti grotuvą:',
	'ogg-more'                => 'Daugiau...',
	'ogg-dismiss'             => 'Uždaryti',
	'ogg-download'            => 'Atsisiųsti bylą',
	'ogg-desc-link'           => 'Apie šią bylą',
);

$messages['nds'] = array(
	'ogg-short-audio' => 'Ogg-$1-Toondatei, $2',
	'ogg-short-video' => 'Ogg-$1-Videodatei, $2',
	'ogg-short-general' => 'Ogg-$1-Mediendatei, $2',
	'ogg-long-audio' => '(Ogg-$1-Toondatei, $2 lang, $3)',
	'ogg-long-video' => '(Ogg-$1-Videodatei, $2 lang, $4×$5 Pixels, $3)',
	'ogg-long-multiplexed' => '(Ogg-Multiplexed-Audio-/Video-Datei, $1, $2 lang, $4×$5 Pixels, $3 alltohoop)',
	'ogg-long-general' => '(Ogg-Mediendatei, $2 lang, $3)',
	'ogg-long-error' => '(Kaputte Ogg-Datei: $1)',
	'ogg-play' => 'Afspelen',
	'ogg-pause' => 'Paus',
	'ogg-stop' => 'Stopp',
	'ogg-play-video' => 'Video afspelen',
	'ogg-play-sound' => 'Toondatei afspelen',
	'ogg-no-player' => 'Süht so ut, as wenn dien Reekner keen passlichen Afspeler hett. Du kannst en <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Afspeler dalladen</a>.',
	'ogg-no-xiphqt' => 'Süht so ut, as wenn dien Reekner de XiphQT-Kumponent för QuickTime nich hett. Ahn dat Ding kann QuickTime keen Ogg-Datein afspelen. Du kannst <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT dalladen</a> oder en annern Afspeler utwählen.',
	'ogg-player-videoElement' => '<video>-Element',
	'ogg-player-oggPlugin' => 'Ogg-Plugin',
	'ogg-player-thumbnail' => 'blot Standbild',
	'ogg-player-soundthumb' => 'Keen Afspeler',
	'ogg-player-selected' => '(utwählt)',
	'ogg-use-player' => 'Afspeler bruken:',
	'ogg-more' => 'Mehr...',
	'ogg-dismiss' => 'Dichtmaken',
	'ogg-download' => 'Datei dalladen',
	'ogg-desc-link' => 'Över disse Datei',
);

/** Dutch (Nederlands)
 * @author Siebrand
 * @author SPQRobin
 */
$messages['nl'] = array(
	'ogg-desc'                => 'Handelt Ogg Theora- en Vorbis-bestanden af met een JavaScript-mediaspeler',
	'ogg-short-audio'         => 'Ogg $1 geluidsbestand, $2',
	'ogg-short-video'         => 'Ogg $1 videobestand, $2',
	'ogg-short-general'       => 'Ogg $1 mediabestand, $2',
	'ogg-long-audio'          => '(Ogg $1 geluidsbestand, lengte $2, $3)',
	'ogg-long-video'          => '(Ogg $1 video file, lengte $2, $4×$5 pixels, $3)',
	'ogg-long-multiplexed'    => '(Ogg gemultiplexed geluids/videobestand, $1, lengte $2, $4×$5 pixels, $3 totaal)',
	'ogg-long-general'        => '(Ogg mediabestand, lengte $2, $3)',
	'ogg-long-error'          => '(Ongeldig ogg-bestand: $1)',
	'ogg-play'                => 'Afspelen',
	'ogg-pause'               => 'Pauze',
	'ogg-stop'                => 'Stop',
	'ogg-play-video'          => 'Video afspelen',
	'ogg-play-sound'          => 'Geluid afspelen',
	'ogg-no-player'           => 'Sorry, uw systeem heeft geen van de ondersteunde mediaspelers. Installeer alstublieft <a href="http://www.java.com/nl/download/manual.jsp">Java</a>.',
	'ogg-no-xiphqt'           => 'Het lijkt erop dat u de component XiphQT voor QuickTime niet heeft. QuickTime kan Ogg-bestanden niet afspelen zonder deze component. Download <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT</a> alstublieft of kies een andere speler.',
	'ogg-player-videoElement' => '<video>-element',
	'ogg-player-oggPlugin'    => 'Ogg-plugin',
	'ogg-player-thumbnail'    => 'Alleen stilstaand beeld',
	'ogg-player-soundthumb'   => 'Geen mediaspeler',
	'ogg-player-selected'     => '(geselecteerd)',
	'ogg-use-player'          => 'Gebruik speler:',
	'ogg-more'                => 'Meer...',
	'ogg-dismiss'             => 'Sluiten',
	'ogg-download'            => 'Bestand downloaden',
	'ogg-desc-link'           => 'Over dit bestand',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Eirik
 */
$messages['nn'] = array(
	'ogg-short-audio'         => 'Ogg $1-lydfil, $2',
	'ogg-short-video'         => 'Ogg $1-videofil, $2',
	'ogg-short-general'       => 'Ogg $1-mediafil, $2',
	'ogg-long-audio'          => '(Ogg $1-lydfil, lengde $2, $3)',
	'ogg-long-video'          => '(Ogg $1-videofil, lengde $2, $4×$5 pikslar, $3)',
	'ogg-long-multiplexed'    => '(Samansett ogg lyd-/videofil, $1, lengde $2, $4×$5 pikslar, $3 til saman)',
	'ogg-long-general'        => '(Ogg mediafil, lengde $2, $3)',
	'ogg-long-error'          => '(Ugyldig ogg-fil: $1)',
	'ogg-play'                => 'Spel av',
	'ogg-pause'               => 'Pause',
	'ogg-stop'                => 'Stopp',
	'ogg-play-video'          => 'Spel av videofila',
	'ogg-play-sound'          => 'Spel av lydfila',
	'ogg-no-player'           => 'Beklagar, systemet ditt har ikkje støtta programvare til avspeling. Ver venleg og <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned ein avspelar</a>.',
	'ogg-no-xiphqt'           => 'Du ser ikkje ut til å ha XiphQT-komponenten til QuickTime. QuickTime kan ikkje spele av ogg-filer utan denne. Ver venleg og <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned XiphQT</a> eller vel ein annan avspelar.',
	'ogg-player-videoElement' => '<video>-element',
	'ogg-player-oggPlugin'    => 'Ogg-tillegg',
	'ogg-player-thumbnail'    => 'Berre stillbilete',
	'ogg-player-soundthumb'   => 'Ingen avspelar',
	'ogg-player-selected'     => '(valt)',
	'ogg-use-player'          => 'Bruk avspelaren:',
	'ogg-more'                => 'Meir...',
	'ogg-dismiss'             => 'Lat att',
	'ogg-download'            => 'Last ned fila',
	'ogg-desc-link'           => 'Om denne fila',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'ogg-desc'                => 'Gjør at Ogg Theora- og Ogg Vorbis-filer kan kjøres med hjelp av JavaScript-avspiller.',
	'ogg-short-audio'         => 'Ogg $1 lydfil, $2',
	'ogg-short-video'         => 'Ogg $1 videofil, $2',
	'ogg-short-general'       => 'Ogg $1 mediefil, $2',
	'ogg-long-audio'          => '(Ogg $1 lydfil, lengde $2, $3)',
	'ogg-long-video'          => '(Ogg $1 videofil, lengde $2, $4×$5 piksler, $3)',
	'ogg-long-multiplexed'    => '(Sammensatt ogg lyd-/videofil, $1, lengde $2, $4×$5 piksler, $3 til sammen)',
	'ogg-long-general'        => '(Ogg mediefil, lengde $2, $3)',
	'ogg-long-error'          => '(Ugyldig ogg-fil: $1)',
	'ogg-play'                => 'Spill',
	'ogg-pause'               => 'Pause',
	'ogg-stop'                => 'Stopp',
	'ogg-play-video'          => 'Spill av video',
	'ogg-play-sound'          => 'Spill av lyd',
	'ogg-no-player'           => 'Beklager, systemet ditt har ingen medieavspillere som støtter filformatet. Vennligst <a href="http://mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned en avspiller</a> som støtter formatet.',
	'ogg-no-xiphqt'           => 'Du har ingen XiphQT-komponent for QuickTime. QuickTime kan ikke spille Ogg-filer uten denne komponenten. <a href="http://mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned XiphQT</a> eller velg en annen medieavspiller.',
	'ogg-player-videoElement' => '<video>-element',
	'ogg-player-oggPlugin'    => 'Ogg-plugin',
	'ogg-player-thumbnail'    => 'Kun stillbilder',
	'ogg-player-soundthumb'   => 'Ingen medieavspiller',
	'ogg-player-selected'     => '(valgt)',
	'ogg-use-player'          => 'Bruk avspiller:',
	'ogg-more'                => 'Mer...',
	'ogg-dismiss'             => 'Lukk',
	'ogg-download'            => 'Last ned fil',
	'ogg-desc-link'           => 'Om denne filen',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'ogg-desc'                => 'Supòrt pels fichièrs Ogg Theora e Vorbis, amb un lector Javascript',
	'ogg-short-audio'         => 'Fichièr son Ogg $1, $2',
	'ogg-short-video'         => 'Fichièr vidèo Ogg $1, $2',
	'ogg-short-general'       => 'Fichièr mèdia Ogg $1, $2',
	'ogg-long-audio'          => '(Fichièr son Ogg $1, durada $2, $3)',
	'ogg-long-video'          => '(Fichièr vidèo Ogg $1, durada $2, $4×$5 pixels, $3)',
	'ogg-long-multiplexed'    => '(Fichièr multiplexat audio/vidèo Ogg, $1, durada $2, $4×$5 pixels, $3)',
	'ogg-long-general'        => '(Fichièr mèdia Ogg, durada $2, $3)',
	'ogg-long-error'          => '(Fichièr Ogg invalid : $1)',
	'ogg-play'                => 'Legir',
	'ogg-pause'               => 'Pausa',
	'ogg-stop'                => 'Stòp',
	'ogg-play-video'          => 'Legir la vidèo',
	'ogg-play-sound'          => 'Legir lo son',
	'ogg-no-player'           => 'O planhem, vòstre sistèma possedís aparentament pas cap de lectors suportats. Installatz <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">un dels lectors suportats</a>.',
	'ogg-no-xiphqt'           => 'Aparentament avètz pas lo compausant XiphQT per Quicktime. Quicktime pòt pas legir los fiquièrs Ogg sens aqueste compausant. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr"> Telecargatz-lo XiphQT</a> o causissetz un autre lector.',
	'ogg-player-videoElement' => 'Element <video>',
	'ogg-player-oggPlugin'    => 'Plugin Ogg',
	'ogg-player-thumbnail'    => 'Imatge estatic solament',
	'ogg-player-soundthumb'   => 'Cap de lector',
	'ogg-player-selected'     => '(seleccionat)',
	'ogg-use-player'          => 'Utilizar lo lector :',
	'ogg-more'                => 'Mai…',
	'ogg-dismiss'             => 'Tampar',
	'ogg-download'            => 'Telecargar lo fichièr',
	'ogg-desc-link'           => "A prepaus d'aqueste fichièr",
);

/** Polish (Polski)
 * @author Derbeth
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'ogg-desc'                => 'Obsługa dla Ogg z kodekami obrazu Theora oraz dźwięku Vobis z odtwarzaczem w JavaScript',
	'ogg-short-audio'         => 'Plik dźwiękowy Ogg $1, $2',
	'ogg-short-video'         => 'Plik wideo Ogg $1, $2',
	'ogg-short-general'       => 'Plik multimedialny Ogg $1, $2',
	'ogg-long-audio'          => '(Plik dźwiękowy Ogg $1, długość $2, $3)',
	'ogg-long-video'          => '(Plik wideo Ogg $1, długość $2, rozdzielczość $4×$5, $3)',
	'ogg-long-multiplexed'    => '(Plik audio/wideo Ogg, $1, długość $2, rozdzielczość $4×$5, ogółem $3)',
	'ogg-long-general'        => '(Plik multimedialny Ogg, długość $2, $3)',
	'ogg-long-error'          => '(Niepoprawny plik Ogg: $1)',
	'ogg-play'                => 'Odtwórz',
	'ogg-pause'               => 'Pauza',
	'ogg-stop'                => 'Stop',
	'ogg-play-video'          => 'Odtwórz wideo',
	'ogg-play-sound'          => 'Odtwórz dźwięk',
	'ogg-no-player'           => 'Twój system nie ma żadnego programu do obsługi odtwarzania. Prosimy o <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/pl">pobranie i zainstalowanie odtwarzacza</a>.',
	'ogg-no-xiphqt'           => 'Brak komponentu XiphQT dla programu QuickTime. QuickTime nie może odtwarzać plików Ogg bez tego komponentu. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/pl">Pobierz XiphQT</a> lub użyj innego odtwarzacza.',
	'ogg-player-videoElement' => 'element <video>',
	'ogg-player-oggPlugin'    => 'wtyczka Ogg',
	'ogg-player-thumbnail'    => 'Tylko nieruchomy obraz',
	'ogg-player-soundthumb'   => 'Brak odtwarzacza',
	'ogg-player-selected'     => '(wybrany)',
	'ogg-use-player'          => 'Użyj odtwarzacza:',
	'ogg-more'                => 'Więcej...',
	'ogg-dismiss'             => 'Zamknij',
	'ogg-download'            => 'Pobierz plik',
	'ogg-desc-link'           => 'Właściwości pliku',
);

$messages['pms'] = array(
	'ogg-short-audio' => 'Registrassion Ogg $1, $2',
	'ogg-short-video' => 'Film Ogg $1, $2',
	'ogg-short-general' => 'Archivi Multimojen Ogg $1, $2',
	'ogg-long-audio' => '(Registrassion Ogg $1, ch\'a dura $2, $3)',
	'ogg-long-video' => '(Film Ogg $1, ch\'a dura $2, formà $4×$5 px, $3)',
	'ogg-long-multiplexed' => '(Archivi audio/video multiplessà Ogg, $1, ch\'a dura $2, formà $4×$5 px, $3 an tut)',
	'ogg-long-general' => '(Archivi multimojen Ogg, ch\'a dura $2, $3)',
	'ogg-long-error' => '(Archivi ogg nen bon: $1)',
	'ogg-play' => 'Smon',
	'ogg-pause' => 'Pàusa',
	'ogg-stop' => 'Fërma',
	'ogg-play-video' => 'Smon ël film',
	'ogg-play-sound' => 'Smon ël sonòr',
	'ogg-no-player' => 'Darmagi, ma sò calcolator a smija ch\'a l\'abia pa gnun programa ch\'a peul smon-e dj\'archivi multi-mojen. Për piasì <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ch\'as në dëscaria un</a>.',
	'ogg-no-xiphqt' => 'A smija che ansima a sò calcolator a-i sia nen ël component XiphQT dël programa QuickTime. QuickTime a-i la fa pa a dovré dj\'archivi an forma Ogg files s\'a l\'ha nen ës component-lì. Për piasì <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ch\'as dëscaria XiphQT</a> ò pura ch\'as sërna n\'àotr programa për dovré j\'archivi multi-mojen.',
	'ogg-player-videoElement' => 'element <video>',
	'ogg-player-oggPlugin' => 'Spinòt për Ogg',
	'ogg-player-thumbnail' => 'Mach na figurin-a fissa',
	'ogg-player-soundthumb' => 'Gnun programa për vardé/scoté',
	'ogg-player-selected' => '(selessionà)',
	'ogg-use-player' => 'Dovré ël programa:',
	'ogg-more' => 'Dë pì...',
	'ogg-dismiss' => 'sëré',
	'ogg-download' => 'Dëscarié l\'archivi',
	'ogg-desc-link' => 'Rësgoard a st\'archivi',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'ogg-short-audio'   => 'Ogg $1 غږيزه دوتنه، $2',
	'ogg-short-video'   => 'Ogg $1 ويډيويي دوتنه، $2',
	'ogg-short-general' => 'Ogg $1 رسنيزه دوتنه، $2',
	'ogg-play'          => 'غږول',
	'ogg-stop'          => 'درول',
	'ogg-play-video'    => 'ويډيو غږول',
	'ogg-play-sound'    => 'غږ غږول',
	'ogg-more'          => 'نور...',
	'ogg-dismiss'       => 'تړل',
	'ogg-download'      => 'دوتنه ښکته کول',
	'ogg-desc-link'     => 'د همدې دوتنې په اړه',
);

/** Portuguese (Português)
 * @author 555
 * @author Malafaya
 */
$messages['pt'] = array(
	'ogg-desc'                => 'Manuseador para ficheiros Ogg Theora e Vorbis, com reprodutor JavaScript',
	'ogg-short-audio'         => 'Áudio Ogg $1, $2',
	'ogg-short-video'         => 'Vídeo Ogg $1, $2',
	'ogg-short-general'       => 'Multimédia Ogg $1, $2',
	'ogg-long-audio'          => '(Áudio Ogg $1, $2 de duração, $3)',
	'ogg-long-video'          => '(Vídeo Ogg $1, $2 de duração, $4×$5 pixels, $3)',
	'ogg-long-multiplexed'    => '(Áudio/vídeo Ogg multifacetado, $1, $2 de duração, $4×$5 pixels, $3 no todo)',
	'ogg-long-general'        => '(Multimédia Ogg, $2 de duração, $3)',
	'ogg-long-error'          => '(Ficheiro ogg inválido: $1)',
	'ogg-play'                => 'Reproduzir',
	'ogg-pause'               => 'Pausar',
	'ogg-stop'                => 'Parar',
	'ogg-play-video'          => 'Reproduzir vídeo',
	'ogg-play-sound'          => 'Reproduzir som',
	'ogg-no-player'           => 'Lamentamos, mas seu sistema aparenta não ter um player suportado. Por gentileza, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">faça o download de um player</a>.',
	'ogg-no-xiphqt'           => 'Aparentemente você não tem o componente XiphQT para QuickTime. Não será possível reproduzir ficheiros Ogg pelo QuickTime sem tal componente. Por gentileza, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">faça o download do XiphQT</a> ou escolha outro reprodutor.',
	'ogg-player-videoElement' => 'elemento <video>',
	'ogg-player-oggPlugin'    => 'Plugin Ogg',
	'ogg-player-thumbnail'    => 'Apenas imagem estática',
	'ogg-player-soundthumb'   => 'Sem player',
	'ogg-player-selected'     => '(selecionado)',
	'ogg-use-player'          => 'Usar player:',
	'ogg-more'                => 'Mais...',
	'ogg-dismiss'             => 'Fechar',
	'ogg-download'            => 'Fazer download do ficheiro',
	'ogg-desc-link'           => 'Sobre este ficheiro',
);

/** Quechua (Runa Simi)
 * @author AlimanRuna
 */
$messages['qu'] = array(
	'ogg-play'              => 'Waqachiy',
	'ogg-pause'             => "P'itiy",
	'ogg-stop'              => 'Tukuchiy',
	'ogg-play-video'        => 'Widyuta rikuchiy',
	'ogg-play-sound'        => 'Ruqyayta uyarichiy',
	'ogg-player-soundthumb' => 'Manam waqachiqchu',
	'ogg-player-selected'   => '(akllasqa)',
	'ogg-use-player'        => "Kay waqachiqta llamk'achiy:",
	'ogg-more'              => 'Astawan...',
	'ogg-dismiss'           => "Wichq'ay",
	'ogg-download'          => 'Willañiqita chaqnamuy',
	'ogg-desc-link'         => 'Kay willañiqimanta',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'ogg-desc'                => 'Обработчик файлов Ogg Theora и Vorbis с использованием JavaScript-проигрывателя',
	'ogg-short-audio'         => 'Звуковой файл Ogg $1, $2',
	'ogg-short-video'         => 'Видео-файл Ogg $1, $2',
	'ogg-short-general'       => 'Медиа-файл Ogg $1, $2',
	'ogg-long-audio'          => '(звуковой файл Ogg $1, длина $2, $3)',
	'ogg-long-video'          => '(видео-файл Ogg $1, длина $2, $4×$5 пикселов, $3)',
	'ogg-long-multiplexed'    => '(мультиплексный аудио/видео-файл Ogg, $1, длина $2, $4×$5 пикселов, $3 всего)',
	'ogg-long-general'        => '(медиа-файл Ogg, длина $2, $3)',
	'ogg-long-error'          => '(неправильный ogg-файл: $1)',
	'ogg-play'                => 'Воспроизвести',
	'ogg-pause'               => 'Пауза',
	'ogg-stop'                => 'Остановить',
	'ogg-play-video'          => 'Воспроизвести видео',
	'ogg-play-sound'          => 'Воспроизвести звук',
	'ogg-no-player'           => 'Извините, ваша система не имеет необходимого программного обеспечение для воспроизведения файлов. Пожалуйста, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">скачайте проигрыватель</a>.',
	'ogg-no-xiphqt'           => 'Отсутствует компонент XiphQT для QuickTime. QuickTime не может воспроизвести файл Ogg без этого компонента. Пожалуйста, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">скачайте XiphQT</a> или выберите другой проигрыватель.',
	'ogg-player-videoElement' => 'элемент <video>',
	'ogg-player-oggPlugin'    => 'Ogg модуль',
	'ogg-player-thumbnail'    => 'Только неподвижное изображение',
	'ogg-player-soundthumb'   => 'Нет проигрывателя',
	'ogg-player-selected'     => '(выбран)',
	'ogg-use-player'          => 'Использовать проигрыватель: ',
	'ogg-more'                => 'Больше…',
	'ogg-dismiss'             => 'Скрыть',
	'ogg-download'            => 'Загрузить файл',
	'ogg-desc-link'           => 'Информация об этом файле',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'ogg-desc'                => 'Обработчик файлов Ogg Theora и Vorbis с использованием JavaScript-проигрывателя',
	'ogg-short-audio'         => 'Звуковой файл Ogg $1, $2',
	'ogg-short-video'         => 'Видео-файл Ogg $1, $2',
	'ogg-short-general'       => 'Медиа-файл Ogg $1, $2',
	'ogg-long-audio'          => '(звуковой файл Ogg $1, уһуна $2, $3)',
	'ogg-long-video'          => '(видео-файл Ogg $1, уһуна $2, $4×$5 пииксэллээх, $3)',
	'ogg-long-multiplexed'    => '(мультиплексный аудио/видео-файл Ogg, $1, уһуна $2, $4×$5 пииксэллээх, барыта $3)',
	'ogg-long-general'        => '(медиа-файл Ogg, уһуна $2, $3)',
	'ogg-long-error'          => '(сыыһа ogg-файл: $1)',
	'ogg-play'                => 'Оонньот',
	'ogg-pause'               => 'Тохтото түс',
	'ogg-stop'                => 'Тохтот',
	'ogg-play-video'          => 'Көрдөр',
	'ogg-play-sound'          => 'Иһитиннэр',
	'ogg-no-player'           => 'Хомойуох иһин эн систиэмэҕэр иһитиннэрэр/көрдөрөр анал бырагырааммалар суохтар эбит. Бука диэн, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">плееры хачайдан</a>.',
	'ogg-no-xiphqt'           => 'QuickTime маннык тэрээбэтэ: XiphQT суох эбит. Онон QuickTime бу Ogg билэни (файлы) оонньотор кыаҕа суох. Бука диэн, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> XiphQT хачайдан</a> эбэтэр атын плееры тал.',
	'ogg-player-videoElement' => '<video> элэмиэнэ',
	'ogg-player-oggPlugin'    => 'Ogg плагин',
	'ogg-player-thumbnail'    => 'Хамсаабат ойууну эрэ',
	'ogg-player-soundthumb'   => 'Плеер суох',
	'ogg-player-selected'     => '(талыллыбыт)',
	'ogg-use-player'          => 'Бу плееры туттарга:',
	'ogg-more'                => 'Өссө...',
	'ogg-dismiss'             => 'Кистээ/сап',
	'ogg-download'            => 'Билэни хачайдаа',
	'ogg-desc-link'           => 'Бу билэ туһунан',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'ogg-desc'                => 'Obsluha súborov Ogg Theora a Vorbis s JavaScriptovým prehrávačom',
	'ogg-short-audio'         => 'Zvukový súbor ogg $1, $2',
	'ogg-short-video'         => 'Video súbor ogg $1, $2',
	'ogg-short-general'       => 'Multimediálny súbor ogg $1, $2',
	'ogg-long-audio'          => '(Zvukový súbor ogg $1, dĺžka $2, $3)',
	'ogg-long-video'          => '(Video súbor ogg $1, dĺžka $2, $3)',
	'ogg-long-multiplexed'    => '(Multiplexovaný zvukový/video súbor ogg, $1, dĺžka $2, $4×$5 pixelov, $3 celkom)',
	'ogg-long-general'        => '(Multimediálny súbor ogg $1, dĺžka $2, $3)',
	'ogg-long-error'          => '(Neplatný súbor ogg: $1)',
	'ogg-play'                => 'Prehrať',
	'ogg-pause'               => 'Pozastaviť',
	'ogg-stop'                => 'Zastaviť',
	'ogg-play-video'          => 'Prehrať video',
	'ogg-play-sound'          => 'Prehrať zvuk',
	'ogg-no-player'           => 'Prepáčte, zdá sa, že váš systém nemá žiadny podporovaný softvér na prehrávanie. Prosím, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">stiahnite si prehrávač</a>.',
	'ogg-no-xiphqt'           => 'Zdá sa, že nemáte komponent QuickTime XiphQT. QuickTime nedokáže prehrávať ogg súbory bez tohto komponentu. Prosím, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">stiahnite si XiphQT</a> alebo si vyberte iný prehrávač.',
	'ogg-player-videoElement' => 'element <video>',
	'ogg-player-oggPlugin'    => 'zásovný modul ogg',
	'ogg-player-thumbnail'    => 'iba nepohyblivý obraz',
	'ogg-player-soundthumb'   => 'žiadny prehrávač',
	'ogg-player-selected'     => '(vybraný)',
	'ogg-use-player'          => 'Použiť prehrávač:',
	'ogg-more'                => 'viac...',
	'ogg-dismiss'             => 'Zatvoriť',
	'ogg-download'            => 'Stiahnuť súbor',
	'ogg-desc-link'           => 'O tomto súbore',
);

/** ћирилица (ћирилица)
 * @author Sasa Stefanovic
 */
$messages['sr-ec'] = array(
	'ogg-play'       => 'Пусти',
	'ogg-pause'      => 'Пауза',
	'ogg-stop'       => 'Стоп',
	'ogg-play-video' => 'Пусти видео',
	'ogg-play-sound' => 'Пусти звук',
	'ogg-more'       => 'Више...',
	'ogg-dismiss'    => 'Затвори',
	'ogg-download'   => 'Преузми фајл',
	'ogg-desc-link'  => 'О овом фајлу',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'ogg-short-audio'         => 'Ogg-$1-Audiodoatäi, $2',
	'ogg-short-video'         => 'Ogg-$1-Videodoatäi, $2',
	'ogg-short-general'       => 'Ogg-$1-Mediadoatäi, $2',
	'ogg-long-audio'          => '(Ogg-$1-Audiodoatäi, Loangte: $2, $3)',
	'ogg-long-video'          => '(Ogg-$1-Videodoatäi, Loangte: $2, $4×$5 Pixel, $3)',
	'ogg-long-multiplexed'    => '(Ogg-Audio-/Video-Doatäi, $1, Loangte: $2, $4×$5 Pixel, $3)',
	'ogg-long-general'        => '(Ogg-Mediadoatäi, Loangte: $2, $3)',
	'ogg-long-error'          => '(Uungultige Ogg-Doatäi: $1)',
	'ogg-play'                => 'Start',
	'ogg-pause'               => 'Pause',
	'ogg-stop'                => 'Stop',
	'ogg-play-video'          => 'Video ouspielje',
	'ogg-play-sound'          => 'Audio ouspielje',
	'ogg-no-player'           => 'Dien System schient uur neen Ouspielsoftware tou ferföigjen. Installier <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ne Ouspielsoftware</a>.',
	'ogg-no-xiphqt'           => 'Dien System schient nit uur ju XiphQT-Komponente foar QuickTime tou ferföigjen. QuickTime kon sunner disse Komponente neen Ogg-Doatäie ouspielje. 
Dou <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">leede XiphQT</a> of wääl ne uur Ouspielsoftware.',
	'ogg-player-videoElement' => '<video>-Element>',
	'ogg-player-oggPlugin'    => 'Ogg-Plugin',
	'ogg-player-thumbnail'    => 'Wies Foarschaubielde',
	'ogg-player-soundthumb'   => 'Naan Player',
	'ogg-player-selected'     => '(uutwääld)',
	'ogg-use-player'          => 'Ouspielsoftware:',
	'ogg-more'                => 'Optione …',
	'ogg-dismiss'             => 'Sluute',
	'ogg-download'            => 'Doatäi spiekerje',
	'ogg-desc-link'           => 'Uur disse Doatäi',
);

/** Swedish (Svenska)
 * @author Lejonel
 */
$messages['sv'] = array(
	'ogg-desc'                => 'Stöder filtyperna Ogg Theora och Ogg Vorbis med en JavaScript-baserad mediaspelare',
	'ogg-short-audio'         => 'Ogg $1 ljudfil, $2',
	'ogg-short-video'         => 'Ogg $1 videofil, $2',
	'ogg-short-general'       => 'Ogg $1 mediafil, $2',
	'ogg-long-audio'          => '(Ogg $1 ljudfil, längd $2, $3)',
	'ogg-long-video'          => '(Ogg $1 videofil, längd $2, $4×$5 pixel, $3)',
	'ogg-long-multiplexed'    => '(Ogg multiplexad ljud/video-fil, $1, längd $2, $4×$5 pixel, $3 totalt)',
	'ogg-long-general'        => '(Ogg mediafil, längd $2, $3)',
	'ogg-long-error'          => '(Felaktig ogg-fil: $1)',
	'ogg-play'                => 'Spela upp',
	'ogg-pause'               => 'Pausa',
	'ogg-stop'                => 'Stoppa',
	'ogg-play-video'          => 'Spela upp video',
	'ogg-play-sound'          => 'Spela upp ljud',
	'ogg-no-player'           => 'Tyvärr verkar det inte finnas någon mediaspelare som stöds installerad i ditt system. Det finns <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">spelare att ladda ner</a>.',
	'ogg-no-xiphqt'           => 'Du verkar inte ha XiphQT-komponenten för QuickTime. Utan den kan inte QuickTime spela upp ogg-filer.Du kan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ladda ner XiphQT</a> eller välja någon annan spelare.',
	'ogg-player-videoElement' => '<video>-element',
	'ogg-player-oggPlugin'    => 'Ogg-plugin',
	'ogg-player-thumbnail'    => 'Endast stillbilder',
	'ogg-player-soundthumb'   => 'Ingen spelare',
	'ogg-player-selected'     => '(vald)',
	'ogg-use-player'          => 'Välj mediaspelare: ',
	'ogg-more'                => 'Alternativ...',
	'ogg-dismiss'             => 'Stäng',
	'ogg-download'            => 'Ladda ner filen',
	'ogg-desc-link'           => 'Om filen',
);

/** Telugu (తెలుగు)
 * @author Veeven
 * @author వైజాసత్య
 */
$messages['te'] = array(
	'ogg-short-audio'         => 'Ogg $1 శ్రావ్యక ఫైలు, $2',
	'ogg-short-video'         => 'Ogg $1 వీడియో ఫైలు, $2',
	'ogg-short-general'       => 'Ogg $1 మీడియా ఫైలు, $2',
	'ogg-long-audio'          => '(Ogg $1 శ్రవణ ఫైలు, నిడివి $2, $3)',
	'ogg-long-video'          => '(Ogg $1 వీడియో ఫైలు, నిడివి $2, $4×$5 పిక్సెళ్ళు, $3)',
	'ogg-long-multiplexed'    => '(ఓగ్ మల్టిప్లెక్సుడ్ శ్రవణ/దృశ్యక ఫైలు, $1, నిడివి $2, $4×$5 పిక్సెళ్ళు, $3 మొత్తం)',
	'ogg-long-general'        => '(Ogg మీడియా ఫైలు, నిడివి $2, $3)',
	'ogg-long-error'          => '(తప్పుడు ogg ఫైలు: $1)',
	'ogg-play'                => 'ఆడించు',
	'ogg-pause'               => 'ఆపు',
	'ogg-stop'                => 'ఆపివేయి',
	'ogg-play-video'          => 'వీడియోని ఆడించు',
	'ogg-play-sound'          => 'శబ్ధాన్ని వినిపించు',
	'ogg-player-videoElement' => '<video> మూలకం',
	'ogg-player-oggPlugin'    => 'Ogg ప్లగిన్',
	'ogg-player-thumbnail'    => 'నిచ్చల చిత్రాలు మాత్రమే',
	'ogg-player-soundthumb'   => 'ప్లేయర్ లేదు',
	'ogg-player-selected'     => '(ఎంచుకున్నారు)',
	'ogg-use-player'          => 'ప్లేయర్ ఉపయోగించు:',
	'ogg-more'                => 'మరిన్ని...',
	'ogg-dismiss'             => 'మూసివేయి',
	'ogg-download'            => 'ఫైలుని దిగుమతి చేసుకోండి',
	'ogg-desc-link'           => 'ఈ ఫైలు గురించి',
);

/** Tajik (Тоҷикӣ)
 * @author Ibrahim
 */
$messages['tg'] = array(
	'ogg-play'              => 'Пахш',
	'ogg-pause'             => 'Сукут',
	'ogg-stop'              => 'Қатъ',
	'ogg-play-video'        => 'Пахши навор',
	'ogg-play-sound'        => 'Пахши овоз',
	'ogg-player-soundthumb' => 'Пахшкунанда нест',
	'ogg-player-selected'   => '(интихобшуда)',
	'ogg-use-player'        => 'Истифода аз пахшкунанда:',
	'ogg-more'              => 'Бештар...',
	'ogg-dismiss'           => 'Бастан',
	'ogg-download'          => 'Боргирии парванда',
	'ogg-desc-link'         => 'Дар бораи ин парванда',
);

/** Turkish (Türkçe)
 * @author Erkan Yilmaz
 * @author Srhat
 */
$messages['tr'] = array(
	'ogg-short-audio'       => 'Ogg $1 ses dosyası, $2',
	'ogg-short-video'       => 'Ogg $1 film dosyası, $2',
	'ogg-short-general'     => 'Ogg $1 medya dosyası, $2',
	'ogg-long-audio'        => '(Ogg $1 ses dosyası, süre $2, $3)',
	'ogg-long-video'        => '(Ogg $1 film dosyası, süre $2, $4×$5 piksel, $3)',
	'ogg-long-multiplexed'  => '(Ogg çok düzeyli ses/film dosyası, $1, süre $2, $4×$5 piksel, $3 genelde)',
	'ogg-long-general'      => '(Ogg medya dosyası, süre $2, $3)',
	'ogg-long-error'        => '(Geçersiz ogg dosyası: $1)',
	'ogg-play'              => 'Oynat',
	'ogg-pause'             => 'Duraklat',
	'ogg-stop'              => 'Durdur',
	'ogg-play-video'        => 'Video filmini oynat',
	'ogg-play-sound'        => 'Sesi oynat',
	'ogg-player-oggPlugin'  => 'Ogg eklentisi',
	'ogg-player-soundthumb' => 'Oynatıcı yok',
	'ogg-player-selected'   => '(seçilmiş)',
	'ogg-more'              => 'Daha...',
	'ogg-dismiss'           => 'Kapat',
	'ogg-desc-link'         => 'Bu dosya hakkında',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'ogg-desc'                => 'Bộ trình bày các tập tin Ogg Theora và Vorbis dùng hộp chơi phương tiện bằng JavaScript',
	'ogg-short-audio'         => 'Tập tin âm thanh Ogg $1, $2',
	'ogg-short-video'         => 'Tập tin video Ogg $1, $2',
	'ogg-short-general'       => 'Tập tin Ogg $1, $2',
	'ogg-long-audio'          => '(tập tin âm thanh Ogg $1, dài $2, $3)',
	'ogg-long-video'          => '(tập tin video Ogg $1, dài $2, $4×$5 điểm ảnh, $3)',
	'ogg-long-multiplexed'    => '(tập tin Ogg có âm thanh và video ghép kênh, $1, dài $2, $4×$5 điểm ảnh, $3 tất cả)',
	'ogg-long-general'        => '(tập tin phương tiện Ogg, dài $2, $3)',
	'ogg-long-error'          => '(Tập tin Ogg có lỗi: $1)',
	'ogg-play'                => 'Chơi',
	'ogg-pause'               => 'Tạm ngừng',
	'ogg-stop'                => 'Ngừng',
	'ogg-play-video'          => 'Coi video',
	'ogg-play-sound'          => 'Nghe âm thanh',
	'ogg-no-player'           => 'Rất tiếc, hình như máy tính của bạn cần thêm phần mềm. Xin <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/vi">tải xuống chương trình chơi nhạc</a>.',
	'ogg-no-xiphqt'           => 'Hình như bạn không có bộ phận XiphQT cho QuickTime, nên QuickTime không thể chơi những tập tin Ogg được. Xin <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/vi">truyền xuống XiphQT</a> hay chọn một chương trình chơi nhạc khác.',
	'ogg-player-videoElement' => 'Phần tử <video>',
	'ogg-player-oggPlugin'    => 'Ogg (plugin)',
	'ogg-player-thumbnail'    => 'Chỉ hiển thị hình tĩnh',
	'ogg-player-soundthumb'   => 'Tắt',
	'ogg-player-selected'     => '(được chọn)',
	'ogg-use-player'          => 'Chọn chương trình chơi:',
	'ogg-more'                => 'Thêm nữa…',
	'ogg-dismiss'             => 'Đóng',
	'ogg-download'            => 'Tải tập tin xuống',
	'ogg-desc-link'           => 'Chi tiết của tập tin này',
);

/** Volapük (Volapük)
 * @author Malafaya
 * @author Smeira
 */
$messages['vo'] = array(
	'ogg-player-videoElement' => 'Dilet: <video>',
	'ogg-more'                => 'Pluikos...',
	'ogg-dismiss'             => 'Färmükön',
	'ogg-desc-link'           => 'Tefü ragiv at',
);

$messages['yue'] = array(
	'ogg-short-audio'      => 'Ogg $1 聲檔，$2',
	'ogg-short-video'      => 'Ogg $1 畫檔，$2',
	'ogg-short-general'    => 'Ogg $1 媒檔，$2',
	'ogg-long-audio'       => '(Ogg $1 聲檔，長度$2，$3)',
	'ogg-long-video'       => '(Ogg $1 畫檔，長度$2，$4×$5像素，$3)',
	'ogg-long-multiplexed' => '(Ogg 多工聲／畫檔，$1，長度$2，$4×$5像素，總共$3)',
	'ogg-long-general'     => '(Ogg 媒檔，長度$2，$3)',
	'ogg-long-error'       => '(無效嘅ogg檔: $1)',
	'ogg-play'             => '去',
	'ogg-pause'            => '暫停',
	'ogg-stop'             => '停',
	'ogg-play-video'       => '去畫',
	'ogg-play-sound'       => '去聲',
	'ogg-no-player'        => '對唔住，你嘅系統並無任何可以支援得到嘅播放器。' .
		'請安裝<a href="http://www.java.com/zh_TW/download/manual.jsp">Java</a>。',
	'ogg-no-xiphqt'        => '你似乎無畀QuickTime用嘅XiphQT組件。' .
		'響未有呢個組件嗰陣，QuickTime係唔可以播放Ogg檔案。' .
		'請<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">下載XiphQT</a>或者揀過另外一個播放器。',

	'ogg-player-videoElement' => '<video>元素',
	'ogg-player-oggPlugin' => 'Ogg插件',
	'ogg-player-thumbnail' => '只有靜止圖像',
	'ogg-player-soundthumb' => '無播放器',
	'ogg-player-selected'  => '(揀咗)',
	'ogg-use-player'       => '使用播放器: ',
	'ogg-more'             => '更多...',
	'ogg-dismiss'          => '閂',
	'ogg-download'         => '下載檔案',
	'ogg-desc-link'        => '關於呢個檔案',
);

$messages['zh-hans'] = array(
	'ogg-short-audio'      => 'Ogg $1 声音文件，$2',
	'ogg-short-video'      => 'Ogg $1 视频文件，$2',
	'ogg-short-general'    => 'Ogg $1 媒体文件，$2',
	'ogg-long-audio'       => '(Ogg $1 声音文件，长度$2，$3)',
	'ogg-long-video'       => '(Ogg $1 视频文件，长度$2，$4×$5像素，$3)',
	'ogg-long-multiplexed' => '(Ogg 多工声音／视频文件，$1，长度$2，$4×$5像素，共$3)',
	'ogg-long-general'     => '(Ogg 媒体文件，长度$2，$3)',
	'ogg-long-error'       => '(无效的ogg文件: $1)',
	'ogg-play'             => '播放',
	'ogg-pause'            => '暂停',
	'ogg-stop'             => '停止',
	'ogg-play-video'       => '播放视频',
	'ogg-play-sound'       => '播放声音',
	'ogg-no-player'        => '抱歉，您的系统并无任何可以支持播放的播放器。' .
		'请安装<a href="http://www.java.com/zh_CN/download/manual.jsp">Java</a>。',
	'ogg-no-xiphqt'        => '您似乎没有给QuickTime用的XiphQT组件。' .
		'在未有这个组件的情况下，QuickTime是不能播放Ogg文件的。' .
		'请<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">下载XiphQT</a>或者选取另一个播放器。',

	'ogg-player-videoElement' => '<video>元素',
	'ogg-player-oggPlugin' => 'Ogg插件',
	'ogg-player-thumbnail' => '只有静止图像',
	'ogg-player-soundthumb' => '沒有播放器',
	'ogg-player-selected'  => '(已选取)',
	'ogg-use-player'       => '使用播放器: ',
	'ogg-more'             => '更多...',
	'ogg-dismiss'          => '关闭',
	'ogg-download'         => '下载文件',
	'ogg-desc-link'        => '关于这个文件',
);

$messages['zh-hant'] = array(
	'ogg-short-audio'      => 'Ogg $1 聲音檔案，$2',
	'ogg-short-video'      => 'Ogg $1 影片檔案，$2',
	'ogg-short-general'    => 'Ogg $1 媒體檔案，$2',
	'ogg-long-audio'       => '(Ogg $1 聲音檔案，長度$2，$3)',
	'ogg-long-video'       => '(Ogg $1 影片檔案，長度$2，$4×$5像素，$3)',
	'ogg-long-multiplexed' => '(Ogg 多工聲音／影片檔案，$1，長度$2，$4×$5像素，共$3)',
	'ogg-long-general'     => '(Ogg 媒體檔案，長度$2，$3)',
	'ogg-long-error'       => '(無效的ogg檔案: $1)',
	'ogg-play'             => '播放',
	'ogg-pause'            => '暫停',
	'ogg-stop'             => '停止',
	'ogg-play-video'       => '播放影片',
	'ogg-play-sound'       => '播放聲音',
	'ogg-no-player'        => '抱歉，您的系統並無任何可以支援播放的播放器。' .
		'請安裝<a href="http://www.java.com/zh_TW/download/manual.jsp">Java</a>。',
	'ogg-no-xiphqt'        => '您似乎沒有給QuickTime用的XiphQT組件。' .
		'在未有這個組件的情況下，QuickTime是不能播放Ogg檔案的。' .
		'請<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">下載XiphQT</a>或者選取另一個播放器。',

	'ogg-player-videoElement' => '<video>元素',
	'ogg-player-oggPlugin' => 'Ogg插件',
	'ogg-player-thumbnail' => '只有靜止圖像',
	'ogg-player-soundthumb' => '沒有播放器',
	'ogg-player-selected'  => '(已選取)',
	'ogg-use-player'       => '使用播放器: ',
	'ogg-more'             => '更多...',
	'ogg-dismiss'          => '關閉',
	'ogg-download'         => '下載檔案',
	'ogg-desc-link'        => '關於這個檔案',
);

