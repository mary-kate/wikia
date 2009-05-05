/*
 * Author: Inez Korczynski, Bartek Lapinski
 */

/*
 * Variables
 */

var VET_panel = null;
var VET_curSourceId = 0;
var VET_lastQuery = new Array;
var VET_asyncTransaction = null;
var VET_curScreen = 'Main';
var VET_prevScreen = null;
var VET_slider = null;
var VET_thumbSize = null;
var VET_orgThumbSize = null;
var VET_gallery = -1;
var VET_box = -1;
var VET_width = null;
var VET_height = null;
var VET_widthChanges = 1;
var VET_refid = null;
var VET_wysiwygStart = 1;
var VET_ratio = 1;
var VET_shownMax = false;
var VET_inGalleryPosition = false;

// macbre: show edit video screen (wysiwyg edit)
function VET_editVideo() {
	YAHOO.util.Dom.setStyle('VideoEmbedMain', 'display', 'none');
	VET_indicator(1, true);

	var callback = {
		success: function(o) {
			var data = FCK.wysiwygData[VET_refid];
			FCK.log('video # ' + VET_refid + ' edit');
			FCK.log(data);

			VET_displayDetails(o.responseText);

			$G('VideoEmbedBack').style.display = 'none';

			setTimeout(function() {
				if(!data.thumb) {
					$G('VideoEmbedThumbOption').click();
				}
				if(data.align && data.align == 'left') {
					$G('VideoEmbedLayoutLeft').click();
				}
				if(data.width) {
					VET_readjustSlider( data.width );
					VET_width = data.width;
					$G( 'VideoEmbedManualWidth' ).value = VET_width;
				}
			}, 200);

			if(data.caption) {
				$G('VideoEmbedCaption').value = data.caption;
			}

			// show width slider
			VET_toggleSizing(true);

			// show alignment row
			$G( 'ImageLayoutRow' ).style.display = '';

			// make replace video link to open in new window / tab
			$G('VideoReplaceLink').getElementsByTagName('a')[0].setAttribute('target', '_blank');
		}
	};

	YAHOO.util.Connect.abort(VET_asyncTransaction);
	var params = [];
	params.push('itemTitle='+FCK.wysiwygData[VET_refid].href);

	VET_asyncTransaction = YAHOO.util.Connect.asyncRequest('GET', wgScriptPath + '/index.php?action=ajax&rs=VET&method=editVideo&' + params.join('&'), callback);
}

// macbre: update video in wysiwyg mode
function VET_doEditVideo() {

	YAHOO.util.Event.preventDefault( YAHOO.util.Event.getEvent() );

	// setup metadata
	var extraData = {};

	extraData.href = $G('VideoEmbedHref').value;
	extraData.width= $G('VideoEmbedManualWidth').value;

	if ($G('VideoEmbedThumbOption').checked) {
		extraData.thumb = 1;
	}
	
	if (extraData.thumb) {
		if( $G('VideoEmbedLayoutLeft').checked ) {
			extraData.align = 'left';
		} else {
			extraData.align = 'right';
		}
	}

	if ($G('VideoEmbedCaption').value) {
		 extraData.caption = $G('VideoEmbedCaption').value;
	}

	// generate wikitext
	var wikitext = '[[' + extraData.href;

	if (extraData.thumb) {
		wikitext += '|thumb';
	}

	if (extraData.align) {
		wikitext += '|' + extraData.align;
	}

	if (extraData.width) {
		wikitext += '|' + extraData.width + 'px';
	}

	if (extraData.caption) {
		wikitext += '|' + extraData.caption;
	}

	wikitext += ']]';

	// update FCK
	FCK.VideoUpdate(VET_refid, wikitext, extraData);

	// close dialog
	VET_close();
}


/*
 * Functions/methods
 */
if(mwCustomEditButtons) {
	if( '-1' == VET_gallery ) {
		mwCustomEditButtons[mwCustomEditButtons.length] = {
			"imageFile": stylepath + '/../extensions/wikia/VideoEmbedTool/images/button_vet2.png',
			"speedTip": vet_imagebutton,
			"tagOpen": "",
			"tagClose": "",
			"sampleText": "",
			"imageId": "mw-editbutton-vet"};
	}
}

