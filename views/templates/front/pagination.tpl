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
<div class="{$pagination_align nofilter}">
    <ul class="rm-pagination">
        {*Changes to fix the issue of multi tabs for paginations
        Commenting below code as it was generating wrong pagination tabs in the listing at the Admin Side
        NAFeb2024 pagination
        @date 09-02-2024
        @modifier Nikhil Aggarwal*}
        {* {if $current_page > 1}
            <li class="first"><a href="javascript:void(0)" data-page="1" onclick="{$first_link_funtion nofilter}"
                    title="First">&laquo;</a></li>
            <li><a href="javascript:void(0)" data-page="' . $previous_link . '" onclick="{$previous_link_funtion nofilter}"
                    title="Previous">&lt;</a></li>
        {/if} *}
        {* {foreach from=range($current_page-2, $current_page-1) item=i}
            {if $i > 0}
                <li><a href="javascript:void(0)" data-page="{$i nofilter}" onclick="{$onclickfunc nofilter}" title="Page{$i nofilter}">{$i nofilter}</a></li>
            {/if}
        {/foreach} *}
        {* {if isset($first_link)}
            <li class="first active">{$first_link nofilter}</li>
        {elseif isset($last_link)}
            <li class="last active">{$last_link nofilter}</li>
        {elseif isset($active)}
            <li class="active">{$active nofilter}</li>
        {/if} *}
        {* In the above code, $key represents the index of the current iteration, and $value represents the value at that index in the $right_links array. The condition inside the loop checks if $value is less than or equal to $total_pages and $key is greater than or equal to $current_page+1. If the condition is true, it displays the page number inside an anchor tag.*}
        {foreach $right_links as $key => $value}
            {if $value <= $total_pages && $key >= $current_page+1}
                <li><a href="javascript:void(0)" data-page="{$value nofilter}" onclick="{$ajax_call_function|replace:'{page_number}':$value}"
                        title="Page{$value nofilter}">{$value nofilter}</a></li>
            {/if}
        {/foreach}
        
        {*Changes to fix the issue of multi tabs for paginations
        Commenting below code as it was generating wrong pagination tabs in the listing at the Admin Side
        NAFeb2024 pagination
        @date 09-02-2024
        @modifier Nikhil Aggarwal*}
        {* {if $current_page < $total_pages}
            <li><a href="javascript:void(0)" data-page="' . $next_link . '"
                    onclick="{$ajax_call_function|replace:'{page_number}':$i}" title="Next">&gt;</a></li>
            <li class="last"><a href="javascript:void(0)" data-page="{$total_pages nofilter}"
                    onclick="{$ajax_call_function|replace:'{page_number}':$i}" title="Last">&raquo;</a></li>
        {/if} *}

        {if $current_page > 1}
            {$previous_link = ($previous == 0) ? 1 : $previous}
            {assign var="ajax_call_function_replaced_1" value=$ajax_call_function|replace:'{page_number}':1}
            <li class="first"><a href="javascript:void(0)" data-page="1" onclick="{$ajax_call_function_replaced_1 nofilter}"
                    title="First">«</a></li>
            {assign var="ajax_call_function_replaced_previous" value=$ajax_call_function|replace:'{page_number}':$previous_link}
            <li><a href="javascript:void(0)" data-page="{$previous_link nofilter}" onclick="{$ajax_call_function_replaced_previous nofilter}"
                    title="Previous">&lt;
                </a>
            </li>
            {foreach from=range($current_page-2, $current_page-1) item=i}
                {if $i > 0}
                    {assign var="ajax_call_function_replaced_i" value=$ajax_call_function|replace:'{page_number}':$i}
                    <li><a href="javascript:void(0)" data-page="{$i nofilter}" onclick="{$ajax_call_function_replaced_i nofilter}"
                            title="Page{$i nofilter}">{$i nofilter}</a></li>
                {/if}
            {/foreach}
            {assign var="first_link" value=false}
        {/if}

        {if $first_link}
            <li class="first active">{$current_page nofilter}</li>
        {elseif $current_page == $total_pages}
            <li class="last active">{$current_page nofilter}</li>
        {else}
            <li class="active">{$current_page nofilter}</li>
        {/if}

        {for $i=$current_page+1; $i<$right_links; $i++}
            {if $i<=$total_pages}
                <li>
                    <a href="javascript:void(0)" data-page="{$i}" onclick="{str_replace(['{page_number}', '"'], [$i, ' \\"'],
                $ajax_call_function)}" title="Page {$i nofilter}">{$i nofilter}</a>
                </li>
            {/if}
        {/for}

        {if $current_page < $total_pages}
            {$next_link = ($i > $total_pages) ? $total_pages : $i}
            <li>
                <a href="javascript:void(0)" data-page="{$next_link nofilter}"
                    onclick="{str_replace(['{page_number}', '"'], [$next_link, ' \\"'], $ajax_call_function ) nofilter}"
                    title="Next">&gt;</a>
            </li>
            <li class="last">
                <a href="javascript:void(0)" data-page="{$total_pages nofilter}"
                    onclick="{str_replace(['{page_number}', '"'], [$total_pages, ' \\"'], $ajax_call_function) nofilter}"
                    title="Last">&raquo;</a>
            </li>
        {/if}
    </ul>
</div>