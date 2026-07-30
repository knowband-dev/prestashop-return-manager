{if isset($address) && $address neq ''}
{$sno = 1}
{assign var = 'address_count' value = count($address)}
{foreach $address as $addresses}
<tr class="pure-table-odd">
    <td>{$sno|escape:'htmlall':'UTF-8'}</td>
    <td>{$addresses['title']|escape:'htmlall':'UTF-8'}</td>
    <td class="center" style="padding: 12px;">
    <a style="margin-top: -26px; cursor: pointer; padding: 15px;" type="{$addresses['id_address']|escape:'htmlall':'UTF-8'}"  onClick="actionOnAddress({$addresses['id_address']|escape:'htmlall':'UTF-8'})"><i class="icon-edit"  data-toggle="tooltip" data-placement="top" data-original-title="{l s='Edit this return address' mod='returnmanager'}"></i></a>
    <a style="margin-top: -26px; cursor: pointer; padding: 15px;"  type="{$addresses['id_address']|escape:'htmlall':'UTF-8'}"  onclick="disableAddress({$addresses['id_address']|escape:'htmlall':'UTF-8'},{$addresses['active']|escape:'htmlall':'UTF-8'})">
       {if $addresses['active'] eq 1}
        <i  class="icon-check" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Disable this return address' mod='returnmanager'}"></i>
        {else}
        <i  class="icon-remove" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Enable this return address' mod='returnmanager'}"></i>
        {/if}
    </a>                                                                                                
</td>
</tr>
{$sno = $sno + 1}
{/foreach}
{/if}
<tr>
    <td colspan="2"></td>
    <td class="left center"><a onClick="actionOnAddress(0)" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New address' mod='returnmanager'}</td>
</tr>

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
* Return Manager Refresh address Tab File
*}