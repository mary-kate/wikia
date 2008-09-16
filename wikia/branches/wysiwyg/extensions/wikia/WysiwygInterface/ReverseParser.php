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
	private $dom;

	// used by nested lists parser
	private static $listLevel = 0;

	// bullets stack for nested lists
	private static $listBullets = '';

	// refIds data from JSON array
	private static $fckData = array();

	function __construct() {
		$this->dom = new DOMdocument();
	}

	/**
         * Parse HTML provided into wikimarkup
         */
	public function parse($html, $fckData = array()) {
		wfProfileIn(__METHOD__);
		$output = '';

		// refIds
		self::$fckData = $fckData;

		wfDebug(__METHOD__.": HTML\n\n{$html}\n\n");
		wfDebug(__METHOD__.": fckData\n\n".print_r($fckData, true)."\n");

		// load HTML into DOMdocument
		wfSuppressWarnings();
		$valid = $this->dom->loadHTML($html);
		wfRestoreWarnings();

		if (!$valid) {
			return false;
		}

		// cleanup
		self::$listLevel = 0;

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

		wfDebug(__METHOD__.": wiki\n\n{$output}\n\n");

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

		//wfDebug(__METHOD__. str_repeat(':', $level) . "{$node->nodeName} ({$node->nodeType})\n");

		// recursively parse child nodes
		if ( $node->hasChildNodes() ) {
			$nodes = $node->childNodes;

			$childOutput = '';

			// handle nested lists
			$isListNode = in_array($node->nodeName, array('ul', 'ol'));

			if ($isListNode) {
				self::$listLevel++;
				// build bullets stack
				self::$listBullets .= ($node->nodeName == 'ul') ? '*' : '#';
			}


			for ($n=0; $n < $nodes->length; $n++) {
				$childOutput .= $this->parseNode($nodes->item($n), $level);
			}

			if ($isListNode) {
				self::$listLevel--;
				self::$listBullets = substr(self::$listBullets, 0, -1);
			}
		}

		switch( $node->nodeType ) {
			case XML_ELEMENT_NODE:
				$wasHTML = $node->getAttribute('washtml');

				$content = isset($childOutput) ? $childOutput : self::cleanupTextContent($node->textContent);

				// parse it back to HTML tag
				if (!empty($wasHTML)) {

					$attStr = self::getAttributesStr($node);

					switch ($node->nodeName) {
						case 'br':
							$output = "<br{$attStr} />";
							break;

						default:
							$output = "<{$node->nodeName}{$attStr}>{$content}</{$node->nodeName}>";
					}
				}
				// convert HTML back to wikimarkup
				else {
					switch ($node->nodeName) {
						// basic formatting
						case 'b':
						case 'strong':
							$output = "'''{$content}'''";
							break;

						case 'i':
						case 'em':
							$output = "''{$content}''";
							break;

						case 'p':
							$output = "{$content}\n";
							break;

						case 'h1':
							$output = "= {$content} =\n";
							break;

						case 'h2':
							$output = "== {$content} ==\n";
							break;

						case 'h3':
							$output = "=== {$content} ===\n";
							break;
		
						case 'h4':
							$output = "==== {$content} ====\n";
							break;

						case 'h5':
							$output = "===== {$content} =====\n";
							break;

						case 'h6':
							$output = "====== {$content} ======\n";
							break;

						case 'br':
							$output = '<br />';
							break;

						case 'hr':
							$output = "\n----\n";
							break;

						case 'pre':
							$content = trim(str_replace("\n", "\n ", $content));	// add white space before each line
							$output = " {$content}\n";
							break;

						// tables
						// @see http://en.wikipedia.org/wiki/Help:Table
						case 'table':
							$attStr = self::getAttributesStr($node);
							$output = "\n{|{$attStr}\n{$content}|}\n";
							break;

						case 'tr':
							$output = "|-\n{$content}";
							break;
	
						case 'th':
							$output = "!{$content}";
							break;
					     
						case 'td':
							$output = "|{$content}";
							break;
						
						// ignore tbody tag
						case 'tbody':
							$output = $content;
							break;

						// lists
						case 'ul':
						case 'ol':
							$output = trim($content) . (self::$listLevel == 0 ? "\n" : '');
							break;

						case 'dl':
							$output = $content;

							// make space before next <dl> list
							if ($node->nextSibling && $node->nextSibling->nodeName == 'dl') {
								$output .= "\n";
							}
							break;

						case 'li':
						case 'dd':
						case 'dt':
							$output = self::handleListItem($node, $content);
							break;

						// handle more complicated tags
						case 'a':
							$output = self::handleLink($node, $content);
							break;

						case 'span':
							$output = self::handleSpan($node);
							break;

						// HTML tags
						default:
							$attr = self::getAttributesStr($node);
							$output = "<{$node->nodeName}{$attr}>{$content}</{$node->nodeName}>";
					}
				}
				break;

			case XML_TEXT_NODE:
				$output = self::cleanupTextContent($node->textContent);
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

		// handle spans with refId attribute: images, templates etc.
		$refId = $node->getAttribute('refid');

		if ( is_numeric($refId) && isset(self::$fckData[$refId]) ) {
			$refData = (array) self::$fckData[$refId];

			switch($refData['type']) {
				// [[Image:foo.jpg]]
				case 'image':
				// [[Media:foo.jpg]]
				case 'internal link: media':
					$pipe = ($refData['description'] != '') ? '|'.$refData['description'] : '';
					return "[[{$refData['href']}{$pipe}]]";

				// <gallery></gallery>
				case 'gallery':
					return $node->textContent;

				// <nowiki></nowiki>
				case 'nowiki':
					return "<nowiki>{$node->textContent}</nowiki>";
			}
		}

		return '<!-- unsupported span tag! -->';
	}

	/**
	 * Returns wikimarkup for <a> tag
	 */

	static function handleLink($node, $content) {

		// handle links with refId attribute
		$refId = $node->getAttribute('refid');

		if ( is_numeric($refId) && isset(self::$fckData[$refId]) ) {
			$refData = (array) self::$fckData[$refId];

			// allow formatting of link description
			if ($refData['description'] != '') {

				// $content contains parsed link description (wikitext)
				if ($refData['trial'] != '' ) {
					// $trial (if not empty) is at the end of $content - remove it
					$refData['description'] = substr($content, 0, -strlen($refData['trial']));
				}
				else {
					$refData['description'] = $content;
				}

				// description after pipe
				$pipe = '|'.$refData['description'];
			}
			else {
				$pipe = '';
			}

			// handle various type of links
			switch($refData['type']) {
				// [[foo|bar]]s
				case 'internal link':
				// [[:Image:Jimbo.jpg]]
				case 'internal link: file':
					return "[[{$refData['href']}{$pipe}]]{$refData['trial']}";
			}
		}
		// handle HTML links <a href="http://foo.net">bar</a>
		// TODO: handle local links
		else {
			$href = $node->getAttribute('href');
			$desc = $node->textContent;

			return "[{$href} {$desc}]";
		}

		return '<!-- unsupported anchor tag! -->';
	}

	/**
	 * Returns wikimarkup for (un)ordered / definition lists
	 */

	static function handleListItem($node, $content) {

		switch($node->nodeName) {
			case 'li':
				$bullet = ($node->parentNode->nodeName == 'ul') ? '*' : '#';
				$content = ' ' . ltrim($content, ' ');
				return self::$listBullets . $content;

			case 'dt':
				return ";{$node->textContent}";

			case 'dd':
				return ":{$node->textContent}";
		}
	}

	/**
	 * Clean up node text content
	 */
	static function cleanupTextContent($text) {

		if (empty($text)) {
			return $text;
		}

		// 1. wrap repeating apostrophes using <nowiki>
		$text = preg_replace("/('{2,})/", '<nowiki>$1</nowiki>', $text);

		// 2. wrap list bullets using <nowiki>
		$text = preg_replace("/([#*]+)/", '<nowiki>$1</nowiki>', $text);

		// 3. semicolon at the beginning of the line
		if ($text{0} == ':') {
			$text = '<nowiki>:</nowiki>' . substr($text, 1);
		}

		// 4. wrap magic words {{ }} using <nowiki>
		$text = preg_replace("/{{([^}]+)}}/", '<nowiki>{{$1}}</nowiki>', $text);

		// 5. wrap [[foo]] using <nowiki>
		$text = preg_replace("/(\[+)([^}]+)(\]+)/", '<nowiki>$1$2$3</nowiki>', $text);

		return $text;
	}
}

