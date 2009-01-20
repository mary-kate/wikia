<?php
ini_set('display_errors', true);
/**
 * Answers for answer.wikia.com
 *
 * Translated from gwicke's previous TAL template version to remove
 * dependency on PHPTAL.
 *
 * @ingroup Skins
 * @author Nick Sullivan
 */

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

/**
 * Inherit main code from Monaco, set the CSS and template filter.
 * @ingroup Skins
 */

require dirname(__FILE__) . '/Monaco.php';

class SkinAnswers extends SkinMonaco {

	public function initPage(&$out) {
		parent::initPage( $out );
		$this->skinname  = 'answers';
		$this->stylename = 'answers';
		$this->template  = 'AnswersTemplate';
	}

	function setupSkinUserCss( OutputPage $out ) {
		global $wgHandheldStyle;

		parent::setupSkinUserCss( $out );

		// Append to the default screen common & print styles...
		$out->addStyle( 'answers/main.css', 'screen' );
	}
}

/**
 * @ingroup Skins
 */
class AnswersTemplate extends MonacoTemplate {
	var $skin;
	/**
	 * Template filter callback for MonoBook skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		global $wgRequest, $wgUser, $wgStyleVersion, $wgStylePath, $wgTitle;
		$this->skin = $skin = $this->data['skin'];
		$action = $wgRequest->getText( 'action' );
		$answer_page = Answer::newFromTitle( $wgTitle );
		$is_question = $answer_page->isQuestion();
		($is_question) ? $question_mark = '?' : $question_mark = '';

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="<?php $this->text('xhtmldefaultnamespace') ?>" <?php
	foreach($this->data['xhtmlnamespaces'] as $tag => $ns) {
		?>xmlns:<?php echo "{$tag}=\"{$ns}\" ";
	} ?>xml:lang="<?php $this->text('lang') ?>" lang="<?php $this->text('lang') ?>" dir="<?php $this->text('dir') ?>">
	<head>
		<meta http-equiv="Content-Type" content="<?php $this->text('mimetype') ?>; charset=<?php $this->text('charset') ?>" />
		<?php $this->html('headlinks') ?>
	        <link rel="stylesheet" type="text/css" href="<?=$wgStylePath?>/answers/css/monobook_modified.css?<?=$wgStyleVersion?>" />
	        <link rel="stylesheet" type="text/css" href="<?=$wgStylePath?>/answers/css/reset_modified.css?<?=$wgStyleVersion?>" />
		<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.5.2/build/yahoo-dom-event/yahoo-dom-event.js"></script> 
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
		<script type="text/javascript" src="<?=$wgStylePath?>/answers/js/main.js?<?=$wgStyleVersion?>"></script>
		
		
		<title><?php $this->text('pagetitle') ?></title>
		<?php $this->html('csslinks') ?>

		<?php print Skin::makeGlobalVariablesScript( $this->data ); ?>

		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/common/wikibits.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"><!-- wikibits js --></script>
		<!-- Head Scripts -->
<?php $this->html('headscripts') ?>
	        <link rel="stylesheet" type="text/css" href="<?=$wgStylePath?>/answers/css/main.css?<?=$wgStyleVersion?>" />
<?php	if($this->data['jsvarurl']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('jsvarurl') ?>"><!-- site js --></script>
<?php	} ?>
<?php	if($this->data['pagecss']) { ?>
		<style type="text/css"><?php $this->html('pagecss') ?></style>
<?php	}
		if($this->data['usercss']) { ?>
		<style type="text/css"><?php $this->html('usercss') ?></style>
<?php	}
		if($this->data['userjs']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('userjs' ) ?>"></script>
<?php	}
		if($this->data['userjsprev']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>"><?php $this->html('userjsprev') ?></script>
<?php	}
		if($this->data['trackbackhtml']) print $this->data['trackbackhtml']; ?>
	</head>
<body<?php if($this->data['body_ondblclick']) { ?> ondblclick="<?php $this->text('body_ondblclick') ?>"<?php } ?>
<?php if($this->data['body_onload']) { ?> onload="<?php $this->text('body_onload') ?>"<?php } ?>
 class="mediawiki <?php $this->text('dir') ?> <?php $this->text('pageclass') ?> <?php $this->text('skinnameclass') ?>">

<!--GetHTMLAfterBody-->
<?
wfRunHooks('GetHTMLAfterBody', array (&$this));
?>
<!--GetHTMLAfterBody-->

	<!-- ##### Begin main content #### -->
        <div id="answers_header" class="reset">
		<a href="/" id="wikianswers_logo"><img src="/skins/answers/images/wikianswers_logo.png" /></a>

		<div id="answers_ask">
			<form method="get" action="" onsubmit="return false" name="ask_form" id="ask_form">
				<input type="text" id="answers_ask_field" value="<?=htmlentities(wfMsg("ask_a_question"))?>" class="alt" /><span>?</span>
				<a href="javascript:void(0);" id="ask_button" class="huge_button green"><div></div>Ask</a>
			</form>
		</div><?/*answers_ask*/?>

