<form name="quickaddform" method="post" action="<?=$action?>">
<table>
<?php
if ('' == $name) {
?> 
<tr><td width="120">
<?= wfMsg( 'qva-name' ); ?>
</td>
<td>
<input type="text" id="wpQuickVideoAddName" name="wpQuickVideoAddName" size="50" />
</td>
</tr>
<?php
}
?>
<tr><td>
<?= wfMsg( 'qva-url' ); ?>
</td>
<td>
<input type="text" id="wpQuickVideoAddUrl" name="wpQuickVideoAddUrl" size="50" />
</td>
</tr>
<tr>
<td colspan="2">
<input type="submit" value="<?= wfMsg( 'qva-add' ); ?>" />
</td>
</tr>
</table>

<?php
if ('' != $name) {
?>
<input type="hidden" name="wpQuickVideoAddPrefilled" value="<?= $name ?>" id="wpQuickVideoAddPrefilled"  />
<?php
}
?>

</form>


