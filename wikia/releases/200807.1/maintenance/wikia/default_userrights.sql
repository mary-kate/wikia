delete from user_groups where ug_user in (
/*Adi3ek*/259228,
/*Angela*/2,
/*AngieS*/67261,
/*AnnieC*/160138,
/*Awrigh01*/67758,
/*Avatar*/349903,
/*BartL*/80238,
/*Bhadani*/37197,
/*BillK*/38903,
/*BladeBronson*/140142,
/*CatherineMunro*/108559,
/*Derek*/144146,
/*DNL*/56870,
/*Doug*/261184,
/*Emil*/27301,
/*Eloy.wikia*/51098,
/*Galezewski*/189276,
/*Gil*/20251,
/*Inez*/51654,
/*Jasonr*/1,
/*Jimbo Wales*/13,
/*Johnq*/24316,
/*JSharp*/39018,
/*KaurJmeb*/23838,
/*Kirkburn*/126761,
/*KyleH*/265264,
/*Macbre*/119245,
/*MatthewS*/60035,
/*Mdavis*/11636,
/*Pean*/66574,
/*Ppiotr*/60069,
/*Roblefko*/20151,
/*Scarecroe*/10637,
/*Sannse*/8,
/*Tomsen*/26682,
/*TOR*/23865,
/*Toughpigs*/10370,
/*Yukichi99*/126117,
/*Zuirdj*/47,
/*Wiffle*/305670
) and ug_group="staff";
insert into user_groups(ug_user, ug_group) values
(/*Adi3ek*/259228,'staff'),
(/*Angela*/2,'staff'),
(/*AngieS*/67261,'staff'),
(/*AnnieC*/160138,'staff'),
(/*Awrigh01*/67758,'staff'),
(/*Avatar*/349903, 'staff'),
(/*BartL*/80238,'staff'),
(/*Bhadani*/37197,'staff'),
(/*BillK*/38903,'staff'),
(/*BladeBronson*/140142,'staff'),
(/*CatherineMunro*/108559,'staff'),
(/*Derek*/144146,'staff'),
(/*DNL*/56870,'staff'),
(/*Doug*/261184,'staff'),
(/*Emil*/27301,'staff'),
(/*Eloy.wikia*/51098,'staff'),
(/*Galezewski*/189276,'staff'),
(/*Gil*/20251,'staff'),
(/*Inez*/51654,'staff'),
(/*Jasonr*/1,'staff'),
(/*Jimbo Wales*/13,'staff'),
(/*Johnq*/24316,'staff'),
(/*JSharp*/39018,'staff'),
(/*KaurJmeb*/23838,'staff'),
(/*Kirkburn*/126761,'staff'),
(/*KyleH*/265264,'staff'),
(/*Macbre*/119245,'staff'),
(/*MatthewS*/60035,'staff'),
(/*Mdavis*/11636,'staff'),
(/*Pean*/66574,'staff'),
(/*Ppiotr*/60069,'staff'),
(/*Roblefko*/20151,'staff'),
(/*Scarecroe*/10637, 'staff'),
(/*Sannse*/8,'staff'),
(/*Tomsen*/26682, 'staff'),
(/*TOR*/23865,'staff'),
(/*Toughpigs*/10370,'staff'),
(/*Yukichi99*/126117,'staff'),
(/*Zuirdj*/47,'staff'),
(/*Wiffle*/305670,'staff')
;

delete from user_groups where ug_user in (
/*Jasonr*/1
) and ug_group="searchadmin";
insert into user_groups(ug_user, ug_group) values
(/*Jasonr*/1,'searchadmin')
;

delete from user_groups where ug_user in (
/*GHe*/21944,
/*Greyman*/87167,
/*JackPhenix*/36762,
/*Jaymach*/7701,
/*Uberfuzzy*/161697
) and ug_group="janitor";
insert into user_groups(ug_user, ug_group) values
(/*GHe*/21944,'janitor'),
(/*Greyman*/87167,'janitor'),
(/*JackPhoenix*/36762,'janitor'),
(/*Jaymach*/7701,'janitor'),
(/*Uberfuzzy*/161697, 'janitor')
;

delete from user_groups where ug_user in (
/*AnnElida*/792759,
/*Datrio*/57466,
/*Jnewcombe*/801589,
/*JoePlay*/171752,
/*MeatMan*/226254,
/*Merrystar*/11001,
/*Multimoog*/20290,
/*Muppets101*/77907,
/*Ozzel*/11024,
/*PanSola*/12719,
/*Peteparker*/122657,
/*Rieke Hain*/26246,
/*Richard1990*/25261,
/*Splarka*/8245,
/*Tommy6*/239851
) and ug_group="helper";
insert into user_groups(ug_user, ug_group) values
(/*AnnElida*/792759,'helper'),
(/*Datrio*/57466,'helper'),
(/*Jnewcombe*/801589,'helper'),
(/*JoePlay*/171752,'helper'),
(/*MeatMan*/226254,'helper'),
(/*Merrystar*/11001,'helper'),
(/*Muppets101*/77907,'helper'),
(/*Multimoog*/20290,'helper'),
(/*Ozzel*/11024, 'helper'),
(/*PanSola*/12719,'helper'),
(/*Peteparker*/122657, 'helper'),
(/*Rieke Hain*/26246,'helper'),
(/*Richard1990*/25261,'helper'),
(/*Splarka*/8245,'helper'),
(/*Tommy6*/239851,'helper')
;

delete from user_groups where ug_user in (
/*Default*/49312,
/*WikiaBot*/269919
) and ug_group="bot";
insert into user_groups(ug_user, ug_group) values
(/*Default*/49312,'bot'),
(/*WikiaBot*/269919,'bot')
;