		<?php echo $this->execUserLinks()?>

	</div><?/*answers_header*/?>

	<div id="answers_page">
		<?php
		if ( 
			(!$is_question || in_array( 'staff', $wgUser->getGroups() ) || in_array( 'admin', $wgUser->getGroups() ) ) &&
			$wgTitle->getNamespace() != NS_CATEGORY
		) {
		?>
		<div id="page_bar" class="reset color1 clearfix">
			<ul id="page_controls">
			<?php
			if(isset($this->data['articlelinks']['left'])) {
				foreach($this->data['articlelinks']['left'] as $key => $val) {
			?>
					<li id="control_<?= $key ?>" class="<?= $val['class'] ?>"><div>&nbsp;</div><a rel="nofollow" id="ca-<?= $key ?>" href="<?= htmlspecialchars($val['href']) ?>" <?= $skin->tooltipAndAccesskey('ca-'.$key) ?>><?= htmlspecialchars(ucfirst($val['text'])) ?></a></li>
			<?php
				}
			}
			?>
			</ul>
			<ul id="page_tabs">
			<?php
			global $userMasthead;
			$showright = true;
			if( defined( "NS_BLOG_ARTICLE" ) && $wgTitle->getNamespace() == NS_BLOG_ARTICLE ) {
				$showright = false;
			}
			if(isset($this->data['articlelinks']['right']) && $showright ) {
				foreach($this->data['articlelinks']['right'] as $key => $val) {
			?>
					<li class="<?= $val['class'] ?>"><a href="<?= htmlspecialchars($val['href']) ?>" id="ca-<?= $key ?>" <?= $skin->tooltipAndAccesskey('ca-'.$key) ?> class="<?= $val['class'] ?>"><?= htmlspecialchars(ucfirst($val['text'])) ?></a></li>
			<?php
				}
			}
			?>
			</ul>
		</div><?/*page_bar*/?>
		<?php
		}
		?>

		<div id="answers_article">
		
		<a name="top" id="top"></a>

		<?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>

