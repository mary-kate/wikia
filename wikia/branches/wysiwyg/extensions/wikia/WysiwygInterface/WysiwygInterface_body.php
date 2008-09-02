<?php

class WysiwygParser extends Parser {

}

class WysiwygInterface extends SpecialPage {

		function WysiwygInterface() {
			SpecialPage::SpecialPage("WysiwygInterface");
			wfLoadExtensionMessages('WysiwygInterface');
		}

		function execute( $par ) {
			global $wgRequest, $wgOut;
			$this->setHeaders();

			$title = Title::newFromText($par);
			if($title->exists()) {
				$rev = Revision::newFromTitle($title);

				$options = new ParserOptions();
				$options->setTidy(true);

				$parser = new WysiwygParser();
				$parser->setOutputType(OT_HTML);
				$out = $parser->parse($rev->getText(), $title, $options)->getText();
				$out = htmlspecialchars($out);
			} else {
				$out = '-';
			}

			$wgOut->addHTML($out);
		}

}