<?php
/* Call this file directly to run a set of tests against ArticleAdInspect.php */

require dirname(__FILE__) . '/../ArticleAdInspect.php';

require 'PHPUnit.php';
// Note that this is PEARs pattern of building TestCases, not necessarily ours.
// See PHPUnit.php for more info
class CollisionTest extends PHPUnit_TestCase {

	function __construct($name) {
		// New test cases are constructed here based on the argument.
		$this->PHPUnit_TestCase($name);
	}

	function setUp() {
		// Put common stuff here, will be called for each test case
	}

	function testIsShort() {
		// These should fail the short test. Note that medium should fail too, because it's not short nor long.
		$this->assertFalse(ArticleAdInspect::isShortArticle(file_get_contents('./longArticleWithImagesNoCollision.html')));
		$this->assertFalse(ArticleAdInspect::isShortArticle(file_get_contents('./longArticleWithWideTable.html')));
		$this->assertFalse(ArticleAdInspect::isShortArticle(file_get_contents('./mediumArticlePlainText.html')));

		// These are the true short articles
		$this->assertTrue(ArticleAdInspect::isShortArticle(file_get_contents('./shortArticleWithImagesNoCollision.html')));
		$this->assertTrue(ArticleAdInspect::isShortArticle(file_get_contents('./shortArticle.html')));
	}

	function testIsLong() {
		// These are the true long articles
		$this->assertTrue(ArticleAdInspect::isLongArticle(file_get_contents('./longArticleWithImagesNoCollision.html')));
		$this->assertTrue(ArticleAdInspect::isLongArticle(file_get_contents('./longArticleWithWideTable.html')));

		// These should fail the long test, including the medium, because it's not long enough
		$this->assertFalse(ArticleAdInspect::isLongArticle(file_get_contents('./mediumArticlePlainText.html')));
		$this->assertFalse(ArticleAdInspect::isLongArticle(file_get_contents('./shortArticleWithImagesNoCollision.html')));
		$this->assertFalse(ArticleAdInspect::isLongArticle(file_get_contents('./shortArticle.html')));
	}

	function testIsBoxAd(){
		// These should should display a box ad. note the names of the files for explanations on why. 
		// Additional info in the files themselves
		$this->assertTrue(ArticleAdInspect::isBoxAdArticle(file_get_contents('./longArticleWithImagesNoCollision.html')));
		$this->assertTrue(ArticleAdInspect::isBoxAdArticle(file_get_contents('./mediumArticlePlainText.html')));
		$this->assertTrue(ArticleAdInspect::isBoxAdArticle(file_get_contents('./shortArticleWithImagesNoCollision.html')));
		$this->assertTrue(ArticleAdInspect::isBoxAdArticle(file_get_contents('./articleWithMagicWordBoxAd.html')));

		// These should should display a banner ad
		$this->assertFalse(ArticleAdInspect::isBoxAdArticle(file_get_contents('./articleWithMagicWordBanner.html')));
		$this->assertFalse(ArticleAdInspect::isBoxAdArticle(file_get_contents('./longArticleWithWideTable.html')));
	}
}
// header('Content-Type: text/plain');
$suite = new PHPUnit_TestSuite();
$suite->addTest(new CollisionTest('testIsShort'));
$suite->addTest(new CollisionTest('testIsLong'));
$suite->addTest(new CollisionTest('testIsBoxAd'));
$result = PHPUnit::run($suite);
echo $result->toHTML();


