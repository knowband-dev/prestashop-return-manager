/**
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
 */

var is_update_inventory = 0;
var is_generate_coupon = 0;
function submitform(val) {
    var ex_cat = $.trim($("#ex_category").val());
    var ex_prod = $.trim($("#ex_product").val());

    $('.errorsmall').remove();
    var text_data_html = tinyMCE.get('rm_credit_post_message').getContent();
    var email_content = $(text_data_html).text();
    var error_check = 0;
    if (email_content.trim() == '') {
        $('#mce_32').parent().append('<span class="errorsmall">' + credit_return_error + '</span>');
        error_check = 1;
    }
    // changes by rishabh jain for order status functioanlity
    if ($('input[name="velsof_return[enable_order_status_selection]"]').is(':checked') == true) {
        if ($('#kb_order_statuses').val() == null) {
            $('#kb_order_statuses').parent().append('<span class="errorsmall">' + order_status_error + '</span>');
            error_check = 1;
        }
    }
    // changes over
    // Changes by kanishka kannoujia on 16-Jume-2022 to change getContent('') from getContent()
    var text_data_html = tinyMCE.get('rm_refund_post_message').getContent();
    var email_content = $(text_data_html).text();
    if (email_content.trim() == '') {
        $('#mce_71').parent().append('<span class="errorsmall">' + refund_return_error + '</span>');
        error_check = 1;
    }
    // Changes by kanishka kannoujia on 16-Jume-2022 to change getContent('') from getContent()
    var text_data_html = tinyMCE.get('rm_replacement_post_message').getContent();
    var email_content = $(text_data_html).text();
    if (email_content.trim() == '') {
        $('#mce_110').parent().append('<span class="errorsmall">' + replacement_return_error + '</span>');
        error_check = 1;
    }
    // Changes by kanishka kannoujia on 16-Jume-2022 to change getContent('') from getContent()
    var text_data_html = tinyMCE.get('rm_return_slip_address').getContent();
    var email_content = $(text_data_html).text();
    if (email_content.trim() == '') {
        $('#mce_149').parent().append('<span class="errorsmall">' + return_address_error + '</span>');
        error_check = 1;
    }
    // Changes by kanishka kannoujia on 16-Jume-2022 to change getContent('') from getContent()
    var text_data_html = tinyMCE.get('rm_return_slip_guidelines').getContent();
    var email_content = $(text_data_html).text();
    if (email_content.trim() == '') {
        $('#mce_188').parent().append('<span class="errorsmall">' + return_guidelines_error + '</span>');
        error_check = 1;
    }

    var except_product_id = $("input[name='velsof_return[policy][ex_product]']").val();
    var except_category_id = $("input[name='velsof_return[policy][ex_category]']").val();

    if (!validateExceptionalId(except_product_id) && $.trim(except_product_id) != '') {
        $("input[name='velsof_return[policy][ex_product]']").parent().append('<span class="errorsmall">' + exceptional_error_txt + '</span>');
        error_check = 1;
    }

    if (!validateExceptionalId(except_category_id) && $.trim(except_category_id) != '') {
        $("input[name='velsof_return[policy][ex_category]']").parent().append('<span class="errorsmall">' + exceptional_error_txt + '</span>');
        error_check = 1;
    }

    if (error_check == 1) {
        return false;
    }

    if (ex_cat == '' && ex_prod == '') {
        $("#submit_form").val(val);
        document.returnmanager_form.submit();
    } else if (ex_cat != '' && ex_prod != '') {
        if (!validateExceptionalId(ex_cat) || !validateExceptionalId(ex_prod)) {
            $('.error-triangle').show();
            $("#save-policy-warning").fadeOut('fast').fadeIn('slow').fadeOut('fast').fadeIn('slow');
            return false;
        } else {
            $("#submit_form").val(val);
            document.returnmanager_form.submit();
        }
    } else if (ex_cat != '') {
        if (!validateExceptionalId(ex_cat)) {
            $('.error-triangle').show();
            $("#save-policy-warning").fadeOut('fast').fadeIn('slow').fadeOut('fast').fadeIn('slow');
            return false;
        } else {
            $("#submit_form").val(val);
            document.returnmanager_form.submit();
        }
    } else if (ex_prod != '') {
        if (!validateExceptionalId(ex_prod)) {
            $('.error-triangle').show();
            $("#save-policy-warning").fadeOut('fast').fadeIn('slow').fadeOut('fast').fadeIn('slow');
            return false;
        } else {
            $("#submit_form").val(val);
            document.returnmanager_form.submit();
        }
    }

}

$(document).ready(function () {

    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
    $('.already_mapped').click(function () {
        alert(category_already_mapped_err);
        return false;

    });
    $("#rm_from_date, #rm_to_date").datepicker({ showOtherMonths: true, dateFormat: "mm/dd/yy" });
    $("#c_categories").multipleSelect({
        placeholder: kb_select_category,
        filter: true,
        selectAll: true,
        selectAllText: kb_select_all,
        allSelected: kb_select_all,
        onCheckAll: function () {
            selectAllCategories();
        },
        onUncheckAll: function () {
            getCategoryProduct();
        },
        onClick: function () {
            getCategoryProduct();
        }
    });
    $("#kb_order_statuses").multipleSelect({
        placeholder: kb_select_order_status,
        filter: true,
        selectAll: true,
        selectAllText: kb_select_all,
        allSelected: kb_select_all,
    });
    if ($('input[name="velsof_return[enable_order_status_selection]"]').is(':checked') == true) {
        $('#kb_order_statuses').parent().parent().show();
    } else {
        $('#kb_order_statuses').parent().parent().hide();
    }
    $('input[name="velsof_return[enable_order_status_selection]"]').on('change', function (e) {
        if ($(this).is(':checked') == true) {
            $('#kb_order_statuses').parent().parent().show();
        } else {
            $('#kb_order_statuses').parent().parent().hide();
        }
    });
    /* Start Code Added By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
    if ($('input[name="velsof_return[enable_order_status_selection_return_policy]"]').is(':checked') == true) {
        $('#kb_policy_statuses').parent().parent().parent().show();
    } else {
        $('#kb_policy_statuses').parent().parent().parent().hide();
    }
    $('input[name="velsof_return[enable_order_status_selection_return_policy]"]').on('change', function (e) {
        if ($(this).is(':checked') == true) {
            $('#kb_policy_statuses').parent().parent().parent().show();
        } else {
            $('#kb_policy_statuses').parent().parent().parent().hide();
        }
    });
    /* End Code Added By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
    //Save Policy
    $('#save_policy').on('click', function () {
        $('#save_policy').prop("disabled", true);
        savePolicy();
    });
    //Save Reason
    $('#save_reason').on('click', function () {
        $('#save_reason').prop("disabled", true);
        saveReason();
    });
    //Save Status
    $('#save_status').on('click', function () {
        $('#save_status').prop("disabled", true);
        saveStatus();
    });
    //changes by vishal for adding cancel functionality
    //Save Cancel
    $('#save_cancel').on('click', function () {
        $('#save_cancel').prop("disabled", true);
        saveCancel();
    });
    //changes end
    /* Changes added by rishabh jain on 11 th july to save new address form data */
    $('#save_address').on('click', function () {
        $('#manual-address-form .error').remove();
        var error = false;
        // title
        var empty_title = velovalidation.checkMandatoryOnly($("input[name='address_new_title']"));
        if (empty_title != true) {
            error = true;
            $("input[name='address_new_title']").parent().append('<span class="error">' + empty_title + '</span>');
        }
        // address line 1
        var empty_line1 = velovalidation.checkMandatoryOnly($("input[name='address_new_line1']"));
        var invalid_line1 = velovalidation.checkAddress($("input[name='address_new_line1']"));

        if (empty_line1 != true) {
            error = true;
            $("input[name='address_new_line1']").parent().append('<span class="error">' + empty_line1 + '</span>');
        } else if (invalid_line1 != true) {
            error = true;
            $("input[name='address_new_line1']").parent().append('<span class="error">' + invalid_line1 + '</span>');
        }
        //address line 2
        var empty_line2 = velovalidation.checkMandatoryOnly($("input[name='address_new_line2']"));
        var invalid_line2 = velovalidation.checkAddress($("input[name='address_new_line2']"));
        if (empty_line2 == true) {
            if (invalid_line2 != true) {
                error = true;
                $("input[name='address_new_line2']").parent().append('<span class="error">' + invalid_line2 + '</span>');
            }
        }

        // zipcode
        var empty_zipcode = velovalidation.checkMandatoryOnly($("input[name='address_new_zipcode']"));
        var valid_zipcode = velovalidation.checkZip($("input[name='address_new_zipcode']"));
        if (empty_zipcode != true) {
            error = true;
            $("input[name='address_new_zipcode']").parent().append('<span class="error">' + empty_zipcode + '</span>');
        } else if (valid_zipcode != true) {
            error = true;
            $("input[name='address_new_zipcode']").parent().append('<span class="error">' + valid_zipcode + '</span>');
        }
        // city
        var empty_city = velovalidation.checkMandatoryOnly($("input[name='address_new_city']"));
        var invalid_city = velovalidation.checkCity($("input[name='address_new_city']"));
        if (empty_city != true) {
            error = true;
            $("input[name='address_new_city']").parent().append('<span class="error">' + empty_city + '</span>');
        } else if (invalid_city != true) {
            error = true;
            $("input[name='address_new_city']").parent().append('<span class="error">' + invalid_city + '</span>');
        }
        if (error == false) {
            saveAddress();
        }
    });


    /* changes end */
    $('#address_new_country').on('change', function () {
        //$('input[name="address_new_state"]').attr('value', '');
        $("#address_new_state").val('');
        getStateList();
    });
    if ($('#address_new_country').val() == '') {
        $('#address_new_state').hide();
        $('#address_new_state_label').hide();
    }
    resetArchives(0);
    //    $('.exception_id').on('blur', function(){
    //       if (!validateExceptionalId($(this).val()) && $.trim($(this).val()) != '')
    //           $(this).parent().append('<span class="errorsmall">'+exceptional_error_txt+'</span>');
    //    });
    //    $('.exception_id').on('focus', function(){
    //           $(this).parent().find('span.errorsmall').remove();
    //    });

    //changes by vishal for adding cancel order functionality
    $("#kb_cancel_statuses").multipleSelect({
        placeholder: kb_select_order_status,
        filter: true,
        selectAll: true,
        selectAllText: kb_select_all,
        allSelected: kb_select_all,
    });
    if ($('input[name="velsof_return[enable_cancel]"]').is(':checked') == true) {
        $('#kb_cancel_statuses').parent().parent().show();
    } else {
        $('#kb_cancel_statuses').parent().parent().hide();
    }
    $('input[name="velsof_return[enable_cancel]"]').on('change', function (e) {
        if ($(this).is(':checked') == true) {
            $('#kb_cancel_statuses').parent().parent().show();
        } else {
            $('#kb_cancel_statuses').parent().parent().hide();
        }
    });
    //changes end

    /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to send Test Email */
    $("#test_email").on('click', function () {
        $('.errorsmall').remove();
        $('.success_field').remove();
        var error = false;
        var email = $("[name^='rm_email[test_email]']").val();
        var check_email_mand = velovalidation.checkMandatory($("[name^='rm_email[test_email]']"));
        var check_email = velovalidation.checkEmail($("[name^='rm_email[test_email]']"));
        if (check_email_mand !== true) {
            error = true;
            $("[name^='rm_email[test_email]']").after($('<p class="check_email_mand errorsmall"></p>'));
            $('.check_email_mand').html(check_email_mand);
        }
        else if (check_email !== true) {
            error = true;
            $("[name^='rm_email[test_email]']").after($('<p class="check_email errorsmall"></p>'));
            $('.check_email').html(check_email);
        }
        if (error === false) {
            var subject_test_email = $('#velsof_template_subject').val();
            var body_test_email = tinyMCE.get('velsof_template_content').getContent();

            var body_email = body_test_email.replace(/&amp;/g, '#####@@@@@@');
            body_email = body_email.replace(/&;/g, '#####@@@@@@');
            body_email = body_email.replace(/&/g, '@@@@@@@@@@@@');
            $.ajax({
                url: module_path,
                type: 'post',
                data: { "ajax": "true", "method": "sendTestMail", "email": email, "subject_test_email": subject_test_email, "body_test_email": body_email },
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader').show();
                },
                success: function (json) {
                    if (json['mail_sent']) {
                        $("[name^='rm_email[test_email]']").after($('<p class="email_sent success_field"></p>'));
                        $("[name^='rm_email[test_email]']").val('');
                        $('.email_sent').html(email_sent);
                    } else {
                        $("[name^='rm_email[test_email]']").after($('<p class="email_sent_error errorsmall"></p>'));
                        $('.email_sent_error').html(json['mail_sent']);
                    }
                },
                complete: function () {
                    $('#show_loader').hide();
                }
            });
        }
    });
    /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to send Test Email */
});

