<?php

// use the same namespace as in old NY extension
define( 'NS_VIDEO', 400 );

class Video {
	var 	$id,
		$provider,
		$exists,
		$url,
		$title,
		$width,
		$ratio;			

	public function __construct( $title ) {
		$this->width = 300;
		
		
	}

	public static function newFromName( $name ) {
		$title = Title::makeTitleSafe( NS_VIDEO, $name );
		if ( is_object( $title ) ) {
			return new Video( $title );
		} else {
			return NULL;
		}
	}

	public function addVideo( $id, $provider, $desc ) {
			

	}

	public function load() {

	}

	public function getID() {
		$this->load();
		return $this->id;
	}

	public function getName() {

	}

	public function getProvider() {
		$this->load();
		return $this->provider;
	}

	public function getEmbedCode() {

	}

	public function extractProvider( $url ) {
	
	}

	public function extractID( $url ) {

		// look for provider
		$this->extractProvider();
	}
}

?>