if(skin == 'monaco') {
	addOnloadHook(function () {
		if(document.forms.editform) {
			VET_addHandler();
		} else if ( $G( 'VideoEmbedCreate' ) && ( 400 == wgNamespaceNumber ) ) {
			VET_addCreateHandler();
		} else if ( $G( 'VideoEmbedReplace' ) && ( 400 == wgNamespaceNumber ) ) {
			VET_addReplaceHandler();
		}
	});
}

function VET_addCreateHandler() {
	var btn = $G( 'VideoEmbedCreate' );
  	YAHOO.util.Event.addListener(['vetLink', 'vetHelpLink', btn], 'click',  VET_showReplace);
}

function VET_addReplaceHandler() {
	var btn = $G( 'VideoEmbedReplace' );
  	YAHOO.util.Event.addListener(['vetLink', 'vetHelpLink', btn], 'click',  VET_showReplace);
}

function VET_showReplace(e) {
	YAHOO.util.Event.preventDefault(e);
	VET_show(e);
}

function VET_addHandler() {
	var btn = $G('mw-editbutton-vet');
	if(btn == null) {
 		setTimeout('VET_addHandler()', 250);
  		return;
  	}
  	YAHOO.util.Event.addListener(['vetLink', 'vetHelpLink', btn], 'click',  VET_show);
}

function VET_toggleSizing( enable ) {
	if( enable ) {
		$G( 'VideoEmbedThumbOption' ).disabled = false;
		$G( 'ImageWidthRow' ).style.display = '';				
		$G( 'VideoEmbedSizeRow' ).style.display = '';						
	} else {
		$G( 'VideoEmbedThumbOption' ).disabled = true;
		$G( 'ImageWidthRow' ).style.display = 'none';
		$G( 'VideoEmbedSizeRow' ).style.display = 'none';				
	}
}

function VET_manualWidthInput( elem ) {
        var val = parseInt( elem.value );
        if ( isNaN( val ) ) {
		$G( 'VideoEmbedManualWidth' ).value = 300;
		VET_readjustSlider( 300 );
		return false;
        }
	$G( 'VideoEmbedManualWidth' ).value = val;
	VET_readjustSlider( val );
}

function VET_readjustSlider( value ) {
		if ( 500 < value ) { // too big, hide slider
			if ( 'hidden' != $G( 'VideoEmbedSliderThumb' ).style.visibility ) {
				$G( 'VideoEmbedSliderThumb' ).style.visibility = 'hidden';
				VET_slider.setValue( 200, true, true, true );
			}
		} else {
			if ( 'hidden' == $G( 'VideoEmbedSliderThumb' ).style.visibility ) {
				$G( 'VideoEmbedSliderThumb' ).style.visibility = 'visible';
			}

			var fixed_width = value - 98;
			value = Math.max(2, Math.round( ( fixed_width * 200 ) / 400 ) );
			VET_slider.setValue( value, true, true, true );
		}
}

function VET_showPreview(e) {
	YAHOO.util.Dom.setStyle('header_ad', 'display', 'none');

	var html = '';
	html += '<div class="reset" id="VideoEmbedPreview">';
	html += '	<div id="VideoEmbedBorder"></div>';
	html += '	<div id="VideoEmbedPreviewClose"><div></div><a href="#">' + vet_close + '</a></div>';
	html += '	<div id="VideoEmbedPreviewBody">';
	html += '		<div id="VideoEmbedPreviewContent" style="display: none;"></div>';
	html += '	</div>';
	html += '</div>';

	var element = document.createElement('div');
	element.id = 'VET_previewDiv';
	element.style.width = '600px';
	element.style.height = '500px';
	element.innerHTML = html;

	document.body.appendChild(element);

	VET_previewPanel = new YAHOO.widget.Panel('VET_previewDiv', {
		modal: true,
		constraintoviewport: true,
		draggable: false,
		close: false,
		fixedcenter: true,
		underlay: "none",
		visible: false,
		zIndex: 1600
	});
	VET_previewPanel.render();
	VET_previewPanel.show();
	if(VET_refid != null && VET_wysiwygStart == 2) {
		VET_editVideo();
	} else {
		VET_loadMain();
	}

	YAHOO.util.Event.addListener('VideoEmbedPreviewClose', 'click', VET_previewClose);
}

