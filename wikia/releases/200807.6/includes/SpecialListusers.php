<?php

# Copyright (C) 2004 Brion Vibber, lcrocker, Tim Starling,
# Domas Mituzas, Ashar Voultoiz, Jens Frank, Zhengzhu.
#
# © 2006 Rob Church <robchur@gmail.com>
#
# http://www.mediawiki.org/
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
# http://www.gnu.org/copyleft/gpl.html
/**
 *
 * @addtogroup SpecialPage
 */

/**
 * This class is used to get a list of user. The ones with specials
 * rights (sysop, bureaucrat, developer) will have them displayed
 * next to their names.
 *
 * @addtogroup SpecialPage
 */

class UsersPager extends AlphabeticPager {

	function __construct($group=null) {
		global $wgRequest;
		$this->requestedGroup = $group != "" ? $group : $wgRequest->getVal( 'group' );
		$un = $wgRequest->getText( 'username' );
		$this->requestedUser = '';
		if ( $un != '' ) {
			$username = Title::makeTitleSafe( NS_USER, $un );
			if( ! is_null( $username ) ) {
				$this->requestedUser = $username->getText();
			}
		}
		//Filter setup begin, ticet #699
	        global $wgUser, $wgMemc;
	        $this->mShowAll = false;
	        $this->mShowLink = false;
	        $this->mLocalUsers =  false;
	        
	        
            if ( in_array('sysop', $wgUser->getGroups()) || in_array('staff', $wgUser->getGroups()) ){
	                $this->mShowLink = true;
			$this->mShowAll = $wgRequest->getVal( 'showall' ) ? true : false;
            }
            if ( $this->requestedGroup != '' || $this->requestedUser!='' ){
	                $this->mShowLink = false;
			$this->mShowAll = true;
            }
		$key = wfMemcKey( 'LocalUsers' );
		if ( $wgMemc->get( $key ) !== NULL ){
		        $this->mLocalUsers = $wgMemc->get( $key );
		}else{
			$db =& wfGetDB(DB_SLAVE);
			$res = $db->query("show tables like 'local_users';");
			if ($db->fetchObject($res)){
				$wgMemc->set( $key, true);
				$this->mLocalUsers = true;
			}else{
				$wgMemc->set( $key, false, 24*3600);
				$this->mLocalUsers = false;       
			}
		}
	        /*if ($this->mLocalUsers){
                	$this->mShowLink = false;
			$this->mShowAll = true;
	        }*/
		//Filter setup end, ticet #699
		
		parent::__construct();
	}


	function getIndexField() {
		return 'user_name';
	}

	function getQueryInfo() {
		$conds=array();
		// don't show hidden names
		$conds[]='ipb_deleted IS NULL OR ipb_deleted = 0';
		if ($this->requestedGroup != "") {
			$conds['ug_group'] = $this->requestedGroup;
		}
		if ($this->requestedUser != "") {
			$conds[] = 'user_name >= ' . wfGetDB()->addQuotes( $this->requestedUser );
		}
		if (!$this->mLocalUsers){
			list ($user,$user_groups,$revision,$ipblocks) = wfGetDB()->tableNamesN('user','user_groups','revision','ipblocks');
		
			$ret = array(
				'tables' => " $user LEFT JOIN $user_groups ON user_id=ug_user LEFT JOIN $ipblocks ON user_id=ipb_user AND ipb_auto=0 ",
				'fields' => array('user_name',
					'MAX(user_id) AS user_id',
					'COUNT(ug_group) AS numgroups', 
					'MAX(ug_group) AS singlegroup'),
				'options' => array('GROUP BY' => 'user_name'), 
				'conds' => $conds
			);
			if ( !$this->mShowAll ){
		       		$ret ['tables'] .= " JOIN (select rev_user, count(*) as cnt from $revision group by rev_user having cnt>5) as tmp ON user_id=rev_user "; 
			}
		}else {
			list ($local_users,$user_groups,$ipblocks) = wfGetDB()->tableNamesN('local_users','user_groups','ipblocks');
			if (!$this->mShowAll){
			        $conds[] = 'rev_cnt > 5 ';
			}

			$ret = array(
			'tables' => " $local_users LEFT JOIN $user_groups ON user_id=ug_user LEFT JOIN $ipblocks ON user_id=ipb_user AND ipb_auto=0 ",
			'fields' => array('user_name',
				'MAX(user_id) AS user_id',
				'MAX(numgroups) AS numgroups', 
				'MAX(singlegroup) AS singlegroup'),
			'options' => array('GROUP BY' => 'user_name'),
			'conds' => $conds
			);		         
		}
		
		return $ret;

	}

