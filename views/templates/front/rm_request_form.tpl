<style>
    {if isset($product['img_path'])}    
        .rm_box_shadow {
            background-image: url({$product['img_path']|escape:'quotes':'UTF-8'});
        }

    {else}
        .rm_box_shadow {
            background-image: url({Context::getContext()->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default') nofilter});
        }

    {/if}
</style>
<script>
    var path_fold = '{$kb_admin_link nofilter}';
</script>
<div id="rm_return_form_popup" class="white_content kb_single_return_form_popup">
    <div class="rm_innerBox">
        <a href="javascript:void(0)" class="rm_popup_close_icon" onclick="handleReturnBlockRefresh()">&nbsp;</a>
        <div id="rm_row">
            <div id="rm_popup_pro_info" class="rm_popup_left rm_left rm_box_shadow">
                <div class="rm_pop_heading">
                    {l s='Item Detail' mod='returnmanager'}
                    <span
                        style='font-size: 12px; text-transform: none;'>{l s='Order' mod='returnmanager'}:&nbsp;{$product['odr_reference'] nofilter}</span>
                </div>
                <div class="rm_row rm_pro_detail_block">
                    <div class="rm_popup_pro_img">
                        {if isset($product['img_path'])}
                            <img src="{$product['img_path'] nofilter}"
                                onerror="this.src='{$path nofilter}returnmanager/views/img/No-image.jpg'">
                        {else}
                            <img src="{Context::getContext()->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default') nofilter}"
                                onerror="this.src='{$path nofilter}returnmanager/views/img/No-image.jpg'">
                        {/if}
                    </div>
                    <div class="rm_popup_pro_name_block">
                        <span class="rm_popup_pro_name">{$product['name'] nofilter}</span>
                        {foreach $product['attributes'] as $p_attr}
                            <span class="rm_popup_pro_attr">{$p_attr nofilter}</span>
                        {/foreach}
                    </div>
                </div>
                <div class="rm_row rmAddressSection">
                    <span class="rm_popup_pro_name uppercase"
                        style="margin-bottom:8px;">{l s='Return Address' mod='returnmanager'}</span>
                    <span class="rm_popup_addr" name="rm_popup_address" id="rm_popup_address">
                        {$product['shipping_address'] nofilter}{* variable contains html content, can not escape *}
                        {*{if $enable_address == 0 && $default_address != ''}
                            {$default_address nofilter}
                        {/if}*}
                    </span>

                </div>
            </div>
            <div id="rm_popup_request_form" class="rm_popup_right rm_left">
                <div class="rm_row_form_title">
                    <div class="rm_pop_heading"><img
                            src="{$img_path|escape:'htmlall':'UTF-8'}return.png" />{l s='Easy Return' mod='returnmanager'}
                    </div>
                    <span
                        class="rm-heading-small rm_form_info_text">{l s='Please fill the below form to make request for return.' mod='returnmanager'}</span>
                </div>
                <div class="kb_field_row">
                    <div class="rm_row_form">
                        <div class="rm_form_left_half">
                            <span class="rm_form_label">{l s='Return Type' mod='returnmanager'}:</span>
                        </div>
                        <div class="rm_form_right_half">
                            <div class="rm_form_control_block">
                                <select name="rm_return_type" class="rm_form_control"
                                    onchange="displayReturnNote(this)">
                                    <option value="0">{l s='Select Return Type' mod='returnmanager'}</option>
                                    {foreach $product['return_types'] as $type}
                                        <option value="{$type['value'] nofilter}">{$type['text'] nofilter}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div id="rm_return_type_note" class="rm_form_note">
                                {foreach $product['return_types'] as $type}
                                    <p id="rm_return_type_note_{$type['value'] nofilter}" class="vss_font-size-13">{$type['note'] nofilter}
                                    </p>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                    <div class="rm_row_form">
                        <div class="rm_form_left_half">
                            <span class="rm_form_label">{l s='Quantity' mod='returnmanager'}:</span>
                        </div>
                        <div class="rm_form_right_half">
                            <div class="rm_form_control_block">
                                <select name="rm_return_quantity" class="rm_form_control"
                                    style="padding:10px">
                                        <option value="{$product['product_quantity'] nofilter}">{$product['product_quantity'] nofilter}</option>
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kb_field_row">
                    {if count($product['reasons']) > 0}
                        <div class="rm_row_form">
                            <div class="rm_form_left_half">
                                <span class="rm_form_label">{l s='Reason' mod='returnmanager'}:</span>
                            </div>
                            <div class="rm_form_right_half">
                                <div class="rm_form_control_block">
                                    <select name="rm_return_reason" class="rm_form_control"
                                        onchange="displayReasonNote(this)">
                                        <option value="0">{l s='Select Reason' mod='returnmanager'}</option>
                                        {foreach $product['reasons'] as $reason}
                                            <option value="{$reason['reason_id'] nofilter}">{$reason['text'] nofilter}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div id="rm_reason_type_note" class="rm_form_note">
                                    {foreach $product['reasons'] as $reason}
                                        <p id="rm_reason_type_note_{$reason['reason_id'] nofilter}" class="vss_font-size-13">
                                            {$reason['shipping_paid_by'] nofilter}</p>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>

                <div class="kb_field_row">
                    {if isset($enable_product_selection) && $enable_product_selection == 1}
                        <div class="rm_row_form" id="kb_product_choose_block" style="display:none; width: 100% !important">
                            <div class="rm_form_left_half">
                                <span class="rm_form_label">{l s='Select Products' mod='returnmanager'}:</span>
                            </div>
                            <div class="rm_form_right_half">
                                <div class="rm_form_control_block">
                                    <input type="text" name="kb_Product_specific_products" id="kb_Product_specific_products"
                                        value="" class="ac_input pvt_accesssetting" autocomplete="off">
                                    <input type="hidden" name="rm_return_product" id="rm_return_product">
                                    <img id="rm_kbproduct_loader"
                                        src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif"
                                        style="display: none;float: right;margin-top: 8px">
                                </div>

                            </div>
                        </div>
                        <div class="rm_row_form" id="kb_product_attribute_choose_block" style="display:none;">
                            <div class="rm_form_left_half">
                                <span class="rm_form_label">{l s='Select Attribute' mod='returnmanager'}:</span>
                            </div>
                            <div class="rm_form_right_half">
                                <div class="rm_form_control_block" id="rm_return_product_attribute">
                                    {*<select name="rm_return_product_attribute" class="chosen rm_form_control">
                                    <option value="0">{l s='Select Product Attribute' mod='returnmanager'}</option>
                                </select>*}
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
                <!-- Start - Code to insert custom fields in cart block -->
                {if isset($enable_custom_field) && $enable_custom_field == 1 && isset($array_fields) && !empty($array_fields)}
                    <div class="rm_row_form_title">
                        <span class="rm_form_label" style="margin-left: 9px;">{$custom_field_block_title nofilter}</span>
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
                                                name="custom_fields[field_{$field['id_velsof_rm_custom_fields'] nofilter}]"
                                                value="{$field['default_value'] nofilter}">
                                            <span id="error_field_{$field['id_velsof_rm_custom_fields'] nofilter}"
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
                                            <textarea name="custom_fields[field_{$field['id_velsof_rm_custom_fields'] nofilter}]"
                                                class="rm_form_control">{$field['default_value'] nofilter}</textarea>
                                            <span id="error_field_{$field['id_velsof_rm_custom_fields'] nofilter}"
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
                                            <select name="custom_fields[field_{$field['id_velsof_rm_custom_fields'] nofilter}]"
                                                class="rm_form_control">
                                                {foreach from=$field['options'] item=field_options}
                                                    <option
                                                        {if $field_options['default_value'] eq $field_options['option_value']}selected{/if}
                                                        value="{$field_options['option_value'] nofilter}">{$field_options['option_label'] nofilter}
                                                    </option>
                                                {/foreach}
                                            </select>
                                            <span id="error_field_{$field['id_velsof_rm_custom_fields'] nofilter}"
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
                                                        name="custom_fields[field_{$field['id_velsof_rm_custom_fields'] nofilter}]"
                                                        value="{$field_options['option_value'] nofilter}"
                                                        {if $field_options['default_value'] eq $field_options['option_value']}checked{/if}>
                                                    <label
                                                        for="field_{$field['id_velsof_rm_custom_fields'] nofilter}">{$field_options['option_label'] nofilter}</label>
                                                </span>
                                                {*</div>*}
                                                {assign var=radio_counter value=$radio_counter+1}
                                            {/foreach}
                                            <span id="error_field_{$field['id_velsof_rm_custom_fields'] nofilter}"
                                                class="errorsmall_custom kb_hidden_custom"></span>
                                        </div>
                                    </div>
                                {/if}
                                {if $field['type'] eq "checkbox"}
                                    <div class="rm_form_left_half">
                                        <span class="cursor_help rm_form_label"
                                            title="{$field['field_help_text'] nofilter}">{$field['field_label']}{if $field['required'] eq "1"}<span
                                                style="display:inline;" class="rm-required">*</span>{/if}</span>
                                    </div>
                                    <div class="rm_form_right_half">
                                        <div class="rm_form_control_block">
                                            {foreach from=$field['options'] item=field_options}
                                                {*<div class="input-box input-field_{$field['id_velsof_rm_custom_fields'] nofilter}">*}
                                                <div class="checker checkbox" id="uniform-field_{$field['id_velsof_rm_custom_fields'] nofilter}">
                                                    <span class="checked">
                                                        <input
                                                            {if $field_options['default_value'] eq $field_options['option_value']}checked{/if}
                                                            type="checkbox"
                                                            name="custom_fields[field_{$field['id_velsof_rm_custom_fields'] nofilter}][]"
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
                {* Changes done by rishabh to add options to select address *}
                {if count($address_list) > 0 && $enable_address == 1}
                    <div class="rm_row_form">
                        <div class="rm_form_left_half">
                            <span class="rm_form_label">{l s='Select Return Address' mod='returnmanager'}:</span>
                        </div>
                        <div class="rm_form_right_half">
                            <div class="rm_form_control_block">
                                <select name="rm_return_address" id="rm_return_address" class="rm_form_control"
                                    onchange="displayReturnAddress()">
                                    <option value="0">{l s='Default Address' mod='returnmanager'}</option>
                                    {foreach $address_list as $address}
                                        <option value="{$address['id_address']|escape:'htmlall':'UTF-8'}">
                                            {$address['title']|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                                {foreach $address_list as $address}
                                    <input type="hidden" id="full_addr_{$address['id_address']|escape:'htmlall':'UTF-8'}"
                                        name="full_addr_{$address['id_address']|escape:'htmlall':'UTF-8'}"
                                        value="{$full_addr[$address['id_address']]|escape:'htmlall':'UTF-8'}" />
                                {/foreach}
                                <input type="hidden" id="default_addr" name="default_addr"
                                    value="{$product['shipping_address'] nofilter}" />{* variable contains html content can not escape *}
                            </div>

                        </div>
                    </div>
                {/if}
                {* changes end *}
                <div class="rm_row_form" style="margin-bottom:10px;">
                    <span class="rm_form_label">{l s='Comment' mod='returnmanager'}:</span>
                    <div class="rm_form_right_half rm_control_block_fw">
                        <textarea name="rm_comment" class="rm_form_control rm_textarea"></textarea>
                    </div>
                </div>
                <div class="rm_row rm_responsive_left">
                    <div class="rm_left vss_font-size-15">
                        <input type="checkbox" name="rm_agree_toc" />
                        {l s='I agree with terms & Conditions.' mod='returnmanager'}(<a id="rm_display_toc"
                            href="javascript:showhidetoc();" class="rm_link">{l s='See here' mod='returnmanager'}</a>)
                    </div>
                    <div class="rm_right">
                        <input type="hidden" name="id_order_detail" value="{$product['id_order_detail'] nofilter}" />
                        <input type="hidden" name="id_order" value="{$product['id_order'] nofilter}" />
                        <input type="hidden" name="id_product" value="{$product['id_product'] nofilter}" />
                        <input type="hidden" name="id_product_attribute" value="{$product['id_product_attribute'] nofilter}" />
                        <input type="hidden" name="id_customer" value="{$product['customer_id'] nofilter}" />
                        <input type="hidden" name="id_policy" value="{$product['policy_id'] nofilter}" />
                        <button onclick="handleReturnBlockRefresh()" id="rm_pop_up_close_btn"
                            class="btn btn-medium btn-primary vss_font-size-13"><span>{l s='Close' mod='returnmanager'}</span></button>
                        <button onclick="return rmSubmitReturnRequest(this)"
                            class="btn btn-medium btn-success vss_font-size-13"><span>{l s='Submit' mod='returnmanager'}</span></button>
                    </div>
                </div>
                <div class="rm_clear"></div>
                <div id="rm_toc_block" class="rm_row">
                    <p>{l s='Terms & Conditions' mod='returnmanager'}:</p>
                    <p id="rm_toc_textarea">
                        {$product['return_toc'] nofilter}
                    </p>
                </div>
            </div>
        </div>
        <div class="rm_clear"></div>
    </div>
</div>
<div id="rm_fade" class="black_overlay"></div>

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