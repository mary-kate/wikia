<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
chdir(getenv('DOCUMENT_ROOT'));
require 'includes/WebStart.php';
require 'extensions/wikia/AdEngine/AdEngine.php';
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>Test Page</title>
<script>
var wgContentLanguage = "en";
var wgCatId = 3;
function $() {
	var elements = new Array();
	for (var i = 0; i < arguments.length; i++) {
		var element = arguments[i];
		if (typeof element == 'string')
			element = document.getElementById(element);
		if (arguments.length == 1)
			return element;
		elements.push(element);
	}
	return elements;
}


</script>
<style type="text/css">
body {
	background-color: #F5F5F5;
	font-family: arial, sans-serif;
	font-size: 10pt;
	margin: 0;	
}
.monaco_shrinkwrap {
	position: relative;
	width: 100%;	
}
#wikia_header {
	background-color: #F5F5F5;
	border-bottom: 1px solid #999;
	height: 50px;	
	position: relative;
}
#background_strip {
	background-color: #FFF;
	border-bottom: 1px solid #999;
	height: 155px;
}
#wikia_page {
	background-color: #FFF;
	border: 1px solid #AAA;
	height: 1%;
	margin: 0 5px 0 216px;
	overflow: hidden;
	position: relative;
	top: -176px;
	z-index: 5;	
}
#page_bar {
	background-color: #36C;
	color: #FFF;
	font-family: tahoma, sans-serif;
	font-size: 11pt;
	line-height: 32px;	
	margin: 2px 2px 0;
	overflow: hidden;
	padding: 0 5px;
}
#article {
	min-height: 200px;
	padding: 10px;
	position: relative;	
}
#articleFooter {
	border-top: 1px dashed #CCC;
	height: 100px;
	padding: 10px;	
}
#widget_sidebar {
	left: 5px;
	position: absolute;
	top: 5px;
	width: 206px;
	z-index: 20;	
}

#TOP_LEADERBOARD {
	background: #333;
	margin-bottom: 10px;
}
#TOP_RIGHT_BOXAD {
	background: #333;
	float: right;
	margin: 0 0 10px 10px;
}
#FASTsleeper1 {
	display: none;
	position: absolute;
	top: 10px;	
}
#FASTsleeper2 {
	display: none;
	position: absolute;
	top: 10px;	
	width: 100%;
}
</style>
</head>

<body>
<div id="wikia_header">
	<!--
	<input type="button" value="FAST1" onclick="FAST(1);" />
	<input type="button" value="FAST2" onclick="FAST(2);" />
	-->
</div>
<div id="background_strip"></div>
<div class="monaco_shrinkwrap">
	<div id="wikia_page">
		<div id="page_bar">controls here</div>
		<div id="article">
			<div id="FAST1"></div>
			<div id="FAST2"></div>
			<?php $html=file_get_contents(dirname(__FILE__) . '/testfiles/longArticleWithImagesNoCollision.html'); echo $html;?>
			<?php echo AdEngine::getInstance()->getPlaceHolderDiv("TOP_LEADERBOARD"); ?>
			<?php echo AdEngine::getInstance()->getPlaceHolderDiv("TOP_RIGHT_BOXAD"); ?>
			
		</div><!-- Closing "article" -->
		<div id="articleFooter">
			Article controls here
			<div style="float:right">
			  Footer Right box ad right: <br />
			  <?php echo AdEngine::getInstance()->getPlaceHolderDiv("FOOTER_BOXAD_RIGHT"); ?>
			</div>
		
			<br clear="all">
			<hr />
		
			<div style="width:33%; float:right">
			  Center spotlight: <br />
			  <?php echo AdEngine::getInstance()->getAd("FOOTER_SPOTLIGHT_MIDDLE"); ?>
			</div>
			<div style="width:33%; float:right">
			  Right spotlight: <br />
			  <?php echo AdEngine::getInstance()->getAd("FOOTER_SPOTLIGHT_RIGHT"); ?>
			</div>
			<div style="width:33%; float:right">
		  	  Left spotlight: <br />
			  <?php echo AdEngine::getInstance()->getAd("FOOTER_SPOTLIGHT_LEFT"); ?>
			</div>
		</div>
	</div><!-- Closing "wikia_page" -->
	<div id="widget_sidebar">
		Left Skyscraper 1:
		<?php echo AdEngine::getInstance()->getPlaceHolderDiv("LEFT_SKYSCRAPER_1"); ?>
		
		<p>
		Left Spotlight:
		<?php echo AdEngine::getInstance()->getAd("LEFT_SPOTLIGHT_1"); ?>

		<p>
		Left Skyscraper 2:
		<?php echo AdEngine::getInstance()->getPlaceHolderDiv("LEFT_SKYSCRAPER_2"); ?>
	
	</div>
</div><!--Closing "monaco_shrinkwrap" -->
<?php echo AdEngine::getInstance()->getDelayedLoadingCode()?>

<script language="javascript">
function swapMe(slot){
alert(slot);
  realDiv=document.getElementById(slot);
  loadDiv=document.getElementById(slot+'_load');
  realDiv.innerHTML=loadDiv.innerHTML;
}
<?php
foreach (AdEngine::getInstance()->getPlaceholders() as $ph){
	echo "swapMe('$ph');\n";
}
</script>
</body>
</html>