		<?php
		if ($is_question) {
			$author = $answer_page->getOriginalAuthor();
			
			$category_text = array();
			global $wgOut;
			$categories_array = $wgOut->getCategoryLinks();
			if( is_array( $categories_array["normal"] ) ){
				foreach($categories_array["normal"] as $ctg){
					$category_text[]=strip_tags($ctg);
				}
			}
		?>
		<div class="sectionedit">[<a href="<?=$this->data['content_actions']['move']['href']?>"><?=wfMsg('editsection')?></a>]</div>
		<div id="question">
			<div class="top"><span></span></div>
			<h1 id="firstHeading" class="firstHeading"><?php $this->data['displaytitle']!=""?$this->html('title'):$this->text('title') ?><?=$question_mark?></h1>
			<div class="categories">
			<?php
				foreach($category_text as $ctg){
					$category_title = Title::makeTitle(NS_CATEGORY, $ctg );
					if( $categories ) $categories .= ", ";
					$categories .=  "<a href=\"" . $category_title->escapeFullURL() . "\">{$ctg}</a>";
				}
				echo wfMsg("categories") . ": " . $categories;
				?>
			</div>
			<div class="bottom"><span></span></div>
		</div>
		<div id="attribution" class="clearfix">
			<?php 
			/* If it is an anonymous user, then we send it to the
			 * contributions page for that IP per Gil
			 */
			if (ip2long($author["user_name"])){
				$url = "/wiki/Special:Contributions/" . $author["user_name"];
			} else { 
				$url = $author["title"]->escapeFullURL();
			} 
			?>
			<div>
			<span>Question asked by <a href="<?= $url ?>"><?= $author["user_name"] ?></a></span>
			<a href="<?= $url ?>"><?= $author["avatar"]?></a>
			</div>
			<div id="question_tail"></div>
		</div>
		<?php
		} else {
		?>	
		<h1 id="firstHeading" class="firstHeading"><?php $this->data['displaytitle']!=""?$this->html('title'):$this->text('title') ?></h1>
		<?php
		}
		?>
		
		<?
		global $wgTitle;
		if ( $is_question && $answer_page->isArticleAnswered() ) {
			echo '<div class="sectionedit">[<a href="'. $this->data['content_actions']['edit']['href'] .'">'. wfMsg('editsection') .'</a>]</div>';
			echo '<div id="answer_title">'. wfMsg("answer_title") .'</div>';
			$bodyContentClass = ' class="question"';
		} else if ($wgTitle->getNamespace() == NS_CATEGORY) {
			$bodyContentClass = ' class="category"';
		} else {
			$bodyContentClass = '';
		}
		?>
		
		<div id="bodyContent"<?=$bodyContentClass?>> 
			<h3 id="siteSub"><?php $this->msg('tagline') ?></h3>
			<div id="contentSub"><?php $this->html('subtitle') ?></div>
			<?php if($this->data['undelete']) { ?><div id="contentSub2"><?php     $this->html('undelete') ?></div><?php } ?>
			<?php if($this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html('newtalk')  ?></div><?php } ?>
			<?php if($this->data['showjumplinks']) { ?><div id="jump-to-nav"><?php $this->msg('jumpto') ?> <a href="#column-one"><?php $this->msg('jumptonavigation') ?></a>, <a href="#search_input"><?php $this->msg('jumptosearch') ?></a></div><?php } ?>
			<!-- start content -->
			<?php $this->html('bodytext') ?>
			<!-- end content -->
			<?php if($this->data['dataAfterContent']) { $this->html ('dataAfterContent'); } ?>
			<div class="visualClear"></div>
		</div><?/*bodyContent*/?>

