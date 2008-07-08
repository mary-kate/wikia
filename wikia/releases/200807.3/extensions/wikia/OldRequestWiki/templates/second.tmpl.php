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
#rw-form div.hint {font-style: italic; text-align: left; margin-left: 22em;}
#create-tagcloud {border: 1px solid #DCDCDC; margin: 0.5em; padding: 0.5em; text-align: center; font-size: medium;}
#rw-submit {padding: 1em; font-size: larger; background: #DCDCDC; }
/*]]>*/
</style>
<script type="text/javascript">
/*<![CDATA[*/
YAHOO.namespace("WRequest");

var YC = YAHOO.util.Connect;
var YD = YAHOO.util.Dom;
var YE = YAHOO.util.Event;
var WR = YAHOO.WRequest;
var ajaxpath = "<?php echo $GLOBALS["wgScriptPath"]."/index.php" ?>";

YAHOO.WRequest.NameCallback = {
    success: function( oResponse ) {
        var divData = YAHOO.Tools.JSONParse(oResponse.responseText);
        var div = YD.get( divData["div-name"] );
        var error = divData["is-error"];
        if (error == 1) {
            YD.get( "rw-submit" ).disabled = true;
        }
        else {
            YD.get( "rw-submit" ).disabled = false;
            if (YD.get( "rw-title" ).value == "") {
                var _tmp = YD.get( "rw-name" ).value + " Wiki";
                YD.get( "rw-title" ).value = _tmp.substring(0, 1).toUpperCase() + _tmp.substring(1, _tmp.length);
                YD.get( "rw-submit" ).disabled = false;
            }
        }
        div.innerHTML = divData["div-body"];
        // unlock for editing
        YD.get( "rw-title" ).disabled = false;
        YD.get( "rw-description-international" ).disabled = false;
        // WF.Busy(0);
    },
    failure: function( oResponse ) {
        YAHOO.log( "simple replace failure " + oResponse.responseText );
        // WF.Busy(0);
    },
    timeout: 50000
};
YAHOO.WRequest.watchName = function (e) {
    YD.get("rw-name-check").innerHTML = '<img src="http://images.wikia.com/common/progress_bar.gif" width="70" height="11" alt="Wait..." border="0" />';
    var name = YD.get("rw-name").value;
    var lang = YD.get("rw-language").value;

    // to lowercase
    name = name.toLowerCase();
    YD.get("rw-name").value = name;

    // prevent from editing
    YD.get( "rw-title" ).disabled = true;
    YD.get( "rw-description-international" ).disabled = true;

    YC.asyncRequest( "GET", ajaxpath+"?action=ajax&rs=axWRequestCheckName&name="+name+"&lang="+lang+"&edit=<?= $request_id ?>", YAHOO.WRequest.NameCallback);
};

YAHOO.WRequest.watchLanguage = function (e) {
    if ( YD.get("rw-language").value != 'en' ) {
        YD.get( "rw-description-english" ).disabled = false;
        YD.setStyle( "rw-div-descen", "display", "block" );
        YD.setStyle( "rw-description-english", "background", "inherit" );
    }
    else {
        // YD.get( "rw-description-english" ).disabled = true;
        // YD.setStyle( "rw-description-english", "background", "#DCDCDC" );
        YD.setStyle( "rw-div-descen", "display", "none" );
    }
    YD.get("rw-name-check").innerHTML = '<img src="http://images.wikia.com/common/progress_bar.gif" width="70" height="11" alt="Wait..." border="0" />';
    var name = YD.get("rw-name").value;
    var lang = YD.get("rw-language").value;
    YC.asyncRequest( "GET", ajaxpath+"?action=ajax&rs=axWRequestCheckName&name="+name+"&lang="+lang+"&edit=<?= $request_id ?>", YAHOO.WRequest.NameCallback);
};

