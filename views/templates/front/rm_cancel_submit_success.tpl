<style>
    {*{if isset($returned_product['img_path'])}    
        .rm_box_shadow {
            background-image: url({$product['img_path']|escape:'quotes':'UTF-8'});
        }    
    {else}
        .rm_box_shadow {
            background-image: url({Context::getContext()->link->getImageLink($returned_product['link_rewrite'], $returned_product['id_image'], 'small_default')});
        }
    {/if}*}
</style>
<div id="rm_return_submit_success_popup" class="white_content">
    <div class="rm_innerBox">
        <a href="javascript:void(0)" class="rm_popup_close_icon" onclick="handleReturnBlockRefresh()" style="z-index:9999999;margin-right: -7px;margin-top: -12px;">&nbsp;</a>
        <div id="rm_row">
            <div id="rm_popup_pro_info" class="rm_popup_left rm_left rm_box_shadow rm_success_form_height" style="background: black;">
                <div class="rm_row" style='margin-top: 8px;'>
                    <span class="rm_popup_pro_name uppercase rm_sub_heading titleHeading">{l s='You Returned' mod='returnmanager'}</span>
                    {*<div class="rm_popup_pro_img">
			{if isset($returned_product['img_path'])}
                                <img src="{$returned_product['img_path'] nofilter}" onerror="this.src='{$path nofilter}returnmanager/views/img/No-image.jpg'">
			{else}
				<img src="{Context::getContext()->link->getImageLink($returned_product['link_rewrite'], $returned_product['id_image'], 'small_default')}" onerror="this.src='{$path nofilter}returnmanager/views/img/No-image.jpg'">
			{/if}
                    </div>
                    <div class="rm_popup_pro_name_block">
                        <span class="rm_popup_pro_name">{$returned_product['name'] nofilter}</span>
                        {foreach $returned_product['attributes'] as $p_attr}
                            <span class="rm_popup_pro_attr">{$p_attr nofilter}</span>
                        {/foreach}
                    </div>*}
                </div>
                {*<div class="rm_row rm_vertical_border rm_vpad3 rm_returned_qty">
                    <span class="">{l s='Quantity' mod='returnmanager'}: {$returned_product['quantity'] nofilter}</span>
                </div>*}
                <br>
                <div class="rm_row returnDetails">
                    <span class="rm_popup_pro_name uppercase rm_sub_heading" style="color:white">{l s='Reason' mod='returnmanager'}</span>
                    <div class="rm_popup_addr rm_vpad3 vss_line_height-13" style="color:white"><p style="color:white">{$return_reason nofilter}</p></div>
                </div>
                <br>
                <div class="rm_row returnDetails">
                    <span class="rm_popup_pro_name uppercase rm_sub_heading" style="color:white">{l s='Your Comment' mod='returnmanager'}</span>
                    <div class="rm_popup_addr rm_vpad3 rm_order_return_comment vss_line_height-13" style="color:white">{if $customer_commet neq '<pre></pre>'}{$customer_commet nofilter}{else}<span class='vss_italic_text'>{l s='No comments.' mod='returnmanager'}</span>{/if}</div>
                    {*Variable contains html content, escape not required*}
                </div>
            </div>
            <div id="rm_popup_success_form" class="rm_popup_right rm_left">
                <div class="rm_row">
                    <div class="rm_submit_success_heading"> <img src="{$img_path|escape:'htmlall':'UTF-8'}tick.png"/> 
                        {* Start - Code Modified by Raghu on 24-Oct-2017 for fixing the Success Message content issue in Return Success Popup *}
                            {$success_message nofilter}{*Variable contains html content, escape not required*}
                        {* End - Code Modified by Raghu on 24-Oct-2017 for fixing the Success Message content issue in Return Success Popup *}
                    </div>
                </div>
                <div class="rm_row">
                    <div class="rm_return_id_text">{l s='Your Cancel Id' mod='returnmanager'}: <b>#{$return_id nofilter}</b></div>
                </div>
                <div class="rm_row">
                    <div class="rm_success_note">
                        <p>{$success_msg nofilter}</p>{*Variable contains html content, escape not required*}
                    </div>
                </div>
                <div class="rm_row">
                    <div class="rm_fancy_heading">{l s='Order Details' mod='returnmanager'}</div>
                    <div class="rm_o_detail_block vss_font-size-13">
                        {if count($products) > 0}
                            <div class="rm_o_sub_block rm_o_sub_block_width">
                                <p class="rm_o_section_heading">{l s='Items' mod='returnmanager'}</p>
                                <div id="rm_o_cart_info" class="rm_row">
                                    {foreach $products as $product}
                                    <div class="rm_row rm_o_p_row">
                                        <div class="rm_o_p_img">
						{if isset($product['img_path'])}
							
                                <img src="{$product['img_path'] nofilter}" onerror="this.src='{$path nofilter}returnmanager/views/img/No-image.jpg'">
                            
						{else}
							
				<img src="{Context::getContext()->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default') nofilter}" onerror="this.src='{$path nofilter}returnmanager/views/img/No-image.jpg'">
                            
						{/if}
                                        </div>
                                        <div class="rm_o_p_info">
                                            <div class="rm_popup_pro_name">{$product['name'] nofilter}</div>
                                            {foreach $product['attributes'] as $p_attr}
                                                <span>{$p_attr nofilter}</span>
                                            {/foreach}
                                            <span>{l s='Quantity' mod='returnmanager'}: {$product['quantity'] nofilter}</span>
                                        </div>
                                        {*
                                        * Use PHP-preformatted price; Tools::displayPrice() removed in newer PrestaShop.
                                        * 21-07-2026
                                        *}
                                        <div class="rm_o_p_price">{$product['unit_price_display']|escape:'html':'UTF-8'}</div>
                                    </div>
                                    {/foreach}
                                </div>                            
                            </div>
                        {/if}
                        <div class="rm_o_sub_block rm_o_right_block">
                            <div class="rm_row" style="line-height: 1.0 !important;">
                                <p class="rm_o_section_heading">{l s='Shipping Address' mod='returnmanager'}</p>
                                <div class="">
                                    {$shipping_address nofilter}{*Variable contains html content, escape not required*}
                                </div>    
                            </div>
                            <br>
                            <div class="rm_row">
                                <p class="rm_o_section_heading">{l s='Order total' mod='returnmanager'}</p>
                                <div class="rm_row">
                                    <div class="rm_o_total_left">{l s='Sub-Total' mod='returnmanager'}:</div>
                                    <div class="rm_o_total_right">{$sub_total nofilter}</div>
                                </div>
                                <div class="rm_row">
                                    <div class="rm_o_total_left">{l s='Shipping Charge' mod='returnmanager'}:</div>
                                    <div class="rm_o_total_right">
                                        {if $shipping_charge > 0}
                                            {*
                                            * Use PHP-preformatted shipping; Tools::displayPrice() removed in newer PrestaShop.
                                            * 21-07-2026
                                            *}
                                            {$shipping_charge_display|escape:'html':'UTF-8'}
                                        {else}
                                            {l s='Free' mod='returnmanager'}
                                        {/if}</div>
                                </div>
                                <div class="rm_row">
                                    <div class="rm_o_total_left">{l s='Total' mod='returnmanager'}:</div>
                                    <div class="rm_o_total_right">{$order_total nofilter}</div>
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rm_clear"></div>
            </div>
        </div>
        <div class="rm_clear"></div>    
    </div>        
</div>
<div id="rm_fade" class="black_overlay"></div>

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
* @category  PrestaShop Module
*
* Description
*
* Returns Submit Successful Page
*}