function VET_getCaret() {
	if (typeof FCK == 'undefined') {
		var control = document.getElementById('wpTextbox1');
	} else {
		var control = FCK.EditingArea.Textarea;
	}
	
  var caretPos = 0;
	if(YAHOO.env.ua.ie != 0) { // IE Support
    control.focus();
    var sel = document.selection.createRange();
    var sel2 = sel.duplicate();
    sel2.moveToElementText(control);
    var caretPos = -1;
    while(sel2.inRange(sel)) {
      sel2.moveStart('character');
      caretPos++;
    }
  } else if (control.selectionStart || control.selectionStart == '0') { // Firefox
    caretPos = control.selectionStart;
  }
  return (caretPos);
}

function VET_inGallery() {
	var originalCaretPosition = VET_getCaret();
	if (typeof FCK == 'undefined') {
		var originalText = document.getElementById('wpTextbox1').value;
	} else {
		var originalText = FCK.EditingArea.Textarea.value;
	}
	var lastIndexOfvideogallery = originalText.substring(0, originalCaretPosition).lastIndexOf('<videogallery>');

	if(lastIndexOfvideogallery > 0) {
	  var indexOfvideogallery = originalText.substring(originalCaretPosition).indexOf('</videogallery>');
	  if(indexOfvideogallery > 0) {
	    var textInTag = originalText.substring(lastIndexOfvideogallery + 15, indexOfvideogallery + originalCaretPosition);
	    if(textInTag.indexOf('<') == -1 && textInTag.indexOf('>') == -1) {
		    return textInTag.lastIndexOf("\n") + lastIndexOfvideogallery + 15;
	    }
	  }
	}
	return false;
}

function VET_getFirstFree( gallery, box ) {
	for (i=box; i >= 0; i--) {
		if ( ! $G( 'WikiaVideoGalleryPlaceholder' + gallery + 'x' + i ) ) {
			return i + 1;
		}			
	}
	return box;
}

function VET_show(e, gallery, box) {
	VET_refid = null;
	VET_wysiwygStart = 1;

	if(typeof gallery != "undefined") {

		// if in preview mode, go away
		if ($G ( 'editform' ) && !YAHOO.lang.isNumber(e) ) {
			alert( vet_no_preview );
			return false;
		}
		VET_gallery = gallery;
		VET_box = box;
	}

	if(YAHOO.lang.isNumber(e)) {
		VET_refid = e;
		if(VET_refid == -1) {
			VET_track('open/fromWysiwyg/new');
			// go to main page
		} else {
			VET_track('open/fromWysiwyg/existing');
			if(FCK.wysiwygData[VET_refid].href) {
				// go to details page
				VET_wysiwygStart = 2;
			} else {
				// go to main page
			}
		}
	} else {
		var el = YAHOO.util.Event.getTarget(e);
		if (el.id == 'vetHelpLink') {
			VET_track('open/fromEditTips'); //tracking
		} else if (el.id == 'mw-editbutton-vet') {
			VET_inGalleryPosition = VET_inGallery();
			VET_track('open/fromToolbar'); //tracking
		} else {
			VET_track('open');
		}
	}

	YAHOO.util.Dom.setStyle('header_ad', 'display', 'none');
	if(VET_panel != null) {
		if ( 400 == wgNamespaceNumber ) {
			if( $G( 'VideoEmbedPageWindow' ) ) {
				$G( 'VideoEmbedPageWindow' ).style.visibility = 'hidden';
			}
		}

		VET_panel.show();
		if(VET_refid != null && VET_wysiwygStart == 2) {
			VET_editVideo();
		} else {
			if($G('VideoEmbedUrl')) $G('VideoEmbedUrl').focus();
		}
		return;
	}

	var html = '';
	html += '<div class="reset" id="VideoEmbed">';
	html += '	<div id="VideoEmbedBorder"></div>';
	html += '	<div id="VideoEmbedProgress1" class="VideoEmbedProgress"></div>';
	html += '	<div id="VideoEmbedBack"><div></div><a href="#">' + vet_back + '</a></div>';
	html += '	<div id="VideoEmbedClose"><div></div><a href="#">' + vet_close + '</a></div>';
	html += '	<div id="VideoEmbedBody">';
	html += '		<div id="VideoEmbedError"></div>';
	html += '		<div id="VideoEmbedMain"></div>';
	html += '		<div id="VideoEmbedDetails" style="display: none;"></div>';
	html += '		<div id="VideoEmbedConflict" style="display: none;"></div>';
	html += '		<div id="VideoEmbedSummary" style="display: none;"></div>';
	html += '	</div>';
	html += '</div>';

	var element = document.createElement('div');
	element.id = 'VET_div';
	element.style.width = '812px';
	element.style.height = '587px';
	element.innerHTML = html;

	document.body.appendChild(element);

	VET_panel = new YAHOO.widget.Panel('VET_div', {
		modal: true,
		constraintoviewport: true,
		draggable: false,
		close: false,
		fixedcenter: true,
		underlay: "none",
		visible: false,
		zIndex: 1500
	});
	VET_panel.render();
	VET_panel.show();
	if(VET_refid != null && VET_wysiwygStart == 2) {
		VET_editVideo();
	} else {
		VET_loadMain();
	}

	YAHOO.util.Event.addListener('VideoEmbedBack', 'click', VET_back);
	YAHOO.util.Event.addListener('VideoEmbedClose', 'click', VET_close);

	if ( 400 == wgNamespaceNumber ) {
		if( $G( 'VideoEmbedPageWindow' ) ) {
			$G( 'VideoEmbedPageWindow' ).style.visibility = 'hidden';
		}
	}
}

