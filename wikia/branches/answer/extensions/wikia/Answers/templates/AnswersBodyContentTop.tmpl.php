<?
global $wgTitle, $wgArticle, $wgOut;
//if ($wgTitle->isContentPage() && !ArticleAdLogic::isMainPage()) {
if ($wgTitle->exists() && $wgTitle->isContentPage() && !$wgTitle->isTalkPage() && $wgOut->isArticle()) {

	
	//TODO:
	//Determine if the page contains an answer
	//Can use $wgArticle->getContent() 
	//Must remove any [[Category:blah]] tags
	//Also must remove from edit and preview pages

	echo '<div class="clearfix"><a href="'. $wgTitle->getEditURL() .'" class="bigButton"><big>Answer this question</big><small></small></a></div>';

	echo '<div class="answer_a">Answer:</div>';
}
?>
