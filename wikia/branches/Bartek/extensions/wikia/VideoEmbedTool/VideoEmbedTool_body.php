<?php
/*
 * @author Inez Korczyński
 * @author Bartek Łapiński
 */

class VideoEmbedTool {

	function loadMain( $error = false ) {
		$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');
		$tmpl->set_vars(array(
				'result' => '',
				'error'  => $error
				)
		);
		return $tmpl->execute("main");
	}


	function recentlyUploaded() {
		global $IP, $wmu;
		require_once($IP . '/includes/SpecialPage.php');
		require_once($IP . '/includes/specials/SpecialNewimages.php');
		// this needs to be revritten, since we will not display recently uploaded, but embedded

		$isp = new IncludableSpecialPage('Newimages', '', 1, 'wfSpecialNewimages', $IP . '/includes/specials/SpecialNewimages.php');
		wfSpecialNewimages(8, $isp);
		$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');
		$tmpl->set_vars(array('data' => $wmu));
		return $tmpl->execute("results_recently");
	}

	function query() {
		global $wgRequest, $IP;

		$query = $wgRequest->getText('query');
		$page = $wgRequest->getVal('page');
		$sourceId = $wgRequest->getVal('sourceId');
		// to be rewritten too
		
		if($sourceId == 1) {
			require_once($IP.'/extensions/3rdparty/ImportFreeImages/phpFlickr-2.2.0/phpFlickr.php');
			$flickrAPI = new phpFlickr('bac0bd138f5d0819982149f67c0ca734');
			$flickrResult = $flickrAPI->photos_search(array('tags' => $query, 'tag_mode' => 'all', 'page' => $page, 'per_page' => 8, 'license' => '4,5', 'sort' => 'interestingness-desc'));
			$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');
			$tmpl->set_vars(array('results' => $flickrResult, 'query' => addslashes($query)));
			return $tmpl->execute('results_flickr');
		} else if($sourceId == 0) {
			$db =& wfGetDB(DB_SLAVE);
			$res = $db->query("SELECT count(*) as count FROM `page` WHERE lower(page_title) LIKE '%".strtolower($db->escapeLike($query))."%' AND page_namespace = 6 ORDER BY page_title ASC LIMIT 8");
			$row = $db->fetchRow($res);
			$results = array();
			$results['total'] = $row['count'];
			$results['pages'] = ceil($row['count']/8);
			$results['page'] = $page;
			$res = $db->query("SELECT page_title FROM `page` WHERE lower(page_title) LIKE '%".strtolower($db->escapeLike($query))."%' AND page_namespace = 6 ORDER BY page_title ASC LIMIT 8 OFFSET ".($page*8-8));
			while($row = $db->fetchObject($res)) {
				$results['images'][] = array('title' => $row->page_title);
			}
			$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');
			$tmpl->set_vars(array('results' => $results, 'query' => addslashes($query)));
			return $tmpl->execute('results_thiswiki');
		}
	}

	function chooseImage() {

		global $wgRequest, $wgUser, $IP;
		$itemId = $wgRequest->getVal('itemId');
		$sourceId = $wgRequest->getInt('sourceId');

		// todo this is unused now, since there is currently now search
		// to be applied later

		return $this->detailsPage($props);
	}

	function insertVideo() {
		global $IP, $wgRequest, $wgUser, $wgTitle;
		require_once( "$IP/extensions/wikia/WikiaVideo/VideoPage.php" );

		$ns = $wgTitle->getNamespace();

		$url = $wgRequest->getVal( 'wpVideoEmbedUrl' );			
		$tempname = 'Temp_video_'.$wgUser->getID().'_'.rand(0, 1000);
		$title = Title::makeTitle( NS_VIDEO, $tempname );
		$video = new VideoPage( $title );

		// todo some safeguard here to take care of bad urls
		if( !$video->parseUrl( $url ) ) {
			header('X-screen-type: error');
			return $this->loadMain( wfMsg( 'vet-bad-url' ) );
		}
	
		if( !$video->checkIfVideoExists() ) {
			header('X-screen-type: error');
			return $this->loadMain( wfMsg( 'vet-non-existing' ) );
		}	
	
		$props['provider'] = $video->getProvider();
		$props['id'] = $video->getVideoId();
		$props['vname'] = $video->getVideoName();
		$data = $video->getData();
		if (is_array( $data ) ) {
			$props['metadata'] = implode( ",", $video->getData() );
		} else {
			$props['metadata'] = '';		
		}
		$props['code'] = $video->getEmbedCode( VIDEO_PREVIEW );

		if ( ( NS_VIDEO == $ns ) && (!$video->getID() )) {
			$props['oname'] = $wgTitle->getText();
		} else {
			$props['oname'] = 'Strobos';			
		}
		return $this->detailsPage($props);
	}

