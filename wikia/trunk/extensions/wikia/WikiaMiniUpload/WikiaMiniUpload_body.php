<?php
/*
 * @author Inez Korczyński
 */

class WikiaMiniUpload {

	function loadMain() {
		$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');
		$tmpl->set_vars(array('result' => $this->recentlyUploaded()));
		return $tmpl->execute("main");
	}

	function recentlyUploaded() {
		global $IP, $wmu;
		require_once($IP . '/includes/SpecialPage.php');
		require_once($IP . '/includes/specials/SpecialNewimages.php');
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

		if($sourceId == 0) {
			$file = wfFindFile(Title::newFromText($itemId, 6));
			$props = array();
			$props['file'] = $file;
			$props['mwname'] = $itemId;
		} else if($sourceId == 1) {
			require_once($IP.'/extensions/3rdparty/ImportFreeImages/phpFlickr-2.2.0/phpFlickr.php');
			$flickrAPI = new phpFlickr('bac0bd138f5d0819982149f67c0ca734');
			$flickrResult = $flickrAPI->photos_getInfo($itemId);
			$url = "http://farm{$flickrResult['farm']}.static.flickr.com/{$flickrResult['server']}/{$flickrResult['id']}_{$flickrResult['secret']}.jpg";
			$data = array('wpUpload' => 1, 'wpSourceType' => 'web', 'wpUploadFileURL' => $url);
			$form = new UploadForm(new FauxRequest($data, true));
			$tempname = 'Temp_file_'.$wgUser->getID().'_'.rand(0, 1000);
			$file = new FakeLocalFile(Title::newFromText($tempname, 6), RepoGroup::singleton()->getLocalRepo());
			$file->upload($form->mTempPath, '', '');
			$props = array();
			$props['file'] = $file;
			$props['name'] = preg_replace("/[^".Title::legalChars()."]|:/", '-', trim($flickrResult['title']).'.jpg');
			$props['mwname'] = $tempname;
			$props['extraId'] = $itemId;
		}
		return $this->detailsPage($props);
	}

	function uploadImage() {
		global $wgRequest, $wgUser;
		$tempname = 'Temp_file_'.$wgUser->getID().'_'.rand(0, 1000);
		$file = new FakeLocalFile(Title::newFromText($tempname, 6), RepoGroup::singleton()->getLocalRepo());
		$file->upload($wgRequest->getFileTempName('wpUploadFile'), '', '');
		$props = array();
		$props['file'] = $file;
		$props['name'] = $wgRequest->getFileName('wpUploadFile');
		$props['mwname'] = $tempname;
		$props['upload'] = true;
		return $this->detailsPage($props);
	}

	function detailsPage($props) {
		$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');
		$tmpl->set_vars(array('props' => $props));
		return $tmpl->execute('details');
	}

