{if isset($cancel_detail) && $cancel_detail neq ''}
    {$sno = 1}
    {assign var = 'cancel_count' value = count($cancel_detail)}
    {foreach $cancel_detail as $cancel_data}
        <tr class="pure-table-odd">
            <td>{$sno|escape:'htmlall':'UTF-8'}</td>
            <td>{$cancel_data['value']|escape:'htmlall':'UTF-8'}</td>
            <td class="center" style="padding: 12px;">
                <a style="margin-top: -26px; cursor: pointer;" type="{$cancel_data['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons pencil" onClick="actionOnCancel('{$cancel_data['return_data_id']|escape:'htmlall':'UTF-8'}')"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Edit this Cancel reason' mod='returnmanager'}"></i></a>
                    {if $cancel_count neq 1}
                    <a style="margin-top: -26px; cursor: pointer;"  type="{$cancel_data['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons bin" onclick="deleteAction('{$cancel_data['return_data_id']|escape:'htmlall':'UTF-8'}', 'cancel')"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Delete this Cancel reason' mod='returnmanager'}"></i></a>
                    {else}
                    <a href="javascript:void(0)" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="{l s='Atleast one Status is required. Hence you can not delete it.' mod='returnmanager'}" title='{l s='Note' mod='returnmanager'}:' style="margin-top: -26px;" class="velsof-glyphicons2 glyphicons bin rm_customer_notes"><i></i></a>
                        {/if}
            </td>
        </tr>
        {$sno = $sno + 1}
    {/foreach}
{/if}
<tr>
    <td colspan="2"></td>
    <td class="left center"><a onClick="actionOnCancel(0)" style=" text-decoration:none;" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New' mod='returnmanager'}</td>
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
* Return Manager Refresh Status Tab File
*}