//changes by vishal on 18 aug 2020 for handling return functionality for admin
function getCancelForm(id_info, e) {
    var test123 = id_info.split("_");
    var div_id = test123[0];
    setCookie("current_tab", div_id, 1);
    $.ajax({
        url: module_link,
        type: 'post',
        data: 'ajax=true&method=getRequestCancelForm&id_info=' + id_info,
        dataType: 'json',
        beforeSend: function () {
            $('#kb_rm_pop_up').html('');
            $(e).parent().append('<img src="' + path + 'returnmanager/views/img/loader_small.gif" />');
        },
        complete: function () {
            $(e).parent().find('img').remove();
        },
        success: function (response) {
            if (response['detail_found']) {
                $('#kb_rm_pop_up').html(response['template']);
                $('#kb_rm_pop_up #rm_fade').show();
                $('#kb_rm_pop_up #rm_return_form_popup').show();
                $('#kb_rm_popup_address').html($('#default_addr').val());
            } else {
                alert(orderedProductNotFound);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(rm_ajax_failed + ': ' + textStatus);
        }
    });
}

function kbrmgetReturnForm(e) {
    var kb_order_ids = [];
    $(e).closest('.rm_single_order_row').find('input:checkbox').each(function () {
        if ($(this).prop("checked") && $(this).val() != 'on') {
            kb_order_ids.push($(this).val());
        }
    });
    if (kb_order_ids.length == 0) {
        alert(select_product_error);
        return false;
    }
    $.ajax({
        url: module_link,
        type: 'post',
        data: 'ajax=true&method=kbgetRequestForm&id_info=' + kb_order_ids,
        dataType: 'json',
        beforeSend: function () {
            $('#kb_rm_pop_up').html('');
            $(e).parent().append('<img src="' + path + 'returnmanager/views/img/loader_small.gif" />');
        },
        complete: function () {
            $(e).parent().find('img').remove();
        },
        success: function (response) {
            if (response['detail_found']) {
                $('#kb_rm_pop_up').html(response['template']);
                $('#kb_rm_pop_up #rm_fade').show();
                $('#kb_rm_pop_up #rm_return_form_popup').show();
                $('#rm_popup_address').html($('#default_addr').val());
                $('input[name="kb_Product_specific_products"]').autocomplete(path_fold, {
                    delay: 10,
                    minChars: 3,
                    autoFill: true,
                    max: 20,
                    matchContains: true,
                    mustMatch: true,
                    scroll: false,
                    cacheLength: 0,
                    // param multipleSeparator:'||' ajoutÃ© Ã  cause de bug dans lib autocomplete
                    multipleSeparator: '||',
                    formatItem: function (item) {
                        return item[1] + ' - ' + item[0];
                    },
                    extraParams: {
                        productIds: function () {
                            var selected_pro = $('input[name="kb_Product_specific_product_items').val();
                            if (typeof selected_pro != 'undefined') {
                                return selected_pro.replace(/\-/g, ',');
                            }
                        },
                        excludeVirtuals: 0,
                        exclude_packs: 0
                    }
                }).result(function (event, item, formatted) {
                    addProductToMultipleMappedproductpage(item, $(this));
                    event.stopPropagation();
                });
            } else {
                alert(orderedProductNotFound);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(rm_ajax_failed + ': ' + textStatus);
        }
    });
}

function addProductToMultipleMappedproductpage(data, e) {
    console.log(e);
    if (data == null)
        return false;

    var productId = data[1];
    $(e).siblings('input[name^="rm_return_product_"]').val(productId);
    displaymultipleKbProductAttribute($(e).siblings('input[name^="rm_return_product_"]'));
}

function addProductToMappedproductpage(data) {
    console.log(data);
    if (data == null)
        return false;

    var productId = data[1];
    $('input[name="rm_return_product"]').val(productId);
    displayKbProductAttribute(productId);
}

function displayReturnNoteOnMultipleForm(e, product_id) {
    $(e).parent().find('span.rm_error').remove();
    var val = $(e).val();
    if (val == 'replacement') {
        $('#kb_product_choose_block_' + product_id).show();
    } else {
        $('#kb_product_choose_block_' + product_id).hide();
    }
    $('#rm_return_type_note_' + product_id + ' p').hide();
    if (val != 0) {
        $('#rm_return_type_note_' + product_id + ' p#rm_return_type_note_' + val).show();
    }
    setLeftColHeight('rm_popup_request_form');
}

function displaymultipleKbProductAttribute(e) {
    var kb_product_id = $(e).attr('kb_value');
    var val = $(e).val();
    console.log(val);
    if (val == 0) {
        $('#kb_product_attribute_choose_block_' + kb_product_id).hide();
    } else {
        $('#rm_kbproduct_loader').show();
        $.ajax({
            url: module_link,
            type: 'post',
            data: 'ajax=true&method=kbgetProductAttribute&rm_return_product=' + val,
            dataType: 'json',
            beforeSend: function () {
            },
            complete: function () {
            },
            success: function (json) {
                var count = Object.keys(json).length;
                var select = $("<select class='chosen rm_form_control'></select>").attr("id", "rm_return_product_attribute_id_" + kb_product_id).attr("name", "rm_return_product_attribute_id_" + kb_product_id);
                $.each(json, function (index, json) {
                    select.append($("<option></option>").attr("value", json.product_attribute_id).text(json.product_attribute_name));
                });
                $("#rm_return_product_attribute_" + kb_product_id).html(select);
                if (count != 0) {
                    $('#kb_product_attribute_choose_block_' + kb_product_id).show();
                } else {
                    $('#kb_product_attribute_choose_block_' + kb_product_id).hide();
                }
                $('#rm_kbproduct_loader').hide();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
            }
        });
    }
}

function rmSubmitReturnMultipleRequest(e) {
    var error = false;
    $('#rm_popup_request_form span.rm_error').remove();
    $('#rm_popup_request_form select[name*="rm_return_type_"]').each(function () {
        if ($(this).length && $(this).val() == 0) {
            error = true;
            $(this).parent().append('<span class="rm_error">' + rm_return_type_required + '</span>');
        }
    });

    $('#rm_popup_request_form select[name*="rm_return_reason_"]').each(function () {
        if ($(this).length && $(this).val() == 0) {
            error = true;
            $(this).parent().append('<span class="rm_error">' + rm_reason_required + '</span>');
        }
    });

    //    $('#rm_popup_request_form select[name*="rm_return_type_"]').each(function(){
    //        if ($(this).length && $(this).val() == 0){
    //        error = true;
    //        $(this).parent().append('<span class="rm_error">' + rm_product_required + '</span>');
    //        }
    //    });

    //    if (!error) {
    $('#rm_popup_request_form input[name*="rm_agree_toc_"]').each(function () {
        if ($(this).length && !$(this).is(':checked')) {
            error = true;
            var terms_error = rm_toc_checked;
            terms_error = terms_error.replace('&amp;', '&');
            $(this).parent().append('<span class="rm_error">' + terms_error + '</span>');
        }
    });
    //    }

    if (!error) {
        var myFormData = new FormData();
        // changes done by Kanishka Kannoujia on 18-06-2022 to add upload image functionality in case of complete order return
        if ($('input:file[name*="rm_return_image_"]').length > 0) {
            i = 0;
            $('input:file[name*="rm_return_image_"]').each(function (index) {
                name = $(this).attr('name');
                myFormData.append(name, $('input:file[name*="' + name + '"]')[0].files[0]);
            });
        }
        // changes done by Kanishka Kannoujia on 18-06-2022 to add upload image functionality in case of complete order return
        myFormData.append('ajax', 'true');
        myFormData.append('method', 'submitReturnMultipleRequest');
        var other_data = $('#rm_return_form_popup input, #rm_return_form_popup select, #rm_return_form_popup textarea').serializeArray();
        $.each(other_data, function (key, input) {
            myFormData.append(input.name, input.value);
        });
        $.ajax({
            url: module_link,
            type: 'post',
            processData: false,
            contentType: false,
            data: myFormData,
            dataType: 'json',
            beforeSend: function () {
                $(e).attr('disabled', true);
                $(e).parent().append('<img src="' + path + 'returnmanager/views/img/loader_small.gif" />');
            },
            complete: function () {
                $(e).parent().find('img').remove();
                $(e).attr('disabled', false);
            },
            success: function (response) {
                //changes by vishal for add error message for custom fields on 26 august 2020
                if (response.hasOwnProperty('custom_fields_errors')) {
                    $(".errorsmall_custom").hide();
                    $.each(response.custom_fields_errors.error, function (key, data) {
                        $("#error_" + key).html(data);
                        $("#error_" + key).show();
                        $("#error_" + key).parent().parent().css("border-color", "#FF0000");
                    });
                } else {
                    $('#kb_rm_pop_up').html(response['template']);
                    $('#kb_rm_pop_up #rm_fade').show();
                    setLeftColHeight('rm_popup_success_form');
                }
                //changes end       
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(rm_ajax_failed + ': ' + textStatus);
            }
        });
    }
    return false;
}

// function kbrmgetReturnForm(e) {
//     var kb_order_ids = [];
//     $(e).closest('.rm_single_order_row').find('input:checkbox').each(function () {
//         if ($(this).prop("checked") && $(this).val() != 'on') {
//             kb_order_ids.push($(this).val());
//         }
//     });
//     if (kb_order_ids.length == 0) {
//         alert(select_product_error);
//         return false;
//     }
//     $.ajax({
//         url: module_link,
//         type: 'post',
//         data: 'ajax=true&method=kbgetRequestForm&id_info=' + kb_order_ids,
//         dataType: 'json',
//         beforeSend: function () {
//             $('#kb_rm_pop_up').html('');
//             $(e).parent().append('<img src="' + path + 'returnmanager/views/img/loader_small.gif" />');
//         },
//         complete: function () {
//             $(e).parent().find('img').remove();
//         },
//         success: function (response) {
//             if (response['detail_found']) {
//                 $('#kb_rm_pop_up').html(response['template']);
//                 $('#kb_rm_pop_up #rm_fade').show();
//                 $('#kb_rm_pop_up #rm_return_form_popup').show();
//                 $('#rm_popup_address').html($('#default_addr').val());
//             } else {
//                 alert(orderedProductNotFound);
//             }
//         },
//         error: function (XMLHttpRequest, textStatus, errorThrown) {
//             alert(rm_ajax_failed + ': ' + textStatus);
//         }
//     });
// }

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function rmSubmitCancelRequest(e) {
    var error = false;
    $('#rm_popup_request_form span.rm_error').remove();
    if ($('#rm_popup_request_form select[name="rm_return_type"]').length && $('#rm_popup_request_form select[name="rm_return_type"]').val() == -1) {
        error = true;
        $('#rm_popup_request_form select[name="rm_return_type"]').parent().append('<span class="rm_error">' + rm_return_reason_required + '</span>');
    }

    if (!error) {
        if ($('#rm_popup_request_form input[name="rm_agree_toc"]').length && !$('#rm_popup_request_form input[name="rm_agree_toc"]').is(':checked')) {
            error = true;
            var terms_error = rm_toc_checked;
            terms_error = terms_error.replace('&amp;', '&');
            $('#rm_popup_request_form').find('.rm_left').append('<span class="rm_error">' + terms_error + '</span>');
            //alert(terms_error);
        }
    }
    if ($('#rm_cancel_reason').val() == 0) {
        if ($('#rm_reason').val() == "") {
            error = true;
            $('#rm_reason').parent().append('<span class="rm_error">' + rm_return_reason_blank + '</span>');
        }
    }

    if (!error) {
        //var data = $('#rm_popup_request_form input[name="rm_return_image"]')[0].files[0];
        var myFormData = new FormData();
        if ($('input[type=file]').length > 0) {
            myFormData.append('image', $('input[type=file]')[0].files[0]);
        }
        myFormData.append('ajax', 'true');
        myFormData.append('method', 'submitCancelRequest');
        var other_data = $('#rm_return_form_popup input, #rm_return_form_popup select, #rm_return_form_popup textarea').serializeArray();
        $.each(other_data, function (key, input) {
            myFormData.append(input.name, input.value);
        });
        $.ajax({
            url: module_link,
            type: 'post',
            processData: false, // important
            contentType: false, // important
            //            data: 'ajax=true&method=submitReturnRequest&'+$('#rm_return_form_popup input, #rm_return_form_popup select, #rm_return_form_popup textarea').serialize(),
            data: myFormData,
            dataType: 'json',
            beforeSend: function () {
                $(e).attr('disabled', true);
                $(e).parent().append('<img src="' + path + 'returnmanager/views/img/loader_small.gif" />');
            },
            complete: function () {
                $(e).parent().find('img').remove();
                $(e).attr('disabled', false);
            },
            success: function (response) {
                console.log(response);
                if (response.hasOwnProperty('custom_fields_errors')) {
                    $(".errorsmall_custom").hide();
                    $.each(response.custom_fields_errors.error, function (key, data) {
                        $("#error_" + key).html(data);
                        $("#error_" + key).show();
                        $("#error_" + key).parent().parent().css("border-color", "#FF0000");
                    });
                } else {
                    $('#kb_rm_pop_up').html(response['template']);
                    $('#kb_rm_pop_up #rm_fade').show();
                    setLeftColHeight('rm_popup_success_form');
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(rm_ajax_failed + ': ' + textStatus);
            }
        });
    }
    return false;
}
//changes end

function getStateList() {
    var country_id = $('#address_new_country').val();
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=getStateList'
            + '&country_id=' + country_id
        ,
        success: function (response) {
            if (response == '') {
                $('#address_new_state').hide();
                $('#address_new_state_label').hide();
            } else {
                $('#address_new_state').show();
                $('#address_new_state_label').show();
                $('#address_new_state').html(response);
            }
        }
    });

}
function checkOtherReturnOptions(e) {
    if ($('.vss_return_options:checked').length < 1) {
        alert(atleast_one_text);
        $(e).prop('checked', true);
    }
}

function getMessagesData(elem) {
    $.ajax({
        type: "POST",
        url: module_path,
        data: 'ajax=true&method=getMessagesData&selected_lang=' + $(elem).val(),
        dataType: 'json',
        beforeSend: function () {
            $('#rm_messages_loader').show();
        },
        success: function (json) {
            $('#rm_messages_loader').hide();
            tinyMCE.get('rm_credit_post_message').setContent(json['credit']);
            tinyMCE.get('rm_refund_post_message').setContent(json['refund']);
            tinyMCE.get('rm_replacement_post_message').setContent(json['replace']);
            tinyMCE.get('rm_cancel_post_message').setContent(json['cancel']);
        }
    });
}

function getReturnSlipData(elem) {
    $.ajax({
        type: "POST",
        url: module_path,
        data: 'ajax=true&method=getReturnSlipData&selected_lang=' + $(elem).val(),
        dataType: 'json',
        beforeSend: function () {
            $('#rm_return_slip_loader').show();
        },
        success: function (json) {
            $('#rm_return_slip_loader').hide();
            tinyMCE.get('rm_return_slip_address').setContent(json['address']);
            tinyMCE.get('rm_return_slip_guidelines').setContent(json['guidelines']);
        }
    });
}

function getNextReturnsListingPage(page, active) {
    /* Start Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
    var list_type = '';
    var return_id = '';
    var customer_name = '';
    var customer_email = '';
    var product_name = '';
    var order_id = '';
    var status_id = '';
    var order_by = '';
    var order_dir = '';

    if (active == 5) {
        list_type = 'rm_cancel_returns_list_holder';
        return_id = $("#rm_cancelled_custom_return_id").val();
        customer_name = $("#rm_cancelled_customer_name").val();
        product_name = $("#rm_cancelled_product_name").val();
        order_id = $("#rm_cancelled_order_id").val();
        order_by = $('select[name="rm_cancelled_sortby"]').val();
        order_dir = $('select[name="rm_cancelled_sortdir"]').val();
    } else if (active == 2) {
        list_type = 'rm_active_returns_list_holder';
        return_id = $("#rm_active_custom_return_id").val();
        customer_email = $("#rm_active_customer_email").val();
        product_name = $("#rm_active_product_name").val();
        status_id = $('select[name="rm_active_return_status"]').val();
        order_by = $('select[name="rm_active_sortby"]').val();
        order_dir = $('select[name="rm_active_sortdir"]').val();
    } else if (active == 4) {
        list_type = 'rm_archive_list';
        return_id = $("#rm_custom_return_id").val();
        customer_name = $("#rm_customer_name").val();
        product_name = $("#rm_product_name").val();
        order_id = $("#rm_order_id").val();
        status_id = $('select[name="rm_archive_return_status"]').val();
        order_by = $('select[name="rm_archive_sortby"]').val();
        order_dir = $('select[name="rm_archive_sortdir"]').val();
    } else {
        list_type = 'rm_pending_returns_list_holder';
        return_id = $("#rm_pending_custom_return_id").val();
        customer_name = $("#rm_pending_customer_name").val();
        product_name = $("#rm_pending_product_name").val();
        order_id = $("#rm_pending_order_id").val();
        order_by = $('select[name="rm_pending_sortby"]').val();
        order_dir = $('select[name="rm_pending_sortdir"]').val();
    }
    /* End Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
    $.ajax({
        url: module_path,
        /**
         * Start Changes to fix the issue of email search when any special character is used in the email search field
         * Added the encodeURIComponent() for the email so that the special characters can be handled
         * NAFeb2024 encodeURIComponent
         * @date 09-02-2024
         * @modifier Nikhil Aggarwal
         */
        data: '&ajax=true&method=getNextReturnsListingPage&inc_page_number=' + page + '&active_status=' + active + '&return_id=' + return_id + '&customer_name=' + customer_name + '&customer_email=' + encodeURIComponent(customer_email) + '&product_name=' + product_name + '&order_id=' + order_id + '&status_id=' + status_id + '&order_by=' + order_by + '&order_dir=' + order_dir,
        // Changes end by Nikhil
        type: 'post',
        datatype: 'json',
        beforeSend: function () {
            $('#' + list_type + ' .rm-bigloader').show();
        },
        success: function (json) {
            $('#' + list_type + ' .rm-bigloader').hide();
            var html = '';
            var i = 0;
            var row_class = '';
            if (active == 2) {
                if (json['flag']) {
                    for (i = 0; i < json['data'].length; i++) {
                        if (i % 2 == 0)
                            row_class = 'even';
                        else
                            row_class = 'odd';

                        var product_attr = '';
                        if (typeof json['data'][i]['product_attr'] != 'undefined')
                            product_attr = json['data'][i]['product_attr'];
                        else
                            product_attr = '<br>';
                        if (isEmpty(product_attr)) {
                            product_attr = '<br>';
                        }

                        if (json['data'][i]['comment'] != '')
                            var return_comment = json['data'][i]['comment'];
                        else
                            var return_comment = no_comments_text;
                        //					var return_comment = "<span class='vss_italic_text'>"+no_comments_text+"</span>";
                        if (json['data'][i]['replacedwith_product_link'] != undefined) {
                            var requested_product_html = '<b><a href="' + json['data'][i]['replacedwith_product_link'] + '" target="_blank">' + json['data'][i]['replacedwith_product_name'] + '</a></b>';
                        } else {
                            var requested_product_html = not_applicable_msg;
                        }
                        html += '<tr id="rm_pending_returns_' + json['data'][i]['return_id'] + '" class="rm_pending_returns pure-table-' + row_class + '">';
                        // changes by rishabh jain to add return id column
                        html += '<td>' + json['data'][i]['return_id'] + '</td>';
                        // changes over
                        html += '<td><a href="' + customer_controller + '&id_customer=' + json['data'][i]['customer_id'] + '&viewcustomer" target="_blank">' + json['data'][i]['cust_email'] + '</a></td>';
                        /* Start Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing */
                        html += '<td>' + json["data"][i]["address_title"] + '</td>';
                        /* End Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing */
                        html += '<td><b>' + json['data'][i]['product_name'] + '</b><br>' + product_attr + '</td>';
                        html += '<td>' + requested_product_html + '</td>';
                        html += '<td>' + json['data'][i]['quantity'] + '</td>';
                        html += '<td>' + json['data'][i]['request_date'] + '</td><td>' + json['data'][i]['return_type'] + '</td><td class="rm_pending_return_status_col">' + json['data'][i]['status'] + '</td>';
                        html += '<td class="rm_velsof_action">';
                        html += '<a type="' + json['data'][i]['return_id'] + '_' + json['data'][i]['id_lang'] + '" style="cursor: pointer;" onclick="denyRequest(this);" class="velsof-glyphicons glyphicons remove" title="' + rm_deny_return_text + '"><i></i></a>';
                        html += '<a type="' + json['data'][i]['return_id'] + '_' + json['data'][i]['id_lang'] + '" style="cursor: pointer;" onclick="changeReturnStatus(this);" class="velsof-glyphicons glyphicons edit" title="' + rm_change_status_text + '"><i></i></a>';
                        html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" onclick="viewReturnDetail(this)" class="velsof-glyphicons glyphicons history" title="' + rm_view_history_text + '"><i></i></a>';
                        html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="' + return_comment + '" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title="' + rm_comment_text + '"><i></i></a>';
                        if (json['data'][i]['is_refund_type'] == 1) {
                            html += '<a type="' + json['data'][i]['return_id'] + '_' + json['data'][i]['id_lang'] + '" refund="1" style="cursor: pointer;" style="cursor: pointer;"  onclick="completeReturn(this)" class="velsof-glyphicons glyphicons ok_2" title="' + rm_complete_return_text + '"><i></i></a>';
                        } else {
                            html += '<a type="' + json['data'][i]['return_id'] + '_' + json['data'][i]['id_lang'] + '" refund="0" style="cursor: pointer;" style="cursor: pointer;"  onclick="completeReturn(this)" class="velsof-glyphicons glyphicons ok_2" title="' + rm_complete_return_text + '"><i></i></a>';
                        }

                        if (json['data'][i]['image_path'] != '') {
                            html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" style="cursor: pointer;" href="' + json['data'][i]['image_path'] + '" target="_blank" onclick="" class="velsof-glyphicons glyphicons file" title="' + rm_view_image_text + '"><i></i></a>';
                        }

                        // changes by rishabh jain for internal note
                        html += '<a type="' + json['data'][i]['return_id'] + '_' + json['data'][i]['id_lang'] + '" style="cursor: pointer;" style="cursor: pointer;" onclick="viewInternalNotes(this)" class="velsof-glyphicons glyphicons comments" title="' + rm_view_internal_note_text + '"><i></i></a>';
                        // changes by rishabh jain for ticket link
                        if (json['data'][i]['is_ticket_exist'] != 0) {
                            html += '<a href="' + json['data'][i]['ticket_link'] + '" type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" style="cursor: pointer;" href="' + json['data'][i]['image_path'] + '" target="_blank"  class="velsof-glyphicons glyphicons book_open" title="' + rm_view_ticket_text + '"><i></i></a>';
                        }
                        html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;"  onclick="getReturnmanagerActiveCustomFeildDetail(' + json['data'][i]['return_id'] + ')" class="velsof-glyphicons glyphicons list" title="' + rm_custom_field_text + '"><i></i></a>'
                        // changes over
                        html += '<input type="hidden" id="rm_active_curr_status_' + json['data'][i]['return_id'] + '" value="' + json['data'][i]['status_id'] + '" /></td></tr>';
                    }
                } else {
                    html += '<tr><td colspan="8" rowspan="3"><div class="rm_no_data"><span>' + rm_no_active_text + '</span></div></td></tr>';
                }
                $('#rm_active_return_list').html(html);
                $('#rm_active_returns_current_page').attr('value', page);
                $('#rm_active_returns_list_holder .paginator-block').html(json['pagination']);
            }
            else if (active == 4 || active == 5) {
                if (json['flag']) {
                    for (i = 0; i < json['data'].length; i++) {
                        if (i % 2 == 0)
                            row_class = 'even';
                        else
                            row_class = 'odd';
                        var product_attr = '';
                        if (typeof json['data'][i]['product_attr'] != 'undefined')
                            product_attr = json['data'][i]['product_attr'];
                        else
                            product_attr = '<br>';
                        if (isEmpty(product_attr)) {
                            product_attr = '<br>';
                        }


                        if (json['data'][i]['comment'] != '')
                            var return_comment = json['data'][i]['comment'];
                        else
                            var return_comment = no_comments_text;
                        //					var return_comment = "<span class='vss_italic_text'>"+no_comments_text+"</span>";

                        if (json['data'][i]['whopayshipping'] == 'c')
                            var shipping_paid_by = rm_customer_text;
                        else
                            var shipping_paid_by = rm_so_text;
                        if (json['data'][i]['replacedwith_product_link'] != undefined) {
                            var requested_product_html = '<b><a href="' + json['data'][i]['replacedwith_product_link'] + '" target="_blank">' + json['data'][i]['replacedwith_product_name'] + '</a></b>';
                        } else {
                            var requested_product_html = not_applicable_msg;
                        }
                        html += '<tr class="pure-table-' + row_class + '">';
                        // changes by rishabh jain to add return id column
                        html += '<td>' + json['data'][i]['return_id'] + '</td>';
                        // changes over
                        html += '<td><a href="' + order_controller + '&id_order=' + json['data'][i]['order_id'] + '&vieworder" target="_blank">' + json['data'][i]['order_reference'] + '</a></td>';
                        html += '<td><a href="' + customer_controller + '&id_customer=' + json['data'][i]['customer_id'] + '&viewcustomer" target="_blank">' + json['data'][i]['cust_name'] + '</a></td>';
                        html += '<td><b><a href="' + json['data'][i]['product_link'] + '" target="_blank">' + json['data'][i]['product_name'] + '</a></b><br>' + product_attr + '</td>';
                        html += '<td>' + requested_product_html + '</td>';
                        html += '<td>' + json['data'][i]['unit_price_tax_incl'] + '</td><td>' + json['data'][i]['quantity'] + '</td>';
                        html += '<td>' + shipping_paid_by + '</td><td>' + json['data'][i]['return_type'] + '</td><td class="rm_velsof_action">';
                        html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="' + json['data'][i]['reason'] + '" class="velsof-glyphicons glyphicons circle_question_mark rm_customer_notes" title="' + rm_reason_text + '"><i></i></a>';
                        html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="' + return_comment + '" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title="' + rm_comment_text + '"><i></i></a>';
                        if (json['data'][i]['image_path'] != '') {
                            html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" style="cursor: pointer;" href="' + json['data'][i]['image_path'] + '" target="_blank" onclick="" class="velsof-glyphicons glyphicons file" title="' + rm_view_image_text + '"><i></i></a>';
                        }
                        // changes by rishabh jain for internal note
                        html += '<a type="' + json['data'][i]['return_id'] + '_' + json['data'][i]['id_lang'] + '" style="cursor: pointer;" style="cursor: pointer;" onclick="viewInternalNotes(this)" class="velsof-glyphicons glyphicons comments" title="' + rm_view_internal_note_text + '"><i></i></a>';
                        // changes by rishabh jain for ticket link
                        if (json['data'][i]['is_ticket_exist'] != 0) {
                            html += '<a href="' + json['data'][i]['ticket_link'] + '" type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" style="cursor: pointer;" href="' + json['data'][i]['image_path'] + '" target="_blank"  class="velsof-glyphicons glyphicons book_open" title="' + rm_view_ticket_text + '"><i></i></a>';
                        }
                        html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;"  onclick="getReturnmanagerCancelCustomFeildDetail(' + json['data'][i]['return_id'] + ')" class="velsof-glyphicons glyphicons list" title="' + rm_custom_field_text + '"><i></i></a>'
                        // changes over
                        html += '</td></tr>';
                    }
                } else {
                    html += '<tr><td colspan="9" rowspan="3"><div class="rm_no_data"><span>' + rm_no_data_label + '</span></div></td></tr>';
                }
                if (active == 4) {
                    $('#rm_archive_list_tbody').html(html);
                    $('#rm_archive_returns_current_page').attr('value', page);
                    $('#rm_list_container .paginator-block').html(json['pagination']);
                } else {
                    $('#rm_cancel_list_tbody').html(html);
                    $('#rm_cancel_returns_current_page').attr('value', page);
                    $('#rm_cancel_returns_list_holder .paginator-block').html(json['pagination']);
                }
                //                $('#rm_archive_list_tbody').html(html);
                //                $('#rm_archive_returns_current_page').attr('value', page);
                //                $('#rm_list_container .paginator-block').html(json['pagination']);
            }
            else {
                if (json['flag']) {
                    for (i = 0; i < json['data'].length; i++) {
                        if (i % 2 == 0)
                            row_class = 'even';
                        else
                            row_class = 'odd';
                        var product_attr = '';
                        if (typeof json['data'][i]['product_attr'] != 'undefined')
                            product_attr = json['data'][i]['product_attr'];
                        else
                            product_attr = '<br>';
                        if (isEmpty(product_attr)) {
                            product_attr = '<br>';
                        }

                        if (json['data'][i]['comment'] != '')
                            var return_comment = json['data'][i]['comment'];
                        else
                            var return_comment = no_comments_text;
                        //					var return_comment = "<span class='vss_italic_text'>"+no_comments_text+"</span>";

                        if (json['data'][i]['whopayshipping'] == 'c')
                            var shipping_paid_by = rm_customer_text;
                        else
                            var shipping_paid_by = rm_so_text;
                        if (json['data'][i]['replacedwith_product_link'] != undefined) {
                            var requested_product_html = '<b><a href="' + json['data'][i]['replacedwith_product_link'] + '" target="_blank">' + json['data'][i]['replacedwith_product_name'] + '</a></b>';
                        } else {
                            var requested_product_html = not_applicable_msg;
                        }
                        html += '<tr class="rm_pending_returns pure-table-' + row_class + '">';
                        // changes by rishabh jain to add return id column
                        html += '<td>' + json['data'][i]['return_id'] + '</td>';
                        // changes over
                        html += '<td><a href="' + order_controller + '&id_order=' + json['data'][i]['order_id'] + '&vieworder" target="_blank">' + json['data'][i]['order_reference'] + '</a></td>';
                        html += '<td><a href="' + customer_controller + '&id_customer=' + json['data'][i]['customer_id'] + '&viewcustomer" target="_blank">' + json['data'][i]['cust_name'] + '</a></td>';
                        /* Start Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing */
                        html += '<td>' + json["data"][i]["address_title"] + '</td>';
                        /* End Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing */
                        html += '<td><b><a href="' + json['data'][i]['product_link'] + '" target="_blank">' + json['data'][i]['product_name'] + '</a></b><br>' + product_attr + '</td>';
                        html += '<td>' + requested_product_html + '</td>';
                        html += '<td>' + json['data'][i]['unit_price_tax_incl'] + '</td><td>' + json['data'][i]['quantity'] + '</td>';
                        html += '<td>' + shipping_paid_by + '</td><td>' + json['data'][i]['return_type'] + '</td><td class="rm_velsof_action">';
                        html += '<a type="' + json['data'][i]['return_id'] + '_' + json['data'][i]['id_lang'] + '" style="cursor: pointer;" onclick="allowRequest(this);" class="velsof-glyphicons glyphicons ok" title="' + rm_allow_return_text + '"><i></i></a>';
                        html += '<a type="' + json['data'][i]['return_id'] + '_' + json['data'][i]['id_lang'] + '" style="cursor: pointer;" onclick="denyRequest(this);" class="velsof-glyphicons glyphicons remove" title="' + rm_deny_return_text + '"><i></i></a>';
                        html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="' + json['data'][i]['reason'] + '" class="velsof-glyphicons glyphicons circle_question_mark rm_customer_notes" title="' + rm_reason_text + '"><i></i></a>';
                        html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="' + return_comment + '" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title="' + rm_comment_text + '"><i></i></a>';
                        if (json['data'][i]['image_path'] != '') {
                            html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" style="cursor: pointer;" href="' + json['data'][i]['image_path'] + '" target="_blank" onclick="" class="velsof-glyphicons glyphicons file" title="' + rm_view_image_text + '"><i></i></a>';
                        }
                        // changes by rishabh jain for ticket link
                        if (json['data'][i]['is_ticket_exist'] != 0) {
                            html += '<a href="' + json['data'][i]['ticket_link'] + '" type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" style="cursor: pointer;" href="' + json['data'][i]['image_path'] + '" target="_blank"  class="velsof-glyphicons glyphicons book_open" title="' + rm_view_ticket_text + '"><i></i></a>';
                        }
                        html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;"  onclick="getReturnmanagerPendingCustomFeildDetail(' + json['data'][i]['return_id'] + ')" class="velsof-glyphicons glyphicons list" title="' + rm_custom_field_text + '"><i></i></a>'
                        // changes over
                        html += '</td></tr>';
                    }
                } else {
                    html += '<tr><td colspan="9" rowspan="3"><div class="rm_no_data"><span>' + rm_no_pending_text + '</span></div></td></tr>';
                }
                $('#rm_pending_returns_list').html(html);
                $('#rm_pending_returns_current_page').attr('value', page);
                $('#rm_pending_returns_list_holder .paginator-block').html(json['pagination']);
            }
            $(".rm_customer_notes").popover();
            $('[data-toggle="tooltip"]').tooltip();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#' + list_type + ' .rm-bigloader').hide();
            alert('Technical error occurred. Contact to support.');
        }
    });
}
function selectAllCategories() {
    $('.ms-drop #selectItem').each(function () {
        if ($(this).prop("disabled")) {
            $(this).prop('checked', false);
        }
    });
}
function fetchTemplateData() {
    $('#velsof_template_subject').removeClass('vss-hlyt-inv-field');
    var selected_temp = $('#rm_template_name').val();
    if (selected_temp != '') {
        $.ajax({
            type: "POST",
            url: module_path,
            data: 'ajax=true&method=loadEmailTemplate&selected_temp=' + selected_temp + '&selected_lang=' + $("#rm_template_lang").val(),
            dataType: 'json',
            beforeSend: function () {
                $('#rm_template_loader').show();
            },
            success: function (json) {
                $('.email_template_content_block').show();
                $('.test_email_block').show();
                $('#rm_template_loader').hide();
                $('#velsof_template_subject').val(json['subject']);
                $('#hidden_template_id').val(json['id_template']);
                tinyMCE.get('velsof_template_content').setContent(json['body']);
            }
        });
    } else {
        $('.email_template_content_block').hide();
        $('.test_email_block').hide();
    }
}

