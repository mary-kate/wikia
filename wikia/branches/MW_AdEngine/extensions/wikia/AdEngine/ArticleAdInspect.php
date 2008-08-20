<?php
$wgExtensionCredits['other'][] = array(
        'name' => 'ArticleAdInspect',
        'author' => 'Nick Sullivan'
);

/*
Think about:
Reporting on collisions via javascript

*/
class ArticleAdInspect {

	// Play with these levels
	const shortArticleThreshold=1000; 
	const longArticleThreshold=3500; 
	const collisionRankThreshold=.15; 
	const firstHtmlThreshold=1500; // Check this much of the html for collision causing tags
	const wideObjectThreshold=300; 

	public static function isShortArticle($html){
		return strlen(strip_tags($html)) < self::shortArticleThreshold;
	}

	public static function isLongArticle($html){
		return strlen(strip_tags($html)) > self::longArticleThreshold;
	}


	public static function hasWikiaMagicWord ($html, $word){
		return strpos($html, '<!-- __WIKIA_' . strtoupper($word) . '__ -->') !== false; 
	}

	/* Return the likelihood that there is a collision with the Box Ad
 	 * 1 - we are sure there is a collision.
 	 * 0 - we are sure there won't be.
 	 * num in between - we don't know, the higher the number the more likely
 	 *
 	 * Logic:
 	 * Check for a series of things known to cause collision. If found, increase score based
 	 * based on the likelihood of that item causing a collision, ala Mr. Bayes.
 	 */
	public static function getCollisionRank($html){
		$score=0;

		$firstHtml=substr($html, 0, self::firstHtmlThreshold);

		// Look for html tags that may cause collisions, and evaluate them
		if (preg_match_all('/<(table|img)[^>]+>/is', $firstHtml, $matches, PREG_OFFSET_CAPTURE)){

			// PHP's preg_match_all return is a PITA to deal with	
			for ($i=0; $i< sizeof($matches[0]); $i++){
				$wholetag=$matches[0][$i];
				$tag=$matches[1][$i][0];

				// Get attributes from tag.
				// Note, this requires well-formed html with quoted attributes.
				$pattern='/\s([a-zA-Z]+)\=[\x22\x27]([^\x22\x27]+)[\x22\x27]/';
				$attr=array();
				if (preg_match_all($pattern, $matches[0][$i][0], $attmatch)){
					for ($j=0; $j<sizeof($attmatch[1]); $j++){
						$attr[$attmatch[1][$j]]=$attmatch[2][$j];
					}
				}

				$score+=self::getTagCollisionScore($tag, $attr);
			}
		}

		if ($score > 1) $score=1;

		return $score;
	}

	
	/* Find out how naughty a particular tag is. */
	function getTagCollisionScore($tag, $attr){
		switch (strtolower($tag)){
		  case 'table':
			if (isset($attr['width']) && $attr['width'] >= self::wideObjectThreshold){
				return .5;
			} else {
				return .1;
			}
		    
		  case 'img':
			if (isset($attr['width']) && $attr['width'] >= self::wideObjectThreshold){
				return .5;
			} else {
				return .1;
			}

		  default : return 0;
		}
	}

	/* Based on our collision detection logic, figure out if we are displaying
	 * the leaderboard or the box ad. Return true for box ad, else false.
	 *
	 * Rules:
	 * 1) If magic word WIKIA_BANNER appears, return false
	 * 2) If magic word WIKIA_BOXAD appears, return true
	 * 3) If collisionRank is higher than collisionRankThreshold, return false
	 *
	 * Otherwise, return true.
	 */
	public static function isBoxAdArticle($html){
		if (self::hasWikiaMagicWord($html, "BANNER")){
			return false;
		} else if (self::hasWikiaMagicWord($html, "BOXAD")){
			return true;
		} else if (self::getCollisionRank($html) > self::collisionRankThreshold){
			return false;
		} else {
			return true;
		}
	}
  
}

