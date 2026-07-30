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
$(document).ready(function () {
    $.fn.modal.Constructor.prototype.enforceFocus = function () { };

    /**
     * Start Changes to fix the additional jquery issue added in TPL order_detail_content.tpl
     * NASep2023 accordion
     * @date 18-09-2023
     * @modifier Ashish Kumar
     * @commentor Nikhil Aggarwal
     */
    // $('.head-checkbox input[type="checkbox"]').click(function () {
    //     if ($(this).prop("checked") == true) {
    //         $(this).closest('.rm_single_order_row').find('input:checkbox').prop('checked', true);
    //     }
    //     else if ($(this).prop("checked") == false) {
    //         $(this).closest('.rm_single_order_row').find('input:checkbox').prop('checked', false);
    //     }
    // });
    $('body').on("click", '.head-checkbox input[type="checkbox"]', function () {
        if ($(this).prop("checked") == true) {
            $(this).closest('.rm_single_order_row').find('input:checkbox').prop('checked', true);
        }
        else if ($(this).prop("checked") == false) {
            $(this).closest('.rm_single_order_row').find('input:checkbox').prop('checked', false);
        }
    });
    // Changes end by Ashish Sir
    
    //$('#rm_return_history_block').find('tbody').find('tr').find('p').find('a').css('margin-top','15px');  
    $('#rm_single_order_detail_container').on('click', '#rm_find_another_order_btn', function () {
        $("#rm_single_order_detail_container").hide();
        $("#rm_single_order_detail_container").html('');
        $("#rm_find_order_form input").val('');
        $("#rm_find_order_form").show();
    });
    $(".rm_clickable_accordian").first().trigger('click');
});

//changes by vishal on 20 july 2020 for resolving the product attribute issue on replacing products
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
//changes end

function closeReturnPopup() {
    $('#kb_rm_pop_up').html('');
}

