<?php
/**
 * PHP Reverse Parser - Processes html and provides a one-way
 * transformation into wikimarkup
 *
 * @author Maciej 'macbre' Brencz <macbre(at)wikia-inc.com>
 * @author Inez Korczynski <inez(at)wikia-inc.com>
 */
class ReverseParser {

	private $dom;

	// used by nested lists parser
	private $listLevel = 0;

	// bullets stack for nested lists
	private $listBullets = '';

	function __construct() {
		$this->dom = new DOMdocument();
	}

	public function parse($html, $wysiwygData = array()) {
		wfProfileIn(__METHOD__);

		$out = '';

		if(is_string($html) && $html != '') {
			$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

			wfProfileIn(__METHOD__."-cleanup");

			// HTML cleanup - remove whitespaces between tags
			$html = preg_replace("/>([\s]+)<p>/", '><p>', $html); // before <p> tag
			$html = preg_replace("/<\/p>([\s]+)</", '</p><', $html); // after </p> tag
			$html = preg_replace("/p>([\s]+)<br/", 'p><br', $html); // between <p> and <br /> tag
			$html = str_replace('</dl> </dd>', '</dl></dd>', $html); // between </dl> and </dd> tag
			$html = str_replace('</li> <li', '</li><li', $html); // between li tags defined as html

			// remove whitespace after <br /> and decode &nbsp;
			$html = str_replace(array('<br /> ', '&nbsp;'), array('<br />', ' '), $html);

			$this->listLevel = 0;
			$this->listBullets = '';

			wfDebug("ReverseParser HTML: {$html}\n");

			wfProfileOut(__METHOD__."-cleanup");

			wfSuppressWarnings();
			if($this->dom->loadHTML($html)) {

				$body = $this->dom->getElementsByTagName('body')->item(0);
				$out = $this->parseNode($body);

			}
			wfRestoreWarnings();

			// final cleanup
			$out = rtrim($out);

			if ($out{0} == "\n" && $out{1} != "\n") {
				// remove ONE empty line from the beginning of wikitext
				$out = substr($out, 1);
			}

			wfDebug("ReverseParser wikitext: {$out}\n");

		}

		wfProfileOut(__METHOD__);
		return $out;
	}