function saveEmailTemplate() {
    $('#velsof_template_subject').removeClass('vss-hlyt-inv-field');
    $('.returnmanager_template_msg').remove();
    if ($('#velsof_template_subject').val() == '') {
        $('#velsof_template_subject').parent().append('<span class="errorsmall">' + template_subject_error + '</span>');
        return false;
    }
    var selected_lang = $('#rm_template_lang').val();
    var text_email_body = tinyMCE.get('velsof_template_content').getBody().textContent;
    tinyMCE.triggerSave();
    $.ajax({
        type: "POST",
        url: module_path,
        data: 'ajax=true&method=saveEmailTemplate&' + $('#rm_email_template_form :input').serialize() + '&text_content=' + text_email_body,
        dataType: 'json',
        beforeSend: function () {
            $('#rm_template_saving_loader').show();
        },
        success: function (json) {
            $('#rm_template_saving_loader').hide();
            $('.returnmanager_template_msg').remove();
            if (json['error'] != undefined) {
                var html = '<div class="bootstrap returnmanager_template_msg"><div class="alert alert-danger">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += json['error'];
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                setTimeout(function () {
                    $('.returnmanager_template_msg').remove();
                }, 5000);
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
            else {
                var html = '<div class="bootstrap returnmanager_template_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += json['msg'];
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                setTimeout(function () {
                    $('.returnmanager_template_msg').remove();
                }, 5000);
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
        }
    });
}

function savePolicy() {
    $('#manual-policy-form .error').remove();
    var error = false;
    var kberror1 = false;
    var kb_credit_same_error = false;
    var kb_credit_float_error = false;
    var kb_credit_not_digit_error = false;
    var kb_refund_same_error = false;
    var kb_refund_float_error = false;
    var kb_refund_not_digit_error = false;
    var kb_rep_same_error = false;
    var kb_rep_float_error = false;
    var kb_rep_not_digit_error = false;
    $('#manual-policy-form input.add_policy_new').each(function () {
        if ($(this).val() == '') {
            console.log(1);
            error = true;
            $(this).parent().append('<span class="error">' + policy_title_error + '</span>');
        }
    });
    $('#manual-policy-form textarea.add_policy_new_term').each(function () {
        if ($(this).val() == '') {
            error = true;
            $(this).parent().append('<span class="error">' + policy_terms_error + '</span>');
        }
    });
    var numPattern = /^\d+$/;
    if ($('#credit_check').is(':checked')) {
        if ($('#credit_min').val() == '') {
            error = true;
            kb_credit_same_error = true;
            $('#credit_min').parent().append('<div style="margin-left:17px;"><span class="error">' + credit_error + '</span></div>');
        } else if ($('#credit_min').val() % 1 !== 0) {
            error = true;
            kb_credit_float_error = true;
            $('#credit_min').parent().append('<div style="margin-left:17px;"><span class="error">' + Notrequirefloat + '</span></div>');
        } else if (!numPattern.test($('#credit_min').val())) {
            error = true;
            kb_credit_not_digit_error = true;
            $('#credit_min').parent().append('<div style="margin-left:17px;"><span class="error">' + requiredNumber + '</span></div>');
        } else if ($('#credit_min').val() >= 1000) {
            error = true;
            $('#credit_min').parent().append('<div style="margin-left:17px;"><span class="error">' + number_days_error + '</span></div>');
        } else if (Number($('#credit_min').val()) > Number($('#credit_max').val())) {
            error = true;
            $('#credit_min').parent().append('<div style="margin-left:17px;"><span class="error">' + credit_min_error + '</span></div>');
        } else if ($('#credit_min').val() == $('#credit_max').val()) {
            error = true;
            $('#credit_min').parent().append('<div style="margin-left:17px;"><span class="error">' + day_equal_error + '</span></div>');
        }

        if ($('#credit_max').val() == '') {
            error = true;
            if (!kb_credit_same_error) {
                $('#credit_max').parent().append('<div style="margin-left:17px;"><span class="error">' + credit_error + '</span></div>');
            }
        } else if ($('#credit_max').val() % 1 !== 0) {
            error = true;
            if (!(kb_credit_float_error)) {
                $('#credit_max').parent().append('<div style="margin-left:17px;"><span class="error">' + Notrequirefloat + '</span></div>');
            }
        } else if (!numPattern.test($('#credit_max').val())) {
            error = true;
            if (!(kb_credit_not_digit_error)) {
                $('#credit_max').parent().append('<div style="margin-left:17px;"><span class="error">' + requiredNumber + '</span></div>');
            }
        } else if ($('#credit_max').val() >= 1000 || $('#credit_max').val() <= 0) {
            error = true;
            $('#credit_max').parent().append('<div style="margin-left:17px;"><span class="error">' + number_days_error + '</span></div>');
        }
    }

    if ($('#refund_check').is(':checked')) {
        if ($('#refund_min').val() == '') {
            error = true;
            kb_refund_same_error = true;
            $('#refund_min').parent().append('<div style="margin-left:17px;"><span class="error">' + refund_error + '</span></div>');
        } else if ($('#refund_min').val() % 1 !== 0) {
            error = true;
            kb_refund_float_error = true;
            $('#refund_min').parent().append('<div style="margin-left:17px;"><span class="error">' + Notrequirefloat + '</span></div>');
        } else if (!numPattern.test($('#refund_min').val())) {
            error = true;
            kb_refund_not_digit_error = true;
            $('#refund_min').parent().append('<div style="margin-left:17px;"><span class="error">' + requiredNumber + '</span></div>');
        } else if ($('#refund_min').val() >= 1000) {
            error = true;
            $('#refund_min').parent().append('<div style="margin-left:17px;"><span class="error">' + number_days_error + '</span></div>');
        } else if (Number($('#refund_min').val()) > Number($('#refund_max').val())) {
            error = true;
            $('#refund_min').parent().append('<div style="margin-left:17px;"><span class="error">' + refund_min_error + '</span></div>');
        } else if ($('#refund_min').val() == $('#refund_max').val()) {
            error = true;
            $('#refund_min').parent().append('<div style="margin-left:17px;"><span class="error">' + day_equal_error + '</span></div>');
        }

        if ($('#refund_max').val() == '') {
            error = true;
            if (!kb_refund_same_error) {
                $('#refund_max').parent().append('<div style="margin-left:17px;"><span class="error">' + refund_error + '</span></div>');
            }
        } else if ($('#refund_max').val() % 1 !== 0) {
            error = true;
            if (!kb_refund_float_error) {
                $('#refund_max').parent().append('<div style="margin-left:17px;"><span class="error">' + Notrequirefloat + '</span></div>');
            }
        } else if (!numPattern.test($('#refund_max').val())) {
            error = true;
            if (!kb_refund_not_digit_error) {
                $('#refund_max').parent().append('<div style="margin-left:17px;"><span class="error">' + requiredNumber + '</span></div>');
            }
        } else if ($('#refund_max').val() >= 1000 || $('#refund_max').val() <= 0) {
            error = true;
            $('#refund_max').parent().append('<div style="margin-left:17px;"><span class="error">' + number_days_error + '</span></div>');
        }
    }

    if ($('#replacement_check').is(':checked')) {
        if ($('#replacement_max').val() == '') {
            kb_rep_same_error = true;
            error = true;
            $('#replacement_max').parent().append('<div style="margin-left:17px;"><span class="error">' + replacement_error + '</span></div>');
        } else if ($('#replacement_max').val() % 1 !== 0) {
            kb_rep_float_error = true;
            error = true;
            $('#replacement_max').parent().append('<div style="margin-left:17px;"><span class="error">' + Notrequirefloat + '</span></div>');
        } else if (!numPattern.test($('#replacement_max').val())) {
            kb_rep_not_digit_error = true;
            error = true;
            $('#replacement_max').parent().append('<div style="margin-left:17px;"><span class="error">' + requiredNumber + '</span></div>');
        } else if ($('#replacement_max').val() >= 1000 || $('#Replacement').val() <= 0) {
            error = true;
            $('#replacement_max').parent().append('<div style="margin-left:17px;"><span class="error">' + number_days_error + '</span></div>');
        } else if ($('#replacement_min').val() == $('#replacement_max').val()) {
            error = true;
            $('#replacement_min').parent().append('<div style="margin-left:17px;"><span class="error">' + day_equal_error + '</span></div>');
        }

        if ($('#replacement_min').val() == '') {
            error = true;
            if (!kb_rep_same_error) {
                $('#replacement_min').parent().append('<div style="margin-left:17px;"><span class="error">' + replacement_error + '</span></div>');
            }
        } else if ($('#replacement_min').val() % 1 !== 0) {
            error = true;
            if (!kb_rep_float_error) {
                $('#replacement_min').parent().append('<div style="margin-left:17px;"><span class="error">' + Notrequirefloat + '</span></div>');
            }
        } else if (!numPattern.test($('#replacement_min').val())) {
            error = true;
            if (!kb_rep_not_digit_error) {
                $('#replacement_min').parent().append('<div style="margin-left:17px;"><span class="error">' + requiredNumber + '</span></div>');
            }
        } else if ($('#replacement_min').val() >= 1000) {
            error = true;
            $('#replacement_min').parent().append('<div style="margin-left:17px;"><span class="error">' + number_days_error + '</span></div>');
        } else if (Number($('#replacement_min').val()) > Number($('#replacement_max').val())) {
            error = true;
            $('#replacement_min').parent().append('<div style="margin-left:17px;"><span class="error">' + replacement_min_error + '</span></div>');
        }
    }

    if (!error) {
        $('.returnmanager_success_msg').remove();
        $.ajax({
            url: module_path,
            type: 'post',
            dataType: 'json',
            data: 'ajax=true&method=AddData&'
                + $('#manual-policy-form input, #manual-policy-form textarea').serialize()
                + '&type=policy',
            beforeSend: function () {
                $('#manual-policy-form').fadeTo('slow', 0.9);
                $('#rm_policy_form_loader').show();
            },
            complete: function () {
                $('#manual-policy-form').fadeTo('slow', 1);
                $('#rm_policy_form_loader').hide();
                $(".process-icon-new").closest("a").css("text-decoration", "none");
            },
            success: function (responsepolicy) {
                $('#policy_records').html(responsepolicy['html']);
                refreshDefaultPolicy(responsepolicy['policy_data'], responsepolicy['default_policy']);
                $('.modal-backdrop').hide();
                $('#modal_policy').modal('hide');
                $(".rm_customer_notes").popover();
                $('[data-toggle="tooltip"]').tooltip();
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += success_adding_policy;
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                setTimeout(function () {
                    $('.returnmanager_success_msg').remove();
                }, 5000);
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#save_policy').prop("disabled", false);
            }
        });
    } else {
        $('#save_policy').prop("disabled", false);
    }
}

function saveReason() {
    $('.returnmanager_success_msg').remove();
    $('#manual-reason-form .error').remove();
    var error = false;
    $('input.add_reason_new').each(function () {
        if ($(this).val() == '') {
            error = true;
            $(this).parent().append('<span class="error">' + reason_error + '</span>');
        }
    });
    if (!error) {
        $.ajax({
            url: module_path,
            type: 'post',
            data: 'ajax=true&method=AddData&'
                + $('#manual-reason-form :input').serialize()
                + '&type=reason',
            beforeSend: function () {
                $('#manual-reason-form').fadeTo('slow', 0.9);
                $('#rm_new_reason_form_loader').show();
            },
            complete: function () {
                $('#manual-reason-form').fadeTo('slow', 1);
                $('#rm_new_reason_form_loader').hide();
                $(".process-icon-new").closest("a").css("text-decoration", "none");
            },
            success: function (response) {
                $('#reason_records').html(response);
                $('.modal-backdrop').hide();
                $('#modal_reason').modal('hide');
                $(".rm_customer_notes").popover();
                $('[data-toggle="tooltip"]').tooltip();
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += success_adding_reason;
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                setTimeout(function () {
                    $('.returnmanager_success_msg').remove();
                }, 5000);
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#save_reason').prop("disabled", true);
            }
        });
    } else {
        $('#save_reason').prop("disabled", true);
    }
}

//changes by vishal for adding cancel functionality

function getNextCancelListingPage(page, active) {
    /* Start Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
    var list_type = '';
    var cancel_id = '';
    var customer_name = '';
    var order_id = '';
    var order_by = '';
    var order_dir = '';
    if (active == 2) {
        list_type = 'rm_complete_cancel_list';
        cancel_id = $("#rm_complete_cancel_custom_cancel_id").val();
        customer_name = $("#rm_complete_cancel_customer_name").val();
        order_id = $("#rm_complete_cancel_order_id").val();
        order_by = $('select[name="rm_complete_cancel_sortby"]').val();
        order_dir = $('select[name="rm_complete_cancel_sortdir"]').val();
    } else if (active == 1) {
        list_type = 'rm_pending_cancel_list_holder';
        cancel_id = $("#rm_pending_cancel_custom_cancel_id").val();
        customer_name = $("#rm_pending_cancel_customer_name").val();
        order_id = $("#rm_pending_cancel_order_id").val();
        order_by = $('select[name="rm_pending_cancel_sortby"]').val();
        order_dir = $('select[name="rm_pending_cancel_sortdir"]').val();
    } else {
        list_type = 'rm_pending_cancel_list_holder';
    }
    /* End Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
    $.ajax({
        url: module_path,
        data: '&ajax=true&method=getNextCancelListingPage&inc_page_number=' + page + '&active_status=' + active + '&cancel_id=' + cancel_id + '&customer_name=' + customer_name + '&order_id=' + order_id + '&order_by=' + order_by + '&order_dir=' + order_dir,
        type: 'post',
        datatype: 'json',
        beforeSend: function () {
            $('#' + list_type + ' .rm-bigloader').show();
        },
        success: function (json) {
            $('#' + list_type + ' .rm-bigloader').hide();
            var html = '';
            var i = 0;
            var row_class = '';

            if (active == 2) {
                if (json['flag']) {
                    for (i = 0; i < json['data'].length; i++) {
                        if (i % 2 == 0)
                            row_class = 'even';
                        else
                            row_class = 'odd';
                        var product_attr = '';
                        if (typeof json['data'][i]['product_attr'] != 'undefined')
                            product_attr = json['data'][i]['product_attr'];
                        else
                            product_attr = '<br>';
                        if (isEmpty(product_attr)) {
                            product_attr = '<br>';
                        }


                        if (json['data'][i]['comment'] != '')
                            var return_comment = json['data'][i]['comment'];
                        else
                            var return_comment = no_comments_text;
                        //					var return_comment = "<span class='vss_italic_text'>"+no_comments_text+"</span>";

                        if (json['data'][i]['whopayshipping'] == 'c')
                            var shipping_paid_by = rm_customer_text;
                        else
                            var shipping_paid_by = rm_so_text;
                        html += '<tr class="pure-table-' + row_class + '">';
                        // changes by rishabh jain to add return id column
                        html += '<td>' + json['data'][i]['cancel_id'] + '</td>';
                        // changes over
                        html += '<td><a href="' + order_controller + '&id_order=' + json['data'][i]['order_id'] + '&vieworder" target="_blank">' + json['data'][i]['order_reference'] + '</a></td>';
                        html += '<td><a href="' + customer_controller + '&id_customer=' + json['data'][i]['customer_id'] + '&viewcustomer" target="_blank">' + json['data'][i]['cust_name'] + '</a></td>';
                        html += '<td>' + json['data'][i]['reason'] + '</td>';
                        html += '<td class="rm_velsof_action">';
                        html += '<a style="margin-top: -20px;" type="' + json['data'][i]['cancel_id'] + '" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="' + json['data'][i]['reason'] + '" class="velsof-glyphicons glyphicons circle_question_mark rm_customer_notes" title="' + rm_reason_text + '"><i></i></a>';
                        html += '<a style="margin-top: -20px;" type="' + json['data'][i]['cancel_id'] + '" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="' + return_comment + '" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title="' + rm_comment_text + '"><i></i></a>';
                        html += '</td></tr>';
                    }
                } else {
                    html += '<tr><td colspan="9" rowspan="3"><div class="rm_no_data"><span>' + rm_no_data_label + '</span></div></td></tr>';
                }
                $('#rm_cancel_archive_list_tbody').html(html);
                $('#rm_archive_cancel_current_page').attr('value', page);
                $('#rm_list_cancel_container .paginator-block').html(json['pagination']);
            }
            else {
                if (json['flag']) {
                    for (i = 0; i < json['data'].length; i++) {
                        if (i % 2 == 0)
                            row_class = 'even';
                        else
                            row_class = 'odd';
                        var product_attr = '';
                        if (typeof json['data'][i]['product_attr'] != 'undefined')
                            product_attr = json['data'][i]['product_attr'];
                        else
                            product_attr = '<br>';
                        if (isEmpty(product_attr)) {
                            product_attr = '<br>';
                        }

                        if (json['data'][i]['comment'] != '')
                            var return_comment = json['data'][i]['comment'];
                        else
                            var return_comment = no_comments_text;
                        //					var return_comment = "<span class='vss_italic_text'>"+no_comments_text+"</span>";

                        if (json['data'][i]['whopayshipping'] == 'c')
                            var shipping_paid_by = rm_customer_text;
                        else
                            var shipping_paid_by = rm_so_text;
                        if (json['data'][i]['replacedwith_product_link'] != undefined) {
                            var requested_product_html = '<b><a href="' + json['data'][i]['replacedwith_product_link'] + '" target="_blank">' + json['data'][i]['replacedwith_product_name'] + '</a></b>';
                        } else {
                            var requested_product_html = not_applicable_msg;
                        }
                        html += '<tr class="rm_pending_cancel_returns pure-table-' + row_class + '">';
                        // changes by rishabh jain to add return id column
                        html += '<td>' + json['data'][i]['cancel_id'] + '</td>';
                        // changes over
                        html += '<td><a href="' + order_controller + '&id_order=' + json['data'][i]['order_id'] + '&vieworder" target="_blank">' + json['data'][i]['order_reference'] + '</a></td>';
                        html += '<td><a href="' + customer_controller + '&id_customer=' + json['data'][i]['customer_id'] + '&viewcustomer" target="_blank">' + json['data'][i]['cust_name'] + '</a></td>';
                        html += '<td>' + json['data'][i]['reason'] + '</td>';
                        html += '<td class="rm_velsof_action">';
                        html += '<a style="margin-top: -20px;" type="' + json['data'][i]['cancel_id'] + '_' + json['data'][i]['id_lang'] + '" style="cursor: pointer;" onclick="allowCancel(this);" class="velsof-glyphicons glyphicons ok" title="' + rm_allow_return_text + '"><i></i></a>';
                        html += '<a style="margin-top: -20px;" type="' + json['data'][i]['cancel_id'] + '_' + json['data'][i]['id_lang'] + '" style="cursor: pointer;" onclick="denyCancel(this);" class="velsof-glyphicons glyphicons remove" title="' + rm_deny_return_text + '"><i></i></a>';
                        html += '<a style="margin-top: -20px;" type="' + json['data'][i]['cancel_id'] + '" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="' + json['data'][i]['reason'] + '" class="velsof-glyphicons glyphicons circle_question_mark rm_customer_notes" title="' + rm_reason_text + '"><i></i></a>';
                        html += '<a style="margin-top: -20px;" type="' + json['data'][i]['cancel_id'] + '" style="cursor: pointer;" data-container="body" data-toggle="popover" data-placement="left" data-content="' + return_comment + '" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title="' + rm_comment_text + '"><i></i></a>';
                        // changes over
                        html += '</td></tr>';
                    }
                } else {
                    html += '<tr><td colspan="9" rowspan="3"><div class="rm_no_data"><span>' + rm_no_pending_text + '</span></div></td></tr>';
                }
                $('#rm_pending_cancel_list').html(html);
                $('#rm_pending_cancel_current_page').attr('value', page);
                $('#rm_pending_cancel_list_holder .paginator-block').html(json['pagination']);
            }
            $(".rm_customer_notes").popover();
            $('[data-toggle="tooltip"]').tooltip();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#' + list_type + ' .rm-bigloader').hide();
            alert('Technical error occurred. Contact to support.');
        }
    });
}

function actionOnCancel(action) {
    $('.error').remove();
    $('#save_cancel').prop("disabled", false);
    $('input[name="cancel_action_type"]').attr('value', action);
    $('#manual-cancel-form input[type="text"]').val('');
    if (action > 0) {
        $.ajax({
            url: module_path,
            type: 'post',
            dataType: 'json',
            data: 'ajax=true&method=getData'
                + '&cancel_id=' + action
                + '&type=cancel',
            success: function (response) {
                if (response['cancel_text'] != undefined && response['cancel_text'].length) {
                    for (var i in response['cancel_text']) {
                        if ($('input[name="cancel_new_' + response['cancel_text'][i]['id_lang'] + '"]').length) {
                            $('input[name="cancel_new_' + response['cancel_text'][i]['id_lang'] + '"]').attr('value', response['cancel_text'][i]['text']);
                            $('input[name="cancel_new_' + response['cancel_text'][i]['id_lang'] + '"]').val(response['cancel_text'][i]['text']);
                        }
                    }
                }
                $('#modal_cancel').modal({ 'show': true, 'backdrop': 'static' });
                $(".rm_customer_notes").popover();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    } else {
        $('#modal_cancel').modal({ 'show': true, 'backdrop': 'static' });
        $(".rm_customer_notes").popover();
        $('[data-toggle="tooltip"]').tooltip();
    }
}
//changes end


//changes by vishal on 14 august 2020 for handling the order return from admin end

function displayKbProductAttribute(e) {
    var val = e;
    if (val == 0) {
        $('#kb_product_attribute_choose_block').hide();
    } else {
        $('#rm_kbproduct_loader').show();
        $.ajax({
            url: module_link,
            type: 'post',
            data: 'ajax=true&method=kbgetProductAttribute&rm_return_product=' + val,
            dataType: 'json',
            beforeSend: function () {
            },
            complete: function () {
            },
            success: function (json) {
                var count = Object.keys(json).length;
                var select = $("<select class='chosen rm_form_control'></select>").attr("id", "rm_return_product_attribute_id").attr("name", "rm_return_product_attribute_id");
                $.each(json, function (index, json) {
                    select.append($("<option></option>").attr("value", json.product_attribute_id).text(json.product_attribute_name));
                });
                $("#rm_return_product_attribute").html(select);
                if (count != 0) {
                    $('#kb_product_attribute_choose_block').show();
                } else {
                    $('#kb_product_attribute_choose_block').hide();
                }
                $('#rm_kbproduct_loader').hide();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
            }
        });
    }
}

function displayReturnNote(e) {
    $(e).parent().find('span.rm_error').remove();
    var val = $(e).val();
    if (val == 'replacement') {
        $('#kb_product_choose_block').show();
    } else {
        $('#kb_product_choose_block').hide();
    }
    $('#rm_return_type_note p').hide();
    if (val != 0) {
        $('#rm_return_type_note p#rm_return_type_note_' + val).show();
    }
    //setLeftColHeight('rm_popup_request_form');
}

function setLeftColHeight(container) {
    $('#rm_popup_pro_info').css('height', $('#' + container).height() + 21);
}

function displayReasonNoteOnMultipleForm(e, product_id){
    $(e).parent().find('span.rm_error').remove();
    var val = $(e).val();
    $('#rm_reason_type_note_' + product_id + ' p').hide();
    if (val != 0){
    $('#rm_reason_type_note_' + product_id + ' p#rm_reason_type_note_' + val).show();
    }
    setLeftColHeight('rm_popup_request_form');
}

//changes by vishal on 14 august 2020 for handling the order return from admin end
//chages add new address


function displayEditCustomFieldPopup(idCustomField) {
    $.ajax({
        type: "POST",
        url: rm_ajax_action,
        data: $('#returnmanager_configuration_form').serialize() + '&ajax=true&custom_fields_action=displayEditCustomFieldForm&id=' + idCustomField,
        async: false,
        dataType: 'json',
        success: function (json) {
            $('#modal_edit_custom_field_form').html(json.response);
            $('#modal_edit_custom_field_form').modal({ 'show': true, 'backdrop': 'static' });
        }
    });
}

function submitEditForm() {
    //    $("#loader_edit_form").removeClass("hidden_custom");
    var errorOccured = validateEditForm();
    if (errorOccured != 0) {
        //        $("#loader_edit_form").addClass("hidden_custom");
        return false;
    } else {
        // Showing the ajax loader

        //Send ajax request to save the data
        $.ajax({
            type: "POST",
            url: rm_ajax_action,
            data: $('#returnmanager_configuration_form').serialize() + '&ajax=true&custom_fields_action=editCustomFieldForm',
            async: true,
            dataType: 'json',
            beforeSend: function () {
                closeModalForm("modal_edit_custom_field_form");
            },
            success: function (json) {
                // Adding a row in table
                var requiredText = '', activeText = '';
                if (json.response.required == '1') {
                    requiredText = yes_text;
                } else {
                    requiredText = no_text;
                }
                if (json.response.active == '1') {
                    activeText = yes_text;
                } else {
                    activeText = no_text;
                }
                var findRowCount = $("#tr_pure_table_" + json.response.id_velsof_rm_custom_fields).children("td:first").text();
                var tableRow = '<tr class="row_changed" id="tr_pure_table_' + json.response.id_velsof_rm_custom_fields + '">';
                tableRow += '<td>' + findRowCount + '</td>';
                tableRow += '<td class="width_25"><div class="div_250px_ellipsis">' + json.response.field_label + '</div></td>';
                tableRow += '<td>' + getCustomFieldsTypeTranslatedText(json.response.type) + '</td>';
                tableRow += '<td>' + requiredText + '</td>';
                tableRow += '<td>' + activeText + '</td>';
                tableRow += '<td class="center" style="padding: 12px;">';
                tableRow += '<a style="margin-top: -26px;" href="javascript://" onclick="displayEditCustomFieldPopup(' + json.response.id_velsof_rm_custom_fields + ')" type="11" class="velsof-glyphicons2 glyphicons pencil"><i data-toggle="tooltip" data-placement="top" data-original-title="Edit this custom field"></i></a>';
                tableRow += '<a style="margin-top: -26px;" href="javascript://" onclick="deleteCustomFieldRow(' + json.response.id_velsof_rm_custom_fields + ')" type="11" class="velsof-glyphicons2 glyphicons bin" onclick=""><i data-toggle="tooltip" data-placement="top" data-original-title="Delete this custom field."></i></a>';
                tableRow += '</td>';
                tableRow += '</tr>';

                // Removing the success green color from all the previous edited/added rows
                $("#tbody_custom_fields_data").children().removeClass("row_changed");
                $("#tr_pure_table_" + json.response.id_velsof_rm_custom_fields).replaceWith(tableRow);
                $("#div_custom_fields_success").removeClass("hidden_custom");
                $("#loader_edit_form").addClass("hidden_custom");
                setTimeout(function () {
                    $("#tbody_custom_fields_data").children().removeClass("row_changed", 1000);
                }, 5000);
            }
        });
    }
}

function deleteCustomFieldRow(idCustomField) {
    var canDelete = confirm(areYouSureToDelete);
    if (canDelete == true) {
        // Send ajax request to save the data
        $.ajax({
            type: "POST",
            url: rm_ajax_action,
            data: '&ajax=true&custom_fields_action=deleteCustomFieldRow&id_velsof_rm_custom_fields=' + idCustomField,
            async: false,
            dataType: 'json',
            success: function (json) {
                $("#tr_pure_table_" + idCustomField).addClass("hidden_custom");
                $("#div_custom_fields_success").removeClass("hidden_custom");
            }
        });
    }
}

// Edit form
function checkFieldTypeEdit(objE) {
    var boxValue = objE.value;
    // If options are required
    if (boxValue == "selectbox" || boxValue == "radio" || boxValue == "checkbox") {
        // Display textarea to accept option values and labels
        $("#edit_field_options").removeClass("hidden_custom");
        $('select[name="edit_custom_fields[validation_type]"]').prop('disabled', 'disabled');
    } else {
        $("#edit_field_options").addClass("hidden_custom");
        $('select[name="edit_custom_fields[validation_type]"]').prop('disabled', false);
    }
}

/**
 * This function is used to validate the values
 * @returns {undefined}
 */
function validateEditForm() {
    var error = 0, errorFieldOptions = 0;
    var errorMessageFieldOptions;
    var elemType = $("#returnmanager_edit_custom_field_type");
    var optionBoxes = $(".returnmanager_edit_field_options");

    var elemLabelBoxes = $(".returnmanager_edit_field_label");
    var boxCheckerLabels = 0;
    elemLabelBoxes.each(function (index) {
        if ($(this).val() != "") {
            boxCheckerLabels = 1;
        }
    });

    // If nothing provided
    if (boxCheckerLabels == 0) {
        error = 1;
        errorMessageFieldOptions = canNotLeaveAllBoxesEmpty;
        $("#error_message_edit_field_label").html(errorMessageFieldOptions);
        $("#error_message_edit_field_label").removeClass("hidden_custom");
    } else {
        $("#error_message_edit_field_label").addClass("hidden_custom");
    }

    // Checking if selectbox or radio or checkbox is selected
    if (elemType.val() == 'selectbox' || elemType.val() == "radio" || elemType.val() == "checkbox") {
        // Loopiong through each value
        var boxChecker = 0;
        optionBoxes.each(function (index) {
            if ($(this).val() != "") {
                boxChecker = 1;
                // Splitting on \n
                var lines = $(this).val().split('\n');
                for (var i = 0; i < lines.length; i++) {
                    var alphanumeric = lines[i].split('|');
                    if (lines[i] == '') {
                        continue;
                    }
                    // If there are more than one | present in a line
                    if (alphanumeric.length != 2) {
                        error = 1;
                        errorFieldOptions = 1;
                    } else {
                        for (var j = 0; j < alphanumeric.length; j++) {
                            if (j == 0) {
                                var expression = /^[a-zA-Z0-9]+$/;
                            } else {
                                var expression = /^[a-zA-Z0-9 -_/]+$/;
                            }
                            if (!expression.test(alphanumeric[j])) {
                                error = 1;
                                errorFieldOptions = 1;
                            }
                        }
                    }
                }
            }
        });

        // If nothing provided
        if (boxChecker == 0) {
            error = 1;
            errorMessageFieldOptions = canNotLeaveAllBoxesEmpty;
            $("#error_message_edit_field_options").html(errorMessageFieldOptions);
            $("#error_message_edit_field_options").removeClass("hidden_custom");
        } else {
            if (errorFieldOptions == 1) {
                errorMessageFieldOptions = pleaseProvideInValidFormat;
                $("#error_message_edit_field_options").html(errorMessageFieldOptions);
                $("#error_message_edit_field_options").removeClass("hidden_custom");
            } else {
                $("#error_message_edit_field_options").addClass("hidden_custom");
            }
        }
    }

    return error;
}

/*
 * Function Added by Raghu on 22-Aug-2017 for fixing 'Custom Fields type translations' issue
 * @param {type} block_name
 * @returns {String|cart_block_txt}
 */
function getCustomFieldsTypeTranslatedText(type_value) {
    var final_txt = '';
    switch (type_value) {
        case 'textbox':
            final_txt = text_box_txt;
            break;
        case 'selectbox':
            final_txt = select_box_txt;
            break;
        case 'textarea':
            final_txt = text_area_txt;
            break;
        case 'radio':
            final_txt = radio_button_txt;
            break;
        case 'checkbox':
            final_txt = check_boxes_txt;
            break;
    }
    return final_txt;
}

function changeLanguageBox(objE, elementToChange) {
    var idLanguage = objE.value;
    $(".returnmanager_" + elementToChange).addClass("hidden_custom");
    $("#" + elementToChange + "_language_" + idLanguage).removeClass("hidden_custom");
}

function checkFieldType(objE) {
    var boxValue = objE.value;
    // If options are required
    if (boxValue == "selectbox" || boxValue == "radio" || boxValue == "checkbox") {
        // Display textarea to accept option values and labels
        $("#field_options").removeClass("hidden_custom");
        $('select[name="custom_fields[validation_type]"]').prop('disabled', 'disabled');
    }
    else {
        $("#field_options").addClass("hidden_custom");
        $('select[name="custom_fields[validation_type]"]').prop('disabled', false);
    }
}

/**
 * This functin submits the form if all the values are valid
 * @returns {undefined}
 */
function kbrmsubmitForm() {
    //    $("#loader_add_form").removeClass("hidden_custom");
    var errorOccured = validateForm();

    if (errorOccured != 0) {
        $("#loader_add_form").addClass("hidden_custom");
        return false;
    } else {
        $.ajax({
            type: "POST",
            url: rm_ajax_action,
            data: $('#returnmanager_configuration_form').serialize() + '&ajax=true&custom_fields_action=addCustomFieldForm',
            async: true,
            dataType: 'json',
            beforeSend: function () {
                closeModalForm("modal_custom_field");

            },
            success: function (json) {
                var requiredText = '', activeText = '';
                if (json.response.required == '1') {
                    requiredText = yes_text;
                } else {
                    requiredText = no_text;
                }
                if (json.response.active == '1') {
                    activeText = yes_text;
                } else {
                    activeText = no_text;
                }
                var rowCount = $('#table_custom_fields_data > tbody > tr').length;
                var tableRow = '<tr class="pure-table-striped row_changed" id="tr_pure_table_' + json.response.id_velsof_rm_custom_fields + '">';
                tableRow += '<td>' + rowCount + '</td>';
                tableRow += '<td class="width_25"><div class="div_250px_ellipsis">' + json.response.field_label + '</div></td>';
                tableRow += '<td>' + getCustomFieldsTypeTranslatedText(json.response.type) + '</td>';
                tableRow += '<td>' + requiredText + '</td>';
                tableRow += '<td>' + activeText + '</td>';
                tableRow += '<td class="center" style="padding: 12px;">';
                tableRow += '<a style="margin-top: -26px;" href="javascript://" onclick="displayEditCustomFieldPopup(' + json.response.id_velsof_rm_custom_fields + ')" type="11" class="velsof-glyphicons2 glyphicons pencil"><i data-toggle="tooltip" data-placement="top" data-original-title="Edit this custom field"></i></a>';
                tableRow += '<a style="margin-top: -26px;" href="javascript://" onclick="deleteCustomFieldRow(' + json.response.id_velsof_rm_custom_fields + ')" type="11" class="velsof-glyphicons2 glyphicons bin" onclick=""><i data-toggle="tooltip" data-placement="top" data-original-title="Delete this custom field."></i></a>';
                tableRow += '</td>';
                tableRow += '</tr>';
                $("#tbody_custom_fields_data").children().removeClass("row_changed");
                $("#tr_custom_fields_add_new").before(tableRow);
                //                $("#div_custom_fields_success").removeClass("hidden_custom");
                //                $("#loader_add_form").addClass("hidden_custom");
                setTimeout(function () {
                    $("#tbody_custom_fields_data").children().removeClass("row_changed", 1000);
                }, 5000);
            }
        });
    }
}

/**
 * This function is used to validate the values
 * @returns {undefined}
 */
function validateForm() {
    var error = 0, errorFieldOptions = 0;
    var errorMessageFieldOptions;
    var elemType = $("#returnmanager_custom_field_type");
    var optionBoxes = $(".returnmanager_field_options");

    var elemLabelBoxes = $(".returnmanager_field_label");
    var boxCheckerLabels = 0;
    elemLabelBoxes.each(function (index) {
        if ($(this).val() != "") {
            boxCheckerLabels = 1;
        }
    });

    // If nothing provided
    if (boxCheckerLabels == 0) {
        error = 1;
        errorMessageFieldOptions = canNotLeaveAllBoxesEmpty;
        $("#error_message_field_label").html(errorMessageFieldOptions);
        $("#error_message_field_label").removeClass("hidden_custom");
    } else {
        $("#error_message_field_label").addClass("hidden_custom");
    }

    // Checking if selectbox or radio or checkbox is selected
    if (elemType.val() == 'selectbox' || elemType.val() == "radio" || elemType.val() == "checkbox") {
        // Loopiong through each value
        var boxChecker = 0;
        optionBoxes.each(function (index) {
            if ($(this).val() != "") {
                boxChecker = 1;
                // Splitting on \n
                var lines = $(this).val().split('\n');
                for (var i = 0; i < lines.length; i++) {
                    var alphanumeric = lines[i].split('|');
                    if (lines[i] == '') {
                        continue;
                    }
                    // If there are more than one | present in a line
                    if (alphanumeric.length != 2) {
                        error = 1;
                        errorFieldOptions = 1;
                    } else {
                        for (var j = 0; j < alphanumeric.length; j++) {
                            if (j == 0) {
                                // Not allowing the space in value side
                                var expression = /^[a-zA-Z0-9]+$/;
                            } else {
                                var expression = /^[a-zA-Z0-9 -_/]+$/;
                            }
                            if (!expression.test(alphanumeric[j])) {
                                error = 1;
                                errorFieldOptions = 1;
                            }
                        }
                    }
                }
            }
        });

        // If nothing provided
        if (boxChecker == 0) {
            error = 1;
            errorMessageFieldOptions = canNotLeaveAllBoxesEmpty;
            $("#error_message_field_options").html(errorMessageFieldOptions);
            $("#error_message_field_options").removeClass("hidden_custom");
        } else {
            if (errorFieldOptions == 1) {
                errorMessageFieldOptions = pleaseProvideInValidFormat;
                $("#error_message_field_options").html(errorMessageFieldOptions);
                $("#error_message_field_options").removeClass("hidden_custom");
            } else {
                $("#error_message_field_options").addClass("hidden_custom");
            }
        }
    }

    return error;
}

function saveAddress() {

    $('#manual-address-form .error').remove();
    var error = false;
    $('#manual-address-form input.add_address_new').each(function () {
        if ($(this).val() == '') {
            if (this.id != 'address_new_line2') {
                error = true;
                $(this).parent().append('<span class="error">' + address_error + '</span>');
            }
        }
    });
    if (!error) {
        $.ajax({
            url: module_path,
            type: 'post',
            data: 'ajax=true&method=AddData&'
                + $('#manual-address-form :input').serialize()
                + '&type=address',
            beforeSend: function () {
                $('#manual-address-form').fadeTo('slow', 0.9);
                $('#rm_new_address_form_loader').show();
            },
            complete: function () {
                $('#manual-address-form').fadeTo('slow', 1);
                $('#rm_new_address_form_loader').hide();
                $(".process-icon-new").closest("a").css("text-decoration", "none");
            },
            success: function (response) {
                $('#address_records').html(response);
                $('.modal-backdrop').hide();
                $('#modal_address').modal('hide');
                $(".rm_customer_notes").popover();
                $('[data-toggle="tooltip"]').tooltip();
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += success_adding_address;
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                setTimeout(function () {
                    $('.returnmanager_success_msg').remove();
                }, 5000);
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
        });
    }
}
//changes over

//changes by vishal for adding cancel functionality
function saveCancel() {
    $('#manual-cancel-form .error').remove();
    var error = false;
    $('#manual-cancel-form input.add_cancel_new').each(function () {
        if ($(this).val() == '') {
            error = true;
            $(this).parent().append('<span class="error">' + status_error + '</span>');
        }
    });
    if (!error) {
        $.ajax({
            url: module_path,
            type: 'post',
            data: 'ajax=true&method=AddData&'
                + $('#manual-cancel-form :input').serialize()
                + '&type=cancel',
            beforeSend: function () {
                $('#manual-cancel-form').fadeTo('slow', 0.9);
                $('#rm_new_cancel_form_loader').show();
            },
            complete: function () {
                $('#manual-cancel-form').fadeTo('slow', 1);
                $('#rm_new_cancel_form_loader').hide();
                $(".process-icon-new").closest("a").css("text-decoration", "none");
            },
            success: function (response) {
                $('#cancel_records').html(response);
                $('.modal-backdrop').hide();
                $('#modal_cancel').modal('hide');
                $(".rm_customer_notes").popover();
                $('[data-toggle="tooltip"]').tooltip();
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += success_adding_cancel;
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                setTimeout(function () {
                    $('.returnmanager_success_msg').remove();
                }, 5000);
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#save_cancel').prop("disabled", false);
            }
        });
    } else {
        $('#save_cancel').prop("disabled", true);
    }
}
//changes end

function saveStatus() {
    $('#manual-status-form .error').remove();
    var error = false;
    $('#manual-status-form input.add_status_new').each(function () {
        if ($(this).val() == '') {
            error = true;
            $(this).parent().append('<span class="error">' + status_error + '</span>');
        }
    });
    if (!error) {
        $.ajax({
            url: module_path,
            type: 'post',
            data: 'ajax=true&method=AddData&'
                + $('#manual-status-form :input').serialize()
                + '&type=status',
            beforeSend: function () {
                $('#manual-status-form').fadeTo('slow', 0.9);
                $('#rm_new_status_form_loader').show();
            },
            complete: function () {
                $('#manual-status-form').fadeTo('slow', 1);
                $('#rm_new_status_form_loader').hide();
                $(".process-icon-new").closest("a").css("text-decoration", "none");
            },
            success: function (response) {
                $('#status_records').html(response);
                $('.modal-backdrop').hide();
                $('#modal_status').modal('hide');
                $(".rm_customer_notes").popover();
                $('[data-toggle="tooltip"]').tooltip();
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += success_adding_status;
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                setTimeout(function () {
                    $('.returnmanager_success_msg').remove();
                }, 5000);
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#save_status').prop("disabled", true);
            }
        });
    } else {
        $('#save_status').prop("disabled", true);
    }
}

function toggleStatus(e) {
    if ($(e).is(':checked')) {
        $(e).parent().find('input[type="text"]').attr('disabled', false);
        $(e).parent().parent().find('.rm_policy_options_text').show();
    }
    else {
        $(e).parent().find('input[type="text"]').attr('disabled', true);
        $(e).parent().parent().find('.rm_policy_options_text').hide();
    }

}

function closeModalForm(modal_form) {
    $('.modal-backdrop').hide();
    $('#' + modal_form).modal('hide');
    $('#' + modal_form + ' input[type="text"]').val('');
    $('#' + modal_form + ' textarea').val('');
    $('#' + modal_form + ' input[type="checkbox"]').attr('checked', false);
    if (modal_form == 'modal_policy') {
        $('#' + modal_form + ' input[type="checkbox"][name="credit"]').attr('disabled', true);
        $('#' + modal_form + ' input[type="checkbox"][name="Refund"]').attr('disabled', true);
        $('#' + modal_form + ' input[type="checkbox"][name="Replacement"]').attr('disabled', true);
    }
}

function productMapping(elem) {
    var id = $(elem).attr("type");
    $('.already_mapped').attr('disabled', false);
    $('.already_mapped').removeClass('already_mapped');
    $.ajax({
        type: 'post',
        url: module_path,
        dataType: "json",
        type: 'post',
        data: 'ajax=true&method=get_mapped_product'
            + '&policy_id=' + id,
        dataType: 'json',
        success: function (response) {
            if (response['category'].length > 0) {
                $('#c_categories').multipleSelect('setSelects', response['category']);
            } else {
                $('#c_categories').multipleSelect("refresh");
                $('#c_categories').multipleSelect("uncheckAll");
                $('#c_categories').multipleSelect('setSelects', 0);
            }
            if (response['mapped_category'].length > 0) {
                $('#c_categories').parent().find('input[type=checkbox]').each(function () {
                    if (($.inArray($(this).attr('value'), response['mapped_category'])) != -1) {
                        $(this).addClass('already_mapped');
                        $(this).attr('disabled', true);
                    } else {
                        $(this).removeClass('already_mapped');
                        $(this).attr('disabled', false);
                    }
                    //$("#c_categories").multipleSelect('updateSelectAll',0);
                });
            } else {

            }
            $('#c_products').empty();
            $('#c_products').find('option').remove().end();
            if (response['category_product'].length > 0) {
                for (var i in response.category_product) {
                    $('#c_products').append($("<option/>", {
                        value: response.category_product[i]['id_product'],
                        text: response.category_product[i]['name']
                    }));
                }
                $('#c_products').multipleSelect("refresh");
                $('#c_products').multipleSelect("uncheckAll");
                if (response['product_ids'].length > 0) {
                    $('#c_products').multipleSelect('setSelects', response['product_ids']);
                }
            } else {
                $('#c_products').append($("<option/>", {
                    value: 0,
                    text: 'Select Product'
                }));
                $('#c_products').multipleSelect("refresh");
                $("#c_products").multipleSelect({ placeholder: select_pros_placeholder, filter: true });
            }
            $(".rm_customer_notes").popover();
            $('[data-toggle="tooltip"]').tooltip();
            $("#c_categories").parent().find(".ms-parent").attr("style", "width:500px !important;");
        }

    });
    $('#mapping_error').hide();
    $('#mapping_error_constant').hide();
    $('#modal_policy_product').modal({ 'show': true, 'backdrop': 'static' });
    $('#return_data_type').val(id);
}

function selectAllCategories() {
    $('.ms-drop #selectItem').each(function () {
        if ($(this).prop("disabled")) {
            $(this).prop('checked', false);
        }
    });
    //    $('#c_categories option').each(function () {
    //        if ($(this).prop("disabled")) {
    //            $(this).removeAttr("selected");
    //        }
    //    });
}
function map() {
    $('.returnmanager_success_msg').remove();
    var categories = $("#c_categories").multipleSelect("getSelects");
    var product = $("#c_products").multipleSelect("getSelects");
    $.ajax({
        url: module_path,
        type: 'post',
        dataType: 'json',
        data: 'ajax=true&method=policy_to_product_mapping'
            + '&category=' + categories
            + '&product=' + product
            + '&policy_id=' + $('#return_data_type').val(),
        beforeSend: function () {
            $('#mapping_error').html('');
            $('#rm_policy_product_mapping_form_loader').show();
        },
        success: function (response) {
            $('#rm_policy_product_mapping_form_loader').hide();
            if (response.length == 0) {
                $('#modal_policy_product').modal("hide");
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += success_mapping_policy;
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                setTimeout(function () {
                    $('.returnmanager_success_msg').remove();
                }, 5000);
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
            else {
                $('#mapping_error_constant').html(category_already_mapped + '<br>');
                for (var i in response) {
                    $('#mapping_error').append('<div>' + (i * 1 + 1) + '. ' + response[i] + '</div><br>');
                }
                $('#mapping_error').show();
                $('#mapping_error_constant').show();
            }
            $(".rm_customer_notes").popover();
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
}

function getCategoryProduct() {
    $("#c_products").multipleSelect({ placeholder: select_pros_placeholder, filter: true });
    var categories = $("#c_categories").multipleSelect("getSelects");
    $.ajax({
        url: module_path,
        type: 'POST',
        dataType: 'json',
        data: 'ajax=true&method=getCategoryProduct'
            + '&category=' + categories
            + '&policy_id=' + $('#return_data_type').val(),
        beforeSend: function () {
            $('#category_loader_cust').show();
        },
        complete: function () {
            $('#category_loader_cust').hide();
        },
        success: function (json) {
            $('#c_products').empty();
            $('#c_products').find('option').remove().end();
            if (json['category_product'].length > 0) {
                for (var i in json.category_product) {
                    $('#c_products').append($("<option/>", {
                        value: json.category_product[i]['id_product'],
                        text: json.category_product[i]['name']
                    }));
                }
                $('#c_products').multipleSelect("refresh");
                $('#c_products').multipleSelect("uncheckAll");
                if (json['mapped_products'].length > 0) {
                    var data = [];
                    for (var i in json.mapped_products) {
                        data.push(json.mapped_products[i]['id_product']);
                    }
                    if (data.length > 0) {
                        $('#c_products').multipleSelect('setSelects', data);
                    }
                }
            } else {
                $('#c_products').append($("<option/>", {
                    value: 0,
                    text: 'Select Product'
                }));
                $('#c_products').multipleSelect("refresh");
                $("#c_products").multipleSelect({ placeholder: select_pros_placeholder, filter: true });
            }
        }
    });
}
function viewInternalNotes(anchor) {
    var ret_id = $(anchor).attr('type');
    $('.rm-bigloader').show();
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=getInternalNoteData&ret=' + ret_id,
        success: function (res) {
            $('#rm_return_comment_modal').modal({ 'show': true, 'backdrop': 'static' });
            $('.rm-bigloader').hide();
            $('#rm_internal_note_complete_loader').hide();
            $('#rm_internal_notes').html(res);
        }
    });
}

function rmAddInternalNote() {
    var ret_id = $('input[name="rm_current_return_id"]').val();
    var notes = $.trim($('textarea[name="internal_note"]').val());
    if (notes.length > 0) {
        $('#rm_internal_note_complete_loader').show();
        $.ajax({
            url: module_path,
            type: 'post',
            data: 'ajax=true&method=addInternalNote&ret=' + ret_id + '&note=' + notes,
            success: function (res) {
                $('#rm_internal_note_complete_loader').hide();
                rmCloseModal('rm_return_comment_modal');
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">*</button>';
                html += internal_note_success;
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
        });
    } else {
        rmCloseModal('rm_return_comment_modal');
    }
}

//changes by vishal for adding cancel order functionality
function allowCancel(anchor) {
    var ret_lang = $(anchor).attr("type");
    /*Start Added by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer*/
    var res = ret_lang.split('_');
    var ret_id = res[0];
    var lang_id = res[1];
    /*End Added by Anshul Mittal on "26-08-2017"  to fix the issue of sent email language according to customer*/

    $('#rm_select_state').modal({ 'show': true, 'backdrop': 'static' });
    $('#rm_yes_cancel').on('click', function () {
        $('#rm_select_state').modal('hide');
        var kb_state_selected = $('#kb_order_state').val();
        $('#rm_approve_confirm_cancel').modal({ 'show': true, 'backdrop': 'static' });
        $('.returnmanager_success_msg').remove();
        /*Start Added by Anshul Mittal on "25-08-2017" to add a functionality of email editing before sending it to customer*/
        $.ajax({
            url: module_path,
            type: 'post',
            data: 'ajax=true&method=loadEmailTemplate&selected_temp=cancel_app&selected_lang=' + lang_id,
            beforeSend: function () {
                $('#rm_yes_approve_cancel').hide();
            },
            success: function (json) {
                $('#subject_email_allow_cancel').val(json['subject']);
                //$('#body_email_comp').val(json['body']);
                tinyMCE.get('body_email_allow_cancel').setContent(json['body']);
                $('#rm_yes_approve_cancel').show();
            }
        });
        /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
        $('#rm_yes_approve_cancel').bind().unbind().on('click', function () {
            /*Start Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
            var subject_email_allow = $('#subject_email_allow_cancel').val();
            // Changes by kanishka kannoujia on 16-Jume-2022 to change getContent('') from getContent()
            var body_email_allow = tinyMCE.get('body_email_allow_cancel').getContent();
            /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
            $('#rm_approve_return_popup_loader').show();
            var body_email = body_email_allow.replace(/&amp;/g, '#####@@@@@@');
            body_email = body_email.replace(/&;/g, '#####@@@@@@');
            body_email = body_email.replace(/&/g, '@@@@@@@@@@@@');
            $.ajax({
                url: module_path,
                type: 'post',
                /*Edited by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer*/
                data: 'ajax=true&method=approvecancel&ret=' + ret_id + '&subject_email_allow=' + subject_email_allow + '&body_email_allow=' + body_email + '&order_state=' + kb_state_selected,
                dataType: 'json',
                success: function (response) {
                    $('#rm_approve_return_popup_loader').hide();
                    getNextCancelListingPage($('#rm_pending_cancel_current_page').val(), 1);
                    getNextCancelListingPage($('#rm_archive_cancel_current_page').val(), 2);
                    if (response['mail_sent'])
                        var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                    else
                        var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-warning">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += success_cancel_approval;
                    if (!response['mail_sent'])
                        html += '. ' + email_not_sent;
                    html += '</div></div>';
                    $('#velsof_rm_container').before(html);
                    $('#rm_approve_confirm_cancel').modal('hide');
                    $(".rm_customer_notes").popover();
                    $('[data-toggle="tooltip"]').tooltip();
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            });
            $('#rm_yes_approve_cancel').unbind();
        });
    });
}

function denyCancel(anchor) {
    // var ret_id = $(anchor).attr('type');

    var ret_lang = $(anchor).attr("type");
    /*Start Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/

    var res = ret_lang.split('_');
    var ret_id = res[0];
    var lang_id = res[1];
    /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
    $('#rm_deny_confirm_cancel').modal({ 'show': true, 'backdrop': 'static' });
    $('.returnmanager_success_msg').remove();
    /*Start Added by Anshul Mittal on "25-08-2017" to add a functionality of email editing before sending it to customer*/
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=loadEmailTemplate&selected_temp=cancel_den&selected_lang=' + lang_id,
        beforeSend: function () {
            $('#rm_yes_deny_cancel').hide();
        },
        success: function (json) {
            $('#subject_email_deny_cancel').val(json['subject']);
            //$('#body_email_comp').val(json['body']);
            tinyMCE.get('body_email_deny_cancel').setContent(json['body']);
            $('#rm_yes_deny_cancel').show();
        }
    });
    /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
    $('#rm_yes_deny_cancel').bind().unbind().on('click', function () {
        $('#rm_deny_return_popup_loader').show();
        /*Start Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
        var subject_email_deny = $('#subject_email_deny_cancel').val();
        var body_email_deny = tinyMCE.get('body_email_deny_cancel').getContent();
        /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/

        var body_email = body_email_deny.replace(/&amp;/g, '#####@@@@@@');
        body_email = body_email.replace(/&;/g, '#####@@@@@@');
        body_email = body_email.replace(/&/g, '@@@@@@@@@@@@');

        $.ajax({
            url: module_path,
            type: 'post',
            /*Edited by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer*/
            data: 'ajax=true&method=denyCancel&ret=' + ret_id + '&subject_email_deny=' + subject_email_deny + '&body_email_deny=' + body_email,
            success: function (response) {
                $('#rm_deny_return_popup_loader').hide();
                if (response) {
                    getNextCancelListingPage($('#rm_pending_cancel_current_page').val(), 1);
                    getNextCancelListingPage($('#rm_archive_cancel_current_page').val(), 2);
                    if (response['mail_sent'])
                        var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                    else
                        var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-warning">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += success_cancel_denied;
                    if (!response['mail_sent'])
                        html += '. ' + email_not_sent;
                    html += '</div></div>';
                    $('#velsof_rm_container').before(html);
                    $('#rm_deny_confirm_cancel').modal('hide');
                    $(".rm_customer_notes").popover();
                    $('[data-toggle="tooltip"]').tooltip();
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            }
        });
        $('#rm_yes_deny_cancel').unbind();
    });
}
//changes end

function allowRequest(anchor) {
    var ret_lang = $(anchor).attr("type");
    /*Start Added by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer*/
    var res = ret_lang.split('_');
    var ret_id = res[0];
    var lang_id = res[1];
    /*End Added by Anshul Mittal on "26-08-2017"  to fix the issue of sent email language according to customer*/

    $('#rm_approve_confirm').modal({ 'show': true, 'backdrop': 'static' });
    $('.returnmanager_success_msg').remove();
    /*Start Added by Anshul Mittal on "25-08-2017" to add a functionality of email editing before sending it to customer*/
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=loadEmailTemplate&selected_temp=ret_app&selected_lang=' + lang_id,
        beforeSend: function () {
            $('#rm_yes_approve').hide();
        },
        success: function (json) {
            $('#subject_email_allow').val(json['subject']);
            //$('#body_email_comp').val(json['body']);
            tinyMCE.get('body_email_allow').setContent(json['body']);
            $('#rm_yes_approve').show();
        }
    });
    /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
    $('#rm_yes_approve').bind().unbind().on('click', function () {
        /*Start Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
        var subject_email_allow = $('#subject_email_allow').val();
        var body_email_allow = tinyMCE.get('body_email_allow').getContent();
        /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
        $('#rm_approve_return_popup_loader').show();
        var body_email = body_email_allow.replace(/&amp;/g, '#####@@@@@@');
        body_email = body_email.replace(/&;/g, '#####@@@@@@');
        body_email = body_email.replace(/&/g, '@@@@@@@@@@@@');
        $.ajax({
            url: module_path,
            type: 'post',
            /*Edited by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer*/
            data: 'ajax=true&method=approveReturn&ret=' + ret_id + '&subject_email_allow=' + subject_email_allow + '&body_email_allow=' + body_email,
            dataType: 'json',
            success: function (response) {
                $('#rm_approve_return_popup_loader').hide();
                getNextReturnsListingPage($('#rm_pending_returns_current_page').val(), 1);
                getNextReturnsListingPage($('#rm_active_returns_current_page').val(), 2);
                if (response['mail_sent'])
                    var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                else
                    var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-warning">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += success_return_approval;
                if (!response['mail_sent'])
                    html += '. ' + email_not_sent;
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                $('#rm_approve_confirm').modal('hide');
                $(".rm_customer_notes").popover();
                $('[data-toggle="tooltip"]').tooltip();
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
        });
        $('#rm_yes_approve').unbind();
    });
}

