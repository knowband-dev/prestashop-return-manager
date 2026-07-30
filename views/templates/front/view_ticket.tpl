{extends file=$layout}
{block name='content'}
{if isset($confirmations)}
    <p class="alert alert-success">
        {$confirmations|escape:'html':'UTF-8'}
    </p>
{/if}
{if isset($show_success)}
<div class="alert alert-success">
    <strong> {l s='Your Ticket has been created successfully.' mod='returnmanager'} </strong>
  </div>
{/if}
{if isset($thread_success_message)}
<div class="alert alert-success">
    <strong> {l s='Your Thread has been created successfully.' mod='returnmanager'} </strong>
</div>
{/if}
<h1 class="small_heading">{l s='Ticket' mod='returnmanager'} #{$ticket->ticket_number|escape:'html':'UTF-8'}</h1>
<div id="kbmpss-container">
    <table class="kbmpss-table-list">
        <tbody>
            <tr>
                <td class="left grey-bg">{l s='Status' mod='returnmanager'}</td>
                <td class="left" style="background-color:white;">{$ticket->status|escape:'html':'UTF-8'}</td>
            </tr>
            <tr>
                <td class="grey-bg  left">{l s='Name' mod='returnmanager'}</td>
                <td class="left" style="background-color:white;">{$ticket->cus_fname|escape:'html':'UTF-8'} {$ticket->cus_lname|escape:'html':'UTF-8'}</td>
            </tr>
            <tr>
                <td class="grey-bg  left">{l s='Email' mod='returnmanager'}</td>
                <td class="left" style="background-color:white;">{$ticket->cus_email|escape:'html':'UTF-8'}</td>
            </tr>
            <tr>
                <td class="grey-bg  left">{l s='Phone Number' mod='returnmanager'}</td>
                <td class="left" style="background-color:white;">{$ticket->phone_number|escape:'html':'UTF-8'}</td>
            </tr>
            <tr>
                <td class="grey-bg  left">{l s='Created Date' mod='returnmanager'}</td>
                {* Changes to fix the issue of 500 error because of the different number of parameters in the function
                   Adding PS Version condition to call the Tools::displayDate()
                   NAFeb2024 displaydate
                   @date 09-02-2024
                   @modifier Nikhil Aggarwal *}
                {if $ps_version_store >= 8}
                    <td class="left" style="background-color:white;">{Tools::displayDate($ticket->date_add, true)|escape:'html':'UTF-8'}</td>            
                {else}
                    <td class="left" style="background-color:white;">{Tools::displayDate($ticket->date_add, null, true)|escape:'html':'UTF-8'}</td>
                {/if}
            </tr>
            <tr>
                <td class="left grey-bg">{l s='Subject' mod='returnmanager'}</td>
                <td class="left" style="background-color:white;">{$ticket->subject|escape:'html':'UTF-8'}</td>
            </tr>
        </tbody>
    </table>

    {if count($ticket_threads) > 0}
        <h3>{l s='Ticket Threads' mod='returnmanager'}({count($ticket_threads)|intval})</h3>
        {foreach $ticket_threads as $thread}
            <table class="kbmpss-table-list {if $thread['reply_by'] == RmTicket::REPLY_BY_ADMIN}kbmpss-support-reply-box{else}kbmpss-customer-reply-box{/if}">
                <tbody>
                    <tr>
                        <td class="left" style="background-color: #F5F5F5;">
                            {* Changes to fix the issue of 500 error because of the different number of parameters in the function
                            Adding PS Version condition to call the Tools::displayDate()
                            NAFeb2024 displaydate
                            @date 09-02-2024
                            @modifier Nikhil Aggarwal *}
                            {if $ps_version_store >= 8}
                                <strong>{l s='Posted on' mod='returnmanager'}&nbsp;&nbsp;&nbsp;{Tools::displayDate($thread['date_add'], true)|escape:'html':'UTF-8'} {l s='by' mod='returnmanager'} {if $thread['reply_by'] == RmTicket::REPLY_BY_ADMIN}{l s='Admin' mod='returnmanager'}{else}{l s='You' mod='returnmanager'}{/if}</strong>
                            {else}
                                <strong>{l s='Posted on' mod='returnmanager'}&nbsp;&nbsp;&nbsp;{Tools::displayDate($thread['date_add'], null, true)|escape:'html':'UTF-8'} {l s='by' mod='returnmanager'} {if $thread['reply_by'] == RmTicket::REPLY_BY_ADMIN}{l s='Admin' mod='returnmanager'}{else}{l s='You' mod='returnmanager'}{/if}</strong>
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:white;" class="left">{nl2br($thread['message']) nofilter}</td>{*Variable contains css and html content, escape not required*}
                    </tr>
                </tbody>
            </table>
        {/foreach}
    {/if}
    {if $is_closed eq 0}
    <h3>{l s='Post Reply' mod='returnmanager'}</h3>

    <form id="kbmpss-post-reply-form" method="post" action="{$link->getModuleLink('returnmanager', 'customerticketview', ['id_rm_ticket' => $id_rm_ticket], (bool)Configuration::get('PS_SSL_ENABLED'))|escape:'html':'UTF-8'}">
        <input type="hidden" name="post_reply" value="1" />
        <table class="kbmpss-table-list" style="background-color: rgb(250, 250, 250);">
            <tbody>
                <tr>
                    <td class="left">
                        <textarea id="post_reply_message" name="post_reply_message" rows="10" style="width:99%;" placeholder="{l s='To best assist you, please be specific and detailed' mod='returnmanager'}"></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="left">
                        <button type="button" name="postReplySubmit" onclick="submitPostReply()">{l s='Post Reply' mod='returnmanager'}</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    {/if}
    <script type="text/javascript">
        function submitPostReply()
        {
            var message = $('#post_reply_message').val();
            if (message == '' || message.length < 30)
            {
                var error = true;
                alert("{l s='Minimum 30 characters required' mod='returnmanager'}")
            } else {
                $('#kbmpss-post-reply-form').submit();
            }
        }
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