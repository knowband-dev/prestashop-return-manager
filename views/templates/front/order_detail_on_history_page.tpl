<script>
	var path = "{$path|escape:'quotes':'UTF-8'}";
	var module_link = "{$module_link|escape:'quotes':'UTF-8'}";
	var rm_is_logged = "{$isLogged|escape:'htmlall':'UTF-8'}";
	var requiredField = "{l s='Required field' mod='returnmanager'}";
	var confirm_delete_msg = "{l s='Are you sure want to cancel the return request ?' mod='returnmanager'}";
	var invalidEmailId = "{l s='Invalid Email Address' mod='returnmanager'}";
	var orderNotFound = "{l s='No order found for provided information.' mod='returnmanager'}";
	var orderedProductNotFound = "{l s='The requested product not found.' mod='returnmanager'}";
	var rm_ajax_failed = "{l s='Technical Error Occurred' mod='returnmanager'}";
	var rm_return_type_required = "{l s='Please select return type' mod='returnmanager'}";
	var rm_reason_required = "{l s='Please select reason' mod='returnmanager'}";
	var rm_toc_checked = "{l s='Please agree to terms & conditions' mod='returnmanager'}";
        var file_type_error = "{l s='Invalid Format, Please select only image and zip file' mod='returnmanager'}";
        var image_size_error = "{l s='File size should not greater than 4 MB' mod='returnmanager'}";
        var cancel_success = "{l s='Return request cancelled successfully.' mod='returnmanager'}";
        var cancel_failure = "{l s='Return request could not be cancelled' mod='returnmanager'}";
