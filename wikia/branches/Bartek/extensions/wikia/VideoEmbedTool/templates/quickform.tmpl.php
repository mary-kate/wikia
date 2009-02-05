<form name="quickaddform" method="post" action="<?=$action?>">
<table>
<tr><td width="120">
<?= wfMsg( 'qva-name' ); ?>
</td>
<td>
<input type="text" name="wpQuickVideoAddName" size="50" />
</td>
</tr>
<tr><td>
<?= wfMsg( 'qva-url' ); ?>
</td>
<td>
<input type="text" name="wpQuickVideoAddUrl" size="50" />
</td>
</tr>
<tr>
<td colspan="2">
<input type="submit" value="<?= wfMsg( 'qva-add' ); ?>" />
</td>
</tr>
</table>
</form>


