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

		
	}

	public static function newFromName( $name ) {
		$title = Title::makeTitleSafe( NS_VIDEO, $name );
		if ( is_object( $title ) ) {
			return new Video( $title );
		} else {
			return NULL;
		}
	}
}

?>