YAHOO.WRequest.insertCategory = function (e, tag) {
    YE.preventDefault(e);
    // more sofisticated category cloud
    _tmp = YAHOO.Tools.trim( YD.get( "rw-category" ).value );
    if ( _tmp == "" ) {
        _tmp = tag;
    }
    else {
        // count how many categories we have already
        var cats = new Array();
        cats = _tmp.split(",");
        if (cats.length < 5) {
            _tmp = _tmp + ", " + tag;
        }
        else if (cats.length == 5) {
            // ok, maybe we have four but last part is empty (coma at end)?
            if (cats[4] == "") {
                _tmp = _tmp + " " + tag;
            }
        }
    }
    YD.get( "rw-category" ).value = _tmp;
}

// check all form, unlock fileds which are locked
YAHOO.WRequest.watchForm = function (e) {
};

// init all fields
YAHOO.WRequest.init = function () {
};

YAHOO.WRequest.Uppercase = function( e, field ) {
    var _tmp = YD.get( field ).value;
    YD.get( field ).value = _tmp.substring(0, 1).toUpperCase() + _tmp.substring(1, _tmp.length);
    YD.get( field ).disabled = false;
}

YE.addListener("rw-name", "change", WR.watchName );
YE.addListener("rw-language", "change", WR.watchLanguage );
YE.addListener("rw-title", "change", WR.Uppercase, "rw-title" );
YE.addListener("rw-submit", "submit", WR.watchForm );

/*]]>*/
</script>
<?php if (!empty($is_staff)): ?>
<div>
    [<a href="<?= $title->getLocalUrl("action=list") ?>">list of requests</a>]
<?php
    if (!empty($editing)):
        foreach($links as $action => $link):
?>
    [<a href="<?= $link ?>"><?= $action ?></a>]
<?php
        endforeach;
    endif;
?>
</div>
<? endif ?>
<div>
<form id="rw-form" action="<?= $title->getLocalUrl("action=third") ?>" method="post" style="margin-left: auto; margin-right: auto;">
<fieldset>
 <legend><?= wfMsg("requestwiki") ?></legend>
 <div class="row">
    <label><?= wfMsg("requestwiki_founder") ?></label>
<?php if (!empty($is_staff)): ?>
    <input maxlength="255" type="text" name="rw-username" id="rw-username" value="<?= $user->mName ?>" />
    <div class="info">
        <?= (!empty($errors["rw-username"])) ? $errors["rw-username"] : "&nbsp;" ?>
    </div>
    <div class="hint">
        <?= wfMsg("requestwiki_usernamehint") ?>
    </div>
<?php else: ?>
    <strong><?= $user->mName ?></strong>
    <input type="hidden" name="rw-username" id="rw-username" value="<?= $user->mName ?>" readonly="readonly" />
<?php endif ?>
    <input type="hidden" name="rw-userid" value="<?= $user->mId ?>" />
    <?php if($editing==1): ?>
    <input type="hidden" name="editing" value="1" />
    <input type="hidden" name="rw-id" value="<?= $request_id ?>" />
    <?php else: ?>
    <input type="hidden" name="editing" value="0" />
    <?php endif ?>
 </div>
 <?php if (!empty($is_staff)): ?>
 <div class="row admin">
    <label><?= wfMsg("email") ?></label>
    <strong><?= $user->mEmail ?></strong>
 </div>
 <?php endif ?>
 <div class="row">
    <label for="rw-name"><?= wfMsg("requestwiki_name") ?></label>
    <?php if ($is_staff || empty($editing)): ?>
    <input maxlength="255" type="text" name="rw-name" id="rw-name" value="<?= $params["request_name"] ?>" />.wikia.com
    <?php else: ?>
    <span class="inactive"><?= $params["request_name"] ?></span>.wikia.com
    <input type="hidden" name="rw-name" id="rw-name" value="<?= $params["request_name"] ?>" />
    <?php endif ?>
    <div class="info" id="rw-name-check">
        <?= (!empty($errors["rw-name"])) ? $errors["rw-name"] : "&nbsp;" ?>
    </div>
    <div class="hint">
        <?= wfMsg("requestwiki_namehint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_title") ?></label>
    <input maxlength="255" type="text" id="rw-title" name="rw-title" value="<?= $params["request_title"] ?>" />
    <div class="info">
        <?= (!empty($errors["rw-title"])) ? $errors["rw-title"] : "&nbsp;" ?>
    </div>
    <div class="hint">
        <?= wfMsg("requestwiki_titlehint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_language") ?></label>
    <select id="rw-language" name="rw-language">
