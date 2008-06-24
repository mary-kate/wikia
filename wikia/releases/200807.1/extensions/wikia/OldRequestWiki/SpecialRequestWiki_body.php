<?php

/**
 * @package MediaWiki
 * @subpackage SpecialPage
 * @author Krzysztof Krzyżaniak <eloy@wikia.com> for Wikia.com
 * @version: 0.1
 */

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "This is MediaWiki extension named RequestWiki.\n";
    exit( 1 ) ;
}
#--- Add messages
global $wgMessageCache, $wgRequestWikiMessages;
foreach( $wgRequestWikiMessages as $key => $value ) {
    $wgMessageCache->addMessages( $wgRequestWikiMessages[$key], $key );
}

/**
 * @addtogroup SpecialPage
 */

/**
 * main class
 */
class RequestWikiPage extends SpecialPage {

    var $mTitle, $mAction;

    var $mCategories = array(
        "Entertainment"             => 0,
        "Gaming"                    => 0,
        "Sports"                    => 0,
        "Technology"                => 0,
        "Travel"                    => 0,
        "Finance"                   => 0,
        "Books"                     => 1,
        "Education"                 => 1,
        "Health"                    => 1,
        "Hobbies"                   => 1,
        "Miscellaneous"             => 1,
        "Philosophy and Religion"   => 1,
        "Politics and Activism"     => 1,
        "Science and Nature"        => 1,
        "Art"                       => 2,
        "Business"                  => 2,
        "Cartoons"                  => 2,
        "Comics"                    => 2,
        "Communication"             => 2,
        "Culture"                   => 2,
        "Documentation"             => 2,
        "Engineering"               => 2,
        "Events and meetings"       => 2,
        "Fanon"                     => 2,
        "Food and drink"            => 2,
        "Forums"                    => 2,
        "History"                   => 2,
        "Humor"                     => 2,
        "Imagination"               => 2,
        "Language"                  => 2,
        "Non-profit Organizations"  => 2,
        "People"                    => 2,
        "Personal life"             => 2,
        "Places"                    => 2,
        "Science fiction"           => 2,
        "Society"                   => 2,
        "Sustainability"            => 2
    );
    var $mCloudSizes = array(
        0 => "font-size: larger;",
        1 => "font-size: normal;",
        2 => "font-size: small;"
    );

    /**
     * contructor
     */
    function  __construct() {
        #--- we use 'createwiki' restriction
        parent::__construct( "RequestWiki"  /*class*/, 'requestwiki' /*restriction*/);
    }

    function execute() {
        global $wgUser, $wgOut, $wgRequest;

        if ( $wgUser->isBlocked() ) {
            $wgOut->blockedPage();
            return;
        }
        if ( wfReadOnly() ) {
            $wgOut->readOnlyPage();
            return;
        }
        if ( !$wgUser->isAllowed( 'requestwiki' ) ) {
            $this->displayRestrictionError();
            return;
        }

        #--- initial output
        $this->mTitle = Title::makeTitle( NS_SPECIAL, 'RequestWiki' );
        $wgOut->setPageTitle( wfMsg('requestwiki_pagetitle') );
        $wgOut->setRobotpolicy( 'noindex,nofollow' );
        $wgOut->setArticleRelated( false );

        $this->mAction = $wgRequest->getVal("action");
        if (empty($this->mAction)) {
            $this->do_firststep();
        }
        elseif ($this->mAction === "second") {
            $this->do_secondstep();
        }
        elseif ($this->mAction === "third") {
            $this->do_thirdstep();
        }
        elseif ($this->mAction === "list") {
            $this->do_list();
        }

    }

    /////////////////////////////////////////////////////////////////////////
    function do_firststep()
    {
        global $wgOut, $wgUser;

        #--- check if user is logged in
        #--- check if user have confirmed mail email first
        $oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );

