<?php

$wgHooks['SkinAfterBottomScripts'][]  = 'wfEyeAddJS';

$wgEyeImage = 'http://contractor.wikia-inc.com/images/1/10/Eyeoftzeentch.png';
$wgEyeSize = array(120, 150);

$wgExtensionCredits['other'][] = array(
        'name' => 'TheEye',
        'description' => 'Simple JS game inside wiki article',
	'version' => '0.2',
	'author' => '[http://pl.inside.wikia.com/wiki/User:Macbre Maciej Brencz]',
);

function wfEyeAddJS( $skin, & $bottomScripts ) {

	global $wgEyeImage, $wgEyeSize;

	$JS = <<<EOD
<script type= "text/javascript">/*<![CDATA[*/
function TheEyeSetup() {

	Dom = YAHOO.util.Dom;

	// image to be placed
	eye = {
		w: $wgEyeSize[0],
		h: $wgEyeSize[1]
	};

	// center of user viewport
	center = {x: Dom.getViewportWidth()/2, y: Dom.getViewportHeight()/2 };

	// randomize position of eye by dx and dy pixels
	areaSize = {dx: 100, dy: 100};

	// randomize (add extra 10px padding)
	eye.x = parseInt( (Math.random()-0.5) * areaSize.dx + center.x - eye.w/2 );
	eye.y = parseInt( (Math.random()-0.5) * areaSize.dy + center.y - eye.h/2 );

	YAHOO.log(center, 'info', 'TheEye');
	YAHOO.log(eye, 'info', 'TheEye');

	// add eye
	eyeDiv = document.createElement('div');

	eyeDiv.id = 'eye';
	eyeDiv.style.left = eye.x + 'px';
	eyeDiv.style.top = eye.y + 'px';
	eyeDiv.title = "You've found me";

	document.body.appendChild(eyeDiv);

	eyeDiv.innerHTML = '&nbsp;';
}

if ( wgIsArticle ) {
	YAHOO.util.Event.onDOMReady(TheEyeSetup);
}
/*]]>*/</script>
<style type="text/css">/*<![CDATA[*/
	#eye {
		background-image: url('$wgEyeImage');
		width: $wgEyeSize[0]px;
		height: $wgEyeSize[1]px;
		position: fixed;
		z-index: 15;
		opacity: 0.5;
	}
/*]]>*/</style>
EOD;

	$bottomScripts .= $JS;

        return true;
}

