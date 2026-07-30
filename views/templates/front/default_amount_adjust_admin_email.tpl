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
<table style="width: 640px; border: 0px none;" width="600" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td colspan="9" align="center" valign="top">
                <table style="border: 1px solid #e6e6e6; width: 100%;" width="100%" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td style="margin: 0; padding: 25px 20px 5px 25px;" align="left" valign="top"
                                bgcolor="FFFFFF">
                                <p style="padding: 0 0 0 0; margin: 0; font-size: 16px; font-weight: bold;">Dear Admin,
                                </p>
                                <p style="padding: 20px 0 0 0; margin: 0; color: #565656; line-height: 20px;">
                                    We want to inform you that you just accepted a replacement request for client with
                                    email id {$client_email nofilter}. There is a difference of {$difference_amount nofilter} which you need
                                    to
                                    pay to client. Kindly do the needful. Item details are below: </p>
                            </td>
                        </tr>
                        <tr>

                            <td style="padding: 20px 20px 0 20px;" align="left" valign="top">
                                <p style="padding: 0 0 0 0; margin: 0; font-size: 16px; font-weight: bold;">The product
                                    which needs to be returned:</p><br>
                                <a href="{$replaced_product_link nofilter}">{$replaced_product_name nofilter}</a>
                            </td>
                        </tr>
                        <tr>

                            <td style="padding: 20px 20px 0 20px;" align="left" valign="top">
                                <p style="padding: 0 0 0 0; margin: 0; font-size: 16px; font-weight: bold;">The product
                                    which is requested to replace with:</p><br>
                                <a href="{$replacedwith_product_link nofilter}">{$replacedwith_product_name nofilter}</a>
                            </td>
                        </tr>
                        <tr>
                            <td style="margin: 0; padding: 25px 20px 5px 25px;" align="left" valign="top"
                                bgcolor="FFFFFF">
                                <p style="margin: 0; color: #565656; line-height: 20px;"><strong>Return id for this
                                        request is : #{$return_id nofilter}</strong></p>
                            </td>
                        </tr>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>