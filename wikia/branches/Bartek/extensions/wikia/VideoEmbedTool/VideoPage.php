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

		$this->openShowVideo();

		if ( $this->getID() ) {
			Article::view();
		} else {
			# Just need to set the right headers
			$wgOut->setArticleFlag( true );
			$wgOut->setRobotpolicy( 'noindex,nofollow' );
			$wgOut->setPageTitle( $this->mTitle->getPrefixedText() );
			$this->viewUpdates();
		}
	

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

	function openShowVideo() {


	}

}

class VideoHistoryList {





}


?>







