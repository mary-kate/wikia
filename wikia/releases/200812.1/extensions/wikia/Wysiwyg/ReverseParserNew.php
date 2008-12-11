<?php
/**
 * PHP Reverse Parser - Processes given HTML into wikimarkup (new development version)
 *
 * @author Maciej 'macbre' Brencz <macbre(at)wikia-inc.com>
 * @author Inez Korczynski <inez(at)wikia-inc.com>
 *
 * @see http://meta.wikimedia.org/wiki/Help:Editing
 */
class ReverseParser {

	// DOMDocument for processes HTML
	private $dom;

	// Wysiwyg/FCK meta data
	private $data = array();

	// cache results of wfUrlProtocols()
	private $urlProtocols;

	private function getUrlProtocols() {
		if(empty($this->urlProtocols)) {
			$this->urlProtocols = wfUrlProtocols();
		}
		return $this->urlProtocols;
	}

	function __construct() {
		$this->dom = new DOMdocument();
	}

	public function parse($html, $data = array()) {
		wfProfileIn(__METHOD__);

		$out = '';

		if(is_string($html) && $html != '') {

			// fix for proper encoding of UTF characters

			wfDebug("Wysiwyg ReverseParserNew HTML original: $html\n");

			$html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body>'.$html.'</body></html>';

			$this->data = $data;

			wfDebug("Wysiwyg ReverseParserNew data: ".print_r($this->data, true)."\n");

			wfDebug("Wysiwyg ReverseParserNew HTML: $html\n");

			wfSuppressWarnings();
			if($this->dom->loadHTML($html)) {
				$body = $this->dom->getElementsByTagName('body')->item(0);
				wfDebug("Wysiwyg ReverseParserNew HTML from DOM: ".$this->dom->saveHTML()."\n");
				$out = $this->parseNode($body);
			}
			wfRestoreWarnings();

			wfDebug("Wysiwyg ReverseParserNew wikitext: {$out}\n");
		}

		wfProfileOut(__METHOD__);
		return $out;
	}

