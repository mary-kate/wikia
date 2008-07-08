<!-- s:<?= __FILE__ ?> -->
<style type="text/css">
/*<![CDATA[*/
#rw-form label { display: block; width: 14em !important; float: left; padding-right: 1em; text-align: right;}
#rw-form input.text { width: 24em;}
#rw-form option {width: 20em; }
#rw-form textarea { width: 24em; height: 16em;}
#rw-form .inactive {color: #2F4F4F; padding: 0.2em; font-weight: bold;}
#rw-form .admin {background: #F0E68C;}
#rw-form div.row { padding: 0.8em; margin-bottom: 0.8em; display: block; clear: both; border-bottom: 1px solid #DCDCDC; }
#rw-form div.info, div.hint { text-align: center;}
#rw-form div.hint {font-style: italic; text-align: justify;margin-left: 22em;}
/*]]>*/
</style>
<div>
<form id="rw-form" action="<?= $title->getLocalUrl("action=comment") ?>" method="post" style="margin-left: auto; margin-right: auto;">
<fieldset>
 <legend><?= wfMsg("requestwiki") ?></legend>
 <div class="row">
    <label><?= wfMsg("requestwiki_founder") ?></label>
    <strong><?= $user->mName ?></strong>
    <input type="hidden" name="rw-userid" value="<?= $user->mId ?>" />
    <input type="hidden" name="rw-id" value="<?= $params["request_id"] ?>" />
    <input type="hidden" name="rw-name" value="<?= $params["request_name"] ?>" />
 </div>
 <? if (!empty($is_staff)): ?>
 <div class="row admin">
    <label><?= wfMsg("email") ?></label>
    <strong><?= $user->mEmail ?></strong>
 </div>
 <? endif ?>
 <div class="row">
    <label for="rw-name"><?= wfMsg("requestwiki_name") ?></label>
    <span class="inactive"><?= $params["request_name"] ?></span>.wikia.com
    <div class="hint">
        <?= wfMsg("requestwiki_namehint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_title") ?></label>
    <span class="inactive"><?= $params["request_title"] ?></span>
    <div class="hint">
        <?= wfMsg("requestwiki_titlehint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_language") ?></label>
    <span class="inactive"><?= $params["request_language"] ?> - <?= $languages[$params["request_language"]] ?></span>
    <div class="hint">
        <?= wfMsg("requestwiki_languagehint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_description") ?></label>
    <span class="inactive"><?= $params["request_description_international"] ?></span>
    <div class="hint">
        <?= wfMsg("requestwiki_descriptionhint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_descriptionenglish") ?></label>
    <span class="inactive"><?= $params["request_description_english"] ?></span>
    <div class="hint">
        <?= wfMsg("requestwiki_descriptionenglishhint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_community") ?></label>
    <span class="inactive"><?= $params["request_community"] ?></span>
    <div class="hint">
        <?= wfMsg("requestwiki_communityhint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_category") ?></label>
    <span class="inactive"><?= $params["request_category"] ?></span>
    <div class="hint">
        <?= wfMsg("requestwiki_categoryhint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_comments") ?></label>
    <textarea id="rw-comments" name="rw-comments" /><?= $params["request_comments"] ?></textarea>
    <div class="hint">
        <?= wfMsg("requestwiki_commentshint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_questions") ?></label>
    <textarea id="rw-questions" name="rw-questions" /><?= $params["request_questions"] ?></textarea>
    <div class="hint">
        <?= wfMsg("requestwiki_questionshint") ?>
    </div>
 </div>
 <div style="text-align: center;">
   <input type="submit" name="rw-submit" id="rw-submit" value="Save questions &amp; comments" />
 </div>
</fieldset>
<? if (!empty($is_staff)): ?>
<div class="row admin" style="text-align:center;font-style: italic;">Fields on backgound like this are visible only by staff</div>
<? endif ?>
</form>
</div>
<!-- e:<?= __FILE__ ?> -->
