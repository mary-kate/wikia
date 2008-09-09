<?php

class WysiwygParser extends Parser {

	/**
	 * Tag hook handler for 'pre'.
	 */
	function renderPreTag( $text, $attribs ) {
		// Backwards-compatibility hack
		$content = StringUtils::delimiterReplace( '<nowiki>', '</nowiki>', '$1', $text, 'i' );

		$attribs = Sanitizer::validateTagAttributes( $attribs, 'pre' );
		$attribs['wasHtml'] = 1;
		return wfOpenElement( 'pre', $attribs ) .
			Xml::escapeTagsOnly( $content ) .
			'</pre>';
	}

}

class WysiwygInterface extends SpecialPage {

		function WysiwygInterface() {
			SpecialPage::SpecialPage("WysiwygInterface");
			wfLoadExtensionMessages('WysiwygInterface');
		}

		function execute( $par ) {
			global $wgRequest, $wgOut, $wgTitle;
			$this->setHeaders();

			if(empty($par)) {
				$wikitext = $wgRequest->getText('wikitext');
				$wgOut->addHTML('<form method="POST"><textarea name="wikitext" style="height: 140px; width: 800px;">'.$wikitext.'</textarea><br /><br /><input type="submit" value="Post" /></form>');
			} else {
				$title = Title::newFromText($par);
				if($title->exists()) {
					$rev = Revision::newFromTitle($title);
					$wikitext = $rev->getText();
				} else {
					$wikitext = '-';
				}
			}

			$options = new ParserOptions();
			$options->setTidy(true);

			$parser = new WysiwygParser();
			$parser->setOutputType(OT_HTML);
			$out = $parser->parse($wikitext, $wgTitle, $options, false)->getText();
			$out = htmlspecialchars($out);

			$wgOut->addHTML('<br />'.$out);
		}

}