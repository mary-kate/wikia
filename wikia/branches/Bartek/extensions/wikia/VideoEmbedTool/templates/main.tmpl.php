<div id="VideoEmbedError"></div>
<?php
	$uploadmesg = wfMsgExt( 'vet-uploadtext', 'parse' );
	$uploadmesg = preg_replace( '/(<a[^>]+)/', '$1 target="_new" ', $uploadmesg );

?>

<table cellspacing="0" style="width: 100%;" id="VideoEmbedInputTable">
	<tr id="VideoEmbedTitle">
		<td colspan="2">
			<div id="VideoEmbedTitle"><h1><?= wfMsg( 'vet-title' ) ?></h1></div>
		</td>
	</tr>
	<tr id="VideoEmbedFind">
		<td colspan="2"><?= wfMsg('vet-find') ?></td>
	</tr>
	<tr id="VideoEmbedSearch">
		<td colspan="2">
<?php

global $wgStylePath, $wgUser, $wgScriptPath;

if( ( $wgUser->isLoggedIn() ) && ( $wgUser->isAllowed( 'upload' ) ) ) {
?>
			<div onclick="VET_changeSource(event);" style="font-size: 9pt; float: right; margin-top: 5px;">
				<a id="VET_source_0" href="#" style="font-weight: bold;"><?= wfMsg('vet-thiswiki') ?></a> |
				<a id="VET_source_1" href="#"><?= wfMsg('vet-flickr') ?></a>
			</div>
<?php
}
?>
			<textarea onkeydown="VET_trySendQuery(event);" type="text" id="VideoQuery"></textarea>
			<input onclick="VET_trySendQuery(event);" type="button" value="<?= wfMsg('vet-find-btn') ?>" />
			<img src="<?= $wgStylePath; ?>/monaco/images/widget_loading.gif" id="VideoEmbedProgress2" style="visibility: hidden;"/>
		</td>
	</tr>
	<tr id="VideoEmbedUpload">
		<td><h1><?= wfMsg('vet-upload') ?></h1></td>
		<td>
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
			<form onsubmit="return AIM.submit(this, VET_uploadCallback)" action="<?= $wgScriptPath ?>/index.php?action=ajax&rs=VET&method=uploadImage" id="VideoEmbedForm" method="POST" enctype="multipart/form-data">
				<input id="VideoEmbedFile" name="wpUploadFile" type="text" size="32" />
				<input type="submit" value="<?= wfMsg('vet-upload-btn') ?>" onclick="return VET_upload(event);" />
			</form>
	<?php
}
?>
		</td>
	</tr>

</table>

<div id="VET_results_0">
	<?= $result ?>
</div>

<div id="VET_results_1" style="display: none;">
	<br/><br/><br/><br/><br/>
	<div style="text-align: center;">
		<img src="<?= $wgStylePath ?>/../extensions/wikia/VideoEmbedTool/images/flickr_logo.gif" />
		<div class="VideoEmbedSourceNote"><?= wfMsg('vet-flickr-inf') ?></div>
	</div>
</div>
