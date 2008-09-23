<?php
/**
 * PHP Reverse Parser - Processes html and provides a one-way
 * transformation into wikimarkup
 *
 * @author Maciej 'macbre' Brencz <macbre(at)wikia-inc.com>
 * @author Inez Korczynski <inez(at)wikia-inc.com>
 */
class ReverseParser {

	// DOMdocument object used to parse HTML
	private $dom;

	// used by nested lists parser
	private $listLevel = 0;

	// bullets stack for nested lists
	private $listBullets = '';

	// refIds data from JSON array
	private $fckData = array();

	// level => nodeName
	private $lastNodeName = array();

	// nodes tree
	private $nodesTree = array();

	function __construct() {
		$this->dom = new DOMdocument();
	}

	/**
	 * Parse provided HTML into wikimarkup
	 */
	public function parse($html, $fckData = array()) {
		wfProfileIn(__METHOD__);

		if (!is_string($html) || $html == '') {
			return '';
		}

		$output = '';

		// refIds
		$this->fckData = $fckData;

		// fix UTF-8 bug
		$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

		// load HTML into DOMdocument
		wfSuppressWarnings();
		$valid = $this->dom->loadHTML($html);
		wfRestoreWarnings();

		if (!$valid) {
			return '';
		}

		wfDebug("WYSIWYG ReverseParser parse for:\n{$html}\n");

		// cleanup
		$this->listLevel = 0;
		$this->nodesTree = array();

		// let's begin with <body> node
		$body = $this->dom->getElementsByTagName('body')->item(0);

		// nothing inside body?
		if (!$body->hasChildNodes()) {
			return '';
		}

		// go through body tag children
		$nodes = $body->childNodes;

		for ($n=0; $n < $nodes->length; $n++) {
			$output .= $this->parseNode($nodes->item($n));
		}

		$this->nodesTree = array();

		wfProfileOut(__METHOD__);

		return rtrim($output);
	}

	/**
	 * Convert HTML node into wikimarkup
	 *
	 * If node has children, call parseNode for every children
	 */

