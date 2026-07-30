/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

var validation_fields = {
        'isGenericName': /^[^<>={}]*$/,
        'isInt': /^[0-9]*$/,
        'isIntExcludeZero': /^[1-9]*$/
};

var mp_ajax_submit_url = '';

/**
 * Function to check the mandatory fields and direct the user to the next step
 * @param {type} form_id
 * @date 21-02-2023
 * @commenter Prvind Panday
 */
function quick_change_action(id)
{
    /**
     * If id is mp_quick_ticket_state then we need to check the previous status of the ticket and if the previous status is not selected then we need to show the alert message.
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    if(id == 'mp_quick_ticket_state') {
        var previous_status = velovalidation.checkMandatoryOnly($('#'+id));
        if (previous_status != true) {
        alert(previous_selected_status);
        return false;
        }
    }

    cfm = confirm(confirm_msg);
    if (cfm) {
        location.href = $('#'+id).find('option:selected').val();
    }
}

/**
 * Function to validate the new ticket form on submit at admin end.
 * @deprecated since version 1.0.0
 * @date 21-02-2023
 * @commenter Prvind Panday
 */
function validateKbNewTicketForm()
{
    $('.vel_error_msg').remove();
    var status = validateKbHelperForm('kb_mp_new_ticket_form');

    var title_empty = velovalidation.checkMandatoryOnly($("[name='new_ticket[title]']"));
    var new_ticket_title_html = velovalidation.checkHtmlTags($("[name='new_ticket[title]']"));
    if (title_empty != true) {
        $("[name='new_ticket[title]']").after($('<p class="title_empty vel_error_msg"></p>'));
        $('.title_empty').html(empty_msg);
        status = false;
    } else if (new_ticket_title_html != true) {
        $("[name='new_ticket[title]']").after($('<p class="new_ticket_title_html vel_error_msg"></p>'));
        $('.new_ticket_title_html').html(html_error_msg);
        status = false;
    }

    var summary_empty = velovalidation.checkMandatoryOnly($("[name='new_ticket[summary]']"));
    var new_ticket_message_html = velovalidation.checkHtmlTags($("[name='new_ticket[summary]']"));
    if (summary_empty != true) {
        $("[name='new_ticket[summary]']").closest(".form-group").addClass('has-error');
        $("[name='new_ticket[summary]']").after($('<p class="new_ticket_message_html vel_error_msg"></p>'));
        $('.new_ticket_message_html').html(empty_msg);
        status = false;
    } else if (new_ticket_message_html != true) {
        $("[name='new_ticket[summary]']").after($('<p class="new_ticket_message_html vel_error_msg"></p>'));
        $('.new_ticket_message_html').html(html_error_msg);
        status = false;
    }
    if(status){
        $('#kb_mp_new_ticket_form').submit();
    }else{
        $('#kb_mp_new_ticket_form .form-wrapper').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>' + info_alert + '</div>');
    }
}

/**
 * @deprecated since version 1.0.0
 * @param {*} value 
 * @param {*} element 
 * @returns boolean
 */
function kbValidateField(value, element)
{
    var value = $(element).val();

    for (var key in validation_fields)
    {
        if ($(element).hasClass(key))
        {
            var reg = new RegExp(validation_fields[key]);
            if(reg.test(value))
            {
                return true;
                break;
            }
        }
    }

    return false;
}

/**
 * Function to validate the admin reply form on submit at admin end.
 * @date 21-02-2023
 * @commenter Prvind Panday
 * @returns boolean
 */
function validateAdminReplyForm() {
    var error = true;
    $('.mp_reply_message_empty').remove();
    var mp_reply_message_empty = velovalidation.checkMandatoryOnly($("[name='mp_reply_message']"));
    var mp_reply_message_html = velovalidation.checkHtmlTags($("[name='mp_reply_message']"));

    /**
     * If the reply message is empty then we need to show the error message and if the reply message contains html tags then we need to show the error message.
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    if (mp_reply_message_empty != true) {
       // alert(mp_reply_message_empty);
        $("[name='mp_reply_message']").addClass('error_field');
        $("[name='mp_reply_message']").after($('<p class="mp_reply_message_empty vel_error_msg"></p>'));
        $('.mp_reply_message_empty').html(mp_reply_message_empty);
        error = false;
    } else if (mp_reply_message_html != true) {
       // alert(mp_reply_message_empty);
        $("[name='mp_reply_message']").addClass('error_field');
        $("[name='mp_reply_message']").after($('<p class="mp_reply_message_empty vel_error_msg"></p>'));
        $('.mp_reply_message_empty').html(mp_reply_message_html);
        error = false;
    }

    /**
     * If the error is false then we need to return false else we need to return true.
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    if(error != true) {
        return false;
    } else {
        return true;
    }

}