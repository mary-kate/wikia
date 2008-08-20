<?php

/**
 * Variant of QueryPage which formats the result as a simple link to the page
 *
 * @package MediaWiki
 * @addtogroup SpecialPage
 */
class PageQueryPage extends QueryPage {

	/**
	 * Format the result as a simple link to the page
	 *
	 * @param Skin $skin
	 * @param object $row Result row
	 * @return string
	 */
	public function formatResult( $skin, $row ) {
		global $wgContLang;
		$title = Title::makeTitleSafe( $row->namespace, $row->title );
		if (is_object($title)) {
			return $skin->makeKnownLinkObj( $title,
					htmlspecialchars( $wgContLang->convert( $title->getPrefixedText() ) ) );
		} else {
			return '' ;
		}
	}
}


