<script>
    var path_fold = '{$kb_admin_link nofilter}';
</script>
<div id="rm_return_form_popup" class="white_content">
    <div class="rm_innerBox">
        <a href="javascript:void(0)" class="rm_popup_close_icon" onclick="handleReturnBlockRefresh()">&nbsp;</a>
        <div id="rm_row">
            <div id="rm_popup_request_form" class="" style='{*max-height: 500px; overflow: scroll;*}'>
                <div class="rm_row_form_title">
                    <div class="rm_pop_heading"><img
                            src="{$img_path|escape:'htmlall':'UTF-8'}return.png" />{l s='Easy Return' mod='returnmanager'}
                    </div>
                    <span
                        class="rm-heading-small rm_form_info_text">{l s='Please fill the below form to make request for return.' mod='returnmanager'}</span>
                </div>
                <div class="" style='text-align: center;'>
                </div>
                {foreach $products_info as $product}
                    <div class="returnList">
                        <div class="kb_field_row">
                            <div class="rm_row_form" id="rm_multi_header">
                                <span style='text-transform: none;'
                                    class="orderNumber">{l s='Order' mod='returnmanager'}:&nbsp;<b>{$odr_reference nofilter}</b></span>
                                <div class="rm_popup_pro_img">
                                    {if isset($product['product']['img_path'])}
                                        <img src="{$product['product']['img_path'] nofilter}"
                                            onerror="this.src='{$path nofilter}returnmanager/views/img/No-image.jpg'">
                                    {else}
                                        <img src="{Context::getContext()->link->getImageLink($product['product']['link_rewrite'], $product['product']['id_image'], 'small_default') nofilter}"
                                            onerror="this.src='{$path nofilter}returnmanager/views/img/No-image.jpg'">
                                    {/if}
                                </div>
                            </div>
                            <div class="rm_row_form companyAddress">
                                <span class="rm_popup_pro_name uppercase"
                                    style="margin-bottom:8px;">{l s='Return Address' mod='returnmanager'}</span>
                                <span class="rm_popup_addr" name="rm_popup_address"
                                    id="rm_popup_address_{$product['product']['id_order_detail'] nofilter}">
                                    {$product['product']['shipping_address'] nofilter}{* variable contains html content, can not escape *}
                                </span>
                            </div>
                        </div>

                        <div class="kb_field_row">
                            <div class="rm_row_form">
                                <div class="rm_form_left_half">
                                    <span class="rm_form_label">{l s='Return Type' mod='returnmanager'}:</span>
                                </div>
                                <div class="rm_form_right_half">
                                    <div class="rm_form_control_block">
                                        <select name="rm_return_type_{$product['product']['id_order_detail'] nofilter}"
                                            class="rm_form_control"
                                            onchange="displayReturnNoteOnMultipleForm(this, {$product['product']['id_order_detail'] nofilter})">
                                            <option value="0">{l s='Select Return Type' mod='returnmanager'}</option>
                                            {foreach $product['product']['return_types'] as $type}
                                                <option value="{$type['value'] nofilter}">{$type['text'] nofilter}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div id="rm_return_type_note_{$product['product']['id_order_detail'] nofilter}"
                                        class="rm_form_note kb_rm_form_note">
                                        {foreach $product['product']['return_types'] as $type}
                                            <p id="rm_return_type_note_{$type['value'] nofilter}" class="vss_font-size-13">
                                                {$type['note'] nofilter}</p>
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                            {if count($product['product']['reasons']) > 0}
                                <div class="rm_row_form">
                                    <div class="rm_form_left_half">
                                        <span class="rm_form_label">{l s='Reason' mod='returnmanager'}:</span>
                                    </div>
                                    <div class="rm_form_right_half">
                                        <div class="rm_form_control_block">
                                            <select name="rm_return_reason_{$product['product']['id_order_detail'] nofilter}"
                                                class="rm_form_control"
                                                onchange="displayReasonNoteOnMultipleForm(this, {$product['product']['id_order_detail'] nofilter})">
                                                <option value="0">{l s='Select Reason' mod='returnmanager'}</option>
                                                {foreach $product['product']['reasons'] as $reason}
                                                    <option value="{$reason['reason_id'] nofilter}">{$reason['text'] nofilter}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div id="rm_reason_type_note_{$product['product']['id_order_detail'] nofilter}"
                                            class="rm_form_note kb_rm_form_note">
                                            {foreach $product['product']['reasons'] as $reason}
                                                <p id="rm_reason_type_note_{$reason['reason_id'] nofilter}" class="vss_font-size-13">
                                                    {$reason['shipping_paid_by'] nofilter}</p>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            {/if}

                        </div>
                        <div class="kb_field_row">
                            {if isset($product['enable_product_selection']) && $product['enable_product_selection'] == 1}
                                <div class="rm_row_form" id="kb_product_choose_block_{$product['product']['id_order_detail'] nofilter}"
                                    style="display:none;">
                                    <div class="rm_form_left_half">
                                        <span class="rm_form_label">{l s='Select Products' mod='returnmanager'}:</span>
                                    </div>
                                    <div class="rm_form_right_half">
                                        <div class="rm_form_control_block">
                                            <input type="text" name="kb_Product_specific_products"
                                                id="kb_Product_specific_products" value="" class="ac_input pvt_accesssetting"
                                                autocomplete="off">
                                            <input type="hidden" name="rm_return_product_{$product['product']['id_order_detail'] nofilter}" kb_value="{$product['product']['id_order_detail'] nofilter}">
                                            <img id="rm_kbproduct_loader"
                                                src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif"
                                                style="display: none;float: right;margin-top: 8px">
                                        </div>
                                    </div>
                                </div>
                                <div class="rm_row_form"
                                    id="kb_product_attribute_choose_block_{$product['product']['id_order_detail'] nofilter}"
                                    style="display:none;">
                                    <div class="rm_form_left_half">
                                        <span class="rm_form_label">{l s='Select Attribute' mod='returnmanager'}:</span>
                                    </div>
                                    <div class="rm_form_right_half">
                                        <div class="rm_form_control_block"
                                            id="rm_return_product_attribute_{$product['product']['id_order_detail'] nofilter}">
                                            {*<select name="rm_return_product_attribute" class="chosen rm_form_control">
                                            <option value="0">{l s='Select Product Attribute' mod='returnmanager'}</option>
                                            </select>*}
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
                        <div class="kb_field_row">
                            {if count($product['address_list']) > 0 && $product['enable_address'] == 1}
                                <div class="rm_row_form">
                                    <div class="rm_form_left_half">
                                        <span class="rm_form_label">{l s='Select Return Address' mod='returnmanager'}:</span>
                                    </div>
                                    <div class="rm_form_right_half">
                                        <div class="rm_form_control_block">
                                            <select name="rm_return_address_{$product['product']['id_order_detail'] nofilter}"
                                                id="rm_return_address_{$product['product']['id_order_detail'] nofilter}"
                                                class="rm_form_control"
                                                onchange="displayReturnAddressOnMultipleForm({$product['product']['id_order_detail'] nofilter})">
                                                <option value="0">{l s='Default Address' mod='returnmanager'}</option>
                                                {foreach $product['address_list'] as $address}
                                                    <option value="{$address['id_address']|escape:'htmlall':'UTF-8'}">
                                                        {$address['title']|escape:'htmlall':'UTF-8'}</option>
                                                {/foreach}
                                            </select>
                                            {foreach $product['address_list'] as $address}
                                                <input type="hidden"
                                                    id="full_addr_{$address['id_address']|escape:'htmlall':'UTF-8'}_{$product['product']['id_order_detail'] nofilter}"
                                                    name="full_addr_{$address['id_address']|escape:'htmlall':'UTF-8'}"
                                                    value="{$product['full_addr'][$address['id_address']]|escape:'htmlall':'UTF-8'}" />
                                            {/foreach}
                                            <input type="hidden" id="default_addr_{$product['product']['id_order_detail'] nofilter}"
                                                name="default_addr"
                                                value="{$product['product']['shipping_address'] nofilter}" />{* variable contains html content can not escape *}
                                        </div>

                                    </div>
                                </div>
                            {/if}
                            <div class="rm_row_form">
                                <div class="rm_form_left_half">
                                    <span class="rm_form_label">{l s='Quantity' mod='returnmanager'}:</span>
                                </div>
                                <div class="rm_form_right_half">
                                    <div class="rm_form_control_block">
                                        <select name="rm_return_quantity_{$product['product']['id_order_detail'] nofilter}"
                                            class="rm_form_control" style="width:50px; padding:10px">
                                                <option value="{$product['product']['product_quantity'] nofilter}">{$product['product']['product_quantity'] nofilter}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Start - Code to insert custom fields in cart block -->
                        {if isset($enable_custom_field) && $enable_custom_field == 1 && isset($array_fields) && !empty($array_fields)}
                            <div class="rm_row_form_title">
                                <span class="rm-heading-small" style="text-align:center;">{$custom_field_block_title nofilter}</span>
                            </div>
                            <div class="kb_field_row">
                                {foreach from=$array_fields item=field}
                                    <div class="rm_row_form">
                                        {if $field['type'] eq "textbox"}
                                            <div class="rm_form_left_half">
                                                <span class="cursor_help rm_form_label"
                                                    title="{$field['field_help_text'] nofilter}">{$field['field_label'] nofilter}{if $field['required'] eq "1"}<span
                                                        style="display:inline;" class="rm-required">*</span>{/if}</span>
                                            </div>
                                            <div class="rm_form_right_half">
                                                <div class="rm_form_control_block">
                                                    <input type="text" class="rm_form_control"
                                                        name="custom_fields[field_{$field['id_velsof_rm_custom_fields'] nofilter}][{$product['product']['id_order_detail'] nofilter}]"
                                                        value="{$field['default_value'] nofilter}">
                                                    <span
                                                        id="error_field_{$field['id_velsof_rm_custom_fields'] nofilter}_{$product['product']['id_order_detail'] nofilter}"
                                                        class="errorsmall_custom kb_hidden_custom"></span>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $field['type'] eq "textarea"}
                                            <div class="rm_form_left_half">
                                                <span class="cursor_help rm_form_label"
                                                    title="{$field['field_help_text'] nofilter}">{$field['field_label'] nofilter}{if $field['required'] eq "1"}<span
                                                        style="display:inline;" class="rm-required">*</span>{/if}</span>
                                            </div>
                                            <div class="rm_form_right_half">
                                                <div class="rm_form_control_block">
                                                    <textarea
                                                        name="custom_fields[field_{$field['id_velsof_rm_custom_fields'] nofilter}][{$product['product']['id_order_detail'] nofilter}]"
                                                        class="rm_form_control">{$field['default_value'] nofilter}</textarea>
                                                    <span
                                                        id="error_field_{$field['id_velsof_rm_custom_fields'] nofilter}_{$product['product']['id_order_detail'] nofilter}"
                                                        class="errorsmall_custom kb_hidden_custom"></span>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $field['type'] eq "selectbox"}
                                            <div class="rm_form_left_half">
                                                <span class="cursor_help rm_form_label"
                                                    title="{$field['field_help_text'] nofilter}">{$field['field_label'] nofilter}{if $field['required'] eq "1"}<span
                                                        style="display:inline;" class="rm-required">*</span>{/if}</span>
                                            </div>
                                            <div class="rm_form_right_half">
                                                <div class="rm_form_control_block">
                                                    <select
                                                        name="custom_fields[field_{$field['id_velsof_rm_custom_fields'] nofilter}][{$product['product']['id_order_detail'] nofilter}]"
                                                        class="rm_form_control">
                                                        {foreach from=$field['options'] item=field_options}
                                                            <option
                                                                {if $field_options['default_value'] eq $field_options['option_value']}selected{/if}
                                                                value="{$field_options['option_value'] nofilter}">{$field_options['option_label'] nofilter}
                                                            </option>
                                                        {/foreach}
                                                    </select>
                                                    <span
                                                        id="error_field_{$field['id_velsof_rm_custom_fields'] nofilter}_{$product['product']['id_order_detail'] nofilter}"
                                                        class="errorsmall_custom kb_hidden_custom"></span>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $field['type'] eq "radio"}
                                            <div class="rm_form_left_half">
                                                <span class="cursor_help rm_form_label"
                                                    title="{$field['field_help_text'] nofilter}">{$field['field_label'] nofilter}{if $field['required'] eq "1"}<span
                                                        style="display:inline;" class="rm-required">*</span>{/if}</span>
                                            </div>
                                            <div class="rm_form_right_half">
                                                <div class="rm_form_control_block">
                                                    {assign var=radio_counter value=1}
                                                    {foreach from=$field['options'] item=field_options}
                                                        {*<div class="radio" id="uniform-field_{$field['id_velsof_rm_custom_fields'] nofilter}">*}
                                                        <span>
                                                            <input type="radio"
                                                                name="custom_fields[field_{$field['id_velsof_rm_custom_fields'] nofilter}][{$product['product']['id_order_detail'] nofilter}]"
                                                                value="{$field_options['option_value'] nofilter}"
                                                                {if $field_options['default_value'] eq $field_options['option_value']}checked{/if}>
                                                            <label
                                                                for="field_{$field['id_velsof_rm_custom_fields'] nofilter}_{$product['product']['id_order_detail'] nofilter}">{$field_options['option_label'] nofilter}</label>
                                                        </span>
                                                        {*</div>*}
                                                        {assign var=radio_counter value=$radio_counter+1}
                                                    {/foreach}
                                                    <span
                                                        id="error_field_{$field['id_velsof_rm_custom_fields'] nofilter}_{$product['product']['id_order_detail'] nofilter}"
                                                        class="errorsmall_custom kb_hidden_custom"></span>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $field['type'] eq "checkbox"}
                                            <div class="rm_form_left_half">
                                                <span class="cursor_help rm_form_label"
                                                    title="{$field['field_help_text'] nofilter}">{$field['field_label'] nofilter}{if $field['required'] eq "1"}<span
                                                        style="display:inline;" class="rm-required">*</span>{/if}</span>
                                            </div>
                                            <div class="rm_form_right_half">
                                                <div class="rm_form_control_block">
                                                    {foreach from=$field['options'] item=field_options}
                                                        {*<div class="input-box input-field_{$field['id_velsof_rm_custom_fields'] nofilter}">*}
                                                        <div class="checker checkbox"
                                                            id="uniform-field_{$field['id_velsof_rm_custom_fields'] nofilter}">
                                                            <span class="checked">
                                                                <input
                                                                    {if $field_options['default_value'] eq $field_options['option_value']}checked{/if}
                                                                    type="checkbox"
                                                                    name="custom_fields[field_{$field['id_velsof_rm_custom_fields'] nofilter}][{$product['product']['id_order_detail'] nofilter}][]"
                                                                    value="{$field_options['option_value'] nofilter}">
                                                                <label
                                                                    for="field_{$field['id_velsof_rm_custom_fields'] nofilter}"><b>{$field_options['option_label'] nofilter}</b></label>
                                                            </span>
                                                        </div>
                                                        {*</div>*}
                                                    {/foreach}
                                                    <span id="error_field_{$field['id_velsof_rm_custom_fields'] nofilter}"
                                                        class="errorsmall_custom kb_hidden_custom"></span>
                                                </div>
                                            </div>
                                        {/if}
                                    </div>
                                {/foreach}
                            </div>
                        {/if}
                        <!-- End - Code to insert custom fields in registration form block -->

                        {* changes end *}
                        <div class="rm_row_form" style="margin-bottom:10px;width:100%">
                            <span class="rm_form_label">{l s='Comment' mod='returnmanager'}:</span>
                            <div class="rm_form_right_half rm_control_block_fw">
                                <textarea name="rm_comment_{$product['product']['id_order_detail'] nofilter}"
                                    class="rm_form_control rm_textarea"></textarea>
                            </div>
                        </div>
                        <div class="rm_row rm_responsive_left">
                            <div class="rm_left vss_font-size-15">
                                <input type="checkbox" name="rm_agree_toc_{$product['product']['id_order_detail'] nofilter}" />
                                {l s='I agree with terms & Conditions.' mod='returnmanager'}(<a
                                    id="rm_display_toc_{$product['product']['id_order_detail'] nofilter}"
                                    href="javascript:showhidetocOnMultipleForm({$product['product']['id_order_detail'] nofilter});"
                                    class="rm_link">{l s='See here' mod='returnmanager'}</a>)
                            </div>
                            <div class="rm_right">
                                <input type="hidden" name="id_order_detail_{$product['product']['id_order_detail'] nofilter}"
                                    value="{$product['product']['id_order_detail'] nofilter}" />
                                <input type="hidden" name="id_order_{$product['product']['id_order_detail'] nofilter}"
                                    value="{$product['product']['id_order'] nofilter}" />
                                <input type="hidden" name="id_product_{$product['product']['id_order_detail'] nofilter}"
                                    value="{$product['product']['id_product'] nofilter}" />
                                <input type="hidden" name="id_product_attribute_{$product['product']['id_order_detail'] nofilter}"
                                    value="{$product['product']['id_product_attribute'] nofilter}" />
                                <input type="hidden" name="id_customer_{$product['product']['id_order_detail'] nofilter}"
                                    value="{$product['product']['customer_id'] nofilter}" />
                                <input type="hidden" name="id_policy_{$product['product']['id_order_detail'] nofilter}"
                                    value="{$product['product']['policy_id'] nofilter}" />
                            </div>
                        </div>
                        <div class="rm_clear"></div>
                        <div id="rm_toc_block_{$product['product']['id_order_detail'] nofilter}" class="rm_row kb_rm_toc_block">
                            <p>{l s='Terms & Conditions' mod='returnmanager'}:</p>
                            <p id="rm_toc_textarea_{$product['product']['id_order_detail'] nofilter}">
                                {$product['product']['return_toc'] nofilter}
                            </p>
                        </div>
                    </div>
                {/foreach}
                {*
                 * Escape JSON for HTML attribute so quotes do not break value / POST payload.
                 * 21-07-2026
                 *}
                <input type="hidden" name="kb_order_infos" value="{$kb_order_infos|escape:'html':'UTF-8'}" />
                <div class='rm_right' style="padding-right:10px">
                    <button onclick="handleReturnBlockRefresh()" id="rm_pop_up_close_btn"
                        class="btn btn-medium btn-primary vss_font-size-13"><span>{l s='Close' mod='returnmanager'}</span></button>
                    <button onclick="return rmSubmitReturnMultipleRequest(this)"
                        class="btn btn-medium btn-success vss_font-size-13"><span>{l s='Submit' mod='returnmanager'}</span></button>
                </div>
            </div>
        </div>
        <div class="rm_clear"></div>
    </div>
