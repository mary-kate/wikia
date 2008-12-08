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

		switch($node->nodeType) {
			case XML_ELEMENT_NODE:

				$refid = $node->getAttribute('refid');

				if(!empty($refid)) {
					$nodeData = $this->data[$refid];
				}

				switch($node->nodeName) {
					case 'body':
						$out = $textContent;
						break;

					case 'br':
						$out = "\n\n";
						break;

					case 'p':

						if($node->previousSibling->nodeName == 'p') {
							$textContent = "\n" . $textContent;
							if($node->firstChild->nodeName == 'br') {
								$textContent = substr($textContent, 1);
							}
						}

						if($textContent == '') {
							$textContent = "\n";
						}

						$out = $textContent."\n";
						break;

/*
					case 'h1':
					case 'h2':
					case 'h3':
					case 'h4':
					case 'h5':
					case 'h6':
						$tag = str_repeat('=', intval($node->nodeName{1}));
						$out = $tag.$node->textContent.$tag;

						if(!empty($this->fckData[$refid])) {
							$out = str_repeat("\n", $this->fckData[$refid]['linesBefore']).$out.str_repeat("\n", $this->fckData[$refid]['linesAfter']);
						} else {
							$out .= "\n";
						}

						break;
*/
				}
				break;

			case XML_TEXT_NODE:
				if($node->previousSibling->nodeName == 'br' && $textContent{0} == " ") {
					$textContent = substr($textContent, 1);
				}
				$out = $textContent;
				break;
		}

		wfProfileOut(__METHOD__);
		return $out;
	}

}
