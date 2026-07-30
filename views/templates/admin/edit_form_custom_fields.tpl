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
*}
<div class="modal-dialog" style="width:50%">

    <div class="modal-content">
        <div class="modal-header">
            <span class="font_popup_header">{l s='Edit Custom Field' mod='returnmanager'}</span>
            <button type="button" class="close" onclick="closeModalForm('modal_edit_custom_field_form')"><span aria-hidden="true">×</span><span class="sr-only">{l s='Close' mod='returnmanager'}</span></button>
        </div>
        <div class="modal-body" style="padding-bottom:0;">
            <div class="row">
                <div class="span" style="margin-left:0; width:100%;">
                    <div id="modal_incentive_form_process_status" class="modal_process_status_blk alert" style="display:none;"></div>
                </div>
            </div>
            <div style="overflow-y:auto !important;" id="id_parent_edit_form">
                
                <input class="hidden_custom" type="hidden" name="edit_custom_fields[id_velsof_rm_custom_fields]" value="{$id_velsof_rm_custom_fields nofilter}">
                
                <table class="list form" style="width:100%">
                    <tbody id="custom_table_tbody">

                        <tr class="returnmanager_custom_field_form_fields">
                            <td class="right"><span class="control-label">{l s='Field Label' mod='returnmanager'}</span>
                                <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" title="{l s='Label of the custom field.' mod='returnmanager'}"></i>
                            </td>
                            <td class="returnmanager_popup_form_field">
                                <div class="span">
                                    <span class='float_left margin_right_20'>
                                        {foreach $languages as $language}
                                            <input class="required_entry returnmanager_edit_field_label {if $language_current neq $language['id_lang']}hidden_custom{/if}" type="text" id='edit_field_label_language_{$language['id_lang'] nofilter}' name="edit_custom_fields[field_label][{$language['id_lang'] nofilter}]" value="{$custom_field_lang_details[$language['id_lang']]['field_label'] nofilter}">
                                        {/foreach}
                                    </span>
                                    <span class='float_left'>
                                        <select class="width_small" name="languages" onchange="changeLanguageBox(this, 'edit_field_label')">
                                            {foreach $languages as $language}
                                                <option value="{$language['id_lang'] nofilter}" {if $language_current eq $language['id_lang']}selected{/if}>{$language['language_code'] nofilter}</option>
                                            {/foreach}
                                        </select>
                                    </span>
                                    <span id="error_message_edit_field_label" class="error_message new_line hidden_custom">Error!</span>
                                </div>
                            </td>
                        </tr>

                        <tr class="returnmanager_custom_field_form_fields">
                            <td class="right"><span class="control-label">{l s='Help Text (optional)' mod='returnmanager'}</span>
                                <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" title="{l s='Help text for the custom field.' mod='returnmanager'}"></i>
                            </td>
                            <td class="returnmanager_popup_form_field">
                                <div class="span">
                                    <span class='float_left margin_right_20'>
                                        {foreach $languages as $language}
                                            <input class="returnmanager_edit_help_text {if $language_current neq $language['id_lang']}hidden_custom{/if}" type="text" id='edit_help_text_language_{$language['id_lang'] nofilter}' name="edit_custom_fields[help_text][{$language['id_lang'] nofilter}]" value="{$custom_field_lang_details[$language['id_lang']]['field_help_text'] nofilter}">
                                        {/foreach}
                                    </span>
                                    <span class='float_left'>
                                        <select class="width_small" name="languages" onchange="changeLanguageBox(this, 'edit_help_text')">
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
                                <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" title="Type of the custom input field."></i>
                            </td>
                            <td class="returnmanager_popup_form_field">
                                <div class="span5">
                                    <select class="dropdn_templates" id="returnmanager_edit_custom_field_type" name="edit_custom_fields[type]"  onchange="checkFieldTypeEdit(this)">
                                        <option value="textbox" {if $custom_field_basic_details['type'] eq "textbox"}selected{/if}>{l s='Text Box' mod='returnmanager'}</option>
                                        <option value="selectbox" {if $custom_field_basic_details['type'] eq "selectbox"}selected{/if}>{l s='Select Box' mod='returnmanager'}</option>
                                        <option value="textarea" {if $custom_field_basic_details['type'] eq "textarea"}selected{/if}>{l s='Text Area' mod='returnmanager'}</option>
                                        <option value="radio" {if $custom_field_basic_details['type'] eq "radio"}selected{/if}>{l s='Radio Buttons' mod='returnmanager'}</option>
                                        <option value="checkbox" {if $custom_field_basic_details['type'] eq "checkbox"}selected{/if}>{l s='Check Boxes' mod='returnmanager'}</option>                                        
                                    </select>
                                </div>
                            </td>
                        </tr>

                        <tr class="returnmanager_edit_custom_field_form_fields {if $show_option_field eq 0}hidden_custom{/if}" id="edit_field_options">
                            <td class="right"><span class="control-label"><span class="required">*</span>{l s='Field Options' mod='returnmanager'}</span>
                                <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" title="{l s='Enter the data for options of the field.' mod='returnmanager'}"></i>
                                <p class="help-block">{l s='Enter only one option in 1 line.' mod='returnmanager'}</p>
                                <p class="help-block">{l s='Avoid blank lines.' mod='returnmanager'}</p>                                
                                <p class="help-block">{l s='Accepted format example' mod='returnmanager'}: m|{l s='Male' mod='returnmanager'}</p>
                                <p class="help-block">f|{l s='Female' mod='returnmanager'}</p>
                            </td>
                            <td class="returnmanager_popup_form_field">
                                <div class="span">
                                    <span class='float_left margin_right_20'>
                                        {foreach $languages as $language}
                                            <textarea class="returnmanager_edit_field_options {if $language_current neq $language['id_lang']}hidden_custom{/if}" id='edit_field_options_language_{$language['id_lang'] nofilter}' name="edit_custom_fields[field_options][{$language['id_lang'] nofilter}]">{$custom_field_option_details[$language['id_lang']] nofilter}</textarea>
                                        {/foreach}
                                    </span>
                                    <span class='float_left'>
                                        <select class="width_small" name="languages" onchange="changeLanguageBox(this, 'edit_field_options')">
                                            {foreach $languages as $language}
                                                <option value="{$language['id_lang'] nofilter}" {if $language_current eq $language['id_lang']}selected{/if}>{$language['language_code'] nofilter}</option>
                                            {/foreach}
                                        </select>
                                    </span>
                                    <span id="error_message_edit_field_options" class="error_message new_line hidden_custom">Error!</span>
                                </div>
                            </td>
                        </tr>
                        
                        <tr class="returnmanager_custom_field_form_fields">
                            <td class="right"><span class="control-label">{l s='Default Value (optional)' mod='returnmanager'}</span>
                                <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" title="{l s='Default value of the custom input field.' mod='returnmanager'}"></i>
                                <p class="help-block">{l s='For selectbox, radio or checkboxes, set the default value like this.' mod='returnmanager'} {l s=' Option' mod='returnmanager'}:- n|No, {l s='Default Value' mod='returnmanager'}:- n</p>
                            </td>
                            <td class="returnmanager_popup_form_field">
                                <div class="span">
                                    <input class="" type="text" name="edit_custom_fields[default_value]" value="{$custom_field_basic_details['default_value'] nofilter}">
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="right"><span class="control-label"><span class="required">*</span>{l s='Validation Type' mod='returnmanager'}</span>
                                <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" title="{l s='Type of the validation you want to set for the field.' mod='returnmanager'}"></i>
                                <p class="help-block">{l s='Validation type will be automatically set as None in case of Selectbox, Radio or Checkboxes.' mod='returnmanager'}</p>
                            </td>
                            <td class="returnmanager_popup_form_field">
                                <div class="span5">
                                    <select {if $custom_field_basic_details['type'] eq "selectbox" || $custom_field_basic_details['type'] eq "radio" || $custom_field_basic_details['type'] eq "checkbox"}disabled="disabled"{/if} class="dropdn_templates" name="edit_custom_fields[validation_type]">
                                        <option value="0" {if $custom_field_basic_details['validation_type'] eq "0"}selected{/if}>{l s='None' mod='returnmanager'}</option>
                                        <option value="isInt" {if $custom_field_basic_details['validation_type'] eq "isInt"}selected{/if}>isInt</option>
                                        <option value="isName" {if $custom_field_basic_details['validation_type'] eq "isName"}selected{/if}>isName</option>
                                        <option value="isEmail" {if $custom_field_basic_details['validation_type'] eq "isEmail"}selected{/if}>isEmail</option>                                        
                                    </select>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="right"><span class="control-label">{l s='Required' mod='returnmanager'}</span>
                                <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" title="{l s='Set field as required or not required.' mod='returnmanager'}"></i>
                            </td>
                            <td class="returnmanager_popup_form_field">
                                <div class="form-group">
                                    <div class="col-lg-9">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="edit_custom_fields[required]" id="edit_custom_fields[required]_on" value="1" {if $custom_field_basic_details['required'] eq "1"}checked="checked"{/if}>
                                            <label for="edit_custom_fields[required]_on">{l s='Yes' mod='returnmanager'}</label>
                                            <input type="radio" name="edit_custom_fields[required]" id="edit_custom_fields[required]_off" value="0" {if $custom_field_basic_details['required'] eq "0"}checked="checked"{/if}>
                                            <label for="edit_custom_fields[required]_off">{l s='No' mod='returnmanager'}</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="right"><span class="control-label">{l s='Active' mod='returnmanager'}</span>
                                <i class="icon-question-sign tooltip_color" data-toggle="tooltip" data-placement="top" title="{l s='Set the field as active or inactive.' mod='returnmanager'}"></i>
                            </td>
                            <td class="returnmanager_popup_form_field">
                                <div class="form-group">
                                    <div class="col-lg-9">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="edit_custom_fields[active]" id="edit_custom_fields[active]_on" value="1" {if $custom_field_basic_details['active'] eq "1"}checked="checked"{/if}>
                                            <label for="edit_custom_fields[active]_on">{l s='Yes' mod='returnmanager'}</label>
                                            <input type="radio" name="edit_custom_fields[active]" id="edit_custom_fields[active]_off" value="0" {if $custom_field_basic_details['active'] eq "0"}checked="checked"{/if}>
                                            <label for="edit_custom_fields[active]_off">{l s='No' mod='returnmanager'}</label>
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
            <button type="button" onclick="closeModalForm('modal_edit_custom_field_form')" class="btn btn-default">{l s='Close' mod='returnmanager'}</button>
            <button type="button" onclick="submitEditForm()" class="btn btn-primary">
                {l s='Save' mod='returnmanager'}
                {*<img id='loader_edit_form' class='loader_save_button hidden_custom' src='{$module_dir_url nofilter}/supercheckout/views/img/admin/ajax_loader.gif'/>*}
            </button>
        </div>
    </div>
</div>