/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

function tinySetup(config)
{
	if(!config)
		config = {};

	/*
	 * Abort until TinyMCE global is available (PS 8+ may expose tinymce instead of tinyMCE).
	 * 21-07-2026
	 */
	var editorApi = (typeof tinyMCE !== 'undefined') ? tinyMCE : ((typeof tinymce !== 'undefined') ? tinymce : null);
	if (!editorApi) {
		return;
	}

	if (typeof config['editor_selector'] != 'undefined')
		config['selector'] = '.'+config['editor_selector'];

	var adminBase = (typeof ad !== 'undefined') ? ad : '';
	var langIso = (typeof iso !== 'undefined') ? iso : 'en';

	default_config = {
		selector: ".rte" ,
		plugins : "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor",
		toolbar1 : "code,|,bold,italic,underline,strikethrough,|,alignleft,aligncenter,alignright,alignfull,formatselect,|,blockquote,colorpicker,pasteword,|,bullist,numlist,|,outdent,indent,|,link,unlink,|,cleanup,|,media,image",
		toolbar2: "",
		external_filemanager_path: adminBase+"/filemanager/",
		filemanager_title: "File manager" ,
		external_plugins: { "filemanager" : adminBase+"/filemanager/plugin.min.js"},
		language: langIso,
		skin: "prestashop",
		statusbar: false,
		relative_urls : false,
		convert_urls: false,
		extended_valid_elements : "img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]",
		menu: {
			edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},
			insert: {title: 'Insert', items: 'media image link | pagebreak'},
			view: {title: 'View', items: 'visualaid'},
			format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
			table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
			tools: {title: 'Tools', items: 'code'}
		}

	}

	$.each(default_config, function(index, el)
	{
		if (config[index] === undefined )
			config[index] = el;
	});

	editorApi.init(config);

};
