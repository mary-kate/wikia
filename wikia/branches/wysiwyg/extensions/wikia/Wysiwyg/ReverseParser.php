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

			wfSuppressWarnings();
			$valid = $this->dom->loadHTML($html);
			wfRestoreWarnings();

			if($valid) {

				$body = $this->dom->getElementsByTagName('body')->item(0);

				if($body->hasChildNodes()) {

					$nodes = $body->childNodes;

					for($i=0; $i < $nodes->length; $i++) {

						$out .= $this->parseNode($nodes->item($i));
					}


				}

			}

		}

		wfProfileOut(__METHOD__);
		return rtrim($out);
	}

	private function parseNode($node, $level = 0) {

		if($node->nodeType == XML_ELEMENT_NODE) {

			wfDebug("ReverseParser XML_ELEMENT_NODE\n");

			$washtml = $node->getAttribute('washtml');

			$textContent = $this->cleanupTextContent($node->textContent);

			if(!empty($wasHTML)) {

			} else {

				wfDebug("ReverseParser nodeName: {$node->nodeName}\n");

				switch($node->nodeName) {

					case 'br':
						$out = '<br />';
						break;

					case 'p':
						$out = $textContent;
						break;

					case 'h1':
						$out = "= {$textContent} =";
						break;

					case 'h2':
						$out = "== {$textContent} ==";
						break;

					case 'h3':
						$out = "=== {$textContent} ===";
						break;

					case 'h4':
						$out = "==== {$textContent} ====";
						break;

					case 'h5':
						$out = "===== {$textContent} =====";
						break;

					case 'h6':
						$out = "====== {$textContent} ======";
						break;

				}

				if($node->nodeName == 'p') {
					if($node->previousSibling && $node->previousSibling->nodeName == 'p') {
						$out = "\n\n{$out}";
					} else if($node->previousSibling && $node->previousSibling->previousSibling && $node->previousSibling->previousSibling->nodeName{0} == 'h' && is_numeric($node->previousSibling->previousSibling->nodeName{1})) {
						$out = "\n{$out}";
					} else if($node->previousSibling && $this->isHeading($node->previousSibling)) {
						$out = "\n{$out}";
					}
				} else if($this->isHeading($node)) {
					if($node->previousSibling) {
						$out = "\n\n{$out}";
					}
				}

			}

		} else if($node->nodeType == XML_TEXT_NODE) {

			wfDebug("ReverseParser XML_TEXT_NODE\n");

			if(trim($node->textContent) == '') {
				$out = '';
			} else {
				$out = $this->cleanupTextContent($node->textContent);
			}

		}

		return $out;
	}

	private function cleanupTextContent($text) {
		return $text;
	}

	private function isHeading($node) {
		return $node->nodeName{0} == 'h' && is_numeric($node->nodeName{1});
	}

}