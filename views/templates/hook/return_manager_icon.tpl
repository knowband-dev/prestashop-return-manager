{**
* Start Changes to use the template instead of using the HTML in PHP file
* Using a TPL file to display the Returns Manager Icon
* NAFeb2024 return_manager_icon
* @date 04-02-2024
* @modifier Nikhil Aggarwal
*}
{**
* Start Changes to fix the issue of icon size being small on Mobile devices
* The icon size was small on mobile devices, so added a class to increase the size of the icon to handle small size screen Media Query
* NAFeb2024 return_manager_icon_mobile_size
* @date 09-03-2024
* @modifier Nikhil Aggarwal
*}
<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" href="{$return_manager_link_hook nofilter}" title="{$return_manager_text_hook nofilter}" >{*Variable contains html content, escape not required*}
    <span class="link-item">
        <i class="material-icons">&#xE418;</i>
        {$return_manager_text_hook nofilter} {*Variable escape not required*}
    </span>
</a>
{* Changes end by Nikhil *}

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
* Return Manager Icon Hook File
*}