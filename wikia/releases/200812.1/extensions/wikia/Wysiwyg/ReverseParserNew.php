<?php
/**
 * PHP Reverse Parser - Processes given HTML into DOM tree and
 * transform it into wikimarkup (new development version)
 *
 * @author Maciej 'macbre' Brencz <macbre(at)wikia-inc.com>
 * @author Inez Korczynski <inez(at)wikia-inc.com>
 *
 * @see http://meta.wikimedia.org/wiki/Help:Editing
 */
class ReverseParser {

	// DOMDocument
	private $dom;

	// FCK meta data
	private $fckData = array();

	// cache results of wfUrlProtocols()
	private $protocols;

	function __construct() {
		$this->dom = new DOMdocument();
		$this->protocols = wfUrlProtocols();
	}

	public function parse($html, $wysiwygData = array()) {
		wfProfileIn(__METHOD__);

		$out = '';

		if(is_string($html) && $html != '') {
			// fix for proper encoding of UTF characters
			$html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body>'.$html.'</body></html>';

			$this->fckData = $wysiwygData;

			wfDebug("metaData: ".print_r($this->fckData, true)."\n");

			wfDebug("ReverseParserNew HTML: {$html}\n");

			wfSuppressWarnings();
			if($this->dom->loadHTML($html)) {
				$body = $this->dom->getElementsByTagName('body')->item(0);
				wfDebug("ReverseParser HTML from DOM: ".$this->dom->saveHTML()."\n");
				$out = $this->parseNode($body);
			}
			wfRestoreWarnings();

			wfDebug("ReverseParserNew wikitext: {$out}\n");
		}

		wfProfileOut(__METHOD__);
		return $out;
	}

	private function parseNode($node, $level = 0) {
		wfProfileIn(__METHOD__);

		wfDebug('ReverseParser: ' . str_repeat(' ', $level) . $node->nodeName . "\n");

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

		switch($node->nodeType) {
			case XML_ELEMENT_NODE:
				switch($node->nodeName) {
					case 'body':
						$out = $childOut;
						break;

					case 'p':
						$out = $node->textContent."\n";
						break;

					case 'h1':
					case 'h2':
						$level = intval($node->nodeName{1});
						$out = str_repeat('=', $level) . $node->textContent . str_repeat('=', $level);
						break;
				}
				break;
		}

		wfProfileOut(__METHOD__);
		return $out;
	}
}
