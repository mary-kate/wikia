var Event = YAHOO.util.Event;
var Dom = YAHOO.util.Dom;
var categories;
//HTML IDs
inputId = 'myInput';
suggestContainerId = 'myContainer';
mainContainerId = 'myAutoComplete';
addCategoryButtonText = 'Add category';

function deleteCategory(e) {
	var catId = e.parentNode.parentNode.getAttribute('catId');
	YAHOO.log('deleting catId = ' + catId);
	YAHOO.log(e.parentNode.parentNode);
	e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode);
	delete categories[catId];
}

function modifyCategory(e) {
	var catId = e.parentNode.parentNode.getAttribute('catId');
	YAHOO.log('catId = ' + catId);
	YAHOO.log(categories[catId]);
	var sortkey = prompt('Provide sortkey', categories[catId].sortkey);
	if (sortkey != null) {
		categories[catId].sortkey = sortkey;
	}
	if (categories[catId].sortkey == '') {
		oldClassName = 'CScontrolSorted';
		newClassName = 'CScontrolSort';
	} else {
		oldClassName = 'CScontrolSort';
		newClassName = 'CScontrolSorted';
	}
	Dom.replaceClass(e, oldClassName , newClassName);
}

function replaceAddToInput(e) {
	e.parentNode.removeChild(e);
	$(inputId).style.display = 'block';
	$(inputId).focus();
}

function addAddCategoryButton() {
	elementA = document.createElement('a');
	elementA.className = 'CSitem';	//setAttribute doesn't work in IE
	elementA.tabindex = '-1';
	elementA.onfocus = 'this.blur()';
	elementA.onclick = function(e) {replaceAddToInput(this); return false;};

	elementSpanOuter = document.createElement('span');
	elementSpanOuter.className = 'CSitemOuterAddCategory';
	elementA.appendChild(elementSpanOuter);

	elementText = document.createTextNode(addCategoryButtonText);
	elementSpanOuter.appendChild(elementText);

	elementSpan = document.createElement('span');
	elementSpan.className = 'CScontrol CScontrolAdd';
	elementSpan.onclick = function(e) {replaceAddToInput(this); return false;};
	elementSpanOuter.appendChild(elementSpan);

	$(mainContainerId).insertBefore(elementA, $(suggestContainerId));
}

function addCategory(category, params, index) {
	YAHOO.log('index = ' + index);
	if (params == undefined) {
		params = {'outerTag': '', 'sortkey': ''};
	}

	if (index == undefined) {
		index = categories.length;
	}

	categories[index] = {'category': category, 'outerTag': params['outerTag'], 'sortkey': params['sortkey']};

	YAHOO.log('addCategory: ' + category);
	elementA = document.createElement('a');
	elementA.className = 'CSitem';	//setAttribute doesn't work in IE
	elementA.tabindex = '-1';
	elementA.onfocus = 'this.blur()';
	elementA.setAttribute('catId', index);

	elementSpanOuter = document.createElement('span');
	elementSpanOuter.className = 'CSitemOuter';
	elementA.appendChild(elementSpanOuter);

	elementText = document.createTextNode(category);
	elementSpanOuter.appendChild(elementText);

	elementSpan = document.createElement('span');
	elementSpan.className = 'CScontrol CScontrolRemove';
	elementSpan.onclick = function(e) {deleteCategory(this); return false;};
	elementSpanOuter.appendChild(elementSpan);

	elementSpan = document.createElement('span');
	elementSpan.className = 'CScontrol ' + (params['sortkey'] == '' ? 'CScontrolSort' : 'CScontrolSorted');
	elementSpan.onclick = function(e) {modifyCategory(this); return false;};
	elementSpanOuter.appendChild(elementSpan);

	$(mainContainerId).insertBefore(elementA, $(inputId));

	$(inputId).value = '';
}

Event.onDOMReady(function() {
	YAHOO.log('onDOMReady');

	addAddCategoryButton();
	if (categories == undefined) {
		categories = new Array();
	} else {
		for(c in categories) {
			YAHOO.log(categories[c].category);
			addCategory(categories[c].category, {'outerTag': categories[c].outerTag, 'sortkey': categories[c].sortkey}, c);
		}
	}

	// So far this extension works only in Firefox and Internet Explorer
//	if(YAHOO.env.ua.ie > 0 || YAHOO.env.ua.gecko > 0) {
//		var submitAutoComplete_callback = {
//			success: function(o) {
//				if(o.responseText !== undefined) {
//					window.location.href=o.responseText;
//				}
//			}
//		};

		var submitAutoComplete = function(comp, resultListItem) {
//			YAHOO.Wikia.Tracker.trackByStr(null, 'search/suggestItem/' + escape(YAHOO.util.Dom.get('search_field').value.replace(/ /g, '_')));
//			sUrl = wgServer + wgScriptPath + '?action=ajax&rs=getSuggestedArticleURL&rsargs=' + encodeURIComponent(Dom.get('search_field').value);
//			var request = YAHOO.util.Connect.asyncRequest('GET', sUrl, submitAutoComplete_callback);
			YAHOO.log('category selected');
			YAHOO.log('event type:' + comp);
			YAHOO.log('selected category:' + resultListItem[2]);
			addCategory(resultListItem[2][0]);
		};

		var inputKeyPress = function(e, oSelf) {
			if(e.keyCode == 13) {
				//TODO: stop AJAX call for AutoComplete
				YAHOO.util.Event.preventDefault(e);
				category = $(inputId).value;
				YAHOO.log('enter pressed, value = ' + category);
				if (category != '') {
					addCategory(category);
				}
			}
		};

		//handle [enter] for non existing categories
		YAHOO.util.Event.addListener(inputId, 'keypress', inputKeyPress);

		// Init datasource
		var oDataSource = new YAHOO.widget.DS_XHR(wgServer + wgScriptPath + '/', ["\n"]);
		oDataSource.responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
		oDataSource.scriptQueryAppend = 'action=ajax&rs=CategorySelectAjaxGetCategories';

		// Init AutoComplete object and assign datasource object to it
		var oAutoComp = new YAHOO.widget.AutoComplete(inputId, suggestContainerId, oDataSource);
		oAutoComp.autoHighlight = false;
		oAutoComp.queryDelay = 1;
		oAutoComp.highlightClassName = 'CSsuggestHover';
		oAutoComp.queryMatchContains = true;
		oAutoComp.itemSelectEvent.subscribe(submitAutoComplete);
//	}
});
