{* changes done by rishabh on 11th july 2018 to validate address form *}
<script>
        velovalidation.setErrorLanguage({
            empty_field: "{l s='Field cannot be empty.' mod='returnmanager'}",
            html_tags: "{l s='Field should not contain HTML tags..' mod='returnmanager'}",
            empty_zip: "{l s='Please enter zip code.' mod='returnmanager'}",
            maxchar_zip: "{l s='Zip cannot be greater than 10 characters.' mod='returnmanager'}",
            minchar_zip: "{l s='Zip cannot be less than 4 characters.' mod='returnmanager'}",
            minchar_address: "{l s='Address cannot be less than 1 characters.' mod='returnmanager'}",
            maxchar_address: "{l s='Address cannot be greater than 128 characters.' mod='returnmanager'}",
            maxchar_city: "{l s='City cannot be greater than 3 characters.' mod='returnmanager'}",
            minchar_city: "{l s='City cannot be less than 128 characters.' mod='returnmanager'}",
            
        });
        var number_of_states = {$number_state nofilter};
        {*Start Changes to add the UTF-8 for the escaping
          This was reported in the PS Validation of the module
          NAFeb2024 UTF-8
          @date 09-02-2024
          @modifier Nikhil Aggarwal*}
        var rm_ajax_action = "{$action|escape:'quotes':'UTF-8'}";{*Variable Contains Html content,escape not required*}
        // Changes end by Nikhil
        var no_text = "{l s='No' mod='returnmanager'}";
        var yes_text = "{l s='Yes' mod='returnmanager'}";
        var canNotLeaveAllBoxesEmpty = "{l s='Can not leave all the language boxes empty. Please fill at least one box.' mod='returnmanager'}";
        var pleaseProvideInValidFormat1 = "{l s='Please provide value(s) in valid format.' mod='returnmanager'}";
        var pleaseProvideInValidFormat2 = "{l s='valid format is' mod='returnmanager'}";
        var pleaseProvideInValidFormat = pleaseProvideInValidFormat1 + " " +'(' + pleaseProvideInValidFormat2 + ': value|Label)';
        var text_box_txt = "{l s='Text Box' mod='returnmanager'}";
        var radio_button_txt = "{l s='Radio Buttons' mod='returnmanager'}";
        var text_area_txt = "{l s='Text Area' mod='returnmanager'}";
        var select_box_txt = "{l s='Select Box' mod='returnmanager'}";
        var check_boxes_txt = "{l s='Check Boxes' mod='returnmanager'}";
        var areYouSureToDelete = "{l s='Are you sure to delete the row?' mod='returnmanager'}";
        var not_applicable_msg = "{l s='N/A' mod='returnmanager'}";
        var rm_custom_field_text = "{l s='Custom Field Data' mod='returnmanager'}";
        var module_link = "{$module_link nofilter}"
