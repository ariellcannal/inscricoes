/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
    //config.skin = 'moono';
    config.skin = 'bootstrapck';
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for a single toolbar row.
    config.toolbarGroups = [
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list'] },
		{ name: 'tools' }
	];
    
    config.enterMode = CKEDITOR.ENTER_P;        
    config.shiftEnterMode = CKEDITOR.ENTER_BR;
    config.height = '200px';

	// The default plugins included in the basic setup define some buttons that
	// are not needed in a basic editor. They are removed here.
	config.removeButtons = 'Smiley,Image,Save,Flash,PageBreak,Iframe,Anchor,Strike,Subscript,Superscript';

	// Dialog windows are also simplified.
	config.removeDialogTabs = 'link:advanced';
	config.removePlugins = 'elementspath';
};
CKEDITOR.config.allowedContent=true;