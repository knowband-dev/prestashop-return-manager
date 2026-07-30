{extends file=$layout}
{block name='content'}
{if isset($confirmations)}
	<p class="alert alert-success">
        {$confirmations|escape:'html':'UTF-8'}
    </p>
    <p>hey</p>
{/if}   
<h1 class="small_heading" style="margin-top:0px;">{l s='Open A New Ticket' mod='returnmanager'}</h1>
<div id="kbmpss-container">    
    <p>{l s='Please fill in the form below to open a new ticket.' mod='returnmanager'}</p>
    <div class="block" style="margin-top:0;margin-bottom:5%;">
        <form id="kbmpss-new-ticket-form" method="post" action="{$link->getModuleLink('returnmanager', 'admincontact', ['id_return' => $id_return], (bool)Configuration::get('PS_SSL_ENABLED'))|escape:'html':'UTF-8'}">
            <input type="hidden" name="kbmpss_new_ticket_submission" value="1" />
            <h4 class="title_block">{l s='Contact Information' mod='returnmanager'}</h4>
            
            <div class="block_content">
                <div class="kbmpss-form-group">
                    <div class="kbmpss-form-label">
                        <label for="kbmpss-new-ticket-email">{l s='Email Address' mod='returnmanager'}<em>*</em> :</label>
                    </div>
                    <div class="kbmpss-form-field">
                        <input class="small_field" type="text" name="kbmpss_new_ticket[cus_email]" id="kbmpss-new-ticket-email" value="{if $is_logged}{$customer_info['cus_email']|escape:'html':'UTF-8'}{/if}" {if $is_logged}readonly="true"{/if}/>
                    </div>
                </div>
                <div class="kbmpss-form-group">
                    <div class="kbmpss-form-label">
                        <label for="kbmpss-new-ticket-fname">{l s='First Name' mod='returnmanager'}<em>*</em> :</label>
                    </div>
                    <div class="kbmpss-form-field">
                        <input class="small_field" type="text" name="kbmpss_new_ticket[cus_fname]" id="kbmpss-new-ticket-fname" value="{if $is_logged}{$customer_info['cus_fname']|escape:'html':'UTF-8'}{/if}" />
                    </div>
                </div>
                <div class="kbmpss-form-group">
                    <div class="kbmpss-form-label">
                        <label for="kbmpss-new-ticket-lname">{l s='Last Name' mod='returnmanager'} :</label>
                    </div>
                    <div class="kbmpss-form-field">
                        <input class="small_field" type="text" name="kbmpss_new_ticket[cus_lname]" id="kbmpss-new-ticket-lname" value="{if $is_logged}{$customer_info['cus_lname']|escape:'html':'UTF-8'}{/if}" />
                    </div>
                </div>
                <div class="kbmpss-form-group">
                    <div class="kbmpss-form-label">
                        <label for="kbmpss-new-ticket-phone">{l s='Phone Number' mod='returnmanager'} :</label>
                    </div>
                    <div class="kbmpss-form-field">
                        <input class="small_field" type="text" name="kbmpss_new_ticket[phone_number]" id="kbmpss-new-ticket-phone" value="" />
                    </div>
                </div> 
            </div>
            
            <h4 class="title_block">{l s='Ticket Information' mod='returnmanager'}</h4>
            
            <div class="block_content">
                <div class="kbmpss-form-group">
                    <div class="kbmpss-form-label">
                        <label for="kbmpss-new-ticket-summary">{l s='Subject' mod='returnmanager'}<em>*</em> :</label>
                    </div>
                    <div class="kbmpss-form-field">
                        <input type="text" name="kbmpss_new_ticket[subject]" id="kbmpss-new-ticket-subject" value="" />
                    </div>
                </div>
                <div class="kbmpss-form-group">
                    <div class="kbmpss-form-label">
                        <label for="kbmpss-new-ticket-issue">{l s='Issue' mod='returnmanager'}<em>*</em> :</label>
                    </div>
                    <div class="kbmpss-form-field">
                        <textarea rows="8" name="kbmpss_new_ticket[message]" id="kbmpss-new-ticket-issue"></textarea>
                    </div>
                </div> 
            </div>
            
            <div class="kbmpss-form-footer">
                <button type="button" class="btn btn-default button button-medium" name="submitticket" onclick="validateNewIssue()">
                    <span>{l s='Submit' mod='returnmanager'}</span>
                </button>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        var new_ticket_required_label = '{l s='Required field' mod='returnmanager'}';
        var new_ticket_invalid_value = '{l s='Invalid value' mod='returnmanager'}';
        var new_ticket_invalid_phone = '{l s='Invalid phone number' mod='returnmanager'}';
        var new_ticket_mini_chars = '{l s='Minimum 30 characters required' mod='returnmanager'}';
    </script>
</div>
{/block}        
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
* @copyright 2016 knowband
* @license   see file: LICENSE.txt
*}