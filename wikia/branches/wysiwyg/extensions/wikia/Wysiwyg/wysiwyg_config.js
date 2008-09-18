FCKConfig.FormatIndentator = '';
FCKConfig.FontFormats = 'p;h1;h2;h3;pre' ;

FCKConfig.ToolbarSets["Default"] = [
	['Source','-','Cut','Copy','Paste','PasteText','Undo','Redo','-','Find','Replace','-','Bold','Italic','Underline','StrikeThrough','OrderedList','UnorderedList','Outdent','Indent','Link','Unlink','Table','-','FontFormat']
];

FCKConfig.EditorAreaCSS = FCKConfig.BasePath + 'css/fck_editorarea.css?' + window.parent.wgStyleVersion;
FCKConfig.EditorAreaStyles = [window.parent.stylepath + '/monobook/main.css?' + window.parent.wgStyleVersion, 'body {padding: 0 5px}'];
FCKConfig.BodyId = 'fckEditor';