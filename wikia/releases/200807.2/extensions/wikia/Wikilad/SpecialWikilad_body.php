<?php
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install this extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/wikia/Wikilad/SpecialWikilad.php" );
EOT;
    exit( 1 );
}

class Wikilad extends SpecialPage {
	/**
	 * Constructor
	 */
	function Wikilad() {
		SpecialPage::SpecialPage( 'Wikilad','wikilad',false );
		$this->includable( true );
		wfLoadExtensionMessages( 'Wikilad' );
	}

	/**
	 * main()
	 */
	function execute( $par = null ) {
			global $wgUser, $wgOut, $wgRequest;
		
		if( $wgUser->isBlocked() ) {
            $wgOut->blockedPage();
            return;
        }

        if( wfReadOnly() ) {
            $wgOut->readOnlyPage();
            return;
        }
       
        if( !$wgUser->isAllowed( 'wikilad' ) ) {
            $this->displayRestrictionError();
            return;
        }
		
		//this is action controller 
		
		$wgOut->setPageTitle( wfMsg('wikilad') );
		
	    $wgOut->setRobotpolicy( 'noindex,nofollow' );
		
	    $oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
				
		switch ($_REQUEST['action']){
		 
		 case "completejob":
		   
		   break; 	
		 
		 case "preview":
		 default:
		 	if( $_REQUEST['complete_job_id'] != '' ){
		 	//complete all article and job	
		 	}
		 		
		  	$out = array( 'err' => '' );
		  	$joblist = $this->getJobList();
		  	$out['joblist'] = $joblist;
		  	
		  	if( $_REQUEST['job_id'] != '' ) {
				$out['job_id_selected'] = $_REQUEST['job_id'];
			  	$articles = $this->getArticleList( $_REQUEST['job_id'] );		  	
		  	}else{
			  	$out['job_id_selected'] = $joblist[0]['job_id'];
			  	$articles = $this->getArticleList( $joblist[0]['job_id'] );
		  	}
		  	
		  	$out['articles'] = $articles['articles'];
		  	$out['users'] = $articles['users'];
		  	$oTmpl->set_vars( array("data" => $out ));
            $wgOut->addHTML( $oTmpl->execute("preview") );
		    break;	
		} 
	}
	
function getJobList() {
    //helper get list of dates for which there are unprocessed articles
	$dbr =  wfGetDB( DB_SLAVE );

	$query = "select a.job_id as job_id, (select STR_TO_DATE(job_title, '%d_%M_%Y') from `wikilad`.jobs j where j.job_id = a.job_id ) as job_name, count(*) as cnt from `wikilad`.articles a  where a.completed=0 group by a.job_id having cnt > 0 order by job_name desc";

	$result = $dbr->query ( $query ) ;
	$dbs = array();

	while( $row = $dbr->fetchObject( $result ) ) {
		$dbs[] = get_object_vars( $row );
	}

	$dbr->freeResult( $result );
	return $dbs;
}

function getArticleList( $job_id = 0 ) {
	//helper get list unprocessed articles for a job
	$dbr =  wfGetDB( DB_SLAVE);

	$query = "select * from `wikilad`.articles where job_id = $job_id and completed = 0 order by article_title asc";

	$result = $dbr->query ( $query ) ;
	$dbs = array();
	
	while( $row = $dbr->fetchObject( $result ) ) {
		$r =	get_object_vars( $row );
		$dbs['articles'][] = $r;
	}

	$dbr->freeResult( $result );
	
	return $dbs;
}

	
}//end class