function denyRequest(anchor) {
    // var ret_id = $(anchor).attr('type');

    var ret_lang = $(anchor).attr("type");
    /*Start Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/

    var res = ret_lang.split('_');
    var ret_id = res[0];
    var lang_id = res[1];
    /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
    $('#rm_deny_confirm').modal({ 'show': true, 'backdrop': 'static' });
    $('.returnmanager_success_msg').remove();
    /*Start Added by Anshul Mittal on "25-08-2017" to add a functionality of email editing before sending it to customer*/
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=loadEmailTemplate&selected_temp=ret_den&selected_lang=' + lang_id,
        beforeSend: function () {
            $('#rm_yes_deny').hide();
        },
        success: function (json) {
            $('#subject_email_deny').val(json['subject']);
            //$('#body_email_comp').val(json['body']);
            tinyMCE.get('body_email_deny').setContent(json['body']);
            $('#rm_yes_deny').show();
        }
    });
    /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
    $('#rm_yes_deny').bind().unbind().on('click', function () {
        $('#rm_deny_return_popup_loader').show();
        /*Start Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
        var subject_email_deny = $('#subject_email_deny').val();
        var body_email_deny = tinyMCE.get('body_email_deny').getContent();
        /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/

        var body_email = body_email_deny.replace(/&amp;/g, '#####@@@@@@');
        body_email = body_email.replace(/&;/g, '#####@@@@@@');
        body_email = body_email.replace(/&/g, '@@@@@@@@@@@@');

        $.ajax({
            url: module_path,
            type: 'post',
            /*Edited by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer*/
            data: 'ajax=true&method=denyReturn&ret=' + ret_id + '&subject_email_deny=' + subject_email_deny + '&body_email_deny=' + body_email,
            success: function (response) {
                $('#rm_deny_return_popup_loader').hide();
                if (response) {
                    getNextReturnsListingPage($('#rm_pending_returns_current_page').val(), 1);
                    getNextReturnsListingPage($('#rm_active_returns_current_page').val(), 2);
                    if (response['mail_sent'])
                        var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                    else
                        var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-warning">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += success_return_denied;
                    if (!response['mail_sent'])
                        html += '. ' + email_not_sent;
                    html += '</div></div>';
                    $('#velsof_rm_container').before(html);
                    $('#rm_deny_confirm').modal('hide');
                    $(".rm_customer_notes").popover();
                    $('[data-toggle="tooltip"]').tooltip();
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            }
        });
        $('#rm_yes_deny').unbind();
    });
}
$(".rm_customer_notes").popover();
$('[data-toggle="tooltip"]').tooltip();
function changeReturnStatus(anchor) {
    $('#rm_change_status_return_id').attr('value', $(anchor).attr('type'));
    /*Start Added by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer */
    var ret_lang = $('#rm_change_status_return_id').val();
    var res = ret_lang.split('_');
    var rm_ret_id = res[0];
    var lang_id = res[1];
    /*End Added by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer */
    var rm_status_id = $('#rm_active_curr_status_' + rm_ret_id).val();
    /*Start Added by Anshul Mittal on "25-08-2017" to add a functionality of email editing before sending it to customer*/
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=loadEmailTemplate&selected_temp=ret_stat&selected_lang=' + lang_id,
        beforeSend: function () {
            $('#rm_upd_status').hide();
        },
        success: function (json) {
            $('#subject_email_status').val(json['subject']);
            //$('#body_email_comp').val(json['body']);
            tinyMCE.get('body_email_status').setContent(json['body']);
            $('#rm_upd_status').show();
        }
    });
    /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
    $('#rm_change_return_status option').removeAttr('selected');
    $('#rm_change_return_status option').each(function () {
        if ($(this).attr('value') == rm_status_id) {
            $(this).attr('selected', 'selected');
        }
    });
    $('#rm_change_status_modal').modal({ 'show': true, 'backdrop': 'static' });
}