</div>
<div id="rm_fade" class="black_overlay"></div>
<style>
    .orderNumber {
        text-align: center;
        width: 100%;
        display: inline-block;
        margin-bottom: 5px;
    }

    .rm-heading-small.rm_form_info_text {
        text-align: center;
    }

    .returnList {
        background: #ffff;
        padding: 10px;
        box-sizing: border-box;
        box-shadow: 0 0 2px #ccc;
        margin: 8px;
    }

    #rm_return_type_note_28 {
        position: absolute;
        bottom: -14px;
        font-size: 10px;
    }

    #rm_popup_request_form .returnList .kb_field_row .rm_row_form {
        position: relative;
    }

    #rm_return_type_note_28 p {
        font-size: 11px;
    }

    #rm_popup_request_form .rm_form_label {
        font-size: 13px;
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #000;
    }

    #rm_popup_request_form .rm_row_form.fullWidthRow {
        width: 100%;
        display: inline-block;
        margin-bottom: 15px !important;
    }

    .rm_row.rm_responsive_left.fullWidthRow {
        display: inline-block;
        width: 100%;
    }

    .rm_row.padLR {
        padding: 0 10px;
    }

    .rm_row.padLR .kb_rm_toc_block {
        border: 1px solid #e6e6e6;
        border-radius: 4px;
    }

    .rm_row.padLR .kb_rm_toc_block p {
        font-size: 14px;
        margin-bottom: 2px;
    }

    .companyAddress {
        background: #fdfbfb;
        padding: 10px !important;
        border-radius: 4px;
        border: 1px solid #f3f1f1
    }

    #rm_popup_request_form .rm_pop_heading {
        margin-right: 0 !important;
    }

    @media (max-width: 769px) and (min-width: 480px) {
        #rm_return_form_popup .rm_form_left_half {
            width: 100% !important;
        }

        #rm_return_form_popup .rm_form_right_half {
            width: 100% !important;
        }
    }

    @media(max-width:640px) {
        #rm_popup_request_form .returnList .kb_field_row .rm_row_form.companyAddress {
            width: 100%;
        }

        #rm_popup_request_form .returnList .kb_field_row:first-child .rm_row_form {
            width: 100%;
        }

        .rm_popup_close_icon {
            right: 4px;
            top: 9px;
            margin-right: 0;
            margin-top: 0;
        }
    }
</style>

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
* Returns Request Form
*}