	private function parseNode($node, $level = 0) {
		wfProfileIn(__METHOD__);

		wfDebug("Wysiwyg ReverseParserNew level: ".str_repeat('.', $level).$node->nodeName."\n");

		$childOut = '';

		// parse child nodes
		if($node->hasChildNodes()) {
			$nodes = $node->childNodes;
			for($n = 0; $n < $nodes->length; $n++) {
				$childOut .= $this->parseNode($nodes->item($n), $level+1);
			}
		}

		// parse current node
		$out = '';

		$textContent = ($childOut != '') ? $childOut : $node->textContent;

		if($node->nodeType == XML_ELEMENT_NODE) {

			$washtml = $node->getAttribute('washtml');

			if(empty($washtml)) {

				$refid = $node->getAttribute('refid');

				if(is_numeric($refid)) {
					$nodeData = $this->data[$refid];
				}

				switch($node->nodeName) {
					case 'body':
						$out = $textContent;
						break;

					case 'br':
						// e.g. "<br/><!--NEW_LINE_1-->" => "\n"
						if($node->nextSibling && $node->nextSibling->nodeType == XML_COMMENT_NODE && $node->nextSibling->data == "NEW_LINE_1") {
							$out = "\n";
						}
						break;

					case 'p':
						// if the first previous XML_ELEMENT_NODE (so no text and no comment) of the current
						// node is <p> then add new line before the current one
						if(($previousNode = $this->getPreviousElementNode($node)) && $previousNode->nodeName == 'p') {
							$textContent = "\n" . $textContent;
						}

						$out = $textContent;
						break;

					case 'h1':
					case 'h2':
					case 'h3':
					case 'h4':
					case 'h5':
					case 'h6':
						$head = str_repeat("=", $node->nodeName{1});
						if(!empty($nodeData)) {
							$linesBefore = ((($previousNode = $this->getPreviousElementNode($node)) && $this->isHeaderNode($previousNode)) || empty($node->previousSibling)) ? 0 : ($nodeData['linesBefore']+1)%2;
							$linesAfter = $nodeData['linesAfter']-1;
						} else {
							$linesBefore = 0;
							$linesAfter = 1;
							$textContent = " ".trim($textContent)." ";
						}

						$out = str_repeat("\n", $linesBefore) . $head . $textContent . $head . str_repeat("\n", $linesAfter);
						break;

					// text formatting
					// 1 '</b><i><b>' => '<i>'
					// 2 '</i><b><i>' => '<b>'
					// 3 '</b></i><b>' => '</i>'
					// 4 '</i></b><i>' => '</b>'
					case 'i':
					case 'b':
						switch($node->nodeName) {
							case 'i':
								$open = $close = "''";
								break;
							case 'b':
								$open = $close = "'''";
								break;
						}

						// A) opening tags
						// 1, 2
						if($node->parentNode && $node->parentNode->previousSibling &&
							$node->isSameNode($node->parentNode->firstChild) &&
							in_array($node->parentNode->nodeName, array('i','b')) &&
							$node->parentNode->nodeName != $node->nodeName &&
							$node->parentNode->previousSibling->nodeName == $node->nodeName) {
							// don't open bold (1) / italic (2)
							$open = '';
						}

						// 3, 4
						if($node->previousSibling && $node->previousSibling->hasChildNodes() &&
							in_array($node->previousSibling->nodeName, array('i','b')) &&
							$node->previousSibling->nodeName != $node->nodeName &&
							$node->previousSibling->lastChild->nodeName == $node->nodeName) {
							// don't open bold (3) / italic (4)
							$open = '';
						}

						// B) closing tags
						// 1, 2
						if($node->nextSibling && $node->nextSibling->hasChildNodes() &&
							in_array($node->nextSibling->nodeName, array('i','b')) &&
							$node->nextSibling->nodeName != $node->nodeName &&
							$node->nextSibling->firstChild->nodeName == $node->nodeName) {
							// don't close bold (1) / italic (2)
							$close = '';
						}

						// 3, 4
						if($node->parentNode && $node->parentNode->nextSibling &&
							$node->isSameNode($node->parentNode->lastChild) &&
							in_array($node->parentNode->nodeName, array('i','b')) &&
							$node->parentNode->nodeName != $node->nodeName &&
							$node->parentNode->nextSibling->nodeName == $node->nodeName) {
							// don't close bold (3) / italic (4)
							$close = '';
						}

						$out = "{$open}{$textContent}{$close}";
						break;

					case 'tbody':
						$out = $textContent;
						break;
				}

			} else {

				$attstr = $this->getAttributesStr($node);

				if($node->nodeName == 'br' || $node->nodeName == 'hr') {
					$out = "<{$node->nodeName}{$attStr} />";
				} else {
					// remove prohibited HTML tags (may occur after pasting content from external site)
					if(in_array($node->nodeName, array('script', 'embed'))) {
						$out = '';
						break;
					}
					$out = "<{$node->nodeName}{$attStr}>{$textContent}</{$node->nodeName}>";
				}

			}

			// if current processed node contains attribute _wysiwyg_new_line (added in Parser.php)
			// then add new line before it
			if($node->getAttribute('_wysiwyg_new_line') && (!$this->isHeaderNode($node) || empty($node->previousSibling))) {
				$out = "\n" . $out;
			}

		} else if($node->nodeType == XML_COMMENT_NODE) {


		} else if($node->nodeType == XML_TEXT_NODE) {

			// if the next sibling node of the current one text node is comment (NEW_LINE_1)
			// then cut the last character of current text node (it must be space) and add new line
			// e.g. "abc <!--NEW_LINE_1-->" => "abc\n"
			if($node->nextSibling && $node->nextSibling->nodeType == XML_COMMENT_NODE && $node->nextSibling->data == "NEW_LINE_1") {
				$textContent = substr($textContent, 0, -1) . "\n";
			}

			$out = $textContent;

		}

		wfProfileOut(__METHOD__);
		return $out;
	}

	private function getPreviousElementNode($node) {
		$temp = $node;
		while($node->previousSibling) {
			$node = $node->previousSibling;
			if($node->nodeType == XML_ELEMENT_NODE) {
				return $node;
			}
		}
		return false;
	}

	private function isHeaderNode($node) {
		return ($node->nodeName{0} == 'h') && is_numeric($node->nodeName{1});
	}

	/**
	 * input: '<div id="123" washtml="true" _wysiwyg_new_line="false" style="border: solid 1px">'
	 * output: ' id="123" style="border: solid 1px"'
	 */
	private function getAttributesStr($node) {
		if(!$node->hasAttributes()) {
			return '';
		}
		$attStr = '';
		foreach ($node->attributes as $attrName => $attrNode) {
			if($attrName == 'washtml' || $attrName == '_wysiwyg_new_line') {
				continue;
			}
			$attStr .= ' ' . $attrName . '="' . $attrNode->nodeValue  . '"';
		}
		return $attStr;
	}
}