function handleReturnBlockRefresh() {
    if ($('#rm_return_submit_success_popup').length) {
        if (rm_is_logged && rm_is_logged == 1) {
            var data = 'ajax=true&method=getCustomerOrders';
        } else {
            var data = 'ajax=true&method=getGuestOrder&' + $('#returnmanager_form input').serialize();
        }
        $.ajax({
            url: module_link,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (response) {
                $("#rm_single_order_detail_container").html(response['template']);
                $("#rm_single_order_detail_container").show();
                //                if(rm_is_logged) {
                //                    $(".rm_clickable_accordian").first().trigger('click');
                //                }
                var current_tab = getCookie("current_tab");
                $("#rm_accordian_" + current_tab).trigger('click');
                $("#click_to_expand").show();
                $("#click_to_contract").hide();
                setCookie("click_to_expand", 0, 1);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(rm_ajax_failed + ': ' + textStatus);
            }
        });
    }

    closeReturnPopup();
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

function displayReturnAddressOnMultipleForm(element) {
    var add = $('#rm_return_address_' + element).val();
    if (add == 0) {
        $('#rm_popup_address_' + element).html($('#default_addr_' + element).val());
    } else {
        var display_addr = $('#full_addr_' + add + '_' + element).val();
        $('#rm_popup_address_' + element).html(display_addr);
    }
}


function showHiddenOrderData(id_order) {
    if (id_order == 0)
        $("#rm_return_history_block").toggle('slow', 'swing');
    else {
        if ($(".rm_custom_accordian_" + id_order).is(':visible'))
            $('.rm_custom_accordian_' + id_order).slideRow('up');
        else
            $('.rm_custom_accordian_' + id_order).slideRow('down');
    }

    var click_to_expand = getCookie("click_to_expand");
    if (click_to_expand == 1) {
        $("#click_to_expand").show();
        $("#click_to_contract").hide();
        setCookie("click_to_expand", 0, 1);
    } else {
        $("#click_to_expand").hide();
        $("#click_to_contract").show();
        setCookie("click_to_expand", 1, 1);
    }
}

function rmshowHiddenOrderData(id_order) {
    if (!$(".rm_custom_accordian_" + id_order).is(':visible'))
        showHiddenOrderData(id_order);
}

function showHiddenOrderDataRefresh(id_order) {
    if (id_order == 0)
        $("#rm_return_history_block").toggle('slow', 'swing');
    else {
        if ($(".rm_custom_accordian_" + id_order).is(':visible'))
            $('.rm_custom_accordian_' + id_order).slideRow('up');
        else
            $('.rm_custom_accordian_' + id_order).slideRow('down');
    }

    $("#click_to_expand").show();
    $("#click_to_contract").hide();
    setCookie("click_to_expand", 0, 1);
}

(function ($) {
    var sR = {
        defaults: {
            slideSpeed: 400,
            easing: false,
            callback: false
        },
        thisCallArgs: {
            slideSpeed: 400,
            easing: false,
            callback: false
        },
        methods: {
            up: function (arg1, arg2, arg3) {
                if (typeof arg1 == 'object') {
                    for (p in arg1) {
                        /*
                         * Assign option keys via bracket notation for Addons validator compatibility.
                         * 21-07-2026
                         */
                        sR.thisCallArgs[p] = arg1[p];
                    }
                } else if (typeof arg1 != 'undefined' && (typeof arg1 == 'number' || arg1 == 'slow' || arg1 == 'fast')) {
                    sR.thisCallArgs.slideSpeed = arg1;
                } else {
                    sR.thisCallArgs.slideSpeed = sR.defaults.slideSpeed;
                }

                if (typeof arg2 == 'string') {
                    sR.thisCallArgs.easing = arg2;
                } else if (typeof arg2 == 'function') {
                    sR.thisCallArgs.callback = arg2;
                } else if (typeof arg2 == 'undefined') {
                    sR.thisCallArgs.easing = sR.defaults.easing;
                }
                if (typeof arg3 == 'function') {
                    sR.thisCallArgs.callback = arg3;
                } else if (typeof arg3 == 'undefined' && typeof arg2 != 'function') {
                    sR.thisCallArgs.callback = sR.defaults.callback;
                }
                var $cells = $(this).find('td');
                $cells.wrapInner('<div class="slideRowUp" />');
                var currentPadding = $cells.css('padding');
                $cellContentWrappers = $(this).find('.slideRowUp');
                $cellContentWrappers.slideUp(sR.thisCallArgs.slideSpeed, sR.thisCallArgs.easing).parent().animate({
                    paddingTop: '0px',
                    paddingBottom: '0px'
                }, {
                    complete: function () {
                        $(this).children('.slideRowUp').replaceWith($(this).children('.slideRowUp').contents());
                        $(this).parent().css({ 'display': 'none' });
                        $(this).css({ 'padding': currentPadding });
                    }
                });
                var wait = setInterval(function () {
                    if ($cellContentWrappers.is(':animated') === false) {
                        clearInterval(wait);
                        if (typeof sR.thisCallArgs.callback == 'function') {
                            sR.thisCallArgs.callback.call(this);
                        }
                    }
                }, 100);
                return $(this);
            },
            down: function (arg1, arg2, arg3) {
                if (typeof arg1 == 'object') {
                    for (p in arg1) {
                        /*
                         * Assign option keys via bracket notation for Addons validator compatibility.
                         * 21-07-2026
                         */
                        sR.thisCallArgs[p] = arg1[p];
                    }
                } else if (typeof arg1 != 'undefined' && (typeof arg1 == 'number' || arg1 == 'slow' || arg1 == 'fast')) {
                    sR.thisCallArgs.slideSpeed = arg1;
                } else {
                    sR.thisCallArgs.slideSpeed = sR.defaults.slideSpeed;
                }

                if (typeof arg2 == 'string') {
                    sR.thisCallArgs.easing = arg2;
                } else if (typeof arg2 == 'function') {
                    sR.thisCallArgs.callback = arg2;
                } else if (typeof arg2 == 'undefined') {
                    sR.thisCallArgs.easing = sR.defaults.easing;
                }
                if (typeof arg3 == 'function') {
                    sR.thisCallArgs.callback = arg3;
                } else if (typeof arg3 == 'undefined' && typeof arg2 != 'function') {
                    sR.thisCallArgs.callback = sR.defaults.callback;
                }
                var $cells = $(this).find('td');
                $cells.wrapInner('<div class="slideRowDown" style="display:none;" />');
                $cellContentWrappers = $cells.find('.slideRowDown');
                $(this).show();
                $cellContentWrappers.slideDown(sR.thisCallArgs.slideSpeed, sR.thisCallArgs.easing, function () { $(this).replaceWith($(this).contents()); });
                var wait = setInterval(function () {
                    if ($cellContentWrappers.is(':animated') === false) {
                        clearInterval(wait);
                        if (typeof sR.thisCallArgs.callback == 'function') {
                            sR.thisCallArgs.callback.call(this);
                        }
                    }
                }, 100);
                return $(this);
            }
        }
    };
    $.fn.slideRow = function (method, arg1, arg2, arg3) {
        if (typeof method != 'undefined') {
            if (sR.methods[method]) {
                return sR.methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            }
        }
    };
})(jQuery);
//function showHiddenOrderData(id_order)
//{
//	if (id_order == 0)
//		$('#rm_return_history_block').slideToggle('slow');
//	else
//		$(".rm_custom_accordian_"+id_order).slideToggle('slow');
//}

function validateReturn() {
    $('#returnmanager_form .rm_error').remove();
    var emailPattern = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    var error = false;
    if ($.trim($('#rm_customer_email').val()) == '') {
        error = true;
        $('#rm_customer_email').parent().append('<span class="rm_error">' + requiredField + '</span>');
    } else if (!emailPattern.test($.trim($('#rm_customer_email').val()))) {
        error = true;
        $('#rm_customer_email').parent().append('<span class="rm_error">' + invalidEmailId + '</span>');
    }

    if ($.trim($('#rm_reference_id').val()) == '') {
        error = true;
        $('#rm_reference_id').parent().append('<span class="rm_error">' + requiredField + '</span>');
    }

    if (!error) {
        $.ajax({
            url: module_link,
            type: 'post',
            data: 'ajax=true&method=getGuestOrder&' + $('#returnmanager_form input').serialize(),
            dataType: 'json',
            beforeSend: function () {
                $('#error_div').hide();
                $('#error_div').html('');
                $("#rm_single_order_detail_container").hide();
                $("#rm_single_order_detail_container").html('');
                $('#returnmanager_form').fadeTo('slow', 0.9);
            },
            complete: function () {
                $('#returnmanager_form').fadeTo('slow', 1);
            },
            success: function (response) {
                if (!response['order_found']) {
                    $('#error_div').html(orderNotFound);
                    $('#error_div').show();
                    setTimeout(function () { $('#error_div').hide(); }, 10000);
                } else {
                    $("#rm_find_order_form").hide();
                    $("#rm_single_order_detail_container").html(response['template']);
                    $("#rm_single_order_detail_container").show();
                    $("#click_to_expand").show();
                    $("#click_to_contract").hide();
                    setCookie("click_to_expand", 0, 1);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(rm_ajax_failed + ': ' + textStatus);
            }
        });
    }
}

function getReturnForm(id_info, e) {
    var test123 = id_info.split("_");
    var div_id = test123[0];
    setCookie("current_tab", div_id, 1);
    $.ajax({
        url: module_link,
        type: 'post',
        data: 'ajax=true&method=getRequestForm&id_info=' + id_info,
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
                /**
                 * Start Changes to fix the name conflict issue with the function 
                 * Changed name from autocomplete to autocompleteKB
                 * NASep2023 autocomplete_name
                 * @date 18-09-2023
                 * @modifier Nikhil Aggarwal
                 */
                $('input[name="kb_Product_specific_products"]').autocompleteKB(path_fold, {
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
}

function addProductToMappedproductpage(data) {
    console.log(data);
    if (data == null)
        return false;

    var productId = data[1];
    $('input[name="rm_return_product"]').val(productId);
    displayKbProductAttribute(productId);
}


function addProductToMultipleMappedproductpage(data, e) {
    console.log(e);
    if (data == null)
        return false;

    var productId = data[1];
    $(e).siblings('input[name^="rm_return_product_"]').val(productId);
    displaymultipleKbProductAttribute($(e).siblings('input[name^="rm_return_product_"]'));
}

//changes by vishal for adding order cancellation functionality
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
//changes end

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
                /**
                 * Start Changes to fix the name conflict issue with the function 
                 * Changed name from autocomplete to autocompleteKB
                 * NASep2023 autocomplete_name
                 * @date 18-09-2023
                 * @modifier Nikhil Aggarwal
                 */
                $('input[name="kb_Product_specific_products"]').autocompleteKB(path_fold, {
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
    setLeftColHeight('rm_popup_request_form');
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

function displayReasonNote(e) {
    $(e).parent().find('span.rm_error').remove();
    var val = $(e).val();
    $('#rm_reason_type_note p').hide();
    if (val != 0) {
        $('#rm_reason_type_note p#rm_reason_type_note_' + val).show();
    }
    setLeftColHeight('rm_popup_request_form');
}

function displayReasonNoteOnMultipleForm(e, product_id) {
    $(e).parent().find('span.rm_error').remove();
    var val = $(e).val();
    $('#rm_reason_type_note_' + product_id + ' p').hide();
    if (val != 0) {
        $('#rm_reason_type_note_' + product_id + ' p#rm_reason_type_note_' + val).show();
    }
    setLeftColHeight('rm_popup_request_form');
}

function setLeftColHeight(container) {
    $('#rm_popup_pro_info').css('height', $('#' + container).height() + 21);
}

function cancelReturnRequest(return_id) {
    if (confirm(confirm_delete_msg)) {
        $('.kbloading').show();
        $.ajax({
            url: module_link,
            type: 'post',
            data: 'ajax=true&method=cancelReturnRequest&return_id=' + return_id,
            dataType: 'json',
            beforeSend: function () {
            },
            complete: function () {
            },
            success: function (response) {
                if (response['status_updated']) {
                    $.gritter.add({
                        title: '',
                        text: cancel_success,
                        class_name: 'gritter-success',
                        sticky: false,
                        time: '3000'
                    });
                    if (typeof (is_order_history_page) !== 'undefined' && is_order_history_page == '1') {
                        location.reload();
                    }
                    if (rm_is_logged) {
                        var data = 'ajax=true&method=getCustomerOrders';
                    } else {
                        var data = 'ajax=true&method=getGuestOrder&' + $('#returnmanager_form input').serialize();
                    }
                    $.ajax({
                        url: module_link,
                        type: 'post',
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            $("#rm_single_order_detail_container").html(response['template']);
                            $("#rm_single_order_detail_container").show();
                            var current_tab = getCookie("current_tab");
                            $("#rm_accordian_" + current_tab).trigger('click');
                            $("#click_to_expand").show();
                            $("#click_to_contract").hide();
                            setCookie("click_to_expand", 0, 1);
                            $('.kbloading').hide();
                            //$('#rm_return_history_block').find('tbody').find('tr').find('p').find('a').css('margin-top','15px');  
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            alert(rm_ajax_failed + ': ' + textStatus);
                        }
                    });
                } else {
                    $.gritter.add({
                        title: '',
                        text: cancel_failure,
                        class_name: 'gritter-warnings',
                        sticky: false,
                        time: '3000'
                    });
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(rm_ajax_failed + ': ' + textStatus);
            }
        });
    }
}

//changes by vishal for adding cancel order functionality
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
            myFormData.append('image', $("#rm_return_image")[0].files[0]);  // added by sandeep chauhan
            //myFormData.append('image', $('input[type=file]')[0].files[0]);
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

    if ($('#rm_popup_request_form select[name="rm_return_type"]').val() == 'replacement' && $('#rm_popup_request_form select[name="rm_return_product"]').val() == 0) {
        error = true;
        $('#rm_popup_request_form select[name="rm_return_product"]').parent().append('<span class="rm_error">' + rm_product_required + '</span>');
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

    if (!error) {
        //var data = $('#rm_popup_request_form input[name="rm_return_image"]')[0].files[0];
        var myFormData = new FormData();
        if ($('input[type=file]').length > 0) {
            myFormData.append('image', $("#rm_return_image")[0].files[0]); // added by sandeep chauhan
            //myFormData.append('image', $('input[type=file]')[0].files[0]);
        }
        myFormData.append('ajax', 'true');
        myFormData.append('method', 'submitReturnRequest');
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

    $('#rm_popup_request_form .returnList').each(function () {
        if ($(this).find('select[name*="rm_return_type_"]').val() == 'replacement' && $(this).find('select[name*="rm_return_product_"]').val() == 0) {
            error = true;
            $(this).find('select[name*="rm_return_product_"]').parent().append('<span class="rm_error">' + rm_product_required + '</span>');
        }
    });

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
        image_array = [];
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

function CheckFileType(data) {
    $('#rm_popup_request_form input[name="rm_return_image"]').closest('.rm_form_control_block').find('span.rm_error').remove();
    if ($(data).prop('files').length > 0) {
        var extension_arr = ['gif', 'jpg', 'png', 'jpeg', 'zip'];
        var file_ext = $(data).val().trim().substring($(data).val().trim().lastIndexOf('.') + 1).toLowerCase();
        if ($.inArray(file_ext, extension_arr) == - 1) {
            $(data).val('');
            $('#rm_popup_request_form input[name="rm_return_image"]').closest('.rm_form_control_block').append('<span class="rm_error">' + file_type_error + '</span>');
        } else if (parseFloat($(data)[0].files[0].size / 1024).toFixed(0) > 4096) {
            $(data).val('');
            $('#rm_popup_request_form input[name="rm_return_image"]').closest('.rm_form_control_block').append('<span class="rm_error">' + image_size_error + '</span>');
        }
    }
}

//function showhidetoc(){
////To display and hide toc block
////    $('#rm_pop_up').on('click', '#rm_display_toc', function(){
//if ($('#rm_toc_block').is(':visible')){
//$('#rm_toc_block').hide();
//} else{
//$('#rm_toc_block').show();
//}
//setLeftColHeight('rm_popup_request_form');
////        });
//}

function showhidetoc() {
    //To display and hide toc block
    // $('#rm_pop_up').on('click', '#rm_display_toc', function(){
    if ($('#rm_toc_block').is(':visible')) {
        $('#rm_toc_block').hide();
    } else {
        $('#rm_toc_block').show();
    }

    setLeftColHeight('rm_popup_request_form');
    objDiv = document.getElementById("rm_return_form_popup"); objDiv.scrollTop = objDiv.scrollHeight;
    // });
}

function showhidetocOnMultipleForm(element) {
    if ($('#rm_toc_block_' + element).is(':visible')) {
        $('#rm_toc_block_' + element).hide();
    } else {
        $('#rm_toc_block_' + element).show();
    }
    setLeftColHeight('rm_popup_request_form');
}

$(window).on("load", function () {
    $('#rm_return_history_block').find('tbody').find('tr').find('p').find('a').css('margin-top', '15px');
});
