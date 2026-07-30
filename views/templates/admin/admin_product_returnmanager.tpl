<div class="row">
    <div class="col-md-12">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2>{l s='Return Policy' mod='returnmanager'}
                        <span class="help-box" data-toggle="popover" data-content="{l s='Choose Return Policy for this product' mod='returnmanager'}" data-original-title="" title=""></span>
                    </h2>
                </div>
                <div class="col-md-12">
                    <div class="row form-group">
                        <div class="col-md-4" style="padding-bottom: 10px;">
                            <label class="form-control-label">{l s='Choose Return Policy' mod='returnmanager'}</label>
                            <select id="return_manager_policy_select" name="velsof_return_policy" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">
                                <option value="0">{l s='No Policy' mod='returnmanager'}</option>
                                {if isset($velsof_return_policy)}
                                    {foreach from=$policy item="policy_lang"}
                                        {if $policy_lang['return_data_id'] eq $velsof_return_policy}
                                            <option value="{$policy_lang['return_data_id'] nofilter}" selected='selected'>{$policy_lang['value'] nofilter}</option>
                                        {else}
                                            <option value="{$policy_lang['return_data_id'] nofilter}">{$policy_lang['value'] nofilter}</option>
                                        {/if}
                                    {/foreach}
                                {else if isset($velsof_default_return_policy)}
                                    {foreach from=$policy item="policy_lang"}
                                        {if $policy_lang['return_data_id'] eq $velsof_default_return_policy}
                                            <option value="{$policy_lang['return_data_id'] nofilter}" selected='selected'>{$policy_lang['value'] nofilter}</option>
                                        {else}
                                            <option value="{$policy_lang['return_data_id'] nofilter}">{$policy_lang['value'] nofilter}</option>
                                        {/if}
                                    {/foreach}
                                {else}
                                    {foreach from=$policy item="policy_lang"}
                                        <option value="{$policy_lang['return_data_id'] nofilter}">{$policy_lang['value'] nofilter}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info" role="alert">
                                        <i class="material-icons">help</i>
                                        <p class="alert-text">
                                            {l s='The Return Policy set here will be mapped for this product and this product can be returned through that policy conditions only.' mod='returnmanager'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
* Return Manager Product TPL File
*}