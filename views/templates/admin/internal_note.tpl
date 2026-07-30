<!--------------- Start - Custom CSS/JS -------------------->
<div class="widget">
    <div class="widget-head">
        <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Add New Note' mod='returnmanager'}</h3>
    </div>
    <div class="widget-body">
        <div id="tab_custom" class="tab-pane">
            <div class="block">
                <table class="form">
                    <input id="rm_current_return_id" type="hidden" name="rm_current_return_id" value="{$rm_current_return_id|escape:'htmlall':'UTF-8'}" />
                    <tr>
                        <td class="settings">
                            <textarea rows="5" style="resize: both;" name="internal_note"></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>       
<!--------------- End - Custom CSS/JS -------------------->


<div class="widget">
    <div class="widget-head">
        <h3 class="heading" style='margin: 0px; height: 0px;'>{l s='Internal Note History' mod='returnmanager'}</h3>
    </div>
    <div class="widget-body">
        <table class="return-man-tab">
            <thead>
                <tr>
                    <th>{l s='Sr. No.' mod='returnmanager'}</th>
                    <th>{l s='User' mod='returnmanager'}</th>
                    <th>{l s='Notes' mod='returnmanager'}</th>
                    <th>{l s='Added On' mod='returnmanager'}</th>
                </tr>
            </thead>
            <tbody>
                {$sr = 1}
                {foreach $internal_note_list as $internal_note}
                    <tr>
                        <td>{$sr|escape:'htmlall':'UTF-8'}</td>
                        <td>{$internal_note['name']|escape:'htmlall':'UTF-8'}</td>
                        <td>{$internal_note['comment']|escape:'htmlall':'UTF-8'}</td>
                        <td>{$internal_note['date_added']|escape:'htmlall':'UTF-8'}</td>
                    </tr>
                    {$sr=$sr+1}
                {/foreach}
            </tbody>
        </table>                                                    
    </div>
</div>

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2015 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Return Manager Return Detail File
*}


