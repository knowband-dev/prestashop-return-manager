<div class="form-horizontal">
    <div class="form-group">
        {if $empty eq 0}
            <div class="col-lg-9">
                <div class="col-lg-3">                                    
                    <label class="control-label">{l s='Custom fields values' mod='returnmanager'}</label>
                </div>
            </div>
            {foreach from=$fields_data item=field_data}
                <div class="col-lg-9">
                    <div class="col-lg-3">
                        <p class="form-control-static" style="font-weight: bold;">{l s={$field_data['field_label'] nofilter} mod='returnmanager'}:</p>
                    </div>
                    <div class="col-lg-6">                        
                        <p class="form-control-static">{$field_data['field_value'] nofilter}</p>                        
                    </div>
                </div>
            {/foreach}
        {else}
            <div class="list-empty-msg">
                <i class="icon-warning-sign list-empty-icon"></i>
                {l s='No Custom Field Value found.' mod='returnmanager'}
            </div>
        {/if}
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
* @category  PrestaShop Module
* @author    velsof.com <support@velsof.com>
* @copyright 2017 Velocity Software Solutions Pvt Ltd
* @license   see file: LICENSE.txt
*
* Description
*
* 
*}