        if (!$wgUser->isLoggedIn()) {
            #--- show info that user should be logged in
            $oTmpl->set_vars( array(
                "title" => $this->mTitle,
                "is_logged" => 0,
                "login" => Title::makeTitle( NS_SPECIAL, 'UserLogin' ),
            ));
            $wgOut->addHTML( $oTmpl->execute("autoconfirm") );
        }
        elseif (!$wgUser->isAllowed( 'emailconfirmed' )) {
            #--- show info that user should be emailconfirmed
            $oTmpl->set_vars( array(
                "title" => $this->mTitle,
                "is_logged" => 1,
                "confirm" => Title::makeTitle( NS_SPECIAL, 'ConfirmEmail' )
            ));
            $wgOut->addHTML( $oTmpl->execute("autoconfirm") );
        }
        else {
            $oTmpl->set_vars( array(
                "title" => $this->mTitle,
                "is_staff" => in_array("staff", $wgUser->mGroups) ? 1 : 0
            ));
            $wgOut->addHTML( $oTmpl->execute("first") );
        }
    }

    #--- do_secondstep ------------------------------------------------------
    /**
     * show request form
     */
    function do_secondstep($errors = null, $params = array())
    {
        global $wgOut, $wgUser, $wgRequest;

        $oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );

        $languages = Language::getLanguageNames();
        $request = null;
        $editing = 0;
        $request = array();
        $iRequestID = $wgRequest->getIntOrNull("id");

        #--- flags
        $is_staff = in_array("staff", $wgUser->getGroups()) ? 1 : 0;
        $is_requester = 0;

        if (!empty($iRequestID)) {
            #--- get request data from database
            $dbr = wfGetDB( DB_SLAVE );
            $dbr->selectDB( "wikicities" );

            $res = $dbr->select("city_list_requests", array("*"),
                array("request_id" => $iRequestID));
            $params = $dbr->fetchRow($res);
            $dbr->freeResult( $res );
            $editing++;

            #--- now get requester data
            $oRequester = User::newFromId($params["request_user_id"]);
            $oRequester->load();
            if ($oRequester->getID() == $wgUser->getID()) {
                $is_requester = 1;
            }
            #--- disconnect from shareddb
            $dbr->selectDB($wgDBname);
        }
        else {
            if (!empty($params["request_user_id"])) {
                #--- create user object from parameter
                $oRequester = User::newFromId( $params["request_user_id"] );
                $oRequester->load();

                #--- but of course check if requester exists
                if ( ! $oRequester->getID() ) {
                    $oRequester = $wgUser;
                }
                $is_requester = 1;
            }
            else {
                $oRequester = $wgUser;
                $is_requester = 1;
            }
        }

        #--- additional links
        $oLinksTitle = Title::makeTitle( NS_SPECIAL, 'CreateWiki' );
        $aLinks = array();
        foreach ( array("create", "reject", "delete") as $action ) {
            $aLinks[$action] = $oLinksTitle->getLocalUrl("action={$action}&request={$params["request_id"]}");
        }

        ksort( $this->mCategories );
        $oTmpl->set_vars( array(
            "user"      => $oRequester,
            "sizes"     => $this->mCloudSizes,
            "title"     => $this->mTitle,
            "links"     => $aLinks,
            "errors"    => $errors,
            "params"    => $params,
            "editing"   => $editing,
            "is_staff"  => $is_staff,
            "languages" => $languages,
            "categories"    => $this->mCategories,
            "request_id"    => $iRequestID,
            "is_requester"  => $is_requester
        ));

        if (empty($is_requester) && empty($is_staff)) {
            $wgOut->addHTML( $oTmpl->execute("comments") );
        }
        else {
            $wgOut->addHTML( $oTmpl->execute("second") );
        }
    }

    #--- do_thirdstep -------------------------------------------------------
    /**
     * third step, validate all params and store request in database.
     * if there are errors redirect to do_secondstep
     */
    function do_thirdstep()
    {
        global $wgOut, $wgRequest, $wgContLang, $wgDBname, $wgUser;

        #-- some initialization
        $errors = array();
        $params = array();
        $notvalid = 0;


        #--- fill params array with request data

        $params["request_user_id"]      = $wgRequest->getVal("rw-userid");
        $params["request_timestamp"]    = $wgRequest->getText("rw-timestamp");
        $params["request_title"]        = $wgContLang->ucfirst(trim($wgRequest->getVal("rw-title")));
        $params["request_name"]         = $wgContLang->lc(trim($wgRequest->getVal( "rw-name" )));
        $params["request_language"]     = $wgRequest->getVal("rw-language");
        $params["request_category"]     = $wgRequest->getVal("rw-category");
        $params["request_community"]    = $wgRequest->getVal("rw-community");
        $params["request_comments"]     = $wgRequest->getText("rw-comments");
        $params["request_questions"]    = $wgRequest->getText("rw-questions");
        $params["request_description_page"]             = $wgRequest->getVal("rw-description-page");
        $params["request_description_english"]          = $wgRequest->getVal("rw-description-english");
        $params["request_description_international"]    = $wgRequest->getVal("rw-description-international");

        $editing    = $wgRequest->getIntOrNull( "editing" );
        $iRequestID = $wgRequest->getIntOrNull( "rw-id" );
        $iRequestUserID = $wgRequest->getIntOrNull( "rw-userid" );
        $sRequestUserName = $wgRequest->getVal("rw-username");

        #---
        # staff can change username, we would know about it by comparing
        # $sRequestUserName with getName user object created from $iRequestUserID

        $oRequester = User::newFromId($iRequestUserID);
        $oRequester->load();

        if ( $oRequester->getName() != $sRequestUserName ) {
            #--- name was changed, check if user exists
            $oNewRequester = User::newFromName( $sRequestUserName );
            $oNewRequester->load();

            if ( ! $oNewRequester->getID() ) {
                $errors["rw-username"] = Wikia::errormsg(wfMsg("requestwiki_usernameerror"));
                $this->do_secondstep($errors, $params);
                return;
            }
            $params["request_user_id"] = $oNewRequester->getID();
        }

        #--- we need name, seriously
        if (empty($params["request_name"]) || strlen($params["request_name"]) == 0) {
            $errors["rw-name"] = Wikia::errormsg(wfMsg("requestwiki_errorempty"));
            $this->do_secondstep($errors, $params);
            return;
        }

        #--- and properly formated categories
        if ( wfRequestCategoryCheck( $params["request_category"] ) === false) {
            $errors["rw-category"] = Wikia::errormsg(wfMsg("requestwiki_badformatcat"));
            $this->do_secondstep($errors, $params);
            return;
        }
        else {
            $params["request_category"] = wfRequestCategoryCheck( $params["request_category"] );
        }

        #--- check citydomain before creating wikia
        $bExists = wfRequestExact($params["request_name"], $params["request_language"]);
        if (!empty($bExists)) {
            $errors["rw-name"] = Wikia::errormsg(wfMsg("requestwiki_usedname"));
            $this->do_secondstep($errors, $params);
            return;
        }

        #--- master connection
        $dbw = wfGetDB( DB_MASTER );
        $dbw->selectDB( "requests" );

        #---
        # check if there is article on requests.wikia.com and it doesn't
        # contain RequestForm2 template

        #-- build page from elements
        $oTitle = wfRequestTitle( $params["request_name"], $params["request_language"] );
        $oArticle = new Article( $oTitle /*title*/, 0 );
        $sContent = $oArticle->getContent();
        if (empty($iEdit)) {
            if ($oArticle->exists() && strpos($sContent, "RequestForm2" ) === false) {
                $errors["rw-name"] = Wikia::errormsg(
                    wfMsg("requestwiki_pagexists", array(
                        sprintf("<a href=\"%s\">%s</a>", $oTitle->getLocalURL(),$oTitle->getText()))
                ));
                $this->do_secondstep($errors, $params);
                return;
            }
        }

        $bTitleChanged = false;
        if (empty($editing)) {
            #--- these fields are mandatory
            foreach (array("request_title" => "rw-title", "request_category" => "rw-category") as $param => $field) {
                if (empty($params[$param]) || strlen($params[$param]) == 0) {
                    $errors[$field] = Wikia::errormsg(wfMsg("requestwiki_errorempty"));
                    $notvalid++;
                }
            }

            #--- some fields are empty
            if ($notvalid) {
                $this->do_secondstep($errors, $params);
                return;
            }

            #--- first make sure if really, REALLY pair name-language doesn't exists
            $row = $dbw->selectRow(
                wfSharedTable("city_list_requests"),
                array("count(*) as count"),
                array("request_name" => $params["request_name"], "request_language" => $params["request_language"]),
                __METHOD__
            );
            if (!empty($row->count)) {
                #--- redirect to existed request
                $errors["rw-name"] = "<span style=\"color: #fe0000; font-weight: bold;\">".wfMsg("requestwiki_inprogress")."</span>";
                $this->do_secondstep($errors, $params);
                return;
            }
            else {
                #--- ewentualy insert new request
                $dbw->insert(wfSharedTable("city_list_requests"), $params, __METHOD__);
                $iRequestID = $dbw->insertId();
            }
        }
        else {
            #--- editing exisiting request
            unset( $params["request_timestamp"] );

            #---
            # check if title is changed, if is changed mark it -
            # then we first read request from database
            $oRow = $dbw->selectRow(
                wfSharedTable( "city_list_requests" ),
                array( "*" ),
                array( "request_id" => $iRequestID ),
                __METHOD__
            );

            $dbw->update(
                wfSharedTable("city_list_requests"),
                $params,
                array( "request_id" => $iRequestID ),
                __METHOD__
            );

            if (strtolower($oRow->request_name) != strtolower($params["request_name"]) ||
                strtolower($oRow->request_language) != strtolower($params["request_language"])
            ){
                $bTitleChanged = true;
                $oOldTitle = wfRequestTitle( $oRow->request_name, $oRow->request_language );
            }
        }
        #-- build page from elements
        $oTitle = wfRequestTitle( $params["request_name"], $params["request_language"] );

        $oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
        $oTmpl->set_vars( array(
            "title"         => $this->mTitle,
            "params"        => $params,
            "username"      => $sRequestUserName,
            "languages"     => Language::getLanguageNames(),
            "request_id"    => $iRequestID,
            "categories"    => wfRequestCategoryCheck($params["request_category"], 1 /*wantarray*/),
        ));
        $sPage = $oTmpl->execute("page-template");

        $oArticle = new Article( $oTitle /*title*/, 0 );

        #--- set redirection if title was changed
        if ( $bTitleChanged == true ) {
            $oArticle->setRedirectedFrom( $oOldTitle );
        }
        #--- delete page_restrictions for this article (if any)
        if ( $oArticle->getID() ) {
            $dbw->delete("page_restrictions", array("pr_page" => $oArticle->getID()), __METHOD_);
            $iFlags = EDIT_UPDATE|EDIT_MINOR;
        }
        else {
            $iFlags = EDIT_NEW;
        }

        #--- insert template into page
        $oArticle->doEdit( $sPage, "new or updated request", $iFlags);


        #--- update restrictions
        $dbw->insert("page_restrictions", array(
            "pr_page" => $oArticle->getID(),
            "pr_type" => "edit",
            "pr_level" => "sysop",
            "pr_cascade" => 0,
            "pr_user" => null,
            "pr_expiry" => "infinity"
        ));
        $dbw->insert("page_restrictions", array(
            "pr_page" => $oArticle->getID(),
            "pr_type" => "move",
            "pr_level" => "sysop",
            "pr_cascade" => 0,
            "pr_user" => null,
            "pr_expiry" => "infinity"
        ));

        #$oArticle->updateRestrictions(
        #            array( "edit" => "sysop", "move" => "sysop" ),
        #    "auto after creating or editing", 0, "infinity"
        #);

        #--- now if name was changed we have to edit old page and made redirect
        if ( $bTitleChanged == true ) {
            $oOldArticle = new Article( $oOldTitle /*title*/, 0 /*current id*/ );
            $sNewTitle = $oTitle->getText();
            $oOldArticle->doEdit( "#REDIRECT [[{$sNewTitle}]]", "redirect after name changing", EDIT_UPDATE|EDIT_MINOR );

            #--- update restrictions
            $oOldArticle->updateRestrictions(
                array( "edit" => "sysop", "move" => "sysop" ),
                "auto after creating or editing", 0, "infinity"
            );
        }

        $oTmpl->set_vars( array(
            "link"  => $oTitle->getLocalUrl( "action=purge" ),
            "title" => $this->mTitle
        ));

        return $wgOut->addHTML( $oTmpl->execute("third") );
    }

    #--- do_list ------------------------------------------------------------
    function do_list()
    {
        global $wgOut;
        $pager = new RequestListPager;

        $oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
        $oTmpl->set_vars( array(
            "title" => $this->mTitle,
            "form"  => $pager->getForm(),
            "body"  => $pager->getBody(),
            "pager" => $pager->getNavigationBar()
        ));
        $wgOut->addHTML( $oTmpl->execute("list") );
    }
};

