<?php

// use the same namespace as in old NY extension
define( 'NS_VIDEO', 400 );

// main video page class
class VideoPage extends Article {
	var	$id,
		$provider,
		$data,
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
                $r = '<ul id="filetoc">
                        <li><a href="#file">' . $wgLang->getNsText( NS_VIDEO ) . '</a></li>
                        <li><a href="#filehistory">' . wfMsgHtml( 'filehist' ) . '</a></li>
                        <li><a href="#filelinks">' . wfMsgHtml( 'imagelinks' ) . '</a></li>' .
                        ($metadata ? ' <li><a href="#metadata">' . wfMsgHtml( 'metadata' ) . '</a></li>' : '') . '
                </ul>';
                return $r;
        }

	function getContent() {
		return Article::getContent();
	}

	public function parseUrl( $url, $load = true ) {
		$provider = '';
		$id = '';
		$data = new array();

		$text = preg_match("/metacafe\.com/i", $url );
                if( $text ) { // metacafe
			$provider = "metacafe";                        	
			$standard_url = strpos( strtoupper( $url ), "HTTP://WWW.METACAFE.COM/WATCH/" );
			if (false !== $standard_url) {
				$id = substr( $url , $standard_url+ strlen("HTTP://WWW.METACAFE.COM/WATCH/") , strlen($url) );				
				// todo safeguard better
													

			}
                }
	

	}

	function loadFromPars( $provider, $id, $data ) {
		$this->provider = $provider;
		$this->id = $id;
		$this->data = $data;		
	}

	public function save() {



	}

	public function load() {
		$fname = get_class( $this ) . '::' . __FUNCTION__;
		$dbr = wfGetDB( DB_SLAVE );		
		$row = $dbr->selectRow(
			'image',
			'img_metadata',
			array( 'img_name' => $this->mTitle->getPrefixedText() ),
			$fname	
		);	
		if ($row) {
			$metadata = split( ",", $row->img_metadata ); 	
			if ( is_array( $metadata ) ) {
				$this->provider = $metadata[0];
				$this->id = $metadata[1];
				array_splice( $metadata, 0, 2 );
				if ( count( $metadata ) > 0 ) {
					foreach( $metadata as $data  ) {
						$this->data[] = $data;						
					}
				}
			}
		}
	}

	function revert() {


	}

	function videoHistory() {
		global $wgOut;
		$list = new VideoHistoryList( $this );

	}

	function videoLinks() {


	}

        public function getEmbedCode() {
                $embed = "";
                switch( $this->provider ) {
                        case "metacafe":
				$url = 'http://www.metacafe.com/fplayer/' . $this->id . '/' . $this->data[0];
                                $embed = "<embed src=\"{$url}\" width=\"400\" height=\"345\" wmode=\"transparent\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\"> </embed>";
                                break;
                        default: break;
                }
                return $embed;
        }

	function openShowVideo() {
		global $wgOut;
		$this->getContent();
		$this->load();	
		$wgOut->addHTML( $this->getEmbedCode() );
	}
}

class VideoHistoryList {





}


?>