function rmUpdateStatus() {
    var rm_status_id = $('#rm_change_return_status').val();
    $('.returnmanager_success_msg').remove();
    $('#rm_active_curr_status_' + $('#rm_change_status_return_id').val()).val(rm_status_id);
    $('#rm_return_status_change_loader').show();
    /*Start Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
    var subject_email_status = $('#subject_email_status').val();
    var body_email_status = tinyMCE.get('body_email_status').getContent();

    var body_email = body_email_status.replace(/&amp;/g, '#####@@@@@@');
    body_email = body_email.replace(/&;/g, '#####@@@@@@');
    body_email = body_email.replace(/&/g, '@@@@@@@@@@@@');
    /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
    $.ajax({
        url: module_path,
        type: 'post',
        /*Edited by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer*/
        data: 'ajax=true&method=changeReturnStatus&stat=' + rm_status_id + '&ret=' + $('#rm_change_status_return_id').val() + '&subject_email_status=' + subject_email_status + '&body_email_status=' + body_email,
        dataType: 'json',
        success: function (response) {
            $('#rm_return_status_change_loader').hide();
            $('#rm_pending_returns_' + $('#rm_change_status_return_id').val() + ' .rm_pending_return_status_col').html(response['value']);
            $('#rm_change_status_modal').modal('hide');
            if (response['mail_sent'])
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
            else
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-warning">';
            html += '<button type="button" class="close" data-dismiss="alert">×</button>';
            html += success_return_status_changed;
            if (!response['mail_sent'])
                html += '. ' + email_not_sent;
            html += '</div></div>';
            $('#velsof_rm_container').before(html);
            $("html, body").animate({ scrollTop: 0 }, "slow");
            getNextReturnsListingPage($('#rm_active_returns_current_page').val(), 2);
        }
    });
}

