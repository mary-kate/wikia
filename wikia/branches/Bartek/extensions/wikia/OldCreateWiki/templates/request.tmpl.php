<!-- s:<?php echo __FILE__ ?> -->
<style type="text/css">
/*<![CDATA[*/
#CreateWikiForm label { display: block; width: 14em !important; float: left; padding-right: 1em; text-align: right;}
#CreateWikiForm input.text { width: 24em;}
#CreateWikiForm option {width: 20em; }
#CreateWikiForm textarea { width: 24em; height: 16em;}
#CreateWikiForm .inactive {color: #2F4F4F; padding: 0.2em; font-weight: bold;}
#CreateWikiForm .admin { background: #F0E68C; }
#CreateWikiForm div.row { padding: 0.8em; margin-bottom: 0.8em; border-bottom: 1px solid #DCDCDC; display: block !important; clear: both; }
#CreateWikiForm div.error { text-align: center; color: #fe0000; font-size: small;}
#CreateWikiForm div.hint {font-style: italic; text-align: justify;margin-left: 15em;}

tr { border: 1px solid #dcdcdc; }
/*]]>*/
</style>

<script type="text/javascript">
/*<![CDATA[*/
//
YAHOO.namespace("Wiki.Create");

var YC = YAHOO.util.Connect;
var YD = YAHOO.util.Dom;
var YE = YAHOO.util.Event;
var WC = YAHOO.Wiki.Create;
var ajaxpath = "<?php echo $GLOBALS["wgScriptPath"]."/index.php" ?>";


YAHOO.Wiki.Create.domainCallback = {
    success: function( oResponse ) {
        var respData = YAHOO.Tools.JSONParse(oResponse.responseText);
        YD.get( "domains-exact" ).innerHTML = respData["exact"];
        YD.get( "domains-like" ).innerHTML = respData["like"];
    },
    failure: function( oResponse ) {
    },
    timeout: 50000
};

YAHOO.Wiki.Create.domainWatch = function (e) {
    var subdomain = YD.get( "wc-name" ).value;
    var lang = YD.get( "wc-language" ).value;
    var prefix = YD.get( "wc-prefix" ).checked;

    if (prefix) {
        subdomain = lang + "." + subdomain;
    }
    var domain = subdomain + ".wikia.com";
    YD.get( "domains-like" ).innerHTML =  '<?php echo Wikia::ImageProgress() ?>';
    YD.get( "domain-preview" ).innerHTML = domain ;
    YAHOO.util.Connect.asyncRequest( "GET", ajaxpath+"?action=ajax&rs=axWCreateCheckName&name="+subdomain, WC.domainCallback);
};

YAHOO.Wiki.Create.check = function(e) {
    YAHOO.util.Event.preventDefault( e ); //--- do not submit by default
    oForm = YAHOO.util.Dom.get( "CreateWikiForm" )
    oHub = YAHOO.util.Dom.get( "wiki-category" );
    if ( oHub.value == 0 ) {
        alert("Select hub for breadcrumbs, please.");
    }
    else {
        oForm.submit();
    }
    return false;
}

YAHOO.util.Event.addListener( "wc-name", "change", WC.domainWatch );
YAHOO.util.Event.addListener( "wc-prefix", "change", WC.domainWatch );
YAHOO.util.Event.addListener( "wc-language", "change", WC.domainWatch );
YAHOO.util.Event.addListener( "CreateWikiForm", "submit", YAHOO.Wiki.Create.check );
/*]]>*/
</script>
<div>
<form name="createwiki" id="CreateWikiForm" method="post" action="<?php echo $title->getFullUrl()?>">
    <input type="hidden" name="action" value="process" />
    <input type="hidden" name="wpFounderUserID" value="<?php echo $request->request_user_id ?>" />
    <input type="hidden" name="wpRequestID" value="<?php echo $request->request_id ?>" />
    <input type="hidden" name="request" value="<?php echo $request->request_id ?>" />
    <div class="row">
        <label><?php echo wfMsg( "createwikifounder" ) ?></label>
        <strong>
            <a href="<?php echo htmlspecialchars($founderpage["href"]) ?>" <?php echo $founderpage["href"] ? "" : "class=\"new\""?>>
                <?php echo $founder->mName ?>
            </a>
            <?php echo "{$founder->mRealName} &lt;{$founder->mEmail}&gt;" ?>
        </strong>
    </div>
    <div class="row">
        <label><?php echo wfMsg( "createwikiname" ) ?></label>
        <input type="text" id="wc-name" name="wpCreateWikiName" value="<?php echo strtolower($request->request_name) ?>" maxlength="255" />
        <div class="error">
        <?php
            if (isset($data["errors"]["wpCreateWikiName"])):
                echo $data["errors"]["wpCreateWikiName"];
            endif
        ?>
        </div>
        <div class="hint">
        <ul>
        <li>
            <strong>Proposed domain:</strong> <span id="domain-preview"><?php echo $name ?></span>
        </li>
        <?php if (is_array($domains) && count($domains)): ?>
        <li>
            <strong>Wiki with the same name:</strong>
            <span id="domains-exact">
            <?php foreach( $domains["exact"] as $domain ): ?>
                <a href="http://<?php echo $domain->city_domain ?>/" target="_blank"><?php echo $domain->city_domain ?></a>&nbsp;
            <?php endforeach ?>
            </span>
        </li>
        <li>
            <strong>Wiki with similar names:</strong>
            <span id="domains-like">
            <?php foreach( $domains["like"] as $domain ): ?>
                <a href="http://<?php echo $domain->city_domain ?>/" target="_blank"><?php echo $domain->city_domain ?></a>&nbsp;
            <?php endforeach ?>
            </span>
        </li>
        <?php endif ?>
        </ul>
        </div>
    </div>
    <div class="row">
        <label><?php echo wfMsg( "createwikititle" ) ?></label>
    	<input type="text" name="wpCreateWikiTitle" value="<?php echo $request->request_title ?>" maxlength="255" />
    </div>
    <div class="row">
        <label><?php echo wfMsg( "createwikilang" ) ?></label>
        <select name="wpCreateWikiLang" id="wc-language">
        <?php
        foreach ($languages as $key => $language) {
            $selected = "";
            if ( $key === $request->request_language ) {
                $selected = "selected=\"selected\"";
            }
            echo "<option value=\"{$key}\" {$selected}>{$key} - {$language}</option>";
        }
        ?>
        </select>
        <div class="hint">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <label>Include language prefix in URL</label>
        <input type="checkbox" name="wpCreateWikiLangPrefix" <?php echo empty($request_prefix) ? "" : "checked=\"checked\"" ?> id="wc-prefix" />
        <div class="hint">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <label>Import content from <a href="http://starter.wikia.com/">starter.wikia.com</a></label>
        <input type="checkbox" name="wpCreateWikiImportStarter" <?php echo empty($request_starter) ? "" : "checked=\"checked\"" ?>/>
        <div class="hint">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <label><?php echo wfMsg( "createwikidesc" ) ?></label>
        <textarea name="wpCreateWikiDesc" /><?php echo $request->request_description_international ?></textarea>
    </div>
    <div class="row">
        <label><?php echo wfMsg( "createwikidesc" ) ?> (in english)</label>
        <textarea name="wpCreateWikiDescEn" /><?php echo $request->request_description_english ?></textarea>
    </div>
    <div class="row">
        <label>Questions &amp; Comments</label>
        <div class="hint" style="background: #eeeeee;border: 1px solid #DCDCDC; padding: 0.2em;">
            <?php echo $talk->getText() ?>
        </div>
    </div>
    <div class="row">
        <label>Page title for Central Wikia</label>
    	<input type="text" name="wpCreateWikiDescPageTitle" value="<?php echo $request->request_title ?>" />
        <div class="hint">
            http://www.wikia.com/<strong>Page_title</strong>
        </div>
    </div>
    <div class="row">
        <label><?php echo wfMsg( "createwikidesc" ) ?> for Central Wikia</label>
        <textarea name="wpCreateWikiDescPage" /><?php echo $description ?></textarea>
    </div>
    <div class="row">
        <label><?php echo wfMsg( "createwikicategory" ) ?></label>
        <textarea name="wpCreateWikiCategory" /><?php echo $request->request_category ?></textarea>
    </div>
    <div class="row">
        <label>Hub</label>
        <?php echo $hubs ?>
        <div class="hint">
            Hub for Breadcrumb
        </div>
    </div>
    <div class="row">
        <label>Additional starter for hub</label>
        <select name="wpCreateWikiCategoryStarter">
            <option value="0" selected="selected">--- not selected ---</option>
            <option value="3711">entertainmentstarter.wikia.com</option>
            <option value="3578">gamingstarter.wikia.com</option>
        </select>
        <div class="hint">
            so far, only two
        </div>
    </div>
    <div style="text-align: center;">
        <input type="submit" name="wpCreateSubmit" value="Create new Wiki from this data" />
        <input type="submit" name="wpRejectSubmit" value="Reject this Wiki" id="wc-reject" />
    </div>
</form>
</div>
<!-- e:<?php echo __FILE__ ?> -->
