var Event = YAHOO.util.Event;
//var categories = new Array();	//TODO: get this from API for existing article
//HTML IDs
const inputId = 'myInput';
const suggestContainerId = 'myContainer';
const mainContainerId = 'myAutoComplete';

function deleteCategory(e) {
	var catId = e.parentNode.parentNode.getAttribute('catId');
	console.log('deleting catId = ' + catId);
	console.log(e.parentNode.parentNode);
	e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode);
	delete categories[catId];
}

function modifyCategory(e) {
	var catId = e.parentNode.parentNode.getAttribute('catId');
	console.log('catId = ' + catId);
	console.log(categories[catId]);
	var sortkey = prompt('Provide sortkey', categories[catId].sortkey);
	if (sortkey != null) {
		categories[catId].sortkey = sortkey;
	}
}

function addCategory(category, params, index) {
	console.log('index = ' + index);
	if (params == undefined) {
		params = {'outerTag': '', 'sortkey': ''};
	}

	if (index == undefined) {
		index = categories.length;
	}

	categories[index] = {'category': category, 'outerTag': params['outerTag'], 'sortkey': params['sortkey']};

	console.log('addCategory: ' + category);
	elementA = document.createElement('a');
	elementA.setAttribute('class', 'CSitem');
	elementA.setAttribute('href', '#');
	elementA.setAttribute('tabindex', '-1');
	elementA.setAttribute('catId', index);
	elementA.setAttribute('onfocus', 'this.blur()');

	elementSpanOuter = document.createElement('span');	
	elementSpanOuter.setAttribute('class', 'CSitemOuter');
	elementA.appendChild(elementSpanOuter);

	elementText = document.createTextNode(category);
	elementSpanOuter.appendChild(elementText);

	elementSpan = document.createElement('span');
	elementSpan.setAttribute('class', 'CSitemX');
	elementSpan.setAttribute('onclick', 'deleteCategory(this); return false;');
	elementSpanOuter.appendChild(elementSpan);

	elementSpan = document.createElement('span');
	elementSpan.setAttribute('class', 'CSitemM');
	elementSpan.setAttribute('onclick', 'modifyCategory(this); return false;');
	elementSpanOuter.appendChild(elementSpan);

	$(mainContainerId).insertBefore(elementA, $(inputId));

	$(inputId).value = '';
}

Event.onDOMReady(function() {
	console.log('onDOMReady');

	for(c in categories) {
		console.log(categories[c].category);
		addCategory(categories[c].category, {'outerTag': categories[c].outerTag, 'sortkey': categories[c].sortkey}, c);
	}

	// So far this extension works only in Firefox and Internet Explorer
	if(YAHOO.env.ua.ie > 0 || YAHOO.env.ua.gecko > 0) {
		var submitAutoComplete_callback = {
			success: function(o) {
				if(o.responseText !== undefined) {
					window.location.href=o.responseText;
				}
			}
		};

		var submitAutoComplete = function(comp, resultListItem) {
//			YAHOO.Wikia.Tracker.trackByStr(null, 'search/suggestItem/' + escape(YAHOO.util.Dom.get('search_field').value.replace(/ /g, '_')));
//			sUrl = wgServer + wgScriptPath + '?action=ajax&rs=getSuggestedArticleURL&rsargs=' + encodeURIComponent(Dom.get('search_field').value);
//			var request = YAHOO.util.Connect.asyncRequest('GET', sUrl, submitAutoComplete_callback);
			console.log('category selected');
			console.log('event type:' + comp);
			console.log('selected category:' + resultListItem[2]);
			addCategory(resultListItem[2][0]);
		};

		var inputKeyPress = function(e, oSelf) {	
			if(e.keyCode == 13) {
				//TODO: stop AJAX call for AutoComplete
				YAHOO.util.Event.preventDefault(e);
				category = $(inputId).value;
				console.log('enter pressed, value = ' + category);
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
	}
});