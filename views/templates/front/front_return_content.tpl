<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0"/>
<script>
	var path = "{$path nofilter}";
	var module_link = "{$module_link nofilter}";
	var rm_is_logged = "{$isLogged nofilter}";
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
<div id="rm_find_order_form" class="rm_row">
    <div id="velocity_returnmanager_result_div" class="rm_order_form_block">         
        <div id="returnmanager_form" class="velsof-box">
            <div class="rm_row rm_bottom_border">
                <span class="rm-heading-large">{l s='Need to return something??' mod='returnmanager'}</span>
                <span class="rm-heading-small">{l s='Just provide your email id and order reference number to know your order.' mod='returnmanager'}</span>
            </div>
            <div class="rm_row rm_top_margin">
                <div id='error_div' class="rm_error_heading" style="display:none;"></div>
            </div>
            <div class="rm_row rm_center_row">
                <div class="rm_field-block">
                    <div class="rm_form_left"><span class="rm_label">{l s='Your Email' mod='returnmanager'}<span class="star_red">*</span></span></div>
                   <div class="rm_form_right">
                       <input type="text" value="" name="rm_customer_email" id="rm_customer_email" placeholder="{l s='example@example.com' mod='returnmanager'}"/>
                   </div>
                </div>
                <div class="rm_field-block">
                   <div class="rm_form_left" ><span class="rm_label">{l s='Order Reference ID' mod='returnmanager'}<span class="star_red">*</span></span></div>
                   <div class="rm_form_right" id='email_field'>
                       <input type="text" value="" name="rm_reference_id" id="rm_reference_id" placeholder="{l s='Enter Reference ID' mod='returnmanager'}"/>
                   </div>
                </div>
                <div class="rm_field-block">
                   <div class="rm_form_left" style="display:inline-block;">&nbsp;</div>
                   <div class="rm_form_right" id='email_field'>
                       <button class="velsof-button" id='find_order' onclick="validateReturn();"><span>{l s='Find Order' mod='returnmanager'}</span></button>
                   </div>
                </div>
            </div>        
        </div>
    </div>
</div>
<div id='rm_single_order_detail_container' class="rm_row"></div>
<div id="kb_rm_pop_up"></div>

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
* @category  PrestaShop Module
*
* Description
*
* Returns Manager Form
*}
