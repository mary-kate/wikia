<?php

// use the same namespace as in old NY extension
define( 'NS_VIDEO', 400 );

// main video page class
class VideoPage extends Article {
	var	$id,
		$provider,
		$url,
		$video,
		$dataline;

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
		
		if ( $this->getID() ) {
			$wgOut->addHTML( $this->showTOC('') );
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
		$this->id	= substr( $idmatch[0], 4, -5 );
		$this->url	= substr( $urlmatch[0], 5, -6 );
		$this->provider	= substr( $prmatch[0], 10, -11 );
	}

	function revert() {


	}

	function videoHistory() {


	}

	function videoLinks() {


	}

        function getEmbedCode() {
                $embed = "";
                switch( $this->provider ) {
                        case "metacafe":
				$url = 'http://www.metacafe.com/fplayer/' . $this->id . '/' . $this->url;
                                $embed = "<embed src=\"{$this->url}\" width=\"400\" height=\"345\" wmode=\"transparent\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\"> </embed>";
                                break;
                        default: break;
                }
                return $embed;
        }

	function openShowVideo() {
		global $wgOut;
		$this->getContent();
		$this->parseDataline();	
		$wgOut->addHTML( $this->getEmbedCode() );
	}
}

class VideoHistoryList {





}


?>







