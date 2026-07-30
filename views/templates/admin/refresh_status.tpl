{if isset($status) && $status neq ''}
    {$sno = 1}
    {assign var = 'status_count' value = count($status)}
    {foreach $status as $statuss}
        <tr class="pure-table-odd">
            <td>{$sno nofilter}</td>
            <td>{$statuss['value'] nofilter}</td>
            <td class="center" style="padding: 12px;">
                <a style="margin-top: -26px; cursor: pointer;" type="{$statuss['return_data_id'] nofilter}" class="velsof-glyphicons2 glyphicons pencil" onClick="actionOnStatus('{$statuss['return_data_id'] nofilter}')"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Edit this return status' mod='returnmanager'}"></i></a>
                    {if $status_count neq 1}
                    <a style="margin-top: -26px; cursor: pointer;"  type="{$statuss['return_data_id'] nofilter}" class="velsof-glyphicons2 glyphicons bin" onclick="deleteAction('{$statuss['return_data_id'] nofilter}', 'status')"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Delete this return status' mod='returnmanager'}"></i></a>
                    {else}
                    <a href="javascript:void(0)" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="{l s='Atleast one Status is required. Hence you can not delete it.' mod='returnmanager'}" title='{l s='Note' mod='returnmanager'}:' style="margin-top: -26px;" class="velsof-glyphicons2 glyphicons bin rm_customer_notes"><i></i></a>
                        {/if}
            </td>
        </tr>
        {$sno = $sno + 1}
    {/foreach}
    <script>
        var kb_refresh_return_status = {$refresh_status nofilter};
        console.log(kb_refresh_return_status);
        var current_selected_status = $('select[name="velsof_return[status][default]"]').val();
        $('select[name="velsof_return[status][default]"]').html('');
        $.each(kb_refresh_return_status, function (key, value) {
            if (current_selected_status == value['return_data_id']) {
                $('select[name="velsof_return[status][default]"]').append('<option value="' + value['return_data_id'] + '" selected>' + value['value'] + '</option>');
            } else {
                $('select[name="velsof_return[status][default]"]').append('<option value="' + value['return_data_id'] + '">' + value['value'] + '</option>');
            }
        });
        delete kb_refresh_return_status;
    </script>
{/if}
<tr>
    <td colspan="2"></td>
    <td class="left center"><a onClick="actionOnStatus(0)" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New Status' mod='returnmanager'}</td>
</tr>
    <script>
        // console.log(typeof refresh_return_status);
        
    </script> 

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