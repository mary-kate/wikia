<?php

class ReverseParser {

	private $dom;

	function __construct() {
		$this->dom = new DOMdocument();
	}

	public function parse($html, $wysiwygData = array()) {
		wfProfileIn(__METHOD__);

		$out = '';

		if(is_string($html) && $html != '') {
			$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

			wfProfileIn(__METHOD__."-cleanup");

			// HTML cleanup - remove whitespaces between tags
			$html = preg_replace("/>([\s]+)<p>/", '><p>', $html); // before <p> tag
			$html = preg_replace("/<\/p>([\s]+)</", '</p><', $html); // after </p> tag
			$html = preg_replace("/p>([\s]+)<br/", 'p><br', $html); // between <p> and <br /> tag

			// remove whitespace after <br /> and decode &nbsp;
			$html = str_replace(array('<br /> ', '&nbsp;'), array('<br />', ' '), $html);

			wfDebug("ReverseParser HTML: {$html}\n");

			wfProfileOut(__METHOD__."-cleanup");

			wfSuppressWarnings();
			if($this->dom->loadHTML($html)) {

				$body = $this->dom->getElementsByTagName('body')->item(0);
				$out = $this->parseNode($body);

			}
			wfRestoreWarnings();

			// final cleanup
			$out = rtrim($out);

			if ($out{0} == "\n" && $out{1} != "\n") {
				// remove ONE empty line from the beginning of wikitext
				$out = substr($out, 1);
			}
		}


		wfProfileOut(__METHOD__);
		return $out;
	}

	private function parseNode($node, $level = 0) {
		wfProfileIn(__METHOD__);

		$childOut = '';

		if($node->hasChildNodes()) {

			$nodes = $node->childNodes;

			for($i = 0; $i < $nodes->length; $i++) {
				$childOut .= $this->parseNode($nodes->item($i));
			}
		}

		if($node->nodeType == XML_ELEMENT_NODE) {

			wfDebug("ReverseParser XML_ELEMENT_NODE\n");

			$washtml = $node->getAttribute('washtml');

			$textContent = ($childOut != '') ? $childOut : $this->cleanupTextContent($node->textContent);

			if(!empty($wasHTML)) {

			} else {

				wfDebug("ReverseParser nodeName: {$node->nodeName}\n");

				switch($node->nodeName) {
					case 'body':
						$out = $textContent;
						break;
					case 'br':
						if($node->parentNode && $node->parentNode->nodeName == 'p' && $node->parentNode->hasChildNodes() && $node->parentNode->childNodes->item(0)->isSameNode($node)) {
							$out = "\n";
						} else {
							$out = '<br />';
						}

						break;
					case 'p':
						$out = $textContent;

						// new line logic
						if($node->previousSibling && $node->previousSibling->nodeName == 'p') {

							// paragraph after paragraph
							$out = "\n\n{$out}";

						} else if($node->previousSibling && $node->previousSibling->nodeName{0} == 'h' && is_numeric($node->previousSibling->nodeName{1})) {

							// header before paragraph
							$out = "\n{$out}";

						} else {

							$out = "\n{$out}";

						}
						break;
					case 'h1':
					case 'h2':
					case 'h3':
					case 'h4':
					case 'h5':
					case 'h6':
						$head = str_repeat("=", $node->nodeName{1});
						$out = "{$head} {$textContent} {$head}";

						// new line logic
						if($node->previousSibling) {
							$out = "\n{$out}";
						}
						break;
					case 'i':
					case 'em':
						$out = "''{$textContent}''";
						break;
					case 'b':
					case 'strong':
						$out = "'''{$textContent}'''";
						break;

				}

			}

		} else if($node->nodeType == XML_TEXT_NODE) {

			wfDebug("ReverseParser XML_TEXT_NODE\n");

			if(trim($node->textContent, "\n") == '') {
				$out = '';
			} else {
				$out = $this->cleanupTextContent($node->textContent);
			}

		}

		wfProfileOut(__METHOD__);
		return $out;
	}

	private function cleanupTextContent($text) {
		return $text;
	}

}
