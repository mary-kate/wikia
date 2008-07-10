<?php

/**
 * @package MediaWiki
 * @subpackage RequestWiki
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
# Helpers functions, used mostly in Ajax calls
#----------------------------------------------------------------------------
function __rebuildRequestWikiMessages()
{
    global $wgMessageCache, $wgRequestWikiMessages;
    #--- Add messages
    foreach( $wgRequestWikiMessages as $key => $value ) {
        $wgMessageCache->addMessages( $wgRequestWikiMessages[$key], $key );
    }
}


/**
 * checks if category string is properly formated (up to 5 strings splitted
 * by coma, then normalize string (trims)
 *
 * false - when input is badly formatted
 *
 * normalized string - when everything is fine and wantarray = 0
 * array - when everything is fine and wantarray = 1
 */
function wfRequestCategoryCheck($catInput, $wantarray = 0)
{
    $aCategories = explode(",", $catInput);

    #--- check if is not more than 5 categories
    if (sizeof( $aCategories ) > 5 ) {
        return false;
    }

    #--- "normalize" string
    $aTmp = array();
    foreach ($aCategories as $category) {
        $aTmp[] = trim($category);
    }
    if (!empty($wantarray)) {
        return $aTmp;
    }
    else {
        return implode( ", ", $aTmp );
    }
}

/**
 * wfRequestExact
 *
 * check in city_domains if we have such wikia in city_domains
 *
 * @param string $name: domain name
 * @param string $language default null - choosen language
 *
 * @return integer - 0 or 1
 */
function wfRequestExact( $name, $language = null  )
{
    $sDomain = Wikia::fixDomainName($name, $language);

    $dbr = wfGetDB( DB_SLAVE );
    $oRow = $dbr->selectRow(
        wfSharedTable("city_domains"),
        array( "count(*) as count" ),
        array( "city_domain" => $sDomain ),
        __METHOD__
    );
    return $oRow->count;
}

/**
 * wfRequestTitle
 *
 * build Title for request page
 *
 * @param string $name: domain name
 * @param string $language default null: choosen language
 *
 * @return Title: MW Title class
 */
function wfRequestTitle( $name, $language = null)
{
    global $wgContLang;

    $sTitle = ($language == "en")
        ? $wgContLang->ucfirst(trim($name))
        : $wgContLang->ucfirst(trim($language)).".".$wgContLang->ucfirst(trim($name));

    return Title::newFromText( $sTitle, NS_MAIN );
}

/**
 * wfRequestLikeOrExact
 *
 * check if name is similar or the same, using sql like queries
 *
 * @access public
 * @author eloy@wikia
 *
 * @param string $name: name to check
 * @param string $language default null - choosen language
 *
 * @return array with matches
 */
