{if isset($policy) && $policy neq ''}
    {$sno = 1}
    {foreach $policy as $policies}
        {$policy_string = ','|impl:$policies}
        <tr class="pure-table-odd">
		    <td>{$sno|escape:'htmlall':'UTF-8'}</td>
		    <td>{$policies['value']|escape:'htmlall':'UTF-8'}</td>
                    <td>{$policies['credit_min_days']|escape:'htmlall':'UTF-8'}</td>
		    <td>{if $policies['credit_days'] >= 0} {$policies['credit_days']|escape:'htmlall':'UTF-8'} {else} {l s='NA' mod='returnmanager'} {/if}</td>
		    <td>{$policies['refund_min_days']|escape:'htmlall':'UTF-8'}</td>
                    <td>{if $policies['refund_days'] >= 0} {$policies['refund_days']|escape:'htmlall':'UTF-8'} {else} {l s='NA' mod='returnmanager'} {/if}</td>
		    <td>{$policies['replacement_min_days']|escape:'htmlall':'UTF-8'} </td>
                    <td>{if $policies['replacement_days'] >=0 } {$policies['replacement_days']|escape:'htmlall':'UTF-8'} {else} {l s='NA' mod='returnmanager'} {/if} </td>
            <td class="center" style="padding: 12px;">
                <a style="margin-top: -26px;" href="javascript://" type="{$policies['return_data_id'] nofilter}" class="velsof-glyphicons2 glyphicons pencil" onclick="actionOnPolicy('{$policies['return_data_id'] nofilter}')"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Edit this return policy' mod='returnmanager'}"></i></a>
                <a style="margin-top: -26px;" href="javascript://" type="{$policies['return_data_id'] nofilter}" class="velsof-glyphicons2 glyphicons git_merge" onclick="productMapping(this);"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Map products to this return policy' mod='returnmanager'}"></i></a>
                <a style="margin-top: -26px;" href="javascript://" type="{$policies['return_data_id'] nofilter}" class="velsof-glyphicons2 glyphicons bin" onclick="deleteAction('{$policies['return_data_id'] nofilter}', 'policy')"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Delete this return policy' mod='returnmanager'}"></i></a>
            </td>
        </tr>
        {$sno = $sno + 1}
    {/foreach}
{/if}

<tr>
            <td colspan="8"></td>
    <td class="left center"><a style="cursor: pointer;" onClick="actionOnPolicy(0)" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New' mod='returnmanager'}</td>
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
* Return Manager Policies Table
*}






