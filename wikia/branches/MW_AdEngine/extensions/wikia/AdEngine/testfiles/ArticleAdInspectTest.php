<?php
require '../ArticleAdInspect.php';

require 'PHPUnit.php';
class CollisionTest extends PHPUnit_TestCase {
	function __construct($name) {
		$this->PHPUnit_TestCase($name);
	}

	function setUp() {
	}

	function testIsShort() {
		$this->assertFalse(ArticleAdInspect::isShortArticle(file_get_contents('./longArticleWithImagesNoCollision.html')));
		$this->assertFalse(ArticleAdInspect::isShortArticle(file_get_contents('./longArticleWithWideTable.html')));
		$this->assertFalse(ArticleAdInspect::isShortArticle(file_get_contents('./mediumArticlePlainText.html')));

		$this->assertTrue(ArticleAdInspect::isShortArticle(file_get_contents('./shortArticleWithImagesNoCollision.html')));
		$this->assertTrue(ArticleAdInspect::isShortArticle(file_get_contents('./shortArticle.html')));
	}

	function testIsLong() {
		$this->assertTrue(ArticleAdInspect::isLongArticle(file_get_contents('./longArticleWithImagesNoCollision.html')));
		$this->assertTrue(ArticleAdInspect::isLongArticle(file_get_contents('./longArticleWithWideTable.html')));

		$this->assertFalse(ArticleAdInspect::isLongArticle(file_get_contents('./mediumArticlePlainText.html')));
		$this->assertFalse(ArticleAdInspect::isLongArticle(file_get_contents('./shortArticleWithImagesNoCollision.html')));
		$this->assertFalse(ArticleAdInspect::isLongArticle(file_get_contents('./shortArticle.html')));
	}

	function testIsBoxAd(){
		$this->assertTrue(ArticleAdInspect::isBoxAdArticle(file_get_contents('./longArticleWithImagesNoCollision.html')));
		$this->assertTrue(ArticleAdInspect::isBoxAdArticle(file_get_contents('./mediumArticlePlainText.html')));
		$this->assertTrue(ArticleAdInspect::isBoxAdArticle(file_get_contents('./shortArticleWithImagesNoCollision.html')));
		$this->assertTrue(ArticleAdInspect::isBoxAdArticle(file_get_contents('./articleWithMagicWordBoxAd.html')));

		$this->assertFalse(ArticleAdInspect::isBoxAdArticle(file_get_contents('./articleWithMagicWordBanner.html')));
		$this->assertFalse(ArticleAdInspect::isBoxAdArticle(file_get_contents('./longArticleWithWideTable.html')));
	}
}
header('Content-Type: text/plain');
$suite = new PHPUnit_TestSuite();
$suite->addTest(new CollisionTest('testIsShort'));
$suite->addTest(new CollisionTest('testIsLong'));
$suite->addTest(new CollisionTest('testIsBoxAd'));
$result = PHPUnit::run($suite);
echo $result->toHTML();


