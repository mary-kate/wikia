<?php
if(!defined('MEDIAWIKI')) {
	exit(1);
}

$wgHooks['ParserBeforeStrip'][] = 'WikiaVideoParserBeforeStrip';
$wgHooks['ArticleFromTitle'][] = 'WikiaVideoArticleFromTitle';


function WikiaVideoParserBeforeStrip($parser, $text, $strip_state) {
	// TODO change this to accomodate more cases ie parser inside, links and all
	// MW parser stuff 
      	$pattern = "@(\[\[Video:)([^\]]*?)].*?\]@si";   	 	 
	$text = preg_replace_callback($pattern, 'WikiaVideoRenderVideo', $text);
	return true;
}

function WikiaVideoRenderVideo( $matches ) {
        global $IP, $wgOut;
        require_once( "$IP/extensions/wikia/VideoEmbedTool/Video.php" );
        $name = $matches[2];
        $params = explode("|",$name);
        $video_name = $params[0];
//        $video =  VideoPage;

        $x = 1;

        $width = 300;
        $align = 'left';
        $caption = '';

        foreach($params as $param){
                if($x > 1){
                        $width_check = preg_match("/px/i", $param );

                        if($width_check){
                                $width = preg_replace("/px/i", "", $param);
                        } else if ($x == 3){
                                $align = $param;
                        } else if ($x == 4) {
                                $caption = $param;
                        }
                }
                $x++;
        }

        if ( is_object( $video ) ) {
                        $output = "<video name=\"{}\" width=\"{$width}\" align=\"{$align}\" caption=\"{$caption}\"></video>";
                        return $output;
        }
        return $matches[0];
}

function WikiaVideoArticleFromTitle( $title, $article ) {
        global $wgUser, $IP;

        require_once( "$IP/extensions/wikia/WikiaVideo/VideoPage.php" );

        if( NS_VIDEO == $title->getNamespace() ) {
                //todo for edit
                $article = new VideoPage( $title );
        }
        return true;
}