	function insertImage() {
		global $wgRequest, $wgUser, $wgContLang, $IP;
		$type = $wgRequest->getVal('type');
		$name = $wgRequest->getVal('name');
		$mwname = $wgRequest->getVal('mwname');
		$extraId = $wgRequest->getVal('extraId');

		if($name !== NULL) {
			if($name == '') {
				header('X-screen-type: error');
				return 'You need to specify file name first!';
			} else {
				$name = preg_replace("/[^".Title::legalChars()."]|:/", '-', $name);
				$title = Title::makeTitleSafe(NS_IMAGE, $name);
				if(is_null($title)) {
					header('X-screen-type: error');
					return 'Specified file name is incorrect!';
				}
				if($title->exists()) {
					if($type == 'overwrite') {
						$title = Title::newFromText($name, 6);
						$file_name = new LocalFile($title, RepoGroup::singleton()->getLocalRepo());
						$file_mwname = new FakeLocalFile(Title::newFromText($mwname, 6), RepoGroup::singleton()->getLocalRepo());

						if(!empty($extraId)) {
							require_once($IP.'/extensions/3rdparty/ImportFreeImages/phpFlickr-2.2.0/phpFlickr.php');
							$flickrAPI = new phpFlickr('bac0bd138f5d0819982149f67c0ca734');
							$flickrResult = $flickrAPI->photos_getInfo($extraId);

							$nsid = $flickrResult['owner']['nsid']; // e.g. 49127042@N00
							$username = $flickrResult['owner']['username']; // e.g. bossa67
							$license = $flickrResult['license'];

							$caption = '{{MediaWiki:Flickr'.intval($license).'|1='.wfEscapeWikiText($extraId).'|2='.wfEscapeWikiText($nsid).'|3='.wfEscapeWikiText($username).'}}';
						} else {
							$caption = '';
						}

						$file_name->upload($file_mwname->getPath(), '', $caption);
						$file_mwname->delete('');
					} else if($type == 'existing') {
						header('X-screen-type: existing');
						$file = wfFindFile(Title::newFromText($name, 6));
						$props = array();
						$props['file'] = $file;
						$props['mwname'] = $name;
						return $this->detailsPage($props);
					} else {
						header('X-screen-type: conflict');
						$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');
						$tmpl->set_vars(array('name' => $name, 'mwname' => $mwname, 'extraId' => $extraId));
						return $tmpl->execute('conflict');
					}
				} else {
					$temp_file = new LocalFile(Title::newFromText($mwname, 6), RepoGroup::singleton()->getLocalRepo());
					$file = new LocalFile($title, RepoGroup::singleton()->getLocalRepo());

					if(!empty($extraId)) {
						require_once($IP.'/extensions/3rdparty/ImportFreeImages/phpFlickr-2.2.0/phpFlickr.php');
						$flickrAPI = new phpFlickr('bac0bd138f5d0819982149f67c0ca734');
						$flickrResult = $flickrAPI->photos_getInfo($extraId);

						$nsid = $flickrResult['owner']['nsid']; // e.g. 49127042@N00
						$username = $flickrResult['owner']['username']; // e.g. bossa67
						$license = $flickrResult['license'];

						$caption = '{{MediaWiki:Flickr'.intval($license).'|1='.wfEscapeWikiText($extraId).'|2='.wfEscapeWikiText($nsid).'|3='.wfEscapeWikiText($username).'}}';
					} else {
						$caption = $wgRequest->getVal('CC_license') == 'true' ? "== Licensing ==\n{{cc-by-sa-3.0}}" : '';
					}

					$file->upload($temp_file->getPath(), '', $caption);
					$temp_file->delete('');
				}
				$wgUser->addWatch($title);
				$db =& wfGetDB(DB_MASTER);
				$db->close();
			}
		} else {
			$title = Title::newFromText($mwname, 6);
		}

		$file = wfFindFile($title);
		if (!is_object($file)) {
			header('X-screen-type: error');
			return 'File was not found!';
		}

		header('X-screen-type: summary');

		$size = $wgRequest->getVal('size');
		$width = $wgRequest->getVal('width');
		$layout = $wgRequest->getVal('layout');
		$caption = $wgRequest->getVal('caption');
		$slider = $wgRequest->getVal('slider');

		$ns_img = $wgContLang->getFormattedNsText( NS_IMAGE );

		$tag = '[[' . $ns_img . ':'.$title->getDBkey();
		if($size != 'full' && ($file->getMediaType() == 'BITMAP' || $file->getMediaType() == 'DRAWING')) {
			$tag .= '|thumb';
			if($layout != 'right') {
				$tag .= '|'.$layout;
			}
			if($slider == 'true') {
				$tag .= '|'.$width;
			}
		}
		if($caption != '') {
			if($size == 'full') {
				$tag .= '|frame';
			}
			$tag .= '|'.$caption.']]';
		} else {
			$tag .= ']]';
		}

		$tmpl = new EasyTemplate(dirname(__FILE__).'/templates/');
		$tmpl->set_vars(array('tag' => $tag));
		return $tmpl->execute('summary');
	}

	function clean() {
		global $wgRequest;
		$file = new FakeLocalFile(Title::newFromText($wgRequest->getVal('mwname'), 6), RepoGroup::singleton()->getLocalRepo());
		$file->delete('');
	}
}

class FakeLocalFile extends LocalFile {

	function recordUpload2( $oldver, $comment, $pageText, $props = false, $timestamp = false ) {
		global $wgUser;
		$dbw = $this->repo->getMasterDB();
		if ( !$props ) {
			$props = $this->repo->getFileProps( $this->getVirtualUrl() );
		}
		$this->setProps( $props );
		$this->purgeThumbnails();
		$this->saveToCache();
		return true;
	}

	function upgradeRow() {}

	function doDBInserts() {}
}