function VET_loadMain() {
	var callback = {
		success: function(o) {
			$G('VideoEmbedMain').innerHTML = o.responseText;
			VET_indicator(1, false);
			if($G('VideoEmbedUrl') && VET_panel.element.style.visibility == 'visible') $G('VideoEmbedUrl').focus();
		}
	}
	VET_indicator(1, true);
	YAHOO.util.Connect.asyncRequest('GET', wgScriptPath + '/index.php?action=ajax&rs=VET&method=loadMain', callback);
	VET_curSourceId = 0;
}

function VET_recentlyUploaded(param, pagination) {
	if(pagination) {
		VET_track('pagination/' + pagination + '/src-recent'); // tracking
	}
	var callback = {
		success: function(o) {
			$G('VET_results_0').innerHTML = o.responseText;
			VET_indicator(1, false);
		}
	}
	VET_indicator(1, true);
	YAHOO.util.Connect.asyncRequest('GET', wgScriptPath + '/index.php?action=ajax&rs=VET&method=recentlyUploaded&'+param, callback);
}

function VET_sendQuery(query, page, sourceId, pagination) {

	if(pagination) {
		VET_track('pagination/' + pagination + '/src-' + sourceId); // tracking
	}
	var callback = {
		success: function(o) {
			$G('VET_results_' + o.argument[0]).innerHTML = o.responseText;
			VET_indicator(1, false);
		},
		argument: [sourceId]
	}
	VET_lastQuery[sourceId] = query;
	VET_indicator(1, true);
	YAHOO.util.Connect.abort(VET_asyncTransaction)
	VET_asyncTransaction = YAHOO.util.Connect.asyncRequest('GET', wgScriptPath + '/index.php?action=ajax&rs=VET&method=query&' + 'query=' + query + '&page=' + page + '&sourceId=' + sourceId, callback);
}

function VET_indicator(id, show) {
	if(show) {
		if(id == 1) {
			$G('VideoEmbedProgress1').style.display = 'block';
		} else if(id == 2) {
			$G('VideoEmbedProgress2').style.visibility = 'visible';
		}
	} else {
		if(id == 1) {
			$G('VideoEmbedProgress1').style.display = '';
		} else if(id == 2) {
			$G('VideoEmbedProgress2').style.visibility = 'hidden';
		}
	}
}

