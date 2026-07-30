/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2016 Knowband
 * @license   see file: LICENSE.txt
 */

/**
 * This file is used to handle the ticket creation and ticket checking
 * functionality on the front end.
 * @date 21-02-2023
 * @commenter Prvind Panday
 */
$(document).ready(function(){

    /**
     * Below code is executed when the user clicks on the form input, select or textarea fields, it removes the error class and error message by default.
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    $('.kbmpss-form-field input, .kbmpss-form-field select, .kbmpss-form-field textarea').focusin(function(){
        $(this).closest('.kbmpss-form-group').removeClass('error');
        $(this).parent().find('.err-message').remove();
    });

});


/**
 * This function is used to validate the new ticket form.
 * @date 21-02-2023
 * @commenter Prvind Panday
 */
function validateNewIssue()
{
    var error = false;
    $('#kbmpss-new-ticket-form').find('.err-message').remove();
    $('#kbmpss-new-ticket-form').find('.kbmpss-form-group').removeClass('error');

    /**
     * Below code is used to validate the email address, first name, last name, phone number, subject and issue fields.
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    if ($('#kbmpss-new-ticket-email').val() == '')
    {
        var error = true;
        $('#kbmpss-new-ticket-email').closest('.kbmpss-form-group').addClass('error');
        $('#kbmpss-new-ticket-email').parent().append("<span class='err-message'>"+new_ticket_required_label+"</span>");
    } else
    {
        var email_reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (!email_reg.test($('#kbmpss-new-ticket-email').val()))
        {
            var error = true;
            $('#kbmpss-new-ticket-email').closest('.kbmpss-form-group').addClass('error');
            $('#kbmpss-new-ticket-email').parent().append("<span class='err-message'>"+new_ticket_invalid_value+"</span>");
        }
    }

    var name_reg = /^[^0-9!<>,;?=+()@#"°{}_$%:]*$/;
    if ($('#kbmpss-new-ticket-fname').val() == '')
    {
        var error = true;
        $('#kbmpss-new-ticket-fname').closest('.kbmpss-form-group').addClass('error');
        $('#kbmpss-new-ticket-fname').parent().append("<span class='err-message'>"+new_ticket_required_label+"</span>");
    } else
    {
        if (!name_reg.test($('#kbmpss-new-ticket-fname').val()))
        {
            var error = true;
            $('#kbmpss-new-ticket-fname').closest('.kbmpss-form-group').addClass('error');
            $('#kbmpss-new-ticket-fname').parent().append("<span class='err-message'>"+new_ticket_invalid_value+"</span>");
        }
    }

    if ($('#kbmpss-new-ticket-lname').val() != '' && !name_reg.test($('#kbmpss-new-ticket-lname').val()))
    {
        var error = true;
        $('#kbmpss-new-ticket-lname').closest('.kbmpss-form-group').addClass('error');
        $('#kbmpss-new-ticket-lname').parent().append("<span class='err-message'>"+new_ticket_invalid_value+"</span>");
    }

    if ($('#kbmpss-new-ticket-phone').val() != '')
    {
        var phone_reg = /^[+0-9. ()-]*$/;
        if (!phone_reg.test($('#kbmpss-new-ticket-phone').val()))
        {
            var error = true;
            $('#kbmpss-new-ticket-phone').closest('.kbmpss-form-group').addClass('error');
            $('#kbmpss-new-ticket-phone').parent().append("<span class='err-message'>"+new_ticket_invalid_phone+"</span>");
        }
    }

    if ($('#kbmpss-new-ticket-subject').val() == '')
    {
        var error = true;
        $('#kbmpss-new-ticket-subject').closest('.kbmpss-form-group').addClass('error');
        $('#kbmpss-new-ticket-subject').parent().append("<span class='err-message'>"+new_ticket_required_label+"</span>");
    }
    if ($('#kbmpss-new-ticket-issue').val() == '')
    {
        var error = true;
        $('#kbmpss-new-ticket-issue').closest('.kbmpss-form-group').addClass('error');
        $('#kbmpss-new-ticket-issue').parent().append("<span class='err-message'>"+new_ticket_mini_chars+"</span>");
    } else {
        var message = $('#kbmpss-new-ticket-issue').val();
        if (message == '' || message.length < 30)
        {
            var error = true;
            $('#kbmpss-new-ticket-issue').closest('.kbmpss-form-group').addClass('error');
            $('#kbmpss-new-ticket-issue').parent().append("<span class='err-message'>"+new_ticket_mini_chars+"</span>");
        }
    }

    /**
     * If there is no error then submit the form else show the error message.
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    if (!error)
    {
        $('#kbmpss-new-ticket-form').submit();
    }
   
}


/**
 * This function is used to validate the check ticket form.
 * @date 21-02-2023
 * @commenter Prvind Panday
 */
function validateCheckTicket()
{
    var error = false;
    $('#kbmpss-check-ticket-form').find('.err-message').remove();
    $('#kbmpss-check-ticket-form').find('.kbmpss-form-group').removeClass('error');

    /**
     * Check if the ticket number and email is empty or not. If not empty then check the email is valid or not.
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    if ($('#kbmpss-check-ticket-number').val() == '')
    {
        var error = true;
        $('#kbmpss-check-ticket-number').closest('.kbmpss-form-group').addClass('error');
        $('#kbmpss-check-ticket-number').parent().append("<span class='err-message'>"+kbmpss_new_ticket_required_label+"</span>");
    }

    if ($('#kbmpss-check-ticket-email').length > 0) {
        if ($('#kbmpss-check-ticket-email').val() == '')
        {
            var error = true;
            $('#kbmpss-check-ticket-email').closest('.kbmpss-form-group').addClass('error');
            $('#kbmpss-check-ticket-email').parent().append("<span class='err-message'>"+kbmpss_new_ticket_required_label+"</span>");
        } else
        {
            var email_reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if (!email_reg.test($('#kbmpss-check-ticket-email').val()))
            {
                var error = true;
                $('#kbmpss-check-ticket-email').closest('.kbmpss-form-group').addClass('error');
                $('#kbmpss-check-ticket-email').parent().append("<span class='err-message'>"+kbmpss_new_ticket_invalid_email+"</span>");
            }
        }
    }

    /**
     * If there is no error then submit the form else show the error message.
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    if (!error)
    {
        $('#kbmpss-check-ticket-form').submit();
    }
}

/**
 * Function to get the ajax data for my tickets based on the filter.
 * @param {*} kb_table_id 
 * @param {*} page_number 
 * @date 21-02-2023
 * @commenter Prvind Panday
 */
function getAjaxMyTickets(kb_table_id, page_number){

    /**
     * Get the filter parameters. serializeObjectToSerialize is a function to convert the object to serialize string.
     * If the request return the status true then show the data in the table.
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    var request_params = serializeObjectToSerialize(filter_paramters);
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getAjaxMyTickets'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}