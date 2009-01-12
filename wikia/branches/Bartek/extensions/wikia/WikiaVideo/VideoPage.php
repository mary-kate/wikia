<?php

// use the same namespace as in old NY extension
define( 'NS_VIDEO', 400 );

// main video page class
class VideoPage extends Article {
	var $video, $dataline;

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

		if ( $this->getID() ) {
			$this->openShowVideo();
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
                $r = '<ul id="videotoc">
                        <li><a href="#file">' . $wgLang->getNsText( NS_VIDEO ) . '</a></li>
                        <li><a href="#filehistory">' . wfMsgHtml( 'filehist' ) . '</a></li>
                        <li><a href="#filelinks">' . wfMsgHtml( 'imagelinks' ) . '</a></li>' .
                        ($metadata ? ' <li><a href="#metadata">' . wfMsgHtml( 'metadata' ) . '</a></li>' : '') . '
                </ul>';
                return $r;
        }

	function getContent() {
		$content = Article::getContent();
		if (!$this->dataline) {
			$this->dataline = preg_match("/^[^\n]+/", $content, $matches);
			if( is_array( $matches ) ) {
				$this->dataline = $matches[0];
			} 
		}

		$content = preg_replace( "/^[^\n]+/", "", $content ) ;	
		return $content;	
	}

	function parseDataline() {
		$id = preg_match( "/<id>.+<\/id>/", $this->dataline, $idmatch );
		$url = preg_match( "/<url>.+<\/url>/", $this->dataline, $urlmatch );
		$provider = preg_match( "/<provider>.+<\/provider>/", $this->dataline, $prmatch );
		return array(
				'id'		=> substr( $idmatch[0], 4, -5 ),
				'url'		=> substr( $urlmatch[0], 5, -6 ),
				'provider'	=> substr( $prmatch[0], 10, -11 )
		);
	}

	function revert() {


	}

	function videoHistory() {


	}

	function videoLinks() {


	}

	function openShowVideo() {
		global $wgOut;
		$this->getContent();
		$this->video->loadFromPage( $this->parseDataline() );	
		$wgOut->addHTML( $this->video->getEmbedCode() );
	}

}

class VideoHistoryList {





}


?>







