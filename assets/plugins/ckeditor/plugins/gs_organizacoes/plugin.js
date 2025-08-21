CKEDITOR.plugins.add('gs_organizacoes', {
    requires : [ 'richcombo' ],
    init : function(editor) {
	$.ajax({
	    url : "/ajax/editorTags",
	    data : {
		tbl : 'organizacoes'
	    },
	    type : "POST",
	    dataType : 'json',
	    async: false,
	    success : function(tags, status, jqXHR) {
		for (var combo in tags) {
		    editor.ui.addRichCombo(combo, {
			tags : tags[combo],
			label : combo,
			title : combo,
			voiceLabel : combo,
			className : 'cke_format',
			multiSelect : false,
			modes: { wysiwyg: 1, source: 1 },
			panel : {
			    css : [ editor.config.contentsCss, CKEDITOR.skin.getPath('editor') ],
			    voiceLabel : editor.lang.panelVoiceLabel
			},
			init : function() {
			    this.startGroup(this.title);
			    for ( var i in this.tags) {
				this.add(i, this.tags[i][0], this.tags[i][1]);
			    }
			},
			onClick : function(value) {
			    editor.focus();
			    editor.fire('saveSnapshot');
			    editor.insertHtml(value);
			    editor.fire('saveSnapshot');
			}
		    });
		}
	    }
	});
    }
});