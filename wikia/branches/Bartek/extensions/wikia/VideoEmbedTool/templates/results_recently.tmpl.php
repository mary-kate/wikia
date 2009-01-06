<div id="VideoEmbedHeadline">
<div id="VideoEmbedPagination">
<?php
if(isset($data['prev'])) {
?>
	<a onclick="VET_recentlyUploaded('from=<?= $data['prev'] ?>', 'prev'); return false;" href="#"><?= wfMsg('vet-prev') ?></a>
<?php
}
if(isset($data['prev']) && isset($data['next'])) {
?>
	|
<?php
}
if(isset($data['next'])) {
?>
	<a onclick="VET_recentlyUploaded('until=<?= $data['next'] ?>', 'next'); return false;" href="#"><?= wfMsg('vet-next') ?></a>
<?php
}
?>
</div>
<?= wfMsg('vet-recent-inf') ?>
</div>

<table cellspacing="0" id="VideoEmbedFindTable">
	<tbody>
<?php
if($data['gallery']) {
	for($j = 0; $j < ceil(count($data['gallery']->mImages) / 4); $j++) {
?>
		<tr class="VideoEmbedFindImages">
<?php
		for($i = $j*4; $i < ($j+1)*4; $i++) {
			if(isset($data['gallery']->mImages[$i])) {
				$file = wfLocalFile($data['gallery']->mImages[$i][0]);
				$results['images'][$i]['file'] = $file;
?>
				<td><a href="#" alt="<?= addslashes($file->getName()) ?>" title="<?= addslashes($file->getName()) ?>" onclick="VET_chooseImage(0, '<?= urlencode($file->getName()) ?>'); return false;"><?= $file->getThumbnail(120, 90)->toHtml() ?></a></td>
<?php
			}
		}
?>
		</tr>
		<tr class="VideoEmbedFindLinks">
<?php
		for($i = $j*4; $i < ($j+1)*4; $i++) {
			if(isset($data['gallery']->mImages[$i])) {
?>
				<td><a href="#" onclick="VET_chooseImage(0, '<?= urlencode($results['images'][$i]['file']->getName()) ?>'); return false;"><?= wfMsg('vet-insert3') ?></a></td>
<?php
			}
		}
?>
		</tr>
<?php
	}
}
?>
	</tbody>
</table>
