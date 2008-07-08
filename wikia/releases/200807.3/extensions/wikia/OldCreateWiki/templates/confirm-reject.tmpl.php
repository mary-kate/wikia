<!-- s:<?= __FILE__ ?> -->
<style type="text/css">/*<![CDATA[*/
#rw-form label { display: block; width: 12em !important; float: left; padding-right: 1em; text-align: right;font-weight: bold; }
#rw-form input.text { width: 24em;}
#rw-form textarea { width: 20em; height: 10em;}
#rw-form div.row { padding-bottom: 0.8em; margin-bottom: 0.8em; display: block; clear: both; border-bottom: 1px solid #DCDCDC; }
#rw-form div.hint { width: 18em; font-style: italic; text-align: left; padding: 0.5em; background: #eeeeee;border: 1px solid #DCDCDC;}
a.r-template {font-weight: bold; cursor:pointer;}
/*]]>*/</style>
<script type="text/javascript">
/*<![CDATA[*/

YAHOO.namespace("Wikia.Reject");

YAHOO.Wikia.Reject.changeTemplate = function ( e, title ) {
    YAHOO.util.Event.preventDefault(e);
    YAHOO.util.Dom.get( "rw-reject-info" ).value = "{{" + title + "| }}";
}

YAHOO.Wikia.Reject.init = function () {
    // get all links with "r-template" class and add listener
    var links = YAHOO.util.Dom.getElementsByClassName("r-template", "a");
    for ( var l in links ) {
        YAHOO.util.Event.addListener( links[ l ], "click", YAHOO.Wikia.Reject.changeTemplate, links[ l ].title );
    }
}
YAHOO.util.Event.onDOMReady(YAHOO.Wikia.Reject.init)

/*]]>*/
</script>


<div id="rw-form">
    <form action="<?= $title->getLocalUrl("action=reject&doit=1") ?>" method="post">
    <fieldset>
        <legend>Please confirm rejecting of Wiki:</legend>
        <div class="row">
            <label>Name:</label>
            <span><?= $request->request_name ?></span>
        </div>
        <div class="row">
            <label>Title:</label>
            <span><?= $request->request_title ?></span>
        </div>
        <div class="row">
            <label>Description:</label>
            <span><?= $request->request_description_english ?></span>
            <span><?= $request->request_description_international ?></span>
        </div>
        <div class="row">
            <label>Reason for rejecting:</label>
            <div>
                <textarea id="rw-reject-info" name="wpRejectInfo" style="float: left;" /></textarea>
                <div class="hint" style="float: left;">
                    <?= wfMsg("createwikirejecttemplates") ?>
                </div>
            </div>
            <br style="clear: both;" />
        </div>
        <div style="text-align: center;">
            <input type="hidden" name="request" id="request" value="<?= $request->request_id ?>" />
            <input type="submit" name="rw-submit" id="rw-submit" value="Reject request" />
        </div>
    </fieldset>
    </form>
</div>
<!-- e:<?= __FILE__ ?> -->