</script>
{capture name=path}
{l s='Returns Manager' mod='returnmanager'}
{/capture}
{*<div id='rm_single_order_detail_container' class="rm_row">*}

    <div id="block">
        
        {if $isLogged}
            <div class="rm_row" style="margin-bottom:5px; margin-top:17px;">
                <div class="rm_half_row">
                    <div class="rm_order_heading"><div class="rm_label_orderstatus" style="background-color:black; color: white;">{l s='Manage Returns' mod='returnmanager'}</div></div>
                </div>        
            </div>    
        {/if}

        {if count($orders) > 0}
            {foreach $orders as $order}
                <div class="rm_scrollable_div">
                    <table class="rm_single_order_row" cellspaing="4" cellpadding="2">
                        <tr class="rm_order_header rm_clickable_accordian" {if $isLogged}id="rm_accordian_{$order['order_id']|escape:'htmlall':'UTF-8'}" onclick="showHiddenOrderDataRefresh('{$order['order_id']|escape:'htmlall':'UTF-8'}')" style="cursor: pointer;"{/if}>
                            <td class="rm_label_hightlight" colspan='2'>{$order['reference_id']|escape:'htmlall':'UTF-8'}</td>
                            <td class="rm_order_header rm_right_align" colspan='3'>
                                <div class="rm_label_orderstatus" style="background-color:{$order['order_state_color']|escape:'htmlall':'UTF-8'}">{$order['order_state']|escape:'htmlall':'UTF-8'}</div>
                            </td>
                        </tr>

                        {foreach $order['products'] as $product}            
                            <tr class="rm_pro_row {if $isLogged}rm_custom_accordian_hidden rm_custom_accordian_{$order['order_id']|escape:'htmlall':'UTF-8'}{/if}" style="display: table-row;">
                                <td class="rm_img_col"><a href="{$product['product_link']|escape:'htmlall':'UTF-8'}"><img src='{$product['pro_img']|escape:'htmlall':'UTF-8'}' height='100' width='100'/></a></td>
                                <td class="rm_description_col">
                                    <div class="rm_product_name">
                                        <a href="{$product['product_link']|escape:'htmlall':'UTF-8'}" >{$product['name']|escape:'htmlall':'UTF-8'}</a>
                                    </div>
                                    {foreach $product['attributes'] as $p_attr}
                                        <div class="rm_product_attr">{$p_attr|escape:'htmlall':'UTF-8'}</div>                        
                                    {/foreach}                    
                                    <div>
                                        {l s='Quantity' mod='returnmanager'}: {$product['quantity']|escape:'htmlall':'UTF-8'}
                                        {if isset($product['total_return_qty'])}
                                            <br>{l s='Returned' mod='returnmanager'}: {$product['total_return_qty']|escape:'htmlall':'UTF-8'}
                                        {/if}
                                    </div>
                                </td>

                                <td class="rm_center_align">{$product['price']|escape:'htmlall':'UTF-8'}</td>

                                <td class="rm_center_align">
                                    {if $product['is_delivered']}
                                        {if $product['is_returnable']}
                                            {if $product['is_creditable'] || $product['is_refundable'] || $product['is_replacement']}
                                                    
                                                    {if $product['is_refundable']}
                                                        <span class="rm_product_return_stat">{l s='Refundable within' mod='returnmanager'} {$product['refund_days']|escape:'htmlall':'UTF-8'} {l s='Days' mod='returnmanager'}</span>
                                                    {/if}
                                                   
                                            {else}
                                                    <span class="rm_product_return_stat">{l s='Not Applicable' mod='returnmanager'}</span>
                                            {/if}
                                        {else if $product['already_returned']}
                                            <span class="rm_product_return_stat">{l s='Already Returned' mod='returnmanager'}</span>                            
                                        {else}
                                            <span class="rm_product_return_stat">{l s='Not Applicable' mod='returnmanager'}</span>
                                        {/if}
                                    {else}
                                        <span class="rm_product_return_stat">{l s='Not Applicable' mod='returnmanager'}</span>
                                    {/if}
                                </td>

                                <td class="{if $product['is_creditable'] || $product['is_refundable'] || $product['is_replacement']}rm_right_align{else}rm_center_align{/if}">
                                    {if !$product['is_delivered']}
                                        {l s='Product is not applicable for return yet.' mod='returnmanager'}
                                    {else if $product['already_returned']}
                                        {l s='Already Returned' mod='returnmanager'}
                                    {else if !$product['is_returnable']}
                                        {l s='Not Applicable' mod='returnmanager'}
                                    {else if $product['is_creditable'] || $product['is_refundable'] || $product['is_replacement']}
                                        {$value_arr[0] = $product['id_order']}
                                        {$value_arr[1] = $product['id_order_detail']}
                                        {$value_arr[2] = $product['id_product']}
                                        {$value_arr[3] = $order['rm_customer_id']}
                                        {$value_arr[4] = $product['rm_policy_id']}
                                        {$value_str= '_'|impl:$value_arr}
                                        <button class="btn btn-medium btn-primary" style='font-weight:bold;' onclick="getReturnForm('{$value_str|escape:'htmlall':'UTF-8'}', this);"><span>{l s='Return' mod='returnmanager'}</span></button>   
                                    {else}
                                        {l s='Not Applicable' mod='returnmanager'}
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                        <tr class="rm_single_order_total_row {if $isLogged}rm_custom_accordian_hidden rm_custom_accordian_{$order['order_id']|escape:'htmlall':'UTF-8'}{/if}" style="display: table-row;">
                            <td colspan='3' style='text-align: left;'>{l s='Order placed on' mod='returnmanager'}: {$order['order_date']|escape:'htmlall':'UTF-8'}</td>
                            <td colspan='2' style="text-align: right; font-weight: bold;"><div style="width: 102%;">{l s='Order Total' mod='returnmanager'}: {$order['cart_total']|escape:'htmlall':'UTF-8'}</div>
                                <div>{l s='Total Paid' mod='returnmanager'}: {$order['total_paid']|escape:'htmlall':'UTF-8'}</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <br>
            {/foreach}    
        {else}
            <div class="rm_history_nodata">{l s='No Order Created yet' mod='returnmanager'}</div>
        {/if}
        <br>
        <div class="rm_row rm_history_header">
            <div class="rm_half_row rm_left rm_clickable_accordian" onclick='showHiddenOrderData(0)' style='cursor: pointer;'>
                <div class="rm_order_heading"><div class="rm_label_orderstatus" style="background-color:black; color: white; margin-bottom: -3px;">{l s='Return History' mod='returnmanager'}</div></div> <label id="click_to_expand"> {l s='Click to Expand' mod='returnmanager'}</label><label id="click_to_contract" style="display:none;" > {l s='Click to Contract' mod='returnmanager'}</label>
            </div>        
        </div>

        <div class="rm_scrollable_div">
            {if count($return_history) > 0}   
                <table class="rm_single_order_row rm_custom_accordian_hidden" id='rm_return_history_block' cellspacing="4" cellpadding="1">
                    <thead>
                        <tr class="rm_order_header">
                            {*<th>{l s='Return Id' mod='returnmanager'}</th>*}
                            <th>{l s='Product' mod='returnmanager'}</th>
                            <th>{l s='Status' mod='returnmanager'}</th>
                            <th>{l s='Return Type' mod='returnmanager'}</th>
                            <th>{l s='Notes' mod='returnmanager'}</th>
                            <th>{l s='Request Date' mod='returnmanager'}</th>
                            <th>{l s='Approve/Deny' mod='returnmanager'}</th>
                            {* changes over *}
                        </tr>
                    </thead>
                    <tbody>    
                    {foreach $return_history as $return}            
                        <tr class="rm_pro_row">  
                            <td>{$return['status']|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                <label class='rm_product_name'><a href="{$return['product_link']|escape:'htmlall':'UTF-8'}">{$return['product_name']|escape:'htmlall':'UTF-8'}</a></label>{if $return['product_attr'] != ''}<br>{$return['product_attr']|escape:'htmlall':'UTF-8'}{/if}<br>{l s='Quantity' mod='returnmanager'}: {$return['quantity']|escape:'htmlall':'UTF-8'}
                            </td>
                            <td>{$return['status']|escape:'htmlall':'UTF-8'}</td>
                            <td>{$return['return_type']|escape:'htmlall':'UTF-8'}</td>
                            <td style="max-width: 300px;">{if $return['comment'] != ''}{html_entity_decode($return['comment']|escape:'htmlall':'UTF-8')}{else}<span class='vss_italic_text'>{l s='No comments by customer.' mod='returnmanager'}</span>{/if}</td>
                            <td>{$return['request_date']|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                    {if $return['active'] eq 2}
                                            <label style='color: green;'>{l s='Approved' mod='returnmanager'}</label>
                                            {if isset($return_slip) && $return_slip == 1}
                                                    <p><a target="_blank" class="rm_return_slip_link" href="{$return['slip_link']|escape:'htmlall':'UTF-8'}">{l s='Return Slip' mod='returnmanager'}</a></p>
                                            {/if}
                                    {else if $return['active'] eq 3}
                                            <label style='color: red;'>{l s='Denied' mod='returnmanager'}</label>
                                    {else if $return['active'] eq 4}<label style='color: darkblue;'>
                                            {l s='Completed' mod='returnmanager'}</label>
                                    {else}
                                            {l s='Pending' mod='returnmanager'}{/if}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>        
                </table><br>
            {else}
                <div class="rm_history_nodata rm_custom_accordian_hidden" id='rm_return_history_block'>{l s='No Return Created yet' mod='returnmanager'}</div>
            {/if}
        </div>
    </div>

{if $isLogged}
    <div id="kb_rm_pop_up"></div>
{/if}

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2015 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Return Manager Order Detail Page
*}