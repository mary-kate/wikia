<?php
/**
 * PHP Reverse Parser - Processes html and provides a one-way
 * transformation into wikimarkup
 *
 * @author Maciej 'macbre' Brencz <macbre(at)wikia-inc.com>
 */
class ReverseParser
{
	var $dom;	// DOMdocument object used to parse HTML

	function __construct() {
		$this->dom = new DOMdocument();
	}

	/**
         * Parse HTML provided into wikimarkup
         */
	public function parse($html) {
		wfProfileIn(__METHOD__);
		$output = '';

		// load HTML into DOMdocument
		$this->dom->loadHTML($html);

		// let's begin with <body> node
		$body = $this->dom->getElementsByTagName('body')->item(0);

		// nothing inside body?
		if ( !$body->hasChildNodes() ) {
			return false;
		}

		// go through body tag children
		$nodes = $body->childNodes;

		for ($n=0; $n < $nodes->length; $n++) {
			$output .= $this->parseNode($nodes->item($n));
		}

		wfProfileOut(__METHOD__);

		return $output;
	}

	/**
	 * Convert HTML node into wikimarkup
	 *
	 * If node has children, call parseNode for every children
	 */

	private function parseNode($node, $level = 0) {
		wfProfileIn(__METHOD__);

		$level++;

		wfDebug(__METHOD__. str_repeat(':', $level) . "{$node->nodeName} ({$node->nodeType})\n");


		$output = '';

		// recursively parse child nodes
		if ( $node->hasChildNodes() ) {
			$nodes = $node->childNodes;

			$childOutput = '';

			for ($n=0; $n < $nodes->length; $n++) {
				$childOutput .= $this->parseNode($nodes->item($n), $level);
			}
		}

		switch( $node->nodeType ) {
			case XML_ELEMENT_NODE:
				$wasHTML = $node->getAttribute('washtml');

				$content = !empty($childOutput) ? $childOutput : $node->textContent;

				// parse it back to HTML tag
				if (!empty($wasHTML)) {
					$output = "<{$node->nodeName}>{$content}</{$node->nodeName}>";
				}
				else {
					switch ($node->nodeName) {
						case 'b':
							$output = "'''{$content}'''";
						break;
						case 'i':
							$output = "''{$output}''";
						break;
					}
				}
				break;
			case XML_TEXT_NODE:
				$output = $node->textContent;
				break;
		}

		wfProfileOut(__METHOD__);

		return $output;
	}
}
