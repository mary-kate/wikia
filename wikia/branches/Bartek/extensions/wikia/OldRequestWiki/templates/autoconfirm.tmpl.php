<!-- s:<?= __FILE__ ?> -->
<div>
<? if ($is_logged): ?>
    <?= wfMsg("requestwiki_autoconfirmedinfo", array(1 => $confirm->getLocalUrl())) ?>
<? else: ?>
    <?= wfMsg("requestwiki_logininfo", array(1 => $login->getLocalUrl())) ?>
<? endif ?>
</div>
<!-- e:<?= __FILE__ ?> -->