function wfRequestLikeOrExact( $name, $language = null )
{

    $dbr = wfGetDB( DB_SLAVE );

    #--- check name availability
    $aDomains = array();
    $aDomains["like"] = array();
    $aDomains["exact"] = array();
    $aSkip = array();


    #--- don't check short names
    if (strlen($name) > 2) {

        $names = explode(" ", $name);
        $bSkipCondition = false;
        $aCondition = array();

        if (is_array($names)) {
            foreach ($names as $n) {
                if (!preg_match("/^[\w\.]+$/",$n)) continue;
                $aCondition[] = "city_domain like '{$n}.%'";
            }
            if (sizeof($aCondition)) {
                $sCondition = implode(" or ", $aCondition);
            }
            else {
                $bSkipCondition = true;
            }
        }
        else {
            $sCondition = "city_domain like '{$name}.%'";
        }

        if ( $bSkipCondition === false ) {
            #--- exact (but with language prefixes)
            $oRes = $dbr->select(
                wfSharedTable("city_domains"),
                array("*"),
                array($sCondition),
                __METHOD__,
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

            $oRes = $dbr->select(
                wfSharedTable("city_domains"),
                array("*"),
                array($sCondition),
                __METHOD__,
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
    return $aDomains;
}


#----------------------------------------------------------------------------
# Ajax requests handlers
#----------------------------------------------------------------------------

/**
 * check name availability in databases (city_list and city_list_requests tables)
 */
function axWRequestCheckName()
{
    __rebuildRequestWikiMessages();

    global $wgRequest, $wgDBname, $wgContLang;

    $sName = $wgRequest->getVal("name");
    $sLang = $wgRequest->getVal("lang");
    $iEdit = $wgRequest->getVal("edit");

    $iError = 0;
    $sResponse = Wikia::successmsg(wfMsg("requestwiki_validname"));

    if (!strlen($sName)) {
        $sResponse = Wikia::errormsg(wfMsg("requestwiki_errorempty"));
        $iError++;
    }
    elseif (!ctype_alnum($sName)) {
        $sResponse = Wikia::errormsg(wfMsg("requestwiki_badname"));
        $iError++;
    }
    else {

        $iExists = wfRequestExact($sName, $sLang);
        #--- only $aDomains["exact"] are insteresting
        if (!empty($iExists)) {
            $sResponse = Wikia::errormsg(wfMsg("requestwiki_usedname"));
            $iError++;
        }
        else {
            #--- check city_list_requests as well
            $dbr = wfGetDB( DB_SLAVE );
            $oRow = $dbr->selectRow(
                wfSharedTable("city_list_requests"),
                array( "*" ),
                array(
                    "LOWER(request_name)" => strtolower($sName),
                    "request_language" => $sLang
                ),
                __METHOD__
            );
            if ( !empty($oRow->request_id) && $oRow->request_id != $iEdit ) {
                $sResponse = Wikia::errormsg(wfMsg("requestwiki_inprogress"));
                $iError++;
            }

            # check if there is article on requests.wikia.com and it doesn't
            # contain RequestForm2 template

            #-- build page from elements
            $oTitle = wfRequestTitle( $sName, $sLang );
            $oArticle = new Article( $oTitle /*title*/, 0 );
            $sContent = $oArticle->getContent();
            if (empty($iEdit)) {
                if ($oArticle->exists() && strpos($sContent, "RequestForm2" ) === false) {
                    $sResponse = Wikia::errormsg(
                        wfMsg("requestwiki_pagexists", array( sprintf("<a href=\"%s\">%s</a>",
                            $oTitle->getLocalURL(),$oTitle->getText()))
                    ));
                    $iError++;
                }
            }
        }
    }
    $aResponse = array(
        "div-body" => $sResponse,
        "is-error" => $iError,
        "div-name" => "rw-name-check"
    );

    if (!function_exists('json_encode'))  {
        $oJson = new Services_JSON();
        return $oJson->encode($aResponse);
    }
    else {
        return json_encode($aResponse);
    }
}

/**
 * Like or Exact
 * returns unordered list <ul><li></li></ul>
 */
function axRequestLikeOrExact()
{
    global $wgRequest;

    __rebuildRequestWikiMessages();

    $sName = $wgRequest->getVal("name");
    $sLike = $sExact = "";

    $aDomains = wfRequestLikeOrExact( $sName );

    if (strlen($sName) < 3) {
        $sLike = "&nbsp;";
        $sExact = wfMsg("requestwiki_nametooshort");
    }
    elseif ( count($aDomains["exact"]) == 0 && count($aDomains["like"]) == 0 ) {
        $sLike = "&nbsp;";
        $sExact = "<div>".wfMsg("requestwiki_noexact")."</div>";
    }
    else {
        if (is_array($aDomains["like"])) {
            foreach ( $aDomains["like"] as $domain ) {
                $sLike .= "<li><a href=\"http://{$domain->city_domain}/\" target=\"_blank\">{$domain->city_domain}</a></li>";
            }
        }

        if (is_array($aDomains["exact"])) {
            foreach ( $aDomains["exact"] as $domain ) {
                $sExact .= "<li><a href=\"http://{$domain->city_domain}/\" target=\"_blank\">{$domain->city_domain}</a></li>";
            }
        }
        $sLike = "<ul>{$sLike}</ul>";
        $sExact = "<ul>{$sExact}</ul>";
    }


    $aResponse = array(
        "like" => $sLike,
        "exact" => $sExact
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
$wgAjaxExportList[] = "axWRequestCheckName";
$wgAjaxExportList[] = "axRequestLikeOrExact";
?>
