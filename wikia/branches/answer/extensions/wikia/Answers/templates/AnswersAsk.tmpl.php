<?
global $wgTitle, $wgArticle, $wgOut;
//if ($wgTitle->isContentPage() && !ArticleAdLogic::isMainPage()) {
if ( $wgTitle->exists() && $wgTitle->isContentPage() && !$wgTitle->isTalkPage() && $wgOut->isArticle() ) {

?>
<script type="text/javascript">
function ask_question() {
	document.location = 'http://answer.wikia.com/wiki/Special:CreatePage?Createtitle=' + document.getElementById('answers_ask_field').value;
}
</script>

<div id="answers_ask">
	<form method="get" action="javascript:ask_question();">
		Ask a new question: 
		<input type="text" id="answers_ask_field" />
		<input type="submit" id="answers_ask_button" value="Ask"  />
	</form>
</div>
<?
}
?>
