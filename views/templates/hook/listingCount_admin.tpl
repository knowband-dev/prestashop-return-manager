<ul class="header-list component" id='return-listing-container' style='display:none;'>
    <li id="notification" class="dropdown">
        <a href="javascript:void(0);" class="notification dropdown-toggle notifs">
            <i class="icon-list"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right notifs_dropdown">
            <div class="notifications">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item active" style="width:100%;">
                        <a class="nav-link" data-toggle="tab" data-type="order" href="#return-listing-count" role="tab" id="listing-count-tab">{l s='Return Manager Listing Count' mod='returnmanager'}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="return-listing-count" role="tabpanel">
                        <a class="notif" href="{$module_path|escape:'htmlall':'UTF-8'}&return_listing=ordercanceled">
                            {l s='Pending Order Cancel List Count' mod='returnmanager'}: <strong>{$cancel_order_count|escape:'htmlall':'UTF-8'}</strong>
                        </a>
                        <a class="notif" href="{$module_path|escape:'htmlall':'UTF-8'}&return_listing=ordercomplete">
                            {l s='Completed Order Cancel List Count' mod='returnmanager'}: <strong>{$cancel_order_approved_count|escape:'htmlall':'UTF-8'}</strong>
                        </a>
                        <a class="notif" href="{$module_path|escape:'htmlall':'UTF-8'}&return_listing=pendingreturn">
                            {l s='Pending Returns List Count' mod='returnmanager'}: <strong>{$pending_return_count|escape:'htmlall':'UTF-8'}</strong>
                        </a>
                        <a class="notif" href="{$module_path|escape:'htmlall':'UTF-8'}&return_listing=activereturn">
                            {l s='Active Returns List Count' mod='returnmanager'}: <strong>{$active_return_count|escape:'htmlall':'UTF-8'}</strong>
                        </a>
                        <a class="notif" href="{$module_path|escape:'htmlall':'UTF-8'}&return_listing=canceledreturn">
                            {l s='Cancelled Returns List Count' mod='returnmanager'}: <strong>{$cancel_return_count|escape:'htmlall':'UTF-8'}</strong>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </li>
</ul>
<div class="component header-right-component" id="header-return-listing-container" style='display:none;'>
    <div id="return-listing" class="notification-center dropdown dropdown-clickable">
        <button class="btn notification dropdown-toggle" data-toggle="dropdown">
            <i class="material-icons">list</i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            <div class="notifications">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item active" style="width:100%;">
                        <a class="nav-link" data-toggle="tab" data-type="order" href="#return-listing-count" role="tab" id="listing-count-tab">{l s='Return Manager Listing Count' mod='returnmanager'}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="return-listing-count" role="tabpanel">
                        <a class="notif" href="{$module_path|escape:'htmlall':'UTF-8'}&return_listing=ordercanceled">
                            {l s='Pending Order Cancel List Count' mod='returnmanager'}: <strong>{$cancel_order_count|escape:'htmlall':'UTF-8'}</strong>
                        </a>
                        <a class="notif" href="{$module_path|escape:'htmlall':'UTF-8'}&return_listing=ordercomplete">
                            {l s='Completed Order Cancel List Count' mod='returnmanager'}: <strong>{$cancel_order_approved_count|escape:'htmlall':'UTF-8'}</strong>
                        </a>
                        <a class="notif" href="{$module_path|escape:'htmlall':'UTF-8'}&return_listing=pendingreturn">
                            {l s='Pending Returns List Count' mod='returnmanager'}: <strong>{$pending_return_count|escape:'htmlall':'UTF-8'}</strong>
                        </a>
                        <a class="notif" href="{$module_path|escape:'htmlall':'UTF-8'}&return_listing=activereturn">
                            {l s='Active Returns List Count' mod='returnmanager'}: <strong>{$active_return_count|escape:'htmlall':'UTF-8'}</strong>
                        </a>
                        <a class="notif" href="{$module_path|escape:'htmlall':'UTF-8'}&return_listing=canceledreturn">
                            {l s='Cancelled Returns List Count' mod='returnmanager'}: <strong>{$cancel_return_count|escape:'htmlall':'UTF-8'}</strong>
                        </a>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        if ($('#header-employee-container').length == 1) {
            $('#header-return-listing-container').insertAfter('#header-employee-container');
            $('#header-return-listing-container').show();
        } else {
            $('#return-listing-container').show();
        }
    });
</script>

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin tpl file
*}