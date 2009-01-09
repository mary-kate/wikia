<?php

// main video page class
class VideoPage extends Article {
	var $video;

        function __construct (&$title){
                parent::__construct(&$title);
        }

        function render() {
                global $wgOut;
                $wgOut->setArticleBodyOnly( true );
                parent::view();
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

        function showTOC( $metadata ) {
                global $wgLang;
                $r = '<ul id="filetoc">
                        <li><a href="#file">' . $wgLang->getNsText( NS_IMAGE ) . '</a></li>
                        <li><a href="#filehistory">' . wfMsgHtml( 'filehist' ) . '</a></li>
                        <li><a href="#filelinks">' . wfMsgHtml( 'imagelinks' ) . '</a></li>' .
                        ($metadata ? ' <li><a href="#metadata">' . wfMsgHtml( 'metadata' ) . '</a></li>' : '') . '
                </ul>';
                return $r;
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







