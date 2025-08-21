CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'links', groups: [ 'links' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		'/',
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'Source,Save,NewPage,Preview,Print,Templates,Find,Replace,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Superscript,Subscript,CopyFormatting,Blockquote,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Flash,Smiley,Iframe,Styles,Format,ShowBlocks,About';
	
		config.extraPlugins = 'popup';
		config.extraPlugins = 'filebrowser';
		config.extraPlugins = 'outliers_contratos';
		config.filebrowserImageUploadUrl = '/ajax/images/?type=Images';
	
};