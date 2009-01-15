<?php

// use the same namespace as in old NY extension
define( 'NS_VIDEO', 400 );

// main video page class
class VideoPage extends Article {
	var	$mName,
		$mId,
		$mProvider,
		$mData,
		$mDataline;

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
			$this->videoHistory();
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

		$text = preg_match("/metacafe\.com/i", $url );
		if( $text ) { // metacafe
			$provider = "metacafe";                        	
			// reuse some NY stuff for now
			$standard_url = strpos( strtoupper( $url ), "HTTP://WWW.METACAFE.COM/WATCH/" );
			if( false !== $standard_url ) {
				$id = substr( $url , $standard_url+ strlen("HTTP://WWW.METACAFE.COM/WATCH/") , strlen($url) );				
				$last_char = substr( $id,-1 ,1 );

				if($last_char == "/"){
					$id = substr( $id , 0 , strlen($id)-1 );
				}
				
				if ( !( false !== strpos( $id, ".SWF" ) ) ) {
					$id .= ".swf";
				}
				
				$data = split( "/", $id );
				if (is_array( $data ) ) {
					$this->mProvider = $provider;
					$this->mId = $data[0];
					$this->mData = array( $data[1] );
				}
			}
		}
	}

	function loadFromPars( $provider, $id, $data ) {
		$this->mProvider = $provider;
		$this->mId = $id;
		$this->mData = $data;		
	}

	public function setName( $name ) {
		$this->mName = $name;
	}

	public function save() {
		global $wgUser;

		$this->mTitle = Title::makeTitle( NS_VIDEO, $this->mName );
		$desc = "added video [[" . $this->mTitle->getPrefixedText() . "]]";			

                $dbw = wfGetDB( DB_MASTER );
                $now = $dbw->timestamp();
	
		switch( $this->mProvider ) {
			case 'metacafe':		
				$metadata = $this->mProvider . ',' . $this->mId . ',' . $this->mData[0];
				break;
			default: 
				$metadata = '';
				break;
		}

                $dbw->insert( 'image',
                        array(
                                'img_name' => $this->mTitle->getPrefixedText(),
                                'img_size' => 300,
                                'img_description' => '',
                                'img_user' => $wgUser->getID(),
                                'img_user_text' => $wgUser->getName(),
                                'img_timestamp' => $now,
				'img_metadata'	=> $metadata,										
                                'img_media_type' => 'VIDEO',
				'img_major_mime' => 'video',
				'img_minor_mime' => 'swf',					
                        ),
                        __METHOD__,
                        'IGNORE'
                );

                if( $dbw->affectedRows() == 0 ) {
			// we are updating
                        $desc = "updated video [[" . $this->mTitle->getPrefixedText() . "]]";
			                        $dbw->insertSelect( 'oldimage', 'image',
                                array(
                                        'oi_name' => 'img_name',
                                        'oi_archive_name' => 'img_name',
                                        'oi_size' => 'img_size',
                                        'oi_width' => 'img_width',
                                        'oi_height' => 'img_height',
                                        'oi_bits' => 'img_bits',
                                        'oi_timestamp' => 'img_timestamp',
                                        'oi_description' => 'img_description',
                                        'oi_user' => 'img_user',
                                        'oi_user_text' => 'img_user_text',
                                        'oi_metadata' => 'img_metadata',
                                        'oi_media_type' => 'img_media_type',
                                        'oi_major_mime' => 'img_major_mime',
                                        'oi_minor_mime' => 'img_minor_mime',
                                        'oi_sha1' => 'img_sha1'
                                ), array( 'img_name' => $this->mTitle->getPrefixedText() ), __METHOD__
                        );

		        // update the current image row
                        $dbw->update( 'image',
                                array( /* SET */
                                        'img_timestamp' => $now,
                                        'img_user' => $wgUser->getID(),
                                        'img_user_text' => $wgUser->getName(),
                                        'img_metadata' => $metadata,
                                ), array( /* WHERE */
                                        'img_name' => $this->mTitle->getPrefixedText()
                                ), __METHOD__
                        );
		}
		
		// todo make those categories more flexible
		$this->doEdit( "[[Category:Videos]]", $desc, EDIT_SUPPRESS_RC );			

		$dbw->immediateCommit();
		
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
				$this->mProvider = $metadata[0];
				$this->mId = $metadata[1];
				array_splice( $metadata, 0, 2 );
				if ( count( $metadata ) > 0 ) {
					foreach( $metadata as $data  ) {
						$this->mData[] = $data;						
					}
				}
			}
		}
	}

	function revert() {


	}

	function videoHistory() {
		global $wgOut;
		$dbr = wfGetDB( DB_SLAVE );
		$list = new VideoHistoryList( $this );
		$s = $list->beginVideoHistoryList();
		$s .= $list->videoHistoryLine( true );
		$s .= $list->videoHistoryLine();
		$s .= $list->endVideoHistoryList();
		$wgOut->addHTML( $s );
	}

	function videoLinks() {


	}

        public function getEmbedCode( $width = false, $height = false ) {
                $embed = "";
                switch( $this->mProvider ) {
                        case "metacafe":
				$url = 'http://www.metacafe.com/fplayer/' . $this->mId . '/' . $this->mData[0];
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
	var $mTitle;

        function __construct( $article ) {
		$this->mTitle = $article->mTitle;
        }

        public function beginVideoHistoryList() {
                global $wgOut, $wgUser;
                return Xml::element( 'h2', array( 'id' => 'filehistory' ), wfMsg( 'filehist' ) )
                        . $wgOut->parse( wfMsgNoTrans( 'filehist-help' ) )
                        . Xml::openElement( 'table', array( 'class' => 'filehistory' ) ) . "\n"
                        . '<tr>'
                        . '<th>' . wfMsgHtml( 'filehist-datetime' ) . '</th>'
                        . '<th>' . wfMsgHtml( 'filehist-user' ) . '</th>'
                        . "</tr>\n";
        }

	public function videoHistoryLine( $iscur = false ) {
		global $wgOut, $wgUser;
		
		$dbr = wfGetDB( DB_SLAVE );		

		if ( $iscur ) {
			// load from current db
			$history = $dbr->select( 'image',
					array(
						'img_metadata',
						'img_name',
						'img_user',
						'img_user_text',
						'img_timestamp',
						'img_description',
						"'' AS ov_archive_name"
					     ),
					array( 'img_name' => $this->mTitle->getPrefixedText() ),
					__METHOD__
					);
			if ( 0 == $dbr->numRows( $history ) ) {
				return '';
			} else {
				$s = '';				
				$row = $dbr->fetchObject( $history );
				return '<tr>' . '<td>' . $row->img_timestamp . '</td>' . '<td>' . $row->img_user_text .'</td></tr>';
			}			
		} else {
			// load from old video db
			$history = $dbr->select( 'oldimage',
					array(
						'oi_metadata AS img_metadata',
						'oi_name AS img_name',
						'oi_user AS img_user',
						'oi_user_text AS img_user_text',
						'oi_timestamp AS img_timestamp',
						'oi_description AS img_description',
					     ),
					array( 'oi_name' => $this->mTitle->getPrefixedText() ),
					__METHOD__,
					array( 'ORDER BY' => 'oi_timestamp DESC' )
					);
			$s = '';
			while( $row = $dbr->fetchObject( $history ) ) {
				$s .= '<tr>' . '<td>' . $row->img_timestamp . '</td>' . '<td>' . $row->img_user_text .'</td></tr>';	
			}			
			return $s;
		}
	}

        public function endVideoHistoryList() {
                return "</table>\n";
        }
}

?>







