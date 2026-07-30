{**
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
 *
 *}
 
<script>
	var path = "{$path|escape:'quotes':'UTF-8'}";
	var is_order_history_page = "1";
	var module_link = "{$module_link|escape:'quotes':'UTF-8'}";
	var path_fold = '{$kb_admin_link nofilter}';
	var rm_is_logged = "{$isLogged|escape:'htmlall':'UTF-8'}";
	var requiredField = "{l s='Required field' mod='returnmanager'}";
	var invalidEmailId = "{l s='Invalid Email Address' mod='returnmanager'}";
	var orderNotFound = "{l s='No order found for provided information.' mod='returnmanager'}";
	var orderedProductNotFound = "{l s='The requested product not found.' mod='returnmanager'}";
	var rm_ajax_failed = "{l s='Technical Error Occurred' mod='returnmanager'}";
	var rm_return_type_required = "{l s='Please select return type' mod='returnmanager'}";
        
	var rm_reason_required = "{l s='Please select reason' mod='returnmanager'}";
	var rm_toc_checked = "{l s='Please agree to terms & conditions' mod='returnmanager'}";
        var file_type_error = "{l s='Invalid Format, Please select only image and zip file' mod='returnmanager'}";
        var image_size_error = "{l s='File size should not greater than 4 MB' mod='returnmanager'}";
</script>

