/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

  config.contentsCss = CKEDITOR.basePath + 'styles.css';
  config.font_names = 'Arial;Verdana';

  config.stylesSet = [
    // Block Styles
      { name: 'Heading 2', element:'h2' },
      { name: 'Heading 3', element:'h3' },
      { name: 'Paragraph', element:'p' },

    // Inline Styles

    // Object Styles

  ];

	config.toolbarGroups = [
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'others' },
		{ name: 'styles' }
	];
   
  // Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Strike,Subscript,Superscript,Anchor,Image,HorizontalRule,Format';

	// Set the most common block elements.
	config.format_tags = 'div;p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';

    // only use autocorrect if site is utf-8
    config.extraPlugins = 'justify,htmlwriter,autocorrect';

};