	private function parseNode($node, $level = 0) {
		wfProfileIn(__METHOD__);

		$output = '';

		wfDebug("WYSIWYG ReverseParser parseNode for nodeName: {$node->nodeName} on level: {$level}\n");

		// build nodes tree
		if ($node->nodeType == XML_ELEMENT_NODE) {
			$this->nodesTree[$level][] = $node->nodeName;
		}
		else {
			$this->nodesTree[$level][] = '#text';
		}

		$level++;

		// recursively parse child nodes
		if ($node->hasChildNodes()) {
			$nodes = $node->childNodes;

			$childOutput = '';

			// handle nested lists
			$isListNode = in_array($node->nodeName, array('ul', 'ol', 'dl'));

			if ($isListNode) {
				$this->listLevel++;
				// build bullets stack
				switch ($node->nodeName) {
					case 'ul':
						$bullet = '*';
						break;
					case 'ol':
						$bullet = '#';
						break;
					case 'dl':
						$bullet = ':';
						break;
				}
				$this->listBullets .= $bullet;
			}

			// parse child nodes
			$this->nodesTree[$level] = array();

			for ($n=0; $n < $nodes->length; $n++) {
				$childOutput .= $this->parseNode($nodes->item($n), $level);
			}

			unset($this->nodesTree[$level]);

			if ($isListNode) {
				// fix for different list types on the same level of nesting
				if ($node->previousSibling && in_array($node->previousSibling->nodeName, array('ol', 'ul', 'dl')) && $this->listLevel > 1) {
					$childOutput = "\n" . trim($childOutput);
				} else {
					$childOutput = trim($childOutput);
				}

				$this->listLevel--;
				$this->listBullets = substr($this->listBullets, 0, -1);
			}
		} else {
			$childOutput = false;
		}

		switch ($node->nodeType) {
			case XML_ELEMENT_NODE:

				$wasHTML = $node->getAttribute('washtml');
				$content = ($childOutput !== false) ? $childOutput : $this->cleanupTextContent($node->textContent);

				// if originally specified tag was an html then save it back as html
				if (!empty($wasHTML)) {

					$attStr = $this->getAttributesStr($node);

					switch ($node->nodeName) {
						case 'br':
							$output = "<br{$attStr} />";
							break;

						default:
							// nice formatting of nested HTML in wikimarkup
							if ($node->hasChildNodes() && $node->childNodes->item(0)->nodeType != XML_TEXT_NODE) {
								// node with child nodes
								$content = "\n".trim($content)."\n";
								$trial = "\n";
							} else {
								$trial = '';
							}
							$output = "<{$node->nodeName}{$attStr}>{$content}</{$node->nodeName}>{$trial}";
					}
				} else {
					// if tag wasn't an html before then parse it to wikitext
					switch ($node->nodeName) {

						// basic inline formatting
						case 'b':
						case 'strong':
							$output = "'''{$content}'''";
							break;

						case 'i':
						case 'em':
							$output = "''{$content}''";
							break;

						case 'br':
							$output = '';
							// TODO: there should be usually \n after br tag, but it does not work always
							// e.g. in all types of lists
							break;

						case 'hr':
							$output = "----\n";
							break;

						case 'p':
							// handle indentations
							$indentation = $this->getIndentationLevel($node);
							if ($indentation !== false) {
								$prefix = str_repeat(':', $indentation);
							} else {
								$prefix = '';
							}
							$output = $prefix . $content;
							break;

						case 'h1':
							$output = "= {$content} =";
							break;

						case 'h2':
							$output = "== {$content} ==";
							break;

						case 'h3':
							$output = "=== {$content} ===";
							break;

						case 'h4':
							$output = "==== {$content} ====";
							break;

						case 'h5':
							$output = "===== {$content} =====";
							break;

						case 'h6':
							$output = "====== {$content} ======";
							break;

						case 'pre':
							$content = trim(str_replace("\n", "\n ", $content));	// add white space before each line
							$output = " {$content}";
							break;

						// tables
						// @see http://en.wikipedia.org/wiki/Help:Table
						case 'table':
							$attStr = $this->getAttributesStr($node);
							$output = "{|{$attStr}\n{$content}|}\n";
							break;

						case 'tr':
							$output = "|-\n{$content}";
							break;

						case 'th':
							$output = "!{$content}";
							break;

						case 'td':
							$output = "|{$content}\n";
							break;

						// ignore tbody tag
						case 'tbody':
							$output = $content;
							break;

						// lists
						case 'ul':
						case 'ol':
						case 'dl':
							// handle indentations
							if ($node->nodeName == 'dl') {
								$indentation = $this->getIndentationLevel($node);
								$prefix = '';
								if ($indentation !== false) {
									$prefix = str_repeat(':', $indentation);
								}
							} else {
								$prefix = '';
							}
							if($node->previousSibling) {
								$prefix = "\n".$prefix;
							}
							$output = $prefix . $content . ($this->listLevel == 0 ? "\n" : '');
							break;
						case 'li':
						case 'dd':
						case 'dt':
							$output = $this->handleListItem($node, $content);
							break;
						// handle more complicated tags
						case 'a':
							$output = $this->handleLink($node, $content);
							break;
						case 'span':
							$output = $this->handleSpan($node);
							break;
						// HTML tags
						default:
							$attr = $this->getAttributesStr($node);
							$output = "<{$node->nodeName}{$attr}>{$content}</{$node->nodeName}>";
					}
				}
				break;

			case XML_TEXT_NODE:
				if(trim($node->textContent) == '') {
					$output = '';
				} else {
					$output = $this->cleanupTextContent($node->textContent);
				}
				break;

		}

		//newline adding logic based on node context
		//var_dump($this->nodesTree);

		$currentNodeIndex = count($this->nodesTree[$level-1]) - 1;

		$previousNode = ($currentNodeIndex > 0) ? $this->nodesTree[$level-1][$currentNodeIndex - 1] : false;
		$parentNode = ($level > 1) ? end($this->nodesTree[$level-2]) : false;

		$prefix = $suffix = '';

		switch($node->nodeName) {
			case 'p':
				// header before paragraph
				if ($previousNode !== false && $previousNode{0} == 'h' && is_numeric($previousNode{1})) {
					$prefix = "\n";
				}
				// paragraph after paragraph
				else if ($previousNode == 'p') {
					$prefix = "\n\n";
				}
				// fix for empty paragraphs
				if ($output == '') {
					$output = "\n";
				}
				break;

			case 'br':
				//var_dump($this->nodesTree); var_dump($parentNode); var_dump($currentNodeIndex);
				if ($parentNode == 'p' && $currentNodeIndex == 0) {
					$output = "\n";
				} 
				else {
					$output = '<br />';
				}
				break;
		}

		$output = $prefix . $output . $suffix;

		wfProfileOut(__METHOD__);

		return $output;
	}

