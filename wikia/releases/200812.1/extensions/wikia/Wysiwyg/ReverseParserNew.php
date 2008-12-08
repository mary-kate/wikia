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
			// cleanup
			$replacements = array(
				' <h' => '<h',
				'<p><br /></p>' => "\n"
			);

			//$html = strtr($html, $replacements);

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

				$refid = $node->getAttribute('refid');

				switch($node->nodeName) {
					case 'body':
						$out = $childOut;
						break;

					case 'p':
						$out =  ($this->previousNodeIs($node, 'p') ? "\n" : '') . $node->textContent . "\n";
						break;

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
				}
				break;

			case XML_TEXT_NODE:
				$out = $node->textContent;
				break;
		}

		wfProfileOut(__METHOD__);
		return $out;
	}

	private function previousNodeIs($node, $name) {
		return ($node->previousSibling && $node->previousSibling->nodeName == $name);
	}
}
