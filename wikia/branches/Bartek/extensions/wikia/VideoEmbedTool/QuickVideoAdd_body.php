<?php



class QuickVideoAddForm extends SpecialPage
{

	var	$mAction,
		$mPosted;

	/* constructor */
	function __construct () {
		$this->mAction = "";
		parent::__construct( "QuickVideoAdd", "quickvideoadd" );
	}

	public function execute( $subpage ) {
		global $wgOut, $wgRequest;

		$this->mTitle = Title::makeTitle( NS_SPECIAL, 'QuickVideoAdd' );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setPageTitle( "QuickVideoAdd" );
		$wgOut->setArticleRelated( false );

		$this->mAction = $wgRequest->getVal( "action" );
		$this->mPosted = $wgRequest->wasPosted();

		switch( $this->mAction ) {
			case 'submit' :
				if ( $wgRequest->wasPosted() ) {
					$this->mAction = $this->doSubmit();
				}
				break;
			default:
				$this->showForm();

				break;


		}

	}

	public function showForm() {
		global $wgOut;
		$titleObj = Title::makeTitle( NS_SPECIAL, 'RegexBlock' );
		$action = $titleObj->escapeLocalURL( "action=submit" );

		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$oTmpl->set_vars( array(
					"out"		=> 	$wgOut,
					"action"	=>	$action,
				       ) );
		$wgOut->addHTML( $oTmpl->execute("quickform") );
	

	}

	public function doSubmit() {
		global $wgOut, $wgRequest;
		

	}
}

