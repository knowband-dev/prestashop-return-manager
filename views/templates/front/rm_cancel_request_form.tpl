<style>
    {*{if isset($product['img_path'])}    
        .rm_box_shadow {
            background-image: url({$product['img_path']|escape:'quotes':'UTF-8'});
        }    
    {else}
        .rm_box_shadow {
            background-image: url({Context::getContext()->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default')});
        }
    {/if}*}
</style>
<div id="rm_return_form_popup" class="white_content">
    <div class="rm_innerBox">
        <a href="javascript:void(0)" class="rm_popup_close_icon" onclick="handleReturnBlockRefresh()">&nbsp;</a>
        <div id="rm_row">
            <div id="rm_popup_pro_info" class="rm_popup_left rm_left rm_box_shadow" style="background: black;min-height: 330px;">
                {*<div class="rm_pop_heading">
                    {l s='Item Detail' mod='returnmanager'}
                    <span style='font-size: 12px; text-transform: none;'>{l s='Order' mod='returnmanager'}:&nbsp;{$product['odr_reference']}</span>
                </div>*}
                {*<div class="rm_row rm_pro_detail_block">
                    <div class="rm_popup_pro_img">
                        {if isset($product['img_path'])}
                            <img src="{$product['img_path'] nofilter}" onerror="this.src='{$path nofilter}returnmanager/views/img/No-image.jpg'">
                        {else}
                            <img src="{Context::getContext()->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default')}" onerror="this.src='{$path nofilter}returnmanager/views/img/No-image.jpg'">
                        {/if}
                    </div>
                    <div class="rm_popup_pro_name_block">
                        <span class="rm_popup_pro_name">{$product['name'] nofilter}</span>
                        {foreach $product['attributes'] as $p_attr}
                            <span class="rm_popup_pro_attr">{$p_attr nofilter}</span>
                        {/foreach}
                    </div>
                </div>*}
                <div class="rm_row rmAddressSection" style="margin-top: 10%;">
                    <span class="rm_popup_pro_name uppercase" style="margin-bottom:8px;">{l s='Order Details' mod='returnmanager'}</span>
                    <span class="rm_popup_addr" name="rm_popup_address" id="rm_popup_address">
                        <p>{l s='Order Reference' mod='returnmanager'} : {$order_reference nofilter}<p>
                        <p>{l s='Order Total' mod='returnmanager'} : {$order_paid nofilter}<p>
                        <p>{l s='Name' mod='returnmanager'} : {$cust_name nofilter}<p>
                        <p>{l s='Email' mod='returnmanager'} : {$cust_email nofilter}<p></p>
                    </span>
                
                </div>
            </div>
            <div id="rm_popup_request_form" class="rm_popup_right rm_left">
                <div class="rm_row_form_title">
                    <div class="rm_pop_heading"><img src="{$img_path|escape:'htmlall':'UTF-8'}return.png"/>{l s='Easy Cancel' mod='returnmanager'}</div>
                    <span class="rm-heading-small rm_form_info_text">{l s='Please fill the below form to make request for Order cancellation.' mod='returnmanager'}</span>
                </div>
                <div class="kb_field_row">
                <div class="rm_row_form" style="width:100%">
                    <div class="rm_form_left_half">
                        <span class="rm_form_label">{l s='Cancel Reason' mod='returnmanager'}:</span>
                    </div>
                    <div class="rm_form_right_half">
                        <div class="rm_form_control_block">
                            <select name="rm_return_type" id="rm_cancel_reason" class="rm_form_control">
                                <option value="-1">{l s='Select Reason' mod='returnmanager'}</option>
                                {foreach $kb_cancel_data as $type}
                                    <option value="{$type['return_data_id'] nofilter}">{$type['value'] nofilter}</option>
                                {/foreach}
                            </select>
                        </div>
                        
                    </div>
                </div>
                {*<div class="rm_row_form">
                    <div class="rm_form_left_half">
                        <span class="rm_form_label">{l s='Quantity' mod='returnmanager'}:</span>
                    </div>
                    <div class="rm_form_right_half">
                        <div class="rm_form_control_block">
                            <select name="rm_return_quantity" class="rm_form_control" style="width:50px;">
                                {for $qty=1 to $product['product_quantity']}
                                    <option value="{$qty nofilter}">{$qty nofilter}</option>
                                {/for}
                            </select>
                        </div>
                    </div>
                </div>*}
                </div>
                <div class="rm_row_form" id="kb_other_reason" style="margin-bottom:10px;display: none;">
                    <span class="rm_form_label">{l s='Other Reason' mod='returnmanager'}</span>
                    <div class="rm_form_right_half rm_control_block_fw">
                        <textarea name="rm_reason" id="rm_reason" class="rm_form_control rm_textarea"></textarea>
                    </div>
                </div>
                {*<div class="kb_field_row">            
                {if count($product['reasons']) > 0}
                    <div class="rm_row_form">
                        <div class="rm_form_left_half">
                            <span class="rm_form_label">{l s='Reason' mod='returnmanager'}:</span>
                        </div>
                        <div class="rm_form_right_half">
                            <div class="rm_form_control_block">
                                <select name="rm_return_reason" class="rm_form_control" onchange="displayReasonNote(this)">
                                    <option value="0">{l s='Select Reason' mod='returnmanager'}</option>
                                    {foreach $product['reasons'] as $reason}
                                        <option value="{$reason['reason_id'] nofilter}">{$reason['text'] nofilter}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div id="rm_reason_type_note" class="rm_form_note">
                                {foreach $product['reasons'] as $reason}
                                    <p id="rm_reason_type_note_{$reason['reason_id'] nofilter}" class="vss_font-size-13">{$reason['shipping_paid_by'] nofilter}</p>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                {/if}
                {if isset($enable_product_selection) && $enable_product_selection == 1}
                    <div class="rm_row_form" id="kb_product_choose_block" style="display:none;">                        
                        <div class="rm_form_left_half">
                            <span class="rm_form_label">{l s='Select Products' mod='returnmanager'}:</span>
                        </div>
                        <div class="rm_form_right_half">
                            <div class="rm_form_control_block">
                                <select name="rm_return_product" class="chosen rm_form_control">
                                    <option value="0">{l s='Select Product' mod='returnmanager'}</option>
                                    {foreach $product_array as $kbproduct}
                                        <option value="{$kbproduct['id_product'] nofilter}">{$kbproduct['name'] nofilter}:{$kbproduct['reference'] nofilter}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>                        
                    </div>
                {/if}
                </div> *}               
                <div class="rm_row_form" style="margin-bottom:10px;">
                    <span class="rm_form_label">{l s='Comment' mod='returnmanager'}:</span>
                    <div class="rm_form_right_half rm_control_block_fw">
                        <textarea name="rm_comment" class="rm_form_control rm_textarea"></textarea>
                    </div>
                </div>
                <div class="rm_row rm_responsive_left" style="float:left;">
                    <div class="rm_left vss_font-size-15">
                        <input type="checkbox" name="rm_agree_toc" />
                        {l s='I agree with terms & Conditions.' mod='returnmanager'}(<a id="rm_display_toc" href="javascript:showhidetoc();" class="rm_link">{l s='See here' mod='returnmanager'}</a>)
                    </div>
                    <div class="rm_right">
                        <input type="hidden" name="id_order" value="{$kb_order_id nofilter}" />
                        <button onclick="handleReturnBlockRefresh()" id="rm_pop_up_close_btn" class="btn btn-medium btn-primary vss_font-size-13" ><span>{l s='Close' mod='returnmanager'}</span></button>
                        <button onclick="return rmSubmitCancelRequest(this)" class="btn btn-medium btn-success vss_font-size-13" ><span>{l s='Submit' mod='returnmanager'}</span></button>                                
                    </div>
                </div>
                <div class="rm_clear"></div>
                <div id="rm_toc_block" class="rm_row">
                    <p>{l s='Terms & Conditions' mod='returnmanager'}:</p>
                    <p id="rm_toc_textarea">
                        {$kb_policy nofilter}
                    </p>
                </div>
            </div>
        </div>
        <div class="rm_clear"></div>    
    </div>        
</div>
<div id="rm_fade" class="black_overlay"></div>
<script>
    $(document).ready(function(){
         $('#rm_cancel_reason').on('change', function() {
            if ($('#rm_cancel_reason').val() == 0){
                $('#kb_other_reason').show();
            } else {
                $('#kb_other_reason').hide();
            }
}); 
    });
</script>

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