<?php
    foreach ($languages as $key => $language) {
        $selected = "";
        if (!empty($params["request_language"])) {
            if ( $key===$params["request_language"] ) {
                $selected = "selected=\"selected\"";
            }
        }
        else {
            if ($key === 'en') {
                $selected = "selected=\"selected\"";
            }
        }
        echo "\t<option value=\"{$key}\" {$selected}>{$key} - {$language}</option>\n";
    }
?>
    </select>
    <div class="hint">
        <?= wfMsg("requestwiki_languagehint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_description") ?></label>
    <textarea id="rw-description-international" name="rw-description-international" /><?= $params["request_description_international"] ?></textarea>
    <div class="hint">
        <?= wfMsg("requestwiki_descriptionhint") ?>
    </div>
 </div>
 <?php $hidediv = ($params["request_language"] == "en" || empty($params["request_language"])) ? "display: none;": "display: block;"; ?>
 <div class="row" id="rw-div-descen" style="<?= $hidediv ?>">
    <label><?= wfMsg("requestwiki_descriptionenglish") ?></label>
    <?php $dscparams = ($params["request_language"] == "en" || empty($params["request_language"])) ? "class=\"inactive\" disabled=\"disabled\"": ""; ?>
    <textarea id="rw-description-english" name="rw-description-english" <?= $dscparams ?> /><?= $params["request_description_english"] ?></textarea>
    <div class="hint">
        <?= wfMsg("requestwiki_descriptionenglishhint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_community") ?></label>
    <textarea id="rw-community" name="rw-community" /><?= $params["request_community"] ?></textarea>
    <div class="hint">
        <?= wfMsg("requestwiki_communityhint") ?>
    </div>
 </div>
 <div class="row">
    <label><?= wfMsg("requestwiki_category") ?></label>
    <textarea id="rw-category" name="rw-category" /><?= $params["request_category"] ?></textarea>
    <div class="info">
        <?= (!empty($errors["rw-category"])) ? $errors["rw-category"] : "&nbsp;" ?>
    </div>
    <div class="hint">
        <?= wfMsg("requestwiki_categoryhint") ?>
    </div>
    <div id="create-tagcloud">
    <?php foreach( $categories as $cat => $size): ?>
        &nbsp;<a href="#tag-<?= str_replace(" ", "-", $cat) ?>" style="<?= $sizes[$size] ?>" id="tag-<?= str_replace(" ", "-", $cat) ?>"><?= $cat ?></a>&nbsp;
        <script type="text/javascript">/*<![CDATA[*/
        YE.addListener("tag-<?= str_replace(" ", "-", $cat) ?>", "click", WR.insertCategory, "<?= $cat ?>" );
        /*]]>*/</script>
    <?php endforeach ?>
    </div>
 </div>
 <?php if (!empty($is_staff)): ?>
 <div class="row admin">
    <label><?= wfMsg("requestwiki_date") ?></label>
    <?= ($editing == 1) ? $params["request_timestamp"] : wfTimestampNow() ?>
    <input size="14" type="hidden" name="rw-timestamp" id="rw-timestamp" value="<?= ($editing == 1) ? $params["request_timestamp"] : wfTimestampNow() ?>" readonly="readonly" />
    <div class="hint">
        <?= wfMsg("requestwiki_datehint") ?>
    </div>
 </div>
 <?php else: ?>
 <input size="14" type="hidden" name="rw-timestamp" id="rw-timestamp" value="<?= ($editing == 1) ? $params["request_timestamp"] : wfTimestampNow() ?>" readonly="readonly" />
 <?php endif ?>
 <div style="text-align: center;">
   <input type="submit" name="rw-submit" id="rw-submit" value="<?= wfMsg("requestwiki_save") ?>" />
 </div>
</fieldset>
<?php if (!empty($is_staff)): ?>
<div class="row admin" style="text-align:center;font-style: italic;">Fields on backgound like this are visible only by staff</div>
<?php endif ?>
</form>
</div>
<!-- e:<?= __FILE__ ?> -->