class RequestListPager extends TablePager {
    var $mFieldNames = null;
    var $mMessages = array();
    var $mQueryConds = array();

    #--- constructor --------------------------------------------------------
    function __construct()
    {
        global $wgRequest, $wgMiserMode;
        if ( $wgRequest->getText( 'sort', 'img_date' ) == 'img_date' ) {
            $this->mDefaultDirection = true;
        } else {
            $this->mDefaultDirection = false;
        }
        $search = $wgRequest->getText( 'ilsearch' );
        if ( $search != '' && !$wgMiserMode ) {
            $nt = Title::newFromUrl( $search );
            if( $nt ) {
                $dbr = wfGetDB( DB_SLAVE );
                $m = $dbr->strencode( strtolower( $nt->getDBkey() ) );
                $m = str_replace( "%", "\\%", $m );
                $m = str_replace( "_", "\\_", $m );
             }
        }
        parent::__construct();
    }

    #--- getFieldNames ------------------------------------------------------
    function getFieldNames()
    {

        if ( !$this->mFieldNames ) {
            $this->mFieldNames = array();
            $this->mFieldNames["request_name"] = wfMsg( "requestwiki_request_name" );
            $this->mFieldNames["request_language"] = wfMsg( "requestwiki_request_language" );
            $this->mFieldNames["request_category"] = wfMsg( "requestwiki_request_category" );
            $this->mFieldNames["request_timestamp"] = wfMsg( "requestwiki_request_timestamp" );
            $this->mFieldNames["request_id"] = wfMsg( "requestwiki_request_id" );
        }
        return $this->mFieldNames;
    }