		<?php
		global $wgTitle;
		if ($wgUser->isAnon() && !$answer_page->isArticleAnswered() && $_GET['state'] == 'asked') {
			$submit_title = SpecialPage::getTitleFor( 'Userlogin' );
			$submit_url = $submit_title->escapeFullURL("type=signup&action=submitlogin");
			
			global $wgOut, $wgCaptchaTriggers;
				if($wgCaptchaTriggers['createaccount'] == true){
					$f = new FancyCaptcha();
					$captcha = ( "<div class='captcha'>" .
					wfMsg("createaccount-captcha") .
					$f->getForm() . "</div>\n" );
				}
			?>
			<script type="text/javascript" src="<?=$wgStylePath?>/answers/js/inline_register.js?<?=$wgStyleVersion?>"></script>
			<div class="inline_form reset">
				<h1><?= wfMsg("inline-register-title") ?></h1>
				<div class="inline_form_inside">
					<form name="register" method="post" id="register" action="<?= $submit_url ?>">
						<?= $captcha ?>
						<table>
						<tr>
							<td><?= wfMsg("yourname") ?></td>
							<td><input type="text" value="" name="wpName" id="wpName2"> <span id="username_check"></span></td>
						</tr>
						<tr>
							<td><?= wfMsg("youremail") ?></td>
							<td><input type="text" value="" name="wpEmail" id="wpEmail"></td>
						</tr>
						<tr>
							<td><?= wfMsg("yourpassword") ?></td>
							<td><input type="password" value="" name="wpPassword" id="wpPassword2"></td>
						</tr>
						<tr>
							<td><?= wfMsg("yourpasswordagain") ?></td>
							<td><input type="password" value="" name="wpRetype" id="wpRetype"></td>
						</tr>
						</table>
						<input type="hidden" name="wpRemember" value="1" id="wpRemember"/>
						<div class="toolbar">
							<input type='submit' name="wpCreateaccount" id="wpCreateaccount" value="<?= wfMsg("createaccount") ?>">
						</div>
					</form>
				</div>
			</div>
			<?php
		}
		if ($is_question) {
		?>
		<div id="bottom_ads"> 
			<div id="google_ad_1" class="google_ad"></div>
			<div id="google_ad_2" class="google_ad"></div> 
		</div> 
		
		<div id="huge_buttons" class="clearfix">
			<? if ( $answer_page->isArticleAnswered() ) { ?>
			<a href="<?= $wgTitle->getEditURL() ?>" class="huge_button edit"><div></div><?= wfMsg("improve_this_answer") ?></a>	
			<a href="<?= $wgTitle->escapeFullURL("action=watch") ?>" class="huge_button watchlist"><div></div><?= wfMsg("notify_improved") ?></a>
			<? } else { ?>
			<a href="<?= $wgTitle->getEditURL() ?>" class="huge_button edit"><div></div><?= wfMsg("answer_this_question") ?></a>	
			<a href="<?= $wgTitle->escapeFullURL("action=watch") ?>" class="huge_button watchlist"><div></div><?= wfMsg("notify_answered") ?></a>
			<? } ?>
		</div>
		<?php } ?>

		<?php if($this->data['catlinks']) { $this->html('catlinks'); } ?>

		<!-- XIAN: Pull content that is now in "AnswersAfterArticle" hook -->

                <!-- NICK: Related answered questions -->
		<? if ( $is_question ) { ?>
		<div id="related_questions" class="reset widget">
			<h2><?= wfMsg("related_answered_questions") ?></h2>
			<ul id="related_answered_questions">
			</ul>
			<div id="google_ad_5" class="google_ad"></div>
		</div>
		<? } ?>
		</div><?/*answers_article*/?>
	</div><?/*answers_page*/?>
	
	<?
	if(isset($this->data['userlinks']['more'])) {
	?>
		<div id="header_menu_user" class="header_menu reset">
		<ul>
	<?php
		foreach($this->data['userlinks']['more'] as $itemKey => $itemVal) {
		if ($itemKey == 'widgets') {
			continue;
		}
	?>
			<li><a rel="nofollow" href="<?= htmlspecialchars($itemVal['href']) ?>" class="yuimenuitemlabel" <?= $skin->tooltipAndAccesskey('pt-'.$itemKey) ?>><?= htmlspecialchars($itemVal['text']) ?></a></li>
	<?php
		}
	?>
		</ul>
	</div>
	<?php
	}
	?>

	<?php
	$wikiafooterlinks = $this->data['data']['wikiafooterlinks'];
	if(count($wikiafooterlinks) > 0) {
		echo '<div id="wikia_footer">';
		$wikiafooterlinksA = array();
		foreach($wikiafooterlinks as $key => $val) {
			// Very primitive way to actually have copyright WF variable, not MediaWiki:msg constant.
			// This is only shown when there is copyright data available. It is not shown on special pages for example.
			if ( 'GFDL' == $val['text'] ) {
				if (!empty($this->data['copyright'])) {
					$wikiafooterlinksA[] = $this->data['copyright'];
				}
			} else {
				$wikiafooterlinksA[] = '<a rel="nofollow" href="'.htmlspecialchars($val['href']).'">'.$val['text'].'</a>';
			}
		}
		$wikiafooterlinksA_2 = array_splice($wikiafooterlinksA, ceil(count($wikiafooterlinksA) / 2 ));
		echo implode(' | ', $wikiafooterlinksA);
		echo '<br />';
		echo implode(' | ', $wikiafooterlinksA_2);
		echo '</div>';
	}
	?>