	private function parseNode($node, $level = 0) {
		wfProfileIn(__METHOD__);

		$childOut = '';

		if($node->hasChildNodes()) {

			$nodes = $node->childNodes;

			// handle lists
			$isListNode = in_array($node->nodeName, array('ul', 'ol', 'dl'));

			if($isListNode) {
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

			for($i = 0; $i < $nodes->length; $i++) {
				$childOut .= $this->parseNode($nodes->item($i));
			}

			// handle lists
			if($isListNode) {
				// fix for different list types on the same level of nesting
				if($node->previousSibling && in_array($node->previousSibling->nodeName, array('ol', 'ul', 'dl')) && $this->listLevel > 1) {
					$childOutput = "\n" . trim($childOutput);
				} else {
					$childOutput = trim($childOutput);
				}

				$this->listLevel--;
				$this->listBullets = substr($this->listBullets, 0, -1);
			}
		}

		if($node->nodeType == XML_ELEMENT_NODE) {

			wfDebug("ReverseParser XML_ELEMENT_NODE\n");

			$washtml = $node->getAttribute('washtml');

			$textContent = ($childOut != '') ? $childOut : $this->cleanupTextContent($node->textContent);

			if(empty($washtml)) {
				switch($node->nodeName) {
					case 'body':
						$out = $textContent;
						break;

					case 'br':
						if($node->parentNode && $node->parentNode->nodeName == 'p' && $node->parentNode->hasChildNodes() && $node->parentNode->childNodes->item(0)->isSameNode($node)) {
							$out = "\n";
						} else {
							$out = '<br />';
						}

						break;

					case 'p':
						if($textContent{0} == ' ') {
							$textContent = '<nowiki> </nowiki>' . substr($textContent, 1);
						}

						$out = $textContent;

						// handle indentations
						$indentation = $this->getIndentationLevel($node);
						if ($indentation !== false) {
							$out = str_repeat(':', $indentation) . $out;
						}

						// new line logic
						if($node->previousSibling && $node->previousSibling->nodeName == 'p') {
							// paragraph after paragraph
							$out = "\n\n{$out}";
						} else {
							$out = "\n{$out}";
						}
						break;

					// headings
					case 'h1':
					case 'h2':
					case 'h3':
					case 'h4':
					case 'h5':
					case 'h6':
						$head = str_repeat("=", $node->nodeName{1});
						$out = "{$head} {$textContent} {$head}";

						// new line logic
						if($node->previousSibling) {
							$out = "\n{$out}";
						}
						break;

					// text formatting
					case 'i':
					case 'em':
						if(in_array($node->parentNode->nodeName, array('b', 'strong'))) {
							$open = '<em>';
							$close = '</em>';
						} else {
							$open = $close = "''";
						}

						$out = "{$open}{$textContent}{$close}";
						break;

					case 'b':
					case 'strong':
						if(in_array($node->parentNode->nodeName, array('i', 'em'))) {
							$open = '<strong>';
							$close = '</strong>';
						} else {
							$open = $close = "'''";
						}

						$out = "{$open}{$textContent}{$close}";
						break;

					// lists
					case 'ul':
					case 'ol':
					case 'dl':
						$prefix = $suffix = '';
						// handle indentations created using definition lists
						if($node->nodeName == 'dl') {
							$indentation = $this->getIndentationLevel($node);
							if($indentation !== false) {
								$prefix = str_repeat(':', $indentation);
							}
							// paragraph is following this <dl> list
							if($node->nextSibling && $node->nextSibling->nodeName == 'p') {
								$suffix = ($node->nextSibling->textContent != '') ? "\n" : "\n\n";
							}
						}
						if($node->previousSibling) {
							// first item of nested list
							$prefix = "\n".$prefix;
						}
						// rtrim used to remove \n added by the last list item
						$out = $prefix . rtrim($textContent, " \n") . $suffix;
						break;

					// lists elements
					case 'li':
					case 'dd':
					case 'dt':
						$out = $this->handleListItem($node, $textContent);
						break;

					// ignore tbody tag
					case 'tbody':
						$out = $textContent;
						break;

					// HTML tags
					default:
						$washtml = true;
						break;
				}
			}

			if(!empty($washtml)) {

				$attStr = $this->getAttributesStr($node);

				switch ($node->nodeName) {
					case 'br':
					case 'hr':
						$out = "<{$node->nodeName}{$attStr} />";
						break;

					default:
						// nice formatting of nested HTML in wikimarkup
						if($node->hasChildNodes() && $node->childNodes->item(0)->nodeType != XML_TEXT_NODE) {
							// node with child nodes
							// add \n only when node is HTML block element
							if ($this->isInlineElement($node)) {
								$textContent = trim($textContent);
								$trial = '';
							}
							else {
								$textContent = "\n".trim($textContent)."\n";
								$trial = "\n";
							}
						} else {
							$trial = $this->isInlineElement($node) ? '' : "\n";
						}
						$out = "<{$node->nodeName}{$attStr}>{$textContent}</{$node->nodeName}>{$trial}";

						// add \n after previous non-wasHTML tag
						if ($node->previousSibling && !$node->previousSibling->getAttribute('washtml')) {
							$out = "\n{$out}";
						}
				}

			}

		} else if($node->nodeType == XML_TEXT_NODE) {

			wfDebug("ReverseParser XML_TEXT_NODE\n");

			if(trim($node->textContent, "\n") == '') {
				$out = '';
			} else {
				$out = $this->cleanupTextContent($node->textContent);
			}

		}

		wfProfileOut(__METHOD__);
		return $out;
	}

	/**
	 * Returns wikimarkup for ordered, unordered and definition lists
	 */
	private function handleListItem($node, $content) {
		switch($node->nodeName) {
			case 'li':
				if( $node->hasChildNodes() && in_array($node->childNodes->item(0)->nodeName, array('ul', 'ol')) ) {
					// nested lists like
					// *** foo
					// *** bar
					return $content . "\n";
				} else {
					return $this->listBullets . ' ' . ltrim($content) . "\n";
				}

			case 'dt':
				return substr($this->listBullets, 0, -1) . ";{$node->textContent}\n";

			case 'dd':
				// hack for :::::foo markup used for indentation
				// <dl><dl>...</dl></dl> (produced by MW markup) would generate wikimarkup like the one below:
				// :
				// ::
				// ::: ...
				if($node->hasChildNodes() && $node->childNodes->item(0)->nodeName == 'dl') {
					return rtrim($content, ' ') . "\n";
				} else {
					return $this->listBullets . $content . "\n";
				}
		}
	}

	/**
	 * Returns level of indentation from value of margin-left CSS property
	 */
	private function getIndentationLevel($node) {
		if(!$node->hasAttributes()) {
			return false;
		}

		$cssStyle = $node->getAttribute('style');

		if(!empty($cssStyle)) {
			$margin = (substr($cssStyle, 0, 11) == 'margin-left') ? intval(substr($cssStyle, 12)) : 0;
			return intval($margin/40);
		}

		return false;
	}

	/**
	 * Clean up node text content
	 */
	private function cleanupTextContent($text) {
		wfProfileIn(__METHOD__);

		if($text == '') {
			wfProfileOut(__METHOD__);
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
		if(in_array($text{0}, array(':', ';'))) {
			$text = '<nowiki>' . $text{0} . '</nowiki>' . substr($text, 1);
		}

		// 5. wrap magic words {{ }} using <nowiki>
		$text = preg_replace("/({{2,3})([^}]+)(}{2,3})/", '<nowiki>$1$2$3</nowiki>', $text);

		// 6. wrap [[foo]] using <nowiki>
		$text = preg_replace("/(\[+)([^\]]+)(\]+)/", '<nowiki>$1$2$3</nowiki>', $text);

		wfProfileOut(__METHOD__);
		return $text;
	}

	/**
	 * Returns HTML string containing node arguments
	 */
	 private function getAttributesStr($node) {
		if(!$node->hasAttributes()) {
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
	 * Return true if given node is inline HTNL element
	 */
	private function isInlineElement($node) {
		return in_array($node->nodeName, array('u', 'b', 'strong', 'i', 'em'));
	}

}
