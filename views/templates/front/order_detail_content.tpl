<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0"/>
<script>
    var path = "{$path nofilter}"; {*escape not required as contains URL*}
    var module_link = "{$module_link nofilter}"; {*escape not required as contains URL*}
    var path_fold = '{$kb_admin_link nofilter}'; {*escape not required as contains URL*}
    var rm_is_logged = "{$isLogged nofilter}"; {*escape not required as contains URL*}
    var requiredField = "{l s='Required field' mod='returnmanager'}";
    var confirm_delete_msg = "{l s='Are you sure want to cancel the return request ?' mod='returnmanager'}";
    var invalidEmailId = "{l s='Invalid Email Address' mod='returnmanager'}";
    var orderNotFound = "{l s='No order found for provided information.' mod='returnmanager'}";
    var orderedProductNotFound = "{l s='The requested product not found.' mod='returnmanager'}";
    var rm_ajax_failed = "{l s='Technical Error Occurred' mod='returnmanager'}";
    var rm_return_type_required = "{l s='Please select return type' mod='returnmanager'}";
    {*changes by vishal for adding cancel functionality*}
    var rm_return_reason_required = "{l s='Please select Reason' mod='returnmanager'}";
    var rm_return_reason_blank = "{l s='Please enter reason' mod='returnmanager'}";
    {*changes end*}
    var rm_reason_required = "{l s='Please select reason' mod='returnmanager'}";
    var rm_product_required = "{l s='Please select a product for replacement.' mod='returnmanager'}";
    var rm_toc_checked = "{l s='Please agree to terms & conditions' mod='returnmanager'}";
    var file_type_error = "{l s='Invalid Format, Please select only image and zip file' mod='returnmanager'}";
    var image_size_error = "{l s='File size should not greater than 4 MB' mod='returnmanager'}";
    var cancel_success = "{l s='Return request cancelled successfully.' mod='returnmanager'}";
    var cancel_failure = "{l s='Return request could not be cancelled' mod='returnmanager'}";
    var select_product_error = "{l s='Please check at least one product you would like to return.' mod='returnmanager'}";
</script>

