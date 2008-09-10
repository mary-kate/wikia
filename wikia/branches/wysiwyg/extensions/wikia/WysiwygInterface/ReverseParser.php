<?php
/**
 * PHP Reverse Parser - Processes html and provides a one-way
 * transformation into wikimarkup
 *
 * @author Maciej 'macbre' Brencz <macbre(at)wikia-inc.com>
 */
class ReverseParser
{
	// DOMdocument object used to parse HTML
	var $dom;

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
		
		$output = '';
		$level++;

		wfDebug(__METHOD__. str_repeat(':', $level) . "{$node->nodeName} ({$node->nodeType})\n");

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

				$content = isset($childOutput) ? $childOutput : $node->textContent;

				// parse it back to HTML tag
				if (!empty($wasHTML)) {
					$attStr = self::getAttributesStr($node);
					$output = "<{$node->nodeName}{$attStr}>{$content}</{$node->nodeName}>";
				}
				// convert HTML back to wikimarkup
				else {
					switch ($node->nodeName) {
						// basic formatting
						case 'b':
							$output = "'''{$content}'''";
							break;

						case 'i':
							$output = "''{$content}''";
							break;

						case 'p':
							$output = "\n{$content}\n";
							break;

						case 'h1':
							$output = "\n={$content} =\n";
							break;

						case 'h2':
							$output = "\n=={$content} ==\n";
							break;

						case 'h3':
							$output = "\n==={$content} ===\n";
							break;
		
						case 'h4':
							$output = "\n===={$content} ====\n";
							break;

						case 'h5':
							$output = "\n====={$content} =====\n";
							break;

						case 'h6':
							$output = "\n======{$content} ======\n";
							break;

						case 'br':
							$output = "\n";
							break;

						case 'hr':
							$output = "\n---\n";
							break;

						case 'pre':
							$content = trim(str_replace("\n", "\n ", $content));	// add white space before each line
							$output = "\n {$content}\n";
							break;

						// lists
						// TODO: handle nested lists
						case 'ul';
						case 'ol':
						case 'dl':
							$output = $childOutput;
							break;

						case 'li':
						case 'dd':
						case 'dt':
							$output = self::handleList($node);
							break;

						// handle more complicated tags
						case 'a':
							$output = self::handleAnchor($node);
							break;

						case 'span':
							$output = self::handleSpan($node);
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

	/**
	 * Returns HTML string containing node arguments
	 */
	static function getAttributesStr($node) {

		if ( !$node->hasAttributes() ) {
			return '';
		}

		$attStr = '';

		foreach ($node->attributes as $attrName => $attrNode) {
			if( $attrName == 'washtml') {
				continue;
			}
			$attStr .= ' ' . $attrName . '="' . $attrNode->nodeValue  . '"';
		}

		return $attStr;
	}


	/**
	 * Returns wikimarkup for <span> tag
	 *
	 * Span is used to wrap various elements like templates etc.
	 */

	static function handleSpan($node) {

		// tag context
		$tagBefore = $node->previousSibling;
		$tagAfter = $node->nextSibling;
		$tagParent = $node->parentNode;

		// remove span automagically added inside <hx>
		if ( $tagParent->nodeName{0} == 'h' ) {
			return trim($node->textContent, ' ');
		}
	}

	/**
	 * Returns wikimarkup for <a> tag
	 */

	static function handleAnchor($node) {

		// tag context
		$tagBefore = $node->previousSibling;
		$tagAfter = $node->nextSibling;
		$tagParent = $node->parentNode;

		// remove anchor automagically added before <hx>
		if ( $tagAfter->nodeName{0} == 'h' ) {
			return '';
		}
	}

	/**
	 * Returns wikimarkup for (un)ordered / definition lists
	 */

	static function handleList($node) {

		switch($node->nodeName) {
			case 'li':	
				$bullet = ($node->parentNode->nodeName == 'ul') ? '#' : '*';
				return $bullet . $node->textContent;

			case 'dt':
				return ':' . $node->textContent;
				break;

			case 'dd':
				return ';' . $node->textContent;
				break;
		}
	}
}
