<div id="VideoEmbedSuccess">
<?= wfMsg('vet-success') ?>
</div>
<div style="text-align: center;">
	<div id="VideoEmbedVisualTag" style="border-bottom: 1px solid #CCC; padding: 15px 0; margin: 15px;"><?= htmlspecialchars( $fake_tag ) ?></div>
	<div id="VideoEmbedPageSuccess" style="display:none;"><?= wfMsg( 'vet-page-success' ) ?></br><br/></div>
	<input onclick="VET_close(event);" type="button" value="<?= wfMsg( 'vet-return' ) ?>" />
	<div id="VideoEmbedCode" style="display: none;" ><?= $code ?></div>
	<input type="hidden" id="VideoEmbedTag" value="<?= htmlspecialchars( $tag ) ?>" />
</div>
