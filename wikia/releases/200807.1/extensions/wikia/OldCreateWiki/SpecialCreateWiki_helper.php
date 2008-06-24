<?php

/**
 * @package MediaWiki
 * @subpackage CreateWiki
 * @author Krzysztof Krzyżaniak <eloy@wikia.com> for Wikia.com
 * @version: 0.1
 *
 * helper classes & functions
 */

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "This is MediaWiki extension and cannot be used standalone.\n";
    exit( 1 ) ;
}

#----------------------------------------------------------------------------
#--- classes ----------------------------------------------------------------
#----------------------------------------------------------------------------


/**
 * rebuild messages, it should be only done when is not already done (but
 * I don't know how to)
 */
function __rebuildCreateWikiMessages()
{
    global $wgMessageCache, $wgCreateWikiMessages;
    #--- Add messages
    if (is_array($wgCreateWikiMessages)) {
        foreach( $wgCreateWikiMessages as $key => $value ) {
            $wgMessageCache->addMessages( $wgCreateWikiMessages[$key], $key );
        }
    }
}

#----------------------------------------------------------------------------
#--- functions --------------------------------------------------------------
#----------------------------------------------------------------------------

/**
 * checking for domain availability (by using sql "like" comparing
 */
function wfWCreateCheckName( $name )
{
    global $wgDBname;

    $dbr = wfGetDB( DB_SLAVE );

    #--- switch to shared
    $dbr->selectDB( "wikicities" );

    #--- check name availability
    $aDomains = array();
    $aSkip = array();

    #--- don't check short names
    if (strlen($name) > 2) {

        $names = explode(" ", $name);
        $bSkipCondition = false;
        $aCondition = array();
        if (is_array($names)) {
            foreach ($names as $n) {
                if (!preg_match("/^[\w\.]+$/",$n)) continue;
                $aCondition[] = "city_domain like '%.{$n}.%'";
            }

            if (sizeof($aCondition)) {
                $sCondition = implode(" or ", $aCondition);
            }
            else {
                $bSkipCondition = true;
            }
        }
        else {
            $sCondition = "city_domain like '%.{$name}.%'";
        }

        if ( $bSkipCondition === false ) {
            #--- exact (but with language prefixes)
            $oRes = $dbr->select("city_domains",
                array("*"),
                array($sCondition), __METHOD__,
                array("limit" => 20)
            );

            while ($oRow = $dbr->fetchObject($oRes)) {
                if (preg_match("/^www\./", strtolower($oRow->city_domain))) continue;
                if (preg_match("/wikicities\.com/", strtolower($oRow->city_domain))) continue;
                $aSkip[strtolower($oRow->city_domain)] = 1;
                    $aDomains["exact"][] = $oRow;
            }
            $dbr->freeResult($oRes);
        }

        #--- similar
        $bSkipCondition = false;
        $aCondition = array();
        if (is_array($names)) {
            foreach ($names as $n) {
                if (!preg_match("/^[\w\.]+$/",$n)) continue;
                $aCondition[] = "city_domain like '%{$n}%'";
            }
            if (sizeof($aCondition)) {
                $sCondition = implode(" or ", $aCondition);
            }
            else {
                $bSkipCondition = true;
            }
        }
        else {
            $sCondition = "city_domain like '%{$name}%'";
        }

        if ( $bSkipCondition === false ) {
            $oRes = $dbr->select("city_domains",
                array("*"),
                array($sCondition), __METHOD__,
                array("limit" => 20)
            );

            while ($oRow = $dbr->fetchObject($oRes)) {
                if (preg_match("/^www\./", strtolower($oRow->city_domain))) continue;
                if (preg_match("/wikicities\.com/", strtolower($oRow->city_domain))) continue;
                if ($aSkip[strtolower($oRow->city_domain)] == 1) continue;
                $aDomains["like"][] = $oRow;
            }
            $dbr->freeResult($oRes);
        }
    }

    #--- back to normal database
    $dbr->selectDB( $wgDBname );
    return $aDomains;
}

/**
 * for cooperating with ajax requests
 * format = 1, unordered list <ul><li></li></ul>
 * format = 0, just string
 */
function axWCreateCheckName()
{
    global $wgRequest;

    __rebuildCreateWikiMessages();

    $sName = $wgRequest->getVal("name");

    $like = "";
    $exact = "";

    $aDomains = wfWCreateCheckName( $sName );

    if (is_array($aDomains["like"])) {
        foreach ( $aDomains["like"] as $key => $domain ) {
            $like .= "<a href=\"http://{$domain->city_domain}/\" target=\"_blank\">{$domain->city_domain}</a> ";
        }
    }
    else {
        $like = "none";
    }
    if (is_array($aDomains["exact"])) {
        foreach ( $aDomains["exact"] as $key => $domain ) {
            $exact .= "<a href=\"http://{$domain->city_domain}/\" target=\"_blank\">{$domain->city_domain}</a> ";
        }
    }
    else {
        $exact = "none";
    }

    if (strlen($sName) < 3) {
        $exact = wfMsg("createwikinametooshort");
        $like = "&nbsp;";
    }

    $aResponse = array(
        "like" => $like,
        "exact" => $exact
    );

    if (!function_exists('json_encode'))  {
        $oJson = new Services_JSON();
        return $oJson->encode($aResponse);
    }
    else {
        return json_encode($aResponse);
    }
}

global $wgAjaxExportList;
$wgAjaxExportList[] = "axWCreateCheckName";

?>