        <div id="answers_sidebar" class="reset">
		<?php
		($wgUser->isLoggedIn()) ? $toolboxClass = '' : $toolboxClass = ' class="anon"';
		?>
		<div id="toolbox"<?=$toolboxClass?>>
			<div id="toolbox_stroke">
			<?php
			if ($wgUser->isLoggedIn()) {
			?>
				<div id="toolbox_inside">
					<h6>Wikianswers toolbox</h6>
					
					<table>
					<tr>
						<td>
						<?
						for($i=0; $i<ceil(count($this->data['data']['toolboxlinks'])/2); $i++) {
							echo '<a href="'. $this->data['data']['toolboxlinks'][$i]['href'] .'">'. $this->data['data']['toolboxlinks'][$i]['text'] .'</a><br />';
						}
						?>
						</td>
						<td>
						<?
						for($i; $i<count($this->data['data']['toolboxlinks']); $i++) {
							echo '<a href="'. $this->data['data']['toolboxlinks'][$i]['href'] .'">'. $this->data['data']['toolboxlinks'][$i]['text'] .'</a><br />';
						}
						?>
						</td>
					</tr>
					</table>
				</div><?/*toolbox_inside*/?>
				<div id="toolbox_search">
					<?=$this->searchBox();?>
				</div>
			<?php
			} else {
			?>
				<div id="toolbox_inside">
					<img src="/skins/answers/images/mr_wales.jpg" class="portrait" />
					<i>"Wikianswers leverages the unique characterstics of a wiki to form the very best answers to any question."</i><br /><br />
					<b>Jimmy Wales</b><br />
					founder of Wikipedia and Wikianswers
				</div>
			<?php
			}
			?>
			</div><?/*toolbox_stroke*/?>
		</div><?/*toolbox*/?>

		<div class="widget">
			<h2><?= wfMsg("recent_unanswered_questions") ?></h2>
			<ul id="recent_unanswered_questions">
				<? 
				if ($is_question) {	
				echo '<li><div id="google_ad_3" class="google_ad"></div></li>';
				} 
				?>
			</ul>
		</div>

		<div class="widget">
			<h2><?= wfMsg("popular_categories") ?></h2>
			<ul id="popular_categories">
				<? 
				if ($is_question) {	
				echo '<li><div id="google_ad_4" class="google_ad"></div></li>';
				}
				?>
			</ul>
		</div>
	</div><?/*answers_sidebar*/?>

