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
<script>
        var previous_selected_status = '{l s='The status you have selected to change is already the present status' mod='returnmanager'}';
        var previous_selected_priority = '{l s='The priority you have selected to change is already the present priority' mod='returnmanager'}';
        var confirm_msg = '{l s='Are u sure' mod='returnmanager'}'; 
        velovalidation.setErrorLanguage({
        empty_field: "{l s='Post reply field can not be empty.' mod='returnmanager'}",
        html_tags: "{l s='Post reply field Should not contain any HTML tags.' mod='returnmanager'}",
        });
</script>
<style>
.vel_error_msg{
    color:red;
}
.error_field{
    border-color:red !important;
}
</style>
    <div class="col-lg-4">
        <div class="panel clearfix" style="height:230px; overflow-y: auto;">
            <div class="panel-heading">
                <i class="icon-info-sign"></i> {l s='Ticket Information' mod='returnmanager'}
            </div>
            <div class="form-horizontal">
                <div class="row">
                        <label class="control-label col-lg-4"><b>{l s='Ticket Number' mod='returnmanager'}:</b></label>
                        <div class="col-lg-8">
                                <p class="form-control-static">{$ticket->ticket_number|escape:'html':'UTF-8'}</p>
                        </div>
                </div>
                <div class="row">
                        <label class="control-label col-lg-4"><b>{l s='Status' mod='returnmanager'}:</b></label>
                        <div class="col-lg-8">
                                <p class="form-control-static">{RmTicket::getStateLabelById($ticket->status)|escape:'html':'UTF-8'}</p>
                        </div>
                </div>
                <div class="row">
                        <label class="control-label col-lg-4"><b>{l s='Arrived Date' mod='returnmanager'}:</b></label>
                        <div class="col-lg-8">
                            <p class="form-control-static"><span><i class="icon-calendar"></i> {Tools::displayDate($ticket->date_add)|escape:'html':'UTF-8'} <i class="icon-time"></i> {$ticket->date_add|date_format:'%I:%M %p'}</span></p>
                        </div>
                </div>
                <div class="row">
                        <label class="control-label col-lg-4"><b>{l s='Last Update' mod='returnmanager'}:</b></label>
                        <div class="col-lg-8">
                            <p class="form-control-static"><span><i class="icon-calendar"></i> {Tools::displayDate($ticket->date_upd)|escape:'html':'UTF-8'} <i class="icon-time"></i> {$ticket->date_upd|date_format:'%I:%M %p'}</span></p>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="panel clearfix" style="height:230px; overflow-y: auto;">
            <div class="panel-heading">
                <i class="icon-info-sign"></i> {l s='Customer Information' mod='returnmanager'}
            </div>
            <div class="form-horizontal">
                <div class="row">
                    <label class="control-label col-lg-4"><b>{l s='Name' mod='returnmanager'}:</b></label>
                        <div class="col-lg-8">
                                <p class="form-control-static">{$customer_name|escape:'html':'UTF-8'}</p>
                        </div>
                </div>
                <div class="row">
                        <label class="control-label col-lg-4"><b>{l s='Email' mod='returnmanager'}:</b></label>
                        <div class="col-lg-8">
                                <p class="form-control-static">{$customer_email|escape:'html':'UTF-8'}</p>
                        </div>
                </div>
                <div class="row">
                        <label class="control-label col-lg-4"><b>{l s='Phone' mod='returnmanager'}:</b></label>
                        <div class="col-lg-8">
                                <p class="form-control-static">{$customer_phone_number|escape:'html':'UTF-8'}</p>
                        </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3">
        <div class="well" style="height:230px; overflow-y: auto;">
            <p><b>{l s='Quick Change Status Action' mod='returnmanager'}:</b></p>
            <div>
                <select id="mp_quick_ticket_state" name="mp_quick_ticket_state" >
                    {foreach $ticket_states as $key => $val}
                            
                        <option {if $key != $ticket->status} value="{$post_reply_action nofilter}&quickstatechange=1&state={$key|intval}&id_rm_ticket={$ticket->id|intval}"  {else} value='' selected {/if}>{$val|escape:'htmlall':'UTF-8'}</option>
                            
                    {/foreach}
                </select>    
                <button class="btn btn-success" style="margin-top: 5px;" onclick="return quick_change_action('mp_quick_ticket_state')">{l s='Change' mod='returnmanager'}</button>
            </div>
            <div class="clearfix"></div>
            <br><br><br>  
        </div>
        <div class="clearfix"></div>
        {*Start Changes to fix the issue of wrong Ticket System Page view on Admin Side
         * Removing extra </div> which is causing the Issue
         * NASep2023 div_ticket
         * @date 18-09-2023
         * @modifier Nikhil Aggarwal *}
        {* </div> *}
        {* Changes end by Nikhil *}
    </div>
    <div class="clearfix"></div>
    
       <div class="panel" style="height:315px; overflow-y: auto;">
            <div class="panel-heading">
                <i class="icon-inbox"></i> {l s='Issue Information' mod='returnmanager'}
            </div>
            <div class="row">
                <h4>{l s='Title' mod='returnmanager'}:</h4>
                <p>{$ticket->subject|escape:'html':'UTF-8'}</p>
            </div>
            <div class="row">
                <h4>{l s='Summary' mod='returnmanager'}:</h4>
                <div style="min-height:45px; overflow-y: auto;">
                    <p>{$ticket_summary|escape:'html':'UTF-8'}</p>    
                </div>
            </div>
            <div class="clearfix"></div>
        </div> 
    
    <div class="panel">
        <form id="mp_post_reply_form" class="defaultForm form-horizontal" action="{$post_reply_action|escape:'html':'UTF-8'}" method="post">
            <h3>{l s='Your Reply to customer' mod='returnmanager'}</h3>
            <div class="form-wrapper">
                <div class="form-group">
                    <label for="mp_ticket_state" class="control-label col-lg-3 ">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='write your reply here to send seller' mod='returnmanager'}">
                            {l s='Post Reply' mod='returnmanager'}<span style="color:red">*</span>
                        </span>
                    </label>
                    <div class="col-lg-9 ">
                        <textarea cols="30" rows="10" name="mp_reply_message"></textarea>    
                    </div>
                </div>
                <div class="form-group">							
                    <label for="mp_ticket_state" class="control-label col-lg-3 ">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='Select the state assign to this ticket' mod='returnmanager'}">
                            {l s='State' mod='returnmanager'}
                        </span>
                    </label>
                    <div class="col-lg-9 ">
                        <select name="mp_ticket_state" class=" fixed-width-xl" id="mp_ticket_state">
                            {foreach $ticket_states as $key => $val}
                                <option value="{$key|intval}" {if $key == $ticket->status}selected="selected"{/if} >{$val|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>		
                </div>
            </div>
            <div class="panel-footer">
                <input type="hidden" name="id_rm_ticket" value="{$ticket->id|intval}" />
                <input type="hidden" name="submitMpTicketReply" value="1" />
                <button class="btn btn-default pull-right" name="submitMpTicketReply" onclick=" return validateAdminReplyForm()"><i class="process-icon-mail-reply"></i> {l s='Send' mod='returnmanager'}</button>
            </div>
        </form>
    </div>
    
    <div class="panel">
        <h3>
            <i class="icon-clock-o"></i>
            {l s='Thread Timeline' mod='returnmanager'}
        </h3>
        <div class="timeline">
            
            {if count($timelined_threads) > 0 }
                {foreach $timelined_threads as $thread}
                   {* <article class="timeline-item {if $thread['is_seller'] eq 0}alt{/if}">*}
                        <div class="timeline-caption">
                            <div class="timeline-panel arrow {if $thread['reply_by'] eq 0}arrow-right{else}arrow-left{/if}">
                                <span class="timeline-icon" {if $issue_thread_id == $thread['id_rm_ticket_thread']}style="background-color:Royalblue"{/if}>
                                    <i class="icon-envelope"></i>
                                </span>
                                <span class="timeline-date"><i class="icon-calendar"></i> {Tools::displayDate($thread['date_add'])|escape:'html':'UTF-8'} - <i class="icon-time"></i> {$thread['date_add']|date_format:'%I:%M %p'}</span>
                                {*{if $issue_thread_id == $thread['id_rm_ticket_thread']}
                                    <p>{$thread['message']|escape:'htmlall':'UTF-8'}</p>
                                {else}*}
                                    <span>{l s='Message by' mod='returnmanager'} 
                                        <span class="badge">
                                            {if $thread['reply_by'] eq 1}
                                                    {$customer_name|escape:'html':'UTF-8'}
                                            {else}
                                                {l s='Admin' mod='returnmanager'}
                                            {/if}
                                        </span>
                                        <br>
                                      
                                        <p>{nl2br($thread['message']) nofilter}</p>
                                    </span>    
                                {*{/if}*}
                            </div>
                        </div>
                    </article>
                {/foreach}
            {/if}		
        </div>
    </div>