    #--- isFieldSortable-----------------------------------------------------
    function isFieldSortable( $field ) {
        static $sortable = array( "request_name", "request_language", "request_category", "request_timestamp" );
        return in_array( $field, $sortable );
    }

    #--- formatValue --------------------------------------------------------
    function formatValue( $field, $value ) {
        global $wgLang, $wgUser, $wgContLang;

        switch ( $field ) {
            case "request_id":
                if (in_array("staff", $wgUser->getGroups())) {
                    $title = Title::makeTitle( NS_SPECIAL, 'CreateWiki' );
                    return sprintf("<a href=\"%s\">create</a> <a href=\"%s\">reject</a> <a href=\"%s\">delete</a>",
                        $title->getLocalUrl("action=create&request={$value}"),
                        $title->getLocalUrl("action=reject&request={$value}"),
                        $title->getLocalUrl("action=delete&request={$value}"));
                }
                else {
                    $id = $this->mCurrentRow->request_id;
                    $title = Title::makeTitle( NS_SPECIAL, 'RequestWiki' );
                    return sprintf("&nbsp;<a href=\"%s\">edit</a>&nbsp;",
                        $title->getLocalUrl("action=second&id={$id}"), $id);
                }
                break;

            case "request_timestamp":
                #--- get last editor of page
                $name = $this->mCurrentRow->request_name;
                $dbr = wfGetDB();
                $oRow = $dbr->selectRow(
                    array( "page", "revision" ) /*from*/,
                    array( "rev_timestamp", "rev_user_text" ) /*what*/,
                    array(
                        "lower(page_title)" => strtolower($name),
                        "page_namespace" => 0,
                        "revision.rev_page = page.page_id"
                    ), /*where*/
                    __METHOD__,
                    array("ORDER BY" => "rev_timestamp DESC")
                );
                $sRetval = "<ul style=\"font-size: x-small;\">";
                $sRetval .= "<li>requested: ". wfTimestamp(TS_DB, $value)."</li>";
                $sRetval .= (!empty($oRow->rev_timestamp)) ? "<li>edited: ". wfTimestamp(TS_DB, $oRow->rev_timestamp)."</li>" : "";
                $sRetval .= (!empty($oRow->rev_user_text)) ? "by ".$oRow->rev_user_text: "";
                $sRetval .= "</li></ul>";
                return $sRetval;
                break;

            case "request_name":
                $value = trim($value);

                $id = $this->mCurrentRow->request_id;
                $sLanguage = $this->mCurrentRow->request_language;

                #-- build page from elements
                $oRequestPage = wfRequestTitle($value, $sLanguage);

                $oFormPage = Title::makeTitle( NS_SPECIAL, 'RequestWiki' );
                $sRetval = "<ul>";
                $sRetval .= sprintf("<li><a href=\"%s\">%s:%s</a></li>",
                    $oFormPage->getLocalUrl("action=second&id={$id}"),
                    $sLanguage, $value);
                $sRetval .= sprintf("<li><a href=\"%s\">%s</a></li>",
                    $oRequestPage->getLocalUrl(),
                    $oRequestPage->getText());
                $sRetval .= "<li>".$this->mCurrentRow->request_title."</li>";
                $sRetval .= "</ul>";
                return $sRetval;
                break;

            case "request_category":
                $aCategories = wfRequestCategoryCheck($value, 1 /*wantarray*/);

                $sRetval = "<ul>";
                if (is_array($aCategories)) {
                    foreach( $aCategories as $category ) {
                        $sRetval .= "<li>{$category}</li>";
                    }
                }
                else {
                    $sRetval = "<li>{$value}</li>";
                }
                $sRetval .= "</ul>";
                return $sRetval;
                break;

            default:
                return $value;
        }
    }

