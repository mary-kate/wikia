<?php
global $wgExtensionsPath;
if(isset($props['name'])) {
?>
<div id="VideoEmbedSection">
	<?= wfMsg('vet-details-inf') ?>
	<table class="VideoEmbedOptionsTable" style="width: 100%;">
		<tr class="VideoEmbedNoBorder">
			<th><?= wfMsg('vet-name') ?></th>
			<td>
			<input id="VideoEmbedName" type="text" size="30" value="<?= $props['partname'] ?>" />
			<label for="VideoEmbedName">.<?= $props['extension'] ?></label>
			<input id="VideoEmbedExtension" type="hidden" value="<?= $props['extension'] ?>" />
			</td>
		</tr>
		<?php
			if(!empty($props['upload'])) {
		?>
		<tr class="VideoEmbedNoBorder VideoEmbedThin">
			<th><?= wfMsg('license') ?></th>
			<td>
			<span id="VideoEmbedLicenseSpan">
			<?php
				$licenses = new Licenses();
				$licensehtml = $licenses->getHtml();	
			?>
				<select name="VideoEmbedLicense" id="VideoEmbedLicense" onchange="VET_licenseSelectorCheck()" />
					<option><?= wfMsg( 'nolicense' ) ?></option>
					<?= $licensehtml ?>
				</select>
			</span>
			</td>
		</tr>		
		<tr class="VideoEmbedNoBorder VideoEmbedNoSpace">
			<th>&nbsp;</th>	
			<td>
				<div id="VideoEmbedLicenseControl"><a id="VideoEmbedLicenseLink" href="#" onclick="VET_toggleLicenseMesg(event);" >[<?= wfMsg( 'vet-hide-license-msg' ) ?>]</a></div>
			</td>
		</tr>
		<tr class="VideoEmbedNoBorder">
		<td colspan="2">
		<div id="VideoEmbedLicenseText">&nbsp;</div>			
		</td>
		</tr>
		<?php
			}
		?>

	</table>
</div>
<?php
}
?>
<?php
if($props['file']->media_type == 'BITMAP' || $props['file']->media_type == 'DRAWING') {
?>
<div style="position: absolute; z-index: 4; left: 0; width: 420px; height: 400px; background: #FFF; opacity: .9; filter: alpha(opacity=90);"></div>
<div id="VideoEmbedThumb" style="text-align: right; position: absolute; z-index: 3; right: 15px; height: <?= isset($props['name']) ? '255' : '370' ?>px;"><?= $props['file']->getThumbnail(min($props['file']->getWidth(), 400))->toHTML() ?></div>
<?php
}
echo '<div style="position: relative; z-index: 5;">';
echo wfMsg('vet-details-inf2')
?>
<table class="VideoEmbedOptionsTable">
<?php
if($props['file']->media_type == 'BITMAP' || $props['file']->media_type == 'DRAWING') {
?>
	<tr>
		<th><?= wfMsg('vet-size') ?></th>
		<td>
			<input onclick="MWU_imageSizeChanged('thumb');" type="radio" name="fullthumb" id="VideoEmbedThumbOption" checked=checked /> <label for="VideoEmbedThumbOption" onclick="MWU_imageSizeChanged('thumb');"><?= wfMsg('vet-thumbnail') ?></label>
			&nbsp;
			<input onclick="MWU_imageSizeChanged('full');" type="radio" name="fullthumb" id="VideoEmbedFullOption" /> <label for="VideoEmbedFullOption" onclick="MWU_imageSizeChanged('full');"><?= wfMsg('vet-fullsize', $props['file']->width, $props['file']->height) ?></label>
		</td>
	</tr>
	<tr id="ImageWidthRow">
		<th><?= wfMsg('vet-width') ?></th>
		<td>
			<input onclick="MWU_imageWidthChanged(VET_widthChanges++);" type="checkbox" id="VideoEmbedWidthCheckbox" />
			<div id="VideoEmbedSlider">
				<img src="<?= $wgExtensionsPath.'/wikia/VideoEmbedTool/images/slider_thumb_bg.png' ?>" id="VideoEmbedSliderThumb" />
			</div>
			<span id="VideoEmbedInputWidth">
				<input type="text" id="VideoEmbedManualWidth" name="VideoEmbedManualWidth" value="" onchange="VET_manualWidthInput(this)" onkeyup="VET_manualWidthInput(this)" /> px
			<span>
		</td>
	</tr>
	<tr id="ImageLayoutRow">
		<th><?= wfMsg('vet-layout') ?></th>
		<td>
			<input type="radio" id="VideoEmbedLayoutLeft" name="layout" />
			<label for="VideoEmbedLayoutLeft"><img src="<?= $wgExtensionsPath.'/wikia/VideoEmbedTool/images/image_upload_left.png' ?>" /></label>
			<input type="radio" id="VideoEmbedLayoutRight" name="layout" checked="checked" />
			<label for="VideoEmbedLayoutRight"><img src="<?= $wgExtensionsPath.'/wikia/VideoEmbedTool/images/image_upload_right.png' ?>" /></label>
		</td>
	</tr>
<?php
}
?>
	<tr>
		<th><?= wfMsg('vet-caption') ?></th>
		<td><input id="VideoEmbedCaption" type="text" /><?= wfMsg('vet-optional') ?></td>
	</tr>
	<tr class="VideoEmbedNoBorder">
		<td>&nbsp;</td>
		<td>
			<input type="submit" value="<?= wfMsg('vet-insert2') ?>" onclick="VET_insertImage(event, 'details');" />
		</td>
	</tr>
</table>
<input id="VideoEmbedExtraId" type="hidden" value="<?= isset($props['extraId']) ? urlencode($props['extraId']) : '' ?>" />
<input id="VideoEmbedMWname" type="hidden" value="<?= urlencode($props['mwname']) ?>" />
<input id="ImageRealWidth" type="hidden" value="<?= $props['file']->getWidth() ?>" />
<input id="ImageRealHeight" type="hidden" value="<?= $props['file']->getHeight() ?>" />
</div>
