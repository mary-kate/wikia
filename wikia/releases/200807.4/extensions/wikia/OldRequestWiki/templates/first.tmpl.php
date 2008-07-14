<!-- s:<?= __FILE__ ?> -->
<style type="text/css">/*<![CDATA[*/
#cw-form { text-align: center; }
#cw-result { text-align: left; padding: 1em;}
#pSubmit {padding: 1em; font-size: larger; background: #DCDCDC; }
h2 { margin-top: 1em; }
/*]]>*/</style>
<script type="text/javascript">/*<![CDATA[*/

YAHOO.namespace("Wikia.Request");

var YC = YAHOO.util.Connect;
var YD = YAHOO.util.Dom;
var YE = YAHOO.util.Event;
var WR = YAHOO.Wikia.Request;
var ajaxpath = "<?php echo $GLOBALS["wgScriptPath"]."/index.php" ?>";

WR.WatchCallback = {
    success: function( oResponse ) {
        var Data = YAHOO.Tools.JSONParse(oResponse.responseText);
        document.getElementById("cw-result").innerHTML =
            "<div style=\"text-align: left;\">"
            + Data["exact"] + Data["like"] + "</div>";
        document.getElementById( "cw-submit" ).disabled = false;
        document.getElementById( "cw-name" ).disabled = false;
    },
    failure: function( oResponse ) {
        YAHOO.log( "simple replace failure " + oResponse.responseText );
    },
    timeout: 20000
};

WR.watchForm = function (e) {
    YE.preventDefault(e);
    var name = document.getElementById("cw-name").value;
    document.getElementById( "cw-result" ).innerHTML = '<img src="/skins/wikia/images/progress-wheel.gif" width="16" height="16" alt="wait" border="0" />';
    document.getElementById( "cw-submit" ).disabled = true;
    document.getElementById( "cw-name" ).disabled = true;
    YC.asyncRequest( "GET", ajaxpath+"?action=ajax&rs=axRequestLikeOrExact&name="+name, WR.WatchCallback);
};

YE.addListener("cw-submit", "click", WR.watchForm, "cw-submit" );
/*]]>*/</script>
<?php if (!empty($is_staff)): ?>
<div>
    [<a href="<?= $title->getLocalUrl("action=list") ?>">list of requests</a>]
</div>
<?php endif ?>
<div style="text-align:center;">
    <h1><?= wfMsg("requestwiki_starting") ?></h1>
    <p>
        <?= wfMsg("requestwiki_startinginfo") ?>
    </p>
</div>

<h2>1. <?= wfMsg("requestwiki_question1") ?></h2>
<p>
    <div>
        <?= wfMsg("requestwiki_question1more") ?>
    </div>
    <br />
    <form id="cw-form">
        <label><?= wfMsg("requestwiki_name") ?></label>
        <input type="text" name="name" size="24" id="cw-name" />
        <button name="submit" size="24" id="cw-submit"><?= wfMsg("requestwiki_question1submit") ?></button>
        <div>
        <?= wfMsg("requestwiki_question1tip") ?>
        </div>
        <div id="cw-result"></div>
    </form>
</p>
<h2>2. <?= wfMsg("requestwiki_question2") ?></h2>
<p><?= wfMsg("requestwiki_question2more") ?></p>
<h2>3. <?= wfMsg("requestwiki_question3") ?></h2>
<p><?= wfMsg("requestwiki_question3more") ?></p>
<h2>4. <?= wfMsg("requestwiki_question4") ?></h2>
<div style="text-align: center">
    <p>
        <?= wfMsg("requestwiki_question") ?>
    </p>
    <form action="<?= $title->getLocalUrl("action=second") ?>" method="post">
        <input type="submit" name="wiki-submit" id="pSubmit" value="<?= wfMsg("requestwiki_agree") ?>" />
    </form>
</div>
<!-- e:<?= __FILE__ ?> -->