    #--- getDefaultSort -----------------------------------------------------
    function getDefaultSort() {
        return 'request_timestamp';
    }

    #--- getQueryInfo -------------------------------------------------------
    function getQueryInfo() {
        $aFields = $this->getFieldNames();
        $aFields["request_title"]++;
        $aFields = array_keys( $aFields );
        return array(
            'tables' => "`wikicities`.city_list_requests",
            'fields' => $aFields,
            'conds' => array("request_status" => 0)
        );
    }

    #--- getForm() -------------------------------------------------------
    function getForm() {
        global $wgRequest, $wgMiserMode;
        $url = $this->getTitle()->escapeLocalURL();
        $search = $wgRequest->getText( 'ilsearch' );
        $s = "<form method=\"get\" action=\"$url\">\n" .
        wfMsgHtml( 'table_pager_limit', $this->getLimitSelect() );
        if ( !$wgMiserMode ) {
            $s .= "<br/>\n" .
            Xml::inputLabel( wfMsg( 'imagelist_search_for' ), 'ilsearch', 'mw-ilsearch', 20, $search );
        }
        $s .= " " . Xml::submitButton( wfMsg( 'table_pager_limit_submit' ) ) ." \n" .
            $this->getHiddenFields( array( 'limit', 'ilsearch' ) ) .
            "</form>\n";
        return $s;
    }
}
?>
