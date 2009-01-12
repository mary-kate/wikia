<?php
if(!defined('MEDIAWIKI')) {
	exit(1);
}

$wgHooks['ArticleSave'][] = 'WikiaVideoArticleSave';
$wgHooks['ParserBeforeStrip'][] = 'WikiaVideoParserBeforeStrip';
$wgHooks['ArticleFromTitle'][] = 'WikiaVideoArticleFromTitle';


function WikiaVideoParserBeforeStrip($parser, $text, $strip_state) {
	// TODO
	return true;
}

function WikiaVideoRenderVideo( $matches ) {
        global $IP, $wgOut;
        require_once( "$IP/extensions/wikia/VideoEmbedTool/Video.php" );
        $name = $matches[2];
        $params = explode("|",$name);
        $video_name = $params[0];
        $video =  Video::newFromName( $video_name );

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
                        $output = "<video name=\"{$video->getName()}\" width=\"{$width}\" align=\"{$align}\" caption=\"{$caption}\"></video>";
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

function WikiaVideoArticleSave( $article, $user, $text, $summary) {
        if (NS_VIDEO == $article->mTitle->getNamespace()) {
                $text = $article->dataline . $text;
        }
        return true;
}