</script>
{* Chnages over *}
<div id="velsof_rm_container" class="content" style="width: 100%;">
    <div class="box">       
         <div class="navbar main hidden-print" style="background: #fff;margin-bottom:1%;">
           <!-- Brand & save buttons -->
           <ul class="pull-left">
               <div style="position: inherit;color: black;font-size: 15px;min-width: 700px;padding-left: 50px;padding-top: 5px;">
                   <img id="rm_messages_loader" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/mosaic-pattern.png">
                   {l s='Have some doubt or issue? Get prompt help from us.' mod='returnmanager'}
               </div>
               <li class="themer_eyedropper" data-toggle="collapse" data-target="#themer"></li>
           </ul>
           <div class="topbuttons">                
                <a href="#" onclick='return submitform("sub")'><span id="save_post_setting" class="btn btn-block btn-success action-btn">{l s='Save' mod='returnmanager'}</span></a>&nbsp;&nbsp;&nbsp;<a href="{$cancel_action|escape:'htmlall':'UTF-8'}"><span class="btn btn-block btn-danger action-btn">{l s='Cancel' mod='returnmanager'}</span></a>
           </div>
       </div>

        {*<div class="navbar main hidden-print" style="width: 100%;">
            <div class="topbuttons">
                <a href="#" onclick='return submitform("sub")'><span id="save_post_setting" class="btn btn-block btn-success action-btn">{l s='Save' mod='returnmanager'}</span></a>&nbsp;&nbsp;&nbsp;<a href="{$cancel_action|escape:'htmlall':'UTF-8'}"><span class="btn btn-block btn-danger action-btn">{l s='Cancel' mod='returnmanager'}</span></a>
            </div>
        </div>*}
        <div class="velsof-container" style="width: 100%;">
            <div class="widget velsof-widget-left" style="width: 100%;">
                <div class="widget-body velsof-widget-left" style="width: 100%; padding: 0px !important">
                    <div id="wrapper" style="width: 100%;">
                        <div id="menuVel" class="hidden-print ui-resizable"  style="position: static">
                            <div class="slimScrollDiv">
                                <div class="slim-scroll">
                                    <ul>
                                        {* Start Code Added by Priyanshu on 18-March-2021 to implement the functionality to show Return listing count on Top of the Admin panel *}
                                        {$active1 = ""}
                                        {$active2 = ""}
                                        {$active3 = ""}
                                        {$active4 = ""}
                                        {$active5 = ""}
                                        {$active6 = ""}
                                        {if $active_listing == 'default'}
                                            {$active1 = "active"}
                                        {elseif $active_listing == 'ordercanceled'}
                                            {$active2 = "active"}
                                        {elseif $active_listing == 'ordercomplete'}
                                            {$active3 = "active"}
                                        {elseif $active_listing == 'pendingreturn'}
                                            {$active4 = "active"}
                                        {elseif $active_listing == 'activereturn'}
                                            {$active5 = "active"}
                                        {elseif $active_listing == 'canceledreturn'}
                                            {$active6 = "active"}
                                        {/if}
                                        {* End Code Added by Priyanshu on 18-March-2021 to implement the functionality to show Return listing count on Top of the Admin panel *}
                                        <li class="{$active1 nofilter}"><a class="glyphicons settings" href="#tab_general_settings" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon1.png"><span>{l s='General Settings' mod='returnmanager'}</span></a></li>
                                        <li class=""><a class="glyphicons embed_close" href="#tab_custom" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon2.png"><span>{l s='Custom CSS/JS ' mod='returnmanager'}</span></a></li>
                                        <li class=""><a class="glyphicons print" href="#tab_return_slip" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon3.png"><span>{l s='Return Slip Settings' mod='returnmanager'}</span></a></li>
                                        <li class=""><a class="glyphicons group" href="#tab_group" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon4.png"><span>{l s='Return Policies' mod='returnmanager'}</span><span class='error-triangle' id='save-policy-warning'></span></a></li>
                                        <li class=""><a class="glyphicons circle_question_mark" href="#tab_reason" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon5.png"><span>{l s='Return Reasons' mod='returnmanager'}</span></a></li>                                        
                                        <li class=""><a class="glyphicons circle_question_mark" href="#tab_cancel_reason" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon6.png"><span>{l s='Cancel Reasons' mod='returnmanager'}</span></a></li>
                                        <li class=""><a class="glyphicons check" href="#tab_status" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon7.png"><span>{l s='Return Statuses' mod='returnmanager'}</span></a></li>
                                        <li class=""><a class="glyphicons check" href="#tab_addresses" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon8.png"><span>{l s='Return Addresses' mod='returnmanager'}</span></a></li>
                                        <li class=""><a class="glyphicons circle_plus" href="#tab_create_return" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon9.png"><span>{l s='Create a Return' mod='returnmanager'}</span></a></li>
                                        <li class=""><a class="glyphicons message_new" href="#tab_email_templates" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon10.png"><span>{l s='Email Templates' mod='returnmanager'}</span></a></li>
                                        {*changes by vishal for adding order cancel functionlity*}
                                        <li class="{$active2 nofilter}"><a class="glyphicons list" href="#tab_cancel_list" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon11.png"><span>{l s='Pending Order Cancel List' mod='returnmanager'}</span></a></li>
                                        <li class="{$active3 nofilter}"><a class="glyphicons list" href="#tab_cancel_complete_list" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon12.png"><span>{l s='Completed Order Cancel List' mod='returnmanager'}</span></a></li>
                                        {*changes end*}
                                        <li class="{$active4 nofilter}"><a class="glyphicons list" href="#tab_return_list" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon13.png"><span>{l s='Pending Returns List' mod='returnmanager'}</span></a></li>
                                        <li class="{$active5 nofilter}"><a class="glyphicons cardio" href="#tab_return_list_active" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon14.png"><span>{l s='Active Returns List' mod='returnmanager'}</span></a></li>
                                        <li class="{$active6 nofilter}"><a class="glyphicons cardio" href="#tab_return_list_cancel" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon15.png"><span>{l s='Cancelled Returns List' mod='returnmanager'}</span></a></li>
                                        <li class=""><a class="glyphicons wallet" href="#tab_archive_list" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon16.png"><span>{l s='Archives List' mod='returnmanager'}</span></a></li>
                                        <li class=""><a class="glyphicons embed_close" href="#tab_custom_fields" data-toggle="tab"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon17.png"><span>{l s='Custom Field' mod='returnmanager'}</span></a></li>{* Code Added By Priyasnhu on 28-FEB-2020 to implement Custom Field Functionality*}
                                        <li class=""><a class="glyphicons bookmark" target="_blank" href="https://addons.prestashop.com/en/149_knowband"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/admin_icon/icon18.png"><span>{l s='Other Plugins' mod='returnmanager'}</span></a></li>
                                    </ul>
                                    <div class="clearfix"></div>
                                    <div class="separator bottom"></div>
                                </div>
                            </div>
                            <div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>
                        </div>

                        <div id="content_knowband">
                            <div class="box">
                                <div class="content tabs">
                                    <form action="{$action|escape:'htmlall':'UTF-8'}" name="returnmanager_form" action="" method="post" enctype="multipart/form-data" id="returnmanager_configuration_form">
                                        <input type='hidden' id='submit_form' name='submit_form' value=''>
                                        <input type='hidden' name='velsof_return_link[link_html]' value='{if isset($velsof_return['link_html'])}{$velsof_return['link_html']|escape:'htmlall':'UTF-8'}{else}<a id="return_manager_opener" href="VELSOF_LINK" style=" float: right; display: block; color: white; font-weight: bold; padding: 8px 10px 11px 10px; text-shadow: 1px 1px rgba(0, 0, 0, 0.2); cursor: pointer; line-height: 18px;">LINK_TEXT</a>{/if}' />  {*Variable Contains Html content, escape not required*}
                                        <input type='hidden' name='velsof_return_link[link_html_class]' value='{if isset($velsof_return['link_html_class'])}{$velsof_return['link_html_class']|escape:'htmlall':'UTF-8'}{else}.nav nav{/if}' />
                                        <div class="layout">
                                            <div class="tab-content even-height">
                                                <!--------------- Start - General Setings -------------------->
                                                <div id="tab_general_settings" class="tab-pane {$active1 nofilter}">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='General Settings' mod='returnmanager'}</h4>
                                                        <table class="form">
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Enable/Disable plugin' mod='returnmanager'}">{l s='Enable/Disable' mod='returnmanager'}: </span>
                                                                    
                                                                </td>
                                                                {*<td class="name vertical_top_align"><span class="control-label">{l s='Enable/Disable' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Enable/Disable plugin' mod='returnmanager'}"></i>
                                                                </td>*}
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable']) and $velsof_return['enable'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable]" id="return_enable" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable]" id="return_enable"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Enable/Disable to delete customer return data on GDPR module delete request.' mod='returnmanager'}">{l s='Delete Customer Data On Delete Request' mod='returnmanager'}: </span>
                                                                    <p class="help" style="font-size: 11px;"><b>{l s='Note' mod='returnmanager'}: </b>{l s='Enable/Disable to delete customer return data on GDPR module delete request.' mod='returnmanager'}</p>
                                                                </td>
                                                                {*<td class="name vertical_top_align"><span class="control-label">{l s='Delete Customer Data On Delete Request' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Enable/Disable to delete customer return data on GDPR module delete request.' mod='returnmanager'}"></i>
                                                                    <p class="help" style="font-size: 11px;"><b>{l s='Note' mod='returnmanager'}: </b>{l s='Enable/Disable to delete customer return data on GDPR module delete request.' mod='returnmanager'}</p>
                                                                </td>*}
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_gdpr_delete']) and $velsof_return['enable_gdpr_delete'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_gdpr_delete]" id="enable_gdpr_delete" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_gdpr_delete]" id="enable_gdpr_delete"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            {*changes done by Kanishka Kannoujia on 17-06-2022 to Need to Provide Enable/Disable option for the "Return" Button on the header*}
                                                            <tr>
                                                                
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Enable/Disable Header Menu' mod='returnmanager'}">{l s='Enable/Disable Header Menu' mod='returnmanager'}: </span>
                                                                    
                                                                </td>
                                                                {*<td class="name vertical_top_align"><span class="control-label">{l s='Enable/Disable File Upload' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Enable/Disable image upload option' mod='returnmanager'}"></i>
                                                                </td>*}
                                                                <td class="settings">
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_header_menu]" id="return_enable_header_menu"/>
                                                                        </div>                                                                </td>
                                                            </tr>
                                                            {*changes end by Kanishka Kannoujia on 17-06-2022 to Need to Provide Enable/Disable option for the "Return" Button on the header*}
                                                            <tr>
                                                                
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Enable/Disable image upload option' mod='returnmanager'}">{l s='Enable/Disable File Upload' mod='returnmanager'}: </span>
                                                                    
                                                                </td>
                                                                {*<td class="name vertical_top_align"><span class="control-label">{l s='Enable/Disable File Upload' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Enable/Disable image upload option' mod='returnmanager'}"></i>
                                                                </td>*}
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_image_upload']) and $velsof_return['enable_image_upload'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_image_upload]" id="return_enable_image_upload" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_image_upload]" id="return_enable_image_upload"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            {* changes started by rishabh jain on 9th July 2019 for custom admin chat functionality *}
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='If enabled then customer can create a ticket for their return request status enquiry.' mod='returnmanager'}">{l s='Enable/Disable Chat Feature' mod='returnmanager'}: </span>
                                                                    
                                                                </td>
                                                                {*<td class="name vertical_top_align"><span class="control-label">{l s='Enable/Disable Chat Feature' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='If enabled then customer can create a ticket for their return request status enquiry.' mod='returnmanager'}"></i>
                                                                </td>*}
                                                                <td class="settings">                                                                  
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_chat]" id="return_enable_chat"/>
                                                                        </div>
                                                                </td>
                                                            </tr>
                                                            {*changes by vishal for adding cancel functionality*}
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='If enabled then customer can create request for cancel his order.' mod='returnmanager'}">{l s='Enable/Disable Order Cancel Functionality' mod='returnmanager'}: </span>
                                                                    
                                                                </td>
                                                                {*<td class="name vertical_top_align"><span class="control-label">{l s='Enable/Disable Chat Feature' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='If enabled then customer can create a ticket for their return request status enquiry.' mod='returnmanager'}"></i>
                                                                </td>*}
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_cancel']) and $velsof_return['enable_cancel'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_cancel]" id="return_enable_cancel" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_cancel]" id="return_enable_cancel"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                {*<td class="name vertical_top_align"><span class="control-label"><span class="asterisk">*</span>{l s='Select Order Status on which return is allowed' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='The return will be allowed only on the selected Order status.' mod='returnmanager'}"></i>
                                                                </td>*}
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='The Order cancellation will be allowed only on the selected Order status.' mod='returnmanager'}">{l s='Select Order Status on which Order cancellation is allowed' mod='returnmanager'}: </span>
                                                                    
                                                                </td>
                                                                <td class="settings">
                                                                    <select multiple="multiple" id='kb_cancel_statuses' name="kb_cancel_statuses">
                                                                        {foreach $available_order_status as $key => $order_status}
                                                                            <option value="{$order_status['id_option']|escape:'htmlall':'UTF-8'}" {if in_array($order_status['id_option'] ,$selected_cancel_status)} selected {/if}>{$order_status['name']|escape:'htmlall':'UTF-8'}</option>
                                                                        {/foreach}
                                                                    </select>
                                                                    {*<p style="font-size: 13px; color: #3F51B5;"><b>{l s='Note' mod='returnmanager'} : </b>{l s='If no order status are selected then .' mod='returnmanager'}</p>*}
                                                                </td>
                                                            </tr>
                                                            {*changes end*}
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label">{l s='Allow Customer to Cancel Return Request' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='If enabled then customer can cancel the return request.' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_cancel_return']) and $velsof_return['enable_cancel_return'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_cancel_return]" id="return_enable_cancel_return" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_cancel_return]" id="return_enable_cancel_return"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='If enabled then the return will be allowed only on the selected order status otherwise return will be enabled for orders which has been delievered.' mod='returnmanager'}">{l s='Enable/disable order status selection' mod='returnmanager'}: </span>
                                                                    
                                                                </td>
                                                                {*<td class="name vertical_top_align"><span class="control-label">{l s='Enable/disable order status selection' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='If enabled then the return will be allowed only on the selected order status otherwise return will be enabled for orders which has been delievered.' mod='returnmanager'}"></i>
                                                                </td>*}
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_order_status_selection']) and $velsof_return['enable_order_status_selection'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_order_status_selection]" id="enable_order_status_selection" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_order_status_selection]" id="enable_order_status_selection"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='If enabled then the Product Selection option will be shown on front in case of a replacement' mod='returnmanager'}">{l s='Enable/disable product selection for replacement' mod='returnmanager'}: </span>
                                                                </td>
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_product_selection_replacement']) and $velsof_return['enable_product_selection_replacement'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_product_selection_replacement]" id="enable_product_selection_replacement" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_product_selection_replacement]" id="enable_product_selection_replacement"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='If enabled then customer will be able to return complete order by selecting all products' mod='returnmanager'}">{l s='Enable/disable Complete Order Return' mod='returnmanager'}: </span>
                                                                </td>
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_order_return']) and $velsof_return['enable_order_return'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_order_return]" id="enable_order_return" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_order_return]" id="enable_order_return"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                {*<td class="name vertical_top_align"><span class="control-label"><span class="asterisk">*</span>{l s='Select Order Status on which return is allowed' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='The return will be allowed only on the selected Order status.' mod='returnmanager'}"></i>
                                                                </td>*}
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='The return will be allowed only on the selected Order status.' mod='returnmanager'}">{l s='Select Order Status on which return is allowed' mod='returnmanager'}: </span>
                                                                    
                                                                </td>
                                                                <td class="settings">
                                                                    <select multiple="multiple" id='kb_order_statuses' name="kb_order_statuses">
                                                                        {foreach $available_order_status as $key => $order_status}
                                                                            <option value="{$order_status['id_option']|escape:'htmlall':'UTF-8'}" {if in_array($order_status['id_option'] ,$selected_order_status)} selected {/if}>{$order_status['name']|escape:'htmlall':'UTF-8'}</option>
                                                                        {/foreach}
                                                                    </select>
                                                                    {*<p style="font-size: 13px; color: #3F51B5;"><b>{l s='Note' mod='returnmanager'} : </b>{l s='If no order status are selected then .' mod='returnmanager'}</p>*}
                                                                </td>
                                                            </tr>
                                                            {* changes over *}
                                                            {* Start Code Added By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status *}
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='If enabled then the return policy will start from the date of the selected Order Status.' mod='returnmanager'}">{l s='Enable/disable Order Status selection for Return Policy' mod='returnmanager'}: </span>
                                                                </td>
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_order_status_selection_return_policy']) and $velsof_return['enable_order_status_selection_return_policy'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_order_status_selection_return_policy]" id="enable_order_status_selection_return_policy" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_order_status_selection_return_policy]" id="enable_order_status_selection_return_policy"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label">{l s='Select Order Status from which Return policies are applicable' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='The return policy will be start from the date of the selected Order status.' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <div class='span4'>
                                                                        <select id='kb_policy_statuses' name="kb_policy_statuses">
                                                                            {foreach $available_order_status as $key => $order_status}
                                                                                <option value="{$order_status['id_option']|escape:'htmlall':'UTF-8'}" {if $order_status['id_option'] == $selected_policy_status} selected {/if}>{$order_status['name']|escape:'htmlall':'UTF-8'}</option>
                                                                            {/foreach}
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            {* End Code Added By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status *}
                                                            <tr>
                                                                {*<td class="name vertical_top_align"><span class="control-label">{l s='Success Messages Language' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign returnmanager-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Select the language you want to save the success messages.' mod='returnmanager'}"></i>
                                                                </td>*}
                                                                <td class="name vertical_top_align"><span class="control-label" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Select the language you want to save the success messages.' mod='returnmanager'}">{l s='Success Messages Language' mod='returnmanager'}: </span>
                                                                    
                                                                </td>
                                                                <td class="settings">
                                                                    <div class='span4'>
                                                                        <select name="velsof_return[success_messages_lang]" onchange="getMessagesData(this)">
                                                                            {foreach from=$languages item="lang"}
                                                                                <option value="{$lang['id_lang']|intval}">{$lang['name']|escape:'htmlall':'UTF-8'}</option>
                                                                            {/foreach}
                                                                        </select>
                                                                    </div>
                                                                    <img id="rm_messages_loader" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/loader_small.gif" style="display:none;">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label">{l s='Allowed Return Method' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Check which return method is  allowed to the customer ' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <label class="checkboxinline no-bold" style="margin-right:50px;font-weight: normal;">
                                                                        <input type="checkbox" style="float: left !important;margin-right: 5px;margin: 2px 4px;" class="checkbox input-checkbox-option vss_return_options" onchange="checkOtherReturnOptions(this)" id="return_credit" name="velsof_return[credit]" value="1" {if isset($velsof_return['credit']) && $velsof_return['credit'] eq 1}checked="checked"{/if} /><b>{l s='Credit' mod='returnmanager'}</b>
                                                                    </label>
                                                                    <label class="checkboxinline no-bold" style="margin-right:50px;font-weight: normal;">
                                                                        <input type="checkbox" style="float: left !important;margin-right: 5px;margin: 2px 4px;" class="checkbox input-checkbox-option vss_return_options" onchange="checkOtherReturnOptions(this)" name="velsof_return[refund]" value="1" {if isset($velsof_return['refund']) && $velsof_return['refund'] eq 1}checked="checked"{/if} /><b>{l s='Refund' mod='returnmanager'}</b>
                                                                    </label>
                                                                    <label class="checkboxinline no-bold" style="margin-right:50px;font-weight: normal;">
                                                                        <input type="checkbox" style="float: left !important;margin-right: 5px;margin: 2px 4px;" class="checkbox input-checkbox-option vss_return_options" onchange="checkOtherReturnOptions(this)" id="return_replacement" name="velsof_return[replacement]" value="1" {if isset($velsof_return['replacement']) && $velsof_return['replacement'] eq 1}checked="checked"{/if} /><b>{l s='Replacement' mod='returnmanager'}</b>
                                                                    </label>
                                                                </td>
                                                            {*changes by vishal for adding order cancellation functionality*}
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label"><i class="error-inline">* </i>{l s='Cancel Return Message' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='This message will appear after submitting Cancel request.' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <textarea id="rm_cancel_post_message" name="velsof_return_msg[cancel_post_message]" class="rm_texteditor" rows="10">{$velsof_return['cancel_post_message']|escape:'htmlall':'UTF-8'}</textarea>
                                                                </td>
                                                            </tr>
                                                            {**}    
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label"><i class="error-inline">* </i>{l s='Credit Return Message' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='This message will appear after submitting return request for credit return type.' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <textarea id="rm_credit_post_message" name="velsof_return_msg[credit_post_message]" class="rm_texteditor" rows="10">{$velsof_return['credit_post_message']|escape:'htmlall':'UTF-8'}</textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label"><i class="error-inline">* </i>{l s='Refund Return Message' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='This message will appear after submitting return request for refund return type.' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <textarea id="rm_refund_post_message" name="velsof_return_msg[refund_post_message]" class="rm_texteditor" rows="10">{$velsof_return['refund_post_message']|escape:'htmlall':'UTF-8'}</textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label"><i class="error-inline">* </i>{l s='Replacement Return Message' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='This message will appear after submitting return request for replacement return type.' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <textarea id="rm_replacement_post_message" name="velsof_return_msg[replacement_post_message]" class="rm_texteditor" rows="10">{$velsof_return['replacement_post_message']|escape:'htmlall':'UTF-8'}</textarea>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!--------------- End - General Settings -------------------->


                                                <!--------------- Start - Custom CSS/JS -------------------->
                                                <div id="tab_custom" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Custom CSS/JS' mod='returnmanager'}</h4>
                                                        <table class="form">
                                                            <tr>
                                                                <td class="name vertical_top_align">
                                                                    <span class="control-label">{l s='Custom CSS' mod='returnmanager'}: </span><i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Provide some CSS code for changes in the front end of ReturnManager' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <textarea rows="5" style="resize: both;" name="velsof_return_custom[css]" id="custom_css" ></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align">
                                                                    <span class="control-label">{l s='Custom JS' mod='returnmanager'}: </span><i class="icon-question-sign " data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Provide some javascript code for changes in the front end of ReturnManager' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <textarea rows="5" style="resize: both;" name="velsof_return_custom[js]"  id="custom_js"></textarea> 
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!--------------- End - Custom CSS/JS -------------------->

                                                <!--------------- Start - Return Slip Settings -------------------->
                                                <div id="tab_return_slip" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Return Slip Settings' mod='returnmanager'}</h4>
                                                        <table class="form">
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label">{l s='Enable/Disable Return Slip' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enable/Disable reutrn slip feature.' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_return_slip']) and $velsof_return['enable_return_slip'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_return_slip]" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_return_slip]" />
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label">{l s='Select Slip Language' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Select the language for which you want to save the return address and guidelines.' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <div class='span4'>
                                                                        <select name="velsof_return[return_slip_lang]" onchange="getReturnSlipData(this)">
                                                                            {foreach from=$languages item="lang"}
                                                                                <option value="{$lang['id_lang']|intval}">{$lang['name']|escape:'htmlall':'UTF-8'}</option>
                                                                            {/foreach}
                                                                        </select>
                                                                    </div>
                                                                    <img id="rm_return_slip_loader" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/loader_small.gif" style="display:none;">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label"><i class="error-inline">* </i>{l s='Return Address' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='This address will be printed on the reutrn slip and will also be displayed while a customer creates a return. Please make sure that it is saved in all the langugaes before enabling the plugin.' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <textarea id="rm_return_slip_address" name="velsof_return_slip[return_address]" class="rm_texteditor" rows="10">{$velsof_return['return_slip_address']|escape:'htmlall':'UTF-8'}</textarea>
                                                                    <p style="font-size: 13px; color: #3F51B5;"><b>{l s='Note' mod='returnmanager'} : </b>{l s='Please enter address in a proper format only because it will be used as it is.' mod='returnmanager'}</p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label"><i class="error-inline">* </i>{l s='Return Guidelines' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='These guidelines will be printed on the reutrn slip. Please make sure that they are saved in all the langugaes before enabling the plugin.' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    <textarea id="rm_return_slip_guidelines" name="velsof_return_slip[return_guidelines]" class="rm_texteditor" rows="10">{$velsof_return['return_slip_guidelines']|escape:'htmlall':'UTF-8'}</textarea>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!--------------- End - Return Slip Settings -------------------->

                                                <!--------------- Start - Return Policies -------------------->
                                                <div id="tab_group" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Return Policies' mod='returnmanager'}</h4>
                                                        {*changes by vishal on 28 dec 2020 to add the functionality for deleting all mapped categories*}
                                                        <img id="rm_mapping_loader" style="float:right;display:none;"src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                        <button type="button" id="delete_category_map" class="btn-block btn-success action-btn" style="float: right;margin-right: 5px;margin-top: -41px;margin-right: px;">{l s='Delete all mapped categories' mod='returnmanager'}</button>
                                                        {*changes end*}
                                                        <div id="default_policy" {if isset($policy) && count($policy) > 0}style="display:block;"{else}style="display:none;"{/if}>
                                                            <table class="form">                                                                                                                                
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Select Default Policy' mod='returnmanager'}: </span>                                                                
                                                                        <i class="icon-question-sign returnmanager-tooltip-color" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='On creation of a new return request it will be mapped to this policy by default if the product is not mapped to any policy.' mod='returnmanager'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div class='span4' id="default_policy_select">
                                                                            <select name="velsof_return[policy][default]" >
                                                                                <option value="0">{l s='No Policy' mod='returnmanager'}</option>
                                                                                {if isset($velsof_return['policy']['default'])}
                                                                                    {foreach from=$policy item="policy_lang"}
                                                                                        {if $policy_lang['return_data_id'] eq $velsof_return['policy']['default']}
                                                                                            <option value="{$policy_lang['return_data_id']|intval}" selected='selected'>{$policy_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                        {else}
                                                                                            <option value="{$policy_lang['return_data_id']|intval}">{$policy_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {foreach from=$policy item="policy_lang"}
                                                                                        <option value="{$policy_lang['return_data_id']|intval}">{$policy_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                    {/foreach}
                                                                                {/if}
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Exceptional Product Ids' mod='returnmanager'}: </span>                                                                
                                                                        <i class="icon-question-sign returnmanager-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Provide Exceptional Product ids seperated by comma(,) for which any policy is not applicable.' mod='returnmanager'}"></i>
                                                                        <p style="font-size: 11px; color: #666;"><b>{l s='Note' mod='returnmanager'}:</b>{l s='This field is for providing the exceptional product ids seperated by comma(,) for which any policy is not applicale.' mod='returnmanager'}</p>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <input type="text" name="velsof_return[policy][ex_product]" class="exception_id" id="ex_product" value="{if isset($velsof_return['policy']['ex_product']) && $velsof_return['policy']['ex_product'] != '' }{$velsof_return['policy']['ex_product']|escape:'htmlall':'UTF-8'}{/if}"/>
                                                                        <p class="help-block" style="padding-left: 10px;"> ({l s='For Example: 65,2323,87,983,22' mod='returnmanager'})</p>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Exceptional Category Ids' mod='returnmanager'}: </span>                                                                
                                                                        <i class="icon-question-sign returnmanager-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Provide Exceptional Category ids seperated by comma(,) for which any policy is not applicable.' mod='returnmanager'}"></i>
                                                                        {*<p style="font-size: 11px; color: #666;"><b>{l s='Note' mod='returnmanager'}:</b>{l s='This field is for providing the exceptional category ids seperated by comma(,) for which any policy is not applicale.' mod='returnmanager'}</p>*}
                                                                    </td>
                                                                    <td class="settings">
                                                                        <input type="text" name="velsof_return[policy][ex_category]" class="exception_id" id="ex_category" value="{if isset($velsof_return['policy']['ex_category']) && $velsof_return['policy']['ex_category'] != '' }{$velsof_return['policy']['ex_category']|escape:'htmlall':'UTF-8'}{/if}"/>
                                                                        <p class="help-block" style="padding-left: 10px;"> ({l s='For Example: 65,2323,87,983,22' mod='returnmanager'})</p>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br>
                                                        </div>
                                                        <div class="block policy_data_section" style="overflow:auto;"><!--Monika 11092019 added class-->
                                                            <div class="widget">
                                                                <div class="widget-head">
                                                                    <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Return Policies List' mod='returnmanager'}</h3>
                                                                </div>
                                                                <div class="widget-body">
                                                                    <div id="policy_data">
                                                                        <table class="pure-table" style='width: 100%;'>
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="font-weight: normal;">{l s='#id' mod='returnmanager'}</th>
                                                                                    <th style="font-weight: normal;">{l s='Policy' mod='returnmanager'}</th>
                                                                                    <th style="font-weight: normal;">{l s='Credit (Min days)' mod='returnmanager'} ({l s='in days' mod='returnmanager'})</th>
                                                                                    <th style="font-weight: normal;">{l s='Credit (Max days)' mod='returnmanager'} ({l s='in days' mod='returnmanager'})</th>
                                                                                    <th style="font-weight: normal;">{l s='Refund (Min days)' mod='returnmanager'} ({l s='in days' mod='returnmanager'})</th>
                                                                                    <th style="font-weight: normal;">{l s='Refund (Max days)' mod='returnmanager'} ({l s='in days' mod='returnmanager'})</th>
                                                                                    <th style="font-weight: normal;">{l s='Replacement (Min days)' mod='returnmanager'} ({l s='in days' mod='returnmanager'})</th>
                                                                                    <th style="font-weight: normal;">{l s='Replacement (Max days)' mod='returnmanager'} ({l s='in days' mod='returnmanager'})</th>
                                                                                    <th style="font-weight: normal; width: 22%;">{l s='Action' mod='returnmanager'}</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="policy_records">
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
                                                                                            <td>{if $policies['replacement_days'] >= 0} {$policies['replacement_days']|escape:'htmlall':'UTF-8'} {else} {l s='NA' mod='returnmanager'} {/if} </td>
                                                                                            <td class="center" style="padding: 12px;">
                                                                                                <a style="margin-top: -26px;" href="javascript://" type="{$policies['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons pencil" id="edit_return_policy"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Edit this return policy' mod='returnmanager'}"></i></a>
                                                                                                <a style="margin-top: -26px;" href="javascript://" type="{$policies['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons git_merge" onclick="productMapping(this);"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Map categories to this return policy' mod='returnmanager'}"></i></a>
                                                                                                <a style="margin-top: -26px;" href="javascript://" type="{$policies['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons bin" id="delete_return_policy" ><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Delete this return policy' mod='returnmanager'}"></i></a>
                                                                                            </td>
                                                                                        </tr>
                                                                                        {$sno = $sno + 1}
                                                                                    {/foreach}
                                                                                {/if}
                                                                                <tr>
                                                                                    <td colspan="8"></td>
                                                                                    <td class="left center"><a style="cursor: pointer; text-decoration:none;" id="new_return_policy" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New' mod='returnmanager'}</td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>

                                                                        <div class="modal fade" id="modal_policy"  tab-index="-1" aria-hidden="true" aria-labelledby="modal_policy">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header" style="text-align: center;">
                                                                                        <button type="button" class="close" onclick="closeModalForm('modal_policy')"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                        <h4 class="modal-title velsof_modal_title" id="modal-policy" >{l s='Policy Form' mod='returnmanager'}</h4>
                                                                                    </div>
                                                                                    <div class="modal-body left-flot" style="min-height: 100px;" >
                                                                                        <div id="manual-policy-form" >
                                                                                            <input type="hidden" name="policy_action_type" value="0"/>
                                                                                            <table id="velsof_add_new_policy_table" class="form">
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td class="name vertical_top_align rm_table_label_col_width" ><i class="error-inline">* </i>{l s='Policy Title' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enter your policy title here' mod='returnmanager'}"></i></td>
                                                                                                        <td class="settings" style="padding-left:5px;">
                                                                                                            {$i = 0}
                                                                                                            {foreach from=$languages item='lang'}
                                                                                                                <div class="input-row-margin-bottom" style="display: inline-flex;">
                                                                                                                    <div class='span0'><img src="{$img_lang_dir|escape:'quotes':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" height="11px" width="16px" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div>
                                                                                                                    <div class="span4">
                                                                                                                        <input type="text" id="policy_new{$lang['id_lang']|escape:'htmlall':'UTF-8'}" class="add_policy_new rm_modal_input" name="policy_new_{$lang['id_lang']|escape:'htmlall':'UTF-8'}" placeholder="{l s='Enter Policy' mod='returnmanager'}" style="width: 95%;"/>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                {$i = $i+1}
                                                                                                            {/foreach}
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr><td class="name vertical_top_align rm_table_label_col_width"><i class="error-inline">* </i>{l s='Policy Terms & Conditions' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Write your terms and conditions for this policy' mod='returnmanager'}"></i></td>
                                                                                                        <td class="settings">
                                                                                                            {$i = 0}
                                                                                                            {foreach from=$languages item='lang'}
                                                                                                                <div class="row input-row-margin-bottom">
                                                                                                                    <div class='span0'><img src="{$img_lang_dir|escape:'quotes':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" height="11px" width="16px" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div>
                                                                                                                    <div class="span4">
                                                                                                                        <textarea type="text" rows="7" id="policy_new_term{$lang['id_lang']|escape:'htmlall':'UTF-8'}" class="add_policy_new_term rm_modal_input" name="policy_new_term_{$lang['id_lang']|escape:'htmlall':'UTF-8'}" style="width: 95%;"></textarea>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                {$i = $i+1}
                                                                                                            {/foreach}
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td class="name vertical_top_align rm_table_label_col_width">{l s='Credit' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Days upto which the credit is possible' mod='returnmanager'}"></i></td>
                                                                                                        <td class="settings">
                                                                                                            <div class="row input-row-margin-bottom">
                                                                                                                <div class='span4'>
                                                                                                                    <input id="credit_check" name="credit_check" type="checkbox" onchange="toggleStatus(this);"/>
                                                                                                                    <span>{l s='Min :' mod='returnmanager'}</span>
                                                                                                                    <input class="rm_policy_options_day_input" id="credit_min" type="text" disabled="disabled" name="credit_min" /> &nbsp;({l s='in days' mod='returnmanager'})
                                                                                                                    <span>{l s='Max :' mod='returnmanager'}</span>
                                                                                                                    <input class="rm_policy_options_day_input" id="credit_max" type="text" disabled="disabled" name="credit_max"/> &nbsp;({l s='in days' mod='returnmanager'})
                                                                                                                </div>
                                                                                                                
                                                                                                                <div id="rm_credit_box" class="span4 rm_policy_options_text">
                                                                                                                    {$i = 0}
                                                                                                                    {foreach from=$languages item='lang'}
                                                                                                                        <div class='span0'><img src="{$img_lang_dir|escape:'quotes':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" height="11px" width="16px" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div>
                                                                                                                        <textarea name="rm_credit_text_{$lang['id_lang']|escape:'htmlall':'UTF-8'}"></textarea>
                                                                                                                        {$i = $i+1}
                                                                                                                    {/foreach}                                                                                                                                                                                                                                        
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td class="name vertical_top_align rm_table_label_col_width">{l s='Refund' mod='returnmanager'}: <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Days upto which the refund is possible' mod='returnmanager'}"></i></td>
                                                                                                        <td class="settings">
                                                                                                            <div class="row input-row-margin-bottom">
                                                                                                                <div class='span4'>
                                                                                                                    <input id="refund_check" name="refund_check" type="checkbox" onchange="toggleStatus(this);" />
                                                                                                                    <span>{l s='Min :' mod='returnmanager'}</span>
                                                                                                                    <input class="rm_policy_options_day_input" id="refund_min" type="text" disabled="disabled" name="refund_min"/> &nbsp;({l s='in days' mod='returnmanager'})
                                                                                                                    <span>{l s='Max :' mod='returnmanager'}</span>
                                                                                                                    <input class="rm_policy_options_day_input" id="refund_max" type="text" disabled="disabled" name="refund_max"/> &nbsp;({l s='in days' mod='returnmanager'})
                                                                                                                    {*<input class="rm_policy_options_day_input" id="Refund" type="text" disabled="disabled" name="refund" /> &nbsp;({l s='in days' mod='returnmanager'})*}
                                                                                                                </div>
                                                                                                                <div id="rm_refund_box" class="span4 rm_policy_options_text">
                                                                                                                    {$i = 0}
                                                                                                                    {foreach from=$languages item='lang'}
                                                                                                                        <div class='span0'><img src="{$img_lang_dir|escape:'quotes':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" height="11px" width="16px" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div>
                                                                                                                        <textarea name="rm_refund_text_{$lang['id_lang']|escape:'htmlall':'UTF-8'}"></textarea>
                                                                                                                        {$i = $i+1}
                                                                                                                    {/foreach}                                                                                                                                                                                                                                       
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td class="name vertical_top_align rm_table_label_col_width">{l s='Replacement' mod='returnmanager'}: <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Days upto which the replacement is possible' mod='returnmanager'}"></i></td>
                                                                                                        <td class="settings">
                                                                                                            <div class="row input-row-margin-bottom">
                                                                                                                <div class='span4'>
                                                                                                                    <input name="replacement_check" id="replacement_check" type="checkbox"  onchange="toggleStatus(this);"/>
                                                                                                                    <span>{l s='Min :' mod='returnmanager'}</span>
                                                                                                                    <input class="rm_policy_options_day_input" id="replacement_min" type="text" disabled="disabled" name="replacement_min"/> &nbsp;({l s='in days' mod='returnmanager'})
                                                                                                                    <span>{l s='Max :' mod='returnmanager'}</span>
                                                                                                                    <input class="rm_policy_options_day_input" id="replacement_max" type="text" disabled="disabled" name="replacement_max"/> &nbsp;({l s='in days' mod='returnmanager'})
{*                                                                                                                    <input class="rm_policy_options_day_input" id="Replacement" type="text" disabled="disabled" name="replacement"/> &nbsp;({l s='in days' mod='returnmanager'})*}
                                                                                                                </div>
                                                                                                                <div id="rm_replacement_box" class="span4 rm_policy_options_text">
                                                                                                                    {$i = 0}
                                                                                                                    {foreach from=$languages item='lang'}
                                                                                                                        <div class='span0'><img src="{$img_lang_dir|escape:'quotes':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" height="11px" width="16px" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div>
                                                                                                                        <textarea name="rm_replacement_text_{$lang['id_lang']|escape:'htmlall':'UTF-8'}"></textarea>
                                                                                                                        {$i = $i+1}
                                                                                                                    {/foreach}                                                                                                                                                                                                                                       
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>                                                                                                
                                                                                            </table>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <img id="rm_policy_form_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                                        <button type="button" id="close_policy" class="btn btn-warning" onclick="closeModalForm('modal_policy')">{l s='Close' mod='returnmanager'}</button>
                                                                                        <button type="button" id="save_policy" class="btn btn-success">{l s='Save' mod='returnmanager'}</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal fade" id="modal_policy_product"  tab-index="-1" aria-hidden="true" aria-labelledby="modal_policy_product">
                                                                            <div class="modal-dialog" style="width: 65%;">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header" style="text-align: center;">
                                                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                        <h4 class="modal-title velsof_modal_title" id="modal_policy_product" >{l s='Policy Category Mapping' mod='returnmanager'}</h4>
                                                                                    </div>
                                                                                    <div class="modal-body" style="min-height: 100px;">
                                                                                        <div id="product-policy-form" >

                                                                                            <table style="width: 99.5%;">
                                                                                                <tr>
                                                                                                    <td class="name vertical_top_align"><span class="control-label"><span class="asterisk">*</span>{l s='Select Categories' mod='returnmanager'}: </span>
                                                                                                        <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Select desired categories' mod='returnmanager'}"></i>
                                                                                                    </td>
                                                                                                    <td class="settings">
                                                                                                        <select multiple="multiple" id='c_categories'>
                                                                                                            {foreach $category as $categ}
                                                                                                                {if $categ['id_category'] != ''}
                                                                                                                    <option value="{$categ['id_category']|escape:'htmlall':'UTF-8'}">{$categ['name']|escape:'htmlall':'UTF-8'}</option>
                                                                                                                {/if}
                                                                                                            {/foreach}
                                                                                                        </select>
                                                                                                        {*<span onclick = 'selectAllCategories();'>SElect all </span>
                                                                                                        <span onclick = 'unselectAllCategories();'>unSElect all </span>*}
                                                                                                        <p style="font-size: 13px; color: #3F51B5;"><b>{l s='Note' mod='returnmanager'} : </b>{l s='Policy would be decided on the basis of Default Category of the product.' mod='returnmanager'}</p>
                                                                                                    </td>

                                                                                                    <td class="name vertical_top_align"><span id="category_loader_cust" style="display: none; position: absolute;"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/load.gif" height="25px" width="25px"></span></td>
                                                                                                            {*<td class="name vertical_top_align"><span class="control-label"><span class="asterisk">*</span>{l s='Select Products' mod='returnmanager'}: </span>
                                                                                                            <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Select desired Products' mod='returnmanager'}"></i>
                                                                                                            </td>
                                                                                                            <td class="settings">
                                                                                                            <select id="c_products" name="cust_products" style="width:200px;">
                                                                                                            <option value="0">{l s='Select Product' mod='returnmanager'}</option>
                                                                                                            </select>
                                                                                                            </td>*}
                                                                                                </tr>
                                                                                            </table>


                                                                                        </div>
                                                                                        <div id="mapping_error_constant" class="error" style="font-size: 12px;"></div>
                                                                                        <div id="mapping_error" style="display: none; width: 30%; font-size: 12px;"></div>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <img id="rm_policy_product_mapping_form_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                                        <button type="button" id="close_produtct" class="btn btn-warning" data-dismiss="modal">{l s='Close' mod='returnmanager'}</button>
                                                                                        <input type="hidden" id="return_data_type" value="" />
                                                                                        <button onclick="map()" type="button" id="map_product" class="btn btn-success">{l s='Save' mod='returnmanager'}</button>
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
                                                <!--------------- End - Return Policies -------------------->

                                                <!--------------- Start - Return Reasons -------------------->
                                                <div id="tab_reason" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Return Reasons' mod='returnmanager'}</h4>
                                                        <div class="widget">
                                                            <div class="widget-head">
                                                                <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Returns Reason List' mod='returnmanager'}</h3>
                                                            </div>
                                                            <div class="widget-body">
                                                                <div id="product_data">
                                                                    <table class="pure-table" style='width: 100%;'>
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="font-weight: normal;">{l s='#id' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal; width: 50%">{l s='Reasons' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal;">{l s='Shipping Paid By' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal;">{l s='Action' mod='returnmanager'}</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="reason_records">
                                                                            {if isset($reasons) && $reasons neq ''}
                                                                                {$sno = 1}
                                                                                {assign var = 'reason_count'  value = count($reasons)}
                                                                                {foreach $reasons as $reason}
                                                                                    <tr class="pure-table-odd">
                                                                                        <td>{$sno|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td>{$reason['value']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td>{if $reason['whopayshipping'] eq 'c'} {l s='Customer' mod='returnmanager'} {else} {l s='Store Owner' mod='returnmanager'} {/if}</td>
                                                                                        <td class="center" style="padding: 12px;">
                                                                                            <a style="margin-top: -26px; cursor: pointer; text-decoration:none;" type="{$reason['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons pencil" id="edit_return_reason"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Edit this return reason' mod='returnmanager'}"></i></a>
                                                                                                {if $reason_count neq 1}
                                                                                                <a style="margin-top: -26px; cursor: pointer;" type="{$reason['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons bin" id="delete_return_reason"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Delete this return reason' mod='returnmanager'}"></i></a>
                                                                                                {else}
                                                                                                <a href="javascript:void(0)" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="{l s='Atleast one Reason is required . Hence you can not delete it.' mod='returnmanager'}" title='{l s='Note' mod='returnmanager'}:' style="margin-top: -26px;" class="velsof-glyphicons2 glyphicons bin rm_customer_notes"><i></i></a>
                                                                                                    {/if}
                                                                                        </td>
                                                                                    </tr>
                                                                                    {$sno = $sno + 1}
                                                                                {/foreach}
                                                                            {/if}
                                                                            <tr>
                                                                                <td colspan="3"></td>
                                                                                <td class="left center"><a  style=" text-decoration:none;" data-toggle="modal"  id= "add_return_reason"><span><i class="process-icon-new"></i></span></a>{l s='Add New' mod='returnmanager'}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>

                                                                    <div class="modal fade" id="modal_reason"  tab-index="-1" aria-hidden="true" aria-labelledby="modal_reason">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">

                                                                                <div class="modal-header" style="text-align: center;">
                                                                                    <button type="button" class="close" onclick="closeModalForm('modal_reason')"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                    <h4 class="modal-title velsof_modal_title" id="modal-reason" >{l s='Reason Form' mod='returnmanager'}</h4>
                                                                                </div>
                                                                                <div class="modal-body" style="min-height: 100px;">
                                                                                    <div id="manual-reason-form" >
                                                                                        <input type="hidden" name="reason_action_type" value="0" />
                                                                                        <table id="velsof_add_new_reason_table">
                                                                                            <tr><td class="velsof_bold" style="vertical-align: top; padding-top: 15px;"><i class="error-inline">* </i>{l s='Reason' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Provide reason to return products' mod='returnmanager'}"></i></td>
                                                                                                <td class="pad_td">
                                                                                                    {$i = 0}
                                                                                                    {foreach from=$languages item='lang'}
                                                                                                        <div class="row input-row-margin-bottom" style="display: inline-flex;">
                                                                                                            <div class='span0'><img src="{$img_lang_dir|escape:'quotes':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" height="11px" width="16px" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div>
                                                                                                            <div class="span6" style="width:340px;">
                                                                                                                <input type="text" class="add_reason_new rm_modal_input" name="reason_new_{$lang['id_lang']|escape:'htmlall':'UTF-8'}" placeholder="{l s='Enter Reason' mod='returnmanager'}"/>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        {$i = $i+1}
                                                                                                    {/foreach}
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr><td class="velsof_bold" style="padding-bottom: 10px;"><label>{l s='Shipping Paid By' mod='returnmanager'}:</label>
                                                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Who will pay the shipping charges' mod='returnmanager'}"></i>
                                                                                                </td>
                                                                                                <td class="pad_td">
                                                                                                    <input type="radio" value="so" name="charges" checked /> {l s='Store Owner' mod='returnmanager'}
                                                                                                    <input type="radio" value="c" name="charges"/> {l s='Customer' mod='returnmanager'}
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                        <br><br>

                                                                                        {*<div>
                                                                                        <label>{l s='Shipping Paid By' mod='returnmanager'}:</label>
                                                                                        <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Who will pay the shipping charges' mod='returnmanager'}"></i>
                                                                                        <input type="radio" value="so" name="charges" checked /> {l s='Store Owner' mod='returnmanager'}
                                                                                        <input type="radio" value="c" name="charges"/> {l s='Customer' mod='returnmanager'}
                                                                                        </div>*}
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <img id="rm_new_reason_form_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                                    <button type="button" id="close_reason" class="btn btn-warning" onclick="closeModalForm('modal_reason')">{l s='Close' mod='returnmanager'}</button>
                                                                                    <button type="button" id="save_reason" class="btn btn-success">{l s='Save' mod='returnmanager'}</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - Return Reasons -------------------->
                                                
                                                <!--------------- Start - Cancel Reasons -------------------->
                                                {*changes by vishal for adding cancel functionality*}
                                                <div id="tab_cancel_reason" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Cancel Reasons' mod='returnmanager'}</h4>
                                                        {if isset($cancel_detail) && $cancel_detail neq ''}
                                                            <table class="form">                                                                                                                                
                                                               {* <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Select Default Cancel' mod='returnmanager'}: </span>                                                                
                                                                        <i class="icon-question-sign returnmanager-tooltip-color" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='On creation of a new return request it will be mapped to this status by default.' mod='returnmanager'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div class='span4'>*}
                                                                            {*<select name="velsof_return[status][default]" >
                                                                                {if isset($velsof_return['status']['default'])}
                                                                                    {foreach from=$cancel_detail item="status_lang"}
                                                                                        {if $status_lang['return_data_id'] eq $velsof_return['status']['default']}
                                                                                            <option value="{$status_lang['return_data_id']|intval}" selected='selected'>{$status_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                        {else}
                                                                                            <option value="{$status_lang['return_data_id']|intval}">{$status_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {foreach from=$status item="status_lang"}
                                                                                        <option value="{$status_lang['return_data_id']|intval}">{$status_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                    {/foreach}
                                                                                {/if}
                                                                            </select>*}
                                                                     {*   </div>
                                                                    </td>
                                                                </tr>*}
                                                            </table><br>
                                                        {/if}
                                                        <div class="widget">
                                                            <div class="widget-head">
                                                                <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Cancel Reason List' mod='returnmanager'}</h3>
                                                            </div>
                                                            <div class="widget-body">
                                                                <div id="cancel_data">
                                                                    <table class="pure-table" style='width: 100%;'>
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="font-weight: normal;">{l s='#id' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal; width: 75%;">{l s='Status' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal;">{l s='Action' mod='returnmanager'}</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="cancel_records">
                                                                            {if isset($cancel_detail) && $cancel_detail neq ''}
                                                                                {$sno = 1}
                                                                                {assign var = 'cancel_count' value = count($cancel_detail)}
                                                                                {foreach $cancel_detail as $cancel_data}
                                                                                    <tr class="pure-table-odd">
                                                                                        <td>{$sno|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td>{$cancel_data['value']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="center" style="padding: 12px;">
                                                                                            <a style="margin-top: -26px; cursor: pointer;" type="{$cancel_data['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons pencil" id="edit_cancel_reasons"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Edit this Cancel reason' mod='returnmanager'}"></i></a>
                                                                                                {if $cancel_count neq 1}
                                                                                                <a style="margin-top: -26px; cursor: pointer;"  type="{$cancel_data['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons bin" id="delete_cancel_reasons"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Delete this Cancel reason' mod='returnmanager'}"></i></a>
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
                                                                                <td class="left center"><a id="add_cancel_reason" style=" text-decoration:none;" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New' mod='returnmanager'}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>

                                                                    <div class="modal fade" id="modal_cancel"  tab-index="-1" aria-hidden="true" aria-labelledby="modal_cancel">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header" style="text-align: center;">
                                                                                    <button type="button" class="close" onclick="closeModalForm('modal_cancel')"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                    <h4 class="modal-title velsof_modal_title" id="modal_cancel" >{l s='Cancel Reason Form' mod='returnmanager'}</h4>
                                                                                </div>
                                                                                <div class="modal-body" style="min-height: 100px;">
                                                                                    <div id="manual-cancel-form" >
                                                                                        <input type="hidden" name="cancel_action_type" value="0" />
                                                                                        <table id="velsof_add_new_status_table">
                                                                                            <tr>
                                                                                                <td class="velsof_bold" style="vertical-align: top;"><i class="error-inline">* </i>{l s='Reason' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Provide status of return process' mod='returnmanager'}"></i></td>
                                                                                                <td>
                                                                                                    {$i = 0}
                                                                                                    {foreach from=$languages item='lang'}
                                                                                                        <div class="row input-row-margin-bottom">
                                                                                                            <div class='span0' style="margin-left:15px;"><img src="{$img_lang_dir|escape:'quotes':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" height="11px" width="16px" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div>
                                                                                                            <div class="span6"  style="width:340px;">
                                                                                                                <input type="text" id="cancel_new{$lang['id_lang']|escape:'htmlall':'UTF-8'}" class="add_cancel_new rm_modal_input" name="cancel_new_{$lang['id_lang']|escape:'htmlall':'UTF-8'}" placeholder="{l s='Enter Cancel Reason' mod='returnmanager'}"/>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        {$i = $i+1}
                                                                                                    {/foreach}
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>

                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <img id="rm_new_cancel_form_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                                    <button type="button" id="close_cancel" class="btn btn-warning" onclick="closeModalForm('modal_cancel')">{l s='Close' mod='returnmanager'}</button>
                                                                                    <button type="button" id="save_cancel" class="btn btn-success">{l s='Save' mod='returnmanager'}</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {*changes end*}                                
                                                <!--------------- End - Cancel Reasons -------------------->

                                                <!--------------- Start - Create Status -------------------->
                                                <div id="tab_status" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Return Statuses' mod='returnmanager'}</h4>
                                                        {if isset($status) && $status neq ''}
                                                            <table class="form">                                                                                                                                
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Select Default Status' mod='returnmanager'}: </span>                                                                
                                                                        <i class="icon-question-sign returnmanager-tooltip-color" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='On creation of a new return request it will be mapped to this status by default.' mod='returnmanager'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div class='span4'>
                                                                            <select name="velsof_return[status][default]" >
                                                                                {if isset($velsof_return['status']['default'])}
                                                                                    {foreach from=$status item="status_lang"}
                                                                                        {if $status_lang['return_data_id'] eq $velsof_return['status']['default']}
                                                                                            <option value="{$status_lang['return_data_id']|intval}" selected='selected'>{$status_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                        {else}
                                                                                            <option value="{$status_lang['return_data_id']|intval}">{$status_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {foreach from=$status item="status_lang"}
                                                                                        <option value="{$status_lang['return_data_id']|intval}">{$status_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                    {/foreach}
                                                                                {/if}
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table><br>
                                                        {/if}
                                                        <div class="widget">
                                                            <div class="widget-head">
                                                                <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Return Status List' mod='returnmanager'}</h3>
                                                            </div>
                                                            <div class="widget-body">
                                                                <div id="status_data">
                                                                    <table class="pure-table" style='width: 100%;'>
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="font-weight: normal;">{l s='#id' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal; width: 75%;">{l s='Status' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal;">{l s='Action' mod='returnmanager'}</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="status_records">
                                                                            {if isset($status) && $status neq ''}
                                                                                {$sno = 1}
                                                                                {assign var = 'status_count' value = count($status)}
                                                                                {foreach $status as $statuss}
                                                                                    <tr class="pure-table-odd">
                                                                                        <td>{$sno|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td>{$statuss['value']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="center" style="padding: 12px;">
                                                                                            <a style="margin-top: -26px; cursor: pointer;" type="{$statuss['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons pencil" id="return_status_edit"><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Edit this return status' mod='returnmanager'}"></i></a>
                                                                                                {if $status_count neq 1}
                                                                                                <a style="margin-top: -26px; cursor: pointer;"  type="{$statuss['return_data_id']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons2 glyphicons bin" id="return_status_delete" ><i data-toggle="tooltip" data-placement="top" data-original-title="{l s='Delete this return status' mod='returnmanager'}"></i></a>
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
                                                                                <td class="left center"><a id="return_status_add" style=" text-decoration:none;" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New' mod='returnmanager'}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>

                                                                    <div class="modal fade" id="modal_status"  tab-index="-1" aria-hidden="true" aria-labelledby="modal_status">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header" style="text-align: center;">
                                                                                    <button type="button" class="close" onclick="closeModalForm('modal_status')"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                    <h4 class="modal-title velsof_modal_title" id="modal_status" >{l s='Status Form' mod='returnmanager'}</h4>
                                                                                </div>
                                                                                <div class="modal-body" style="min-height: 100px;">
                                                                                    <div id="manual-status-form" >
                                                                                        <input type="hidden" name="status_action_type" value="0" />
                                                                                        <table id="velsof_add_new_status_table">
                                                                                            <tr>
                                                                                                <td class="velsof_bold" style="vertical-align: top;"><i class="error-inline">* </i>{l s='Status' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Provide status of return process' mod='returnmanager'}"></i></td>
                                                                                                <td>
                                                                                                    {$i = 0}
                                                                                                    {foreach from=$languages item='lang'}
                                                                                                        <div class="row input-row-margin-bottom">
                                                                                                            <div class='span0' style="margin-left:15px;"><img src="{$img_lang_dir|escape:'quotes':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" height="11px" width="16px" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div>
                                                                                                            <div class="span6"  style="width:340px;">
                                                                                                                <input type="text" id="status_new{$lang['id_lang']|escape:'htmlall':'UTF-8'}" class="add_status_new rm_modal_input" name="status_new_{$lang['id_lang']|escape:'htmlall':'UTF-8'}" placeholder="{l s='Enter status' mod='returnmanager'}"/>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        {$i = $i+1}
                                                                                                    {/foreach}
                                                                                                </td>
                                                                                            </tr>
                                                                                            {* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status *}
                                                                                            <tr>
                                                                                                <td class="velsof_bold" style="vertical-align: top;"><i class="error-inline"></i>{l s='Status Text Message' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Add Custom Message for Particular Status' mod='returnmanager'}"></i></td>
                                                                                                <td>
                                                                                                    {$i = 0}
                                                                                                    {foreach from=$languages item='lang'}
                                                                                                        <div class="row input-row-margin-bottom">
                                                                                                            <div class='span0' style="margin-left:15px;"><img src="{$img_lang_dir|escape:'quotes':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" height="11px" width="16px" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div>
                                                                                                            <div class="span6"  style="width:340px;">
                                                                                                                <textarea type="text" id="status_text_new{$lang['id_lang']|escape:'htmlall':'UTF-8'}" class="add_status_text_new rm_modal_input" name="status_text_new_{$lang['id_lang']|escape:'htmlall':'UTF-8'}" placeholder="{l s='Enter status text' mod='returnmanager'}"></textarea>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        {$i = $i+1}
                                                                                                    {/foreach}
                                                                                                </td>
                                                                                            </tr>
                                                                                            {* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status *}
                                                                                        </table>

                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <img id="rm_new_status_form_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                                    <button type="button" id="close_status" class="btn btn-warning" onclick="closeModalForm('modal_status')">{l s='Close' mod='returnmanager'}</button>
                                                                                    <button type="button" id="save_status" class="btn btn-success">{l s='Save' mod='returnmanager'}</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - Create Status -------------------->
                                                <!--------------- Add Adderesses -------------------->
                                                <div id="tab_addresses" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Return Addresses' mod='returnmanager'}</h4>
                                                        <table class="form">
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label">{l s='Enable/Disable Multiple Addreess' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Enable/Disable multiple addresses' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_address']) and $velsof_return['enable_address'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_address]" id="return_enable_address" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_address]" id="return_enable_address"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <div class="widget">
                                                            <div class="widget-head">
                                                                <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Return Address List' mod='returnmanager'}</h3>
                                                            </div>
                                                            <div class="widget-body">
                                                                <div id="address_data">
                                                                    <table class="pure-table" style='width: 100%;'>
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="font-weight: normal;">{l s='#id' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal; width: 75%;">{l s='Address' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal;">{l s='Action' mod='returnmanager'}</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="address_records">
                                                                            {if isset($address) && $address neq ''}
                                                                            {$sno = 1}
                                                                            {assign var = 'address_count' value = count($address)}
                                                                            {foreach $address as $addresses}
                                                                            <tr class="pure-table-odd">
                                                                                <td>{$sno|escape:'htmlall':'UTF-8'}</td>
                                                                                <td>{$addresses['title']|escape:'htmlall':'UTF-8'}</td>
                                                                                <td class="center" style="padding: 12px;">
                                                                                        <a style="margin-top: -26px; cursor: pointer; padding: 15px;"  type="{$addresses['id_address']|escape:'htmlall':'UTF-8'}"  id="add_new_address3"><i data-toggle="tooltip"  class="icon-edit" data-placement="top" data-original-title="{l s='Edit this return address' mod='returnmanager'}"></i></a>
                                                                                        <a style="margin-top: -26px; cursor: pointer; padding: 15px;"  type="{$addresses['id_address']|escape:'htmlall':'UTF-8'}"  id="add_new_address4">
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
                                                                                <td class="left center"><a id="add_new_address1" style=" text-decoration:none;" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New address' mod='returnmanager'}</td>
                                                                    
                                                                    {*            <td class="left center"><a id="add_new_address2" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New address' mod='returnmanager'}</td>
                                                                        *}    </tbody>
                                                                    </table>

                                                                    <div class="modal fade" id="modal_address"  tab-index="-1" aria-hidden="true" aria-labelledby="modal_address">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header" style="text-align: center;">
                                                                                    <button type="button" class="close" onclick="closeModalForm('modal_address')"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                    <h4 class="modal-title velsof_modal_title" id="modal_status" >{l s='Address Form' mod='returnmanager'}</h4>
                                                                                </div>
                                                                                <div class="modal-body" style="min-height: 100px;">
                                                                                    <div id="manual-address-form" >
                                                                                        <input type="hidden" name="address_action_type" value="0" />
                                                                                        <input type="hidden" name="address_new_id" value="0" />
                                                                                        <table id="velsof_add_new_address_table">
                                                                                            <tr>
                                                                                                <td class="velsof_bold" style="vertical-align: top;padding: 10px;"><i class="error-inline">* </i>{l s='Address Title' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enter address title here' mod='returnmanager'}"></i></td>
                                                                                                <td style="padding: 10px;">
                                                                                                    <input type="text" id="address_new_title" class="add_address_new rm_modal_input" name="address_new_title" placeholder="{l s='Enter address title' mod='returnmanager'}"/>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td class="velsof_bold" style="vertical-align: top;padding: 10px;"><i class="error-inline">* </i>{l s='Address line 1' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enter first line of address.' mod='returnmanager'}"></i></td>
                                                                                                <td style="padding: 10px;">
                                                                                                    <input type="text" id="address_new_line1" class="add_address_new rm_modal_input" name="address_new_line1" placeholder="{l s='address line 1' mod='returnmanager'}"/>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td class="velsof_bold" style="vertical-align: top;padding: 10px;"><i class="error-inline"></i>{l s='Address line 2' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enter second line of address.you can also leave it empty' mod='returnmanager'}"></i></td>
                                                                                                <td style="padding: 10px;">
                                                                                                    <input type="text" id="address_new_line2" class="add_address_new rm_modal_input" name="address_new_line2" placeholder="{l s='address line 2' mod='returnmanager'}"/>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <!-- Zipcode -->
                                                                                            <tr>
                                                                                                <td class="velsof_bold" style="vertical-align: top;padding: 10px;"><i class="error-inline">* </i>{l s='Zipcode' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enter postcode/zipcode in this field' mod='returnmanager'}"></i></td>
                                                                                                <td style="padding: 10px;">
                                                                                                    <input type="text" id="address_new_zipcode" class="add_address_new rm_modal_input" name="address_new_zipcode" />
                                                                                                </td>
                                                                                            </tr>
                                                                                            
                                                                                            <!-- City -->
                                                                                            <tr>
                                                                                                <td class="velsof_bold" style="vertical-align: top;padding: 10px;"><i class="error-inline">*</i>{l s='City' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enter city of the return adddress' mod='returnmanager'}"></i></td>
                                                                                                <td style="padding: 10px;">
                                                                                                    <input type="text" id="address_new_city" class="add_address_new rm_modal_input" name="address_new_city" />
                                                                                                </td>
                                                                                            </tr>
                                                                                            <!--Country -->
                                                                                            <tr>
                                                                                                <td class="velsof_bold" style="vertical-align: top;padding: 10px;"><i class="error-inline"></i>{l s='Country' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Select Country from the dropdown' mod='returnmanager'}"></i></td>
                                                                                                <td style="padding: 10px;">
                                                                                                    <select id="address_new_country" class="form-control" name="address_new_country">{$countries_list nofilter}</select> {*Variable Contains Html content, escape not required*}
                                                                                                </td>
                                                                                            </tr>
                                                                                            <!-- State -->
                                                                                            <tr>
                                                                                                <td id="address_new_state_label" class="velsof_bold" style="vertical-align: top;padding: 10px;"><i class="error-inline"></i>{l s='State' mod='returnmanager'}:<i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Select state from the dropdown' mod='returnmanager'}"></i></td>
                                                                                                <td style="padding: 10px;">
                                                                                                    <select id="address_new_state" class="add_address_new rm_modal_input" name="address_new_state" placeholder="{l s='select state' mod='returnmanager'}"/>
                                                                                                        {foreach $state_list as $state}
                                                                                                            <option value="{$state['id_state']|intval}">{$state['name']|escape:'quotes':'UTF-8'}</option>
                                                                                                        {/foreach}
                                                                                                    </select>
                                                                                                </td>
                                                                                            </tr>
                                                                                            
                                                                                        </table>

                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <img id="rm_new_address_form_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                                    <button type="button" id="close_address" class="btn btn-warning" onclick="closeModalForm('modal_address')">{l s='Close' mod='returnmanager'}</button>
                                                                                    <button type="button" id="save_address" class="btn btn-success">{l s='Save' mod='returnmanager'}</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - ADD addressess -------------------->

                                                <!--------------- Start - Create Return -------------------->
                                                <div id="tab_create_return" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Create a Return' mod='returnmanager'}</h4>
                                                        <div class="block">
                                                            <div class="widget">
                                                                <div class="widget-head">
                                                                    <h4 class="heading">{l s='Find Order Here' mod='returnmanager'}</h4>
                                                                </div>
                                                                <div class="widget-body">
                                                                    <div id="velocity_returnmanager_result_div" class="rm_order_form_block">
                                                                        <div id="returnmanager_order_form">
                                                                            <div class="rm_row">
                                                                                <div id='error_div' class="rm_error_heading" style="display:none;"></div>
                                                                            </div>
                                                                            <div class="row">                                                                       
                                                                                <span class="span0 rm_filter_date rm_velsof_width">                                                                                     
                                                                                    <div class="row rm_filter_input_block">
                                                                                        <input type="text" value="" name="rm_customer_email" id="rm_customer_email" placeholder="{l s='Enter Email-id here' mod='returnmanager'}"/>
                                                                                    </div>
                                                                                </span>
                                                                                <span class="span0 rm_filter_date rm_velsof_width">
                                                                                    <div class="row rm_filter_input_block">
                                                                                        <input type="text" value="" name="rm_reference_id" id="rm_reference_id" placeholder="{l s='Enter Reference ID here' mod='returnmanager'}"/>
                                                                                    </div>
                                                                                </span>
                                                                                <span class="span0 rm_filter_date" style="width: 235px;">
                                                                                    <div class="row" style="margin-left:0">
                                                                                        <button class="btn btn-block btn-success velsof-button" id='find_order'><span>{l s='Find Order' mod='returnmanager'}</span></button>
                                                                                        <img id="rm_order_loader" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/loader_small.gif" style="display:none;">
                                                                                    </div>
                                                                                </span>                                                                        
                                                                            </div>

                                                                            {*<div class="rm_row">
                                                                            <div class="">
                                                                            <div class="rm_form_left"><span class="rm_label">{l s='Your Email' mod='returnmanager'}<span class="star_red">*</span></span></div>
                                                                            <div class="rm_pad6">
                                                                            <input type="text" value="" name="rm_customer_email" id="rm_customer_email" placeholder="{l s='Enter Email-id here' mod='returnmanager'}"/>
                                                                            </div>
                                                                            </div>
                                                                            <div class="">
                                                                            <div class="rm_form_left" ><span class="rm_label">{l s='Order Reference ID' mod='returnmanager'}<span class="star_red">*</span></span></div>
                                                                            <div class="rm_pad6" id='email_field'>
                                                                            <input type="text" value="" name="rm_reference_id" id="rm_reference_id" placeholder="{l s='Enter Reference ID here' mod='returnmanager'}"/>
                                                                            </div>
                                                                            </div>
                                                                            <div class="">
                                                                            <div class="" style="display:inline-block;">&nbsp;</div>
                                                                            <div class="">
                                                                            <button class="btn btn-block btn-success velsof-button" id='find_order'><span>{l s='Find Order' mod='returnmanager'}</span></button>
                                                                            </div>
                                                                            </div>
                                                                            </div> *}       
                                                                        </div>
                                                                    </div>
                                                                    <div id='rm_single_order_detail_container' class="rm_row velsof-box"></div>
                                                                    <div id="kb_rm_pop_up"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - Create Reutrn-------------------->

                                                <!--------------- Start - Email Templates -------------------->
                                                <div id="tab_email_templates" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Email Templates' mod='returnmanager'}</h4>
                                                        <div class="block" id="rm_email_template_form">
                                                            <table class="form">
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Template Language' mod='returnmanager'}: </span>
                                                                        <i class="icon-question-sign returnmanager-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Choose language of the email template' mod='returnmanager'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div class='span4'>
                                                                            <select id="rm_template_lang" name="rm_email[template_lang]" onchange="fetchTemplateData();">
                                                                                {foreach from=$languages item="lang"}
                                                                                    <option value="{$lang['id_lang']|intval}">{$lang['name']|escape:'htmlall':'UTF-8'}</option>
                                                                                {/foreach}
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Select Template' mod='returnmanager'}: </span>
                                                                        <i class="icon-question-sign returnmanager-tooltip-color" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Choose language of the email template' mod='returnmanager'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div class='span4'>
                                                                            <select name="rm_email[template_name]" id="rm_template_name" onchange="fetchTemplateData();">
                                                                                <option value="">{l s='Please Select...' mod='returnmanager'}</option>
                                                                                {foreach $templates_list as $name => $text}
                                                                                    <option value="{$name|escape:'htmlall':'UTF-8'}">{$text|escape:'htmlall':'UTF-8'}</option>
                                                                                {/foreach}
                                                                            </select>
                                                                        </div>
                                                                        <img id="rm_template_loader" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/loader_small.gif" style="display:none;">
                                                                    </td>
                                                                </tr>
                                                                <tr class="email_template_content_block" style="display: none;">
                                                                    <td class="name vertical_top_align">
                                                                        <span class="control-label"><i class="error-inline">* </i>{l s='Template Subject' mod='returnmanager'}: </span>
                                                                        <i class="icon-question-sign velsof-icon" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='This text will be sent as subject with the email' mod='returnmanager'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div class="span4 velsof_full_width">
                                                                            <input type="text" class="text-width" id="velsof_template_subject" name="rm_email[subject]" value=""/>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr class="email_template_content_block" style="display: none;">
                                                                    <td class="name vertical_top_align">
                                                                        <span class="control-label"><i class="error-inline">* </i>{l s='Template Content' mod='returnmanager'}: </span>
                                                                        <i class="icon-question-sign velsof-icon" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='This is the main content of the email template.' mod='returnmanager'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div class="span4 velsof_full_width">
                                                                            <textarea name="rm_email[content]" rows="10" aria-hidden="true" id="velsof_template_content" class="rm_texteditor"></textarea>
                                                                            <p style="font-size: 12px; margin-top: 3px; color: red;">{l s='Please do not edit or remove any template variable. Example' mod='returnmanager'}: <b>{literal}{any-variable}{/literal}</b></p>
                                                                            {* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status *}
                                                                            <p style="font-size: 12px; margin-top: 3px; color: red;">{l s='You can use the below placeholder in the Return Request Status Change email Template Only to add the Custom Status Text.' mod='returnmanager'}</p>
                                                                            <p style="font-size: 12px; margin-top: 3px; color: red;"><b>{literal}{custom_status_text}{/literal}</b></p>
                                                                            {* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status *}
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <div class="email_template_content_block modal-footer" style="display: none;">
                                                                <input type="hidden" id="hidden_template_id" name="rm_email[template_id]" value="0" />
                                                                <img id="rm_template_saving_loader" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/loader_small.gif" style="display:none;">
                                                                <button type="button" onclick="saveEmailTemplate();" class="btn btn-success">{l s='Save' mod='returnmanager'}</button>
                                                            </div>
                                                            {* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to send Test Email *}
                                                            <table class="form">
                                                                <tr class="test_email_block" style="display: none;">
                                                                    <td class="name vertical_top_align">
                                                                        <span class="control-label">{l s='Test Email' mod='returnmanager'}: </span>
                                                                        <i class="icon-question-sign velsof-icon" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Enter Email ID to send test Email.' mod='returnmanager'}"></i>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div class="span4 velsof_full_width">
                                                                            <input type="text" class="text-width" id="velsof_test_email" name="rm_email[test_email]" value=""/>
                                                                        </div>
                                                                    </td>
                                                                    <td class="settings">
                                                                        <div id="test_email_button" class="test_button">
                                                                            <a  class="btn btn-default " id="test_email">{l s='Send Test Email' mod='returnmanager'}</a>
                                                                            <img style="display:none" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/loader_small.gif" id="show_loader"/>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            {* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to send Test Email *}
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - Email Templates-------------------->
                                                
                                                {*changes by vishal for adding order cancel  fuctionality*}
                                                
                                                <!--------------- Start - View Pending Cancel list -------------------->
                                                <div id="tab_cancel_list" class="tab-pane {$active2 nofilter}">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='PENDING CANCEL REQUESTS' mod='returnmanager'}</h4>
                                                        <button type="button" id="refresh_cancel" class="btn btn-block btn-success action-btn" class="btn btn-block btn-success action-btn" style="float: right;margin-top: -41px;margin-right: 10px;">{l s='Refresh List' mod='returnmanager'}</button>
                                                        <div class="block">
                                                            {* Start Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs *}
                                                            <div class="widget">
                                                                <div class="widget-head">
                                                                    <h4 class="heading">{l s='Filter PENDING CANCEL REQUESTS' mod='returnmanager'}</h4>
                                                                </div>
                                                                <div class="widget-body" style="display:block;">
                                                                    <div class="row">
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Cancel Id' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_pending_cancel_custom_cancel_id" name="rm_pending_cancel_custom_cancel_id" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Customer Name' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_pending_cancel_customer_name" name="rm_pending_cancel_customer_name" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Order Id' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_pending_cancel_order_id" name="rm_pending_cancel_order_id" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Sort By' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <select name="rm_pending_cancel_sortby" >
                                                                                    <option value="od.date_update">{l s='Update Date' mod='returnmanager'}</option>
                                                                                    <option value="ods.reference">{l s='Order Reference' mod='returnmanager'}</option>
                                                                                </select>
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Sort Dir.' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <select name="rm_pending_cancel_sortdir" >
                                                                                    <option value="desc">{l s='Descending' mod='returnmanager'}</option>
                                                                                    <option value="asc">{l s='Ascending' mod='returnmanager'}</option>
                                                                                </select>
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>&nbsp;</h5>
                                                                            <div class="row" style="margin-left:0">
                                                                                <span class="btn btn-block velsof-btn-block btn-success" id="cancel_filter" >{l s='FILTER' mod='returnmanager'}</span>
                                                                                <span class="btn btn-block velsof-btn-block btn-primary" id="cancel_reset">{l s='Reset' mod='returnmanager'}</span>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {* End Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs *}
                                                            <div id="rm_pending_cancel_list_holder" class="rm_pending_cancel_tab">
                                                                <div class="tbl-blk">
                                                                    <div class="rm-bigloader"></div>
                                                                    <div class="policy-responsive"><!--Monika 11092019 added reponsive div-->
                                                                    <table class="pure-table" style="width:99%;">
                                                                        <thead>
                                                                            <tr>
                                                                                {* changes by rishabh jain to add return id column in tables*}
                                                                                <th style="width: 6%;">{l s='Cancel Id' mod='returnmanager'}</th>
                                                                                <th style="width: 6%;">{l s='Order' mod='returnmanager'}</th>
                                                                                <th style="width: 12%;">{l s='Customer' mod='returnmanager'}</th>
                                                                                <th style="width: 40%;">{l s='Reason' mod='returnmanager'}</th>
                                                                                <th style="width: 11%;text-align: center;">{l s='Action' mod='returnmanager'}</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="rm_pending_cancel_list">
                                                                            {if $cancel_pending['flag']}
                                                                                {$i = 0}
                                                                                {foreach $cancel_pending['data'] as $return}
                                                                                    <tr class='rm_pending_cancel_returns pure-table-{if $i%2 == 0}even{else}odd{/if}'>
                                                                                        {* changes by rishabh jain to add return id column in tables*}
                                                                                        <td>{$return['cancel_id']|escape:'htmlall':'UTF-8'}</td>
                                                                                        {*changes Modified by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL*}
                                                                                        <td><a href="{$order_controller1|escape:'htmlall':'UTF-8'}{$return['order_id']|escape:'htmlall':'UTF-8'}/view?{$order_controller2|escape:'htmlall':'UTF-8'}" target="_blank">{$return['order_reference']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                        <td><a href="{$customer_controller1|escape:'htmlall':'UTF-8'}{$return['customer_id']|escape:'htmlall':'UTF-8'}/edit?{$customer_controller2|escape:'htmlall':'UTF-8'}" target="_blank">{$return['cust_name']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                        {*changes Modified by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL*}
                                                                                        <td>{$return['reason']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class='rm_velsof_action'>
                                                                                            <a style="margin-top: -20px;" type="{$return['cancel_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" value="" style="cursor: pointer;" onclick='allowCancel(this);' class="velsof-glyphicons glyphicons ok" title='{l s='Approve Return' mod='returnmanager'}'><i></i></a>
                                                                                                    {*Edited by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer*}
                                                                                            <a style="margin-top: -20px;" type="{$return['cancel_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" onclick='denyCancel(this);' class="velsof-glyphicons glyphicons remove" title='{l s='Deny Return' mod='returnmanager'}'><i></i></a>
                                                                                                    {*Edited by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer*}
                                                                                            <a style="margin-top: -20px;" type="{$return['cancel_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="{$return['reason']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons glyphicons circle_question_mark rm_customer_notes" title='{l s='Return Reason' mod='returnmanager'}'><i></i></a>
                                                                                            <a style="margin-top: -20px;" type="{$return['cancel_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="{if $return['comment'] neq ''}{$return['comment']|escape:'htmlall':'UTF-8'}{else}<span class='vss_italic_text'>{l s='No comments by customer.' mod='returnmanager'}</span>{/if}" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title='{l s='Customer Notes' mod='returnmanager'}'><i></i></a>
                                                                                        </td>
                                                                                    </tr>
                                                                                    {$i = $i+1}
                                                                                {/foreach}
                                                                            {else}
                                                                                <tr><td colspan="9" rowspan="3"><div class="rm_no_data"><span>{l s='No Pending cancel requests found.' mod='returnmanager'}</span></div></td></tr>
                                                                                            {/if}
                                                                        </tbody>
                                                                    </table>
                                                                    </div>
                                                                    {*<div class="modal fade" id="modal_pending_custom_field_data" tab-index="-1" aria-hidden="true" aria-labelledby="modal-incentive-form">
                                                                        <div class="modal-dialog" style="width:50%">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <span class="font_popup_header">{l s='Custom Field Data' mod='returnmanager'}</span>
                                                                                    <button type="button" class="close" onclick="closeModalForm('modal_pending_custom_field_data')"><span aria-hidden="true">×</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                </div>
                                                                                <div class="modal-body" style="padding-bottom:0;">                                                                                    
                                                                                </div>
                                                                                <div class="modal-footer no_border">
                                                                                    <button type="button" onclick="closeModalForm('modal_pending_custom_field_data')" class="btn btn-default">{l s='Close' mod='returnmanager'}</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>*}
                                                                    <input id="rm_pending_cancel_current_page" type="hidden" name="rm_pending_cancel_current_page" value="1" />
                                                                </div>
                                                                <div class="paginator-block block">
                                                                    {$cancel_pending['pagination']|escape:'quotes':'UTF-8'}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="rm_approve_confirm_cancel"  tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                {*Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                <h4 class="modal-title velsof_modal_title">{l s='Mark As Approved?' mod='returnmanager'}</h4> 

                                                            </div>
                                                            <div class="modal-body">


                                                                {*Start Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                <div class="block">
                                                                    <label class="velsof-help"> {l s='This email will be sent to this customer. If you want to make any changes then you can or send as it is.' mod='returnmanager'}</label>

                                                                    <div class="row">
                                                                        <div>

                                                                            <div>    
                                                                                {* <input type="hidden" value="{$send_email_lang|escape:'htmlall':'UTF-8'}" id="send_email_lang">*}
                                                                                <label>{l s='Email Subject' mod='returnmanager'}:</label>
                                                                                <input type="text" name="subject_email_allow_cancel" id="subject_email_allow_cancel" value="">
                                                                            </div>
                                                                            <div>
                                                                                <label>{l s='Email Content' mod='returnmanager'}:</label>
                                                                                <textarea rows="10" aria-hidden="true" name="body_email_allow_cancel" id="body_email_allow_cancel" class="rm_texteditor"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {*End Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}




                                                            </div>
                                                            <div class="modal-footer">
                                                                <img id="rm_approve_return_popup_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                <button type="button" onclick="rmCloseModal('rm_approve_confirm_cancel')" class="btn btn-warning">{l s='Cancel' mod='returnmanager'}</button>
                                                                <button type="button" id="rm_yes_approve_cancel" class="btn btn-success">{l s='Submit' mod='returnmanager'}</button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                                
                                                <div class="modal fade" id="rm_select_state" tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            {*Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                            <h4 class="modal-title velsof_modal_title">{l s='Select the state for order before marking the cancel as complete' mod='returnmanager'}</h4>
                                                        </div>
                                                                        <div class="modal-body">

                                                                            {*Start Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                            <div class="block" style="text-align: center;">
                                                                                <div class="row" style="padding:2%;">
                                                                                    <div>

                                                                                        <div> 
                                                                                            <label>{l s='Select Order state' mod='returnmanager'}:</label>
                                                                                            <div class="make-switch" data-on="primary" data-off="default" style="border: 0px;">
                                                                                                <select name="kb_order_state" id="kb_order_state">
                                                                                                    {foreach $kb_order_data as $key=>$data}
                                                                                                    <option value="{$data['id_order_state'] nofilter}">{$data['name'] nofilter}</option>
                                                                                                    {/foreach}
                                                                                                </select>
                                                                                            </div>                                                                   
                                                                                        </div>
                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            {*End Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}


                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            {*<img id="rm_return_discount_coupon_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />*}
                                                                            <button type="button" onclick="rmCloseModal('rm_select_state')" class="btn btn-warning">{l s='Cancel' mod='returnmanager'}</button>
                                                                            <button type="button" id="rm_yes_cancel" class="btn btn-success">{l s='Submit' mod='returnmanager'}</button>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>            
                                                                
                                                <div class="modal fade" id="rm_deny_confirm_cancel"  tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                {*Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer*}                                                                
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                <h4 class="modal-title velsof_modal_title">{l s='Mark As Disapproved?' mod='returnmanager'}</h4>

                                                            </div>
                                                            <div class="modal-body">

                                                                {*Start Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                <div class="block">
                                                                    <label class="velsof-help"> {l s='This email will be sent to this customer. If you want to make any changes then you can or send as it is.' mod='returnmanager'}</label>

                                                                    <div class="row">
                                                                        <div>

                                                                            <div>    
                                                                                {*                                                                                <input type="hidden" value="{$send_email_lang|escape:'htmlall':'UTF-8'}" id="send_email_lang">*}
                                                                                <label>{l s='Email Subject' mod='returnmanager'}:</label>
                                                                                <input type="text" name="subject_email_deny_cancel" id="subject_email_deny_cancel" value="">
                                                                            </div>
                                                                            <div>
                                                                                <label>{l s='Email Content' mod='returnmanager'}:</label>
                                                                                <textarea rows="10" aria-hidden="true" name="body_email_deny_cancel" id="body_email_deny_cancel" class="rm_texteditor"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {*End Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}




                                                            </div>
                                                            <div class="modal-footer">
                                                                <img id="rm_deny_return_popup_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                <button type="button" onclick="rmCloseModal('rm_deny_confirm_cancel')" class="btn btn-warning">{l s='Cancel' mod='returnmanager'}</button>
                                                                <button type="button" id="rm_yes_deny_cancel" class="btn btn-success">{l s='Submit' mod='returnmanager'}</button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {* Changes by rishabh jain *}
                                                <div class="modal fade" id="rm_return_comment_modal"  tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                <h4 class="modal-title velsof_modal_title" id="modal-policy" >{l s='Internal Notes' mod='returnmanager'}</h4>
                                                            </div>
                                                            <div class="modal-body" id='rm_internal_notes'></div>
                                                            <div class="modal-footer">
                                                                <img id="rm_internal_note_complete_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                <button type="button" onclick="rmCloseModal('rm_return_comment_modal')"  class="btn btn-warning">{l s='Close' mod='returnmanager'}</button>
                                                                <button type="button" onclick="rmAddInternalNote()" id="rm_add_internal_note" class="btn btn-success">{l s='Save' mod='returnmanager'}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - View Pending Cancel list -------------------->
                                                
                                                <!--------------- Start - Completed Order Cancelled List -------------------->
                                                <div id="tab_cancel_complete_list" class="tab-pane {$active3 nofilter}">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Approved Cancelled Order List' mod='returnmanager'}</h4>
                                                        <div class="block">
                                                            <div class="innerLR">
                                                                {*<div class="widget">
                                                                    <div class="widget-head">
                                                                        <h4 class="heading">{l s='Filter Archive List' mod='returnmanager'}</h4>
                                                                    </div>
                                                                    <div class="widget-body" style="display:block;">
                                                                        <div class="row">
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='From Date' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_from_date" name="rm_from_date" value=""/>
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='To Date' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_to_date" name="rm_to_date" value="" />
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date" style="width: 235px;">
                                                                                <h5>&nbsp;</h5>
                                                                                <div class="row" style="margin-left:0">
                                                                                    <span class="btn btn-block velsof-btn-block btn-success" id="filter_complete_cancel" >{l s='FILTER' mod='returnmanager'}</span>
                                                                                    <span class="btn btn-block velsof-btn-block btn-primary" id="reset_complete_cancel" >{l s='Reset' mod='returnmanager'}</span>
                                                                                    <span class="btn btn-block velsof-btn-block btn-warning" id="export_complete_cancel">{l s='EXPORT' mod='returnmanager'}</span>
                                                                                    <img id="rm_loader" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/loader_small.gif" style="display:none;">
                                                                                </div>
                                                                            </span>
                                                                            <div id="rm_date_error" class="rm-date-error"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>*}
                                                                {* Start Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs *}
                                                                <div class="widget">
                                                                    <div class="widget-head">
                                                                        <h4 class="heading">{l s='Filter APPROVED CANCELLED ORDER LIST' mod='returnmanager'}</h4>
                                                                    </div>
                                                                    <div class="widget-body" style="display:block;">
                                                                        <div class="row">
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Cancel Id' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_complete_cancel_custom_cancel_id" name="rm_complete_cancel_custom_cancel_id" value="" />
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Customer Name' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_complete_cancel_customer_name" name="rm_complete_cancel_customer_name" value="" />
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Order Id' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_complete_cancel_order_id" name="rm_complete_cancel_order_id" value="" />
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Sort By' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <select name="rm_complete_cancel_sortby" >
                                                                                        <option value="od.date_update">{l s='Update Date' mod='returnmanager'}</option>
                                                                                        <option value="ods.reference">{l s='Order Reference' mod='returnmanager'}</option>
                                                                                    </select>
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Sort Dir.' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <select name="rm_complete_cancel_sortdir" >
                                                                                        <option value="desc">{l s='Descending' mod='returnmanager'}</option>
                                                                                        <option value="asc">{l s='Ascending' mod='returnmanager'}</option>
                                                                                    </select>
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>&nbsp;</h5>
                                                                                <div class="row" style="margin-left:0">
                                                                                    <span class="btn btn-block velsof-btn-block btn-success" id="order_cancel_filter">{l s='FILTER' mod='returnmanager'}</span>
                                                                                    <span class="btn btn-block velsof-btn-block btn-primary" id="order_cancel_reset">{l s='Reset' mod='returnmanager'}</span>
                                                                                </div>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {* End Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs *}
                                                                <div id="rm_list_cancel_container" class="">
                                                                    <div class="widget">
                                                                        <div class="widget-head">
                                                                            <h4 class="heading">{l s='Cancelled Order List' mod='returnmanager'}</h4>
                                                                        </div>
                                                                        <div class="row graph_container">
                                                                            <div id="rm_complete_cancel_list" style="width: 98%; margin: 6px auto; height:auto;">
                                                                                <div class="rm-bigloader"></div>
                                                                                {if $cancel_complete_order['flag']}
                                                                                    {$i = 0}
                                                                                    <table class="pure-table">
                                                                                        <thead>
                                                                                            <tr style="background-color:#f2f2f2">
                                                                                                {* changes by rishabh jain *}
                                                                                                <th style="width: 4%;">{l s='Cancel ID' mod='returnmanager'}</th>
                                                                                                {* changes over *}
                                                                                                <th style="width: 7%;">{l s='Order' mod='returnmanager'}</th>
                                                                                                <th style="width: 12%;">{l s='Customer' mod='returnmanager'}</th>
                                                                                                <th style="width: 40%;">{l s='Reason' mod='returnmanager'}</th>
                                                                                                <th style="width: 10%;text-align: center;">{l s='Action' mod='returnmanager'}</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody id="rm_cancel_archive_list_tbody">
                                                                                            {foreach $cancel_complete_order['data'] as $return}
                                                                                                <tr class="pure-table-{if $i%2 == 0}even{else}odd{/if}">
                                                                                                    {* changes by rishabh jain to add return id column in tables*}
                                                                                                    <td>{$return['cancel_id']|escape:'htmlall':'UTF-8'}</td>
                                                                                                    {*changes Modified by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL*}
                                                                                                    <td><a href="{$order_controller1|escape:'htmlall':'UTF-8'}{$return['order_id']|escape:'htmlall':'UTF-8'}/view?{$order_controller2|escape:'htmlall':'UTF-8'}" target="_blank">{$return['order_reference']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                                    <td><a href="{$customer_controller1|escape:'htmlall':'UTF-8'}{$return['customer_id']|escape:'htmlall':'UTF-8'}/edit?{$customer_controller2|escape:'htmlall':'UTF-8'}" target="_blank">{$return['cust_name']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                                    {*changes Modified by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL*}
                                                                                                    <td>{$return['reason']|escape:'htmlall':'UTF-8'}</td>
                                                                                                    <td class='rm_velsof_action' style="width: 25%;">
                                                                                                        <a style="margin-top: -20px;" type="{$return['cancel_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="{$return['reason']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons glyphicons circle_question_mark rm_customer_notes" title='{l s='Return Reason' mod='returnmanager'}'><i></i></a>
                                                                                                        <a style="margin-top: -20px;" type="{$return['cancel_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="{if $return['comment'] neq ''}{$return['comment']|escape:'htmlall':'UTF-8'}{else}<span class='vss_italic_text'>{l s='No comments by customer.' mod='returnmanager'}</span>{/if}" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title='{l s='Customer Notes' mod='returnmanager'}'><i></i></a>                                                                                                             
                                                                                                    </td>
                                                                                                </tr>
                                                                                                {$i = $i + 1}
                                                                                            {/foreach}
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <input id="rm_archive_cancel_current_page" type="hidden" name="rm_archive_cancel_current_page" value="1" />
                                                                                {else}
                                                                                    <div class="rm_no_data"><span>{l s='No data found' mod='returnmanager'}</span></div>
                                                                                        {/if}
                                                                            </div>
                                                                            <div class="paginator-block block">
                                                                                {$cancel_complete_order['pagination']|escape:'quotes':'UTF-8'}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - Complete Order Cancelled LIst -------------------->
                                                
                                                {*changes end*}

                                                <!--------------- Start - View Pending Return list -------------------->
                                                <div id="tab_return_list" class="tab-pane {$active4 nofilter}">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Pending Returns' mod='returnmanager'}</h4>
                                                        <button type="button" id="refresh_pending" class="btn btn-block btn-success action-btn" class="btn btn-block btn-success action-btn">{l s='Refresh List' mod='returnmanager'}</button>
                                                        <div class="block">
                                                            {* Start Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs *}
                                                            <div class="widget">
                                                                <div class="widget-head">
                                                                    <h4 class="heading">{l s='Filter Pending Returns List' mod='returnmanager'}</h4>
                                                                </div>
                                                                <div class="widget-body" style="display:block;">
                                                                    <div class="row">
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Return Id' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_pending_custom_return_id" name="rm_pending_custom_return_id" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Customer Name' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_pending_customer_name" name="rm_pending_customer_name" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Product Name' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_pending_product_name" name="rm_pending_product_name" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Order Id' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_pending_order_id" name="rm_pending_order_id" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Sort By' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <select name="rm_pending_sortby" >
                                                                                    <option value="od.date_update">{l s='Update Date' mod='returnmanager'}</option>
                                                                                    <option value="od.return_type">{l s='Type' mod='returnmanager'}</option>
                                                                                    <option value="ods.reference">{l s='Order Reference' mod='returnmanager'}</option>
                                                                                    <option value="pl.product_name">{l s='Product Name' mod='returnmanager'}</option>
                                                                                </select>
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Sort Dir.' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <select name="rm_pending_sortdir" >
                                                                                    <option value="desc">{l s='Descending' mod='returnmanager'}</option>
                                                                                    <option value="asc">{l s='Ascending' mod='returnmanager'}</option>
                                                                                </select>
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>&nbsp;</h5>
                                                                            <div class="row" style="margin-left:0">
                                                                                <span class="btn btn-block velsof-btn-block btn-success" id="filter_pending_return_list" >{l s='FILTER' mod='returnmanager'}</span>
                                                                                <span class="btn btn-block velsof-btn-block btn-primary" id="reset_pending_return_list">{l s='Reset' mod='returnmanager'}</span>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {* End Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs *}
                                                            <div id="rm_pending_returns_list_holder" class="rm_pending_returns_tab">
                                                                <div class="tbl-blk">
                                                                    <div class="rm-bigloader"></div>
                                                                    <div class="policy-responsive"><!--Monika 11092019 added reponsive div-->
                                                                    <table class="pure-table">
                                                                        <thead>
                                                                            <tr>
                                                                                {* changes by rishabh jain to add return id column in tables*}
                                                                                <th>{l s='Return Id' mod='returnmanager'}</th>
                                                                                <th style="width: 6%;">{l s='Order' mod='returnmanager'}</th>
                                                                                <th style="width: 12%;">{l s='Customer' mod='returnmanager'}</th>
                                                                                {* Start Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing *}
                                                                                <th style="width: 6%;">{l s='Address Title' mod='returnmanager'}</th>
                                                                                {* End Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing *}
                                                                                <th style="width: 14%;">{l s='Product' mod='returnmanager'}</th>
                                                                                {* Start Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                *}
                                                                                <th style="width: 14%;">{l s='Requested Product' mod='returnmanager'}</th>
                                                                                {* End Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                *}
                                                                                <th style="width: 7%;">{l s='Price' mod='returnmanager'}</th>
                                                                                <th style="width: 5%;">{l s='Qty' mod='returnmanager'}</th>
                                                                                <th style="width: 15%;">{l s='Shipping Paid By' mod='returnmanager'}</th>
                                                                                <th style="width: 9%;">{l s='Type' mod='returnmanager'}</th>
                                                                                {* changes by rishabh jain to fix the alignment issue*}
                                                                                <th style="width: 25%;">{l s='Action' mod='returnmanager'}</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="rm_pending_returns_list">
                                                                            {if $return_pending['flag']}
                                                                                {$i = 0}
                                                                                {foreach $return_pending['data'] as $return}
                                                                                    <tr class='rm_pending_returns pure-table-{if $i%2 == 0}even{else}odd{/if}'>
                                                                                        {* changes by rishabh jain to add return id column in tables*}
                                                                                        <td>{$return['return_id']|escape:'htmlall':'UTF-8'}</td>
                                                                                        {*changes Modified by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL*}
                                                                                        <td><a href="{$order_controller1|escape:'htmlall':'UTF-8'}{$return['order_id']|escape:'htmlall':'UTF-8'}/view?{$order_controller2|escape:'htmlall':'UTF-8'}" target="_blank">{$return['order_reference']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                        <td><a href="{$customer_controller1|escape:'htmlall':'UTF-8'}{$return['customer_id']|escape:'htmlall':'UTF-8'}/edit?{$customer_controller2|escape:'htmlall':'UTF-8'}" target="_blank">{$return['cust_name']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                        {*changes Modified by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL*}
                                                                                        {* Start Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing *}
                                                                                        <td>{$return['address_title']|escape:'htmlall':'UTF-8'}</td>
                                                                                        {* End Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing *}
                                                                                        <td><b><a href="{$return['product_link']|escape:'htmlall':'UTF-8'}" target="_blank">{$return['product_name']|escape:'htmlall':'UTF-8'}</a></b><br>{if isset($return['product_attr']) and $return['product_attr'] != ''}{$return['product_attr']|escape:'htmlall':'UTF-8'}{else}<br>{/if}</td>
                                                                                        {* Start Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                        * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                        *}
                                                                                        <td>{if isset($return['replacedwith_product_link'])}<b><a href="{$return['replacedwith_product_link']|escape:'htmlall':'UTF-8'}" target="_blank">{$return['replacedwith_product_name']|escape:'htmlall':'UTF-8'}</a></b>{else}{l s='N/A' mod='returnmanager'}{/if}</td>
                                                                                        {* End Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                        * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                        *}
                                                                                        <td>{$return['unit_price_tax_incl']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td>{$return['quantity']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td>{if $return['whopayshipping'] eq 'c'}{l s='Customer' mod='returnmanager'}{else}{l s='Store Owner' mod='returnmanager'}{/if}</td>
                                                                                      
                                                                                        <td>{$return['return_type']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class='rm_velsof_action'>
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" value="" style="cursor: pointer;" onclick='allowRequest(this);' class="velsof-glyphicons glyphicons ok" title='{l s='Approve Return' mod='returnmanager'}'><i></i></a>
                                                                                                    {*Edited by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer*}
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" onclick='denyRequest(this);' class="velsof-glyphicons glyphicons remove" title='{l s='Deny Return' mod='returnmanager'}'><i></i></a>
                                                                                                    {*Edited by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer*}
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="{$return['reason']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons glyphicons circle_question_mark rm_customer_notes" title='{l s='Return Reason' mod='returnmanager'}'><i></i></a>
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="{if $return['comment'] neq ''}{$return['comment']|escape:'htmlall':'UTF-8'}{else}<span class='vss_italic_text'>{l s='No comments by customer.' mod='returnmanager'}</span>{/if}" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title='{l s='Customer Notes' mod='returnmanager'}'><i></i></a>
                                                                                                    {if $return['image_path'] neq ''}
                                                                                                <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" href="{$return['image_path']|escape:'htmlall':'UTF-8'}" target="_blank" style="cursor: pointer;" onclick='' class="velsof-glyphicons glyphicons file" title='{l s='View uploaded file' mod='returnmanager'}'><i></i></a>
                                                                                                    {/if}
                                                                                            {* changes by rishabh jain for csutomer ticlets *}
                                                                                           {if $return['is_ticket_exist'] neq 0}
                                                                                                <a href="{$return['ticket_link']|escape:'htmlall':'UTF-8'}" target="_blank" style="cursor: pointer;" onclick='' class="velsof-glyphicons glyphicons book_open" title='{l s='View Ticket' mod='returnmanager'}'><i></i></a>
                                                                                            {/if}
                                                                                            {* changes over *}
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;"  onclick="getReturnmanagerPendingCustomFeildDetail({$return['return_id']|escape:'htmlall':'UTF-8'})" class="velsof-glyphicons glyphicons list" title='{l s='Custom Field Data' mod='returnmanager'}'><i></i></a>
                                                                                        </td>
                                                                                    </tr>
                                                                                    {$i = $i+1}
                                                                                {/foreach}
                                                                            {else}
                                                                                <tr><td colspan="9" rowspan="3"><div class="rm_no_data"><span>{l s='No Pending requests found.' mod='returnmanager'}</span></div></td></tr>
                                                                                            {/if}
                                                                        </tbody>
                                                                    </table>
                                                                    </div>
                                                                    <div class="modal fade" id="modal_pending_custom_field_data" tab-index="-1" aria-hidden="true" aria-labelledby="modal-incentive-form">
                                                                        <div class="modal-dialog" style="width:50%">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <span class="font_popup_header">{l s='Custom Field Data' mod='returnmanager'}</span>
                                                                                    <button type="button" class="close" onclick="closeModalForm('modal_pending_custom_field_data')"><span aria-hidden="true">×</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                </div>
                                                                                <div class="modal-body" style="padding-bottom:0;">                                                                                    
                                                                                </div>
                                                                                <div class="modal-footer no_border">
                                                                                    <button type="button" onclick="closeModalForm('modal_pending_custom_field_data')" class="btn btn-default">{l s='Close' mod='returnmanager'}</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input id="rm_pending_returns_current_page" type="hidden" name="rm_pending_returns_current_page" value="1" />
                                                                </div>
                                                                <div class="paginator-block block">
                                                                    {$return_pending['pagination']|escape:'quotes':'UTF-8'}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="rm_approve_confirm"  tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                {*Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                <h4 class="modal-title velsof_modal_title">{l s='Mark As Approved?' mod='returnmanager'}</h4> 

                                                            </div>
                                                            <div class="modal-body">


                                                                {*Start Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                <div class="block">
                                                                    <label class="velsof-help"> {l s='This email will be sent to this customer. If you want to make any changes then you can or send as it is.' mod='returnmanager'}</label>

                                                                    <div class="row">
                                                                        <div>

                                                                            <div>    
                                                                                {* <input type="hidden" value="{$send_email_lang|escape:'htmlall':'UTF-8'}" id="send_email_lang">*}
                                                                                <label>{l s='Email Subject' mod='returnmanager'}:</label>
                                                                                <input type="text" name="subject_email_allow" id="subject_email_allow" value="">
                                                                            </div>
                                                                            <div>
                                                                                <label>{l s='Email Content' mod='returnmanager'}:</label>
                                                                                <textarea rows="10" aria-hidden="true" name="body_email_allow" id="body_email_allow" class="rm_texteditor"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {*End Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}




                                                            </div>
                                                            <div class="modal-footer">
                                                                <img id="rm_approve_return_popup_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                <button type="button" onclick="rmCloseModal('rm_approve_confirm')" class="btn btn-warning">{l s='Cancel' mod='returnmanager'}</button>
                                                                <button type="button" id="rm_yes_approve" class="btn btn-success">{l s='Submit' mod='returnmanager'}</button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="rm_deny_confirm"  tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                {*Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer*}                                                                
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                <h4 class="modal-title velsof_modal_title">{l s='Mark As Disapproved?' mod='returnmanager'}</h4>

                                                            </div>
                                                            <div class="modal-body">

                                                                {*Start Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                <div class="block">
                                                                    <label class="velsof-help"> {l s='This email will be sent to this customer. If you want to make any changes then you can or send as it is.' mod='returnmanager'}</label>

                                                                    <div class="row">
                                                                        <div>

                                                                            <div>    
                                                                                {*                                                                                <input type="hidden" value="{$send_email_lang|escape:'htmlall':'UTF-8'}" id="send_email_lang">*}
                                                                                <label>{l s='Email Subject' mod='returnmanager'}:</label>
                                                                                <input type="text" name="subject_email_deny" id="subject_email_deny" value="">
                                                                            </div>
                                                                            <div>
                                                                                <label>{l s='Email Content' mod='returnmanager'}:</label>
                                                                                <textarea rows="10" aria-hidden="true" name="body_email_deny" id="body_email_deny" class="rm_texteditor"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {*End Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}




                                                            </div>
                                                            <div class="modal-footer">
                                                                <img id="rm_deny_return_popup_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                <button type="button" onclick="rmCloseModal('rm_deny_confirm')" class="btn btn-warning">{l s='Cancel' mod='returnmanager'}</button>
                                                                <button type="button" id="rm_yes_deny" class="btn btn-success">{l s='Submit' mod='returnmanager'}</button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {* Changes by rishabh jain *}
                                                <div class="modal fade" id="rm_return_comment_modal"  tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                <h4 class="modal-title velsof_modal_title" id="modal-policy" >{l s='Internal Notes' mod='returnmanager'}</h4>
                                                            </div>
                                                            <div class="modal-body" id='rm_internal_notes'></div>
                                                            <div class="modal-footer">
                                                                <img id="rm_internal_note_complete_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                <button type="button" onclick="rmCloseModal('rm_return_comment_modal')"  class="btn btn-warning">{l s='Close' mod='returnmanager'}</button>
                                                                <button type="button" onclick="rmAddInternalNote()" id="rm_add_internal_note" class="btn btn-success">{l s='Save' mod='returnmanager'}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - View Pending Return list -------------------->

                                                <!--------------- Start - Active Returns List -------------------->
                                                <div id="tab_return_list_active" class="tab-pane {$active5 nofilter}">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Active Returns' mod='returnmanager'}</h4>
                                                        <button type="button" id="refresh_active" class="btn btn-block btn-success action-btn">{l s='Refresh List' mod='returnmanager'}</button>
                                                        <div class="block">
                                                            {* Start Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs *}
                                                            <div class="widget">
                                                                <div class="widget-head">
                                                                    <h4 class="heading">{l s='Filter Active Returns List' mod='returnmanager'}</h4>
                                                                </div>
                                                                <div class="widget-body" style="display:block;">
                                                                    <div class="row">  
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Return Id' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_active_custom_return_id" name="rm_active_custom_return_id" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Customer Email' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_active_customer_email" name="rm_active_customer_email" value="" />
                                                                            </div>
                                                                        </span>    
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Product Name' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_active_product_name" name="rm_active_product_name" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Return Status' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <select name="rm_active_return_status" >
                                                                                    <option value="">{l s='Select Status' mod='returnmanager'}</option>
                                                                                    {foreach from=$status item="status_lang"}
                                                                                        <option value="{$status_lang['return_data_id']|intval}">{$status_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                    {/foreach}
                                                                                </select>
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Sort By' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <select name="rm_active_sortby" >
                                                                                    <option value="od.date_update">{l s='Update Date' mod='returnmanager'}</option>
                                                                                    <option value="od.return_type">{l s='Type' mod='returnmanager'}</option>
                                                                                    <option value="pl.product_name">{l s='Product Name' mod='returnmanager'}</option>
                                                                                </select>
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Sort Dir.' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <select name="rm_active_sortdir" >
                                                                                    <option value="desc">{l s='Descending' mod='returnmanager'}</option>
                                                                                    <option value="asc">{l s='Ascending' mod='returnmanager'}</option>
                                                                                </select>
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>&nbsp;</h5>
                                                                            <div class="row" style="margin-left:0">
                                                                                <span class="btn btn-block velsof-btn-block btn-success" id="filter_active_return" >{l s='FILTER' mod='returnmanager'}</span>
                                                                                <span class="btn btn-block velsof-btn-block btn-primary" id="reset_active_return">{l s='Reset' mod='returnmanager'}</span>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {* End Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs *}
                                                            <div id="rm_active_returns_list_holder" class="rm_pending_returns_tab">
                                                                <div class="tbl-blk">
                                                                    <div class="rm-bigloader"></div>
                                                                    <div class="policy-responsive">
                                                                    <table class="pure-table velsof-pure-table">
                                                                        <thead>
                                                                            <tr style="background-color:#f2f2f2">
                                                                                {* changes by rishabh jain to add return id column in tables*}
                                                                                <th>{l s='Return Id' mod='returnmanager'}</th>
                                                                                <th>{l s='Email' mod='returnmanager'}</th>
                                                                                {* Start Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing *}
                                                                                <th style="width: 6%;">{l s='Address Title' mod='returnmanager'}</th>
                                                                                {* End Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing *}
                                                                                <th>{l s='Product' mod='returnmanager'}</th>
                                                                                {* Start Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                *}
                                                                                <th style="width: 14%;">{l s='Requested Product' mod='returnmanager'}</th>
                                                                                {* End Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                *}
                                                                                <th>{l s='Qty' mod='returnmanager'}</th>
                                                                                <th>{l s='Date' mod='returnmanager'}</th>
                                                                                <th>{l s='Type' mod='returnmanager'}</th>
                                                                                <th>{l s='Status' mod='returnmanager'}</th>
                                                                                <th style="width: 20%;">{l s='Action' mod='returnmanager'}</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="rm_active_return_list">
                                                                            {if $return_history['flag']}
                                                                                {$i = 0}
                                                                                {foreach $return_history['data'] as $return}
                                                                                    <tr id="rm_pending_returns_{$return['return_id']|escape:'htmlall':'UTF-8'}" class='rm_pending_returns pure-table-{if $i%2 == 0}even{else}odd{/if}'>
                                                                                        {* changes by rishabh jain to add return id column in tables*}
                                                                                        <td>{$return['return_id']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td><a href="{$customer_controller|escape:'htmlall':'UTF-8'}&id_customer={$return['customer_id']|escape:'htmlall':'UTF-8'}&viewcustomer" target="_blank">{$return['cust_email']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                        {* Start Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing *}
                                                                                        <td>{$return['address_title']|escape:'htmlall':'UTF-8'}</td>
                                                                                        {* End Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing *}
                                                                                        <td><b>{$return['product_name']|escape:'htmlall':'UTF-8'}</b><br>{if isset($return['product_attr']) and $return['product_attr'] != ''}{$return['product_attr']|escape:'htmlall':'UTF-8'}{else}<br>{/if}</td>
                                                                                            {*                                                                                        <td><b><a href="{$return['product_link']|escape:'htmlall':'UTF-8'}" target="_blank">{$return['product_name']|escape:'htmlall':'UTF-8'}</a></b><br>{if isset($return['product_attr']) and $return['product_attr'] != ''}{$return['product_attr']|escape:'htmlall':'UTF-8'}{else}&nbsp;{/if}</td>*}
                                                                                            {* Start Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                            * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                            *}
                                                                                        <td>{if isset($return['replacedwith_product_link'])}<b><a href="{$return['replacedwith_product_link']|escape:'htmlall':'UTF-8'}" target="_blank">{$return['replacedwith_product_name']|escape:'htmlall':'UTF-8'}</a></b>{else}N/A{/if}</td>
                                                                                        {* End Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                        * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                        *}
                                                                                        <td>{$return['quantity']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td>{$return['request_date']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td>{$return['return_type']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class="rm_pending_return_status_col">{$return['status']|escape:'htmlall':'UTF-8'}</td>
                                                                                        <td class='rm_velsof_action' style="width: 22%;">
                                                                                            {* Start Edited by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer*}
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" onclick='denyRequest(this);' class="velsof-glyphicons glyphicons remove" title='{l s='Deny Return' mod='returnmanager'}'><i></i></a>
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" onclick='changeReturnStatus(this);' class="velsof-glyphicons glyphicons edit" title='{l s='Change Status' mod='returnmanager'}'><i></i></a>
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" onclick='viewReturnDetail(this)' class="velsof-glyphicons glyphicons history" title='{l s='View History' mod='returnmanager'}'><i></i></a>
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="{if $return['comment'] neq ''}{$return['comment']|escape:'htmlall':'UTF-8'}{else}<span class='vss_italic_text'>{l s='No comments by customer.' mod='returnmanager'}</span>{/if}" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title='{l s='Customer Notes' mod='returnmanager'}'><i></i></a>
                                                                                            {* changes by rishabh jain for refund type return *}
                                                                                            {if $return['is_refund_type'] == 1}
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" refund="1" onclick='completeReturn(this)' class="velsof-glyphicons glyphicons ok_2" title='{l s='Mark as Complete' mod='returnmanager'}'><i></i></a>
                                                                                            {else}
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" refund="0" onclick='completeReturn(this)' class="velsof-glyphicons glyphicons ok_2" title='{l s='Mark as Complete' mod='returnmanager'}'><i></i></a>
                                                                                            {/if}
                                                                                            {* changes over *}
                                                                                                    {* End Edited by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer*}
                                                                                            {* changes  by rishabh jain *}
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" onclick='viewInternalNotes(this)' class="velsof-glyphicons glyphicons comments" title='{l s='View Internal Notes' mod='returnmanager'}'><i></i></a>
                                                                                            <input type="hidden" id="rm_active_curr_status_{$return['return_id']|escape:'htmlall':'UTF-8'}" value="{$return['status_id']|escape:'htmlall':'UTF-8'}" />
                                                                                            {if $return['image_path'] neq ''}
                                                                                                <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" href="{$return['image_path']|escape:'htmlall':'UTF-8'}" target="_blank" style="cursor: pointer;" onclick='' class="velsof-glyphicons glyphicons file" title='{l s='View uploaded file' mod='returnmanager'}'><i></i></a>
                                                                                                    {/if}
                                                                                            {* changes  by rishabh jain *}
                                                                                            {*<a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" onclick='viewInternalNotes(this)' class="velsof-glyphicons glyphicons comments" title='{l s='View Internal Notes' mod='returnmanager'}'><i></i></a>*}
                                                                                            {* changes by rishabh jain for csutomer ticlets *}
                                                                                           {if $return['is_ticket_exist'] neq 0}
                                                                                                <a href="{$return['ticket_link']|escape:'htmlall':'UTF-8'}" target="_blank" style="cursor: pointer;" onclick='' class="velsof-glyphicons glyphicons book_open" title='{l s='View Ticket' mod='returnmanager'}'><i></i></a>
                                                                                            {/if}
                                                                                            {* changes over *}
                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;"  onclick="getReturnmanagerActiveCustomFeildDetail({$return['return_id']|escape:'htmlall':'UTF-8'})" class="velsof-glyphicons glyphicons list" title='{l s='Custom Field Data' mod='returnmanager'}'><i></i></a>
                                                                                        </td>
                                                                                    </tr>
                                                                                    {$i = $i + 1}
                                                                                {/foreach}
                                                                            {else}
                                                                                <tr><td colspan="9" rowspan="3"><div class="rm_no_data"><span>{l s='No Active requests found.' mod='returnmanager'}</span></div></td></tr>
                                                                                            {/if}
                                                                        </tbody>
                                                                    </table>
                                                                    </div>
                                                                    <div class="modal fade" id="modal_active_custom_field_data" tab-index="-1" aria-hidden="true" aria-labelledby="modal-incentive-form">
                                                                        <div class="modal-dialog" style="width:50%">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <span class="font_popup_header">{l s='Custom Field Data' mod='returnmanager'}</span>
                                                                                    <button type="button" class="close" onclick="closeModalForm('modal_active_custom_field_data')"><span aria-hidden="true">×</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                </div>
                                                                                <div class="modal-body" style="padding-bottom:0;">                                                                                    
                                                                                </div>
                                                                                <div class="modal-footer no_border">
                                                                                    <button type="button" onclick="closeModalForm('modal_active_custom_field_data')" class="btn btn-default">{l s='Close' mod='returnmanager'}</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input id="rm_active_returns_current_page" type="hidden" name="rm_active_returns_current_page" value="1" />
                                                                </div>
                                                                <div class="paginator-block block">
                                                                    {$return_history['pagination']|escape:'quotes':'UTF-8'}   
                                                                </div>
                                                            </div>
                                                            <div class="modal fade" id="rm_change_status_modal" tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                            <h4 class="modal-title velsof_modal_title" id="modal-policy" >{l s='Change Return Status' mod='returnmanager'}</h4>
                                                                        </div>
                                                                        <div class="modal-body" id='rm_return_status'>
                                                                            <table>                                                                                                                                
                                                                                <tr>
                                                                                    <td class="name vertical_top_align"><span class="control-label">{l s='Change Return Status' mod='returnmanager'}: </span>                                                                
                                                                                        <i class="icon-question-sign" data-toggle="tooltip"  data-placement="top" data-original-title="{l s='Change Status of Current Return Request' mod='returnmanager'}"></i>
                                                                                    </td>
                                                                                    <td class="settings">
                                                                                        <div class='span4'>
                                                                                            <select id="rm_change_return_status">
                                                                                                {foreach from=$status item="status_lang"}
                                                                                                    <option value="{$status_lang['return_data_id']|intval}">{$status_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                                {/foreach}
                                                                                            </select>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                            {*Start Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                            <div class="block">        
                                                                                <label class="velsof-help"> {l s='This email will be sent to this customer. If you want to make any changes then you can or send as it is.' mod='returnmanager'}</label>

                                                                                <div class="row">
                                                                                    <div>

                                                                                        <div>                                                                                             
                                                                                            <label>{l s='Email Subject' mod='returnmanager'}:</label>
                                                                                            <input type="text" name="subject_email_status" id="subject_email_status" value="">
                                                                                        </div>
                                                                                        <div>
                                                                                            <label>{l s='Email Content' mod='returnmanager'}:</label>
                                                                                            <textarea rows="10" aria-hidden="true" name="body_email_status" id="body_email_status" class="rm_texteditor"></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            {*End Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}



                                                                            <div class="modal-footer">
                                                                                <input type="hidden" id="rm_change_status_return_id" value="0" />
                                                                                <img id="rm_return_status_change_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                                <button type="button" onclick="rmCloseModal('rm_change_status_modal')" class="btn btn-warning">{l s='Cancel' mod='returnmanager'}</button>
                                                                                <button type="button" onclick="rmUpdateStatus()" id="rm_upd_status" class="btn btn-success">{l s='Submit' mod='returnmanager'}</button>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal fade" id="rm_return_history_modal"  tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                            <h4 class="modal-title velsof_modal_title" id="modal-policy" >{l s='Return History' mod='returnmanager'}</h4>
                                                                        </div>
                                                                        <div class="modal-body" id='rm_return_history'></div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" onclick="rmCloseModal('rm_return_history_modal')"  class="btn btn-warning">{l s='Close' mod='returnmanager'}</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal fade" id="rm_complete_confirm" tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            {*Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                            <h4 class="modal-title velsof_modal_title">{l s='Mark As Completed?' mod='returnmanager'}</h4>
                                                                        </div>
                                                                        <div class="modal-body">

                                                                            {*Start Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                            <div class="block">
                                                                                <div class="row">
                                                                                    <span>
                                                                                        <p class="help"><label class="velsof-help"><b>{l s='Note' mod='returnmanager'}: </b>{l s='1. This email will be sent to this customer. If you want to make any changes in the mail then you can.' mod='returnmanager'}</br>
                                                                                                {l s='2.Once you mark a return as complete then it is moved to archives and can only be seen in the Archive Returns tab and this step can not be undone. ' mod='returnmanager'}<br>  </label></p>
                                                                                    </span>


                                                                                    <div>

                                                                                        <div>    
                                                                                            <label>{l s='Email Subject' mod='returnmanager'}:</label>
                                                                                            <input type="text" name="subject_email_comp" id="subject_email_comp" value="">
                                                                                        </div>
                                                                                        <div>
                                                                                            <label>{l s='Email Content' mod='returnmanager'}:</label>
                                                                                            <textarea rows="10" aria-hidden="true" name="body_email_comp" id="body_email_comp" class="rm_texteditor"></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            {*End Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}


                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <img id="rm_return_complete_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />
                                                                            <button type="button" onclick="rmCloseModal('rm_complete_confirm')" class="btn btn-warning">{l s='Cancel' mod='returnmanager'}</button>
                                                                            <button type="button" id="rm_yes_confirm" class="btn btn-success">{l s='Submit' mod='returnmanager'}</button>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal fade" id="rm_generate_coupon" tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            {*Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                            <h4 class="modal-title velsof_modal_title">{l s='Select the options before marking the return as complete' mod='returnmanager'}</h4>
                                                        </div>
                                                                        <div class="modal-body">

                                                                            {*Start Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                            <div class="block" style="text-align: center;">
                                                                                <div class="row" style="padding:2%;">
                                                                                    <div>

                                                                                        <div> 
                                                                                            <label>{l s='Generate Discount Coupon' mod='returnmanager'}:</label>
                                                                                            <div class="make-switch" data-on="primary" data-off="default">
                                                                                                <input class="make-switch" type="checkbox" value="1" name="generate_coupon" id="generate_coupon" checked="checked" />
                                                    </div>
                                                </div>
                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row" style="padding:2%;">
                                                                                    <div>

                                                                                        <div> 
                                                                                            <label>{l s='Update Inventory' mod='returnmanager'}:</label>
                                                                                            <div class="make-switch" data-on="primary" data-off="default">
                                                                                                <input class="make-switch" type="checkbox" value="1" name="update_inventory" id="update_inventory" checked="checked" />
                                                                                            </div>                                                                   
                                                                                        </div>
                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            {*End Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}


                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            {*<img id="rm_return_discount_coupon_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />*}
                                                                            <button type="button" onclick="rmCloseModal('rm_generate_coupon')" class="btn btn-warning">{l s='Cancel' mod='returnmanager'}</button>
                                                                            <button type="button" id="rm_yes_generate" class="btn btn-success">{l s='Submit' mod='returnmanager'}</button>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                                            
                                                               {* Start changes done by Vishal on 28th August 2019 : to add Update inventory functionality on credit and replacement return *}                
                                                                            
                                                            <div class="modal fade" id="rm_update_inventory" tab-index="-1" aria-hidden="true" aria-labelledby="modal-remove">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            {*Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                            <h4 class="modal-title velsof_modal_title">{l s='Select the options before marking the return as complete' mod='returnmanager'}</h4>
                                                        </div>
                                                                        <div class="modal-body">

                                                                            {*Start Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}
                                                                            <div class="block" style="text-align: center;">
                                                                                <div class="row" style="padding:2%;">
                                                                                    <div>

                                                                                        <div> 
                                                                                            <label>{l s='Update Inventory' mod='returnmanager'}:</label>
                                                                                            <div class="make-switch" data-on="primary" data-off="default">
                                                                                                <input class="make-switch" type="checkbox" value="1" name="update_inventory_1" id="update_inventory" checked="checked" />
                                                    </div>
                                                </div>
                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            {*End Added by Anshul Mittal on 24-08-2017 to add a functionality of email editing before sending it to customer*}


                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            {*<img id="rm_return_discount_coupon_loader" src="{$path|escape:'quotes':'UTF-8'}returnmanager/views/img/loader_small.gif" />*}
                                                                            <button type="button" onclick="rmCloseModal('rm_update_inventory')" class="btn btn-warning">{l s='Cancel' mod='returnmanager'}</button>
                                                                            <button type="button" id="rm_yes_generate_update" class="btn btn-success">{l s='Submit' mod='returnmanager'}</button>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>   
                                                                            
                                                            {* End changes done by Vishal on 28th August 2019 : to add Update inventory functionality on credit and replacement return *}                
                                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - Active Returns List -------------------->
                                                
                                                <!--------------- Start - Cancel List -------------------->
                                                <div id="tab_return_list_cancel" class="tab-pane {$active6 nofilter}">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Cancelled Return List' mod='returnmanager'}</h4>
                                                        <div class="block">
                                                            {* Start Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs *}
                                                            <div class="widget">
                                                                <div class="widget-head">
                                                                    <h4 class="heading">{l s='Filter Cancelled Returns List' mod='returnmanager'}</h4>
                                                                </div>
                                                                <div class="widget-body" style="display:block;">
                                                                    <div class="row">
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Return Id' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_cancelled_custom_return_id" name="rm_cancelled_custom_return_id" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Customer Name' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_cancelled_customer_name" name="rm_cancelled_customer_name" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Product Name' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_cancelled_product_name" name="rm_cancelled_product_name" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Order Id' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <input type="text" id="rm_cancelled_order_id" name="rm_cancelled_order_id" value="" />
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Sort By' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <select name="rm_cancelled_sortby" >
                                                                                    <option value="od.date_update">{l s='Update Date' mod='returnmanager'}</option>
                                                                                    <option value="od.return_type">{l s='Type' mod='returnmanager'}</option>
                                                                                    <option value="ods.reference">{l s='Order Reference' mod='returnmanager'}</option>
                                                                                    <option value="pl.product_name">{l s='Product Name' mod='returnmanager'}</option>
                                                                                </select>
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>{l s='Sort Dir.' mod='returnmanager'}:</h5>
                                                                            <div class="row rm_filter_input_block">
                                                                                <select name="rm_cancelled_sortdir" >
                                                                                    <option value="desc">{l s='Descending' mod='returnmanager'}</option>
                                                                                    <option value="asc">{l s='Ascending' mod='returnmanager'}</option>
                                                                                </select>
                                                                            </div>
                                                                        </span>
                                                                        <span class="span0 rm_filter_date">
                                                                            <h5>&nbsp;</h5>
                                                                            <div class="row" style="margin-left:0">
                                                                                <span class="btn btn-block velsof-btn-block btn-success" id="filter_cancelled_return_list">{l s='FILTER' mod='returnmanager'}</span>
                                                                                <span class="btn btn-block velsof-btn-block btn-primary" id="reset_cancelled_return_list" >{l s='Reset' mod='returnmanager'}</span>
                                                                            </div>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {* End Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs *}
                                                            <div id="rm_cancel_returns_list_holder" class="rm_cancel_returns_tab">
                                                                <div class="tbl-blk">
                                                                    <div class="rm-bigloader"></div>
                                                                    <div class="policy-responsive">
                                                                    <table class="pure-table velsof-pure-table">
                                                                        <thead>
                                                                                            <tr style="background-color:#f2f2f2">
                                                                                                {* changes by rishabh jain *}
                                                                                                <th style="width: 4%;">{l s='Return Id' mod='returnmanager'}</th>
                                                                                                {* changes over *}
                                                                                                <th style="width: 7%;">{l s='Order' mod='returnmanager'}</th>
                                                                                                <th style="width: 12%;">{l s='Customer' mod='returnmanager'}</th>
                                                                                                <th style="width: 14%;">{l s='Product' mod='returnmanager'}</th>
                                                                                                {* Start Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                                * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                                *}
                                                                                                <th style="width: 14%;">{l s='Requested Product' mod='returnmanager'}</th>
                                                                                                {* End Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                                * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                                *}
                                                                                                <th style="width: 8%;">{l s='Price' mod='returnmanager'}</th>
                                                                                                <th style="width: 5%;">{l s='Qty' mod='returnmanager'}</th>
                                                                                                <th style="width: 14%;">{l s='Shipping Paid By' mod='returnmanager'}</th>
                                                                                                <th style="width: 14%;">{l s='Cancelled By' mod='returnmanager'}</th>
                                                                                                <th style="width: 9%;">{l s='Type' mod='returnmanager'}</th>
                                                                                                <th style="width: 12%;">{l s='Action' mod='returnmanager'}</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody id="rm_cancel_list_tbody">
                                                                                            {if $cancel_returns['flag']}
                                                                                            {foreach $cancel_returns['data'] as $return}
                                                                                                <tr class="pure-table-{if $i%2 == 0}even{else}odd{/if}">
                                                                                                    <td>{if $return['return_id'] != '' && $return['return_id'] != null}
                                                                                            {$return['return_id']|escape:'htmlall':'UTF-8'}
                                                                                            {else}
                                                                                               - 
                                                                                               {/if}
                                                                                        </td>
                                                                                        {* Changes over *}
                                                                                        {*changes Modified by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL*}
                                                                                                    <td><a href="{$order_controller1|escape:'htmlall':'UTF-8'}{$return['order_id']|escape:'htmlall':'UTF-8'}/view?{$order_controller2|escape:'htmlall':'UTF-8'}" target="_blank">{$return['order_reference']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                                    <td><a href="{$customer_controller1|escape:'htmlall':'UTF-8'}{$return['customer_id']|escape:'htmlall':'UTF-8'}/edit?{$customer_controller2|escape:'htmlall':'UTF-8'}" target="_blank">{$return['cust_name']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                                    {*changes Modified by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL*}
                                                                                                    <td><b><a href="{$return['product_link']|escape:'htmlall':'UTF-8'}" target="_blank">{$return['product_name']|escape:'htmlall':'UTF-8'}</a></b><br>{if isset($return['product_attr']) and $return['product_attr'] != ''}{$return['product_attr']|escape:'htmlall':'UTF-8'}{else}<br>{/if}</td>
                                                                                                    {* Start Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                                    * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                                    *}
                                                                                                    <td>{if isset($return['replacedwith_product_link'])}<b><a href="{$return['replacedwith_product_link']|escape:'htmlall':'UTF-8'}" target="_blank">{$return['replacedwith_product_name']|escape:'htmlall':'UTF-8'}</a></b>{else}N/A{/if}</td>
                                                                                                    {* End Code Added by Priyanshu on 23-March-2020 to add Requested Replacement Proudct Column
                                                                                                    * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                                                                                                    *}
                                                                                                    <td>{$return['unit_price_tax_incl']|escape:'htmlall':'UTF-8'}</td>
                                                                                                    <td>{$return['quantity']|escape:'htmlall':'UTF-8'}</td>
                                                                                                    <td>{if $return['whopayshipping'] eq 'c'}{l s='Customer' mod='returnmanager'}{else}{l s='Store Owner' mod='returnmanager'}{/if}</td>
                                                                                                        {*Start Changes for showing the cancel type in the cancelled tickets tab
                                                                                                         @date 09-04-2024
                                                                                                         @modifier Ravi Kant Gupta*}
                                                                                                    <td>
                                                                                                    {if $return['cancel_type'] eq '3'}{l s='Admin' mod='returnmanager'}{else}{l s='Customer' mod='returnmanager'}{/if}</td>
                                                                                                   {*end of change*}
                                                                                                    <td>{$return['return_type']|escape:'htmlall':'UTF-8'}</td>
                                                                                                    <td class='rm_velsof_action' style="width: 25%;">
                                                                                                        <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="{$return['reason']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons glyphicons circle_question_mark rm_customer_notes" title='{l s='Return Reason' mod='returnmanager'}'><i></i></a>
                                                                                                        <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="{if $return['comment'] neq ''}{$return['comment']|escape:'htmlall':'UTF-8'}{else}<span class='vss_italic_text'>{l s='No comments by customer.' mod='returnmanager'}</span>{/if}" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title='{l s='Customer Notes' mod='returnmanager'}'><i></i></a>
                                                                                                                {if $return['image_path'] neq ''}
                                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" href="{$return['image_path']|escape:'htmlall':'UTF-8'}" target="_blank" style="cursor: pointer;" onclick='' class="velsof-glyphicons glyphicons file" title='{l s='View uploaded file' mod='returnmanager'}'><i></i></a>
                                                                                                                {/if}
                                                                                                        <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" onclick='viewInternalNotes(this)' class="velsof-glyphicons glyphicons comments" title='{l s='View Internal Notes' mod='returnmanager'}'><i></i></a>
                                                                                                        {* changes by rishabh jain for csutomer ticlets *}
                                                                                           {if $return['is_ticket_exist'] neq 0}
                                                                                                <a href="{$return['ticket_link']|escape:'htmlall':'UTF-8'}" target="_blank" style="cursor: pointer;" onclick='' class="velsof-glyphicons glyphicons book_open" title='{l s='View Ticket' mod='returnmanager'}'><i></i></a>
                                                                                            {/if}
                                                                                            {* changes over *}
                                                                                                        <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;"  onclick="getReturnmanagerCancelCustomFeildDetail({$return['return_id']|escape:'htmlall':'UTF-8'})" class="velsof-glyphicons glyphicons list" title='{l s='Custom Field Data' mod='returnmanager'}'><i></i></a>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                {$i = $i + 1}
                                                                                            {/foreach}
                                                                                        
                                                                                    
                                                                                {else}
                                                                                    <tr><td colspan="9" rowspan="3"><div class="rm_no_data"><span>{l s='No Cancel requests found.' mod='returnmanager'}</span></div></td></tr>
                                                                                    
                                                                                        {/if}
                                                                                        
                                                                                    </tbody>
                                                                                    </table>
                                                                                        </div>
                                                                                        <div class="modal fade" id="modal_cancelled_custom_field_data" tab-index="-1" aria-hidden="true" aria-labelledby="modal-incentive-form">
                                                                                            <div class="modal-dialog" style="width:50%">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header">
                                                                                                        <span class="font_popup_header">{l s='Custom Field Data' mod='returnmanager'}</span>
                                                                                                        <button type="button" class="close" onclick="closeModalForm('modal_cancelled_custom_field_data')"><span aria-hidden="true">×</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                                    </div>
                                                                                                    <div class="modal-body" style="padding-bottom:0;">                                                                                    
                                                                                                    </div>
                                                                                                    <div class="modal-footer no_border">
                                                                                                        <button type="button" onclick="closeModalForm('modal_cancelled_custom_field_data')" class="btn btn-default">{l s='Close' mod='returnmanager'}</button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                            <input id="rm_cancel_returns_current_page" type="hidden" name="rm_cancel_returns_current_page" value="1" />
                                                                            </div>
                                                                            <div class="paginator-block block">
                                                                                {$cancel_returns['pagination']|escape:'quotes':'UTF-8'}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                
                                                <!--------------- End - cancel List -------------------->
                                                <!--------------- Start - Archives List -------------------->
                                                <div id="tab_archive_list" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Archives List' mod='returnmanager'}</h4>
                                                        <div class="block">
                                                            <div class="innerLR">
                                                                <div class="widget">
                                                                    <div class="widget-head">
                                                                        <h4 class="heading">{l s='Filter Archive List' mod='returnmanager'}</h4>
                                                                    </div>
                                                                    <div class="widget-body" style="display:block;">
                                                                        <div class="row">
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Return Id' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_custom_return_id" name="rm_custom_return_id" value="" />
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Customer Name' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_customer_name" name="rm_customer_name" value="" />
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Product Name' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_product_name" name="rm_product_name" value="" />
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Order Id' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_order_id" name="rm_order_id" value="" />
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='From Date' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_from_date" name="rm_from_date" value=""/>
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='To Date' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <input type="text" id="rm_to_date" name="rm_to_date" value="" />
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date" style="display:none;">
                                                                                <h5>{l s='Return Status' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <select name="rm_archive_return_status" >
                                                                                        <option value="">{l s='Select Status' mod='returnmanager'}</option>
                                                                                        {foreach from=$status item="status_lang"}
                                                                                            <option value="{$status_lang['return_data_id']|intval}">{$status_lang['value']|escape:'htmlall':'UTF-8'}</option>
                                                                                        {/foreach}
                                                                                    </select>
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Sort By' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <select name="rm_archive_sortby" >
                                                                                        <option value="od.date_update">{l s='Update Date' mod='returnmanager'}</option>
                                                                                        <option value="od.return_type">{l s='Type' mod='returnmanager'}</option>
                                                                                        <option value="ods.reference">{l s='Order Reference' mod='returnmanager'}</option>
                                                                                        <option value="pl.product_name">{l s='Product Name' mod='returnmanager'}</option>
                                                                                    </select>
                                                                                </div>
                                                                            </span>
                                                                            <span class="span0 rm_filter_date">
                                                                                <h5>{l s='Sort Dir.' mod='returnmanager'}:</h5>
                                                                                <div class="row rm_filter_input_block">
                                                                                    <select name="rm_archive_sortdir" >
                                                                                        <option value="desc">{l s='Descending' mod='returnmanager'}</option>
                                                                                        <option value="asc">{l s='Ascending' mod='returnmanager'}</option>
                                                                                    </select>
                                                                                </div>
                                                                            </span>    
                                                                            <span class="span0 rm_filter_date" style="width: 235px;">
                                                                                <h5>&nbsp;</h5>
                                                                                <div class="row" style="margin-left:0">
                                                                                    <span class="btn btn-block velsof-btn-block btn-success" id="filter_archives" >{l s='FILTER' mod='returnmanager'}</span>
                                                                                    <span class="btn btn-block velsof-btn-block btn-primary" id="reset_archives">{l s='Reset' mod='returnmanager'}</span>
                                                                                    <span class="btn btn-block velsof-btn-block btn-warning" id="export_archives">{l s='EXPORT' mod='returnmanager'}</span>
                                                                                    <img id="rm_loader" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/loader_small.gif" style="display:none;">
                                                                                </div>
                                                                            </span>
                                                                            <div id="rm_date_error" class="rm-date-error"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="rm_list_container" class="">
                                                                    <div class="widget">
                                                                        <div class="widget-head">
                                                                            <h4 class="heading">{l s='Filtered Archive List' mod='returnmanager'}</h4>
                                                                        </div>
                                                                        <div class="row graph_container">
                                                                            <div id="rm_archive_list" style="width: 98%; margin: 6px auto; height:auto;">
                                                                                <div class="rm-bigloader"></div>
                                                                                {if $archive_returns['flag']}
                                                                                    {$i = 0}
                                                                                    <table class="pure-table">
                                                                                        <thead>
                                                                                            <tr style="background-color:#f2f2f2">
                                                                                                {* changes by rishabh jain *}
                                                                                                <th style="width: 4%;">{l s='Return Id' mod='returnmanager'}</th>
                                                                                                {* changes over *}
                                                                                                <th style="width: 7%;">{l s='Order' mod='returnmanager'}</th>
                                                                                                <th style="width: 12%;">{l s='Customer' mod='returnmanager'}</th>
                                                                                                <th style="width: 14%;">{l s='Product' mod='returnmanager'}</th>
                                                                                                <th style="width: 8%;">{l s='Price' mod='returnmanager'}</th>
                                                                                                <th style="width: 5%;">{l s='Qty' mod='returnmanager'}</th>
                                                                                                <th style="width: 14%;">{l s='Shipping Paid By' mod='returnmanager'}</th>
                                                                                                <th style="width: 9%;">{l s='Type' mod='returnmanager'}</th>
                                                                                                <th style="width: 12%;">{l s='Action' mod='returnmanager'}</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody id="rm_archive_list_tbody">
                                                                                            {foreach $archive_returns['data'] as $return}
                                                                                                <tr class="pure-table-{if $i%2 == 0}even{else}odd{/if}">
                                                                                                    {* changes by rishabh jain to add return id column in tables*}
                                                                                                    <td>{$return['return_id']|escape:'htmlall':'UTF-8'}</td>
                                                                                                    {*changes Modified by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL*}
                                                                                                    <td><a href="{$order_controller1|escape:'htmlall':'UTF-8'}{$return['order_id']|escape:'htmlall':'UTF-8'}/view?{$order_controller2|escape:'htmlall':'UTF-8'}" target="_blank">{$return['order_reference']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                                    <td><a href="{$customer_controller1|escape:'htmlall':'UTF-8'}{$return['customer_id']|escape:'htmlall':'UTF-8'}/edit?{$customer_controller2|escape:'htmlall':'UTF-8'}" target="_blank">{$return['cust_name']|escape:'htmlall':'UTF-8'}</a></td>
                                                                                                    {*changes Modified by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL*}
                                                                                                    <td><b><a href="{$return['product_link']|escape:'htmlall':'UTF-8'}" target="_blank">{$return['product_name']|escape:'htmlall':'UTF-8'}</a></b><br>{if isset($return['product_attr']) and $return['product_attr'] != ''}{$return['product_attr']|escape:'htmlall':'UTF-8'}{else}<br>{/if}</td>
                                                                                                    <td>{$return['unit_price_tax_incl']|escape:'htmlall':'UTF-8'}</td>
                                                                                                    <td>{$return['quantity']|escape:'htmlall':'UTF-8'}</td>
                                                                                                    <td>{if $return['whopayshipping'] eq 'c'}{l s='Customer' mod='returnmanager'}{else}{l s='Store Owner' mod='returnmanager'}{/if}</td>
                                                                                                    <td>{$return['return_type']|escape:'htmlall':'UTF-8'}</td>
                                                                                                    <td class='rm_velsof_action' style="width: 25%;">
                                                                                                        <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="{$return['reason']|escape:'htmlall':'UTF-8'}" class="velsof-glyphicons glyphicons circle_question_mark rm_customer_notes" title='{l s='Return Reason' mod='returnmanager'}'><i></i></a>
                                                                                                        <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="{if $return['comment'] neq ''}{$return['comment']|escape:'htmlall':'UTF-8'}{else}<span class='vss_italic_text'>{l s='No comments by customer.' mod='returnmanager'}</span>{/if}" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title='{l s='Customer Notes' mod='returnmanager'}'><i></i></a>
                                                                                                                {if $return['image_path'] neq ''}
                                                                                                            <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}_{$return['id_lang']|escape:'htmlall':'UTF-8'}" href="{$return['image_path']|escape:'htmlall':'UTF-8'}" target="_blank" style="cursor: pointer;" onclick='' class="velsof-glyphicons glyphicons file" title='{l s='View uploaded file' mod='returnmanager'}'><i></i></a>
                                                                                                                {/if}
                                                                                                             
                                                                                                        <a type="{$return['return_id']|escape:'htmlall':'UTF-8'}" style="cursor: pointer;" onclick='viewInternalNotes(this)' class="velsof-glyphicons glyphicons comments" title='{l s='View Internal Notes' mod='returnmanager'}'><i></i></a>
                                                                                                        {* changes by rishabh jain for csutomer ticlets *}
                                                                                           {if $return['is_ticket_exist'] neq 0}
                                                                                                <a href="{$return['ticket_link']|escape:'htmlall':'UTF-8'}" target="_blank" style="cursor: pointer;" onclick='' class="velsof-glyphicons glyphicons book_open" title='{l s='View Ticket' mod='returnmanager'}'><i></i></a>
                                                                                            {/if}
                                                                                            {* changes over *}
                                                                                                    </td>
                                                                                                </tr>
                                                                                                {$i = $i + 1}
                                                                                            {/foreach}
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <input id="rm_archive_returns_current_page" type="hidden" name="rm_archive_returns_current_page" value="1" />
                                                                                {else}
                                                                                    <div class="rm_no_data"><span>{l s='No data found' mod='returnmanager'}</span></div>
                                                                                        {/if}
                                                                            </div>
                                                                            <div class="paginator-block block">
                                                                                {$archive_returns['pagination']|escape:'quotes':'UTF-8'}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - Archives List -------------------->
                                                <!--------------- Start - Custom Field --------------------->                                               
                                                <div id="tab_custom_fields" class="tab-pane">
                                                    <div class="block">
                                                        <h4 class='heading-mosaic velsof-header'>{l s='Custom Fields' mod='returnmanager'}</h4>
                                                        <table class="form">
                                                            <tr>
                                                                <td class="name vertical_top_align"><span class="control-label">{l s='Enable/Disable Custom Field block' mod='returnmanager'}: </span>
                                                                    <i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Enable/Disable custom field block on the Return Pop up' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    {if isset($velsof_return['enable_custom_field']) and $velsof_return['enable_custom_field'] eq 1}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_custom_field]" id="return_custom_field" checked="checked" />
                                                                        </div>                                                                   
                                                                    {else}
                                                                        <div class="make-switch" data-on="primary" data-off="default">
                                                                            <input class="make-switch" type="checkbox" value="1" name="velsof_return[enable_custom_field]" id="return_custom_field"/>
                                                                        </div>
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="name vertical_top_align">
                                                                    <span class="control-label">{l s='Custom Field block Title' mod='returnmanager'}: </span><i class="icon-question-sign" data-toggle="tooltip"  data-placement="bottom" data-original-title="{l s='Title will be shown on the front for the custom field block' mod='returnmanager'}"></i>
                                                                </td>
                                                                <td class="settings">
                                                                    {foreach from=$languages item='lang'}
                                                                        <div class="input-row-margin-bottom" style="display: inline-flex;">
                                                                            <div class='span0'><img src="{$img_lang_dir|escape:'quotes':'UTF-8'}{$lang['id_lang']|escape:'htmlall':'UTF-8'}.jpg" height="11px" width="16px" alt="{$lang['name']|escape:'htmlall':'UTF-8'}" title="{$lang['name']|escape:'htmlall':'UTF-8'}"/></div>
                                                                            <div class="span4">
                                                                                <input type="text" id="custom_field_title_{$lang['id_lang']|escape:'htmlall':'UTF-8'}" class="kb_custom_field rm_modal_input" name="custom_field_title_{$lang['id_lang']|escape:'htmlall':'UTF-8'}" placeholder="{l s='Enter Customer Field Title' mod='returnmanager'}" style="width: 95%;" value="{if isset($velsof_return['custom_data']['custom_block_title'][$lang['id_lang']])}{$velsof_return['custom_data']['custom_block_title'][$lang['id_lang']] nofilter}{/if}"/>
                                                                            </div>
                                                                        </div>                                                                        
                                                                    {/foreach}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <div class="widget">
                                                            <div class="widget-head">
                                                                <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Custom Field List' mod='returnmanager'}</h3>
                                                            </div>
                                                            <div class="widget-body">
                                                                <div id="address_data">
                                                                    <table class="pure-table" id="table_custom_fields_data" style='width: 100%;'>
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="font-weight: normal;">{l s='#id' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal;">{l s='Custom Field Label' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal;">{l s='Type' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal;">{l s='Required' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal;">{l s='Active' mod='returnmanager'}</th>
                                                                                <th style="font-weight: normal;">{l s='Action' mod='returnmanager'}</th>                                                                                                                                                                            
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbody_custom_fields_data">
                                                                            {if isset($custom_fields_details) && $custom_fields_details neq ''}
                                                                                {assign var="counter" value="1"}
                                                                                {foreach from=$custom_fields_details item=array_field}
                                                                                    <tr class="pure-table-odd" id="tr_pure_table_{$array_field['id_velsof_rm_custom_fields'] nofilter}">
                                                                                        <td>{$counter nofilter}</td>
                                                                                        <td class="width_25"><div class="div_250px_ellipsis">{$array_field['field_label'] nofilter}</div></td>
                                                                                        <td>{$array_field['type'] nofilter}</td>                                                                                        
                                                                                        <td>{if $array_field['required'] eq '1'}{l s='Yes' mod='returnmanager'}{else}{l s='No' mod='returnmanager'}{/if}</td>
                                                                                        <td>{if $array_field['active'] eq '1'}{l s='Yes' mod='returnmanager'}{else}{l s='No' mod='returnmanager'}{/if}</td>
                                                                                        <td class="center" style="padding: 12px;">
                                                                                            <a style="margin-top: -26px;" href="javascript://" onclick="displayEditCustomFieldPopup({$array_field['id_velsof_rm_custom_fields'] nofilter})" type="11" class="velsof-glyphicons2 glyphicons pencil"><i title="{l s='Edit this custom field' mod='returnmanager'}"></i></a>                                                                                                
                                                                                            <a style="margin-top: -26px;" href="javascript://" onclick="deleteCustomFieldRow({$array_field['id_velsof_rm_custom_fields'] nofilter})" type="11" class="velsof-glyphicons2 glyphicons bin"><i title="{l s='Delete this custom field' mod='returnmanager'}"></i></a>
                                                                                        </td>
                                                                                    </tr>
                                                                                    {assign var=counter value=$counter+1}
                                                                                {/foreach}
                                                                            {/if}
                                                                            <tr id="tr_custom_fields_add_new">
                                                                                <td colspan="5"></td>
                                                                                <td class="left center"><a id="custom_field" style=" text-decoration:none;" data-toggle="modal" ><span><i class="process-icon-new"></i></span></a>{l s='Add New Custom Field' mod='returnmanager'}</td>
                                                                            </tr>  
                                                                        </tbody>
                                                                    </table>                                                                    
                                                                    <div class="modal fade" id="modal_custom_field" tab-index="-1" aria-hidden="true" aria-labelledby="modal-incentive-form">
                                                                        <div class="modal-dialog" style="width:50%">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <span class="font_popup_header">{l s='New Custom Field' mod='returnmanager'}</span>
                                                                                    <button type="button" class="close" onclick="closeModalForm('modal_custom_field')"><span aria-hidden="true">×</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
                                                                                </div>
                                                                                <div class="modal-body" style="padding-bottom:0;">
                                                                                    <div class="row">
                                                                                        <div class="span" style="margin-left:0; width:100%;">
                                                                                            <div id="modal_incentive_form_process_status" class="modal_process_status_blk alert" style="display:none;"></div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div style="overflow-y:auto !important;">
                                                                                        <table class="list form" style="width:100%">
                                                                                            <tbody id="custom_table_tbody">
                                                                                                <tr class="returnmanager_custom_field_form_fields">
                                                                                                    <td class="right"><span class="control-label">{l s='Field Label' mod='returnmanager'}</span>
                                                                                                        <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Label of the custom field.' mod='returnmanager'}"></i>
                                                                                                    </td>
                                                                                                    <td class="returnmanager_popup_form_field">
                                                                                                        <div class="span">
                                                                                                            <span class='float_left margin_right_20'>
                                                                                                                {foreach $languages as $language}
                                                                                                                    <input class="required_entry returnmanager_field_label {if $language_current neq $language['id_lang']}hidden_custom{/if}" type="text" id='field_label_language_{$language['id_lang'] nofilter}' name="custom_fields[field_label][{$language['id_lang'] nofilter}]">
                                                                                                                {/foreach}
                                                                                                            </span>
                                                                                                            <span class='float_left'>
                                                                                                                <select class="width_small" name="languages" onchange="changeLanguageBox(this, 'field_label')">
                                                                                                                    {foreach $languages as $language}
                                                                                                                        <option value="{$language['id_lang'] nofilter}" {if $language_current eq $language['id_lang']}selected{/if}>{$language['language_code'] nofilter}</option>
                                                                                                                    {/foreach}
                                                                                                                </select>
                                                                                                            </span>
                                                                                                            <span id="error_message_field_label" class="error_message new_line hidden_custom">Error!</span>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>

                                                                                                <tr class="returnmanager_custom_field_form_fields">
                                                                                                    <td class="right"><span class="control-label">{l s='Help Text (optional)' mod='returnmanager'}</span>
                                                                                                        <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Help text for the custom field.' mod='returnmanager'}"></i>
                                                                                                    </td>
                                                                                                    <td class="returnmanager_popup_form_field">
                                                                                                        <div class="span">
                                                                                                            <span class='float_left margin_right_20'>
                                                                                                                {foreach $languages as $language}
                                                                                                                    <input class="returnmanager_help_text {if $language_current neq $language['id_lang']}hidden_custom{/if}" type="text" id='help_text_language_{$language['id_lang'] nofilter}' name="custom_fields[help_text][{$language['id_lang'] nofilter}]">
                                                                                                                {/foreach}
                                                                                                            </span>
                                                                                                            <span class='float_left'>
                                                                                                                <select class="width_small" name="languages" onchange="changeLanguageBox(this, 'help_text')">
                                                                                                                    {foreach $languages as $language}
                                                                                                                        <option value="{$language['id_lang'] nofilter}" {if $language_current eq $language['id_lang']}selected{/if}>{$language['language_code'] nofilter}</option>
                                                                                                                    {/foreach}
                                                                                                                </select>
                                                                                                            </span>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>

                                                                                                <tr>
                                                                                                    <td class="right"><span class="control-label"><span class="required">*</span>{l s='Type' mod='returnmanager'}</span>
                                                                                                        <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Type of the custom input field.' mod='returnmanager'}"></i>
                                                                                                    </td>
                                                                                                    <td class="returnmanager_popup_form_field">
                                                                                                        <div class="span5">
                                                                                                            <select class="dropdn_templates" id="returnmanager_custom_field_type" name="custom_fields[type]" onchange="checkFieldType(this)">
                                                                                                                <option value="textbox">{l s='Text Box' mod='returnmanager'}</option>
                                                                                                                <option value="selectbox">{l s='Select Box' mod='returnmanager'}</option>
                                                                                                                <option value="textarea">{l s='Text Area' mod='returnmanager'}</option>
                                                                                                                <option value="radio">{l s='Radio Buttons' mod='returnmanager'}</option>
                                                                                                                <option value="checkbox">{l s='Check Boxes' mod='returnmanager'}</option>                                                                                                                
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>

                                                                                                <tr class="returnmanager_custom_field_form_fields hidden_custom" id="field_options">
                                                                                                    <td class="right"><span class="control-label"><span class="required">*</span>{l s='Field Options' mod='returnmanager'}</span>
                                                                                                        <i class="icon-question-sign" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Enter the data for options of the field.' mod='returnmanager'}"></i>
                                                                                                        <p class="help-block">{l s='Enter only one option in 1 line.' mod='returnmanager'}</p>
                                                                                                        <p class="help-block">{l s='Avoid blank lines.' mod='returnmanager'}</p>
                                                                                                        {*<p class="help-block">{l s='Accepted format example: m|Male' mod='returnmanager'}</p>
                                                                                                        <p class="help-block">{l s='                         f|Female' mod='returnmanager'}</p>*}
                                                                                                        <p class="help-block">{l s='Accepted format example' mod='returnmanager'}: m|{l s='Male' mod='returnmanager'}</p>
                                                                                                        <p class="help-block">f|{l s='Female' mod='returnmanager'}</p>
                                                                                                    </td>
                                                                                                    <td class="returnmanager_popup_form_field">
                                                                                                        <div class="span">
                                                                                                            <span class='float_left margin_right_20'>
                                                                                                                {foreach $languages as $language}
                                                                                                                    <textarea class="returnmanager_field_options {if $language_current neq $language['id_lang']}hidden_custom{/if}" id='field_options_language_{$language['id_lang'] nofilter}' name="custom_fields[field_options][{$language['id_lang'] nofilter}]"></textarea>
                                                                                                                {/foreach}
                                                                                                            </span>
                                                                                                            <span class='float_left'>
                                                                                                                <select class="width_small" name="languages" onchange="changeLanguageBox(this, 'field_options')">
                                                                                                                    {foreach $languages as $language}
                                                                                                                        <option value="{$language['id_lang'] nofilter}" {if $language_current eq $language['id_lang']}selected{/if}>{$language['language_code'] nofilter}</option>
                                                                                                                    {/foreach}
                                                                                                                </select>
                                                                                                            </span>
                                                                                                            <span id="error_message_field_options" class="error_message new_line hidden_custom">Error!</span>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>                                                                                               
                                                                                                <tr class="returnmanager_custom_field_form_fields">
                                                                                                    <td class="right"><span class="control-label">{l s='Default Value (optional)' mod='returnmanager'}</span>
                                                                                                        <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Default value of the custom input field.' mod='returnmanager'}"></i>
                                                                                                        <p class="help-block">{l s='For selectbox, radio or checkboxes, set the default value like this.' mod='returnmanager'} {l s=' Option' mod='returnmanager'}:- n|No, {l s='Default Value' mod='returnmanager'}:- n</p>
                                                                                                    </td>
                                                                                                    <td class="returnmanager_popup_form_field">
                                                                                                        <div class="span">
                                                                                                            <input class="" type="text" name="custom_fields[default_value]" value="">
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>

                                                                                                <tr>
                                                                                                    <td class="right"><span class="control-label"><span class="required">*</span>{l s='Validation Type' mod='returnmanager'}</span>
                                                                                                        <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Type of the validation you want to set for the field.' mod='returnmanager'}"></i>
                                                                                                        <p class="help-block">{l s='Validation type will be automatically set as None in case of Selectbox, Radio or Checkboxes.' mod='returnmanager'}</p>
                                                                                                    </td>
                                                                                                    <td class="returnmanager_popup_form_field">
                                                                                                        <div class="span5">
                                                                                                            <select class="dropdn_templates" name="custom_fields[validation_type]">
                                                                                                                <option value="0">{l s='None' mod='returnmanager'}</option>
                                                                                                                <option value="isInt">isInt</option>
                                                                                                                <option value="isName">isName</option>
                                                                                                                <option value="isEmail">isEmail</option>                                                                                                                
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>

                                                                                                <tr>
                                                                                                    <td class="right"><span class="control-label">{l s='Required' mod='returnmanager'}</span>
                                                                                                        <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Set field as required or not required.' mod='returnmanager'}"></i>
                                                                                                    </td>
                                                                                                    <td class="returnmanager_popup_form_field">
                                                                                                        <div class="form-group">
                                                                                                            <div class="col-lg-9">
                                                                                                                <span class="switch prestashop-switch fixed-width-lg">
                                                                                                                    <input type="radio" name="custom_fields[required]" id="custom_fields[required]_on" value="1">
                                                                                                                    <label for="custom_fields[required]_on">{l s='Yes' mod='returnmanager'}</label>
                                                                                                                    <input type="radio" name="custom_fields[required]" id="custom_fields[required]_off" value="0" checked="checked">
                                                                                                                    <label for="custom_fields[required]_off">{l s='No' mod='returnmanager'}</label>
                                                                                                                    <a class="slide-button btn"></a>
                                                                                                                </span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>

                                                                                                <tr>
                                                                                                    <td class="right"><span class="control-label">{l s='Active' mod='returnmanager'}</span>
                                                                                                        <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Set the field as active or inactive.' mod='returnmanager'}"></i>
                                                                                                    </td>
                                                                                                    <td class="returnmanager_popup_form_field">
                                                                                                        <div class="form-group">
                                                                                                            <div class="col-lg-9">
                                                                                                                <span class="switch prestashop-switch fixed-width-lg">
                                                                                                                    <input type="radio" name="custom_fields[active]" id="custom_fields[active]_on" value="1" checked="checked">
                                                                                                                    <label for="custom_fields[active]_on">{l s='Yes' mod='returnmanager'}</label>
                                                                                                                    <input type="radio" name="custom_fields[active]" id="custom_fields[active]_off" value="0">
                                                                                                                    <label for="custom_fields[active]_off">{l s='No' mod='returnmanager'}</label>
                                                                                                                    <a class="slide-button btn"></a>
                                                                                                                </span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer no_border">
                                                                                    <button type="button" onclick="closeModalForm('modal_custom_field')" class="btn btn-default">{l s='Close' mod='returnmanager'}</button>
                                                                                    <button type="button" onclick="kbrmsubmitForm()" class="btn btn-primary" id='custom_field_save'>
                                                                                        {l s='Save' mod='returnmanager'}
                                                                                        {*<img id='loader_add_form' class='loader_save_button hidden_custom' src='{$module_dir_url nofilter}/returnmanager/views/img/admin/ajax_loader.gif'/>*}
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Start -Modal Popup Edit Custom Field -->
                                                                    <div class="modal fade" id="modal_edit_custom_field_form" tab-index="-1" aria-hidden="true" aria-labelledby="modal-incentive-form">
                                                                        <!-- Render edit form here -->
                                                                    </div>
                                                                    <!-- End - Modal Popup Edit Custom Field -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--------------- End - Custom Field --------------------->
                                            </div>
                                        </div>
                                        {*<div class="navbar main hidden-print" style="width: 100%;">
                                            <div class="topbuttons" style="margin-right: 15px;">
                                                                                <button type="submit" value="1" onclick='return submitform("sub")' id="save_post_setting" name="" class="btn btn-default pull-right kbgc_general_settings_btn">
                                                                <i class="process-icon-save"></i> Save
                                                        </button>

                                                        <!--a href="#" onclick='return submitform("sub")'><span id="save_post_setting" class="btn btn-block btn-success action-btn">0000Save</span></a--><!--a href="index.php?controller=AdminModules&amp;token=56eb82319d7a8cb7a923a97697e44148"><span class="btn btn-block btn-danger action-btn">0000Cancel</span></a-->


                                                </div>
                                        </div>*}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            {**
            * To show the upgrade modal box
             * @date 20-05-2024
             * @author Ravi Kant Gutpa
             *}

<div id="kbUpgradeModal" class="kb_upgrade_modal modal">
  <!-- Modal content -->
  <div class="modal-content kb_modal-content">
    <span class="close">&times;</span>
    <p>In the free version, you cannot change or update this feature. <a href="https://www.knowband.com/prestashop-return-manager">Click here</a> to purchase the module to update these.</p>
  </div>
</div>


<script>
    var pat = "{$path|escape:'quotes':'UTF-8'}";
    var order_status_error = "{l s='Please select at least one order status for which return is allowed.' mod='returnmanager'}";
    var rm_view_ticket_text = "{l s='View Ticket' mod='returnmanager'}";
    var mod_dir = pat + 'returnmanager/';
    var ad = "{$ad|escape:'htmlall':'UTF-8'}";
    var iso = "{$iso|escape:'htmlall':'UTF-8'}";
    var module_path = "{$action|escape:'quotes':'UTF-8'}";
    var requireField = "{l s='Required field' mod='returnmanager'}";
    var requiredNumber = "{l s='Positive number value required' mod='returnmanager'}";
    var Notrequirefloat = "{l s='Please Enter an Integer' mod='returnmanager'}";
    var rm_date_error = "{l s='From date should be less than To date' mod='returnmanager'}";
    var rm_no_data_label = "{l s='No data found.' mod='returnmanager'}";
    var rm_order_text = "{l s='Order' mod='returnmanager'}";
    var rm_customer_text = "{l s='Customer' mod='returnmanager'}";
    var rm_product_text = "{l s='Product' mod='returnmanager'}";
    var rm_price_text = "{l s='Price' mod='returnmanager'}";
    var rm_qty_text = "{l s='Qty' mod='returnmanager'}";
    var rm_shipping_text = "{l s='Shipping Paid By' mod='returnmanager'}";
    var rm_type_text = "{l s='Type' mod='returnmanager'}";
    var rm_action_text = "{l s='Action' mod='returnmanager'}";
    {* changes by rishabh jain to add return id column in list*}
    var rm_return_id_text = "{l s='Return Id' mod='returnmanager'}";
    var rm_so_text = "{l s='Store Owner' mod='returnmanager'}";
    var rm_permission_err = "{l s='Permission error on /reports' mod='returnmanager'}";
    var rm_no_pending_text = "{l s='No Pending requests found.' mod='returnmanager'}";
    var rm_deny_return_text = "{l s='Deny Return' mod='returnmanager'}";
    var rm_change_status_text = "{l s='Change Status' mod='returnmanager'}";
    var rm_view_history_text = "{l s='View History' mod='returnmanager'}";
    var rm_comment_text = "{l s='Customer Notes' mod='returnmanager'}";
    var rm_no_active_text = "{l s='No Active requests found.' mod='returnmanager'}";
    var rm_complete_return_text = "{l s='Mark as Complete' mod='returnmanager'}";
    var rm_view_internal_note_text = "{l s='View Internal Notes' mod='returnmanager'}";
    var day_equal_error = "{l s='Min and Max days should be dfferent.' mod='returnmanager'}";
    var requiredField = "{l s='Required Field' mod='returnmanager'}";
    var deleteConfirmationText = "{l s='Are you sure you want to delete this value?' mod='returnmanager'}";
    var invalidEmailId = "{l s='Invalid Email Address' mod='returnmanager'}";
    var orderNotFound = "{l s='No order found for provided information.' mod='returnmanager'}";
    var orderedProductNotFound = "{l s='The requested product not found.' mod='returnmanager'}";
    var rm_ajax_failed = "{l s='Technical Error Occurred' mod='returnmanager'}";
    var rm_return_type_required = "{l s='Please select return type' mod='returnmanager'}";
    var rm_reason_required = "{l s='Please select reason' mod='returnmanager'}";
    var rm_allow_return_text = "{l s='Approve Return' mod='returnmanager'}";
    var rm_reason_text = "{l s='Return Reason' mod='returnmanager'}";
    var rm_view_image_text = "{l s='View Uploaded file' mod='returnmanager'}";
    var internal_note_success = "{l s='Internal Note added successfully' mod='returnmanager'}";
    var rm_toc_checked = "{l s='Please agree to terms & conditions' mod='returnmanager'}";
    var order_controller = "{$order_controller|escape:'quotes':'UTF-8'}";
    var customer_controller = "{$customer_controller|escape:'quotes':'UTF-8'}";
    var success_adding_policy = "{l s='Return Policy updated successfully' mod='returnmanager'}";
    var success_delete_mapping_categories = "{l s='Mapped categories deleted successfully' mod='returnmanager'}";
    var success_mapping_policy = "{l s='Return Policy mapped to products successfully' mod='returnmanager'}";
    var success_deleting_action = "{l s='Entry deleted successfully' mod='returnmanager'}";
    var success_status_update = "{l s='Status Updated successfully' mod='returnmanager'}";
    var success_adding_address = "{l s='Return address updated successfully' mod='returnmanager'}";
    var success_adding_status = "{l s='Return Status updated successfully' mod='returnmanager'}";
    {*changes by vishal for adding cancel functionality*}
    var success_adding_cancel = "{l s='Cancel Reason updated successfully' mod='returnmanager'}";
    var success_cancel_approval = "{l s='Return Request Approved successfully' mod='returnmanager'}";
    var success_cancel_denied = "{l s='Return Request Denied successfully' mod='returnmanager'}";
    {*changes end*}    
    var success_adding_reason = "{l s='Return Reason updated successfully' mod='returnmanager'}";
    var success_return_approval = "{l s='Return Request Approved successfully' mod='returnmanager'}";
    var success_return_denied = "{l s='Return Request Denied successfully' mod='returnmanager'}";
    var success_return_status_changed = "{l s='Return Status updated successfully' mod='returnmanager'}";
    var success_return_completed = "{l s='Return marked completed successfully' mod='returnmanager'}";
    var email_not_sent = "{l s='Unable to send notification email.' mod='returnmanager'}";
    var select_pros_placeholder = "{l s='Select Products' mod='returnmanager'}";
    var no_comments_text = "{l s='No comments from customer.' mod='returnmanager'}";
    var atleast_one_text = "{l s='At least one return option needs to be selected.' mod='returnmanager'}";
    var no_policy_txt = "{l s='No Policy' mod='returnmanager'}";
    var exceptional_error_txt = "{l s='Invalid Format' mod='returnmanager'}";
    var file_type_error = "{l s='Invalid Format, Please select only image and zip file' mod='returnmanager'}";
    var image_size_error = "{l s='File size should not greater than 4 MB' mod='returnmanager'}";
    var rm_return_id_text = "{l s='Return Id' mod='returnmanager'}";
    var internal_note_success = "{l s='Internal Note added successfully' mod='returnmanager'}";
    var rm_view_internal_note_text = "{l s='View Internal Notes' mod='returnmanager'}";
    var number_days_error = "{l s='Please enter days between 0 to 1000.' mod='returnmanager'}";
    var template_subject_error = "{l s='Please enter Template Subject.' mod='returnmanager'}";
    {* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to send Test Email *}
    var email_sent = "{l s='Email has been sent successfully.' mod='returnmanager'}";
    {* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to send Test Email *}
    var credit_return_error = "{l s='Please enter Credit Return Message.' mod='returnmanager'}";
    var Notrequirefloat = "{l s='Days Cannot be in decimal,Please enter an Integer' mod='returnmanager'}";
    var refund_return_error = "{l s='Please enter Refund Return Message.' mod='returnmanager'}";
    var replacement_return_error = "{l s='Please enter Replacement Return Message.' mod='returnmanager'}";
    var return_address_error = "{l s='Please enter Return Address.' mod='returnmanager'}";
    var return_guidelines_error = "{l s='Please enter Return Guidelines.' mod='returnmanager'}";
    var policy_title_error = "{l s='Please enter Policy Title.' mod='returnmanager'}";
    var policy_terms_error = "{l s='Please enter Policy Terms & Conditions.' mod='returnmanager'}";
    var credit_error = "{l s='Please enter days at Credit.' mod='returnmanager'}";
    var refund_error = "{l s='Please enter days at Refund.' mod='returnmanager'}";
    var refund_min_error = "{l s='Min refund days can not be greater than max refund days.' mod='returnmanager'}";
    var credit_min_error = "{l s='Min credit days can not be greater than max credit days.' mod='returnmanager'}";
    var replacement_min_error = "{l s='Min replacement days can not be greater than max replacement days.' mod='returnmanager'}";
    var replacement_error = "{l s='Please enter days at Replacement.' mod='returnmanager'}";
    var reason_error = "{l s='Please enter the Reason.' mod='returnmanager'}";
    var status_error = "{l s='Please enter the Status.' mod='returnmanager'}";
    var address_error = "{l s='Required Field.' mod='returnmanager'}";
    var email_required_error = "{l s='Please enter the Email-Id.' mod='returnmanager'}";
    var order_required_error = "{l s='Please enter the Reference Id.' mod='returnmanager'}";
    var select_cat_placeholder = "{l s='Select Category' mod='returnmanager'}";
    var select_all_dropdown = "{l s='Select all' mod='returnmanager'}";
    var all_selected = "{l s='All selected' mod='returnmanager'}";
    var count_selected = "{l s='# of % selected' mod='returnmanager'}";
    var no_matches_found = "{l s='No matches found' mod='returnmanager'}";
    var category_already_mapped = "{l s='Following category(s) are already mapped:' mod='returnmanager'}";
    var category_already_mapped_err = "{l s='This category is already mapped with some other policy.' mod='returnmanager'}";
    var kb_select_category = "{l s='Select Category' mod='returnmanager'}";
    var kb_select_all = "{l s='Select All' mod='returnmanager'}";
    var kb_select_order_status = "{l s='Select order Status' mod='returnmanager'}";
    var kb_all_selected = "{l s='All selected' mod='returnmanager'}";
    var kb_no_match_found = "{l s='No matches found' mod='returnmanager'}";
    
    
    $(document).ready(function () {
        /*
         * Wait for TinyMCE script before calling tinySetup (avoids tinyMCE is not defined).
         * 21-07-2026
         */
        function kbInitRmTinyMCE() {
            if (typeof tinyMCE === 'undefined' && typeof tinymce === 'undefined') {
                setTimeout(kbInitRmTinyMCE, 50);
                return;
            }
            tinySetup({
                height: "100",
                editor_selector: "rm_texteditor",
                extended_valid_elements: "img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]",
                setup: function (ed) {
                    ed.on('keydown', function (ed, e) {
                        if (typeof tinyMCE !== 'undefined') {
                            tinyMCE.triggerSave();
                        } else if (typeof tinymce !== 'undefined') {
                            tinymce.triggerSave();
                        }
                        if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
                            textarea = $('#' + tinymce.activeEditor.id);
                        }
                    });
                }
            });
        }
        kbInitRmTinyMCE();
    });
</script>

<style>
table.form{
    width:100%;
}

{*changes by vishal on 14 august 2020 for resolving admin return fuctionality*}

.hidden_custom
{
    display: none !important;
}

.rm-required
{
    color: #FF0000;
}

.errorsmall_custom
{
    color: #FF0000;
}

.rm_form_note{
    display: none;
}

#rm_multi_header{
        float: left;
    width: 72%;
}

.kb_rm_toc_block{
    margin-top: 7px;
    border: 1px solid #ababab;
    height: 100px;
    overflow-y: scroll;
    padding: 5px;
    display:none;
}

{*changes end by vishal on 14 august 2020 for resolving admin return fuctionality*}

.rm_popup_pro_name{
    color:black;
}

.rm_popup_addr{
    color:black;
}

.required
{
    color: #FF0000;
}

.no_border
{
        border: none !important;
}

.float_left
{
    float: left;
}

.margin_right_20
{
    margin-right: 20px
}

.error_message
{
    color: #FF0000;
    font-size: 11px;
    display: block;
}

.new_line
{
    clear: both;
}

.font_popup_header
{
    font-weight: bold;
    font-size: 16px;
}

.returnmanager_popup_form_field
{
    padding: 10px 10px 10px 10px
}
#menuVel li a.glyphicons img {
    position: absolute;
    left: 15px;
    top: 10px;
}
#menuVel .slim-scroll > ul > li.active > a, #menuVel > ul > li.active > a {
    background: #dff3f8;
    color: #0092c8 !important;
    border-left: 5px solid #4ac7e0;
}
/*For Table header*/
.pure-table thead {
    background: #ecf6fb;
}
.pure-table tbody tr td, .pure-table thead tr th {
    font-size: 13px !important;
}
.pure-table-odd td {
    background-color: #fff;
}
.pure-table tr {
    border-bottom: 1px solid #eaf0f3;
}
.pure-table tbody tr td {
    border-left: 0;
    padding-top: 10px;
    padding-bottom: 10px;
}
.pure-table td:last-child, .pure-table th:last-child{
    border-right:0;
}
.pure-table tbody tr:hover td {
    background: #f1f6f9 !important;
}	


.pure-table tbody tr td.list_action_btn {
    width: 84px;
}
table.pure-table a.velsof-glyphicons2.glyphicons.pencil i:before {
    color: #526d9e !important;
}
table.pure-table a.velsof-glyphicons2.glyphicons.bin i:before {
    color: #e6644e !important;
}
table.pure-table a.velsof-glyphicons2.glyphicons.git_merge i:before {
    color: #902ece !important;
}
table.pure-table.velsof-pure-table tbody#rm_active_return_list td.rm_velsof_action .velsof-glyphicons:nth-child(even) i:before {
    color: #e6644e!important;
}
tbody#rm_pending_returns_list td.rm_velsof_action .velsof-glyphicons:nth-child(even) i:before {
    color: #e6644e!important;
}
tbody#rm_pending_returns_list tr.rm_pending_returns .rm_velsof_action {
    text-align: left;
}
#address_records a:nth-child(2) i {
    color: #e6644e;
    font-size: 16px;
}
#address_records a:first-child i {
    color: #526d9e;
    font-size: 16px;
}

.list_action_btn .glyphicons.remove {
    display: inline-block;
    vertical-align: middle;
    margin-top: 5px;
}
table.pure-table.velsof-pure-table tbody#rm_active_return_list td.rm_velsof_action .velsof-glyphicons {
    min-height: 30px;
    display: inline-block;
    margin: 0;
    width: auto;
    padding: 0;
    min-width: 20px;
}
.policy-responsive .pure-table tr td.rm_velsof_action a:nth-child(3) i:before {
   color: #9a2fdc !important;
}
.policy-responsive .pure-table tr td.rm_velsof_action a:nth-child(2) i:before {
color: #e4466e !important;
}
.policy-responsive .pure-table tr td.rm_velsof_action a:nth-child(4) i:before {
color: #07af14 !important;
}
.policy-responsive .pure-table tr td.rm_velsof_action a:nth-child(5) i:before {
color: #b1713b !important;
}
</style>

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
* Return Manager Admin Panel
*}
