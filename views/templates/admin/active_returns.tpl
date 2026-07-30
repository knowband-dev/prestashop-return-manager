{if count($return_history) > 0}
    <table class="pure-table velsof-pure-table">
    <thead>
        <tr style="background-color:#f2f2f2">
            <th>{l s='Email' mod='returnmanager'}</th>
            <th>{l s='Product' mod='returnmanager'}</th>            
            <th>{l s='Qty' mod='returnmanager'}</th>
            <th>{l s='Date' mod='returnmanager'}</th>
            <th>{l s='Type' mod='returnmanager'}</th>
            <th>{l s='Status' mod='returnmanager'}</th>
            <th>{l s='Action' mod='returnmanager'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $return_history as $return}
            <tr class='rm_pending_returns'>
                <td><a href="{$customer_controller nofilter}&id_customer={$return['customer_id'] nofilter}&viewcustomer" target="_blank">{$return['cust_email'] nofilter}</a></td>
                <td><b>{$return['product_name'] nofilter}</b><br>{if isset($return['product_attr']) and $return['product_attr'] != ''}{$return['product_attr'] nofilter}{else}<br>{/if}</td>
                <td>{$return['quantity'] nofilter}</td>
                <td>{$return['request_date'] nofilter}</td>
                <td>{$return['return_type'] nofilter}</td>
                <td>{$return['status'] nofilter}</td>
                <td class='rm_velsof_action'>
                    <a type="{$return['return_id'] nofilter}" onclick='denyRequest(this);' class="velsof-glyphicons glyphicons remove" title='{l s='Deny Return' mod='returnmanager'}'><i></i></a>
                    <a type="{$return['return_id'] nofilter}" onclick='changeReturnStatus(this);' class="velsof-glyphicons glyphicons edit" title='{l s='Change Status' mod='returnmanager'}'><i></i></a>
                    <a type="{$return['return_id'] nofilter}" onclick='viewReturnDetail(this)' class="velsof-glyphicons glyphicons history" title='{l s='View History' mod='returnmanager'}'><i></i></a>
                    <a type="{$return['return_id'] nofilter}" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="{if $return['comment'] neq ''}{$return['comment'] nofilter}{else}<span class='vss_italic_text'>{l s='No comments by customer.' mod='returnmanager'}</span>{/if}" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title='{l s='Customer Notes' mod='returnmanager'}'><i></i></a>
                    <a type="{$return['return_id'] nofilter}" onclick='completeReturn(this)' class="velsof-glyphicons glyphicons ok_2" title='{l s='Mark as Complete' mod='returnmanager'}'><i></i></a>
                </td>
            </tr>
        {/foreach}                                                                        
    </tbody>
</table>
{else}
    <div class="rm_no_data"><span>{l s='No Active requests found.' mod='returnmanager'}</span></div>
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
* Return Manager Active Returns Tab
*}