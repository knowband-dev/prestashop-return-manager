{if isset($velsof_return_custom_data['css'])}
    <style>{$velsof_return_custom_data['css'] nofilter}</style>{*Variable contains CSS content, escape not required*}
{/if}

{if isset($velsof_return_custom_data['js'])}
    <script>{$velsof_return_custom_data['js'] nofilter}</script>{*Variable contains JS content, escape not required*}
{/if}

{if isset($rm_displaynav1_links) && count($rm_displaynav1_links) > 0}
    <style>
        #_desktop_contact_link, #rm_displaynav1_links_container{
            display: inline-table;
        }
        
        .display_nav1_link{
            display: table-cell;
            padding: 0 5px;
        }
    </style>
    <div id="rm_displaynav1_links_container">
        {foreach $rm_displaynav1_links as $sl}
            <div class="display_nav1_link">
                {*
                Changes to add the UTF-8 for the escaping
                NAFeb2024 UTF-8
                @date 09-02-2024
                @modifier Nikhil Aggarwal*}
                <a href="{$sl.href|escape:'quotes':'UTF-8'}" title="{$sl.title nofilter}" rel="nofollow">{*Variable Contains Html content,escape not required*}
                    {$sl.label nofilter}
                </a>
            </div>
        {/foreach}    
    </div>
{/if}

{*
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
* Description
*
* Return Manager Return Link Hook File
*}