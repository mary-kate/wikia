<?
global $wgTitle, $wgArticle, $wgOut;

//if ($wgTitle->isContentPage() && !ArticleAdLogic::isMainPage()) {
if ( $wgTitle->exists() && $wgTitle->isContentPage() && !$wgTitle->isTalkPage() && $wgOut->isArticle() ) {
?>
<style type="text/css">
#page_bar {
	display: none;
}
.firstHeading {
	border: 0;
	color: #000;
	font-family: "Arial Narrow", "Arial", sans-serif;
	font-weight: bold;
	line-height: normal;
	padding: 20px 10px 0;
}
/*
.firstHeading:before { 
	content: "Question: "; 
}
*/
.firstHeading:before, .answer_a, .google_ad_header {
	color: #090;
	font-family: "Arial", sans-serif;
	font-weight: normal;
	font-size: 14pt;
}
/*
.answer_a {
	float: left;
}
*/
.google_ad_container {
	padding: 10px;
}
#article {
	min-height: none;
	padding: 0;
}
#bodyContent {
	padding: 10px;
}
.inline_move_link a {
	color: #00F;
	font-family: "Arial", sans-serif;
	font-size: 9pt;
	font-weight: normal;
	margin-left: 5px;
}
.firstHeadingCats {
	font-family: "Arial", sans-serif;
	font-size: 9pt;
	font-weight: normal;
	margin: 3px 0 0 30px;
}
#catlinks {
	display: none;
}
#bodyContent {
	padding-bottom: 0;
}
#answers_footer {
	padding: 0 10px 10px;
}
</style>
<?
}
?>
