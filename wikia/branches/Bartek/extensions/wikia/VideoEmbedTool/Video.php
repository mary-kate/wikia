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

	public function getHeight( $width ) {
		$this->load();
		return $width * $this->ratio;
	}


	public function getName() {

	}

	public function getProvider() {
		$this->load();
		return $this->provider;
	}

	public function loadFromPage( $data ) {
		$this->id = $data['id'];
		$this->url = $data['url'];
		$this->provider = $data['provider'];		
	}

	public function getEmbedCode() {
		$embed = "";
		switch( $this->provider ) {
			case "metacafe":
				$embed = "<embed src=\"{$this->url}\" width=\"400\" height=\"345\" wmode=\"transparent\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\"> </embed>";
				break;			
			default: break;
		}
		return $embed;
	}

	public function extractProvider( $url ) {
	
	}

	public function extractID( $url ) {

		// look for provider
		$this->extractProvider();
	}
}

?>