	/**
	 * Returns HTML string containing node arguments
	 */
	private function getAttributesStr($node) {

		if (!$node->hasAttributes()) {
			return '';
		}

		$attStr = '';
		foreach ($node->attributes as $attrName => $attrNode) {
			if ($attrName == 'washtml') {
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
	private function handleSpan($node) {

		// handle spans with refId attribute: images, templates etc.
		$refId = $node->getAttribute('refid');

		if ( is_numeric($refId) && isset($this->fckData[$refId]) ) {
			$refData = (array) $this->fckData[$refId];

			switch($refData['type']) {
				// [[Image:foo.jpg]]
				case 'image':
				// [[Media:foo.jpg]]
				case 'internal link: media':
					$pipe = ($refData['description'] != '') ? '|'.$refData['description'] : '';
					return "[[{$refData['href']}{$pipe}]]";

				// <gallery></gallery>
				case 'gallery':
					return $refData['description'];

				// <nowiki></nowiki>
				case 'nowiki':
					return "<nowiki>{$refData['description']}</nowiki>";

				// [[Category:foo]]
				case 'category':
					$pipe = ($refData['description'] != '') ? '|'.$refData['description'] : '';
					return "\n[[{$refData['href']}{$pipe}]]{$refData['trial']}";

				// parser hooks
				case 'hook':
					return $refData['description'];

				// {{template}}
				case 'curly brackets':
					return $refData['description'];

				// __TOC__
				case 'double underscore':
					return $refData['description'];
			}
		}

		return '<!-- unsupported span tag! -->';
	}

	/**
	 * Returns wikimarkup for <a> tag
	 */
	private function handleLink($node, $content) {

		// handle links with refId attribute
		$refId = $node->getAttribute('refid');

		if ( is_numeric($refId) && isset($this->fckData[$refId]) ) {
			$refData = (array) $this->fckData[$refId];

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
	 * Returns wikimarkup for ordered, unordered and definition lists
	 */
	private function handleListItem($node, $content) {
		switch($node->nodeName) {
			case 'li':
				$content = ' ' . ltrim($content, ' ') . "\n";
				return $this->listBullets . $content;
			case 'dt':
				return substr($this->listBullets, 0, -1) . ";{$node->textContent}\n";
			case 'dd':
				// hack for :::::foo markup used for indentation
				// TODO: explain this hack
				if ($node->hasChildNodes() && $node->childNodes->item(0)->nodeName == 'dl') {
					return $content . "\n";
				} else {
					return $this->listBullets . $content . "\n";
				}
			}
	}

	/**
	 * Returns value of margin-left CSS property (FALSE if none)
	 */
	private function getIndentationLevel($node) {
		if ( !$node->hasAttributes() ) {
			return false;
		}

		$cssStyle = $node->getAttribute('style');

		if (!empty($cssStyle)) {
			$margin = (substr($cssStyle, 0, 11) == 'margin-left') ? intval(substr($cssStyle, 12)) : 0;
			return intval($margin/40);
		}

		return false;
	}

	/**
	 * Clean up node text content
	 */
	private function cleanupTextContent($text) {

		if (empty($text)) {
			return '';
		}

		wfDebug("WYSIWYG ReverseParser cleanupTextContent for: {$text}\n");

		// 1. wrap repeating apostrophes using <nowiki>
		$text = preg_replace("/('{2,})/", '<nowiki>$1</nowiki>', $text);

		// 2. wrap = using <nowiki>
		$text = preg_replace("/([=]+)/", '<nowiki>$1</nowiki>', $text);

		// 3. wrap list bullets using <nowiki>
		$text = preg_replace("/^([#*]+)/", '<nowiki>$1</nowiki>', $text);

		// 4. semicolon at the beginning of the line
		if (in_array($text{0}, array(':', ';'))) {
			$text = '<nowiki>' . $text{0} . '</nowiki>' . substr($text, 1);
		}

		// 5. wrap magic words {{ }} using <nowiki>
		$text = preg_replace("/({{2,3})([^}]+)(}{2,3})/", '<nowiki>$1$2$3</nowiki>', $text);

		// 6. wrap [[foo]] using <nowiki>
		$text = preg_replace("/(\[+)([^\]]+)(\]+)/", '<nowiki>$1$2$3</nowiki>', $text);

		return $text;
	}
}
