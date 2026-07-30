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
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin:0">
    <tbody>
        <tr>
            <td width="20%" valign="top" align="center" style="padding:5px 0 0 0;margin:0;text-align:center;
            color:#565656">{$image nofilter}</td>
            <td width="35%" valign="top" align="center" style="padding:5px 0 0 0;margin:0;text-align:center;
            color:#565656">{$item nofilter}</td>
            <td width="7%" valign="top" align="center" style="padding:5px 0 0 0;margin:0;text-align:center;
            color:#565656">{$qty nofilter}</td>
            <td width="35%" valign="top" align="center" style="padding:5px 0 0 10px;margin:0;text-align:right;
            color:#565656">{$price_label nofilter}
            </td>
        </tr>
        <tr>
            <td colspan="4" align="left" valign="top">
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tbody>
                        <tr>
                            <td valign="middle" align="left" style="border-bottom:solid 2px #565656;width:90%">&nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td valign="middle" align="left">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td width="20%">
                <div style="margin-bottom:5px">
                    <a href="{$product_link nofilter}" target="_blank">
                        <img src="{$product_img_path nofilter}" border="0" height="100" width="100"></a>
                </div>
            </td>
            <td valign="top" align="center" style="border-bottom:1px solid #f2f2f2;padding:12px 10px;margin:0">
                <p style="padding:0;margin:0">
                    <a style="text-decoration:none;color:#565656" href="{$product_link nofilter}" target="_blank">
                        <span style="color:#565656"><b>{$product_name nofilter}</b></span><span style="color:#565656">{$attr_html nofilter}</span></a>
                </p>
            </td>
            <td valign="top" align="center" style="border-bottom:1px solid #f2f2f2;padding:12px 0;
        margin:0;text-align:center">
                <p style="padding:0 10px 0 0;margin:0">{$quantity nofilter}</p>
            </td>
            <td valign="top" align="center" style="border-bottom:1px solid #f2f2f2;padding:12px 0 0 10px;
        margin:0;text-align:right">
                <p style="white-space:nowrap;padding:0;margin:0;font-weight:bold">{$price nofilter}</p>
            </td>
        </tr>
    </tbody>
</table>