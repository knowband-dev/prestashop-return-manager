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
                                    <p style="padding:0 0 0 0;margin:0;font-size:16px;font-weight:bold"> Hey Admin, </p>
                                </span>
                                <p style="padding:20px 0 0 0;margin:0;color:#565656;line-height:20px">
                                    A new return request has been received against the order {$order_reference nofilter}</span>.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" valign="top" style="padding:20px 20px 0 20px">
                                Item to be returned <br><br>{$item_details nofilter}
                            </td>
                        </tr>
                        <tr>
                            <td align="left" valign="top" style="margin:0;padding:25px 20px 5px 25px" bgcolor="FFFFFF">
                                <p style="margin:0;color:#565656;line-height:20px">
                                    <b>Return id for this request is : #{$return_id nofilter}</b>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>
                                    <p style="padding:5px 10px;background-color:#fffed5;border:1px solid #f9e2b2;
                        color:#565656;
                         margin:10px 0 0 0;text-align:left;line-height:20px">
                                        Please login to the admin panel and take appropriate action regarding this
                                        return request.
                                        This mail is just to notify you about the return request,
                                        you can process the return request only from back office.<br>
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