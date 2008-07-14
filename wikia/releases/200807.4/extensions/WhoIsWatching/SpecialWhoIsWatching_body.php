<?php

class WhoIsWatching extends SpecialPage
{
    function WhoIsWatching() {
        SpecialPage::SpecialPage( 'WhoIsWatching' );
        self::loadMessages();
    }

    function loadMessages() {
        static $messagesLoaded = false;
        global $wgMessageCache;
        if ( !$messagesLoaded ) {
            $messagesLoaded = true;

            require( dirname( __FILE__ ) . '/SpecialWhoIsWatching.i18n.php' );
            foreach ( $messages as $lang => $langMessages ) {
                $wgMessageCache->addMessages( $langMessages, $lang );
            }
        }
        return true;
    }

    function execute($par) {
        global $wgRequest, $wgOut, $wgCanonicalNamespaceNames;

        $this->setHeaders();
        $wgOut->setPagetitle(wfMsg('whoiswatching'));

        $title = $wgRequest->getVal('page');
        if (!isset($title)) {
            $wgOut->addWikiText(wfMsg('specialwhoiswatchingusage'));
            return;
        }

        $ns = $wgRequest->getVal('ns');
        $ns = str_replace(' ', '_', $ns);
        if ($ns == '')
            $ns = NS_MAIN;
        else {
            foreach ( $wgCanonicalNamespaceNames as $i => $text ) {
                if (preg_match("/$ns/i", $text)) {
                    $ns = $i;
                    break;
                }
            }
        }
        $pageTitle = Title::makeTitle($ns, $title);

        $secret = $wgRequest->getVal("whoiswatching");
        if($secret == sha1("whoiswatching")) {
            $idArray = $wgRequest->getArray('idArray');
            foreach ($idArray as $name => $id) {
                #$wgOut->addWikiText("* Adding name $name userid $id to watchlist\n");
                $u = User::newFromId($id);
                $u->addWatch($pageTitle);
            }
        }
        
        $wiki_title = $pageTitle->getPrefixedText();
        $wiki_path = $pageTitle->getPrefixedDBkey();
        if (preg_match('/^Category/', $wiki_path))
            $wiki_path = ':' . $wiki_path;
        $wgOut->addWikiText("== ".sprintf(wfMsg('specialwhoiswatchingthepage'), "[[$wiki_path|$wiki_title]] =="));

        $dbr = wfGetDB( DB_SLAVE );
        $watchingusers = array();
        $res = $dbr->select( 'watchlist', 'wl_user', array('wl_namespace'=>$ns, 'wl_title'=>$title), __METHOD__);
        for ( $row = $dbr->fetchObject($res); $row; $row = $dbr->fetchObject($res)) {
            $u = User::newFromID($row->wl_user);
            $watchingusers[$row->wl_user] = ":[[:User:" . $u->getName() . "|" . $u->getRealName() . "]]";
        }

        asort($watchingusers);
        foreach ($watchingusers as $id => $link)
            $wgOut->addWikiText($link);
        
        $wgOut->addWikiText("== ".wfMsg('specialwhoiswatchingaddusers')." ==");
        
        $wgOut->addHTML("<form method=\"post\">");
        $wgOut->addHTML("<input type=\"hidden\" value=\"".sha1("whoiswatching")."\" name=\"whoiswatching\" />");
        $wgOut->addHTML("<div style=\"border: thin solid #000000\"><table cellpadding=\"15\" cellspacing=\"0\" border=\"0\">");
        $wgOut->addHTML("<tr><td>");
        $wgOut->addHTML('<select name="idArray[]" size="12" multiple="multiple">');
        $users = array();
        $res = $dbr->select( 'user', 'user_name', '', __METHOD__);
        for ( $row = $dbr->fetchObject($res); $row; $row = $dbr->fetchObject($res)) {
            $u = User::newFromName($row->user_name);
            if (!array_key_exists($u->getID(), $watchingusers))
                if ($u->isAllowed('read') && ($u->getEmail() != ''))
                    $users[strtolower($u->getRealName())] = $u->getID();
        }
        ksort($users);
        foreach ($users as $name => $id)
            $wgOut->addHTML("<option value=\"".$id."\">".$name."</option>");
        $wgOut->addHTML('</select></td><td>');
        $wgOut->addHTML("<input type=\"submit\" value=\"".wfMsg('specialwhoiswatchingaddbtn')."\" />");
        $wgOut->addHTML("</td></tr></table></div></form>");
    }
}
