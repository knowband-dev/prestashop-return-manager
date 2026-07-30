{if isset($reasons) && $reasons neq ''}
    {$sno = 1}
    {assign var = 'reason_count'  value = count($reasons)}
    {foreach $reasons as $reason}
        <tr class="pure-table-odd">
            <td>{$sno nofilter}</td>
            <td>{$reason['value'] nofilter}</td>
            <td>{if $reason['whopayshipping'] eq 'c'} {l s='Customer' mod='returnmanager'} {else} {l s='Store Owner' mod='returnmanager'} {/if}</td>
            <td class="center" style="padding: 12px;">
                <a style="margin-top: -26px; cursor: pointer;" type="{$reason['return_data_id'] nofilter}" class="velsof-glyphicons2 glyphicons pencil" onclick="actionOnReason('{$reason['return_data_id'] nofilter}')"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Edit this return reason' mod='returnmanager'}"></i></a>
                    {if $reason_count neq 1}
                    <a style="margin-top: -26px; cursor: pointer;" type="{$reason['return_data_id'] nofilter}" class="velsof-glyphicons2 glyphicons bin" onclick="deleteAction('{$reason['return_data_id'] nofilter}', 'reason')"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Delete this return reason' mod='returnmanager'}"></i></a>
                    {else}
                    <a href="javascript:void(0)" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="{l s='Atleast one Reason is required. Hence you can not delete it.' mod='returnmanager'}" title='{l s='Note' mod='returnmanager'}:' style="margin-top: -26px;" class="velsof-glyphicons2 glyphicons bin rm_customer_notes"><i></i></a>
                        {/if}
            </td>
        </tr>
        {$sno = $sno + 1}
    {/foreach}
{/if}
<tr>
    <td colspan="3"></td>
    <td class="left center"><a onClick="actionOnReason(0)" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New Reason' mod='returnmanager'}</td>
</tr>

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
* Return Manager Refresh Reason Table
*}