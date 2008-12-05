<?php
class WysiwygParser extends Parser {

	/**
	 * Tag hook handler for 'pre'.
	 */
	function renderPreTag( $text, $attribs ) {
		// Backwards-compatibility hack
		$content = StringUtils::delimiterReplace( '<nowiki>', '</nowiki>', '$1', $text, 'i' );

		$attribs = Sanitizer::validateTagAttributes( $attribs, 'pre' );
		$attribs['wasHtml'] = 1;
		return wfOpenElement( 'pre', $attribs ) .
			Xml::escapeTagsOnly( $content ) .
			'</pre>';
	}

	function formatHeadings( $text, $isMain=true ) {
		return $text;
	}

	function doMagicLinks( $text ) {
		return $text;
	}

	# These next three functions open, continue, and close the list
	# element appropriate to the prefix character passed into them.
	#
	var $mListLevel = 0;
	var $mLast;
	var $bulletLevel = 0;

	/* private */ function openList( $char ) {
		$result = $this->closeParagraph();

		if ( ':' == $char) {
			if ( substr($this->mCurrentPrefix, -1) == ':' ) {
				$this->mLast = 'open';
				$this->mListLevel = strlen($this->mCurrentPrefix);
				$style = ' style="margin-left:'.($this->mListLevel*40).'px"';
				if ($this->mListLevel > 1) {
					$result = "</p><p{$style}>";
				}
				else {
					$result = "<p{$style}>";
				}
			}
			else {
				$result = '';
			}
		}
		else if ( '*' == $char ) {
			$indentLevel = strspn($this->mCurrentPrefix, ':');
			$style = ($indentLevel > 0 && $this->bulletLevel == 0) ? ' style="margin-left:'.($indentLevel*40).'px"' : '';
			$result .= "<ul{$style}><li>";
			$this->bulletLevel++;
		}
		else if ( '#' == $char ) { $result .= '<ol><li>'; }
		else if ( ';' == $char ) {
			$indentLevel = strspn($this->mCurrentPrefix, ':');
			$result .= '<p class="definitionTerm" style="margin-left: '.($indentLevel*40).'px">';
		}
		else { $result = '<!-- ERR 1 -->'; }

		return $result;
	}

/* private */ function nextItem( $char ) {
		if ( ':' == $char ) {
			$this->mListLevel = strlen($this->mCurrentPrefix);
			$style = ' style="margin-left:'.($this->mListLevel*40).'px"';
			if ($this->mLast == 'close') {
				$this->mLast = 'next';
				return "<p{$style}>";
			}
			else {
				$this->mLast = 'next';
				return "</p><p{$style}>";
			}
		}
		else if ( '*' == $char || '#' == $char ) { return '</li><li>'; }
		else if ( ';' == $char ) {
			$indentLevel = strspn($this->mCurrentPrefix, ':');
			return '</p><!-- next ; --><p class="definitionTerm" style="margin-left: '.($indentLevel*40).'px">';
		}
		return '<!-- ERR 2 -->';
	}

	/* private */ function closeList( $char ) {

		if ( ':' == $char ) {
			if ( $this->mLast != 'close' ) {
				$this->mLast = 'close';
				$text = '</p>';
			}
			else {
				$text ='';
			}
		}
		else if ( '*' == $char ) { $text = '</li></ul>'; $this->bulletLevel--; }
		else if ( '#' == $char ) { $text = '</li></ol>'; }
		else {	return '<!-- ERR 3 -->'; }
		return $text."\n";
	}
	/**#@-*/

	function __construct( $conf = array() ) {
		parent::__construct($conf);

		// load hooks from $wgParser
		global $wgParser;
		$this->mTagHooks = & $wgParser->mTagHooks;
		$this->mStripList = & $wgParser->mStripList;
	}

}
