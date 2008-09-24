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
			if($this->dom->loadHTML($html)) {

				$body = $this->dom->getElementsByTagName('body')->item(0);
				$out = $this->parseNode($body);

			}
			wfRestoreWarnings();
		}

		wfProfileOut(__METHOD__);
		return rtrim($out);
	}

	private function parseNode($node, $level = 0) {

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

				if($node->nodeName == 'body') {

					$out = $textContent;

				} else if($node->nodeName == 'br') {

					$out = '<br />';

					/*
					// new line logic
					if($node->parentNode && $node->parentNode->nodeName == 'p') {
						$out = "{$out}\n";
					}
					*/

				} else if($node->nodeName == 'p') {

					$out = $textContent;

					// new line logic
					if($node->previousSibling && $node->previousSibling->nodeName == 'p') {
						// paragraph after paragraph
						$out = "\n\n{$out}";
					} else if(($node->previousSibling && $this->isHeading($node->previousSibling)) || ($node->previousSibling && $node->previousSibling->previousSibling && $this->isHeading($node->previousSibling->previousSibling))) {
						// header before paragraph
						$out = "\n{$out}";
					}

				} else if($this->isHeading($node)) {

					$head = str_repeat("=", $node->nodeName{1});
					$out = "{$head} {$textContent} {$head}";

					// new line logic
					if($node->previousSibling) {
						$out = "\n{$out}";
					}

				} else if($node->nodeName == 'i' || $node->nodeName == 'em') {

					$out = "''{$textContent}''";

				} else if($node->nodeName == 'b' || $node->nodeName == 'strong') {

					$out = "''''{$textContent}''";

				}

			}

		} else if($node->nodeType == XML_TEXT_NODE) {

			wfDebug("ReverseParser XML_TEXT_NODE\n");

			if(trim($node->textContent) == '') {
				$out = '';
			} else {
				$out = $this->cleanupTextContent($node->textContent);
			}

			/*

						if($node->previousSibling && $node->previousSibling->nodeName == 'br' && $out{0} == ' ') {
				$out = substr($out, 1);
			}
			*/

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