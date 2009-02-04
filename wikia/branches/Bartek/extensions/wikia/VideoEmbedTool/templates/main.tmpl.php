<div id="VideoEmbedError"></div>
<?php
	$uploadmesg = wfMsgExt( 'vet-uploadtext', 'parse' );
	$uploadmesg = preg_replace( '/(<a[^>]+)/', '$1 target="_blank" ', $uploadmesg );

?>

<table cellspacing="0" style="width: 100%;" id="VideoEmbedInputTable">
	<tr id="VideoEmbedTitle">
		<td colspan="2">
			<div id="VideoEmbedTitle"><h1><?= wfMsg( 'vet-title' ) ?></h1></div>
		</td>
	</tr>
<?php
	global $wgStylePath, $wgUser, $wgScriptPath, $wgExtensionsPath;
?>
	<tr id="VideoEmbedSearch">
		<td colspan="2">
			<input onkeydown="VET_trySendQuery(event);" type="text" id="VideoQuery" />
                        <input onclick="VET_trySendQuery(event);" type="button" id="VideoQuerySearch" value="<?= wfMsg('vet-find-btn') ?>" />
		</td>		
		<td>
<a href="http://www.metacafe.com" class="image" title="Metacafe" target="_blank"><img alt="" src="<?= $wgExtensionsPath; ?>/wikia/VideoEmbedTool/images/metacafe.gif" height="33" width="120" border="0"></a>				
		</td>
	<tr id="VideoEmbedAdd">
		<th><h1><?= wfMsg('vet-upload') ?></h1></th>

		<td>
			<img src="<?= $wgStylePath; ?>/monaco/images/widget_loading.gif" id="VideoEmbedProgress2" style="visibility: hidden;"/>
			<?php
			if( !$wgUser->isAllowed( 'upload' ) ) {
				if( !$wgUser->isLoggedIn() ) {
					echo wfMsg( 'vet-notlogged' );
				} else {
					echo wfMsg( 'vet-notallowed' ); 
				}
			} else {
				if ($error) {
					?>
						<span id="VET_error_box"><?= $error ?></span>
						<?php
				}
				?>
					<form onsubmit="return AIM.submit(this, VET_uploadCallback)" action="<?= $wgScriptPath ?>/index.php?action=ajax&rs=VET&method=insertVideo" id="VideoEmbedForm" method="POST" enctype="multipart/form-data">
					<input id="VideoEmbedUrl" name="wpVideoEmbedUrl" type="text" size="32" />
					<input id="VideoEmbedUrlSubmit" type="submit" value="<?= wfMsg('vet-upload-btn') ?>" onclick="return VET_upload(event);" />
					</form>
					<?php
			}
?>
</td>
<td>
</td>
</tr>
</table>
<div id="VideoEmbedMainMesg"><?= wfMsg( 'vet-main-info' ) ?></div>
<br/>
<div id="VET_results_0">
	<?= $result ?>
</div>

<a href="http://www.5min.com" class="image" title="5min" target="_blank"><img alt="" src="<?= $wgExtensionsPath; ?>/wikia/VideoEmbedTool/images/5min.gif" height="47" width="120" border="0"></a>
<a href="http://gamevideos.1up.com/" class="image" title="Gamevideos"  target="_blank"><img alt="Gamevideos" src="<?= $wgExtensionsPath; ?>/wikia/VideoEmbedTool/images/gamevideos.jpg" height="51" width="120" border="0"></a>
<a href="http://www.myvideo.de" class="image" title="Myvideo" target="_blank"><img alt="" src="<?= $wgExtensionsPath; ?>/wikia/VideoEmbedTool/images/myvideo.jpg" height="35" width="120" border="0"></a>
<a href="http://en.sevenload.com" class="image" title="Sevenload" target="_blank"><img alt="" src="<?= $wgExtensionsPath; ?>/wikia/VideoEmbedTool/images/sevenload.jpg" height="21" width="120" border="0"></a>
<a href="http://www.vimeo.com" class="image" title="Vimeo" target="_blank"><img alt="" src="<?= $wgExtensionsPath; ?>/wikia/VideoEmbedTool/images/vimeo.png" height="72" width="120" border="0"></a>
<a href="http://www.youtube.com" class="image" title="YouTube" target="_blank"><img alt="" src="<?= $wgExtensionsPath; ?>/wikia/VideoEmbedTool/images/youtube.jpg" height="85" width="120" border="0"></a>

<div id="VET_results_1" style="display: none;">
<br/><br/><br/><br/><br/>
	<div style="text-align: center;">
		<img src="<?= $wgStylePath ?>/../extensions/wikia/VideoEmbedTool/images/flickr_logo.gif" />
		<div class="VideoEmbedSourceNote"><?= wfMsg('vet-flickr-inf') ?></div>
	</div>
</div>
