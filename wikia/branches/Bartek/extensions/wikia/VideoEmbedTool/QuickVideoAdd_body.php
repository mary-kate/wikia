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
		global $wgRequest;

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
		

	}

}