function VET_chooseImage(sourceId, itemId, itemLink, itemTitle) {
	VET_track('insertVideo/choose/src-' + sourceId); // tracking

	var callback = {
		success: function(o) {
			VET_displayDetails(o.responseText);
		}
	}
	VET_indicator(1, true);
	YAHOO.util.Connect.abort(VET_asyncTransaction)
	VET_asyncTransaction = YAHOO.util.Connect.asyncRequest('GET', wgScriptPath + '/index.php?action=ajax&rs=VET&method=chooseImage&' + 'sourceId=' + sourceId + '&itemId=' + itemId + '&itemLink=' + itemLink + '&itemTitle=' + itemTitle, callback);
}

function VET_preQuery(e) {
	if($G('VideoEmbedUrl').value == '') {
		VET_track('query/undefined'); // tracking
		alert(vet_warn2);
		return false;
	} else {
		var query = $G('VideoEmbedUrl').value;

		if ( !( query.match( 'http://' ) || query.match( 'www.' ) ) ) {
			VET_track('query/url/' + query); // tracking			
			VET_sendQuery(query, 1, VET_curSourceId);
			return false;
		} else {
			VET_track('query/search/' + query); // tracking
			VET_indicator(1, true);
			VET_sendQueryEmbed( query );
			return false;
		}
	}
}

function VET_insertTag( target, tag, position ) {
	// store the scrollbar positions
	if (document.selection  && document.selection.createRange) { // IE/Opera
		var winScroll = target.scrollTop;
		target.value = target.value.substring(0, position)
			+ '\n' + tag + '\n'
			+ target.value.substring( position + 1, target.value.length);
		target.scrollTop = winScroll;
	} else if (target.selectionStart || target.selectionStart == '0') { // Mozilla
		var textScroll = target.scrollTop;			
		target.value = target.value.substring(0, position)
			+ '\n' + tag + '\n'
			+ target.value.substring( position + 1, target.value.length);
		target.scrollTop = textScroll;
	}							
}

function VET_displayDetails(responseText) {
	VET_switchScreen('Details');
	VET_width = null;
	$G('VideoEmbedBack').style.display = 'inline';

	$G('VideoEmbed' + VET_curScreen).innerHTML = responseText;

	if($G('VideoEmbedThumb')) {
		VET_orgThumbSize = null;
		var image = $G('VideoEmbedThumb').firstChild;
		var thumbSize = [image.width, image.height];
		VET_orgThumbSize = null;
	}

                VET_slider = YAHOO.widget.Slider.getHorizSlider('VideoEmbedSlider', 'VideoEmbedSliderThumb', 0, 201);
                VET_slider.initialRound = true;
                VET_slider.getRealValue = function() {
                        return ( Math.max( 2, Math.round( this.getValue() * 2 ) ) + 98 );
                }
                VET_slider.subscribe("change", function(offsetFromStart) {
                        if (VET_slider.initialRound) {
                                $G('VideoEmbedManualWidth').value = 300;
                                VET_slider.initialRound = false;
                        } else {
                                $G('VideoEmbedManualWidth').value = VET_slider.getRealValue();
                        }
                });

                VET_slider.setValue(100, true);

	if ($G( 'VET_error_box' )) {
		alert( $G( 'VET_error_box' ).innerHTML );
	}

	if ( ( '-1' != VET_gallery ) || VET_inGalleryPosition ) {
		$G( 'ImageWidthRow' ).style.display = 'none';
		$G( 'ImageLayoutRow' ).style.display = 'none';
		$G( 'VideoEmbedSizeRow' ).style.display = 'none';
	}

	if ( ( 400 == wgNamespaceNumber ) ) {
		if( $G( 'VideoEmbedName' ) ) {
			$G( 'VideoEmbedName' ).value = wgTitle;
			$G( 'VideoEmbedNameRow' ).style.display = 'none';
		}
	}

	VET_indicator(1, false);
}

