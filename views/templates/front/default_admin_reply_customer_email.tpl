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
                                    <p style="padding:0 0 0 0;margin:0;font-size:16px;font-weight:bold"> Hey
                                        {$customer_name nofilter}, </p>
                                </span>
                                <p style="padding:20px 0 0 0;margin:0;color:#565656;line-height:20px">
                                    Admin has just replied on #{$ticket_number nofilter} ticket generated in reference to return
                                    request having #{$return_id nofilter}.</span>.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="space_footer" style="padding:0!important">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
                                <table class="table" style="width:100%">
                                    <tr>
                                        <td width="10" style="padding:7px 0">&nbsp;</td>
                                        <td style="padding:7px 0">
                                            <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                <span style="color:#777">
                                                    <span style="color:#333"><strong>Ticket Number</strong></span>
                                                    #{$ticket_number nofilter}<br />
                                                    <span style="color:#333"><strong>Subject:</strong></span>
                                                    {$subject nofilter}<br /><br />
                                                    <span style="color:#333"><strong>Message:</strong></span>
                                                    {$message nofilter}<br /><br />
                                                </span>
                                            </font>
                                        </td>
                                        <td width="10" style="padding:7px 0">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="space_footer" style="padding:0!important">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="linkbelow" style="padding:7px 0">
                                <span>You can track your ticket status by clicking this <a href="{$ticket_track_url nofilter}"
                                        style="color:#337ff1">link</a></span>
                            </td>
                        </tr>


                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>