	<div id="footer">
	</div><?/*footer*/?>
</div>
<?php $this->html('bottomscripts'); /* JS call to runBodyOnloadHook */ ?>
<?php $this->html('reporttime') ?>
<?php if ( $this->data['debug'] ): ?>
<!-- Debug output:
<?php $this->text( 'debug' ); ?>

-->
<?php endif; ?>
<script>
google_ad_client = 'pub-4086838842346968'; // substitute your client_id (pub-#)
google_ad_channel = '';
google_ad_output = 'js';
google_max_num_ads = '10';
google_ad_type = 'text_html';
google_image_size = '728x90';
google_feedback = 'on';
google_adtest = 'on';
<?
if ($category_text) {
echo 'google_hints = \''. implode(', ', $category_text) .'\';';
}
?>
</script>
<script language="JavaScript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</body></html>
<?php
	wfRestoreWarnings();
	} // end of execute() method

	/*************************************************************************************************/
	function searchBox() {
?>
			<form action="<?php $this->text('searchaction') ?>" id="searchform">
				<input id="search_input" name="search" type="text"<?php echo $this->skin->tooltipAndAccesskey('search');
					if( isset( $this->data['search'] ) ) {
						?> value="<?php $this->text('search') ?>"<?php } ?> />
				<input type='submit' name="go" class="search_button" id="search_go_button"	value="<?php $this->msg('searcharticle') ?>"<?php echo $this->skin->tooltipAndAccesskey( 'search-go' ); ?> />
				<input type='submit' name="fulltext" class="search_button" id="search_button" value="<?php $this->msg('searchbutton') ?>"<?php echo $this->skin->tooltipAndAccesskey( 'search-fulltext' ); ?> />
			</form>
<?php
	}

	/*************************************************************************************************/
	function toolbox() {
?>
	<div class="portlet" id="p-tb">
		<h5><?php $this->msg('toolbox') ?></h5>
		<div class="pBody">
			<ul>
<?php
		if($this->data['notspecialpage']) { ?>
				<li id="t-whatlinkshere"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-whatlinkshere') ?>><?php $this->msg('whatlinkshere') ?></a></li>
<?php
			if( $this->data['nav_urls']['recentchangeslinked'] ) { ?>
				<li id="t-recentchangeslinked"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-recentchangeslinked') ?>><?php $this->msg('recentchangeslinked') ?></a></li>
<?php 		}
		}
		if(isset($this->data['nav_urls']['trackbacklink'])) { ?>
			<li id="t-trackbacklink"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-trackbacklink') ?>><?php $this->msg('trackbacklink') ?></a></li>
<?php 	}
		if($this->data['feeds']) { ?>
			<li id="feedlinks"><?php foreach($this->data['feeds'] as $key => $feed) {
					?><span id="<?php echo Sanitizer::escapeId( "feed-$key" ) ?>"><a href="<?php
					echo htmlspecialchars($feed['href']) ?>"<?php echo $this->skin->tooltipAndAccesskey('feed-'.$key) ?>><?php echo htmlspecialchars($feed['text'])?></a>&nbsp;</span>
					<?php } ?></li><?php
		}

		foreach( array('contributions', 'log', 'blockip', 'emailuser', 'upload', 'specialpages') as $special ) {

			if($this->data['nav_urls'][$special]) {
				?><li id="t-<?php echo $special ?>"><a href="<?php echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-'.$special) ?>><?php $this->msg($special) ?></a></li>
<?php		}
		}

		if(!empty($this->data['nav_urls']['print']['href'])) { ?>
				<li id="t-print"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['print']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-print') ?>><?php $this->msg('printableversion') ?></a></li><?php
		}

		if(!empty($this->data['nav_urls']['permalink']['href'])) { ?>
				<li id="t-permalink"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-permalink') ?>><?php $this->msg('permalink') ?></a></li><?php
		} elseif ($this->data['nav_urls']['permalink']['href'] === '') { ?>
				<li id="t-ispermalink"<?php echo $this->skin->tooltip('t-ispermalink') ?>><?php $this->msg('permalink') ?></li><?php
		}

		wfRunHooks( 'MonoBookTemplateToolboxEnd', array( &$this ) );
		wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this ) );
?>
			</ul>
		</div>
	</div>