function VET_insertFinalVideo(e, type) {
	VET_track('insertVideo/' + type); // tracking

	YAHOO.util.Event.preventDefault(e);

	var params = Array();
	params.push('type='+type);

	if(!$G('VideoEmbedName')) {
		if ($G( 'VideoEmbedOname' ) ) {
			if ('' == $G( 'VideoEmbedOname' ).value)	 {
				alert( vet_warn3 );
				return false;
			}
		} else {
			alert( vet_warn3 );
			return false;
		}
	} else if ('' == $G( 'VideoEmbedName' ).value ) {
		alert( vet_warn3 );
		return false;
	}

	params.push('id='+$G('VideoEmbedId').value);
	params.push('provider='+$G('VideoEmbedProvider').value);

	if( $G( 'VideoEmbedMetadata' ) ) {
		var metadata = Array();
		metadata = $G( 'VideoEmbedMetadata' ).value.split( "," );
		for( var i=0; i < metadata.length; i++ ) {
			params.push( 'metadata' + i  + '=' + metadata[i] );
		}
	}

	if( VET_inGalleryPosition ) {
		params.push( 'mwgalpos=' + VET_inGalleryPosition );
		params.push( 'article='+encodeURIComponent( wgTitle ) );
		params.push( 'ns='+wgNamespaceNumber );
	}

	if( '-1' != VET_gallery ) {
		params.push( 'gallery=' + VET_gallery );
		params.push( 'box=' + VET_box );
		params.push( 'article='+encodeURIComponent( wgTitle ) );
		params.push( 'ns='+wgNamespaceNumber );
		if( VET_refid != null ) {
			params.push( 'fck=true' );
		}
	}

	params.push('oname='+encodeURIComponent( $G('VideoEmbedOname').value ) );

	if(type == 'overwrite') {
		params.push('name='+encodeURIComponent( $G('VideoEmbedExistingName').value ) );
	} else if(type == 'rename') {
		params.push('name='+encodeURIComponent( $G('VideoEmbedRenameName').value ) );
	} else {
		if ($G( 'VideoEmbedName' )) {
			params.push('name='+encodeURIComponent( $G('VideoEmbedName').value) );
		}
	}

	if($G('VideoEmbedThumb')) {
		params.push('size=' + ($G('VideoEmbedThumbOption').checked ? 'thumb' : 'full'));
		params.push( 'width=' + $G( 'VideoEmbedManualWidth' ).value + 'px' );
		if( $G('VideoEmbedLayoutLeft').checked ) {
			params.push( 'layout=left' );
		} else if( $G('VideoEmbedLayoutGallery').checked ) {
			params.push( 'layout=gallery' );
		} else {
			params.push( 'layout=right' );
		}
		params.push('caption=' + encodeURIComponent( $G('VideoEmbedCaption').value ) );
	}

	var callback = {
		success: function(o) {
			var screenType = o.getResponseHeader['X-screen-type'];
			if(typeof screenType == "undefined") {
				screenType = o.getResponseHeader['X-Screen-Type'];
			}
			switch(YAHOO.lang.trim(screenType)) {
				case 'error':
					o.responseText = o.responseText.replace(/<script.*script>/, "" );
					alert(o.responseText);
					break;
				case 'conflict':
					VET_switchScreen('Conflict');
					$G('VideoEmbed' + VET_curScreen).innerHTML = o.responseText;
					break;
				case 'summary':
					VET_switchScreen('Summary');
					$G('VideoEmbedBack').style.display = 'none';
					$G('VideoEmbed' + VET_curScreen).innerHTML = o.responseText;
					if ( !$G( 'VideoEmbedCreate'  ) && !$G( 'VideoEmbedReplace' ) ) {
						if(VET_refid == null) {
							if ('-1' == VET_gallery) {
								if (!VET_inGalleryPosition) { 
									insertTags( $G('VideoEmbedTag').value, '', '');
								} else {
									if (typeof FCK == 'undefined') {
										var txtarea = $G( 'wpTextbox1' );
									} else {
										var txtarea = FCK.EditingArea.Textarea;
									}
									VET_insertTag( txtarea, $G('VideoEmbedTag').value, VET_inGalleryPosition );
								}
							} else {
								// insert into first free "add video" node
								var box_num = VET_getFirstFree( VET_gallery, VET_box );
								if( $G( 'WikiaVideoGalleryPlaceholder' + VET_gallery + 'x' + box_num ) ) {
									var to_update = $G( 'WikiaVideoGalleryPlaceholder' + VET_gallery + 'x' + box_num );
									to_update.parentNode.innerHTML = $G('VideoEmbedCode').innerHTML;			
									YAHOO.util.Connect.asyncRequest('POST', wgServer + wgScript + '?title=' + wgPageName  +'&action=purge');
								}
							}
						} else {
							var wikitag = YAHOO.util.Dom.get('VideoEmbedTag').value;
							var options = {};

							if($G('VideoEmbedThumbOption').checked) {
								options.thumb = 1;
							} else {
								options.thumb = null;
							}
							if($G('VideoEmbedLayoutLeft').checked) {
								options.align = 'left';
							} else {
								options.align = null;
							}
							options.caption = $G('VideoEmbedCaption').value;

							if(VET_refid != -1) {
								FCK.VideoGalleryUpdate( VET_refid, wikitag );
							} else {
								FCK.VideoAdd(wikitag, options);
							}
						}
					} else {
						$G( 'VideoEmbedSuccess' ).style.display = 'none';
						$G( 'VideoEmbedTag' ).style.display = 'none';
						$G( 'VideoEmbedPageSuccess' ).style.display = 'block';
					}
					break;
				case 'existing':
					VET_displayDetails(o.responseText);
					break;
			}
			VET_indicator(1, false);
		}
	}

	VET_indicator(1, true);
	YAHOO.util.Connect.abort(VET_asyncTransaction);
	VET_asyncTransaction = YAHOO.util.Connect.asyncRequest('GET', wgScriptPath + '/index.php?action=ajax&rs=VET&method=insertFinalVideo&' + params.join('&'), callback);
}