function rmCloseModal(id) {
    $('#' + id).modal('hide');
}

function viewReturnDetail(anchor) {
    var ret_id = $(anchor).attr('type');
    $('#rm_return_history_modal').modal({ 'show': true, 'backdrop': 'static' });
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=getReturnData&ret=' + ret_id,
        success: function (res) {
            $('#rm_return_history').html(res);
        }
    });
}
function viewInternalNotes(anchor) {
    var ret_id = $(anchor).attr('type');
    $('.rm-bigloader').show();
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=getInternalNoteData&ret=' + ret_id,
        success: function (res) {
            $('#rm_return_comment_modal').modal({ 'show': true, 'backdrop': 'static' });
            $('.rm-bigloader').hide();
            $('#rm_internal_note_complete_loader').hide();
            $('#rm_internal_notes').html(res);
        }
    });
}

function rmAddInternalNote() {
    var ret_id = $('input[name="rm_current_return_id"]').val();
    var notes = $.trim($('textarea[name="internal_note"]').val());
    if (notes.length > 0) {
        $('#rm_internal_note_complete_loader').show();
        $.ajax({
            url: module_path,
            type: 'post',
            data: 'ajax=true&method=addInternalNote&ret=' + ret_id + '&note=' + notes,
            success: function (res) {
                $('#rm_internal_note_complete_loader').hide();
                rmCloseModal('rm_return_comment_modal');
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">*</button>';
                html += internal_note_success;
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
        });
    } else {
        rmCloseModal('rm_return_comment_modal');
    }
}

function completeReturn(anchor) {
    //var ret_id = $(anchor).attr('type');
    /*Start Added by Anshul Mittal on "26-08-2017" to fix the issue of sent email language according to customer*/
    var ret_lang = $(anchor).attr("type");
    var res = ret_lang.split('_');
    var ret_id = res[0];
    var lang_id = res[1];
    // changes by rishabh jain for coupon code generate functionality
    var is_refund_type = parseInt($(anchor).attr("refund"));
    if (is_refund_type == 1) {
        $('#rm_generate_coupon').modal({ 'show': true, 'backdrop': 'static' });
        $('#rm_yes_generate').on('click', function () {
            $('#rm_generate_coupon').modal('hide');
            if ($('input[name="generate_coupon"]').is(':checked') == true) {
                is_generate_coupon = 1;
            } else {
                is_generate_coupon = 0;
            }
            if ($('input[name="update_inventory"]').is(':checked') == true) {
                is_update_inventory = 1;
            } else {
                is_update_inventory = 0;
            }
            //            is_generate_coupon = $('input[name="generate_coupon"]').is(':checked');
            //            is_update_inventory = $('input[name="update_inventory"]').is(':checked');

            $('#rm_complete_confirm').modal({ 'show': true, 'backdrop': 'static' });
            $('.returnmanager_success_msg').remove();

            var params = '';
            if (is_generate_coupon == 1) {
                params += 'selected_temp=ret_comp_discount';
            } else {
                params += 'selected_temp=ret_comp';
            }
            /*Start Added by Anshul Mittal on "25-08-2017" to add a functionality of email editing before sending it to customer*/
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=loadEmailTemplate&' + params + '&selected_lang=' + lang_id,
                beforeSend: function () {
                    $('#rm_yes_confirm').hide();
                },
                success: function (json) {
                    $('#subject_email_comp').val(json['subject']);
                    //$('#body_email_comp').val(json['body']);
                    tinyMCE.get('body_email_comp').setContent(json['body']);
                    $('#rm_yes_confirm').show();
                }
            });
            /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
            $('#rm_yes_confirm').bind().unbind().on('click', function () {
                /*Start Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
                var subject_email_comp = $('#subject_email_comp').val();
                var body_email_comp = tinyMCE.get('body_email_comp').getContent();
                /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/

                var body_email = body_email_comp.replace(/&amp;/g, '#####@@@@@@');
                body_email = body_email.replace(/&;/g, '#####@@@@@@');
                body_email = body_email.replace(/&/g, '@@@@@@@@@@@@');

                $('#rm_return_complete_loader').show();
                $.ajax({
                    url: module_path,
                    type: 'post',
                    /*Edited by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer*/
                    data: 'ajax=true&method=completeReturn&ret=' + ret_id + '&subject_email_comp=' + subject_email_comp + '&body_email_comp=' + body_email + '&is_generate_coupon=' + is_generate_coupon + '&is_update_inventory=' + is_update_inventory,
                    success: function (response) {
                        if (response) {
                            $('#rm_return_complete_loader').hide();
                            getNextReturnsListingPage($('#rm_active_returns_current_page').val(), 2);
                            getArchives();
                            $('#rm_complete_confirm').modal('hide');
                            if (response['mail_sent'])
                                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                            else
                                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-warning">';
                            html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                            html += success_return_completed;
                            if (!response['mail_sent'])
                                html += '. ' + email_not_sent;
                            html += '</div></div>';
                            $('#velsof_rm_container').before(html);
                            $("html, body").animate({ scrollTop: 0 }, "slow");
                        }
                    }
                });
                $('#rm_yes_confirm').unbind();
            });
        });
    } else {

        /* Start changes done by Vishal on 28th August 2019 : to add Update inventory functionality on credit and replacement return */

        $('#rm_update_inventory').modal({ 'show': true, 'backdrop': 'static' });
        $('#rm_yes_generate_update').on('click', function () {
            $('#rm_update_inventory').modal('hide');
            if ($('input[name="update_inventory_1"]').is(':checked') == true) {
                is_update_inventory = 1;
            } else {
                is_update_inventory = 0;
            }

            /* End changes done by Vishal on 28th August 2019 : to add Update inventory functionality on credit and replacement return */

            // changes over
            /*End Added by Anshul Mittal on "26-08-2017"  to fix the issue of sent email language according to customer*/
            $('#rm_complete_confirm').modal({ 'show': true, 'backdrop': 'static' });
            $('.returnmanager_success_msg').remove();
            /*Start Added by Anshul Mittal on "25-08-2017" to add a functionality of email editing before sending it to customer*/
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=loadEmailTemplate&selected_temp=ret_comp&selected_lang=' + lang_id,
                beforeSend: function () {
                    $('#rm_yes_confirm').hide();
                },
                success: function (json) {
                    $('#subject_email_comp').val(json['subject']);
                    //$('#body_email_comp').val(json['body']);
                    tinyMCE.get('body_email_comp').setContent(json['body']);
                    $('#rm_yes_confirm').show();
                }
            });
            /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
            $('#rm_yes_confirm').bind().unbind().on('click', function () {
                /*Start Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/
                var subject_email_comp = $('#subject_email_comp').val();
                var body_email_comp = tinyMCE.get('body_email_comp').getContent();
                /*End Added by Anshul Mittal on "25-08-2017"  to add a functionality of email editing before sending it to customer*/

                var body_email = body_email_comp.replace(/&amp;/g, '#####@@@@@@');
                body_email = body_email.replace(/&;/g, '#####@@@@@@');
                body_email = body_email.replace(/&/g, '@@@@@@@@@@@@');

                $('#rm_return_complete_loader').show();
                $.ajax({
                    url: module_path,
                    type: 'post',
                    /*Edited by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer*/
                    data: 'ajax=true&method=completeReturn&ret=' + ret_id + '&subject_email_comp=' + subject_email_comp + '&body_email_comp=' + body_email + '&is_update_inventory=' + is_update_inventory,
                    success: function (response) {
                        if (response) {
                            $('#rm_return_complete_loader').hide();
                            getNextReturnsListingPage($('#rm_active_returns_current_page').val(), 2);
                            getArchives();
                            $('#rm_complete_confirm').modal('hide');
                            if (response['mail_sent'])
                                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                            else
                                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-warning">';
                            html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                            html += success_return_completed;
                            if (!response['mail_sent'])
                                html += '. ' + email_not_sent;
                            html += '</div></div>';
                            $('#velsof_rm_container').before(html);
                            $("html, body").animate({ scrollTop: 0 }, "slow");
                        }
                    }
                });
                $('#rm_yes_confirm').unbind();
            });
        });
    }
}