	function formatRow( $row ) {
		$userPage = Title::makeTitle( NS_USER, $row->user_name );
		$name = $this->getSkin()->makeLinkObj( $userPage, htmlspecialchars( $userPage->getText() ) );

		if( $row->numgroups > 1 || ( $this->requestedGroup && $row->numgroups == 1 ) ) {
			$list = array();
			foreach( self::getGroups( $row->user_id ) as $group )
				$list[] = self::buildGroupLink( $group );
			$groups = implode( ', ', $list );
		} elseif( $row->numgroups == 1 ) {
			$groups = self::buildGroupLink( $row->singlegroup );
		} else {
			$groups = '';
		}

		return '<li>' . wfSpecialList( $name, $groups ) . '</li>';
	}

	function getBody() {
		if (!$this->mQueryDone) {
			$this->doQuery();
		}
		$batch = new LinkBatch;

		$this->mResult->rewind();

		while ( $row = $this->mResult->fetchObject() ) {
			$batch->addObj( Title::makeTitleSafe( NS_USER, $row->user_name ) );
		}
		$batch->execute();
		$this->mResult->rewind();
		return parent::getBody();
	}

	function getPageHeader( ) {
		global $wgScript, $wgRequest;
		$self = $this->getTitle();

		# Form tag
		$out  = Xml::openElement( 'form', array( 'method' => 'get', 'action' => $wgScript ) ) .
			'<fieldset>' .
			Xml::element( 'legend', array(), wfMsg( 'listusers' ) );
		$out .= Xml::hidden( 'title', $self->getPrefixedDbKey() );

		# Username field
		$out .= Xml::label( wfMsg( 'listusersfrom' ), 'offset' ) . ' ' .
			Xml::input( 'username', 20, $this->requestedUser, array( 'id' => 'offset' ) ) . ' ';

		# Group drop-down list
		$out .= Xml::label( wfMsg( 'group' ), 'group' ) . ' ' .
			Xml::openElement('select',  array( 'name' => 'group', 'id' => 'group' ) ) .
			Xml::option( wfMsg( 'group-all' ), '' );
		foreach( User::getAllGroups() as $group )
			$out .= Xml::option( User::getGroupName( $group ), $group, $group == $this->requestedGroup );
		$out .= Xml::closeElement( 'select' ) . ' ';

		# Submit button and form bottom
		if( $this->mLimit )
			$out .= Xml::hidden( 'limit', $this->mLimit );
		$out .= Xml::submitButton( wfMsg( 'allpagessubmit' ) ) .
			'</fieldset>' .
			Xml::closeElement( 'form' );
		
		if ($this->mShowLink){
			global $wgTitle;
		        if ($this->mShowAll){
		                $out .= "&nbsp<a href='{$wgTitle->getLocalURL()}?limit={$this->mLimit}&offset={$this->mOffset}&showall=0'>" . wfMsg('hidesome') . "</a><br />\n";
		        }else{
		                $out .= "&nbsp<a href='{$wgTitle->getLocalURL()}?limit={$this->mLimit}&offset={$this->mOffset}&showall=1'>" . wfMsg('showall') . "</a><br />\n";
		        }
		}

		return $out;
	}

	/**
	 * Preserve group and username offset parameters when paging
	 * @return array
	 */
	function getDefaultQuery() {
		$query = parent::getDefaultQuery();
		if( $this->requestedGroup != '' )
			$query['group'] = $this->requestedGroup;
		if( $this->requestedUser != '' )
			$query['username'] = $this->requestedUser;
		return $query;
	}

	/**
	 * Get a list of groups the specified user belongs to
	 *
	 * @param int $uid
	 * @return array
	 */
	private static function getGroups( $uid ) {
		$dbr = wfGetDB( DB_SLAVE );
		$groups = array();
		$res = $dbr->select( 'user_groups', 'ug_group', array( 'ug_user' => $uid ), __METHOD__ );
		if( $res && $dbr->numRows( $res ) > 0 ) {
			while( $row = $dbr->fetchObject( $res ) )
				$groups[] = $row->ug_group;
			$dbr->freeResult( $res );
		}
		return $groups;
	}

	/**
	 * Format a link to a group description page
	 *
	 * @param string $group
	 * @return string
	 */
	private static function buildGroupLink( $group ) {
		static $cache = array();
		if( !isset( $cache[$group] ) )
			$cache[$group] = User::makeGroupLinkHtml( $group, User::getGroupMember( $group ) );
		return $cache[$group];
	}
}

/**
 * constructor
 * $par string (optional) A group to list users from
 */
function wfSpecialListusers( $par = null ) {
	global $wgRequest, $wgOut, $wgMessageCache;

	$wgMessageCache->loadAllMessages();

	$up = new UsersPager($par);

	# getBody() first to check, if empty
	$usersbody = $up->getBody();
	$s = $up->getPageHeader();
	if( $usersbody ) {
		$s .=	$up->getNavigationBar();
		$s .=	'<ul>' . $usersbody . '</ul>';
		$s .=	$up->getNavigationBar() ;
	} else {
		$s .=	'<p>' . wfMsgHTML('listusers-noresult') . '</p>';
	};

	$wgOut->addHTML( $s );
}