function VET_switchScreen(to) {
	VET_prevScreen = VET_curScreen;
	VET_curScreen = to;
	$G('VideoEmbed' + VET_prevScreen).style.display = 'none';
	$G('VideoEmbed' + VET_curScreen).style.display = '';
	if(VET_curScreen == 'Main') {
		$G('VideoEmbedBack').style.display = 'none';
	}
}

function VET_back(e) {
	YAHOO.util.Event.preventDefault(e);
	VET_track('back/' + VET_curScreen);
	if(VET_curScreen == 'Details') {
		VET_switchScreen('Main');
	} else if(VET_curScreen == 'Conflict' && VET_prevScreen == 'Details') {
		VET_switchScreen('Details');
	}
}

function VET_previewClose(e) {
	if(e) {
		YAHOO.util.Event.preventDefault(e);
	}
	VET_track('closePreview/' + VET_curScreen);
	VET_previewPanel.hide();
}

function VET_close(e) {
	if(e) {
		YAHOO.util.Event.preventDefault(e);
	}
	VET_track('close/' + VET_curScreen);
	VET_panel.hide();
	if ( 400 == wgNamespaceNumber ) {
		if( $G( 'VideoEmbedPageWindow' ) ) {
			$G( 'VideoEmbedPageWindow' ).style.visibility = '';
		}
	}
	if(typeof FCK == 'undefined' && $G('wpTextbox1')) $G('wpTextbox1').focus();
	VET_switchScreen('Main');
	VET_loadMain();
	YAHOO.util.Dom.setStyle('header_ad', 'display', 'block');
}

function VET_track(str) {
	YAHOO.Wikia.Tracker.track('VET/' + str);
}

function VET_sendQueryEmbed(query) {
	var callback = {
		success: function(o) {
			var screenType = o.getResponseHeader['X-screen-type'];
			if(typeof screenType == "undefined") {
				screenType = o.getResponseHeader['X-Screen-Type'];
			}

			if( 'error' == YAHOO.lang.trim(screenType) ) {
				alert( o.responseText );		
			} else {
				VET_displayDetails(o.responseText);		
			}

			VET_indicator(1, false);
		}
	}
	VET_indicator(1, true);
	YAHOO.util.Connect.abort(VET_asyncTransaction)
	VET_asyncTransaction = YAHOO.util.Connect.asyncRequest('GET', wgScriptPath + '/index.php?action=ajax&rs=VET&method=insertVideo&' + 'url=' + $G('VideoEmbedUrl').value, callback);
}

