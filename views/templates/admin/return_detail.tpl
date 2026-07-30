<div class="widget">
    <div class="widget-head">
        <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Return History' mod='returnmanager'}</h3>
    </div>

    <div class="widget-body velsof-widget-body">
        <div class='rm_return_history left-flot'>
            <div class="rm_pop_heading">{l s='Order Detail' mod='returnmanager'}</div>
            <div class='rm_product_data'>
                <div class='rm_product_name'><b>{l s='Order' mod='returnmanager'}:</b> <a href="{$order_controller nofilter}&id_order={$return_detail[1]['id_order'] nofilter}&vieworder" target="_blank">{$return_detail[1]['order_reference'] nofilter}</a></div>
                <div class='rm_product_name'><b>{l s='Order Date' mod='returnmanager'}:</b> {$return_detail[1]['order_date'] nofilter}</div>
                <div class='rm_product_name'><b>{l s='Order Status' mod='returnmanager'}:</b> <div class="rm_label_orderstatus" style="background-color:{$return_detail[1]['order_status_color'] nofilter}">{$return_detail[1]['order_status'] nofilter}</div></div>
                <div class='rm_product_name'><b>{l s='Order Total' mod='returnmanager'}:</b> {$return_detail[1]['order_total'] nofilter}</div>
                <div class='rm_product_name'><b>{l s='Shipping Charges' mod='returnmanager'}:</b> {$return_detail[1]['order_shipping'] nofilter}</div>
                <div class='rm_product_name'><b>{l s='Shipping Paid By' mod='returnmanager'}:</b> {if $return_detail[1]['whopayshipping'] eq 'c'}{l s='Customer' mod='returnmanager'}{else}{l s='Store Owner' mod='returnmanager'}{/if}</div>
                <div class='rm_product_name'><b>{l s='Return Reason' mod='returnmanager'}:</b> {$return_detail[1]['reason'] nofilter}</div>
            </div>
        </div>
        <div class='rm_return_history right-flot'>
            <div class="rm_pop_heading">{l s='Customer Detail' mod='returnmanager'}</div>
            <div class='rm_product_data'>
                <div class='rm_product_name'><b>{l s='Name' mod='returnmanager'}:</b> {$return_detail[1]['cust_name'] nofilter}</div>
                <div class='rm_product_name'><b>{l s='Email' mod='returnmanager'}:</b> {$return_detail[1]['email'] nofilter}</div>
                <div class='rm_product_name'><b>{l s='Shipping Address' mod='returnmanager'}:</b> <br>{$return_detail[1]['shipping_address'] nofilter}</div>{*Variable contains html content, escape not required*}
            </div>
        </div>
        <br><br><div class="rm_pop_heading">{l s='Returned Item Detail' mod='returnmanager'}</div>
        <div class='rm_product_data'>
            <span class='rm_product_img'><img src='{$return_detail[1]['pro_img'] nofilter}' height='90px' width="90px"/></span>
            <span class='rm_product_name'><b><a href="{$return_detail[1]['product_link'] nofilter}" target="_blank">{$return_detail[1]['product_name'] nofilter}</a></b></span>
            <br>{if $return_detail[1]['product_attr'] != ''}<span class='rm_product_name'>{$return_detail[1]['product_attr'] nofilter}</span>{else}<span class='rm_product_name'>&nbsp;</span>{/if}
            <br><span class='rm_product_name'><b>{l s='Quantity' mod='returnmanager'}:</b> {$return_detail[1]['quantity'] nofilter}</span>
            <br><span class='rm_product_name'><b>{l s='Price' mod='returnmanager'}:</b> {$return_detail[1]['unit_price_tax_incl'] nofilter}</span>
        </div>        
    </div>
</div>

<div class="widget">
    <div class="widget-head">
        <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Return Status History' mod='returnmanager'}</h3>
    </div>
    <div class="widget-body">
        <table class="return-man-tab">
            <thead>
                <tr>
                    <th>{l s='Sr. No.' mod='returnmanager'}</th>
                    <th>{l s='Status' mod='returnmanager'}</th>
                    <th>{l s='Changed On' mod='returnmanager'}</th>
                </tr>
            </thead>
            <tbody>
                {$sr = 1}
                {foreach $return_detail[0] as $status}
                    <tr>
                        <td>{$sr nofilter}</td>
                        <td>{$status['status'] nofilter}</td>
                        <td>{$status['date'] nofilter}</td>
                    </tr>
                    {$sr=$sr+1}
                {/foreach}
            </tbody>
        </table>                                                    
    </div>
</div>

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
* Return Manager Return Detail File
*}