	function getVideoFromName() {
                global $wgRequest, $wgUser, $wgContLang, $IP;
                require_once( "$IP/extensions/wikia/WikiaVideo/VideoPage.php" );

                $name = $wgRequest->getVal('name');		
		$title = Title::makeTitle( NS_VIDEO, $name );
		$video = new VideoPage( $title );
		$video->load();
		return $video->getEmbedCode();
	}

	function detailsPage($props) {
		$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');

		$tmpl->set_vars(array('props' => $props));	
		return $tmpl->execute('details');
	}

	function insertFinalVideo() {
		global $wgRequest, $wgUser, $wgContLang, $IP;
		require_once( "$IP/extensions/wikia/WikiaVideo/VideoPage.php" );

		$type = $wgRequest->getVal('type');
		$id = $wgRequest->getVal('id');
		$provider = $wgRequest->getVal('provider');
		$name = $wgRequest->getVal('name');
		$oname = $wgRequest->getVal('oname');
		if ('' == $name) {
			$name = $oname;
		}

		$title = Title::makeTitle( NS_VIDEO, $name );
					
		$extra = 0;
		$metadata = array();
		while( '' != $wgRequest->getVal( 'metadata' . $extra ) ) {
			$metadata[] = $wgRequest->getVal( 'metadata' . $extra );
			$extra++;
		}

		if($name !== NULL) {
			if($name == '') {
				header('X-screen-type: error');
				// todo messagize
				return 'You need to specify file name first!';
			} else {

				$title = Title::makeTitleSafe(NS_VIDEO, $name);
				if(is_null($title)) {
					header('X-screen-type: error');
					return wfMsg ( 'wmu-filetype-incorrect' ); 
				}
				if($title->exists()) {
					if($type == 'overwrite') {
						// is the target protected?
						$permErrors = $title->getUserPermissionsErrors( 'edit', $wgUser );
						$permErrorsUpload = $title->getUserPermissionsErrors( 'upload', $wgUser );
						$permErrorsCreate = ( $title->exists() ? array() : $title->getUserPermissionsErrors( 'create', $wgUser ) );

						if( $permErrors || $permErrorsUpload || $permErrorsCreate ) {
							header('X-screen-type: error');
							// todo messagize
							return 'This image is protected';
						}

						$video = new VideoPage( $title );
						if ($video instanceof VideoPage) {
							$video->loadFromPars( $provider, $id, $metadata );					
							$video->setName( $name );
							$video->save();					
						}
					} else if($type == 'existing') {
						header('X-screen-type: existing');
						$title = Title::makeTitle( NS_VIDEO, $name );						
						$video = new VideoPage( $title );
						
						$props = array();
						$video->load();
						$props['provider'] = $video->getProvider();
						$props['id'] = $video->getVideoId();
						$data = $video->getData();
						if (is_array( $data ) ) {
							$props['metadata'] = implode( ",", $video->getData() );
						} else {
							$props['metadata'] = '';
						}
						$props['code'] = $video->getEmbedCode( VIDEO_PREVIEW );
						$props['oname'] = $name;

						return $this->detailsPage($props);
					} else {
						if ('' == $oname) {
							header('X-screen-type: conflict');
							$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');
							$tmpl->set_vars( array(
										'name' => $name,
										'id' => $id,
										'provider' => $provider,
										'metadata' => $metadata,	
									      )
								       );
							return $tmpl->execute('conflict');
						}
					}
				} else {
					// is the target protected?
					$permErrors = $title->getUserPermissionsErrors( 'edit', $wgUser );
					$permErrorsUpload = $title->getUserPermissionsErrors( 'upload', $wgUser );
					$permErrorsCreate = ( $title->exists() ? array() : $title->getUserPermissionsErrors( 'create', $wgUser ) );

					if( $permErrors || $permErrorsUpload || $permErrorsCreate ) {
						header('X-screen-type: error');
						// todo messagize
						return 'This video is protected';
					}

					$video = new VideoPage( $title );
					if ($video instanceof VideoPage) {
						$video->loadFromPars( $provider, $id, $metadata );
						$video->setName( $name );
						$video->save();					
					}
				}
			}
		} else {
			$title = Title::newFromText($mwname, 6);
		}


		header('X-screen-type: summary');

		$size = $wgRequest->getVal('size');
		$width = $wgRequest->getVal('width');
		$layout = $wgRequest->getVal('layout');
		$caption = $wgRequest->getVal('caption');
		$slider = $wgRequest->getVal('slider');

		$ns_img = $wgContLang->getFormattedNsText( NS_VIDEO );

		$tag = '[[' . $ns_img . ':'.$name;

		if($size != 'full') {
			$tag .= '|thumb';
		}

		$tag .= '|'.$width;
		$tag .= '|'.$layout;
		if($caption != '') {
			$tag .= '|'.$caption.']]';
		} else {
			$tag .= ']]';
		}

		$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');
		$tmpl->set_vars(array('tag' => $tag));
		return $tmpl->execute('summary');
	}
}