{capture name=path}
{l s='Returns Manager' mod='returnmanager'}
{/capture}
    <div class="kbloading" style='display:none'>Loading&#8230;</div>
{*{if $isLogged}*}
    <div id='rm_single_order_detail_container' class="rm_row" style="display:block;">
{*{/if}*}
    <div id="block">
        {if !$isLogged}
            <div class="rm_row rm_b_margin rm_bottom_border rm_left">
                <div class="rm_half_row rm_left" style="display:inline-block;">
                    <div class="rm_order_heading">{if isset($customer_info['firstname'])}{$customer_info['firstname'] nofilter} {/if} {if isset($customer_info['lastname'])} {$customer_info['lastname'] nofilter} {/if}</div>
                    <br>
                    <div class="rm_order_heading" style="font-size:14px;">{if isset($customer_info['email'])}{$customer_info['email'] nofilter} {/if}</div>
                </div>

                <div class="rm_half_row rm_right">
                    <div class="rm_order_heading">
                        <button id="rm_find_another_order_btn" class="btn btn-medium btn-warning" style='font-weight:bold;'><span>{l s='Find another order' mod='returnmanager'}</span></button>
                    </div>    
                </div>
            </div>
            <div class="rm_clear"></div>
            <div class="rm_row" style="margin-bottom:5px; margin-top:10px;">
                <div class="rm_half_row">
                    <div class="rm_order_heading"><div class="rm_label_orderstatus" style="background-color:black; color: white;">{l s='Order Detail' mod='returnmanager'}</div></div>
                </div>        
            </div>
        {/if}
        {if $isLogged}
            <div class="rm_row" style="margin-bottom:5px; margin-top:17px;">
                <div class="rm_half_row">
                    <div class="rm_order_heading"><div class="rm_label_orderstatus" style="background-color:black; color: white;">{l s='Orders' mod='returnmanager'}</div></div>
                </div>        
            </div>    
        {/if}

        {if count($orders) > 0}
            {foreach $orders as $order}

                <div class="rm_scrollable_div">
                    <table class="rm_single_order_row" cellspaing="4" cellpadding="2">                        
                        <tr class="rm_order_header rm_clickable_accordian" {*{if $isLogged nofilter}*}id="rm_accordian_{$order['order_id'] nofilter}" style="cursor: pointer;"{*{/if}*}>
                            {if isset($enable_complete_order_return) && $enable_complete_order_return == 1}
                                <td style="width: 10px;" class="head-checkbox" onclick="rmshowHiddenOrderData('{$order['order_id'] nofilter}')"><input type="checkbox"/></td>
                            {/if}
                            <td class="rm_label_hightlight vss_padding-left-10" colspan='2' onclick="showHiddenOrderData('{$order['order_id'] nofilter}')">{$order['reference_id'] nofilter}</td>
                            <td class="rm_order_header rm_right_align" colspan='3' onclick="showHiddenOrderData('{$order['order_id'] nofilter}')">
                                <div class="rm_label_orderstatus" style="background-color:{$order['order_state_color'] nofilter}">{$order['order_state'] nofilter}</div>
                            </td>
                            {*changes by vishal for adding order cancellaton functionality*}
                            {if isset($order['cancellable']) && $order['cancellable'] == 1  && $order['order_status_id'] != $order_canceled_id}
                                <td style="width: 11%">
                                    <button class="kb_rm_cancel_order" style='font-weight:bold;' onclick="getCancelForm('{$order['order_id'] nofilter}');"><span>{l s='Cancel Order' mod='returnmanager'}</span></button>
                                </td>
                            {/if}
                            {*changes end*}
                            {if isset($enable_complete_order_return) && $enable_complete_order_return == 1 && $order['order_status_id'] != $order_canceled_id}
                                <td style="width: 20%">
                                    <button class="kb_rm_return_order" style='font-weight:bold;' onclick="kbrmgetReturnForm(this);"><span>{l s='Return Selected Products' mod='returnmanager'}</span></button>
                                </td>
                            {/if}
                        </tr>

                        {foreach $order['products'] as $product}            
                            <tr class="rm_pro_row {*{if $isLogged nofilter}*}rm_custom_accordian_hidden rm_custom_accordian_{$order['order_id'] nofilter}{*{/if}*}">
                                {if isset($enable_complete_order_return) && $enable_complete_order_return == 1}
                                    <td>
                                        {if ($product['is_creditable'] || $product['is_refundable'] || $product['is_replacement']) && $product['is_returnable']  && ($order['order_status_id'] != $order_canceled_id)}
                                            {$value_arr[0] = $product['id_order']}
                                            {$value_arr[1] = $product['id_order_detail']}
                                            {$value_arr[2] = $product['id_product']}
                                            {$value_arr[3] = $order['rm_customer_id']}
                                            {$value_arr[4] = $product['rm_policy_id']}
                                            {$value_str= '_'|impl:$value_arr}                                        
                                            <span id="rm_product_order_detail_{$value_str nofilter}">
                                                <input type="checkbox" id="rm_order_{$value_str nofilter}" name="rm_order_detail[{$value_str nofilter}]" value="{$value_str nofilter}">
                                            </span>
                                        {/if}                                    
                                    </td>
                                {/if}
                                <td class="rm_img_col"><a href="{$product['product_link'] nofilter}"><img src='{$product['pro_img'] nofilter}' height='100' width='100'/></a></td>
                                <td class="rm_description_col">
                                    <div class="rm_product_name">
                                        <a href="{$product['product_link'] nofilter}" >{$product['name'] nofilter}</a>
                                    </div>
                                    {foreach $product['attributes'] as $p_attr}
                                        <div class="rm_product_attr">{$p_attr nofilter}</div>                        
                                    {/foreach}                    
                                    <div>
                                        {l s='Quantity' mod='returnmanager'}: {$product['quantity'] nofilter}
                                        {if isset($product['total_return_qty'])}
                                            <br>{l s='Returned' mod='returnmanager'}: {$product['total_return_qty'] nofilter}
                                        {/if}
                                    </div>
                                </td>

                                <td class="rm_center_align">{$product['price'] nofilter}</td>

                                <td class="rm_center_align">
                                    {if $product['is_delivered']}
                                        {if $product['is_returnable']}
                                            {if $product['is_creditable'] || $product['is_refundable'] || $product['is_replacement']}
                                                    {if $product['is_creditable']}
                                                        <span class="rm_product_return_stat">{l s='Creditable within' mod='returnmanager'} {$product['credit_days'] nofilter} {l s='Days' mod='returnmanager'}</span>
                                                    {/if}
                                                    {if $product['is_refundable']}
                                                        <span class="rm_product_return_stat">{l s='Refundable within' mod='returnmanager'} {$product['refund_days'] nofilter} {l s='Days' mod='returnmanager'}</span>
                                                    {/if}
                                                    {if $product['is_replacement']}
                                                        <span class="rm_product_return_stat">{l s='Replacement within' mod='returnmanager'} {$product['replacement_days'] nofilter} {l s='Days' mod='returnmanager'}</span>
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

                                <td class="{if ($product['is_creditable'] || $product['is_refundable'] || $product['is_replacement'])  && ($order['order_status_id'] != $order_canceled_id)}rm_right_align{else}rm_center_align{/if}">
                                {if !$product['is_delivered']}
                                        {l s='Product is not applicable for return yet.' mod='returnmanager'}
                                    {else if $product['already_returned']}
                                        {l s='Already Returned' mod='returnmanager'}
                                    {else if !$product['is_returnable']}
                                        {l s='Not Applicable' mod='returnmanager'}
                                    {else if ($product['is_creditable'] || $product['is_refundable'] || $product['is_replacement'] ) && ($order['order_status_id'] != $order_canceled_id)}
                                        {$value_arr[0] = $product['id_order']}
                                        {$value_arr[1] = $product['id_order_detail']}
                                        {$value_arr[2] = $product['id_product']}
                                        {$value_arr[3] = $order['rm_customer_id']}
                                        {$value_arr[4] = $product['rm_policy_id']}
                                        {$value_str= '_'|impl:$value_arr}
                                        <button class="btn btn-medium btn-primary vss_font-size-13" style='font-weight:bold;' onclick="getReturnForm('{$value_str nofilter}', this);"><span>{l s='Return' mod='returnmanager'}</span></button>   
                                    {else}
                                        {l s='Not Applicable' mod='returnmanager'}
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                        <tr class="rm_single_order_total_row {*{if $isLogged nofilter}*}rm_custom_accordian_hidden rm_custom_accordian_{$order['order_id'] nofilter}{*{/if}*}">
                            <td colspan='3' class="vss_padding-left-10 vss_text_align_left">{l s='Order placed on' mod='returnmanager'}: {$order['order_date']  nofilter}</td>
                            <td colspan='2' class="vss_text_align_right vss_font_weight_bold"><div class="vss_width_100">{l s='Order Total' mod='returnmanager'}: {$order['cart_total'] nofilter}</div>
                                <div>{l s='Total Paid' mod='returnmanager'}: {$order['total_paid'] nofilter}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            {/foreach}    
        {else}
            <div class="rm_history_nodata">{l s='No Order Created yet' mod='returnmanager'}</div>
        {/if}
        <br>
        <div class="rm_row rm_history_header">
            <div class="rm_half_row rm_left rm_clickable_accordian" onclick='showHiddenOrderData(0)' style='cursor: pointer;'>
                <div class="rm_order_heading"><div class="rm_label_orderstatus" style="background-color:black; color: white; margin-bottom: -3px;">{l s='Return History' mod='returnmanager'}</div></div> <label id="click_to_expand"> {l s='Click to Expand' mod='returnmanager'}</label><label id="click_to_contract"> {l s='Click to Contract' mod='returnmanager'}</label>
            </div>        
        </div>

        <div class="rm_scrollable_div">
            {if count($return_history) > 0}   
                <table class="rm_single_order_row rm_custom_accordian_hidden" id='rm_return_history_block' cellspacing="4" cellpadding="1">
                    <thead>
                        <tr class="rm_order_header">
                            {*code added by kanishka kannoujia on 30-06-2022 to display return id in the return order history *}
                            <th>{l s='Return Id' mod='returnmanager'}</th>
                            {*code added by kanishka kannoujia on 30-06-2022 to display return id in the return order history *}
                            <th>{l s='Product' mod='returnmanager'}</th>
                            <th>{l s='Status' mod='returnmanager'}</th>
                            <th>{l s='Return Type' mod='returnmanager'}</th>
                            <th>{l s='Notes' mod='returnmanager'}</th>
                            <th>{l s='Request Date' mod='returnmanager'}</th>
                            <th>{l s='Approve/Deny' mod='returnmanager'}</th> 
                            {if isset($enable_cancel_return) && $enable_cancel_return == 1}
                            <th>{l s='Cancel Return Request' mod='returnmanager'}</th>
                            {/if}
                        </tr>
                    </thead>
                    <tbody>    
                    {foreach $return_history as $return}            
                        <tr class="rm_pro_row">     
                            {*code added by kanishka kannoujia on 30-06-2022 to display return id in the return order history *}
                            <td>{$return['id_return'] nofilter}</td>
                            {*code added by kanishka kannoujia on 30-06-2022 to display return id in the return order history *}
                            <td>
                                <label class='rm_product_name'><a href="{$return['product_link'] nofilter}">{$return['product_name'] nofilter}</a></label>{if $return['product_attr'] != ''}<br>{$return['product_attr'] nofilter}{/if}<br>{l s='Quantity' mod='returnmanager'}: {$return['quantity'] nofilter}
                            </td>
                            <td>{$return['status'] nofilter}</td>
                            <td>{$return['return_type'] nofilter}</td>
                            <td style="max-width: 300px;">{if $return['comment'] != ''}{$return['comment'] nofilter}{else}<span class='vss_italic_text'>{l s='No comments by customer.' mod='returnmanager'}</span>{/if}</td>{*Variable contains html content, escape not required*}
                            <td>{$return['request_date'] nofilter}</td>
                            <td>
                                    {if $return['active'] eq 2}
                                            <label style='color: green;'>{l s='Approved' mod='returnmanager'}</label>
                                            {if isset($return_slip) && $return_slip == 1}
                                                {if !$return['is_virtual']}
                                                    <p><a target="_blank" class="rm_return_slip_link" href="{$return['slip_link'] nofilter}">{l s='Return Slip' mod='returnmanager'}</a></p>
                                                {/if}
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
        </div><br>
    </div>
{*{if $isLogged}*}
</div>
{*{/if}*}
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
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
* @category  PrestaShop Module
*
* Description
*
* Return Manager Order Details Content File
*}