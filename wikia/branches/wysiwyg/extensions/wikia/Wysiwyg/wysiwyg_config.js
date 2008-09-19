FCKConfig.FormatIndentator = '';
FCKConfig.FontFormats = 'p;h1;h2;h3;pre' ;

FCKConfig.ToolbarSets["Default"] = [
	['Source','-','Cut','Copy','Paste','PasteText','Undo','Redo','-','Find','Replace','-','Bold','Italic','Underline','StrikeThrough','OrderedList','UnorderedList','Outdent','Indent','Link','Unlink','Table','Rule','-','FontFormat']
];

FCKConfig.StyleVersion = window.parent.wgStyleVersion;
FCKConfig.EditorAreaCSS = FCKConfig.BasePath + 'css/fck_editorarea.css?' + FCKConfig.StyleVersion;
FCKConfig.EditorAreaStyles = window.parent.stylepath + '/monobook/main.css?' + FCKConfig.StyleVersion;

FCKConfig.BodyId = 'fckEditor';
FCKConfig.Plugins.Add('wikitext');
