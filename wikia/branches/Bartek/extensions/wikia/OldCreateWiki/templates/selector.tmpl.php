<!-- s:<?= __FILE__ ?> -->
<div>
<form action="<?= $title->getFullUrl("action=create") ?>" method="post">
<fieldset>
    <legend>Choose request from list</legend>
    <select name="request" id="request">
    <? foreach ($requests as $rq): ?>
        <? if ($request == $rq->request_id) $default = "default=\"default\""; else $default = ""; ?>
        <option value="<?= $rq->request_id ?>" <?= $default ?>>
            <?= "Lang:{$rq->request_language}" ?>, <?= "Name:{$rq->request_name}" ?>, <?= "Title:{$rq->request_title}" ?>
        </option>
    <? endforeach ?>
    </select>
    <input type="submit" name="submit" value="Get this request" />
</fieldset>
</form>
<a href="<?php echo $title->getFullUrl("action=unlock") ?>">Remove create lock (beware! you'll lost your form data)</a>
</div>
<!-- e:<?= __FILE__ ?> -->
