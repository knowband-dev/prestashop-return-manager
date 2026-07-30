{**
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
 *
 *}
<table style="width:640px;border:0px none" width="600" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td colspan="9" align="center" valign="top">
                <table width="100%" style="border:1px solid #e6e6e6;width:100%" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td align="left" valign="top" style="margin:0;padding:25px 20px 5px 25px" bgcolor="FFFFFF">
                                <span>
                                    <p style="padding:0 0 0 0;margin:0;font-size:16px;
                        font-weight:bold"> Dear {$customer_full_name nofilter}, </p>
                                    <p style="padding:20px 0 0 0;margin:0;color:#565656;
                        line-height:20px">Greetings from {$shop_name nofilter}!</p>
                                </span>
                                <p style="padding:20px 0 0 0;margin:0;color:#565656;line-height:20px">
                                    Your return request has been received for the following item in your order
                                    <span style="color:#00648b"> <a href="{$order_history_link nofilter}"
                                            target="_blank">{$order_reference nofilter}</a></span>.
                                </p>
                                <span>
                                    <p style="padding:5px 10px;background-color:#fffed5;border:1px solid #f9e2b2;color:#565656;
                         margin:10px 0 0 0;text-align:left;line-height:20px">
                                        Please keep in mind that this is only the first step of the return process.
                                        The request has to be approved by shop owner in order to further process the
                                        request.
                                        You will be notified once the shop owner approves this return request.<br>
                                    </p>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" valign="top" style="padding:20px 20px 0 20px">
                                {$item_details nofilter}
                            </td>
                        </tr>
                        <tr>
                            <td align="left" valign="top" style="margin:0;padding:25px 20px 5px 25px" bgcolor="FFFFFF">
                                <p style="margin:0;color:#565656;line-height:20px">
                                    <b>Return id for this request is : #{$return_id nofilter}</b>
                                </p>

                                <p style="padding:19px 0 0 0;margin:0;color:#565656;line-height:19px">
                                    We apologize for any inconvenience caused to you.
                                </p><br>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="top" style="padding:15px 40px;margin:0;text-align:center;
                background-color:#f9f9f9" bgcolor="F9F9F9">
                                <p style="padding:0;margin:0 0 7px 0">
                                    <a title="{$shop_name nofilter}" style="text-decoration:none;color:#565656" href="{$shop_url nofilter}"
                                        target="_blank"><span style="color:#565656">{$shop_name nofilter}</span></a>
                                </p><span>
                                    <p
                                        style="padding:10px 0 0 0;margin:0;border-top:solid 1px #cccccc;font-size:11px;color:#565656">
                                        24x7 Customer Support � Flexible Payment Options � Largest Collection � Easy
                                        Returns
                                    </p>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>