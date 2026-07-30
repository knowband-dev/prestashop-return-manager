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

/**
 * File used for compatibility purpose
 * @type {*|jQuery}
 */
var path = $(location).attr('pathname');
var path_array = path.split('/');
path_array.splice((path_array.length - 2), 2);
var final_path = path_array.join('/');
window.tinyMCEPreInit = {};
window.tinyMCEPreInit.base = final_path+'/js/tiny_mce';
window.tinyMCEPreInit.suffix = '.min';
$('head').append($('<script>').attr('type', 'text/javascript').attr('src', final_path + '/js/tiny_mce/tinymce.min.js'));
