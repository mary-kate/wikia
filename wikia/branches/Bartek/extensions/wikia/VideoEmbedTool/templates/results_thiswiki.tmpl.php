<div id="VideoEmbedHeadline">
<div id="VideoEmbedPagination">
<?php
if($results['page'] > 1) {
?>
	<a onclick="VET_sendQuery('<?= $query ?>', <?= $results['page']-1 ?>, 0, 'prev'); return false;" href="#"><?= wfMsg('vet-prev') ?></a>
<?php
}
if($results['page'] > 1 && $results['page'] < $results['pages']) {
?>
	|
<?php
}
if($results['page'] < $results['pages']) {
?>
	<a onclick="VET_sendQuery('<?= $query ?>', <?= $results['page']+1 ?>, 0, 'next'); return false;" href="#"><?= wfMsg('vet-next') ?></a>
<?php
}
?>
</div>
<?= wfMsg('vet-thiswiki2', $results['total']) ?>
</div>

<table cellspacing="0" id="VideoEmbedFindTable">
	<tbody>
<?php
if(isset($results['images'])) {
	for($j = 0; $j < ceil(count($results['images']) / 4); $j++) {
?>
		<tr class="VideoEmbedFindImages">
<?php
		for($i = $j*4; $i < ($j+1)*4; $i++) {
			if(isset($results['images'][$i])) {
				$file = wfLocalFile(Title::newFromText($results['images'][$i]['title'], 6));
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
			if(isset($results['images'][$i])) {
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