function getArchives() {
    var date_from = $('#rm_from_date').val();
    var date_to = $("#rm_to_date").val();
    var from = new Date(date_from);
    var to = new Date(date_to);
    var return_id = $("#rm_custom_return_id").val();
    var customer_name = $("#rm_customer_name").val();
    var product_name = $("#rm_product_name").val();
    var order_id = $("#rm_order_id").val();
    var status_id = $('select[name="rm_archive_return_status"]').val();
    var order_by = $('select[name="rm_archive_sortby"]').val();
    var order_dir = $('select[name="rm_archive_sortdir"]').val();
    var status = "";
    if (from > to) {
        $("#rm_date_error").html(rm_date_error);
        status = "false";
    }
    else {
        $("#rm_date_error").html('');
        status = "true";
    }

    if (status == "true") {
        var params = '&from_date=' + date_from
            + '&to_date=' + date_to + '&return_id=' + return_id + '&customer_name=' + customer_name + '&product_name=' + product_name + '&order_id=' + order_id + '&status_id=' + status_id + '&order_by=' + order_by + '&order_dir=' + order_dir;
        $.ajax({
            url: module_path,
            type: 'POST',
            data: '&ajax=true&method=getArchives' + params,
            dataType: 'json',
            beforeSend: function () {
                $('#rm_loader').show();
            },
            success: function (json) {
                var ship_pay = '';
                if (json['flag']) {
                    var row_class = '';
                    var tab_html = '<div class="rm-bigloader"></div><table class="pure-table"><thead><tr style="background-color:#f2f2f2">';
                    // changes by rishabh jain
                    tab_html += '<th style="width: 7%;">' + rm_return_id_text + '</th><th style="width: 7%;">' + rm_order_text + '</th><th style="width: 12%;">' + rm_customer_text + '</th><th style="width: 14%;">' + rm_product_text + '</th><th style="width: 8%;">' + rm_price_text + '</th><th style="width: 5%;">' + rm_qty_text + '</th><th style="width: 14%;">' + rm_shipping_text + '</th><th style="width: 9%;">' + rm_type_text + '</th><th style="width: 12%;">' + rm_action_text + '</th></tr></thead>';
                    tab_html += '<tbody id="rm_archive_list_tbody">';
                    for (var i in json['data']) {
                        if (i % 2 == 0)
                            row_class = 'even';
                        else
                            row_class = 'odd';
                        if (json['data'][i]['whopayshipping'] == 'c')
                            ship_pay = rm_customer_text;
                        else
                            ship_pay = rm_so_text;
                        var product_attr = '';
                        if (typeof json['data'][i]['product_attr'] != 'undefined')
                            product_attr = json['data'][i]['product_attr'];
                        else
                            product_attr = '<br>';
                        if (isEmpty(product_attr)) {
                            product_attr = '<br>';
                        }

                        if (json['data'][i]['comment'] != '')
                            var return_comment = json['data'][i]['comment'];
                        else
                            var return_comment = no_comments_text;
                        //					var return_comment = "<span class='vss_italic_text'>"+no_comments_text+"</span>";

                        tab_html += '<tr class="pure-table-' + row_class + '">';
                        // changes by rishabh jain to add return id column
                        tab_html += '<td>' + json['data'][i]['return_id'] + '</td>';
                        // changes over
                        tab_html += '<td><a href="' + order_controller + '&id_order=' + json['data'][i]['id_order'] + '&vieworder" target="_blank">' + json['data'][i]['order_reference'] + '</a></td>';
                        tab_html += '<td><a href="' + customer_controller + '&id_customer=' + json['data'][i]['customer_id'] + '&viewcustomer" target="_blank">' + json['data'][i]['cust_name'] + '</a></td>';
                        tab_html += '<td><b><a href="' + json['data'][i]['product_link'] + '" target="_blank">' + json['data'][i]['product_name'] + '</a></b><br>' + product_attr + '</td>';
                        tab_html += '<td>' + json['data'][i]['unit_price_tax_incl'] + '</td>';
                        tab_html += '<td>' + json['data'][i]['quantity'] + '</td>';
                        tab_html += '<td>' + ship_pay + '</td>';
                        tab_html += '<td>' + json['data'][i]['return_type'] + '</td>';
                        tab_html += '<td class="rm_velsof_action"><a data-container="body" style="cursor: pointer;" data-toggle="popover" data-placement="left" data-content="' + json['data'][i]['reason'] + '" class="velsof-glyphicons glyphicons circle_question_mark rm_customer_notes" title="' + rm_reason_text + '"><i></i></a>';
                        tab_html += '<a data-container="body" data-toggle="popover" style="cursor: pointer;" data-placement="left" data-content="' + return_comment + '" class="velsof-glyphicons glyphicons notes_2 rm_customer_notes" title="' + rm_comment_text + '"><i></i></a>';
                        if (json['data'][i]['image_path'] != '') {
                            tab_html += '<a type="' + json['data'][i]['return_id'] + '" style="cursor: pointer;" style="cursor: pointer;" href="' + json['data'][i]['image_path'] + '" target="_blank" onclick="" class="velsof-glyphicons glyphicons file" title="' + rm_view_image_text + '"><i></i></a>';
                        }
                        // changes by rishabh jain for internal note
                        tab_html += '<a type="' + json['data'][i]['return_id'] + '_' + json['data'][i]['id_lang'] + '" style="cursor: pointer;" style="cursor: pointer;" onclick="viewInternalNotes(this)" class="velsof-glyphicons glyphicons comments" title="' + rm_view_internal_note_text + '"><i></i></a>';
                        // changes over
                        tab_html += '</td></tr>';
                    }
                    tab_html += '</tbody></table>';
                }
                else {
                    var tab_html = '<div class="rm_no_data"><span>' + rm_no_data_label + '</span></div>';
                }
                $('#rm_archive_list').html(tab_html);
                $('#rm_list_container .paginator-block').html(json['pagination']);
                $('#rm_loader').hide();
                $(".rm_customer_notes").popover();
            }
        });
    }
}


