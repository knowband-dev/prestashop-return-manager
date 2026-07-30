{*changes by vishal on 18 aug 2020 for handling return functionality for admin*}
<script>
    var select_product_error = "{l s='Please check at least one product you would like to return.' mod='returnmanager'}";
</script>    
{*changes end*}
<div id="block">
    <div class="rm_row rm_b_margin rm_bottom_border rm_left">
        <div class="rm_half_row rm_left" style="display:inline-block;">
            <div class="rm_order_heading">{if isset($customer_info['firstname'])}{$customer_info['firstname'] nofilter} {/if} {if isset($customer_info['lastname'])} {$customer_info['lastname'] nofilter} {/if}</div>
            <br>
            <div class="rm_order_heading" style="font-size:14px;">{$customer_info['email'] nofilter}</div>
        </div>

        <div class="rm_half_row rm_right">
            <div class="rm_order_heading">
                <button id="rm_find_another_order_btn" class="btn btn-medium btn-warning" style='margin-top: 15px; font-weight:bold;'><span>{l s='Find another order' mod='returnmanager'}</span></button>
            </div>
        </div>
    </div>
    <div class="rm_clear"></div>
    <div class="rm_row" style="margin-bottom: 5px; margin-top: 10px;">
        <div class="rm_half_row">
            <div class="rm_order_heading"><div class="rm_label_orderstatus" style="background-color:black; color: white;">{l s='Order Detail' mod='returnmanager'}</div></div>
        </div>
    </div>
    {if count($orders) > 0}
        {foreach $orders as $order}
            <table class="rm_single_order_row" cellspaing="4" cellpadding="2">
                <tr class="rm_order_header">
                    {*changes by vishal on 18 aug 2020 for handling return functionality for admin*}
                    {if isset($enable_complete_order_return) && $enable_complete_order_return == 1}
                        <td style="width: 10px;" class="head-checkbox" onclick=""><input type="checkbox"/></td>
                    {/if}
                    {*changes end*}
                    <td class="rm_label_hightlight rm_left_pad7" colspan='2'>{$order['reference_id'] nofilter}</td>
                    <td class="rm_order_header rm_right_align" colspan='3'>
                        <div class="rm_label_orderstatus" style="background-color:{$order['order_state_color'] nofilter}">{$order['order_state'] nofilter}</div>
                    </td>
                    {*changes by vishal for adding order cancellaton functionality*}
                    {if isset($order['cancellable']) && $order['cancellable'] == 1}
                        <td style="width: 11%">
                            <button class="kb_rm_cancel_order" style='font-weight:bold;' onclick="getCancelForm('{$order['order_id'] nofilter}');"><span>{l s='Cancel Order' mod='returnmanager'}</span></button>
                        </td>
                    {/if}
                    {if isset($enable_complete_order_return) && $enable_complete_order_return == 1}
                        <td style="width: 20%">
                            <button class="kb_rm_return_order" style='font-weight:bold;' onclick="kbrmgetReturnForm(this);"><span>{l s='Return Selected Products' mod='returnmanager'}</span></button>
                        </td>
                    {/if}
                </tr>

                {foreach $order['products'] as $product}
                    <tr class="rm_pro_row">
                        {*changes by vishal on 18 aug 2020 for handling return functionality for admin*}
                        {if isset($enable_complete_order_return) && $enable_complete_order_return == 1}
                            <td>
                                {if $product['is_creditable'] || $product['is_refundable'] || $product['is_replacement']}
                                    {*
                                    Start Changes to fix the 500 error on return page
                                    Removed the nofilter from the assignment operations
                                    NAFeb2024 nofilter
                                    @date 09-02-2024
                                    @modifier Nikhil Aggarwal
                                    *}
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
                        {*changes end*}
                        <td class="rm_img_col"><a href="{$product['product_link'] nofilter}"><img src='{$product['pro_img'] nofilter}'  style="width:70%;" /></a></td>
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

                        <td class="{if $product['is_creditable'] || $product['is_refundable'] || $product['is_replacement']}{else}rm_center_align{/if}">
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
                                <button class="btn btn-medium btn-primary" style='font-weight:bold;' onclick="return getReturnForm('{$value_str  nofilter}', this);"><span>{l s='Return' mod='returnmanager'}</span></button>
                                    {/if}
                        </td>
                    </tr>
                {/foreach}
                <tr class="rm_single_order_total_row">
                    <td colspan='3' class="rm_left_pad7" style='text-align: left;'>{l s='Order placed on' mod='returnmanager'}: {$order['order_date'] nofilter}</td>
                    <td colspan='2' class="rm_right_pad7" style="text-align: right; font-weight: bold;"><div>{l s='Order Total' mod='returnmanager'}: {$order['cart_total'] nofilter}</div>
                        <div>{l s='Total Paid' mod='returnmanager'}: {$order['total_paid'] nofilter}</div>
                    </td>
                </tr>
            </table>
            <br>
        {/foreach}
    {else}
        <div class="rm_history_nodata">{l s='No Order Created yet' mod='returnmanager'}</div>
    {/if}
</div>
<script>
    $(document).ready(function () {
         $('.kb_rm_return_order').click(function () {
        return false;
    });
    $('.kb_rm_cancel_order').click(function () {
        return false;
    });
     $('.rm_order_header input[type="checkbox"]').click(function () {
        if ($(this).prop("checked") == true) {
            $(this).closest('.rm_single_order_row').find('input:checkbox').prop('checked', true);
        }
        else if ($(this).prop("checked") == false) {
            $(this).closest('.rm_single_order_row').find('input:checkbox').prop('checked', false);
        }
    });
    });
    function showhidetocOnMultipleForm(element){
    if ($('#rm_toc_block_'+ element).is(':visible')){
    $('#rm_toc_block_' + element).hide();
    } else{
    $('#rm_toc_block_' + element).show();
    }
    setLeftColHeight('rm_popup_request_form');
}
</script>
<style>
    #rm_popup_pro_info{
        background: white !important;
    }
    .rm_row returnDetails{
        background: rgb(66, 139, 202, 0.7);
    }
    .rm_popup_pro_img{
        margin-top: 20px;
    }
    .companyAddress{
        float:right;
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
* Return Manager Order Detail Page for Admin Panel
*}