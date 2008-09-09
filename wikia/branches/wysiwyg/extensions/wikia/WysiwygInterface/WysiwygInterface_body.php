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
			global $wgRequest, $wgOut, $wgTitle, $IP;
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
			$out = $parser->parse($wikitext, $wgTitle, $options)->getText();

			// macbre: return nicely colored & tabbed code
			require($IP. '/lib/geshi/geshi.php');

			// clear whitespaces between tags
			$out = preg_replace('/>(\s+)</', '><', $out);	// between tags
			$out = preg_replace('/(\s+)<\//', '</', $out);	// before closing tag

			$out = mb_convert_encoding($out, 'HTML-ENTITIES', "UTF-8"); 
		
			$dom = new DOMDocument();
			$dom->loadHTML($out);
			$dom->formatOutput = true;
			$dom->preserveWhiteSpace = false;

		 	// only show content inside <body> tag
			$body = $dom->getElementsByTagName('body')->item(0);
			$out = $dom->saveXML($body);

			$out = '  ' . trim(substr($out, 6, -7));

			$geshi = new geshi($out, 'html4strict');
			$geshi->enable_keyword_links(false);
			
			$wgOut->addHTML($geshi->parse_code());
		}

}
