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
<html>

<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
</head>
<style>
    * {
        font-family: DejaVu Sans;
        font-size: 12px;
    }
</style>

<body>
    <div style="border: 2px dashed grey; page-break-after: always;">
        {$label_content nofilter}
    </div>
    {$mailing_label nofilter}
    <br>
    <div><strong> {$return_address nofilter}</strong></div>
    <h2><strong>{$authorization_label nofilter}</strong></h2>
    <p>
        {$cut_this_place nofilter}</p>
    <hr style="border: 1px dashed grey;">
    <br></table>
    <table style="margin: 0 auto;">
        <tr>
            <td align="center">{$bar_code_html_4_90 nofilter}</td>
        </tr>
        <tr>
            <td align="center"><strong>{$return_id_label nofilter}:</strong> {$return_id nofilter}</td>
        </tr>
        <tr>
            <td align="center">
                <strong>{$order_label nofilter}:</strong> {$order_reference nofilter}
            </td>
        </tr>
    </table><br>
    <table cellpadding="0" cellspacing="0" style="width: 100%;'
            . ' border-right:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4;">
        <tr>
            <td width="350" style="border-left:1px solid #A4A4A4; border-top:1px solid #A4A4A4;
			background: #E6E6E6; padding: 3px;">
                <strong>{$item_desc nofilter}</strong>
            </td>
            <td width="80" style="border-left:1px solid #A4A4A4;'
        . ' border-top:1px solid #A4A4A4; background: #E6E6E6; padding: 3px;">
                <strong>{$total_price_label nofilter}</strong>
            </td>
            <td width="65" style="border-left:1px solid #A4A4A4; border-top:1px solid #A4A4A4;'
            . ' background: #E6E6E6; padding: 3px;">
                <strong>{$quantity_label nofilter}</strong>
            </td>
        </tr>
        <tr>
            <td style="border-left:1px solid #A4A4A4; border-top:1px solid #A4A4A4; padding: 3px;">
                {$product_name nofilter} {if isset($product_attr)}
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 15px; font-style: italic;">{$product_attr nofilter}</span>
                {/if}
            </td>
            <td style="border-left:1px solid #A4A4A4; border-top:1px solid #A4A4A4; padding: 3px;">
                {$product_price nofilter}
            </td>
            <td style="border-left:1px solid #A4A4A4; border-top:1px solid #A4A4A4; padding: 3px;">
                {$ret_qty nofilter}
            </td>
        </tr>
    </table><br>
    <p style="text-align: center;">
        <strong>{$to_whom_label nofilter}</strong>
    </p>
    <p>
        {$declaration nofilter}
        {$if_any nofilter}
        <strong>{$return_id_label nofilter}:{$return_id nofilter}</strong> {$against nofilter}
        <strong>{$order_label nofilter}: {$order_reference nofilter}</strong>
    </p>
    <p>{$declare nofilter}
        {$reject nofilter}</p>
    <p>{$sincerely nofilter}, </p><br><br>
    <hr style="border: 1px dashed grey;">
</body>

</html>