function resetArchives(onload) {
    var today = new Date();
    var last_month = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }
    today = mm + '/' + dd + '/' + yyyy;
    dd = last_month.getDate();
    mm = last_month.getMonth() + 1;
    yyyy = last_month.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }
    last_month = mm + '/' + dd + '/' + yyyy;
    $("#rm_from_date").val(last_month);
    $("#rm_to_date").val(today);
    $("#rm_custom_return_id").val('');
    $("#rm_customer_name").val('');
    $("#rm_product_name").val('');
    $("#rm_order_id").val('');
    $('select[name="rm_archive_sortby"]').val('od.date_update');
    $('select[name="rm_archive_sortdir"]').val('desc');
    if (onload != 0)
        getArchives();
}

function getArchivesExcel() {
    var date_from = $('#rm_from_date').val();
    var date_to = $("#rm_to_date").val();
    var from = new Date(date_from);
    var to = new Date(date_to);
    var return_id = $("#rm_custom_return_id").val();
    var customer_name = $("#rm_customer_name").val();
    var product_name = $("#rm_product_name").val();
    var order_id = $("#rm_order_id").val();
    var status_id = $('select[name="rm_archive_return_status"]').val();
    var order_by = $('select[name="rm_archive_sortby"]').val();
    var order_dir = $('select[name="rm_archive_sortdir"]').val();
    var status = "";
    if (from > to) {
        $("#rm_date_error").html(rm_date_error);
        status = "false";
    }
    else {
        $("#rm_date_error").html('');
        status = "true";
    }

    if (status == "true") {
        var params = '&from_date=' + date_from
            + '&to_date=' + date_to + '&return_id=' + return_id + '&customer_name=' + customer_name + '&product_name=' + product_name + '&order_id=' + order_id + '&status_id=' + status_id + '&order_by=' + order_by + '&order_dir=' + order_dir;
        $.ajax({
            url: module_path,
            type: 'POST',
            data: '&ajax=true&method=writeArchiveExcel' + params,
            beforeSend: function () {
                $('#rm_loader').show();
            },
            success: function (res) {
                if (res != 1) {
                    $("#rm_date_error").html('');
                    window.location.href = pat + res;
                }
                else {
                    $("#rm_date_error").html(rm_permission_err);
                }
                $('#rm_loader').hide();
            }
        });
    }
}


/*
 * jQuery .live() removed since 1.9 — use delegated .on() for PS admin jQuery 3.x.
 * 21-07-2026
 */
$(document).on('click', '#rm_find_another_order_btn', function () {
    $("#rm_single_order_detail_container").hide();
    $("#rm_single_order_detail_container").html('');
    $("#rm_reference_id").val('');
    $("#rm_customer_email").val('');
    $('body').animate({
        scrollTop: 150
    },
        550);
});
function getReturnForm(id_info, e) {
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=getRequestForm&id_info=' + id_info,
        dataType: 'json',
        beforeSend: function () {
            $('#kb_rm_pop_up').html('');
            $(e).parent().append('<img src="' + pat + 'returnmanager/views/img/loader_small.gif" />');
        },
        complete: function () {
            $(e).parent().find('img').remove();
        },
        success: function (response) {
            if (response['detail_found']) {
                $('#kb_rm_pop_up').html(response['template']);
                $('#kb_rm_pop_up #rm_fade').show();
                $('#kb_rm_pop_up #rm_return_form_popup').show();
                $('input[name="kb_Product_specific_products"]').autocomplete(path_fold, {
                    delay: 10,
                    minChars: 3,
                    autoFill: true,
                    max: 20,
                    matchContains: true,
                    mustMatch: true,
                    scroll: false,
                    cacheLength: 0,
                    // param multipleSeparator:'||' ajoutÃ© Ã  cause de bug dans lib autocomplete
                    multipleSeparator: '||',
                    formatItem: function (item) {
                        return item[1] + ' - ' + item[0];
                    },
                    extraParams: {
                        productIds: function () {
                            var selected_pro = $('input[name="kb_Product_specific_product_items').val();
                            if (typeof selected_pro != 'undefined') {
                                return selected_pro.replace(/\-/g, ',');
                            }
                        },
                        excludeVirtuals: 0,
                        exclude_packs: 0
                    }
                }).result(function (event, item, formatted) {
                    addProductToMappedproductpage(item);
                    event.stopPropagation();
                });
            } else {
                alert(orderedProductNotFound);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(rm_ajax_failed + ': ' + textStatus);
        }
    });
    return false;
}

$(document).ready(function () {
    // changes started
    $('#returnmanager_configuration_form table tr td').each(function () {
        var data = $(this).find('.icon-question-sign').prop('attributes');
        $(this).find('.icon-question-sign').remove();
        var this_cus = this;
        for (let ab in data) {
            if (data[ab].name == 'data-toggle' || data[ab].name == 'data-placement' || data[ab].name == 'data-original-title') {
                $(this_cus).find('span').attr(data[ab].name, data[ab].value);
            }
        }
        $(this_cus).find('span').tooltip();
    });
    // chages over

    $('.modal-backdrop').hide();
    $('#kb_rm_pop_up').on('click', '#rm_pop_up_close_btn', function () {
        $('#kb_rm_pop_up').html('');
    });

    $('#kb_rm_pop_up').on('click', '.rm_popup_close_icon', function () {
        $('#kb_rm_pop_up').html('');
    });

    //To display and hide toc block
    $('#kb_rm_pop_up').on('click', '#rm_display_toc', function () {
        if ($('#rm_toc_block').is(':visible')) {
            $('#rm_toc_block').hide();
        } else {
            $('#rm_toc_block').show();
        }
    });

    /* Start changes done by Vishal on 28th August 2019 : to add refresh button functionality  */

    $('#refresh_active').click(function () {
        getNextReturnsListingPage($('#rm_active_returns_current_page').val(), 2);
    })
    $('#refresh_pending').click(function () {

        getNextReturnsListingPage($('#rm_pending_returns_current_page').val(), 1);
    });

    /* End changes done by Vishal on 28th August 2019 : to add refresh button functionality  */

    //changes by vishal for adding cancel order functionality
    $('#refresh_cancel').click(function () {
        getNextCancelListingPage($('#rm_pending_cancel_current_page').val(), 1);
    });
    //changes end

    //changes by vishal on 28 dec 2020 for adding delete all mapped categry button
    $('#delete_category_map').click(function () {
        $.ajax({
            url: module_path,
            data: '&ajax=true&method=delete_all_category_mapping',
            type: 'post',
            datatype: 'json',
            beforeSend: function () {
                $('#delete_category_map').prop("disabled", true);
                $('#rm_mapping_loader').show();
            },
            success: function (json) {
                var html = '<div class="bootstrap returnmanager_success_msg"><div class="alert alert-success">';
                html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                html += success_delete_mapping_categories;
                html += '</div></div>';
                $('#velsof_rm_container').before(html);
                setTimeout(function () {
                    $('.returnmanager_success_msg').remove();
                }, 5000);
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#delete_category_map').prop("disabled", false);
                $('#rm_mapping_loader').hide();
            },
        });
    });
    //changes end

});

function rmSubmitReturnRequest(e) {
    var error = false;
    $('#rm_popup_request_form span.rm_error').remove();
    if ($('#rm_popup_request_form select[name="rm_return_type"]').length && $('#rm_popup_request_form select[name="rm_return_type"]').val() == 0) {
        error = true;
        $('#rm_popup_request_form select[name="rm_return_type"]').parent().append('<span class="rm_error">' + rm_return_type_required + '</span>');
    }

    if ($('#rm_popup_request_form select[name="rm_return_reason"]').length && $('#rm_popup_request_form select[name="rm_return_reason"]').val() == 0) {
        error = true;
        $('#rm_popup_request_form select[name="rm_return_reason"]').parent().append('<span class="rm_error">' + rm_reason_required + '</span>');
    }

    if (!error) {
        if ($('#rm_popup_request_form input[name="rm_agree_toc"]').length && !$('#rm_popup_request_form input[name="rm_agree_toc"]').is(':checked')) {
            error = true;
            var terms_error = rm_toc_checked;
            terms_error = terms_error.replace('&amp;', '&');
            alert(terms_error);
        }
    }


    if (!error) {
        var myFormData = new FormData();
        if ($('input[type=file]').length > 0) {
            myFormData.append('image', $('input[type=file]')[0].files[0]);
        }
        myFormData.append('ajax', 'true');
        myFormData.append('method', 'submitReturnRequest');
        var other_data = $('#rm_return_form_popup input, #rm_return_form_popup select, #rm_return_form_popup textarea').serializeArray();
        $.each(other_data, function (key, input) {
            myFormData.append(input.name, input.value);
        });
        $.ajax({
            url: module_path,
            type: 'post',
            processData: false, // important
            contentType: false, // important
            //            data: 'ajax=true&method=submitReturnRequest&'+$('#rm_return_form_popup input, #rm_return_form_popup select, #rm_return_form_popup textarea').serialize(),
            data: myFormData,
            dataType: 'json',
            beforeSend: function () {
                //$(e).attr('disabled', true);
                $(e).parent().append('<img src="' + pat + 'returnmanager/views/img/loader_small.gif" />');
            },
            complete: function () {
                $(e).parent().find('img').remove();
            },
            success: function (response) {
                if (response.hasOwnProperty('custom_fields_errors')) {
                    $(".errorsmall_custom").hide();
                    $.each(response.custom_fields_errors.error, function (key, data) {
                        $("#error_" + key).html(data);
                        $("#error_" + key).show();
                        $("#error_" + key).parent().parent().css("border-color", "#FF0000");
                    });
                } else {
                    getNextReturnsListingPage($('#rm_pending_returns_current_page').val(), 1);
                    $('#kb_rm_pop_up').html(response['template']);
                    $('#kb_rm_pop_up #rm_fade').show();
                    $(".rm_customer_notes").popover();
                    $('[data-toggle="tooltip"]').tooltip();
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(rm_ajax_failed + ': ' + textStatus);
            }
        });
    }
    return false;
}


/*
 * jQuery .live() removed since 1.9 — use delegated .on() for PS admin jQuery 3.x.
 * 21-07-2026
 */
$(document).on('click', '.rm_popup_close_icon', function () {
    $.ajax({
        url: module_path,
        type: 'post',
        data: 'ajax=true&method=getOrder&' + $('#returnmanager_order_form input').serialize(),
        dataType: 'json',
        success: function (response) {
            $("#rm_single_order_detail_container").html(response['template']);
            $("#rm_single_order_detail_container").show();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert(rm_ajax_failed + ': ' + textStatus);
        }
    });
});
//function displayReturnNote(e) {
//    $(e).parent().find('span.rm_error').remove();
//    var val = $(e).val();
//    $('#rm_return_type_note p').hide();
//    if (val != 0) {
//        $('#rm_return_type_note p#rm_return_type_note_' + val).show();
//    }
//}

function displayReasonNote(e) {
    $(e).parent().find('span.rm_error').remove();
    var val = $(e).val();
    $('#rm_reason_type_note p').hide();
    if (val != 0) {
        $('#rm_reason_type_note p#rm_reason_type_note_' + val).show();
    }
}
/* changes added by rishabh */
function displayReturnAddress() {

    var add = $('#rm_return_address').val();
    if (add == 0) {
        $('#rm_popup_address').html($('#default_addr').val());
    } else {
        var display_addr = $('#full_addr_' + add).val();
        $('#rm_popup_address').html(display_addr);

    }
}
/* changes end */
function refreshDefaultPolicy(data, policy_id) {
    if (data && data != '') {
        var html = '<select name="velsof_return[policy][default]"><option value="0">' + no_policy_txt + '</option>';
        for (var i in data) {
            if (data[i]['return_data_id'] == policy_id)
                html += '<option value="' + data[i]['return_data_id'] + '" selected="selected">' + data[i]['value'] + '</option>';
            else
                html += '<option value="' + data[i]['return_data_id'] + '" >' + data[i]['value'] + '</option>';
        }
        $("#default_policy").show();
        $("#default_policy_select").html(html);
    }
    else {
        $("#default_policy").hide();
    }
}

function validateExceptionalId(s) {
    var reg = /^\d+(,\d+)*$/;
    return reg.test(s);
}


function CheckFileType(data) {
    $('#rm_popup_request_form input[name="rm_return_image"]').closest('.rm_form_control_block').find('span.rm_error').remove();
    if ($(data).prop('files').length > 0) {
        var extension_arr = ['gif', 'jpg', 'png', 'jpeg', 'zip'];
        var file_ext = $(data).val().trim().substring($(data).val().trim().lastIndexOf('.') + 1).toLowerCase();
        if ($.inArray(file_ext, extension_arr) == -1) {
            $(data).val('');
            $('#rm_popup_request_form input[name="rm_return_image"]').closest('.rm_form_control_block').append('<span class="rm_error">' + file_type_error + '</span>');
        } else if (parseFloat($(data)[0].files[0].size / 1024).toFixed(0) > 4096) {
            $(data).val('');
            $('#rm_popup_request_form input[name="rm_return_image"]').closest('.rm_form_control_block').append('<span class="rm_error">' + image_size_error + '</span>');
        }
    }
}

function isEmpty(value) {
    return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
}

function getReturnmanagerPendingCustomFeildDetail(element) {
    $.ajax({
        type: "POST",
        url: module_path,
        data: 'ajax=true&method=getReturnmanagerCustomFeildDetail'
            + '&rm_order_id=' + element,
        dataType: 'json',
        beforeSend: function () {
        },
        success: function (json) {
            $('#modal_pending_custom_field_data .modal-body').html(json['html']);
            $('#modal_pending_custom_field_data').modal({ 'show': true, 'backdrop': 'static' });
        }
    });
}

function getReturnmanagerActiveCustomFeildDetail(element) {
    $.ajax({
        type: "POST",
        url: module_path,
        data: 'ajax=true&method=getReturnmanagerCustomFeildDetail'
            + '&rm_order_id=' + element,
        dataType: 'json',
        beforeSend: function () {
        },
        success: function (json) {
            $('#modal_active_custom_field_data .modal-body').html(json['html']);
            $('#modal_active_custom_field_data').modal({ 'show': true, 'backdrop': 'static' });
        }
    });
}

function getReturnmanagerCancelCustomFeildDetail(element) {
    $.ajax({
        type: "POST",
        url: module_path,
        data: 'ajax=true&method=getReturnmanagerCustomFeildDetail'
            + '&rm_order_id=' + element,
        dataType: 'json',
        beforeSend: function () {
        },
        success: function (json) {
            $('#modal_cancelled_custom_field_data .modal-body').html(json['html']);
            $('#modal_cancelled_custom_field_data').modal({ 'show': true, 'backdrop': 'static' });
        }
    });
}
             /**
             * To show the upgrade modal box
             * @date 20-05-2024
             * @author Ravi Kant Gupta
             */
$(document).ready(function() {

    // Code to handle the toggle and show the upgrade modal box
    $('#return_enable_header_menu, #enable_product_selection_replacement, #return_enable_image_upload, #return_enable_chat, #return_enable_cancel_return, #return_enable_cancel, #return_credit, #return_replacement, #new_return_policy, #edit_return_policy, #delete_return_policy, #return_custom_field,#return_enable_address').change(function(event) {
        event.preventDefault();
        showUpgradeModal();
    });

    // Code to handle the toggle and show the upgrade modal box
    $('#new_return_policy, #edit_return_policy, #delete_return_policy, #find_order, #edit_return_reason, #delete_return_reason, #edit_cancel_reasons, #add_return_reason, #delete_cancel_reasons,#cancel_reset, #cancel_filter, #order_cancel_reset, #order_cancel_filter, #reset_pending_return_list, #filter_pending_return_list, #filter_cancelled_return_list, #reset_cancelled_return_list, #return_status_add, #return_status_delete, #return_status_edit, #add_cancel_reason, #filter_active_return, #reset_active_return, #export_complete_cancel, #reset_complete_cancel, #filter_complete_cancel, #export_archives, #reset_archives, #filter_archives, #custom_field,#add_new_address4, #add_new_address3, #add_new_address2, #add_new_address1').click(function(event) {
        event.preventDefault();
        showUpgradeModal();
    });

    $('#custom_js, #custom_css').on('input', function(event) {
        event.preventDefault();
        showUpgradeModal();
    });

    // Define the function to showUpgradeModal 
    window.showUpgradeModal = function() {
        $('#kbUpgradeModal').css('display', 'block');
    }

    // Function to close the modal when the close button is clicked
    $('#kbUpgradeModal .close').click(function() {
        $('#kbUpgradeModal').css('display', 'none');
    });

    // Close the modal when clicking outside of it
    $(window).click(function(event) {
        if ($(event.target).is('.kb_upgrade_modal')) {
        $('#kbUpgradeModal').css('display', 'none');
        }
    });
    
});


