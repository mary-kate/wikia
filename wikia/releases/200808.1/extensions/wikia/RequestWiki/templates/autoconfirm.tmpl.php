<!-- s:<?= __FILE__ ?> -->
<div>
<? if ($is_logged): ?>
    <?= wfMsg('requestwiki-extra-autoconfirmed-info', array(1 => $confirm->getLocalUrl())) ?>
<? else: ?>
    <?= wfMsg('requestwiki-extra-login-info', array(1 => $login->getLocalUrl())) ?>
<? endif ?>
</div>
<!-- e:<?= __FILE__ ?> -->
