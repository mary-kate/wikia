<?php


// main video page class
class VideoPage extends Article {
	var $video;

        function __construct (&$title){
                parent::__construct(&$title);
        }


	function view() {
		global $wgOut, $wgUser, $wgRequest;
		
		$this->video = new Video( $this->getTitle() );
		

	}

	function getContent() {


		return Article::getContent();
	}


	function revert() {


	}

	function videoHistory() {


	}

	function videoLinks() {


	}

}

class VideoHistoryList {





}


?>