<?php
	}

	/*************************************************************************************************/
	function languageBox() {
		if( $this->data['language_urls'] ) {
?>
	<div id="p-lang" class="portlet">
		<h5><?php $this->msg('otherlanguages') ?></h5>
		<div class="pBody">
			<ul>
<?php		foreach($this->data['language_urls'] as $langlink) {
			// Add title tag only if differ from shown text
			$titleTag = $langlink['title'] == $langlink['text'] 
				? ''
				: 'title="' . htmlspecialchars( $langlink['title'] ) . '"';
			?>
				<li class="<?php echo htmlspecialchars($langlink['class'])?>"><?php
				?><a href="<?php echo htmlspecialchars($langlink['href']) ?>"
				<? echo $titleTag ?> > <?php echo $langlink['text'] ?></a></li>
<?php		} ?>
			</ul>
		</div>
	</div>
<?php
		}
	}

	/*************************************************************************************************/
	function customBox( $bar, $cont ) {
?>
	<div class='generated-sidebar portlet' id='<?php echo Sanitizer::escapeId( "p-$bar" ) ?>'<?php echo $this->skin->tooltip('p-'.$bar) ?>>
		<h5><?php $out = wfMsg( $bar ); if (wfEmptyMsg($bar, $out)) echo $bar; else echo $out; ?></h5>
		<div class='pBody'>
<?php   if ( is_array( $cont ) ) { ?>
			<ul>
<?php 			foreach($cont as $key => $val) { ?>
				<li id="<?php echo Sanitizer::escapeId($val['id']) ?>"<?php
					if ( $val['active'] ) { ?> class="active" <?php }
				?>><a href="<?php echo htmlspecialchars($val['href']) ?>"<?php echo $this->skin->tooltipAndAccesskey($val['id']) ?>><?php echo htmlspecialchars($val['text']) ?></a></li>
<?php			} ?>
			</ul>
<?php   } else {
			# allow raw HTML block to be defined by extensions
			print $cont;
		}
?>
		</div>
	</div>
<?php
	}

	public function execUserLinks(){
		global $wgUser;
                $skin = $wgUser->getSkin();
		if ($wgUser->isLoggedIn()) {
        	?>
			<ul id="user_data">
                                <li id="header_username"><a href="<?php echo htmlspecialchars($this->data['personal_urls']['userpage']['href']) ?>" <?php echo $skin->tooltipAndAccesskey('pt-userpage') ?>><?php echo htmlspecialchars($wgUser->getName()) ?></a></li>
                                <li><a href="<?php echo htmlspecialchars($this->data['personal_urls']['mytalk']['href']) ?>" <?php echo $skin->tooltipAndAccesskey('pt-mytalk') ?>><?php echo htmlspecialchars($this->data['personal_urls']['mytalk']['text']) ?></a></li>
                                <li><a href="<?php echo htmlspecialchars($this->data['personal_urls']['watchlist']['href']) ?>" <?php echo $skin->tooltipAndAccesskey('pt-watchlist') ?>><?php echo htmlspecialchars(wfMsg('prefs-watchlist')) ?></a></li>
                                <li><dl id="header_button_user" class="header_menu_button">
					<dt><?php echo trim(wfMsg('moredotdotdot'), ' .') ?></dt>
                                        <dd>&nbsp;</dd>
                                    </dl>
                                </li>
                                <li><a rel="nofollow" href="<?php echo htmlspecialchars($this->data['personal_urls']['logout']['href']) ?>" <?php echo $skin->tooltipAndAccesskey('pt-logout') ?>><?php echo htmlspecialchars($this->data['personal_urls']['logout']['text']) ?></a></li>
			</ul>
		<?php
 		} else { // not logged in
			global $wgTitle;
		?>
			<ul id="user_data" class="anon">
                                <li id="userLogin">
                                        <a rel="nofollow" class="bigButton" id="login" href="<?php echo htmlspecialchars(Skin::makeSpecialUrl( 'Userlogin', 'returnto=' . $wgTitle->getPrefixedURL()) )?>">
                                                <big><?php echo htmlspecialchars(wfMsg('login')) ?></big>
                                                <small>&nbsp;</small>
                                        </a>
                                </li>
                                <li>
                                        <a rel="nofollow" class="bigButton" id="register" href="<?php echo htmlspecialchars(Skin::makeSpecialUrl( 'Userlogin', 'type=signup' )) ?>">
                                                <big><?php echo htmlspecialchars(wfMsg('nologinlink')) ?></big>
                                                <small>&nbsp;</small>
                                        </a>
                                </li>
			</ul>
<?php
		}
        }
} // end of class



