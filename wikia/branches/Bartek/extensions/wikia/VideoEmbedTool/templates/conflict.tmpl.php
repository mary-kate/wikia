<input id="VideoEmbedExtraId" type="hidden" value="<?= urlencode($extraId) ?>" />
<?php
$file_mwname = new FakeLocalFile(Title::newFromText($mwname, 6), RepoGroup::singleton()->getLocalRepo());
$file_name = new LocalFile(Title::newFromText($name, 6), RepoGroup::singleton()->getLocalRepo());
echo wfMsg('vet-conflict-inf', $file_name->getName());
?>
<table cellspacing="0" id="VideoEmbedConflictTable">
	<tr>
		<td style="border-right: 1px solid #CCC;">
			<h2><?= wfMsg('vet-rename') ?></h2>
			<div style="margin: 5px 0;">
				<input type="text" id="VideoEmbedRenameName" value="<?= $file_name->getName() ?>" />
				<input type="button" value="<?= wfMsg('vet-insert') ?>" onclick="VET_insertImage(event, 'rename');" />
			</div>
		</td>
		<td>
			<h2><?= wfMsg('vet-existing') ?></h2>
			<div style="margin: 5px 0;">
				<input type="button" value="<?= wfMsg('vet-insert') ?>" onclick="VET_insertImage(event, 'existing');" />
			</div>
		</td>
	</tr>
	<tr id="VideoEmbedCompare">
		<td style="border-right: 1px solid #CCC;">
			<?= $file_mwname->getThumbnail(265, 205)->toHtml() ?>
		</td>
		<td>
			<input type="hidden" id="VideoEmbedExistingName" value="<?= $file_name->getName() ?>" />
			<?= $file_name->getThumbnail(265, 205)->toHtml() ?>
		</td>
	</tr>
</table>
<div style="text-align: center;"><a onclick="VET_insertImage(event, 'overwrite');" href="#"><?= wfMsg('vet-overwrite') ?></a></div>
