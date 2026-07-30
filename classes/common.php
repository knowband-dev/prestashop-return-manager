<?php
/**
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
 *
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
/*
 * use Dompdf\Dompdf to resolve the issue of currency symbol by replacing dompdf library
 * @date 05-02-2023
 * @author Ayushi
 * @commenter Prvind Panday
 */

use Dompdf\Dompdf;

class Common extends Module
{
    /**
     * constants defined for the class
     * @date 05-02-2023
     * @commenter Prvind Panday
     */
    const TEMPLATE_NAME = 'velsof_rm';
    const ITEM_PER_PAGE = 10;

    /**
     * Change the value of PAGINATION_ALIGN to "right" to display pagination on right side and "left" to display on left side
     * @date 05-02-2023
     * @commenter Prvind Panday
     */
    const PAGINATION_ALIGN = 'right';
    const RETURN_SLIP_NAME = 'ReturnSlip';

    public function __construct()
    {
        $this->name = 'returnmanager';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();
    }

    /*
     * Format price via Locale API for Addons validator / PS 1.7.6+ compatibility.
     * 21-07-2026
     * @param float $price
     * @param Currency|null $currency
     * @return string
     */
    protected function kbFormatPrice($price, $currency = null)
    {
        if (!is_object($currency) || empty($currency->iso_code)) {
            $currency = $this->context->currency;
        }
        if (method_exists($this->context, 'getCurrentLocale') && $this->context->getCurrentLocale()) {
            return $this->context->getCurrentLocale()->formatPrice((float) $price, $currency->iso_code);
        }
        return (isset($currency->sign) ? $currency->sign . ' ' : '') . number_format((float) $price, 2, '.', '');
    }

    /*
     * Build shop base URL with protocol for SSL and non-SSL contexts.
     * 21-07-2026
     * @param bool $ssl
     * @return string
     */
    protected function kbGetShopBaseUrl($ssl = false)
    {
        if ($ssl) {
            return Tools::getShopDomainSsl(true);
        }
        return Tools::getShopDomain(true);
    }

    /*
     * Display date compatible with PS 1.7 (3 args) and PS 8+ (2 args) without flagging validator.
     * 21-07-2026
     * @param string $date
     * @param bool $full
     * @return string
     */
    protected function kbDisplayDate($date, $full = false)
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            return Tools::displayDate($date, (bool) $full);
        }
        return Tools::displayDate($date, (int) $this->context->language->id);
    }

    /*
     * Load order products safely when Order::getCartProducts() fatals on a deleted address.
     * 21-07-2026
     * @param Order $order
     * @return array
     */
    protected function kbGetOrderCartProducts($order)
    {
        try {
            $products = $order->getCartProducts();
            if (is_array($products)) {
                return $products;
            }
        } catch (Exception $e) {
            /*
             * Invalid/deleted delivery address causes Address::initialize to throw in getProducts().
             * 21-07-2026
             */
        } catch (Error $e) {
            /*
             * Catch PHP Error the same way for robustness across PS versions.
             * 21-07-2026
             */
        }

        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT od.*, p.`id_category_default`
            FROM `' . _DB_PREFIX_ . 'order_detail` od
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = od.`product_id`)
            WHERE od.`id_order` = ' . (int) $order->id
        );
        if (!$rows) {
            return array();
        }

        $product_detail = array();
        foreach ($rows as $row) {
            $cover = Product::getCover((int) $row['product_id']);
            $image = null;
            if (!empty($cover['id_image'])) {
                $image = new Image((int) $cover['id_image']);
            }
            $row['id_product'] = (int) $row['product_id'];
            $row['id_category_default'] = isset($row['id_category_default']) ? (int) $row['id_category_default'] : 0;
            $row['product_quantity_return'] = isset($row['product_quantity_return']) ? (int) $row['product_quantity_return'] : 0;
            $row['image'] = $image;
            $product_detail[] = $row;
        }
        return $product_detail;
    }

    /*
     * Generate unique cart-rule coupon code (moved here so Common callers pass static analysis).
     * 21-07-2026
     * @return string
     */
    protected function generateCouponCode()
    {
        $length = 8;
        $code = '';
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ0123456789';
        $maxlength = Tools::strlen($chars);
        if ($length > $maxlength) {
            $length = $maxlength;
        }
        $i = 0;
        while ($i < $length) {
            $char = Tools::substr($chars, mt_rand(0, $maxlength - 1), 1);
            if (!strstr($code, $char)) {
                $code .= $char;
                $i++;
            }
        }
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'cart_rule where code = "' . pSQL($code) . '"';
        $result = Db::getInstance()->executeS($sql);
        if (count($result) == 0) {
            return $code;
        }
        return $this->generateCouponCode();
    }

    /*
     * Function getTemplateDir is used to get the template directory path
     * @date 05-02-2023
     * @author 
     * @commenter Prvind Panday
     * @return string
     */
    protected function getTemplateDir()
    {
        /*
         * Language::getIsoById($this->context->language->id) is used to get the iso code of the language
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $iso = Configuration::get('VELSOF_RETURN_MANAGER_DEFAULT_TEMPLATE_LANG');
        return _PS_MODULE_DIR_ . 'returnmanager/mails/' . $iso . '/';
    }

    /*
     * Function getReturnSlipName is used to get the return slip name
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param int $return_id
     * @return string
     */
    protected function getReturnSlipName($return_id)
    {
        /*
         * Query to get the language id from the table velsof_rm_order by id_rm_order
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $query = 'Select id_lang from ' . _DB_PREFIX_ . 'velsof_rm_order where id_rm_order = ' . (int) $return_id;
        $language_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        /*
         * Language::getIsoById((int) $language_id) is used to get the iso code of the language
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $language = Language::getIsoById((int) $language_id);
        if (!$language) {
            /*
             * If language is not found then get the iso code of the language from the context
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $language = Language::getIsoById($this->context->language->id);
        }
        /*
         * $this->getModuleTranslationByLanguage('returnmanager', 'ReturnSlip', 'common', $language) is used to get the translation of the string 'ReturnSlip' in the language $language
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $slipname = $this->getModuleTranslationByLanguage('returnmanager', 'ReturnSlip', 'common', $language);
        return $slipname . $return_id . '.pdf';
    }

    /*
     * Function getReturnSlipPath is used to get the return slip path
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @return string
     */
    protected function getReturnSlipPath()
    {
        return _PS_MODULE_DIR_ . 'returnmanager/reports/slips/';
    }

    /**
     * Function customPaginator is used to get the pagination html for the return list
     * @date 05-02-2023
     * @author
     * @commenter Prvind Panday
     * @param int $total_records
     * @param int $total_pages
     * @param string $ajaxcallfn
     * @param int $active
     * @param int $current_page
     * @return array
     */
    protected function customPaginator($total_records, $total_pages, $ajaxcallfn = '', $active = 0, $current_page = 1)
    {
        $summary_txt = '';
        $pagination = '';
        /*
         * If total pages is greater than 0 and total pages is not equal to 1 and current page is less than or equal to total pages then create the pagination else return empty array with no pagination
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages) {
            /*
             * Assign the class name to the variable $summary_align and $pagination_align based on the value of the constant PAGINATION_ALIGN
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $summary_align = 'rm-pagination-left';
            $pagination_align = 'rm-pagination-left';
            /*
             * If PAGINATION_ALIGN is equal to 'right' then assign the class name to the variable $summary_align and $pagination_align
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            /*
             * Apply right-side pagination alignment (PAGINATION_ALIGN constant).
             * 21-07-2026
             */
            $summary_align = 'rm-pagination-left';
            $pagination_align = 'rm-pagination-right';
            /*
             * If current page is greater than 1 then assign the value of the variable $record_start and $record_end
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $record_start = $current_page;
            $record_end = self::ITEM_PER_PAGE;
            if ($current_page > 1) {
                $record_start = (($current_page - 1) * self::ITEM_PER_PAGE) + 1;
                if ($current_page == $total_pages) {
                    $record_end = $total_records;
                } else {
                    $record_end = $current_page * self::ITEM_PER_PAGE;
                }
            }

            /*
             * Assign the html to the variable $summary_txt based on the value of the variable $summary_align
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $this->context->smarty->assign(
                array(
                    'summary_align' => $summary_align,
                    'record_start' => $record_start,
                    'record_end' => $record_end,
                    'total_records' => $total_records,
                    'total_pages' => $total_pages,
                    'pagination_align' => $pagination_align,
                )
            );
            $summary_txt .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'returnmanager/views/templates/front/summary_txt.tpl');

            /*
             * If ajaxcallfn is not empty then create the pagination with ajax call else create the pagination without ajax call
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $ajax_call_function = '';
            if ($ajaxcallfn != '') {
                $ajax_call_function .= $ajaxcallfn . '({page_number}, ' . $active . ');';
            }

            /*
             * Use the variable $right_links and $previous to create the pagination
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $right_links = $current_page + 3;
            $previous = $current_page - 3; //previous link
            $first_link = true; //boolean var to decide our first link

            /*
             * If current page is greater than 1 then create the pagination
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $this->context->smarty->assign(
                array(
                    'current_page' => $current_page,
                    'total_pages' => $total_pages,
                    'ajax_call_function' => $ajax_call_function,
                    'previous' => $previous,
                    'first_link' => $first_link,
                    'right_links' => $right_links,
                )
            );
            if ($current_page > 1) {
                $previous_link = ($previous == 0) ? 1 : $previous;
                /*
                 * If current page is equal to 1 then set the variable $first_link to false
                 * @date 05-02-2023
                 * @commenter Prvind Panday
                 */
                $first_link = false; //set first link to false
                $this->context->smarty->assign(
                    array(
                        'previous_link' => $previous_link,
                        'first_link' => $first_link,
                    )
                );
            }

            $pagination = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'returnmanager/views/templates/front/pagination.tpl');
            /*
             * Create the pagination for the summary
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            return array(
                'paging' => $summary_txt . $pagination,
                'serial' => $record_start
            );
        }
        /*
         * If the total records are less than 1 then return the empty array
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        return array(
            'paging' => '',
            'serial' => 1
        );
    }

    /**
     * Function getGuestOrder is used to get the guest orders
     * @date 05-02-2023
     * @author 
     * @commenter Prvind Panday
     * @param string $reference_id
     * @param string $email
     * @return array
     */
    public function getGuestOrder($reference_id, $email)
    {
        $orders = array();
        /**
         * getOrder function is used to get the order details
         * @date 05-02-2023
         * @commenter Prvind Panday
         * @param boolean $id_customer
         * @param int $id_lang
         * @param string $reference_id
         * @param string $email
         */
        $orders[0] = $this->getOrder(false, 0, $reference_id, $email);

        /**
         * If the order is found then get the customer details else set order_found to false
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if (isset($orders[0]['rm_customer_id']) && $orders[0]['rm_customer_id'] > 0) {
            $id_customer = $orders[0]['rm_customer_id'];
            $order_found = true;
        } else {
            $id_customer = 0;
            $order_found = false;
        }

        /**
         * Query to get the customer details from the customer table based on the customer id, language id and shop id
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $select_customer_info = 'select firstname, lastname,email from ' . _DB_PREFIX_ . 'customer
			where id_customer=' . (int) $id_customer . '
			and id_lang=' . (int) $this->context->language->id . ' and id_shop=' . (int) $this->context->shop->id;
        $customer_info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($select_customer_info);
        /** 
         * Get the return history for the customer
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $return_history = $this->getReturnHistory($id_customer);
        $custom_ssl_var = 0;
        /**
         * If the ssl is enabled then set the custom_ssl_var to 1
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        /**
         * If the ssl is enabled then set the ps_base_url to Tools::getShopDomainSsl(true) else set the ps_base_url to Tools::getShopDomain(true)
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $ps_base_url = Tools::getShopDomainSsl(true);
        } else {
            $ps_base_url = Tools::getShopDomain(true);
        }

        /**
         * Assign the smarty variables
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $this->context->smarty->assign(
            array(
                'isLogged' => ($this->context->customer->isLogged()) ? true : false,
                'orders' => $orders,
                'customer_info' => $customer_info,
                'return_history' => $return_history,
                'path' => $ps_base_url . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_),
                'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager'),
                'kb_admin_link' => $this->context->link->getModuleLink('returnmanager', 'manager', array('method' => 'ajaxproductaction', 'ajax' => true)),
            )
        );

        /**
         * Get the module configuration settings
         * @date 05-02-2023
         * @commenter Prvind Panday
         * @comment Tools::unSerialize is used to unserialize the data
         */
        $settings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        /**
         * If enable_return_slip is set and its value is 1 then set the return_slip to 1 else set the return_slip to 0
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if (isset($settings['enable_return_slip']) && $settings['enable_return_slip'] == 1) {
            $this->context->smarty->assign('return_slip', 1);
        } else {
            $this->context->smarty->assign('return_slip', 0);
        }




        /**
         * If enable_order_return is set and its value is 1 then set the enable_complete_order_return to 1 else set the enable_complete_order_return to 0
         * @date 05-02-2023
         * @author Kanishka Kannujia
         * @commenter Prvind Panday
         */
        if (isset($settings['enable_order_return']) && $settings['enable_order_return'] == 1) {
            $this->context->smarty->assign('enable_complete_order_return', 1);
        } else {
            $this->context->smarty->assign('enable_complete_order_return', 0);
        }

        /**
         * Unset the settings
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        unset($settings);

        /**
         * set the order canceled id to the smarty variable
         * @date 28-02-2023
         * @author Prvind Panday
         * @commenter Prvind Panday
         */
        $order_canceled_id = Configuration::get('PS_OS_CANCELED');
        $this->context->smarty->assign('order_canceled_id', $order_canceled_id);
        /* Start Code Added By Priyanshu on 12-September-2020 to implement the Specific Product Selection Functionality */
        $this->context->smarty->assign('kb_admin_link', $this->context->link->getModuleLink('returnmanager', 'manager', array('method' => 'ajaxproductaction', 'ajax' => true)));
        /* End Code Added By Priyanshu on 12-September-2020 to implement the Specific Product Selection Functionality */
        /**
         * Set the array variable to the smarty variable
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        /**
         * Start changes to fix the issue of using modifier directly in tpl
         * NAAug2023 modifier
         * @date 09-08-2023
         * @author Nikhil Aggarwal
         */
        $this->context->smarty->registerPlugin("modifier", "impl", "implode");
        // Changes end by Nikhil
        $arr = array(
            'order_found' => $order_found,
            'template' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/order_detail_content.tpl'
            )
        );
        return $arr;
    }

    /**
     * Function getOrderAdmin is used to get the order details
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param string|null $reference_id
     * @param string|null $email
     * @return array
     */
    public function getOrderAdmin($reference_id, $email)
    {
        $orders = array();
        /**
         * getOrder function is used to get the order details
         * @date 05-02-2023
         * @commenter Prvind Panday
         * @param boolean $id_customer
         * @param int $id_lang
         * @param string $reference_id
         * @param string $email
         */
        $orders[0] = $this->getOrder(false, 0, $reference_id, $email);
        //changes by vishal for set order ID on 26 august 2020
        /**
         * Start Changes to fix the parse error when no order is found
         * Adding isset condition
         * NASep2023 getOrderAdmin_parse_error
         * @date 19-09-2023
         * @modifier Nikhil Aggarwal
         */
        if (!isset($orders[0]['order_id'])) {
            $id_customer = 0;
            $order_found = false;
        }
        // Changes end by Nikhil
        //changes end
        /**
         * If the order is found then set the id_customer to the customer id else set the id_customer to 0
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if (isset($orders[0]['rm_customer_id']) && $orders[0]['rm_customer_id'] > 0) {
            $id_customer = $orders[0]['rm_customer_id'];
            $order_found = true;
        } else {
            $id_customer = 0;
            $order_found = false;
        }
        /**
         * Query to get the customer details from the customer table based on the customer id, language id and shop id
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $select_customer_info = 'select firstname, lastname,email from ' . _DB_PREFIX_ . 'customer
			where id_customer=' . (int) $id_customer . '
			and id_lang=' . (int) $this->context->language->id . ' and id_shop=' . (int) $this->context->shop->id;
        $customer_info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($select_customer_info);

        $custom_ssl_var = 0;
        /**
         * If the https is set and its value is on then set the custom_ssl_var to 1 else set the custom_ssl_var to 0
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        /**
         * If the PS_SSL_ENABLED is set and its value is 1 and the custom_ssl_var is 1 then set the ps_base_url to Tools::getShopDomainSsl(true) else set the ps_base_url to Tools::getShopDomain(true)
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $ps_base_url = Tools::getShopDomainSsl(true);
        } else {
            $ps_base_url = Tools::getShopDomain(true);
        }

        /**
         * Set the smarty variables
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $this->context->smarty->assign(
            array(
                'orders' => $orders,
                'customer_info' => $customer_info,
                'path' => $ps_base_url . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_),
                'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager')
            )
        );
        /**
         * Start changes to fix the issue of using modifier directly in tpl
         * NAAug2023 modifier
         * @date 09-08-2023
         * @author Nikhil Aggarwal
         */
        $this->context->smarty->registerPlugin("modifier", "impl", "implode");
        // Changes end by Nikhil
        /**
         * to assign the smarty variable for complete order Return Functionality
         * Functionality: To implement Complete Order Return Functionality.
         * @date 05-02-2023
         * @author Prvind Panday
         * @commenter Prvind Panday
         */
        $settings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);

        /**
         * If the enable_order_return is set and its value is 1 then set the enable_complete_order_return to 1 else set the enable_complete_order_return to 0
         * Functionality: To implement Complete Order Return Functionality.
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if (isset($settings['enable_order_return']) && $settings['enable_order_return'] == 1) {
            $this->context->smarty->assign('enable_complete_order_return', 1);
        } else {
            $this->context->smarty->assign('enable_complete_order_return', 0);
        }

        /**
         * $arr is used to set the order_found and template and return the $arr
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $arr = array(
            'order_found' => $order_found,
            'template' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . 'returnmanager/views/templates/admin/order_detail_admin.tpl'
            )
        );
        return $arr;
    }

    /**
     * getOrder function is used to get the order details
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param boolean $is_logged
     * @param int $id_order
     * @param string $reference_id
     * @param string $email
     */
    public function getOrder($is_logged, $id_order, $reference_id = null, $email = null)
    {
        /**
         * If the is_logged is false then set the query to get the order details based on the reference id and email else set the query to get the order details based on the id_order
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if (!$is_logged) {
            $qry = 'select ord.id_order,cust.id_customer from ' .
                _DB_PREFIX_ . 'orders ord, ' . _DB_PREFIX_ . 'customer cust
                where ord.reference= "' . pSQL($reference_id) . '"
                and cust.email= "' . pSQL($email) . '"
                and ord.id_customer=cust.id_customer
                and ord.id_shop=' . (int) $this->context->shop->id;
        } else {
            /**
             * If the is_logged is true then set the query to get the order details based on the id_order
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $qry = 'select ord.id_order,cust.id_customer from ' . _DB_PREFIX_ .
                'orders ord, ' . _DB_PREFIX_ . 'customer cust
                where ord.id_order= "' . (int) $id_order . '"
                and ord.id_customer=cust.id_customer
                and ord.id_shop=' . (int) $this->context->shop->id;
        }

        /**
         * Get the order details based on the query
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $order_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
        $order_detail = array();

        /**
         * If the order_id is not empty and the order_id is greater than 0 then set the order detail
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ($order_id && $order_id['id_order'] != '' && $order_id['id_order'] > 0) {

            /**
             * Set the order detail, order object created by the order id
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $order = new Order((int) $order_id['id_order']);
            $reference_id = $order->reference;
            $order_state = new OrderState($order->current_state, $this->context->language->id);
            $order_status = $order_state->name;
            $order_status_color = $order_state->color;
            /**
             * Start Changes to fix the issue of 500 error because of the different number of parameters in the function
             * In PS8 and above, only two params are allowed in the displayDate(). So, adding the PS version check
             * NAFeb2024 displaydate
             * @date 06-02-2024
             * @modifier Nikhil Aggarwal
             */
            /*
             * Use kbDisplayDate for PS 1.7 / 8+ signature compatibility.
             * 21-07-2026
             */
            $order_date = $this->kbDisplayDate($order->date_add, true);
            // Changes end by Nikhil Aggarwal

            $kb_order_currency_obj = new Currency($order->id_currency);
            $total = $this->kbFormatPrice($order->total_products_wt, $kb_order_currency_obj);
            $total_paid = $this->kbFormatPrice($order->total_paid_tax_incl, $kb_order_currency_obj);

            // edit end by sandeep chauhan

            /* Start Code Modified By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
            $policy_order_status = Configuration::get('VELSOF_RETURNMANAGER_POLICY_STATUS');
            $kbsettings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);

            /**
             * If the enable_order_status_selection_return_policy is not set or its value is 0 or the policy_order_status is null or the policy_order_status is 0 then set the placed_order_date to the order date add else set the placed_order_date to the order status date
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            if (!isset($kbsettings['enable_order_status_selection_return_policy']) || $kbsettings['enable_order_status_selection_return_policy'] == 0 || $policy_order_status == null || $policy_order_status == 0) {
                $placed_order_date = $order->date_add;
                $policy_status_applicable = true;
            } else {
                $qry = 'select date_add from ' . _DB_PREFIX_ .
                    'order_history where id_order= "' . (int) $order_id['id_order'] . '"
                and id_order_state=' . (int) $policy_order_status;
                $order_status_date = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
                if (isset($order_status_date['date_add']) && $order_status_date['date_add'] != null) {
                    $policy_status_applicable = true;
                    $placed_order_date = $order_status_date['date_add'];
                } else {
                    $policy_status_applicable = false;
                    $placed_order_date = $order->date_add;
                }
            }
            /* End Code Modified By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */

            /**
             * get the cart products based on the order
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            /*
             * Use safe loader so deleted order addresses do not throw PrestaShopException.
             * 21-07-2026
             */
            $product_detail = $this->kbGetOrderCartProducts($order);
            $products = array();
            $custom_ssl_var = 0;
            $is_cancellable = 0; // added by sandeep as in case product_detail foreach does not run than is_cancellable does not exist for order_detail array
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }

            if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                $ps_base_url = Tools::getShopDomainSsl(true);
            } else {
                $ps_base_url = Tools::getShopDomain(true);
            }

            /**
             * set the custom product detail array based on the product detail fetched above
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            foreach ($product_detail as $pro) {
                $product = array();
                $product['id_order_detail'] = $pro['id_order_detail'];
                $product['id_product'] = $pro['product_id'];
                if (strpos($pro['product_name'], ' - ')) {
                    $temp = explode(' - ', $pro['product_name']);
                    $product['name'] = trim($temp[0]);
                    $product['attributes'] = explode(',', trim($temp[1]));
                } else {
                    $product['name'] = $pro['product_name'];
                    $product['attributes'] = array();
                }

                if (isset($pro['image']->id_image)) {
                    $product['id_image'] = $pro['image']->id_image;
                } else {
                    $product['id_image'] = 0;
                }

                $product['quantity'] = $pro['product_quantity'];
                // edited by sandeep chauhan
                $kb_order_currency_obj = new Currency($order->id_currency);
                $product['price'] = $this->kbFormatPrice($pro['total_price_tax_incl'], $kb_order_currency_obj);
                // end by sandeep chauhan

                $is_delivered = false;
                $is_returnable = false;
                $is_creditable = false;
                $credit_min_days = 0;
                $credit_days = 0;
                $is_refundable = false;
                $refund_min_days = 0;
                $refund_days = 0;
                $replacement_min_days = 0;
                $replacement_days = 0;
                $is_replacement = false;


                //Check for Deliver
                /* changes  by rishabh jain on 18th JUly 2019
                 * to allow return on the selected order status only
                 */
                $rm_settings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
                //changes by vishal for adding order cancel functionality
                $is_cancellable = 0;

                //changes end

                /**
                 * check if the order status is in the selected order status for return order then set the is_delivered to true
                 * @date 05-02-2023
                 * @commenter Prvind Panday
                 */
                if (isset($rm_settings['enable_order_status_selection']) && $rm_settings['enable_order_status_selection'] == 1) {
                    $selected_order_status = array();
                    $selected_order_status = json_decode(Configuration::get('VELSOF_RETURNMANAGER_ORDER_STATUS'), true);
                    if (in_array($order->current_state, $selected_order_status)) {
                        $is_delivered = true;
                    }
                } else {
                    /**
                     * check if the order is delivered or not and if the order is delivered then set the is_delivered to true
                     * @date 05-02-2023
                     * @commenter Prvind Panday
                     */
                    if ($order->hasBeenDelivered()) {
                        $is_delivered = true;
                    }
                }
                /* changes over */
                $already_returned = false;
                /* Start Code Modified By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
                /**
                 * check if the order status is in the selected order status for return order then set the policy_status_applicable to true
                 * @date 05-02-2023
                 * @commenter Prvind Panday
                 */
                if ($is_delivered && $policy_status_applicable) {
                    /* End Code Modified By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */

                    /**
                     * get the default policy for the product by product id and category id
                     * @date 05-02-2023
                     * @commenter Prvind Panday
                     */
                    $product['rm_policy_id'] = $this->getDefaultPolicy($pro['id_product'], $pro['id_category_default']);
                    /**
                     * Query to get the order details
                     * @date 05-02-2023
                     * @commenter Prvind Panday
                     */
                    $sql = 'SELECT SUM(quantity) as total,active as active from ' . _DB_PREFIX_ . 'velsof_rm_order
                        where id_order_detail = ' . (int) $pro['id_order_detail']
                        . ' AND id_shop = ' . (int) $this->context->shop->id . ' AND active!=5';
                    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

                    $product['total_return_qty'] = $result['total'];

                    /**
                     * check if the total return quantity is greater than or equal to the quantity of the product then set the already_returned to true
                     * @date 05-02-2023
                     * @commenter Prvind Panday
                     */
                    if (($product['total_return_qty'] >= $product['quantity'])) {
                        $already_returned = true;
                    } else {
                        /**
                         * Query to get the return data based on policy id for the product
                         * @date 05-02-2023
                         * @commenter Prvind Panday
                         */
                        $qry = 'select rd.credit_days, rd.refund_days, rd.replacement_days, rd.credit_min_days, rd.refund_min_days, rd.replacement_min_days from ' .
                            _DB_PREFIX_ . 'velsof_return_data as rd
							WHERE rd.policy = 1 AND rd.active = 1 AND rd.return_data_id = ' .
                            (int) $product['rm_policy_id'];
                        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
                        //changes by vishal on 4th december for resolving policy days calculation issue
                        /**
                         * check if the result is not empty and is an array then set the is_returnable to true and calculate the credit days, credit min days, refund days, refund min days, replacement days, replacement min days
                         * @date 05-02-2023
                         * @commenter Prvind Panday
                         */
                        if ($result && is_array($result)) {
                            $is_returnable = true;
                            $current_date = date('Y-m-d H:i:s');
                            $delivery_date = $order->delivery_date;

                            /**
                             * check if the delivery date is not empty then set the placed order date to the delivery date
                             * @date 05-02-2023
                             * @commenter Prvind Panday
                             */
                            $calculated_min_date = date(
                                'Y-m-d H:i:s',
                                strtotime('+' . $result['credit_min_days'] . ' day', strtotime($placed_order_date))
                            );

                            $calculated_date = date(
                                'Y-m-d H:i:s',
                                strtotime('+' . $result['credit_days'] . ' day', strtotime($placed_order_date))
                            );

                            /**
                             * check if the current date is less than the calculated date then set the credit days
                             * @date 05-02-2023
                             * @commenter Prvind Panday
                             */
                            $credit_days = 0;
                            $credit_min_days = 0;
                            if ($current_date < $calculated_date) {
                                $time_diff2 = strtotime($calculated_date) - strtotime($current_date);
                                $credit_days = ceil(($time_diff2) / (60 * 60 * 24));
                            }
                            if ($current_date > $calculated_min_date) {
                                $time_diff2 = strtotime($current_date) - strtotime($calculated_min_date);
                                $credit_min_days = ceil(($time_diff2) / (60 * 60 * 24));
                            }

                            /**
                             * check if the credit min days is greater than 0 then check if the credit days is greater than 0 and credit min days is greater than 0 then set the is_creditable to true
                             * else if the credit days is greater than 0 then set the is_creditable to true
                             * @date 05-02-2023
                             * @commenter Prvind Panday
                             */
                            if ($result['credit_min_days'] > 0) {
                                if ($credit_days > 0 && $credit_min_days > 0) {
                                    $is_creditable = true;
                                }
                            } else {
                                if ($credit_days > 0) {
                                    $is_creditable = true;
                                }
                            }

                            /**
                             * check if the current date is less than the calculated date then set the refund days
                             * @date 05-02-2023
                             * @commenter Prvind Panday
                             */
                            $refund_days = 0;
                            $refund_min_days = 0;
                            $calculated_min_date = date(
                                'Y-m-d H:i:s',
                                strtotime('+' . $result['refund_min_days'] . ' day', strtotime($placed_order_date))
                            );
                            $calculated_date = date(
                                'Y-m-d H:i:s',
                                strtotime('+' . $result['refund_days'] . ' day', strtotime($placed_order_date))
                            );

                            /**
                             * check if the current date is less than the calculated date then set the refund days
                             * @date 05-02-2023
                             * @commenter Prvind Panday
                             */
                            if ($current_date < $calculated_date) {
                                $time_diff1 = strtotime($calculated_date) - strtotime($current_date);
                                $refund_days = ceil(($time_diff1) / (60 * 60 * 24));
                            }

                            if ($current_date > $calculated_min_date) {
                                $time_diff2 = strtotime($current_date) - strtotime($calculated_min_date);
                                $refund_min_days = ceil(($time_diff2) / (60 * 60 * 24));
                            }

                            /**
                             * check if the refund min days is greater than 0 then check if the refund days is greater than 0 and refund min days is greater than 0 then set the is_refundable to true
                             * else if the refund days is greater than 0 then set the is_refundable to true
                             * @date 05-02-2023
                             * @commenter Prvind Panday
                             */
                            if ($result['refund_min_days'] > 0) {
                                if ($refund_min_days > 0 && $refund_days > 0) {
                                    $is_refundable = true;
                                }
                            } else {
                                if ($refund_days > 0) {
                                    $is_refundable = true;
                                }
                            }

                            /**
                             * check if the current date is less than the calculated date then set the replacement days
                             * @date 05-02-2023
                             * @commenter Prvind Panday
                             */
                            $replacement_days = 0;
                            $replacement_min_days = 0;
                            $calculated_date = date(
                                'Y-m-d H:i:s',
                                strtotime('+' . $result['replacement_days'] . ' day', strtotime($placed_order_date))
                            );
                            $calculated_min_date = date(
                                'Y-m-d H:i:s',
                                strtotime('+' . $result['replacement_min_days'] . ' day', strtotime($placed_order_date))
                            );

                            /**
                             * check if the current date is less than the calculated date then set the replacement days
                             * @date 05-02-2023
                             * @commenter Prvind Panday
                             */
                            if ($current_date < $calculated_date) {
                                $time_diff = strtotime($calculated_date) - strtotime($current_date);
                                $replacement_days = ceil(($time_diff) / (60 * 60 * 24));
                            }
                            if ($current_date > $calculated_min_date) {
                                $time_diff2 = strtotime($current_date) - strtotime($calculated_min_date);
                                $replacement_min_days = ceil(($time_diff2) / (60 * 60 * 24));
                            }

                            /**
                             * check if the replacement min days is greater than 0 then check if the replacement days is greater than 0 and replacement min days is greater than 0 then set the is_replacement to true
                             * else if the replacement days is greater than 0 then set the is_replacement to true
                             * @date 05-02-2023
                             * @commenter Prvind Panday
                             */
                            if ($result['replacement_min_days'] > 0) {
                                if ($replacement_min_days > 0 && $replacement_days > 0) {
                                    $is_replacement = true;
                                }
                            } else {
                                if ($replacement_days > 0) {
                                    $is_replacement = true;
                                }
                            }
                        }
                    }
                }

                /**
                 * check if the current date is less than the calculated date then set the cancel days
                 * @date 05-02-2023
                 * @author Vishal
                 * @commenter Prvind Panday
                 */
                $sql = 'SELECT * from ' . _DB_PREFIX_ . 'velsof_rm_cancel
                        where id_order = ' . (int) $order_id['id_order']
                    . ' AND id_shop = ' . (int) $this->context->shop->id . ' AND active=2';
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

                /**
                 * check if the result is empty then set the is_returnable, is_refundable and is_creditable to false
                 * @date 05-02-2023
                 * @commenter Prvind Panday 
                 */
                if (!empty($result)) {
                    $is_returnable = false;
                    $is_refundable = false;
                    $is_creditable = false;
                }
                //changes end

                /**
                 * Set the values to the product array
                 * @date 05-02-2023
                 * @commenter Prvind Panday
                 */
                $product['id_order'] = $order_id['id_order'];
                $product['is_delivered'] = $is_delivered;
                $product['is_returnable'] = $is_returnable;
                $product['is_creditable'] = $is_creditable;
                $product['already_returned'] = $already_returned;
                $img_type = 'home_';
                $img_type .= 'default';
                if (isset($pro['image'])) {
                    $image = new Image($pro['image']->id_image);
                    $product['pro_img'] = $ps_base_url . _THEME_PROD_DIR_ .
                        $image->getExistingImgPath() . '-' . $img_type . '.jpg';
                    unset($image);
                } else {
                    $product['pro_img'] = $ps_base_url . __PS_BASE_URI__ .
                        '/modules/returnmanager/views/img/No-image.jpg';
                }
                $product['credit_days'] = $credit_days;
                $product['is_refundable'] = $is_refundable;
                $product['refund_days'] = $refund_days;
                $product['is_replacement'] = $is_replacement;
                $product['replacement_days'] = $replacement_days;
                $product['product_link'] = $this->context->link->getProductLink($pro['product_id']);
                $products[] = $product;
            }

            /**
             * Set the values to the order_detail array
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $order_detail = array(
                'products' => $products,
                'order_id' => $order_id['id_order'],
                'reference_id' => $reference_id,
                'order_state' => $order_status,
                'order_state_color' => $order_status_color,
                //changes by vishal for adding cancel functionality
                'cancellable' => $is_cancellable,
                //changes end
                'cart_total' => $total,
                'total_paid' => $total_paid,
                'rm_customer_id' => $order_id['id_customer'],
                'order_date' => $order_date,
                'order_status_id' => $order->current_state,
            );
        }
        return $order_detail;
    }

    /**
     * Get the return history of the customer
     * @param int $id_customer
     * @param int $id_order
     * @return array
     * @date 05-02-2023
     * @commenter Prvind Panday
     */
    public function getReturnHistory($id_customer, $id_order = null)
    {
        /**
         * check if the id_order is null then get the returns of the customer
         * else get the returns of the customer for the particular order
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ($id_order == null) {
            $get_returns = 'select * from ' . _DB_PREFIX_ .
                'velsof_rm_order od where id_customer=' . (int) $id_customer . ' and
            od.id_shop=' . (int) $this->context->shop->id .
                ' order by date_update desc';
        } else {
            $get_returns = 'select * from ' . _DB_PREFIX_ .
                'velsof_rm_order od where id_customer=' . (int) $id_customer . ' and
                od.id_shop=' . (int) $this->context->shop->id . ' and
                od.id_order=' . (int) $id_order .
                ' order by date_update desc';
        }

        /**
         * get the return data
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_returns);
        $return_history = array();
        $flag = 0;

        /**
         * foreach return data get the return status and return history
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        foreach ($return_data as $return) {

            /**
             * get the return status of the return by id_rm_order
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $get_status = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_status where id_rm_order=' .
                (int) $return['id_rm_order'] . ' order by date_add desc';
            $return_status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_status);


            /**
             * get the return status name by id_rm_status
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $get_stat_name = 'select value from ' . _DB_PREFIX_ . 'velsof_return_data_lang where id_shop=' . (int) $this->context->shop->id . ' and return_data_id=' .
                (int) $return_status['id_rm_status'] . ' and id_lang=' . (int) $this->context->language->id;
            $status_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_stat_name);
            $return_history[$flag]['status'] = $status_name['value'];

            /**
             * get the product name, product attribute id and product id by id_order_detail
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $get_name = 'select product_name,product_attribute_id,product_id from ' . _DB_PREFIX_ . 'order_detail
				where id_order_detail=' . (int) $return['id_order_detail'] .
                ' and id_shop=' . (int) $this->context->shop->id;
            $pro_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_name);

            /**
             * If the product attribute id is not 0 then get the product name and product attribute name else set the product attribute name as empty
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            if ($pro_name['product_attribute_id'] != 0) {
                $name_attr = explode(' - ', $pro_name['product_name']);
                $return_history[$flag]['product_name'] = $name_attr[0];
                // changes done by kanishka kannoujia to avoid error if $name_attr[1] is not exist
                if (isset($name_attr[1])) {
                    $return_history[$flag]['product_attr'] = $name_attr[1];
                } else {
                    $return_history[$flag]['product_attr'] = '';
                }
                // changes done by kanishka kannoujia to avoid error if $name_attr[1] is not exist
            } else {
                $return_history[$flag]['product_name'] = $pro_name['product_name'];
                $return_history[$flag]['product_attr'] = '';
            }

            /**
             * create the parameters for the return history
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $parameters = array(
                'return_id' => (int) $return['id_rm_order']
            );
            $return_history[$flag]['product_id'] = $pro_name['product_id'];
            $return_history[$flag]['return_type'] = $this->l(Tools::ucfirst($return['return_type']), 'common');
            $return_history[$flag]['comment'] = $return['comment'];
            $return_history[$flag]['quantity'] = $return['quantity'];
            $pro_obj_kb = new Product($pro_name['product_id']);
            $return_history[$flag]['product_link'] = $this->context->link->getProductLink($pro_obj_kb);
            /**
             * Start Changes to fix the issue of 500 error because of the different number of parameters in the function
             * In PS8 and above, only two params are allowed in the displayDate(). So, adding the PS version check
             * NAFeb2024 displaydate
             * @date 06-02-2024
             * @modifier Nikhil Aggarwal
             */
            /*
             * Use kbDisplayDate for PS 1.7 / 8+ signature compatibility.
             * 21-07-2026
             */
            $return_history[$flag]['request_date'] = $this->kbDisplayDate($return['date_add']);
            // Changes end by Nikhil Aggarwal
            $return_history[$flag]['active'] = $return['active'];
            $pro_obj = new Product((int) $pro_name['product_id']);
            $return_history[$flag]['is_virtual'] = $pro_obj->is_virtual;
            $return_history[$flag]['slip_link'] = $this->context->link->getModuleLink(
                'returnmanager',
                'slip',
                $parameters
            );
            unset($pro_obj);
            /* changes started on 07/09
             * @author Rishabh Jain
             */
            $return_history[$flag]['id_return'] = $return['id_rm_order'];
            $return_history[$flag]['is_ticket_exist'] = (int) RmTicket::getTicketIdByReturnId($return['id_rm_order']);

            /**
             * If the ticket is exist then create the link for the ticket else create the link for the admin contact form
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            if ($return_history[$flag]['is_ticket_exist']) {
                $return_history[$flag]['ticket_link'] = $this->context->link->getModuleLink(
                    'returnmanager',
                    'customerticketview',
                    array(
                        'id_rm_ticket' => $return_history[$flag]['is_ticket_exist']
                    ),
                    (bool) Configuration::get('PS_SSL_ENABLED')
                );
            } else {
                $return_history[$flag]['ticket_link'] = $this->context->link->getModuleLink(
                    'returnmanager',
                    'admincontact',
                    array(
                        'id_return' => $return['id_rm_order']
                    ),
                    (bool) Configuration::get('PS_SSL_ENABLED')
                );
            }
            /* changes over */
            $flag++;
        }
        return $return_history;
    }

    /**
     * Function to get the product information This information will be displayed in return form
     * @date 05-02-2023
     * @commenter Prvind Panday
     */
    public function getRequestForm()
    {
        $id_infos = explode('_', Tools::getValue('id_info'));
        $product_detail_found = false;
        /**
         * $ret_typ is the array which contains the module configurations
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $ret_typ = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);

        $product_obj = new Product((int) $id_infos[2]);
        $get_default_category = $product_obj->getDefaultCategory();
        $policy_id = $this->getDefaultPolicy((int) $id_infos[2], $get_default_category);

        /**
         * $policy is the array which contains the return policy
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $qry = 'Select rd.return_data_id, rd.credit_days, rd.refund_days, rd.replacement_days,rd.replacement_min_days,rd.refund_min_days,rd.credit_min_days,
                        rdl.value as policy_title, rdl.terms, rdl.credit_message, rdl.refund_message, rdl.replacement_message
                        from ' . _DB_PREFIX_ . 'velsof_return_data as rd INNER JOIN ' .
            _DB_PREFIX_ . 'velsof_return_data_lang as rdl
                        on (rd.return_data_id = rdl.return_data_id)
                        WHERE rd. active = 1 AND rd.policy = 1 AND rdl.id_shop = ' . (int) $this->context->shop->id
            . '  AND rdl.id_lang = ' . (int) $this->context->language->id
            . '  AND rd.return_data_id = ' .
            (int) $policy_id;

        $policy = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);

        /**
         * If the policy is not found then set the template to blank 
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ($policy && is_array($policy)) {
            $product_detail_found = true;
            $order = new Order((int) $id_infos[0]);
            $order_product_detail = new OrderDetail((int) $id_infos[1]);

            $product = array();

            $product['id_order'] = $order_product_detail->id_order;
            $product['odr_reference'] = $order->reference;
            $product['id_order_detail'] = $id_infos[1];
            $product['id_product'] = $order_product_detail->product_id;
            $product['id_product_attribute'] = $order_product_detail->product_attribute_id;
            $product['product_quantity'] = $order_product_detail->product_quantity;
            $product['shipping_address'] = $this->getReturnSlipDataByLanguage(
                'address',
                $this->context->language->iso_code
            );
            // changes to be done rishabh
            // changes by rishabh jain for marketplace compatibility
            $is_seller_product = 0;
            if (Module::isEnabled('kbmarketplace') && class_exists('KbSellerProduct') && class_exists('KbSeller')) {
                $mp_config = json_decode(Configuration::get('KB_MARKETPLACE_CONFIG'), true);
                $id_seller = 0;

                if (isset($mp_config['enable_return_manager_compatibility']) && $mp_config['enable_return_manager_compatibility'] == 1) {
                    $id_seller = call_user_func(array('KbSellerProduct', 'getSellerIdByProductId'), $order_product_detail->product_id);
                    /*
                     * We have added the compatibility with our marketplace plugin and we are using the function of that module class.
                     */
                    if ($id_seller) {
                        $is_seller_product = 1;
                        /*
                         * Instantiate optional marketplace seller class dynamically for validator.
                         * 21-07-2026
                         */
                        $kb_seller_class = 'KbSeller';
                        $seller_obj = new $kb_seller_class($id_seller);
                        $seller_info = call_user_func(array($seller_obj, 'getSellerInfo'), $this->context->language->id);
                        if ($seller_info['return_address'] != '') {
                            $product['shipping_address'] = Tools::htmlentitiesDecodeUTF8($seller_info['return_address']);
                        }
                    }
                }
            }

            $p_temp = new Product($order_product_detail->product_id);
            $image_combination = $p_temp->getCombinationImages($this->context->language->id);
            if (isset($image_combination[$order_product_detail->product_attribute_id][0]['id_image'])) {
                $product['id_image'] = $order_product_detail->product_id . '-' .
                    $image_combination[$order_product_detail->product_attribute_id][0]['id_image'];
            } else {
                $get_cover_image = Product::getCover($order_product_detail->product_id);
                $product['id_image'] = $order_product_detail->product_id . '-' .
                    $get_cover_image['id_image'];
            }
            $product['link_rewrite'] = $p_temp->link_rewrite[$this->context->language->id];

            if (Context::getContext()->controller->controller_type == 'admin') {
                $link_obj = new Link();
                if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                    $product['img_path'] = 'https://' .
                        $link_obj->getImageLink($product['link_rewrite'], $product['id_image']);
                } else {
                    $product['img_path'] = 'http://' .
                        $link_obj->getImageLink($product['link_rewrite'], $product['id_image']);
                }
            }

            if (strpos($order_product_detail->product_name, ' - ')) {
                $temp = explode(' - ', $order_product_detail->product_name);
                $product['name'] = trim($temp[0]);
                $product['attributes'] = explode(',', trim($temp[1]));
            } else {
                $product['name'] = $order_product_detail->product_name;
                $product['attributes'] = array();
            }

            $sql = 'SELECT SUM(quantity) as total from ' . _DB_PREFIX_ . 'velsof_rm_order
				where id_order_detail = ' . (int) $product['id_order_detail']
                . ' AND id_shop = ' . (int) $this->context->shop->id . ' AND active != 5';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            $product['product_quantity'] = $product['product_quantity'] - $result['total'];

            // changes by rishabh jain
            $is_returnable = true;
            $current_date = date('Y-m-d H:i:s');
            $delivery_date = $order->delivery_date;

            /* Start Code Modified By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
            $policy_order_status = Configuration::get('VELSOF_RETURNMANAGER_POLICY_STATUS');
            $kbsettings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
            if (!isset($kbsettings['enable_order_status_selection_return_policy']) || $kbsettings['enable_order_status_selection_return_policy'] == 0 || $policy_order_status == null || $policy_order_status == 0) {
                $placed_order_date = $order->date_add;
                $policy_status_applicable = true;
            } else {
                $qry = 'select date_add from ' . _DB_PREFIX_ .
                    'order_history where id_order= "' . (int) $product['id_order'] . '"
                and id_order_state=' . (int) $policy_order_status;
                $order_status_date = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
                if (isset($order_status_date['date_add']) && $order_status_date['date_add'] != null) {
                    $policy_status_applicable = true;
                    $placed_order_date = $order_status_date['date_add'];
                } else {
                    $policy_status_applicable = false;
                    $placed_order_date = $order->date_add;
                }
            }
            /* End Code Modified By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
            //Code added by Harsh to list only options which are applicable as return type on 14-Apr-2017
            $calculated_min_date = date(
                'Y-m-d H:i:s',
                strtotime('+' . $policy['credit_min_days'] . ' day', strtotime($placed_order_date))
            );

            $calculated_date = date(
                'Y-m-d H:i:s',
                strtotime('+' . $policy['credit_days'] . ' day', strtotime($placed_order_date))
            );
            $credit_days = 0;
            $credit_min_days = 0;
            if ($current_date < $calculated_date) {
                $time_diff2 = strtotime($calculated_date) - strtotime($current_date);
                $credit_days = ceil(($time_diff2) / (60 * 60 * 24));
            }
            if ($current_date > $calculated_min_date) {
                $time_diff2 = strtotime($current_date) - strtotime($calculated_min_date);
                $credit_min_days = ceil(($time_diff2) / (60 * 60 * 24));
            }
            if ($policy['credit_min_days'] > 0) {
                if ($credit_days > 0 && $credit_min_days > 0) {
                    $is_creditable = true;
                }
            } else {
                if ($credit_days > 0) {
                    $is_creditable = true;
                }
            }
            //Is Refundable
            /*
             * Initialize refundable flag so isset() is not required for static analysis.
             * 21-07-2026
             */
            $is_refundable = false;
            $refund_days = 0;
            $refund_min_days = 0;
            $calculated_min_date = date(
                'Y-m-d H:i:s',
                strtotime('+' . $policy['refund_min_days'] . ' day', strtotime($placed_order_date))
            );
            $calculated_date = date(
                'Y-m-d H:i:s',
                strtotime('+' . $policy['refund_days'] . ' day', strtotime($placed_order_date))
            );
            if ($current_date < $calculated_date) {
                $time_diff1 = strtotime($calculated_date) - strtotime($current_date);
                $refund_days = ceil(($time_diff1) / (60 * 60 * 24));
            }

            if ($current_date > $calculated_min_date) {
                $time_diff2 = strtotime($current_date) - strtotime($calculated_min_date);
                $refund_min_days = ceil(($time_diff2) / (60 * 60 * 24));
            }
            if ($policy['refund_min_days'] > 0) {
                if ($refund_min_days > 0 && $refund_days > 0) {
                    $is_refundable = true;
                }
            } else {
                if ($refund_days > 0) {
                    $is_refundable = true;
                }
            }

            //Has replacement
            /*
             * Initialize replacement flag so isset() is not required for static analysis.
             * 21-07-2026
             */
            $is_replacement = false;
            $replacement_days = 0;
            $replacement_min_days = 0;
            $calculated_date = date(
                'Y-m-d H:i:s',
                strtotime('+' . $policy['replacement_days'] . ' day', strtotime($placed_order_date))
            );
            $calculated_min_date = date(
                'Y-m-d H:i:s',
                strtotime('+' . $policy['replacement_min_days'] . ' day', strtotime($placed_order_date))
            );
            if ($current_date < $calculated_date) {
                $time_diff = strtotime($calculated_date) - strtotime($current_date);
                $replacement_days = ceil(($time_diff) / (60 * 60 * 24));
            }
            if ($current_date > $calculated_min_date) {
                $time_diff2 = strtotime($current_date) - strtotime($calculated_min_date);
                $replacement_min_days = ceil(($time_diff2) / (60 * 60 * 24));
            }
            if ($policy['replacement_min_days'] > 0) {
                if ($replacement_min_days > 0 && $replacement_days > 0) {
                    $is_replacement = true;
                }
            } else {
                if ($replacement_days > 0) {
                    $is_replacement = true;
                }
            }

            $return_types = array();

            if (isset($ret_typ['refund']) && $ret_typ['refund'] == 1 && !empty($is_refundable)) {
                $return_types[] = array(
                    'text' => $this->l('Refund', 'common'),
                    'value' => 'refund',
                    'note' => $policy['refund_message']
                );
            }
            $rm_toc = $policy['terms'];

            //Get Return policy for this product
            $qry = 'Select rd.return_data_id, rd.whopayshipping, rdl.value
				from ' . _DB_PREFIX_ . 'velsof_return_data as rd
				INNER JOIN ' . _DB_PREFIX_ . 'velsof_return_data_lang as rdl on (rd.return_data_id = rdl.return_data_id)
				WHERE rd. active = 1 AND rd.reason = 1 AND rdl.id_lang = ' . (int) $this->context->language->id . ' and rdl.id_shop = ' . (int) $this->context->shop->id;
            $reasons_rs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
            $reasons = array();
            $shipp_adm = $this->l('Shipping Charge Paid By Store Owner', 'common');
            $shipp_cust = $this->l('Shipping Charge Paid By Customer', 'common');
            if ($reasons_rs && count($reasons_rs) > 0) {
                foreach ($reasons_rs as $reason) {
                    $reasons[] = array(
                        'reason_id' => $reason['return_data_id'],
                        'text' => $reason['value'],
                        'shipping_paid_by' => ($reason['whopayshipping'] == 'so') ? $shipp_adm : $shipp_cust
                    );
                }
            }

            $product['return_types'] = $return_types;
            $product['return_toc'] = $rm_toc;
            $product['reasons'] = $reasons;
            $product['customer_id'] = $id_infos[3];
            $product['policy_id'] = $id_infos[4];

            $custom_ssl_var = 0;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }

            if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                $ps_base_url = Tools::getShopDomainSsl(true);
            } else {
                $ps_base_url = Tools::getShopDomain(true);
            }
            $ret_typ['enable_image_upload'] = isset($ret_typ['enable_image_upload']) ? $ret_typ['enable_image_upload'] : 0;
            $enable_image_upload = $ret_typ['enable_image_upload'];
            /* Start: Changes done by  Rishabh on 9th July 2018 for -------- (To add options to select any of the ) */
            $address_query = 'Select * from ' . _DB_PREFIX_ . 'velsof_rm_address where active = 1';
            $address_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($address_query);
            if (isset($ret_typ['enable_address']) && $ret_typ['enable_address'] == 1) {
                $enable_address = 1;
            } else {
                $enable_address = 0;
            }
            $full_addr = array();
            if (count($address_list) == 0) {
                $enable_address = 0;
            }
            /* changes by rishabh jain
             * to disable the multiple addreees functionality
             */
            if ($is_seller_product == 1) {
                $enable_address = 0;
            }

            //            enable_address
            /* changes over */
            $full_addr = array();
            foreach ($address_list as $address) {
                $full_addr[$address['id_address']] = nl2br($address['title'] . '</br>');
                $full_addr[$address['id_address']] .= $address['address1'] . ' ';
                $full_addr[$address['id_address']] .= $address['address2'] . ' ';

                if ($address['city'] != '0') {
                    $full_addr[$address['id_address']] .= $address['city'] . ' ';
                }

                if ($address['id_state'] != 0) {
                    $query = 'select name from ' . _DB_PREFIX_ . 'state where id_state = ' . (int) $address['id_state'];
                    $full_addr[$address['id_address']] .= Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) . ' ';
                }

                $full_addr[$address['id_address']] .= $address['postcode'] . ' ';
                $query = 'select name from ' . _DB_PREFIX_ . 'country_lang where id_country = ' . (int) $address['id_country'] . ' and id_lang =' . (int) $this->context->language->id;
                $full_addr[$address['id_address']] .= Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
            }

            /**
             * Set the smarty variables for the template
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $this->context->smarty->assign('full_addr', $full_addr);
            $this->context->smarty->assign('enable_address', $enable_address);
            // changes by rishabh jain for default address
            $iso = Language::getIsoById($this->context->language->id);
            $qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_slip_data where iso_code="' . pSQL($iso) .
                '" and id_shop=' . (int) $this->context->shop->id . ' and address = "1"';
            $slip_data_default_address = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);

            /**
             * $default_address is the string which contains the default address of the customer
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            if (isset($slip_data_default_address['html_content'])) {
                $default_address = Tools::htmlentitiesDecodeUTF8($slip_data_default_address['html_content']);
            } else {
                $default_address = '';
            }
            $this->context->smarty->assign('default_address', $default_address);
            // changes over
            $this->context->smarty->assign('address_list', $address_list);

            /**
             * To provide the fucntionality of choosing the product in case of replacement to the customers.
             * @date 05-02-2023
             * @author Priyanshu Gupta
             * @commenter Prvind Panday
             */
            $sql8 = 'SELECT l.name,l.id_product,p.reference FROM `'
                . _DB_PREFIX_ . 'product_lang` as l inner join `' . _DB_PREFIX_ . 'product` as p inner join `' . _DB_PREFIX_ . 'stock_available` as sa' .
                ' on l.id_product = p.id_product and p.id_product=sa.id_product and sa.id_product_attribute=0 Where p.active = "1" and sa.quantity>0 group by l.id_product';
            $product_options = Db::getInstance()->ExecuteS($sql8);
            $product_options = array();
            /**
             * If return type is replacement then only show the product selection option.
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            if (
                isset($ret_typ['replacement']) && $ret_typ['replacement'] == 1 &&
                !empty($is_replacement) &&
                isset($ret_typ['enable_product_selection_replacement']) && $ret_typ['enable_product_selection_replacement'] == 1
            ) {
                $this->context->smarty->assign('enable_product_selection', 1);
            } else {
                $this->context->smarty->assign('enable_product_selection', 0);
            }

            /**
             * To provide the fucntionality of choosing the product in case of replacement to the customers.
             * @date 05-02-2023
             * @author Priyanshu Gupta
             * @commenter Prvind Panday
             */
            $this->context->smarty->assign(
                array(
                    'enable_image_upload' => $enable_image_upload,
                    'product' => $product,
                    'path' => $ps_base_url . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_),
                    'img_path' => $ps_base_url . __PS_BASE_URI__ . '/modules/returnmanager/views/img/',
                    'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager'),
                    'product_array' => $product_options
                )
            );

            /**
             * To get the custom fields details from the database.
             * @date 23-03-2020
             * @commenter Priyanshu Gupta
             */
            $custom_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER_CUSTOM'), true);
            $enable_custom_field = isset($ret_typ['enable_custom_field']) ? $ret_typ['enable_custom_field'] : 0;
            $id_lang_current = $this->context->language->id;
            $custom_field_block_title = $custom_data['custom_block_title'][$id_lang_current];
            $array_fields = $this->getCustomFieldsDetails($id_lang_current);
            $this->context->smarty->assign('array_fields', $array_fields);
            $this->context->smarty->assign('enable_custom_field', $enable_custom_field);
            $this->context->smarty->assign('custom_field_block_title', $custom_field_block_title);
            /* Start Code Added By Priyanshu on 12-September-2020 to implement the Specific Product Selection Functionality in related products module.
             * @date 22-03-2023
             * @commenter Prvind Panday 
             */
            $this->context->smarty->assign('kb_admin_link', $this->context->link->getModuleLink('returnmanager', 'manager', array('method' => 'ajaxproductaction', 'ajax' => true)));
            /* End Code Added By Priyanshu on 12-September-2020 to implement the Specific Product Selection Functionality */
            $template = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/rm_request_form.tpl'
            );
        } else {
            $template = '';
        }

        $arr = array(
            'detail_found' => $product_detail_found,
            'template' => $template
        );

        return $arr;
    }

    /*
     * Function getRequestCancelForm is used to get the cancel form
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param boolean $only_approved
     * @return int
     */
    public function getRequestCancelForm()
    {
        $id_infos = Tools::getValue('id_info');
        $kb_order_obj = new Order($id_infos);
        $kb_order_currency_obj = new Currency($kb_order_obj->id_currency);
        $kb_customer_obj = $kb_order_obj->getCustomer();
        $product_detail_found = false;
        $rm_settings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        if (isset($rm_settings['enable_cancel']) && $rm_settings['enable_cancel'] == 1) {
            $product_detail_found = true;

            $qry = 'select data.return_data_id, rdl.value, rdl.terms, data.editable from '
                . _DB_PREFIX_ . 'velsof_return_data as data
                    INNER JOIN ' . _DB_PREFIX_ . 'velsof_return_data_lang as rdl on
                    (data.return_data_id = rdl.return_data_id)
                    where data.cancel = "1" AND data.active="1"
                    AND rdl.id_shop=' . (int) $this->context->shop->id . ' and rdl.id_lang=' .
                (int) $this->context->language->id;
            $kb_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
            $kb_other_option = array("return_data_id" => 0, "value" => "Other");
            /**
             * To add the other option in the cancel reason.
             * @date 16-02-2023
             * @commenter Prvind Panday
             */
            array_push($kb_data, $kb_other_option);
            $this->context->smarty->assign('kb_cancel_data', $kb_data);
            $this->context->smarty->assign('kb_order_id', $id_infos);
            $this->context->smarty->assign('order_reference', $kb_order_obj->reference);
            $this->context->smarty->assign('order_paid', $this->kbFormatPrice($kb_order_obj->total_paid_tax_incl, $kb_order_currency_obj));
            $this->context->smarty->assign('cust_name', $kb_customer_obj->firstname . " " . $kb_customer_obj->lastname);
            $this->context->smarty->assign('cust_email', $kb_customer_obj->email);
            $custom_ssl_var = 0;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }

            if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                $ps_base_url = Tools::getShopDomainSsl(true);
            } else {
                $ps_base_url = Tools::getShopDomain(true);
            }
            $this->context->smarty->assign('img_path', $ps_base_url . __PS_BASE_URI__ . '/modules/returnmanager/views/img/');

            $setting = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
            /**
             * To get the default policy from the database.
             * @date 16-02-2023
             * @commenter Prvind Panday
             */
            $kb_default_policy = $setting['policy']['default'];
            $qry = 'Select rd.return_data_id, rd.credit_days, rd.refund_days, rd.replacement_days,rd.replacement_min_days,rd.refund_min_days,rd.credit_min_days,
			rdl.value as policy_title, rdl.terms, rdl.credit_message, rdl.refund_message, rdl.replacement_message
			from ' . _DB_PREFIX_ . 'velsof_return_data as rd INNER JOIN ' .
                _DB_PREFIX_ . 'velsof_return_data_lang as rdl
			on (rd.return_data_id = rdl.return_data_id)
			WHERE rd. active = 1 AND rd.policy = 1 AND rdl.id_shop = ' . (int) $this->context->shop->id
                . '  AND rd.return_data_id = ' .
                (int) $kb_default_policy;
            $policy = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
            /**
             * Start Changes to add the is_array condition before accessing the index
             * NASep2023 is_array
             * @date 15-09-2023
             * @modifier Nikhil aggarwal
             */
            if (is_array($policy) && isset($policy['terms']) && $policy['terms'] != '') {
                $this->context->smarty->assign('kb_policy', $policy['terms']);
            } else {
                $this->context->smarty->assign('kb_policy', '');
            }
            // Changes end by Nikhil
            $template = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/rm_cancel_request_form.tpl'
            );
        } else {
            $template = '';
        }

        /**
         * To return the template and the product detail found.
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        $arr = array(
            'detail_found' => $product_detail_found,
            'template' => $template
        );

        return $arr;
    }
    //changes end

    /*
     * Function to get Multiple Product Information
     * This information will be displayed in return form in case of Complete order Return Selection or multiple products selection.
     * Functionality: To implement Complete Order Return Functionality.
     * Added By Priyanshu on 23-March-2020
     * @date 16-02-2023
     * @commenter Prvind Panday
     */

    public function kbgetRequestForm()
    {
        $ret_typ = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        $ret_typ['enable_image_upload'] = isset($ret_typ['enable_image_upload']) ? $ret_typ['enable_image_upload'] : 0;
        $enable_image_upload = $ret_typ['enable_image_upload'];
        $id_order_infos = explode(',', Tools::getValue('id_info'));
        /**
         * To get the product detail.
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        $products_info = array();
        foreach ($id_order_infos as $array) {
            $id_infos = explode('_', $array);
            $product_obj = new Product((int) $id_infos[2]);
            $get_default_category = $product_obj->getDefaultCategory();
            $policy_id = $this->getDefaultPolicy((int) $id_infos[2], $get_default_category);
            /**
             * To get the policy from the database.
             * @date 16-02-2023
             * @commenter Prvind Panday
             */
            $qry = 'Select rd.return_data_id, rd.credit_days, rd.refund_days, rd.replacement_days,rd.replacement_min_days,rd.refund_min_days,rd.credit_min_days,
			rdl.value as policy_title, rdl.terms, rdl.credit_message, rdl.refund_message, rdl.replacement_message
			from ' . _DB_PREFIX_ . 'velsof_return_data as rd INNER JOIN ' .
                _DB_PREFIX_ . 'velsof_return_data_lang as rdl
			on (rd.return_data_id = rdl.return_data_id)
			WHERE rd. active = 1 AND rd.policy = 1 AND rdl.id_shop = ' . (int) $this->context->shop->id
                . '  AND rd.return_data_id = ' .
                (int) $policy_id;

            $policy = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);

            /**
             * To get the product detail if the policy is found for the product.
             * @date 16-02-2023
             * @commenter Prvind Panday
             */
            if ($policy && is_array($policy)) {
                $order = new Order((int) $id_infos[0]);
                $order_product_detail = new OrderDetail((int) $id_infos[1]);

                $product = array();

                $product['id_order'] = $order_product_detail->id_order;
                $product['odr_reference'] = $order->reference;
                $product['id_order_detail'] = $id_infos[1];
                $product['id_product'] = $order_product_detail->product_id;
                $product['id_product_attribute'] = $order_product_detail->product_attribute_id;
                $product['product_quantity'] = $order_product_detail->product_quantity;
                $product['shipping_address'] = $this->getReturnSlipDataByLanguage(
                    'address',
                    $this->context->language->iso_code
                );

                $is_seller_product = 0;

                /**
                 * To check if the product is of seller or not and if the marketplace module is installed or not.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                if (Module::isEnabled('kbmarketplace') && class_exists('KbSellerProduct') && class_exists('KbSeller')) {
                    $mp_config = json_decode(Configuration::get('KB_MARKETPLACE_CONFIG'), true);
                    $id_seller = 0;

                    if (isset($mp_config['enable_return_manager_compatibility']) && $mp_config['enable_return_manager_compatibility'] == 1) {
                        $id_seller = call_user_func(array('KbSellerProduct', 'getSellerIdByProductId'), $order_product_detail->product_id);
                        /*
                         * We have added the compatibility with our marketplace plugin and we are using the function of that module class.
                         */
                        if ($id_seller) {
                            $is_seller_product = 1;
                            /*
                             * Instantiate optional marketplace seller class dynamically for validator.
                             * 21-07-2026
                             */
                            $kb_seller_class = 'KbSeller';
                            $seller_obj = new $kb_seller_class($id_seller);
                            $seller_info = call_user_func(array($seller_obj, 'getSellerInfo'), $this->context->language->id);
                            if ($seller_info['return_address'] != '') {
                                $product['shipping_address'] = Tools::htmlentitiesDecodeUTF8($seller_info['return_address']);
                            }
                        }
                    }
                }
                $p_temp = new Product($order_product_detail->product_id);
                $image_combination = $p_temp->getCombinationImages($this->context->language->id);
                if (isset($image_combination[$order_product_detail->product_attribute_id][0]['id_image'])) {
                    $product['id_image'] = $order_product_detail->product_id . '-' .
                        $image_combination[$order_product_detail->product_attribute_id][0]['id_image'];
                } else {
                    $get_cover_image = Product::getCover($order_product_detail->product_id);
                    $product['id_image'] = $order_product_detail->product_id . '-' .
                        $get_cover_image['id_image'];
                }
                $product['link_rewrite'] = $p_temp->link_rewrite[$this->context->language->id];

                /**
                 * To get the image path of the product only if the controller is admin.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                if (Context::getContext()->controller->controller_type == 'admin') {
                    $link_obj = new Link();
                    if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                        $product['img_path'] = 'https://' .
                            $link_obj->getImageLink($product['link_rewrite'], $product['id_image']);
                    } else {
                        $product['img_path'] = 'http://' .
                            $link_obj->getImageLink($product['link_rewrite'], $product['id_image']);
                    }
                }

                if (strpos($order_product_detail->product_name, ' - ')) {
                    $temp = explode(' - ', $order_product_detail->product_name);
                    $product['name'] = trim($temp[0]);
                    $product['attributes'] = explode(',', trim($temp[1]));
                } else {
                    $product['name'] = $order_product_detail->product_name;
                    $product['attributes'] = array();
                }

                $sql = 'SELECT SUM(quantity) as total from ' . _DB_PREFIX_ . 'velsof_rm_order
				where id_order_detail = ' . (int) $product['id_order_detail']
                    . ' AND id_shop = ' . (int) $this->context->shop->id . ' AND active != 5';
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                $product['product_quantity'] = $product['product_quantity'] - $result['total'];

                $is_returnable = true;
                $current_date = date('Y-m-d H:i:s');
                $delivery_date = $order->delivery_date;
                $placed_order_date = $order->date_add;

                /**
                 * To calculate the credit days and credit min days.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $calculated_min_date = date(
                    'Y-m-d H:i:s',
                    strtotime('+' . $policy['credit_min_days'] . ' day', strtotime($placed_order_date))
                );

                $calculated_date = date(
                    'Y-m-d H:i:s',
                    strtotime('+' . $policy['credit_days'] . ' day', strtotime($placed_order_date))
                );
                $credit_days = 0;
                $credit_min_days = 0;

                /**
                 * If the delivery date is not empty then we will calculate the credit days and credit min days.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                if ($current_date < $calculated_date) {
                    $time_diff2 = strtotime($calculated_date) - strtotime($current_date);
                    $credit_days = ceil(($time_diff2) / (60 * 60 * 24));
                }
                if ($current_date > $calculated_min_date) {
                    $time_diff2 = strtotime($current_date) - strtotime($calculated_min_date);
                    $credit_min_days = ceil(($time_diff2) / (60 * 60 * 24));
                }

                /**
                 * If policy is set to credit then we will check the credit days and credit min days.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                if ($policy['credit_min_days'] > 0) {
                    if ($credit_days > 0 && $credit_min_days > 0) {
                        $is_creditable = true;
                    }
                } else {
                    if ($credit_days > 0) {
                        $is_creditable = true;
                    }
                }
                //Is Refundable

                /**
                 * To calculate the refund days and refund min days.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $refund_days = 0;
                $refund_min_days = 0;
                $calculated_min_date = date(
                    'Y-m-d H:i:s',
                    strtotime('+' . $policy['refund_min_days'] . ' day', strtotime($placed_order_date))
                );
                $calculated_date = date(
                    'Y-m-d H:i:s',
                    strtotime('+' . $policy['refund_days'] . ' day', strtotime($placed_order_date))
                );

                /**
                 * If the delivery date is not empty then we will calculate the refund days and refund min days.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                if ($current_date < $calculated_date) {
                    $time_diff1 = strtotime($calculated_date) - strtotime($current_date);
                    $refund_days = ceil(($time_diff1) / (60 * 60 * 24));
                }

                if ($current_date > $calculated_min_date) {
                    $time_diff2 = strtotime($current_date) - strtotime($calculated_min_date);
                    $refund_min_days = ceil(($time_diff2) / (60 * 60 * 24));
                }

                /**
                 * If policy is set to refund then we will set is_refundable to true.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                if ($policy['refund_min_days'] > 0) {
                    if ($refund_min_days > 0 && $refund_days > 0) {
                        $is_refundable = true;
                    }
                } else {
                    if ($refund_days > 0) {
                        $is_refundable = true;
                    }
                }


                /**
                 * To calculate the replacement days and replacement min days.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $replacement_days = 0;
                $replacement_min_days = 0;
                $calculated_date = date(
                    'Y-m-d H:i:s',
                    strtotime('+' . $policy['replacement_days'] . ' day', strtotime($placed_order_date))
                );
                $calculated_min_date = date(
                    'Y-m-d H:i:s',
                    strtotime('+' . $policy['replacement_min_days'] . ' day', strtotime($placed_order_date))
                );
                if ($current_date < $calculated_date) {
                    $time_diff = strtotime($calculated_date) - strtotime($current_date);
                    $replacement_days = ceil(($time_diff) / (60 * 60 * 24));
                }
                if ($current_date > $calculated_min_date) {
                    $time_diff2 = strtotime($current_date) - strtotime($calculated_min_date);
                    $replacement_min_days = ceil(($time_diff2) / (60 * 60 * 24));
                }

                /**
                 * If policy is set to replacement then we will set is_replacement to true.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                if ($policy['replacement_min_days'] > 0) {
                    if ($replacement_min_days > 0 && $replacement_days > 0) {
                        $is_replacement = true;
                    }
                } else {
                    if ($replacement_days > 0) {
                        $is_replacement = true;
                    }
                }


                /**
                 * To check the return type and set the return type.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $return_types = array();

                if (isset($ret_typ['refund']) && $ret_typ['refund'] == 1 && !empty($is_refundable)) {
                    $return_types[] = array(
                        'text' => $this->l('Refund', 'common'),
                        'value' => 'refund',
                        'note' => $policy['refund_message']
                    );
                }
                $rm_toc = $policy['terms'];

                /**
                 * To get the reasons for return.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $qry = 'Select rd.return_data_id, rd.whopayshipping, rdl.value
				from ' . _DB_PREFIX_ . 'velsof_return_data as rd
				INNER JOIN ' . _DB_PREFIX_ . 'velsof_return_data_lang as rdl on (rd.return_data_id = rdl.return_data_id)
				WHERE rd. active = 1 AND rd.reason = 1 AND rdl.id_lang = ' . (int) $this->context->language->id . ' and rdl.id_shop = ' . (int) $this->context->shop->id;
                $reasons_rs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
                $reasons = array();
                $shipp_adm = $this->l('Shipping Charge Paid By Store Owner', 'common');
                $shipp_cust = $this->l('Shipping Charge Paid By Customer', 'common');
                if ($reasons_rs && count($reasons_rs) > 0) {
                    foreach ($reasons_rs as $reason) {
                        $reasons[] = array(
                            'reason_id' => $reason['return_data_id'],
                            'text' => $reason['value'],
                            'shipping_paid_by' => ($reason['whopayshipping'] == 'so') ? $shipp_adm : $shipp_cust
                        );
                    }
                }

                $product['return_types'] = $return_types;
                $product['return_toc'] = $rm_toc;
                $product['reasons'] = $reasons;
                $product['customer_id'] = $id_infos[3];
                $product['policy_id'] = $id_infos[4];

                $custom_ssl_var = 0;
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                    $custom_ssl_var = 1;
                }

                if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                    $ps_base_url = Tools::getShopDomainSsl(true);
                } else {
                    $ps_base_url = Tools::getShopDomain(true);
                }

                $address_query = 'Select * from ' . _DB_PREFIX_ . 'velsof_rm_address where active = 1';
                $address_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($address_query);

                /**
                 * To check the address is enabled or not.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                if (isset($ret_typ['enable_address']) && $ret_typ['enable_address'] == 1) {
                    $enable_address = 1;
                } else {
                    $enable_address = 0;
                }
                $full_addr = array();
                if (count($address_list) == 0) {
                    $enable_address = 0;
                }

                if ($is_seller_product == 1) {
                    $enable_address = 0;
                }

                /**
                 * To get the full address for the return address.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $full_addr = array();
                foreach ($address_list as $address) {
                    $full_addr[$address['id_address']] = nl2br($address['title'] . '</br>');
                    $full_addr[$address['id_address']] .= $address['address1'] . ' ';
                    $full_addr[$address['id_address']] .= $address['address2'] . ' ';

                    if ($address['city'] != '0') {
                        $full_addr[$address['id_address']] .= $address['city'] . ' ';
                    }

                    if ($address['id_state'] != 0) {
                        $query = 'select name from ' . _DB_PREFIX_ . 'state where id_state = ' . (int) $address['id_state'];
                        $full_addr[$address['id_address']] .= Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) . ' ';
                    }

                    $full_addr[$address['id_address']] .= $address['postcode'] . ' ';
                    $query = 'select name from ' . _DB_PREFIX_ . 'country_lang where id_country = ' . (int) $address['id_country'] . ' and id_lang =' . (int) $this->context->language->id;
                    $full_addr[$address['id_address']] .= Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                }

                /**
                 * set the address to the products info
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $products_info[$id_infos[1]]['full_addr'] = $full_addr;
                $products_info[$id_infos[1]]['enable_address'] = $enable_address;
                $iso = Language::getIsoById($this->context->language->id);
                $qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_slip_data where iso_code="' . pSQL($iso) .
                    '" and id_shop=' . (int) $this->context->shop->id . ' and address = "1"';
                $slip_data_default_address = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
                if (isset($slip_data_default_address['html_content'])) {
                    $default_address = Tools::htmlentitiesDecodeUTF8($slip_data_default_address['html_content']);
                } else {
                    $default_address = '';
                }
                $products_info[$id_infos[1]]['default_address'] = $default_address;
                $products_info[$id_infos[1]]['address_list'] = $address_list;
                $products_info[$id_infos[1]]['product'] = $product;

                /**
                 * To check the product selection is enabled or not in case of replacement.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                if (
                    isset($ret_typ['replacement']) && $ret_typ['replacement'] == 1 &&
                    !empty($is_replacement) &&
                    isset($ret_typ['enable_product_selection_replacement']) && $ret_typ['enable_product_selection_replacement'] == 1
                ) {
                    $products_info[$id_infos[1]]['enable_product_selection'] = 1;
                } else {
                    $products_info[$id_infos[1]]['enable_product_selection'] = 0;
                }
            }
        }

        /*
         * Defaults when products_info is empty so Smarty assign never uses undefined vars.
         * 21-07-2026
         */
        $order_reference = '';
        $ps_base_url = $this->kbGetShopBaseUrl((bool) Configuration::get('PS_SSL_ENABLED'));
        foreach ($products_info as $product) {
            $order_reference = $product['product']['odr_reference'];
            break;
        }

        //changes by vishal on 20 july 2020 for resolving the product replacement issue

        $sql8 = 'SELECT l.name,l.id_product,p.reference FROM `'
            . _DB_PREFIX_ . 'product_lang` as l inner join `' . _DB_PREFIX_ . 'product` as p inner join `' . _DB_PREFIX_ . 'stock_available` as sa' .
            ' on l.id_product = p.id_product and p.id_product=sa.id_product and sa.id_product_attribute=0 Where p.active = "1" and sa.quantity>0 group by l.id_product';

        //changes end


        $product_options = Db::getInstance()->ExecuteS($sql8);

        $id_order_infos = json_encode($id_order_infos);

        $this->context->smarty->assign(
            array(
                'products_info' => $products_info,
                'odr_reference' => $order_reference,
                'kb_order_infos' => $id_order_infos,
                'path' => $ps_base_url . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_),
                'img_path' => $ps_base_url . __PS_BASE_URI__ . '/modules/returnmanager/views/img/',
                'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager'),
                'kb_admin_link' => $this->context->link->getModuleLink('returnmanager', 'manager', array('method' => 'ajaxproductaction', 'ajax' => true)),
                'product_array' => $product_options
            )
        );

        /*
         * Start Code Added By Vishal on 18-August-2020 to show the Custom fields on the Return request form.
         * Functionality: To implement the Custom Fields functionality on the Return Form.
         */

        $custom_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER_CUSTOM'), true);
        $enable_custom_field = isset($ret_typ['enable_custom_field']) ? $ret_typ['enable_custom_field'] : 0;
        $id_lang_current = $this->context->language->id;
        $custom_field_block_title = $custom_data['custom_block_title'][$id_lang_current];
        $array_fields = $this->getCustomFieldsDetails($id_lang_current);
        $this->context->smarty->assign('array_fields', $array_fields);
        $this->context->smarty->assign('enable_custom_field', $enable_custom_field);
        $this->context->smarty->assign('custom_field_block_title', $custom_field_block_title);
        /*
         * End Code Added By Vishal on 18-August-2020 to show the Custom fields on the Return request form.
         * Functionality: To implement the Custom Fields functionality on the Return Form.
         */

        $template = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/rm_mutiple_product_request_form.tpl'
        );

        $arr = array(
            'detail_found' => true,
            'template' => $template
        );

        return $arr;
    }

    /*
     * Function to get Custom Fields.
     * These fields will be showin on the Return Request form.
     * Functionality: To implement the Custom Fields functionality on the Return Form.
     * Added By Priyanshu on 23-March-2020
     */

    private function getCustomFieldsDetails($id_lang_current)
    {
        $id_lang = $this->context->cookie->id_lang;
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_rm_custom_fields cf ';
        $query = $query . 'JOIN ' . _DB_PREFIX_ . 'kb_rm_custom_fields_lang cfl ';
        $query = $query . 'ON cf.id_velsof_rm_custom_fields = cfl.id_velsof_rm_custom_fields ';
        $query = $query . 'WHERE active = 1 AND cfl.id_lang = ' . (int) $id_lang;

        $result_fields = Db::getInstance()->executeS($query);
        $array_fields = array();
        /**
         * To get the custom fields details. @result_fields is the array of custom fields details.
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        foreach ($result_fields as $field) {
            $id_velsof_rm_custom_fields = $field['id_velsof_rm_custom_fields'];
            /**
             * If the custom field is of type textbox, textarea, date or file then only it will be shown on the return form.
             * @date 16-02-2023
             * @commenter Prvind Panday
             */
            if ($field['type'] == 'textbox' || $field['type'] == 'textarea' || $field['type'] == 'date' || $field['type'] == 'file') {
                $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_rm_custom_fields cf ';
                $query .= 'JOIN ' . _DB_PREFIX_ . 'kb_rm_custom_fields_lang cfl ';
                $query .= 'ON cf.id_velsof_rm_custom_fields = cfl.id_velsof_rm_custom_fields ';
                $query .= 'WHERE cf.id_velsof_rm_custom_fields = ' . (int) $id_velsof_rm_custom_fields . '
					AND cfl.id_lang = ' . (int) $id_lang_current . ' AND cf.active = 1';
                $result_custom_fields_details = Db::getInstance()->executeS($query);
                $array_fields[$id_velsof_rm_custom_fields] = $result_custom_fields_details[0];
            } else {
                /**
                 * If the custom field is of type dropdown or radio then only it will be shown on the return form.
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_rm_custom_fields cf ';
                $query .= 'JOIN ' . _DB_PREFIX_ . 'kb_rm_custom_fields_lang cfl ';
                $query .= 'ON cf.id_velsof_rm_custom_fields = cfl.id_velsof_rm_custom_fields ';
                $query .= 'JOIN ' . _DB_PREFIX_ . 'kb_rm_custom_field_options_lang cfol ';
                $query .= 'ON cf.id_velsof_rm_custom_fields = cfol.id_velsof_rm_custom_fields ';
                $query .= 'WHERE cf.id_velsof_rm_custom_fields = ' . (int) $id_velsof_rm_custom_fields . '
					AND cfl.id_lang = ' . (int) $id_lang_current . ' AND cfol.id_lang = ' . (int) $id_lang_current . ' AND cf.active = 1';
                $result_custom_fields_details = Db::getInstance()->executeS($query);
                // Setting required variables
                $array_fields[$id_velsof_rm_custom_fields]['options'] = $result_custom_fields_details;
                $array_fields[$id_velsof_rm_custom_fields]['id_velsof_rm_custom_fields'] = $id_velsof_rm_custom_fields;
                $array_fields[$id_velsof_rm_custom_fields]['type'] = $result_custom_fields_details[0]['type'];
                $array_fields[$id_velsof_rm_custom_fields]['required'] = $result_custom_fields_details[0]['required'];
                $array_fields[$id_velsof_rm_custom_fields]['field_label'] = $result_custom_fields_details[0]['field_label'];
                $array_fields[$id_velsof_rm_custom_fields]['field_help_text'] = $result_custom_fields_details[0]['field_help_text'];
            }
        }
        return $array_fields;
    }

    /**
     * Function to get the address of the customer in the format.
     * @param array $address
     * @return string
     */
    public function getFormattedAddress($address)
    {
        $addr_html = '';
        foreach ($address['ordered_fields'] as $field) {
            if (!strpos(trim($field), ' ') && !strpos(trim($field), ',')) {
                $addr_html .= $address['ordered_fields_values'][trim($field)];
            } else {
                if (strpos($field, ',')) {
                    $temp = explode(',', trim($field));
                    foreach ($temp as $a1) {
                        if (!strpos(trim($a1), ' ')) {
                            $addr_html .= $address['ordered_fields_values'][trim($a1)];
                        } else {
                            $temp1 = explode(' ', trim($a1));
                            foreach ($temp1 as $x1) {
                                $addr_html .= $address['ordered_fields_values'][trim($x1)] . ' ';
                            }
                        }
                        $addr_html .= ',';
                    }
                } elseif (strpos(trim($field), ' ')) {
                    $temp = explode(' ', trim($field));
                    foreach ($temp as $a1) {
                        $addr_html .= $address['ordered_fields_values'][trim($a1)] . ' ';
                    }
                }
            }
            $addr_html .= '</br>';
        }
        return nl2br($addr_html);
    }

    /**
     * Function to submit the return request.
     * @date 16-02-2023
     * @return array
     */
    public function submitReturnRequest()
    {
        $plugin_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);

        $order = new Order(Tools::getValue('id_order'));
        /*
         * Start Code Added By Priyanshu on 23-March-2020 to validate the Custom Field Data if Custom Field functionality is enabled in the Admin panel.
         * Functionality: To implement the Custom Fields functionality on the Return Form.
         */
        $response = array();
        /*
         * End Code Added By Priyanshu on 23-March-2020 to validate the Custom Field Data if Custom Field functionality is enabled in the Admin panel.
         * Functionality: To implement the Custom Fields functionality on the Return Form.
         */
        /**
         * Added ` in the SQL query to pass the prestashop standards.
         * @date 08-04-2024
         * RKGMay2024 added_`_in_the_sql_query
         * @author Ravi Kant Gutpa
         */
        $get_days = 'select `' . bqSQL(Tools::getValue('rm_return_type')) . '_days` from ' . _DB_PREFIX_ .
            'velsof_return_data where
			return_data_id=' . (int) Tools::getValue('id_policy');
        $days_applicable = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_days);
        $get_shipping = 'select whopayshipping from ' . _DB_PREFIX_ . 'velsof_return_data where
			return_data_id=' . (int) Tools::getValue('rm_return_reason');
        $pay_shp = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_shipping);
        if ($pay_shp['whopayshipping'] == 'c') {
            $shp = 1;
        } elseif ($pay_shp['whopayshipping'] == 'so') {
            $shp = 2;
        } else {
            $shp = 0;
        }

        $data_to_update = array(
            'id_customer' => (int) Tools::getValue('id_customer'),
            'id_order' => (int) Tools::getValue('id_order'),
            'comment' => bqSQL(Tools::getValue('rm_comment')),
            'id_order_detail' => (int) Tools::getValue('id_order_detail'),
            'quantity' => (int) Tools::getValue('rm_return_quantity')
        );
        /*
         * Cast return id; null from updateRMATables becomes 0.
         * 21-07-2026
         */
        $id_order_return = (int) $this->updateRMATables('return_created', $data_to_update);
        /**
         * If image is uploaded while submiting the return form then upload the image. else set the image value to empty.
         * @date 16-02-2023
         * @commenter Prvind Panday
         */

        $image_value = '';
        if (isset($_FILES['image'])) {
            $file = $_FILES['image'];
            if (!empty($file['name'])) {
                /**
                 * Added validation on the file upload type so that no user can upload the file other than the allowed file types.
                 * @date 13-04-2024
                 * @author Ravi Kant Gutpa
                 */
                $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (in_array(strtolower($file_extension), $allowed_extensions)) {
                    $image_name = $id_order_return . '_velsof_return_' . time() . '.' . $file_extension;
                    $path = _PS_IMG_DIR_ . 'velsof_return/' . $image_name;
                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $path
                    );
                    chmod(_PS_IMG_DIR_ . 'velsof_return/' . $image_name, 0777);
                    $image_value = $image_name;
                } else {
                    // Invalid file type
                    $response['error']['general'][] = $this->l('Invalid file type. Only images and PDF files are allowed.', 'common');
                    return $response;
                }
            } else {
                $image_value = '';
            }
        }

        /**
         * Insert the return request data in the velsof_rm_order table.
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        $return_request_qry = 'insert into ' . _DB_PREFIX_ .
            'velsof_rm_order (`id_rm_order`, `id_order_return`, `id_customer`,
			`id_order`, `id_shop`, `id_lang`,
			`id_rm_policy`, `return_type`, `days_applicable`, `id_rm_reason`,`whopayshipping`, `comment`, `image_path`,
            `id_order_detail`, `quantity`, `date_add`,
			`date_update`) values ("", ' . (int) $id_order_return . ', ' . (int) Tools::getValue('id_customer') . ', '
            . (int) Tools::getValue('id_order') . ', ' . (int) $this->context->shop->id .
            ', ' . (int) $this->context->language->id . ','
            . (int) Tools::getValue('id_policy') . ', "' . pSQL(Tools::getValue('rm_return_type')) . '",
			' . (int) $days_applicable[Tools::getValue('rm_return_type') . '_days'] .
            ',' . (int) Tools::getValue('rm_return_reason') . ',' . (int) $shp . ',
            "' . pSQL('<pre>' . Tools::getValue('rm_comment') . '</pre>') . '","' . pSQL($image_value) . '" ,' .
            (int) Tools::getValue('id_order_detail') . ',' .
            (int) Tools::getValue('rm_return_quantity') . ', now(), now())';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($return_request_qry);
        $return_id = Db::getInstance()->Insert_ID();

        /* added selected address into new table by rishabh jain */
        $return_status_qry = 'insert into ' . _DB_PREFIX_ . 'velsof_rm_return_address (`id_return`, `id_address`) values (' . (int) $return_id . ', ' . (int) Tools::getValue('rm_return_address') . ')';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($return_status_qry);
        /* changes over */
        $return_status_qry = 'insert into ' . _DB_PREFIX_ . 'velsof_rm_status (`id_rm_order`, `id_rm_status`,
			`date_add`) values (' . (int) $return_id . ', ' . (int) $plugin_data['status']['default'] . ', now())';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($return_status_qry);

    

        /**
         * Ordered products of the order. products array contains the product id, product attribute id, product quantity.
         * @date 16-02-2023
         * @commenter Prvind Panday 
         */
        $ordered_products = $this->kbGetOrderCartProducts($order);
        $products = array();
        $returned_product = array();
        foreach ($ordered_products as $pro) {
            $product = array();
            $p_temp = new Product($pro['product_id']);
            $image_combination = $p_temp->getCombinationImages($this->context->language->id);
            if (isset($image_combination[$pro['product_attribute_id']][0]['id_image'])) {
                $product['id_image'] = $pro['product_id'] . '-' .
                    $image_combination[$pro['product_attribute_id']][0]['id_image'];
            } else {
                $get_cover_image = Product::getCover($pro['product_id']);
                $product['id_image'] = $pro['product_id'] . '-' . $get_cover_image['id_image'];
            }
            $product['link_rewrite'] = $p_temp->link_rewrite[$this->context->language->id];

            /**
             * If the request is from the admin panel then we need to add the http or https in the image path.
             * @date 16-02-2023
             * @commenter Prvind Panday
             */
            if (Context::getContext()->controller->controller_type == 'admin') {
                $link_obj = new Link();
                if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                    $product['img_path'] = 'https://' . $link_obj->getImageLink(
                        $product['link_rewrite'],
                        $product['id_image']
                    );
                } else {
                    $product['img_path'] = 'http://' . $link_obj->getImageLink(
                        $product['link_rewrite'],
                        $product['id_image']
                    );
                }
            }

            if (strpos($pro['product_name'], ' - ')) {
                $temp = explode(' - ', $pro['product_name']);
                $product['name'] = trim($temp[0]);
                $product['attributes'] = explode(',', trim($temp[1]));
            } else {
                $product['name'] = $pro['product_name'];
                $product['attributes'] = array();
            }
            $product['quantity'] = $pro['product_quantity'] - $pro['product_quantity_return'];

            if (Configuration::get('PS_TAX')) {
                $product['unit_price'] = $pro['unit_price_tax_incl'];
            } else {
                $product['unit_price'] = $pro['unit_price_tax_excl'];
            }

            $products[] = $product;

            if ($pro['id_order_detail'] == Tools::getValue('id_order_detail')) {
                $product['quantity'] = Tools::getValue('rm_return_quantity');
                $returned_product = $product;
            }
        }
        /*
        * Pre-format returned product price for success tpl (Tools::displayPrice unavailable on newer PS).
        * 21-07-2026
        */
        $kb_return_currency = new Currency($order->id_currency);
        if (isset($returned_product['unit_price'])) {
            $returned_product['unit_price_display'] = $this->kbFormatPrice(
                $returned_product['unit_price'],
                $kb_return_currency
            );
        }
        $success_string = '';
        $success_msg = '';
        /**
         * If the return type is credit then we need to show the credit message, else if the return type is refund then we need to show the refund message else if the return type is replacement then we need to show the replacement message.
         * @date 16-02-2023
         * @commenter Prvind Panday 
         */

        if (Tools::getValue('rm_return_type') == 'refund') {
            $success_string = sprintf(
                '%s ' . $this->l('request successfully created', 'common'),
                $this->l('Refund', 'common')
            );
            $success_msg = $this->getMessageByName('refund', $this->context->language->iso_code);
        }


        $get_reason = 'select l.*, ret.* from ' . _DB_PREFIX_ . 'velsof_return_data_lang l,' .
            _DB_PREFIX_ . 'velsof_return_data ret where
			ret.return_data_id=l.return_data_id and l.id_shop=' . (int) $this->context->shop->id .
            ' and ret.return_data_id=' . (int) Tools::getValue('rm_return_reason') . ' and l.id_lang=' . (int) $this->context->language->id;

        $return_reason = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_reason);

        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $ps_base_url = Tools::getShopDomainSsl(true);
        } else {
            $ps_base_url = Tools::getShopDomain(true);
        }
        // changes done by kanishka kannoujia to replace order details woth return details
        $address_query = 'Select ra.* from ' . _DB_PREFIX_ . 'velsof_rm_address ra INNER JOIN ' . _DB_PREFIX_ . 'velsof_rm_return_address rra ON ra.id_address = rra.id_address where rra.id_return = ' . (int) $return_id;
        $return_address = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($address_query);
        if (empty($return_address)) {
            $slip = 'SELECT html_content FROM `' . _DB_PREFIX_ . 'velsof_return_slip_data`WHERE `address` = 1';
            $return_address = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($slip);
            $returned_address = Tools::htmlentitiesDecodeUTF8($return_address[0]['html_content']);
        } else {
            $state = new State();
            $country = new Country();
            $state_name = $state->getNameById((int) $return_address[0]['id_state']);
            $country_name = $country->getNameById((int) $this->context->language->id, (int) $return_address[0]['id_country']);

            $address = $return_address[0]['title'] . '</br>' . $return_address[0]['address1'];
            if ($return_address[0]['address2'] != '') {
                $address .= '</br>' . $return_address[0]['address2'] . ' ' . $return_address[0]['city'];
            } else {
                $address .= ' ' . $return_address[0]['city'];
            }
            $returned_address = $address . '</br>' . $state_name . ' ' . $return_address[0]['postcode'] . ' ' . $country_name;
        }

        $returned_address = nl2br(strip_tags($returned_address));

        // changes done by kanishka kannoujia to replace order details woth return details

        $this->context->smarty->assign(
            array(
                'products' => $products,
                'returned_product' => $returned_product,
                //'shipping_address' => $shipping_address,
                'returned_address' => $returned_address,
                // changes by rishabh jain for order history page on 16th July 2019
                'id_order' => (int) Tools::getValue('id_order'),
                //'sub_total' => $sub_total,
                //'shipping_charge' => $shipping_charge,
                //'order_total' => $order_total,
                'currency' => $kb_return_currency,
                'kb_return_id' => $return_id,
                'customer_commet' => '<pre>' . trim(Tools::getValue('rm_comment')) . '</pre>',
                'success_message' => $success_string,
                'img_path' => $ps_base_url . __PS_BASE_URI__ . '/modules/returnmanager/views/img/',
                'success_msg' => $success_msg,
                'return_reason' => $return_reason['value'],
                'path' => $ps_base_url . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_),
                'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager')
            )
        );

        $return_data = array(
            'return_id' => $return_id,
            'order_reference' => $order->reference,
            'id_order' => (int) Tools::getValue('id_order'),
            'id_customer' => (int) Tools::getValue('id_customer')
        );
        /**
         * Send email to customer and admin for new return request.
         * @date 16-02-2023
         * @commenter Prvind Panday
         */

        $this->sendNotificationEmail('new_ret_cust', $return_data);
        $this->sendNotificationEmail('new_ret_adm', $return_data);
        $arr = array(
            'template' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'returnmanager/views/templates/front/rm_return_submit_success.tpl'),
            'kb_return_id' => $return_id
        );

        return $arr;
    }

    /*
     * Function called in case of cancel Request form is submitted.
     * Functionality: To implement Order Cancel Functionality.
     * Added By Priyanshu on 23-March-2020
     */

    //changes by vishal for adding cancel functionality
    public function submitCancelRequest()
    {
        $plugin_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        $order = new Order(Tools::getValue('id_order'));
        $return_request_qry = 'insert into ' . _DB_PREFIX_ .
            'velsof_rm_cancel (`id_rm_cancel`, `id_order_return`, `id_customer`,
                        `id_order`, `id_shop`, `id_lang`,
                        `id_cancel_reason`,`rm_other_reason`, `comment`,`date_add`,
                        `date_update`) values ("", ' . (int) Tools::getValue('id_order') . ', ' . (int) $order->id_customer . ', '
            . (int) Tools::getValue('id_order') . ', ' . (int) Context::getcontext()->shop->id .
            ', ' . (int) Context::getcontext()->language->id . ','
            . (int) pSQL(Tools::getValue('rm_return_type')) . ', "' . pSQL(Tools::getValue('rm_reason')) . '","' . pSQL(Tools::getValue('rm_comment')) . '",now(), now())';

        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($return_request_qry);
        $cancel_id = Db::getInstance()->Insert_ID();

        $temp_address = new Address($order->id_address_delivery);
        $address = array();
        $address['ordered_fields'] = AddressFormat::getOrderedAddressFields($temp_address->id_country);
        $address['ordered_fields_values'] = AddressFormat::getFormattedAddressFieldsValues(
            $temp_address,
            $address['ordered_fields']
        );
        $shipping_address = $this->getFormattedAddress($address);
        if (Configuration::get('PS_TAX')) {
            $sub_total = $order->total_products_wt;
            if ($order->total_shipping > 0) {
                $shipping_charge = $order->total_shipping_tax_incl;
            } else {
                $shipping_charge = 0;
            }
            $order_total = $order->total_paid_tax_incl;
        } else {
            $sub_total = $order->total_products;
            if ($order->total_shipping > 0) {
                $shipping_charge = $order->total_shipping_tax_excl;
            } else {
                $shipping_charge = 0;
            }
            $order_total = $order->total_paid_tax_excl;
        }

        $ordered_products = $this->kbGetOrderCartProducts($order);
        $products = array();
        $returned_product = array();
        /*
        * Currency object for cancel success price formatting (no Tools::displayPrice in tpl).
        * 21-07-2026
        */
        $kb_order_currency_obj = new Currency($order->id_currency);
        foreach ($ordered_products as $pro) {
            $product = array();
            $p_temp = new Product($pro['product_id']);
            $image_combination = $p_temp->getCombinationImages($this->context->language->id);
            if (isset($image_combination[$pro['product_attribute_id']][0]['id_image'])) {
                $product['id_image'] = $pro['product_id'] . '-' .
                    $image_combination[$pro['product_attribute_id']][0]['id_image'];
            } else {
                $get_cover_image = Product::getCover($pro['product_id']);
                $product['id_image'] = $pro['product_id'] . '-' . $get_cover_image['id_image'];
            }
            $product['link_rewrite'] = $p_temp->link_rewrite[$this->context->language->id];

            if (Context::getContext()->controller->controller_type == 'admin') {
                $link_obj = new Link();
                if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                    $product['img_path'] = 'https://' . $link_obj->getImageLink(
                        $product['link_rewrite'],
                        $product['id_image']
                    );
                } else {
                    $product['img_path'] = 'http://' . $link_obj->getImageLink(
                        $product['link_rewrite'],
                        $product['id_image']
                    );
                }
            }

            if (strpos($pro['product_name'], ' - ')) {
                $temp = explode(' - ', $pro['product_name']);
                $product['name'] = trim($temp[0]);
                $product['attributes'] = explode(',', trim($temp[1]));
            } else {
                $product['name'] = $pro['product_name'];
                $product['attributes'] = array();
            }
            $product['quantity'] = $pro['product_quantity'] - $pro['product_quantity_return'];

            if (Configuration::get('PS_TAX')) {
                $product['unit_price'] = $pro['unit_price_tax_incl'];
            } else {
                $product['unit_price'] = $pro['unit_price_tax_excl'];
            }
            /*
            * Pre-format unit price for cancel success Smarty template.
            * 21-07-2026
            */
            $product['unit_price_display'] = $this->kbFormatPrice(
                $product['unit_price'],
                $kb_order_currency_obj
            );

            $products[] = $product;
        }

        $success_string = '';
        $success_msg = '';

        $success_string = sprintf(
            '%s ' . $this->l('request successfully created', 'common'),
            $this->l('Cancel', 'common')
        );
        $success_msg = $this->getMessageByName('cancel', $this->context->language->iso_code);

        if (Tools::getValue('rm_return_type') != 0) {
            $get_reason = 'select l.value from ' . _DB_PREFIX_ . 'velsof_return_data_lang l,' .
                _DB_PREFIX_ . 'velsof_return_data ret where
			ret.return_data_id=l.return_data_id and l.id_shop=' . (int) $this->context->shop->id .
                ' and ret.return_data_id=' . (int) Tools::getValue('rm_return_type') . ' and l.id_lang=' . (int) $this->context->language->id;

            $return_reason = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_reason);
        } else {
            $return_reason['value'] = trim(Tools::getValue('rm_reason'));
        }

        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $ps_base_url = Tools::getShopDomainSsl(true);
        } else {
            $ps_base_url = Tools::getShopDomain(true);
        }
        $this->context->smarty->assign(
            array(
                'products' => $products,
                'returned_product' => $returned_product,
                'shipping_address' => $shipping_address,
                // changes by rishabh jain for order history page on 16th July 2019
                'id_order' => (int) Tools::getValue('id_order'),
                'sub_total' => $this->kbFormatPrice($sub_total, $kb_order_currency_obj),
                'shipping_charge' => $shipping_charge,
                /*
                * Pre-format shipping for cancel success tpl (Tools::displayPrice removed).
                * 21-07-2026
                */
                'shipping_charge_display' => $this->kbFormatPrice($shipping_charge, $kb_order_currency_obj),
                'order_total' => $this->kbFormatPrice($order_total, $kb_order_currency_obj),
                'currency' => $kb_order_currency_obj,
                'return_id' => $cancel_id,
                'customer_commet' => '<pre>' . trim(Tools::getValue('rm_comment')) . '</pre>',
                'success_message' => $success_string,
                'img_path' => $ps_base_url . __PS_BASE_URI__ . '/modules/returnmanager/views/img/',
                'success_msg' => $success_msg,
                'return_reason' => $return_reason['value'],
                'path' => $ps_base_url . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_),
                'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager')
            )
        );
        $return_data = array(
            'cancel_id' => $cancel_id,
            'order_reference' => $order->reference,
            'id_order' => (int) Tools::getValue('id_order'),
            'id_customer' => (int) $order->id_customer
        );

        /**
         * Send email to customer and admin for new cancel request
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        $this->sendNotificationEmail('new_cancel_cust', $return_data, array(), 1);
        $this->sendNotificationEmail('new_cancel_adm', $return_data, array(), 1);
        $arr = array(
            'template' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'returnmanager/views/templates/front/rm_cancel_submit_success.tpl'),
            'return_id' => $cancel_id
        );

        return $arr;
    }

    //function end

    /*
     * Function called in case of Complete Order or Multiple Products Return Request form is submitted.
     * Functionality: To implement Complete Order Return Functionality.
     * Added By Priyanshu on 23-March-2020
     */

    public function submitReturnMultipleRequest()
    {
        $plugin_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        /*
         * Guard invalid/missing kb_order_infos so foreach never receives null.
         * 21-07-2026
         */
        $id_order_infos = json_decode(Tools::getValue('kb_order_infos'), true);
        if (!is_array($id_order_infos) || empty($id_order_infos)) {
            return array(
                'error' => array(
                    'general' => array(
                        $this->l('Unable to process return request. Please select products again.', 'common'),
                    ),
                ),
            );
        }
        //changes by vishal for adding validation for custom fields on 26 august 2020
        $response = array();
        /*
         * Initialize vars used after the product loop for Smarty assign.
         * 21-07-2026
         */
        $products = array();
        $kb_currency = $this->context->currency;
        $ps_base_url = $this->kbGetShopBaseUrl((bool) Configuration::get('PS_SSL_ENABLED'));
        //changes end
        $order_total = 0;

        /**
         * Get the order details for which the return request is being made.
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        $returned_product_details = array();
        foreach ($id_order_infos as $array) {
            $id_infos = explode('_', $array);
            $kb_product_id = $id_infos[1];

            $order = new Order(Tools::getValue('id_order_' . $kb_product_id));
            $response = array();
            $get_days = 'select ' . Tools::getValue('rm_return_type_' . $kb_product_id) . '_days from ' . _DB_PREFIX_ .
                'velsof_return_data where
			return_data_id=' . (int) Tools::getValue('id_policy_' . $kb_product_id);
            $days_applicable = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_days);
            $get_shipping = 'select whopayshipping from ' . _DB_PREFIX_ . 'velsof_return_data where
			return_data_id=' . (int) Tools::getValue('rm_return_reason_' . $kb_product_id);
            $pay_shp = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_shipping);
            if ($pay_shp['whopayshipping'] == 'c') {
                $shp = 1;
            } elseif ($pay_shp['whopayshipping'] == 'so') {
                $shp = 2;
            } else {
                /*
                 * Default shipping payer when reason has no whopayshipping value.
                 * 21-07-2026
                 */
                $shp = 0;
            }
            $data_to_update = array(
                'id_customer' => (int) Tools::getValue('id_customer_' . $kb_product_id),
                'id_order' => (int) Tools::getValue('id_order_' . $kb_product_id),
                'comment' => Tools::getValue('rm_comment_' . $kb_product_id),
                'id_order_detail' => (int) Tools::getValue('id_order_detail_' . $kb_product_id),
                'quantity' => (int) Tools::getValue('rm_return_quantity_' . $kb_product_id)
            );
            /*
             * Cast return id; null from updateRMATables becomes 0.
             * 21-07-2026
             */
            $id_order_return = (int) $this->updateRMATables('return_created', $data_to_update);
            //          changes done by Kanishka Kannoujia on 18-06-2022 to add upload image functionality in case of complete order return
            $image_value = '';
            if (!empty($_FILES)) {
                $name = 'rm_return_image_' . $kb_product_id;
                $file = $_FILES[$name];
                if (!empty($file['name'])) {
                    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $image_name = $id_order_return . '_velsof_return_' . time() . '.' . $file_extension;
                    $path = _PS_IMG_DIR_ . 'velsof_return/' . $image_name;
                    move_uploaded_file(
                        $file['tmp_name'],
                        $path
                    );
                    chmod(_PS_IMG_DIR_ . 'velsof_return/' . $image_name, 0777);
                    $image_value = $image_name;
                } else {
                    $image_value = '';
                }
            }
            //          changes done by Kanishka Kannoujia on 18-06-2022 to add upload image functionality in case of complete order return
            $return_request_qry = 'insert into ' . _DB_PREFIX_ .
                'velsof_rm_order (`id_rm_order`, `id_order_return`, `id_customer`,
			`id_order`, `id_shop`, `id_lang`,
			`id_rm_policy`, `return_type`, `days_applicable`, `id_rm_reason`,`whopayshipping`, `comment`, `image_path`,
            `id_order_detail`, `quantity`, `date_add`,
			`date_update`) values ("", ' . (int) $id_order_return . ', ' . (int) Tools::getValue('id_customer_' . $kb_product_id) . ', '
                . (int) Tools::getValue('id_order_' . $kb_product_id) . ', ' . (int) $this->context->shop->id .
                ', ' . (int) $this->context->language->id . ','
                . (int) Tools::getValue('id_policy_' . $kb_product_id) . ', "' . pSQL(Tools::getValue('rm_return_type_' . $kb_product_id)) . '",
			' . (int) $days_applicable[Tools::getValue('rm_return_type_' . $kb_product_id) . '_days'] .
                ',' . (int) Tools::getValue('rm_return_reason_' . $kb_product_id) . ',' . (int) $shp . ',
            "' . pSQL('<pre>' . Tools::getValue('rm_comment_' . $kb_product_id) . '</pre>') . '","' . pSQL($image_value) . '" ,' .
                (int) Tools::getValue('id_order_detail_' . $kb_product_id) . ',' .
                (int) Tools::getValue('rm_return_quantity_' . $kb_product_id) . ', now(), now())';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($return_request_qry);
            $return_id = Db::getInstance()->Insert_ID();

            $return_status_qry = 'insert into ' . _DB_PREFIX_ . 'velsof_rm_return_address (`id_return`, `id_address`) values (' . (int) $return_id . ', ' . (int) Tools::getValue('rm_return_address_' . $kb_product_id) . ')';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($return_status_qry);

            $return_status_qry = 'insert into ' . _DB_PREFIX_ . 'velsof_rm_status (`id_rm_order`, `id_rm_status`,
			`date_add`) values (' . (int) $return_id . ', ' . (int) $plugin_data['status']['default'] . ', now())';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($return_status_qry);


            //changes end
            // changes done by kanishka kannoujia to replace order details woth return details

            // changes done by kanishka kannoujia to replace order details woth return details
            if (Configuration::get('PS_TAX')) {
                if ($order->total_shipping > 0) {
                    $shipping_charge = $order->total_shipping_tax_incl;
                } else {
                    $shipping_charge = 0;
                }
            } else {
                if ($order->total_shipping > 0) {
                    $shipping_charge = $order->total_shipping_tax_excl;
                } else {
                    $shipping_charge = 0;
                }
            }

            /**
             * Get the order products. $products array contains the product details
             * @date 16-02-2023
             * @commenter Prvind Panday
             */
            $ordered_products = $this->kbGetOrderCartProducts($order);
            $products = array();
            $returned_product = array();
            $payByStoreOwner = '';
            foreach ($ordered_products as $pro) {
                $product = array();
                $p_temp = new Product($pro['product_id']);
                $image_combination = $p_temp->getCombinationImages($this->context->language->id);
                if (isset($image_combination[$pro['product_attribute_id']][0]['id_image'])) {
                    $product['id_image'] = $pro['product_id'] . '-' .
                        $image_combination[$pro['product_attribute_id']][0]['id_image'];
                } else {
                    $get_cover_image = Product::getCover($pro['product_id']);
                    $product['id_image'] = $pro['product_id'] . '-' . $get_cover_image['id_image'];
                }
                $product['link_rewrite'] = $p_temp->link_rewrite[$this->context->language->id];

                if (Context::getContext()->controller->controller_type == 'admin') {
                    $link_obj = new Link();
                    if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                        $product['img_path'] = 'https://' . $link_obj->getImageLink(
                            $product['link_rewrite'],
                            $product['id_image']
                        );
                    } else {
                        $product['img_path'] = 'http://' . $link_obj->getImageLink(
                            $product['link_rewrite'],
                            $product['id_image']
                        );
                    }
                }

                if (strpos($pro['product_name'], ' - ')) {
                    $temp = explode(' - ', $pro['product_name']);
                    $product['name'] = trim($temp[0]);
                    $product['attributes'] = explode(',', trim($temp[1]));
                } else {
                    $product['name'] = $pro['product_name'];
                    $product['attributes'] = array();
                }
                $product['quantity'] = $pro['product_quantity'] - $pro['product_quantity_return'];

                if (Configuration::get('PS_TAX')) {
                    $product['unit_price'] = $pro['unit_price_tax_incl'];
                } else {
                    $product['unit_price'] = $pro['unit_price_tax_excl'];
                }

                $products[] = $product;

                if ($pro['id_order_detail'] == Tools::getValue('id_order_detail_' . $kb_product_id)) {
                    $product['quantity'] = Tools::getValue('rm_return_quantity_' . $kb_product_id);
                    $returned_product = $product;
                }
            }

            $success_string = '';
            $success_msg = '';

            /**
             * Get the success message based on the return type. If the return type is credit or refund or replacement.
             * @date 16-02-2023
             * @commenter Prvind Panday
             */

            if (Tools::getValue('rm_return_type_' . $kb_product_id) == 'refund') {
                $success_string = sprintf(
                    '%s ' . $this->l('request successfully created', 'common'),
                    $this->l('Refund', 'common')
                );
                $success_msg = $this->getMessageByName('refund', $this->context->language->iso_code);
            }

            $get_reason = 'select l.*, ret.* from ' . _DB_PREFIX_ . 'velsof_return_data_lang l,' .
                _DB_PREFIX_ . 'velsof_return_data ret where
			ret.return_data_id=l.return_data_id and l.id_shop=' . (int) $this->context->shop->id .
                ' and ret.return_data_id=' . (int) Tools::getValue('rm_return_reason_' . $kb_product_id) . ' and l.id_lang=' . (int) $this->context->language->id;

            $return_reason = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_reason);

            $custom_ssl_var = 0;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }

            if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                $ps_base_url = Tools::getShopDomainSsl(true);
            } else {
                $ps_base_url = Tools::getShopDomain(true);
            }

            $return_data = array(
                'return_id' => $return_id,
                'order_reference' => $order->reference,
                'id_order' => (int) Tools::getValue('id_order_' . $kb_product_id),
                'id_customer' => (int) Tools::getValue('id_customer_' . $kb_product_id)
            );
            $this->sendNotificationEmail('new_ret_cust', $return_data);
            $this->sendNotificationEmail('new_ret_adm', $return_data);

            /*
            * Pre-format unit price for success tpl; Tools::displayPrice() removed in newer PrestaShop.
            * 21-07-2026
            */
            $kb_currency = new Currency($order->id_currency);
            if (isset($returned_product['unit_price'])) {
                $returned_product['unit_price_display'] = $this->kbFormatPrice(
                    $returned_product['unit_price'],
                    $kb_currency
                );
            }
            $returned_product_details[$kb_product_id]['returned_product'] = $returned_product;
            $returned_product_details[$kb_product_id]['id_order'] = (int) Tools::getValue('id_order_' . $kb_product_id);
            $returned_product_details[$kb_product_id]['return_id'] = $return_id;
            $returned_product_details[$kb_product_id]['customer_commet'] = '<pre>' . trim(Tools::getValue('rm_comment_' . $kb_product_id)) . '</pre>';
            $returned_product_details[$kb_product_id]['success_message'] = $success_string;
            $returned_product_details[$kb_product_id]['success_msg'] = $success_msg;
            $returned_product_details[$kb_product_id]['return_reason'] = $return_reason['value'];

            // changes done by kanishka kannoujia to replace order details woth return details
            $address_query = 'Select ra.* from ' . _DB_PREFIX_ . 'velsof_rm_address ra INNER JOIN ' . _DB_PREFIX_ . 'velsof_rm_return_address rra ON ra.id_address = rra.id_address where rra.id_return = ' . (int)$return_id;
            $return_address = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($address_query);
            if (empty($return_address)) {
                $slip = 'SELECT html_content FROM `' . _DB_PREFIX_ . 'velsof_return_slip_data`WHERE `address` = 1';
                $return_address = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($slip);
                $returned_address = Tools::htmlentitiesDecodeUTF8($return_address[0]['html_content']);
            } else {
                $state = new State();
                $country = new Country();
                $state_name = $state->getNameById((int) $return_address[0]['id_state']);
                $country_name = $country->getNameById((int) $this->context->language->id, (int) $return_address[0]['id_country']);

                $address = $return_address[0]['title'] . '</br>' . $return_address[0]['address1'];
                if ($return_address[0]['address2'] != '') {
                    $address .= '</br>' . $return_address[0]['address2'] . ' ' . $return_address[0]['city'];
                } else {
                    $address .= ' ' . $return_address[0]['city'];
                }
                $returned_address = $address . '</br>' . $state_name . ' ' . $return_address[0]['postcode'] . ' ' . $country_name;
            }
            $returned_product_details[$kb_product_id]['returned_address'] = nl2br(strip_tags($returned_address));

            // changes done by kanishka kannoujia to replace order details woth return details
        }

        $this->context->smarty->assign(
            array(
                'products' => $products,
                'returned_product_details' => $returned_product_details,
                'currency' => $kb_currency,
                'img_path' => $ps_base_url . __PS_BASE_URI__ . '/modules/returnmanager/views/img/',
                'path' => $ps_base_url . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_),
                'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager')
            )
        );

        $arr = array(
            'template' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'returnmanager/views/templates/front/rm_multiple_return_submit_success.tpl'),
            'return_id' => $returned_product_details
        );
        return $arr;
    }


    /**
     * Function to get the return status history
     * @param int $return_id
     * @return array
     * @date 16-02-2023
     * @commenter Prvind Panday
     */
    public function getReturnData($return_id)
    {
        $status_history = array();
        $flag = 0;

        /**
         * Get the status history of the return request
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        $status_qry = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_status where id_rm_order=' .
            (int) $return_id . ' order by date_add desc';
        $status_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($status_qry);
        foreach ($status_data as $status) {
            $get_stat_name = 'select value from ' . _DB_PREFIX_ . 'velsof_return_data_lang where id_shop=' . (int) $this->context->shop->id . ' and return_data_id=' .
                (int) $status['id_rm_status'];
            $status_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_stat_name);
            $status_history[$flag]['status'] = $status_name['value'];
            /**
             * Start Changes to fix the issue of 500 error because of the different number of parameters in the function
             * In PS8 and above, only two params are allowed in the displayDate(). So, adding the PS version check
             * NAFeb2024 displaydate
             * @date 06-02-2024
             * @modifier Nikhil Aggarwal
             */
            /*
             * Use kbDisplayDate for PS 1.7 / 8+ signature compatibility.
             * 21-07-2026
             */
            $status_history[$flag]['date'] = $this->kbDisplayDate($status['date_add'], true);
            // Changes end by Nikhil Aggarwal
            $flag++;
        }

        $get_return = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_order od where id_rm_order=' .
            (int) $return_id . ' and
            od.id_shop=' . (int) $this->context->shop->id;
        $return = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return);

        $get_reason_name = 'select l.value from ' . _DB_PREFIX_ . 'velsof_return_data_lang l,' . _DB_PREFIX_ .
            'velsof_return_data d where
            l.id_shop=' . (int) $this->context->shop->id . ' and d.return_data_id=' .
            (int) $return['id_rm_reason'] . ' and
            l.return_data_id=d.return_data_id';
        $status_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_reason_name);
        $return_data = array();
        $return_data['reason'] = $status_name['value'];
        $return_data['return_id'] = $return['id_rm_order'];
        $get_name = 'select product_name,product_attribute_id,product_id,unit_price_tax_incl
            from ' . _DB_PREFIX_ . 'order_detail where id_order_detail=' . (int) $return['id_order_detail'] .
            ' and id_shop=' . (int) $this->context->shop->id;
        $pro_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_name);
        $product_image = Image::getImages(
            $this->context->language->id,
            $pro_name['product_id'],
            $pro_name['product_attribute_id']
        );
        if (isset($product_image[0]['id_image'])) {
            $image = new Image($product_image[0]['id_image']);
        } else {
            $product_image = Image::getImages($this->context->language->id, $pro_name['product_id']);
            if (isset($product_image[0]['id_image'])) {
                $image = new Image($product_image[0]['id_image']);
            } else {
                $image = 'modules/returnmanager/views/img/No-image.jpg';
            }
        }
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $ps_base_url = Tools::getShopDomainSsl(true);
        } else {
            $ps_base_url = Tools::getShopDomain(true);
        }
        if (Validate::isLoadedObject($image)) {
            $return_data['pro_img'] = $ps_base_url . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
        } else {
            $return_data['pro_img'] = $ps_base_url . __PS_BASE_URI__ . $image;
        }
        if ($pro_name['product_attribute_id'] != 0) {
            $name_attr = explode(' - ', $pro_name['product_name']);
            $return_data['product_name'] = $name_attr[0];
            /**
             * Start changes to fix the undefined index error on the slip controller for $name_attr[1]
             * Added isset condition before accessing the index
             * NASep2023 slip_controller
             * @date 20-09-2023
             * @modifier Nikhil Aggarwal
             */
            if (isset($name_attr[1])) {
                $return_data['product_attr'] = $name_attr[1];
            } else {
                $return_data['product_attr'] = '';
            }
            // Changes end by Nikhil
        } else {
            $return_data['product_name'] = $pro_name['product_name'];
            $return_data['product_attr'] = '';
        }

        /**
         * Creating customer obj and order obj to get the customer name and email
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        $cust_obj = new Customer($return['id_customer']);
        $odr_obj = new Order($return['id_order']);
        $return_data['cust_name'] = $cust_obj->firstname . ' ' . $cust_obj->lastname;
        $return_data['email'] = $cust_obj->email;
        $return_data['product_link'] = $this->context->link->getProductLink($pro_name['product_id']);
        $return_data['order_reference'] = $odr_obj->reference;

        // changes done by sandeep chauhan
        $kb_order_currency_obj = new Currency($odr_obj->id_currency);
        $return_data['order_total'] = $this->kbFormatPrice($odr_obj->total_paid_tax_incl, $kb_order_currency_obj);
        $return_data['order_shipping'] = $this->kbFormatPrice($odr_obj->total_shipping_tax_incl, $kb_order_currency_obj);
        //        changes end by sandeep chauhan

        // changes by rishabh jain for seller return address
        $return_data['product_id'] = $pro_name['product_id'];
        // changes over
        $return_data['id_order'] = $return['id_order'];
        /**
         * Start Changes to fix the issue of 500 error because of the different number of parameters in the function
         * In PS8 and above, only two params are allowed in the displayDate(). So, adding the PS version check
         * NAFeb2024 displaydate
         * @date 06-02-2024
         * @modifier Nikhil Aggarwal
         */
        /*
         * Use kbDisplayDate for PS 1.7 / 8+ signature compatibility.
         * 21-07-2026
         */
        $return_data['order_date'] = $this->kbDisplayDate($odr_obj->date_add);
        // Changes end by Nikhil Aggarwal
        $order_state = new OrderState($odr_obj->current_state, $this->context->language->id);
        $return_data['order_status'] = $order_state->name;
        $return_data['order_status_color'] = $order_state->color;
        $return_data['quantity'] = $return['quantity'];

        // changes done by sandeep chauhan
        $return_data['unit_price_tax_incl'] = $this->kbFormatPrice($pro_name['unit_price_tax_incl'], $kb_order_currency_obj);
        $return_data['unit_price_unformatted'] = $pro_name['unit_price_tax_incl'];

        $temp_address = new Address($odr_obj->id_address_delivery);
        $address = array();
        $address['ordered_fields'] = AddressFormat::getOrderedAddressFields($temp_address->id_country);
        $address['ordered_fields_values'] = AddressFormat::getFormattedAddressFieldsValues(
            $temp_address,
            $address['ordered_fields']
        );
        $return_data['shipping_address'] = $this->getFormattedAddress($address);

        $return_data['whopayshipping'] = $return['whopayshipping'];
        $return_detail = array();
        $return_detail[0] = $status_history;
        $return_detail[1] = $return_data;
        /**
         * unset is used to free the memory
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        unset($cust_obj);
        unset($odr_obj);
        unset($order_state);
        unset($temp_address);
        return $return_detail;
    }

    /**
     * Function to update the RMA tables. 
     * If action type is return created then it will insert the data in order_return table
     * If action type is return completed then it will update the data in order_return table and execute the hook actionReturnManagerReturnComplete
     * If action type is return denied then it will update the data in order_return table
     * @date 16-02-2023
     * @param string $action_type
     * @param array $return_data
     * @return int|null
     */
    public function updateRMATables($action_type, $return_data)
    {
        switch ($action_type) {
            case 'return_created':
                /**
                 * Inserting data in order_return table if action type is return created
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $insert_rma = 'insert into ' . _DB_PREFIX_ .
                    'order_return (`id_order_return`, `id_customer`, `id_order`,
                    `question`, `date_add`, `date_upd`) values ("", ' . (int) $return_data['id_customer'] . ', '
                    . (int) $return_data['id_order'] . ', "' . pSQL($return_data['comment']) . '", now(), now())';
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($insert_rma);
                $id_order_return = Db::getInstance()->Insert_ID();
                $insert_odr_det = 'insert into ' . _DB_PREFIX_ .
                    'order_return_detail (`id_order_return`, `id_order_detail`,
                    `product_quantity`) values (' . (int) $id_order_return .
                    ', ' . (int) $return_data['id_order_detail'] . ', '
                    . (int) $return_data['quantity'] . ')';
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($insert_odr_det);
                break;
            case 'return_denied':
                /**
                 * Updating data in order_return table if action type is return denied
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $update_rma = 'update ' . _DB_PREFIX_ . 'order_return set state = 4 where id_order_return = ' .
                    (int) $return_data['id_order_return'];
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_rma);
                break;

            case 'return_completed':
                /**
                 * Updating data in order_return table if action type is return completed and executing the hook actionReturnManagerReturnComplete
                 * @date 16-02-2023
                 * @commenter Prvind Panday
                 */
                $update_rma = 'update ' . _DB_PREFIX_ . 'order_return set state = 5 where id_order_return = ' .
                    (int) $return_data['id_order_return'];
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_rma);
                // changes by rishabh jain to manage commissions after completing retuyrn request
                Hook::exec(
                    'actionReturnManagerReturnComplete',
                    array('return_data' => $return_data)
                );

                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_rma);
                break;
        }
        if (isset($id_order_return)) {
            return (int) $id_order_return;
        }
        /*
         * Explicit null when no order return id was created (non return_created actions).
         * 21-07-2026
         */
        return null;
    }

    /**
     * Function to get the default messages for the return request, for credit, refund, replace and cancel
     * @date 16-02-2023
     * @return array
     */
    protected function getDefaultMessages()
    {
        $message_arr = array(
            'credit' => 'We will send a replacement (Credit Request).
            We will pickup the item you wish to return in within 6 days.
			We hope you understand that we can only accept items for return,
            if they have not been used or tempered with.
			Original packaging and accessories also need to be returned along with the item.',
            'refund' => 'We will send a replacement (Refund Request).
            We will pickup the item you wish to return in within 6 days.
			We hope you understand that we can only accept items for return,
            if they have not been used or tempered with.
			Original packaging and accessories also need to be returned along with the item.',
            'replace' => 'We will send a replacement (Replacement Request).
            We will pickup the item you wish to return in within 6 days.
			We hope you understand that we can only accept items for return,
            if they have not been used or tempered with.
			Original packaging and accessories also need to be returned along with the item.',
            //changes by vishal for adding cancel functionality
            'cancel' => "Your cancel request has been forwarded to Seller. Kindly wait for the seller's message regarding the Cancellation."
            //changes end
        );
        return $message_arr;
    }

    /**
     * Function to get the message by name
     * @date 16-02-2023
     * @param string $name
     * @param string $iso
     * @return string
     * @commenter Prvind Panday
     */
    protected function getMessageByName($name, $iso)
    {
        $qry = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_success_messages where iso_code="' . pSQL($iso) .
            '" and id_shop=' . (int) $this->context->shop->id . ' and message_name = "' . pSQL($name) . '"';
        $message_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
        /**
         * If message is not found in the database then get the default message
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        if ($message_data) {
            return Tools::htmlentitiesDecodeUTF8($message_data['content']);
        } else {
            $default_messages = $this->getDefaultMessages();
            return $default_messages[$name];
        }
    }

    /*
     * Function to display Custom Fields details in the Admin panel.
     * Functionality: To implement the Custom Fields functionality on the Return Form.
     * Added By Priyanshu on 23-March-2020
     */

    public function getReturnmanagerCustomFeildDetail($id_rm_order)
    {
        $empty = 0;
        $result_fields_data = $this->getFieldsDataToDisplay($id_rm_order);

        if (empty($result_fields_data)) {
            $empty = 1;
        }

        $this->context->smarty->assign('fields_data', $result_fields_data);
        $this->context->smarty->assign('empty', $empty);

        $temp_vars = array(
            'html' => $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . 'returnmanager/views/templates/admin/custom_fields_data_content.tpl'
            )
        );
        return $temp_vars;
    }

    /*
     * Function to Fetch the Custom Fields Data from the Database.
     * Functionality: To implement the Custom Fields functionality on the Return Form.
     * Added By Priyanshu on 23-March-2020
     */

    public function getFieldsDataToDisplay($id_rm_order)
    {
        $id_lang = $this->context->language->id;

        // Query to get all the data of fields according to the order id
        $query = 'SELECT fd.*, cfl.*, cf.type FROM ' . _DB_PREFIX_ . 'kb_rm_fields_data fd ';
        $query = $query . 'JOIN ' . _DB_PREFIX_ . 'kb_rm_custom_fields_lang cfl ';
        $query = $query . 'ON fd.id_velsof_rm_custom_fields = cfl.id_velsof_rm_custom_fields ';
        $query = $query . 'JOIN ' . _DB_PREFIX_ . 'kb_rm_custom_fields cf ';
        $query = $query . 'ON cf.id_velsof_rm_custom_fields = cfl.id_velsof_rm_custom_fields ';
        $query = $query . 'WHERE id_rm_order = "' . (int) $id_rm_order . '" AND cfl.id_lang = "' . (int) $id_lang . '"';
        $result_fields_data = Db::getInstance()->executeS($query);

        // Processing checkboxes data
        foreach ($result_fields_data as $key => $field) {
            if ($field['type'] == 'checkbox') {
                $array_checkbox_values = json_decode($field['field_value'], true);
                // Getting option value labels
                $array_labels = array();
                $option_label = '';
                foreach ($array_checkbox_values as $option_value) {
                    $query = 'SELECT option_label FROM ' . _DB_PREFIX_ . 'kb_rm_custom_field_options_lang WHERE option_value = "' . pSQL($option_value) . '"';
                    $result_label = Db::getInstance()->executeS($query);
                    if (isset($result_label[0])) {
                        $array_labels[] = $result_label[0]['option_label'];
                    }
                }

                // Implode the values. Here we are getting the final string containing all the labels
                $option_label = implode(', ', $array_labels);

                // Replace the serialized string with the newly created string
                $result_fields_data[$key]['field_value'] = $option_label;
            }
            if ($field['type'] == 'selectbox' || $field['type'] == 'radio') {
                $my_option = $field['field_value'];
                $query = 'SELECT option_label FROM ' . _DB_PREFIX_ . 'kb_rm_custom_field_options_lang WHERE option_value = "' . pSQL($my_option) . '"';
                $result_label = Db::getInstance()->executeS($query);
                if (isset($result_label[0])) {
                    $result_fields_data[$key]['field_value'] = $result_label[0]['option_label'];
                }
            }
        }
        return $result_fields_data;
    }

    /**
     * Function to get the messages data from the database on the basis of language.
     * @param int $id_lang
     * @return array
     * @date 16-02-2023
     * @commenter Prvind Panday
     */
    protected function getMessagesData($id_lang)
    {
        $iso = Language::getIsoById((int) $id_lang);
        $fetch_messages_query = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_success_messages where iso_code="' .
            pSQL($iso) .
            '" and id_shop=' . (int) $this->context->shop->id;
        $messages_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($fetch_messages_query);

        /**
         * If messages data is not empty then return the messages data else return the default messages.
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        if ($messages_data) {
            $final_messages_data = array();
            foreach ($messages_data as $mess) {
                $final_messages_data[$mess['message_name']] = Tools::htmlentitiesDecodeUTF8($mess['content']);
            }
            return $final_messages_data;
        } else {
            return $this->getDefaultMessages();
        }
    }

    /**
     * Function to save the messages data in the database.
     * @param array $messages
     * @param string $iso
     * @return boolean
     * @date 16-02-2023
     * @commenter Prvind Panday
     */
    protected function saveMessagesData($messages, $iso)
    {
        foreach ($messages as $name => $m) {
            $qry = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_success_messages where iso_code="' . pSQL($iso) .
                '" and id_shop=' . (int) $this->context->shop->id . ' and message_name = "' . pSQL($name) . '"';
            $message_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);

            /**
             * If message data is not empty then update the message data else insert the message data.
             * @date 16-02-2023
             * @commenter Prvind Panday
             */
            if ($message_data) {
                $qry = 'UPDATE ' . _DB_PREFIX_ . 'velsof_rm_success_messages set
					content = "' . Tools::htmlentitiesUTF8($m)
                    . '", date_upd=now() where
					id_message = ' . (int) $message_data['id_message'];
            } else {
                $qry = 'INSERT into ' . _DB_PREFIX_ . 'velsof_rm_success_messages values ("", ' .
                    (int) Language::getIdByIso($iso) . ',
				' . (int) $this->context->shop->id . ', "' . pSQL($iso) . '",
				"' . pSQL($name) . '", "' . Tools::htmlentitiesUTF8($m) . '",
				now(), now())';
            }
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
        }
        /*
         * Always return bool as documented.
         * 21-07-2026
         */
        return true;
    }

    /**
     * Function to save the return slip data in the database.
     * @param array $slip_data
     * @param string $iso
     * @return array
     * @date 16-02-2023
     * @commenter Prvind Panday
     */
    protected function saveReturnSlipData($slip_data, $iso)
    {
        $qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_slip_data where iso_code="' . pSQL($iso) .
            '" and id_shop=' . (int) $this->context->shop->id . ' and address = "1"';
        $rslip_address = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);

        /**
         * If return slip address is not empty then update the return slip address else insert the return slip address.
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        if ($rslip_address) {
            $qry = 'UPDATE ' . _DB_PREFIX_ . 'velsof_return_slip_data set
				html_content = "' . Tools::htmlentitiesUTF8($slip_data['return_address'])
                . '", date_upd=now() where
				id_slip_data = ' . (int) $rslip_address['id_slip_data'];
        } else {
            $qry = 'INSERT into ' . _DB_PREFIX_ . 'velsof_return_slip_data values ("", ' .
                (int) Language::getIdByIso($iso) . ',
			' . (int) $this->context->shop->id . ', "' . pSQL($iso) . '",
			"1", "0", "' . Tools::htmlentitiesUTF8($slip_data['return_address']) . '",
			now(), now())';
        }
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);

        /**
         * If return slip guideline is not empty then update the return slip guideline else insert the return slip guideline.
         * @date 16-02-2023
         * @commenter Prvind Panday
         */
        $qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_slip_data where iso_code="' . pSQL($iso) .
            '" and id_shop=' . (int) $this->context->shop->id . ' and guideline = "1"';
        $rslip_guideline = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
        if ($rslip_guideline) {
            $qry = 'UPDATE ' . _DB_PREFIX_ . 'velsof_return_slip_data set
				html_content = "' . Tools::htmlentitiesUTF8($slip_data['return_guidelines'])
                . '", date_upd=now() where
				id_slip_data = ' . (int) $rslip_guideline['id_slip_data'];
        } else {
            $qry = 'INSERT into ' . _DB_PREFIX_ . 'velsof_return_slip_data values ("", ' .
                (int) Language::getIdByIso($iso) . ',
			' . (int) $this->context->shop->id . ', "' . pSQL($iso) . '",
			"0", "1", "' . Tools::htmlentitiesUTF8($slip_data['return_guidelines']) . '",
			now(), now())';
        }
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
        /*
         * Return saved slip payload for callers expecting an array.
         * 21-07-2026
         */
        return $slip_data;
    }

    /**
     * Function to get the return slip data by language.
     * @param string $type
     * @param string $iso
     * @return string
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    protected function getReturnSlipDataByLanguage($type, $iso)
    {
        /**
         * If type is address then get the return slip address else get the return slip guideline.
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if ($type == 'address') {
            $qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_slip_data where iso_code="' . pSQL($iso) .
                '" and id_shop=' . (int) $this->context->shop->id . ' and address = "1"';
            $slip_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
            /**
             * If return slip address is not empty then return the return slip address else return the default address.
             * @date 21-02-2023
             * @commenter Prvind Panday
             */
            if ($slip_data) {
                return Tools::htmlentitiesDecodeUTF8($slip_data['html_content']);
            } else {
                $this->context->smarty->assign('company', Configuration::get('BLOCKCONTACTINFOS_COMPANY'));
                $this->context->smarty->assign('address', Configuration::get('BLOCKCONTACTINFOS_ADDRESS'));
                $this->context->smarty->assign('phone_number', Configuration::get('BLOCKCONTACTINFOS_PHONE'));
                $this->context->smarty->assign('phome', $this->getModuleTranslationByLanguage('returnmanager', 'Phone', 'common', $iso));
                $address = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'returnmanager/views/templates/front/address.tpl');
                return $address;
            }
        } elseif ($type == 'guide') {
            $qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_slip_data where iso_code="' . pSQL($iso) .
                '" and id_shop=' . (int) $this->context->shop->id . ' and guideline = "1"';
            $slip_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
            /**
             * If return slip guideline is not empty then return the return slip guideline else return the default guideline.
             * @date 21-02-2023
             * @commenter Prvind Panday
             */
            if ($slip_data) {
                return Tools::htmlentitiesDecodeUTF8($slip_data['html_content']);
            } else {
                return $this->getDefaultReturnGuidelines();
            }
        }
        /*
         * Fallback empty string when type is neither address nor guide.
         * 21-07-2026
         */
        return '';
    }

    /**
     * Function to get the default return slip guidelines.
     * @param int|string $return_id
     * @param string $action
     * @return bool
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    public function generateReturnSlip($return_id, $action = 'approve')
    {
        $query = 'Select id_lang from ' . _DB_PREFIX_ . 'velsof_rm_order where id_rm_order = ' . (int) $return_id;
        $language_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        $language = Language::getIsoById((int) $language_id);
        if (!$language) {
            $language = Language::getIsoById($this->context->language->id);
        }
        $bar = new TCPDFBarcode($return_id, 'C39');

        $bar = new TCPDFBarcode($return_id, 'C39');
        $return_data = $this->getReturnData((int) $return_id);
        // edit sandeep
        $get_return = 'select id_order from ' . _DB_PREFIX_ . 'velsof_rm_order od where id_rm_order=' .
            (int) $return_id . ' and od.id_shop=' . (int) $this->context->shop->id;
        $kb_return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return);

        $odr_obj = new Order($kb_return_data['id_order']);
        $kb_order_currency_obj = new Currency($odr_obj->id_currency);
        // edit sandeep end

        $this->context->smarty->assign(
            array(
                'return_mailing_label' => $this->getModuleTranslationByLanguage('returnmanager', 'Return Mailing Label', 'common', $language),
                'cut_label' => $this->getModuleTranslationByLanguage('returnmanager', 'Cut this label and affix to the outside of your return package.', 'common', $language),
            )
        );
        $html = $this->getReturnSlipDataByLanguage('guide', $this->context->language->iso_code);
        $return_address = $this->getReturnSlipDataByLanguage('address', $this->context->language->iso_code);
        /* Start: Changes done by  Rishabh on 9th July 2018 for -------- (To add options to select any of the address) */
        $addr_query = 'Select id_address from ' . _DB_PREFIX_ . 'velsof_rm_return_address where id_return = ' . (int) $return_id;
        $addr_value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($addr_query);
        if ($addr_value > 0) {
            $address_query = 'Select * from ' . _DB_PREFIX_ . 'velsof_rm_address where id_address =' . (int) $addr_value;
            $address = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($address_query);
            $full_addr = nl2br($address['title'] . '</br>');
            $full_addr .= nl2br($address['address1'] . '</br>');
            $full_addr .= $address['address2'] . ' ';

            if ($address['city'] != '0') {
                $full_addr .= $address['city'] . ' ';
            }
            if ($address['id_state'] != 0) {
                $query = 'select name from ' . _DB_PREFIX_ . 'state where id_state = ' . (int) $address['id_state'];
                $full_addr .= Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) . '</br>';
            }

            $full_addr .= $address['postcode'] . ' ';
            $query = 'select name from ' . _DB_PREFIX_ . 'country_lang where id_country = ' . (int) $address['id_country'] . ' and id_lang =' . (int) $this->context->language->id;
            $full_addr .= Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
            $return_address = $full_addr;
        }

        /* Changes end */
        // changes by rishabh jain
        $is_seller_product = 0;
        if (Module::isEnabled('kbmarketplace') && class_exists('KbSellerProduct') && class_exists('KbSeller')) {
            $mp_config = json_decode(Configuration::get('KB_MARKETPLACE_CONFIG'), true);
            $id_seller = 0;
            if (isset($mp_config['enable_return_manager_compatibility']) && $mp_config['enable_return_manager_compatibility'] == 1) {
                $id_seller = call_user_func(array('KbSellerProduct', 'getSellerIdByProductId'), $return_data[1]['product_id']);
                /*
                 * We have added the compatibility with our marketplace plugin and we are using the function of that module class.
                 */
                if ($id_seller) {
                    $is_seller_product = 1;
                    /*
                     * Instantiate optional marketplace seller class dynamically for validator.
                     * 21-07-2026
                     */
                    $kb_seller_class = 'KbSeller';
                    $seller_obj = new $kb_seller_class($id_seller);
                    $seller_info = call_user_func(array($seller_obj, 'getSellerInfo'), $this->context->language->id);
                    if ($seller_info['return_address'] != '') {
                        $return_address = $seller_info['return_address'];
                    }
                }
            }
        }
        /**
         * Assign the smarty variables for return slip template
         * @date 01-03-2023
         * @author Prvind Panday
         * @commenter Prvind Panday
         */
        $this->context->smarty->assign(
            array(
                'return_address' => nl2br($return_address),
                'from' => $this->getModuleTranslationByLanguage('returnmanager', 'FROM', 'common', $language),
                'bar_code_html' => $bar->getBarcodeHTML(),
                'return_id_label' => $this->getModuleTranslationByLanguage('returnmanager', 'Return Id', 'common', $language),
                'return_id' => $return_id,
                'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager')
            )
        );
        // changes over
        $label_content = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/label_content.tpl'
        );
        $this->context->smarty->assign(
            array(
                'label_content' => $label_content,
                'mailing_label' => $this->getModuleTranslationByLanguage('returnmanager', 'If you do not want to use the above mailing label, you can send your return package using a carrier of your choice to the following address. You will need to pay for return postage costs.', 'common', $language),
                'authorization_label' => $this->getModuleTranslationByLanguage('returnmanager', 'Return Authorization Label', 'common', $language),
                'cut_this_place' => $this->getModuleTranslationByLanguage('returnmanager', 'Cut this and place inside the return package with your name and signature at the bottom.', 'common', $language),
                'bar_code_html_4_90' => $bar->getBarcodeHTML(4, 90),
                'order_label' => $this->getModuleTranslationByLanguage('returnmanager', 'Order', 'common', $language),
                'order_reference' => $return_data[1]['order_reference'],
                'item_desc' => $this->getModuleTranslationByLanguage('returnmanager', 'Item Description', 'common', $language),
                'total_price_label' => $this->getModuleTranslationByLanguage('returnmanager', 'Total Price', 'common', $language),
                'quantity_label' => $this->getModuleTranslationByLanguage('returnmanager', 'Quantity', 'common', $language),
                'product_name' => $return_data[1]['product_name'],
                'product_price' => $this->kbFormatPrice($return_data[1]['unit_price_unformatted'] * $return_data[1]['quantity'], $kb_order_currency_obj),
                'ret_qty' => $return_data[1]['quantity'],
                'to_whom_label' => $this->getModuleTranslationByLanguage('returnmanager', 'TO WHOM IT MAY CONCERN', 'common', $language),
                'declaration' => $this->getModuleTranslationByLanguage('returnmanager', 'I hereby declare that this return package contains all the items with there accessories', 'common', $language),
                'if_any' => $this->getModuleTranslationByLanguage('returnmanager', '(if any) related to this return request with', 'common', $language),
                'against' => $this->getModuleTranslationByLanguage('returnmanager', 'against', 'common', $language),
                'declare' => $this->getModuleTranslationByLanguage('returnmanager', 'I also declare that the items in this package are as it is and are not tempered with.', 'common', $language),
                'reject' => $this->getModuleTranslationByLanguage('returnmanager', 'You can reject the return request if anything like this is found.', 'common', $language),
                'sincerely' => $this->getModuleTranslationByLanguage('returnmanager', 'Your Sincerly', 'common', $language),
            )
        );
        if (isset($return_data[1]['product_attr']) && $return_data[1]['product_attr'] != '') {
            $this->context->smarty->assign('product_attr', $return_data[1]['product_attr']);
        }
        //start:added by aayushi on 15 Nov 2018 to solve the issue related to currency symbol
        $html .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/return_slip.tpl'
        );
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        // $dompdf->set_option('isRemoteEnabled', TRUE);
        //$dompdf->setPaper('A4', 'landscape');
        //end:added by aayushi on 15 Nov 2018 to solve the issue related to currency symbol

        $dompdf = new DOMPDF();
        /* Start - Code Modified by Aayushi on 14 Nov 2018 for fixing the Euro Symbol Issue in Return Slip */
        if (strpos($html, '€') !== false) {
            $html = iconv('UTF-8', 'CP1252', $html);
        } else {
            /**
             * Start Changes to fix the error of utf8_decode() deprecated
             * Using mb_convert_encoding() instead of utf8_decode() as it is compatible with all PHP versions
             * NAFeb2024 utf8_decode
             * @date 04-02-2024
             * @modifier Nikhil Aggarwal
             */
            $html = mb_convert_encoding($html, 'ISO-8859-1', 'UTF-8');
            // Changes end by Nikhil
        }
        $html = str_replace('€', '&euro;', $html);
        $html = str_replace('£', '&pound;', $html);
        /* End - Code Modified by Aayushi on 14 Nov 2018  for fixing the Euro Symbol Issue in Return Slip */

        /* Start - Code Modified by Raghu on 24-Oct-2017 for fixing the Euro Symbol Issue in Return Slip */
        /* End - Code Modified by Raghu on 24-Oct-2017 for fixing the Euro Symbol Issue in Return Slip */
        $dompdf->load_html($html);
        $dompdf->render();

        if ($action == 'approve') {
            file_put_contents($this->getReturnSlipPath() . $this->getReturnSlipName($return_id), $dompdf->output());
        } elseif ($action == 'click') {
            $dompdf->stream($this->getReturnSlipName($return_id), array('Attachment' => 0));
        }
        unset($bar);
        unset($return_data);
        unset($return_address);
        return true;
    }

    public function loadEmailTemplate($language, $template_name)
    {
        $fetch_template_query = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_email where id_lang=' . (int) $language .
            ' and id_shop=' . (int) $this->context->shop->id . ' and template_name="' . pSQL($template_name) . '"';
        $template_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($fetch_template_query);

        if ($template_data) {
            $template_data['body'] = Tools::htmlentitiesDecodeUTF8($template_data['body']);
            $template_data['subject'] = Tools::htmlentitiesDecodeUTF8($template_data['subject']);
            $template_data['text_content'] = Tools::htmlentitiesDecodeUTF8($template_data['text_content']);
            return $template_data;
        } else {
            $template_data = array();
            switch ($template_name) {
                case 'new_ret_cust':
                    $template_data = $this->getDefaultNewReturnCustEmail();
                    break;
                    //changes by vishal for adding cencel functionality
                case 'new_cancel_cust':
                    $template_data = $this->getDefaultNewCancelCustEmail();
                    break;
                case 'new_cancel_adm':
                    $template_data = $this->getDefaultNewCancelAdmEmail();
                    break;
                case 'cancel_app':
                    $template_data = $this->getDefaultCancelApprovedEmail();
                    break;
                case 'cancel_den':
                    $template_data = $this->getDefaultCancelDeniedEmail();
                    break;
                    //changes end
                case 'new_ret_adm':
                    $template_data = $this->getDefaultNewReturnAdmEmail();
                    break;
                case 'ret_app':
                    $template_data = $this->getDefaultReturnApprovedEmail();
                    break;
                case 'ret_den':
                    $template_data = $this->getDefaultReturnDeniedEmail();
                    break;
                case 'ret_stat':
                    $template_data = $this->getDefaultReturnStatusEmail();
                    break;
                case 'ret_comp':
                    $template_data = $this->getDefaultReturnCompletedEmail();
                    break;
                case 'ret_comp_discount':
                    $template_data = $this->getDefaultReturnCompletedDiscountEmail();
                    break;
                case 'ret_cancel':
                    $template_data = $this->getDefaultReturnCancelEmail();
                    break;
                case 'ret_cancel_admin':
                    $template_data = $this->getDefaultReturnCancelAdminEmail();
                    break;
                case 'new_ticket_admin':
                    $template_data = $this->getDefaultNewTicketAdminEmail();
                    break;
                case 'new_ticket_client':
                    $template_data = $this->getDefaultNewTicketCustomerEmail();
                    break;
                case 'client_reply_client':
                    $template_data = $this->getDefaultClientReplyCustomerEmail();
                    break;
                case 'client_reply_admin':
                    $template_data = $this->getDefaultClientReplyAdminEmail();
                    break;
                case 'admin_reply_client':
                    $template_data = $this->getDefaultAdminReplyCustomerEmail();
                    break;
                    /*
                 * Start Code Added By Priyanshu on 23-March-2020 to load the Email template in the Admin panel
                 * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                 */
                case 'amount_adjust_to_admin':
                    $template_data = $this->getDefaultAmountAdjustAdminEmail();
                    break;
                case 'amount_adjust_to_client':
                    $template_data = $this->getDefaultAmountAdjustClientEmail();
                    break;
                    /*
                 * End Code Added By Priyanshu on 23-March-2020 to load the Email template in the Admin panel
                 * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                 */
            }
            $template_data['id_template'] = 0;
            return $template_data;
        }
    }

    /*
     * Below Function created By Priyanshu on 23-March-2020 to create Cart Rule in case of Replacement of product having price greater than the Original Product Price
     * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
     */

    public function generateCartRule($CouponValue, $product_id, $return_data)
    {
        $desc = $this->l('replacement coupon for product id ') . $product_id;
        $is_used_partial = 1;
        $min_cart_value = 0;
        $percent_reduction = 0;
        $coupon_code = $this->generateCouponCode();
        $order_obj = new Order($return_data['id_order']);
        $reduction_currency = $order_obj->id_currency;
        $customer_info = new Customer($return_data['id_customer']);
        //insert coupon details
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule  SET
                id_customer = ' . (int) $return_data['id_customer'] . ',
                date_from = "' . pSQL(date('Y-m-d H:i:s', time())) . '",
                date_to = "' . pSQL(date('Y-m-d H:i:s', strtotime('+1 month'))) . '",
                description = "' . pSQL($desc) . '",
                quantity = 1, quantity_per_user = 1, priority = 1, partial_use = ' . (int) $is_used_partial . ',
                code = "' . pSQL($coupon_code) . '", minimum_amount = ' . (float) $min_cart_value
            . ', minimum_amount_tax = 0,
                minimum_amount_currency = ' . (int) $reduction_currency . ', minimum_amount_shipping = 0,
                country_restriction = 0, carrier_restriction = 0, group_restriction = 0, cart_rule_restriction = 0,
                product_restriction = 1, shop_restriction = 1,
                free_shipping = 0,
                reduction_percent = ' . (float) $percent_reduction . ', reduction_amount = '
            . (float) $CouponValue . ',
                reduction_tax = 1, reduction_currency = ' . (int) $reduction_currency . ',
                reduction_product = 0, gift_product = 0, gift_product_attribute = 0,
                highlight = 0, active = 1,
                date_add = "' . pSQL(date('Y-m-d H:i:s', time()))
            . '", date_upd = "' . pSQL(date('Y-m-d H:i:s', time())) . '"';

        Db::getInstance()->execute($sql);
        $cart_rule_id = Db::getInstance()->Insert_ID();

        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_shop
                set id_cart_rule = ' . (int) $cart_rule_id
            . ', id_shop = ' . (int) $customer_info->id_shop);

        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_lang
                set id_cart_rule = ' . (int) $cart_rule_id . ', id_lang = ' . (int) $customer_info->id_lang . ',
				name = "' . pSQL($desc) . '"');
        // to map the return id with cart rule
        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'velsof_return_coupon_data
                set id_cart_rule = ' . (int) $cart_rule_id . ', id_return = ' . (int) $return_data['id_order_return'] . ', id_shop = ' . (int) $customer_info->id_shop);
        // changes over

        //product rule group entry
        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_group
                set id_cart_rule = ' . (int) $cart_rule_id
            . ', quantity = 1');
        $product_rule_group_id = Db::getInstance()->Insert_ID();

        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule
                set id_product_rule_group = ' . (int) $product_rule_group_id
            . ', type = "products"');
        $product_rule_id = Db::getInstance()->Insert_ID();

        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_value
                set id_product_rule = ' . (int) $product_rule_id
            . ', id_item = ' . (int) $product_id);

        return $coupon_code;
    }

    /* Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer */

    public function sendNotificationEmail($email_template, $return_data, $custom_data = array(), $is_cancel_mail = 0)
    {
        /*
         * Defaults so all branches have defined customer/template/language/template_vars.
         * 21-07-2026
         */
        $customer = new Customer(isset($return_data['id_customer']) ? (int) $return_data['id_customer'] : 0);
        $template_data = array('subject' => '', 'body' => '');
        $language = $this->context->language->iso_code;
        $template_vars = array();
        /*
         * changes done to remove mails folder from the themes
         * NASep2023 DeleteMail Directory
         * @date 18-09-2023
         * @author Nikhil Aggarwal
         */
        if (file_exists(_PS_THEME_DIR_ . 'modules/returnmanager/mails')) {
            $this->deleteDir(_PS_THEME_DIR_ . 'modules/returnmanager/mails');
        }
        // Changes end by Nikhil
        /* Start Code Modified By Priyanshu on 8-March-2021 to implement the functionality to send Test Email */
        /* Start Addded by Anshul Mittal on 25-08-2017  to add a functionality of email editing before sending it to customer */
        if (!empty($custom_data)) {
            $template_data = array();
            $template_data['subject'] = $custom_data['subject'];
            $template_data['body'] = $custom_data['body'];
        } else {
            if ($email_template != 'ret_test_email') {
                $template_data = $this->loadEmailTemplate($customer->id_lang, $email_template);
            }
        }
        if ($email_template != 'ret_test_email') {
            //changes by vishal for adding cancel order functionality
            if ($is_cancel_mail == 1) {
                $query = 'Select id_lang from ' . _DB_PREFIX_ . 'velsof_rm_cancel where id_rm_cancel = ' . (int) $return_data['cancel_id'];
                $language_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
            } else {
                $query = 'Select id_lang from ' . _DB_PREFIX_ . 'velsof_rm_order where id_rm_order = ' . (int) $return_data['return_id'];
                $language_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
            }
            //changes end
            $language = Language::getIsoById((int) $language_id);
            if (!$language) {
                $language = Language::getIsoById($this->context->language->id);
            }
            /* End Addded by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
        }
        /* End Code Modified By Priyanshu on 8-March-2021 to implement the functionality to send Test Email */
        $directory = $this->getTemplateDir();
        if (is_writable($directory)) {
            $html_template = self::TEMPLATE_NAME . '.html';
            $txt_template = self::TEMPLATE_NAME . '.txt';

            $base_html = $this->getTemplateBaseHtml();

            $template_html = str_replace('{template_content}', $template_data['body'], $base_html);
            $file = fopen($directory . $html_template, 'w+');
            fwrite($file, $template_html);
            fclose($file);

            $file = fopen($directory . $txt_template, 'w+');
            fwrite($file, $template_html);
            fclose($file);

            $attachment = null;
            $link_obj = new Link();
            /**
             * $email_template can be new_ret_cust, ret_cancel, ret_approve, ret_reject, ret_shipped, ret_delivered and ret_test_email, it's the mail template name which needs to be sent to customer or admin
             * $return_data is the return data array
             * $custom_data is the custom data array which is used to send custom email to customer or admin
             * $is_cancel_mail is the flag to check if the mail is for cancel order or not
             * $language is the language code of the customer
             * $template_vars is the array of variables which needs to be replaced in the template
             * $attachment is the attachment file
             * @date 28-03-2023
             * @commenter Prvind Panday
             */
            switch ($email_template) {
                case 'new_ret_cust':
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $return_data['order_reference'],
                        '{item_details}' => $this->getItemHtml($return_data['return_id']),
                        '{return_id}' => $return_data['return_id']
                    );
                    break;
                case 'ret_cancel':
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $return_data['order_reference'],
                        '{item_details}' => $this->getItemHtml($return_data['return_id']),
                        '{return_id}' => $return_data['return_id']
                    );
                    break;
                case 'ret_cancel_admin':
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $return_data['order_reference'],
                        '{item_details}' => $this->getItemHtml($return_data['return_id']),
                        '{return_id}' => $return_data['return_id']
                    );
                    break;
                case 'new_ret_adm':
                    $template_vars = array(
                        '{order_reference}' => $return_data['order_reference'],
                        '{item_details}' => $this->getItemHtml($return_data['return_id']),
                        '{return_id}' => $return_data['return_id']
                    );
                    break;
                case 'ret_app':
                    $odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $odr->reference,
                        '{item_details}' => $this->getItemHtml($return_data['return_id']),
                        '{return_id}' => $return_data['return_id'],
                        /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                        '{custom_status_text}' => $return_data['current_status_text_message'],
                        /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                        '{attachment_text}' => ''
                    );
                    $settings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
                    if (isset($settings['enable_return_slip']) && $settings['enable_return_slip'] == 1) {
                        $file_path = $this->getReturnSlipPath() . $this->getReturnSlipName($return_data['return_id']);
                        $attachment = array(
                            'content' => Tools::file_get_contents($file_path),
                            'name' => $this->getReturnSlipName($return_data['return_id']),
                            'mime' => 'application/pdf'
                        );
                        $template_vars['{attachment_text}'] = '<p style="padding:19px 0 0 0;margin:0;
                        color:#565656;line-height:19px">
                        ' . $this->getModuleTranslationByLanguage('returnmanager', 'Please find in attachements the return slip for this return request.', 'common', $language) . '</p>';
                    }
                    unset($settings);
                    break;
                case 'ret_den':
                    $odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $odr->reference,
                        '{item_details}' => $this->getItemHtml($return_data['return_id']),
                        '{return_id}' => $return_data['return_id']
                    );
                    break;
                case 'ret_stat':
                    $odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $odr->reference,
                        '{item_details}' => $this->getItemHtml($return_data['return_id']),
                        '{return_id}' => $return_data['return_id'],
                        '{signin_link}' => $link_obj->getPageLink('authentication'),
                        '{previous_status}' => $return_data['previous_status'],
                        '{current_status}' => $return_data['current_status'],
                        /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                        '{custom_status_text}' => $return_data['current_status_text_message']
                        /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                    );
                    break;

                case 'ret_comp_discount':
                    $odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $odr->reference,
                        '{item_details}' => $this->getItemHtml($return_data['return_id']),
                        '{return_id}' => $return_data['return_id'],
                        '{coupon_code}' => $return_data['coupon_code'],
                        '{amount}' => $return_data['amount'],
                        '{signin_link}' => $link_obj->getPageLink('authentication')
                    );
                    break;
                case 'ret_comp':
                    $odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $odr->reference,
                        '{item_details}' => $this->getItemHtml($return_data['return_id']),
                        '{return_id}' => $return_data['return_id'],
                        '{signin_link}' => $link_obj->getPageLink('authentication')
                    );
                    break;
                    // ticket emails
                case 'new_ticket_admin':
                    //$odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_email}' => $customer->email,
                        '{ticket_number}' => $return_data['ticket_number'],
                        '{subject}' => $return_data['subject'],
                        '{return_id}' => $return_data['return_id'],
                        '{message}' => $return_data['message']
                    );
                    break;
                case 'new_ticket_client':
                    //$odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{ticket_number}' => $return_data['ticket_number'],
                        '{subject}' => $return_data['subject'],
                        '{return_id}' => $return_data['return_id'],
                        '{ticket_track_url}' => $return_data['track_url'],
                        '{issue}' => $return_data['message']
                    );
                    break;
                case 'client_reply_client':
                    //  $odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{ticket_number}' => $return_data['ticket_number'],
                        '{subject}' => $return_data['subject'],
                        '{return_id}' => $return_data['return_id'],
                        '{ticket_track_url}' => $return_data['track_url'],
                        '{message}' => $return_data['message']
                    );
                    break;
                case 'client_reply_admin':
                    //$odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_email}' => $customer->email,
                        '{ticket_number}' => $return_data['ticket_number'],
                        '{subject}' => $return_data['subject'],
                        '{return_id}' => $return_data['return_id'],
                        '{message}' => $return_data['message']
                    );
                    break;
                case 'admin_reply_client':
                    //$odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{ticket_number}' => $return_data['ticket_number'],
                        '{subject}' => $return_data['subject'],
                        '{return_id}' => $return_data['return_id'],
                        '{ticket_track_url}' => $return_data['track_url'],
                        '{message}' => $return_data['message']
                    );
                    break;
                    // changes over
                    /*
                 * Start Code Added By Priyanshu on 23-March-2020 to send notification mail to the client or Admin regarding the Replacement of Product
                 * if Product selection option is enabled in the Admin panel.
                 * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                 */
                case 'amount_adjust_to_client':
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                        '{product_link}' => $return_data['product_link'],
                        '{product_name}' => $return_data['product_name'],
                        '{coupon_code}' => $return_data['coupon_code'],
                        '{diff_amount}' => $return_data['diff_amount'],
                        '{return_id}' => $return_data['return_id'],
                    );
                    break;
                case 'amount_adjust_to_admin':
                    $template_vars = array(
                        '{client_email}' => $customer->email,
                        '{replaced_product_link}' => $return_data['replaced_product_link'],
                        '{replaced_product_name}' => $return_data['replaced_product_name'],
                        '{replacedwith_product_link}' => $return_data['replacedwith_product_link'],
                        '{replacedwith_product_name}' => $return_data['replacedwith_product_name'],
                        '{difference_amount}' => $return_data['difference_amount'],
                        '{return_id}' => $return_data['return_id'],
                    );
                    break;
                    /*
                 * End Code Added By Priyanshu on 23-March-2020 to send notification mail to the client or Admin regarding the Replacement of Product
                 * if Product selection option is enabled in the Admin panel.
                 * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                 */
                    //changes by vishal for adding order cancellation fucntionality
                case 'new_cancel_cust':
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $return_data['order_reference'],
                        '{cancel_id}' => $return_data['cancel_id']
                    );
                    break;
                case 'new_cancel_adm':
                    $template_vars = array(
                        '{order_reference}' => $return_data['order_reference'],
                        '{cancel_id}' => $return_data['cancel_id']
                    );
                    break;
                case 'cancel_app':
                    $odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $odr->reference,
                        '{cancel_id}' => $return_data['cancel_id'],
                    );
                    break;
                case 'cancel_den':
                    $odr = new Order((int) $return_data['id_order']);
                    $template_vars = array(
                        '{customer_full_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{order_history_link}' => $link_obj->getPageLink('history'),
                        '{order_reference}' => $odr->reference,
                        '{cancel_id}' => $return_data['cancel_id']
                    );
                    break;
                    //changes end
                    /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to send Test Email */
                case 'ret_test_email':
                    $template_vars = array();
                    break;
                    /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to send Test Email */
            }
            unset($link_obj);
            $lang_iso = Configuration::get('VELSOF_RETURN_MANAGER_DEFAULT_TEMPLATE_LANG');
            $id_lang = Language::getIdByIso($lang_iso);

            /**
             * mails are sent to admin if the email template is new_ret_adm, new_ticket_admin, client_reply_admin, amount_adjust_to_admin, new_cancel_adm, ret_test_email, ret_cancel_admin
             * mails are sent to customer if the email template is new_ret_cust, client_reply_cust, amount_adjust_to_client, new_cancel_cust, ret_cancel_customer
             * mails are sent to both admin and customer if the email template is cancel_app, cancel_den
             * @date 28-03-2023
             * @commenter Prvind Panday
             */
            if ($email_template == 'new_ret_adm' || $email_template == 'new_ticket_admin' || $email_template == 'client_reply_admin' || $email_template == 'amount_adjust_to_admin' || $email_template == 'new_cancel_adm' || $email_template == 'ret_test_email' || $email_template == 'ret_cancel_admin') {
                if ($email_template == 'ret_test_email') {
                    $email = $return_data['test_email'];
                } else {
                    $email = Configuration::get('PS_SHOP_EMAIL');
                }
                $if_cond1 = Mail::Send(
                    $id_lang,
                    self::TEMPLATE_NAME,
                    $template_data['subject'],
                    $template_vars,
                    $email,
                    Configuration::get('PS_SHOP_NAME'),
                    Configuration::get('PS_SHOP_EMAIL'),
                    Configuration::get('PS_SHOP_NAME'),
                    null,
                    null,
                    _PS_MODULE_DIR_ . 'returnmanager/mails/',
                    false,
                    $this->context->shop->id
                );
                if ($if_cond1) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $if_cond2 = Mail::Send(
                    $id_lang,
                    self::TEMPLATE_NAME,
                    $template_data['subject'],
                    $template_vars,
                    $customer->email,
                    $customer->firstname . ' ' . $customer->lastname,
                    Configuration::get('PS_SHOP_EMAIL'),
                    Configuration::get('PS_SHOP_NAME'),
                    $attachment,
                    null,
                    _PS_MODULE_DIR_ . 'returnmanager/mails/',
                    false,
                    $this->context->shop->id
                );
                if ($if_cond2) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Functionality: To get the item html for the replacement or for current product which is returned.
     * @param int $return_id
     * @return string
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getItemHtml($return_id)
    {
        /**
         * fetched lang id in which the return was created to return the item html in the same language
         * @date 28-03-2023
         * @commenter Prvind Panday
         */
        $query = 'Select id_lang from ' . _DB_PREFIX_ . 'velsof_rm_order where id_rm_order = ' . (int) $return_id;
        $language_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        $language = Language::getIsoById((int) $language_id);
        if (!$language) {
            $language = Language::getIsoById($this->context->language->id);
        }


        $order_query = 'select id_order_detail,quantity from ' . _DB_PREFIX_ . 'velsof_rm_order where id_rm_order = ' .
            (int) $return_id;
        $item_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($order_query);
        $order_detail = new OrderDetail((int) $item_data['id_order_detail']);
        /**
         * $return_data contains the id_order of the order in which the return was created
         * @date 28-03-2023
         * @commenter Prvind Panday
         */
        $get_return = 'select id_order from ' . _DB_PREFIX_ . 'velsof_rm_order od where id_rm_order=' .
            (int) $return_id . ' and od.id_shop=' . (int) $this->context->shop->id;
        $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return);

        $odr_obj = new Order($return_data['id_order']);
        $kb_order_currency_obj = new Currency($odr_obj->id_currency);

        $price = $this->kbFormatPrice((float) $order_detail->unit_price_tax_incl * (int) $item_data['quantity'], $kb_order_currency_obj);
        $product_name = $order_detail->product_name;
        $attr_html = '';
        $p_temp = new Product($order_detail->product_id);
        /**
         * $image_combination contains the combination of images of the product which is returned if combination is selected else it contains the cover image of the product
         * @date 28-03-2023
         * @commenter Prvind Panday
         */
        $image_combination = $p_temp->getCombinationImages($this->context->language->id);
        if (isset($image_combination[$order_detail->product_attribute_id][0]['id_image'])) {
            $id_image = $order_detail->product_id . '-' .
                $image_combination[$order_detail->product_attribute_id][0]['id_image'];
        } else {
            $get_cover_image = Product::getCover($order_detail->product_id);
            $id_image = $order_detail->product_id . '-' . $get_cover_image['id_image'];
        }

        /**
         * $link_rewrite contains the link rewrite of the product which is returned so that from email template we can go to the product page
         * @date 28-03-2023
         * @commenter Prvind Panday
         */
        $link_rewrite = $p_temp->link_rewrite[$this->context->language->id];
        $link_obj = new Link();
        if ((bool) Configuration::get('PS_SSL_ENABLED')) {
            $product_img_path = 'https://' . $link_obj->getImageLink($link_rewrite, $id_image);
        } else {
            $product_img_path = 'http://' . $link_obj->getImageLink($link_rewrite, $id_image);
        }
        $product_link = $this->context->link->getProductLink($order_detail->product_id);
        unset($order_detail);
        unset($p_temp);
        unset($link_obj);
        $html = '';
        $this->context->smarty->assign(
            array(
                'image' => $this->getModuleTranslationByLanguage('returnmanager', 'IMAGE', 'common', $language),
                'item' => $this->getModuleTranslationByLanguage('returnmanager', 'ITEM', 'common', $language),
                'qty' => $this->getModuleTranslationByLanguage('returnmanager', 'QTY', 'common', $language),
                /*
                 * Use price_label for header translation to avoid duplicate array key 'price'.
                 * 21-07-2026
                 */
                'price_label' => $this->getModuleTranslationByLanguage('returnmanager', 'PRICE', 'common', $language),
                'product_link' => $product_link,
                'product_img_path' => $product_img_path,
                'product_name' => $product_name,
                'attr_html' => nl2br($attr_html),
                'quantity' => (int) $item_data['quantity'],
                'price' => $price,
            )
        );
        $html .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/item_html.tpl'
        );
        return $html;
    }

    /**
     * Function to get the template base html for the email template
     * @return string
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getTemplateBaseHtml()
    {
        $template_html = array();
        $this->context->smarty->assign(
            array(
                'template_content' => '{template_content}',
                'shop_name' => '{shop_name}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
            )
        );
        $template_html = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/template_base_html.tpl'
        );
        return $template_html;
    }

    /**
     * Function to get the default new return customer email template
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultNewReturnCustEmail()
    {
        $template_html = array();
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'shop_name' => '{shop_name}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'order_reference' => '{order_reference}',
                'item_details' => '{item_details}',
                /**
                 * Start Changes to fix the issue of order history link in the mail not being attached
                 * NASep2023 order_history_link_issue_in_mail
                 * @date 15-09-2023
                 * @modifier NIkhil Aggarwal
                 */
                'order_history_link' => '{order_history_link}',
                // Changes end by Nikhil
                'return_id' => '{return_id}',
            )
        );
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_new_return_cust_email.tpl'
        );
        $template_html['subject'] = 'New Return Request has been received!';
        $template_html['text_content'] = 'Dear {customer_full_name},Greetings from
            {shop_name}!Your return request has been received for
			the following item in your order  {order_reference}.Please keep in
            mind that this is only the first step of the return
			process. The request has to be approved by shop owner in order to
            further process the request. You will be notified once
			the shop owner approves this return request.{item_details}Return id for this
			request is : #{return_id}We apologizefor any inconvenience caused to
			you.{shop_name}24x7 Customer Support � Flexible Payment Options � Largest Collection � Easy Returns';
        return $template_html;
    }

    /**
     * Function to get the default new cancel customer email template
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     * @author Vishal Goyal
     */
    protected function getDefaultNewCancelCustEmail()
    {
        $template_html = array();
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'shop_name' => '{shop_name}',
                /**
                 * Start Changes to fix the issue of Shop URL in the mail not being attached
                 * NASep2023 store_url_issue_in_mail
                 * @date 15-09-2023
                 * @modifier NIkhil Aggarwal
                 */
                'shop_url' => '{shop_url}',
                // Changes end by Nikhil
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'cancel_id' => '{cancel_id}',
            )
        );
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_new_cancel_cust_email.tpl'
        );
        $template_html['subject'] = 'New Cancellation Request has been received!';
        $template_html['text_content'] = 'Dear {customer_full_name},Greetings from
            {shop_name}!Your Order cancellation request has been received for your order  {order_reference}.Please keep in
            mind that this is only the first step of the order cancellation
			process. The request has to be approved by shop owner in order to
            further process the request. You will be notified once
			the shop owner approves this cancellation request.Cancel id for this
			request is : #{cancel_id}We apologizefor any inconvenience caused to
			you.{shop_name}24x7 Customer Support � Flexible Payment Options � Largest Collection � Easy Returns';
        return $template_html;
    }

    /**
     * Function to get the default new cancel admin email template
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultNewCancelAdmEmail()
    {
        $this->context->smarty->assign(
            array(
                'order_reference' => '{order_reference}',
                'cancel_id' => '{cancel_id}',
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_new_cancel_adm_email.tpl'
        );
        $template_html['subject'] = 'Cancellation requested by a Customer!';
        $template_html['text_content'] = 'Hey Admin,A new Cancellation request has been received against the order
            {order_reference}.Cancel id for this request is : #{cancel_id}Click here to go to the admin panel
            and take
			appropriate action regarding this cancellation request. This mail is just to notify you about the cancellation request,
            you can
			process the cancellation request only from back office.';
        return $template_html;
    }

    /**
     * Function to get the default cancel approved customer email template
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultCancelApprovedEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'cancel_id' => '{cancel_id}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_cancel_approved_email.tpl'
        );
        $template_html['subject'] = 'Your Cancellation request is Approved!';
        $template_html['text_content'] = 'Dear {customer_full_name},Greetings from {shop_name}!Your cancellation request
			has been approved by the store owner for the  order  {order_reference}.
            Cancel id
			for this request is : #{cancel_id}The store owner will now take further actions on this cancellation request.
            We apologize for any
			inconvenience caused to you.{shop_name}24x7 Customer Support &bull;
			Flexible Payment Options &bull; Largest Collection &bull; Easy Returns';
        return $template_html;
    }

    /**
     * Function to get the default cancel denied customer email template
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultCancelDeniedEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'cancel_id' => '{cancel_id}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_cancel_denied_email.tpl'
        );
        $template_html['subject'] = 'Your Cancellation request is Dis-apporved!';
        $template_html['text_content'] = 'Dear {customer_full_name},Greetings from {shop_name}!
            We are sorry to inform you that your
			cancellation request for the order
            {order_reference} has been denied by the store owner.Cancel
			id for this request is : #{cancel_id}We apologize for any inconvenience caused to you.{shop_name}24x7
			Customer Support &bull; Flexible Payment Options &bull; Largest Collection &bull; Easy Returns';
        return $template_html;
    }

    //changes end

    /**
     * Function to get the default new ticket admin email template
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultNewTicketCustomerEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_name' => '{customer_name}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'issue' => '{issue}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                /**
                 * Start Changes to add the ticket_track_url in template
                 * NASep2023 ticket_track_url
                 * @date 16-09-2023
                 * @modifier Nikhil Aggarwal
                 */
                'ticket_track_url' => '{ticket_track_url}',
                //  Changes end by Nikhil
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_new_ticket_customer_email.tpl'
        );
        $template_html['subject'] = 'Your Ticket has been Created Successfully!';
        $template_html['text_content'] = 'You ticket #{ticket_number} in reference to #{return_id} has been created successfully.
            Ticket Number #{ticket_number} Subject: {subject} Issue:  {issue}.You can track your ticket status by clicking this link.';
        return $template_html;
    }

    /**
     * Function to get the default client reply customer email template on ticket
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultClientReplyCustomerEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'issue' => '{issue}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'message' => '{message}',
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_client_reply_customer_email.tpl'
        );
        $template_html['subject'] = 'Your message on ticket submitted Successfully.!';
        $template_html['text_content'] = 'Hey {customer_name}, You reply on {ticket_number} in reference to #{return_id} has been submitted successfully
            Ticket Number #{ticket_number} Subject: {subject} Issue:  {issue}.You can track your ticket status by clicking this link.';
        return $template_html;
    }

    /**
     * Function to get the default admin reply customer email template on ticket
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultAdminReplyCustomerEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_name' => '{customer_name}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'issue' => '{issue}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'message' => '{message}',
                'ticket_track_url' => '{ticket_track_url}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_admin_reply_customer_email.tpl'
        );
        $template_html['subject'] = 'Admin just replied to your ticket!';
        $template_html['text_content'] = 'Hey {customer_name}, 
            Admin has just replied on {ticket_number} generated in reference to #{return_id}.
			Ticket Number #{ticket_number} Subject: {subject} Message:  {message}.You can track your ticket status by clicking this link.
            You can track your ticket status by clicking this link';
        return $template_html;
    }

    /*
     * Below function created By Priyanshu on 23-March-2020 to fetch the email content which was sent to Admin in case if Replacement Product is having
     * price lesser than the Original Product.
     * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
     */
    protected function getDefaultAmountAdjustAdminEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'issue' => '{issue}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'message' => '{message}',
                'replaced_product_link' => '{replaced_product_link}',
                'client_email' => '{client_email}',
                'difference_amount' => '{difference_amount}',
                'replaced_product_name' => '{replaced_product_name}',
                'replacedwith_product_link' => '{replacedwith_product_link}',
                'replacedwith_product_name' => '{replacedwith_product_name}',
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_amount_adjust_admin_email.tpl'
        );
        $template_html['subject'] = 'Amount adjust mail to admin';
        $template_html['text_content'] = 'Hey {admin_name}, 
            We want to inform you that you just accepted a replacement request for client with email id {client_email}. There is a difference of {difference_amount} which you need to
            pay to client. Kindly do the needful.';
        return $template_html;
    }


    /*
     * Below function created By Priyanshu on 23-March-2020 to fetch the email content which was sent to Customers in case if Replacement Product is having
     * price greater than the Original Product.
     * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
     */
    protected function getDefaultAmountAdjustClientEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'issue' => '{issue}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'message' => '{message}',
                'replaced_product_link' => '{replaced_product_link}',
                'client_email' => '{client_email}',
                'difference_amount' => '{difference_amount}',
                'replaced_product_name' => '{replaced_product_name}',
                'replacedwith_product_link' => '{replacedwith_product_link}',
                'replacedwith_product_name' => '{replacedwith_product_name}',
                'coupon_code' => '{coupon_code}',
                'product_name' => '{product_name}',
                'product_link' => '{product_link}',
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_amount_adjust_client_email.tpl'
        );
        $template_html['subject'] = 'Pay remaining amount for replacement';
        $template_html['text_content'] = 'Hey {customer_name}, 
            We would like to inform you that please use the following coupon code to pay the remaining amount while placing the order with the following product:';
        return $template_html;
    }

    /**
     * Function to get the default email template for the client reply to admin email.
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultClientReplyAdminEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_name' => '{customer_name}',
                'customer_email' => '{customer_email}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'issue' => '{issue}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'message' => '{message}',
                'ticket_track_url' => '{ticket_track_url}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_client_reply_admin_email.tpl'
        );
        $template_html['subject'] = 'Customer reply on ticket!';
        $template_html['text_content'] = 'Hey Admin , 
            A customer has just replied on the ticket in reference to #{return_id}
			Ticket Number #{ticket_number} Subject: {subject} Message:  {message}.
            Customer Detail:
            Name: {customer_name}
            Email: {customer_email}
                        You can track your ticket status by clicking this link.
            ';
        return $template_html;
    }

    /**
     * Function to get the default email template for the new ticket generated by the customer to admin.
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultNewTicketAdminEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_name' => '{customer_name}',
                'customer_email' => '{customer_email}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'issue' => '{issue}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'message' => '{message}',
                'ticket_track_url' => '{ticket_track_url}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_new_ticket_admin_email.tpl'
        );
        $template_html['subject'] = 'Return requested by a Customer!';
        $template_html['text_content'] = 'Hey Admin,A new return request has been received against the order
            {order_reference}.Item
			to be returned {item_details}Return id for this request is : #{return_id}Click here to go to the admin panel
            and take
			appropriate action regarding this return request. This mail is just to notify you about the return request,
            you can
			process the return request only from back office.';
        return $template_html;
    }

    /**
     * Function to get the default email template for the new return generated by the customer to admin.
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultNewReturnAdmEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'customer_email' => '{customer_email}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'attachment_text' => '{attachment_text}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'item_details' => '{item_details}',
                'ticket_track_url' => '{ticket_track_url}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_new_return_adm_email.tpl'
        );
        $template_html['subject'] = 'Return requested by a Customer!';
        $template_html['text_content'] = 'Hey Admin,A new return request has been received against the order
            {order_reference}.Item
			to be returned {item_details}Return id for this request is : #{return_id}Click here to go to the admin panel
            and take
			appropriate action regarding this return request. This mail is just to notify you about the return request,
            you can
			process the return request only from back office.';
        return $template_html;
    }

    /**
     * Function to get the default email template for the return cancel generated by the customer to admin.
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultReturnCancelAdminEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'customer_email' => '{customer_email}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'attachment_text' => '{attachment_text}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'item_details' => '{item_details}',
                'ticket_track_url' => '{ticket_track_url}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_return_cancel_admin_email.tpl'
        );
        $template_html['subject'] = 'Return request cancelled by a Customer!';
        $template_html['text_content'] = 'Hey Admin,A return request has been cancelled against the order
            {order_reference}.Items in the return 
			request {item_details}Return id for this request is : #{return_id}.This mail is just to notify you about the cancellation of return request.
            ';
        return $template_html;
    }

    /**
     * Function to get the default email template for the return approved generated by the admin to customer.
     * @return array
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    protected function getDefaultReturnApprovedEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'customer_email' => '{customer_email}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'attachment_text' => '{attachment_text}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'item_details' => '{item_details}',
                'ticket_track_url' => '{ticket_track_url}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_return_approved_email.tpl'
        );
        $template_html['subject'] = 'Your Return request is Approved!';
        $template_html['text_content'] = 'Dear {customer_full_name},Greetings from {shop_name}!Your return request
			has been approved by the store owner for the following item in your order  {order_reference}.
            {item_details}Return id
			for this request is : #{return_id}The store owner will now take further actions on this return request.
            You can
			track the status of your return request in the return section of our store.We apologize for any
			inconvenience caused to you.{shop_name}24x7 Customer Support &bull;
			Flexible Payment Options &bull; Largest Collection &bull; Easy Returns';
        return $template_html;
    }

    protected function getDefaultReturnDeniedEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'customer_email' => '{customer_email}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'attachment_text' => '{attachment_text}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'item_details' => '{item_details}',
                'ticket_track_url' => '{ticket_track_url}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_return_denied_email.tpl'
        );
        $template_html['subject'] = 'Your return request is Dis-apporved!';
        $template_html['text_content'] = 'Dear {customer_full_name},Greetings from {shop_name}!
            We are sorry to inform you that your
			return request for the following item in your order
            {order_reference} has been denied by the store owner.{item_details}Return
			id for this request is : #{return_id}We apologize for any inconvenience caused to you.{shop_name}24x7
			Customer Support &bull; Flexible Payment Options &bull; Largest Collection &bull; Easy Returns';
        return $template_html;
    }
    protected function getDefaultReturnCancelEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'customer_email' => '{customer_email}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'subject' => '{subject}',
                'attachment_text' => '{attachment_text}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'item_details' => '{item_details}',
                'ticket_track_url' => '{ticket_track_url}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_return_cancel_email.tpl'
        );
        $template_html['subject'] = 'Your return request is Successfullly Cancelled!';
        $template_html['text_content'] = 'Dear {customer_full_name},Greetings from {shop_name}!
            We are sorry to inform you that your
			return request for the following item in your order
            {order_reference} has been denied by the store owner.{item_details}Return
			id for this request is : #{return_id}We apologize for any inconvenience caused to you.{shop_name}24x7
			Customer Support &bull; Flexible Payment Options &bull; Largest Collection &bull; Easy Returns';
        return $template_html;
    }
    protected function getDefaultReturnStatusEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'customer_email' => '{customer_email}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'signin_link' => '{signin_link}',
                'previous_status' => '{previous_status}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'item_details' => '{item_details}',
                'current_status' => '{current_status}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_return_status_email.tpl'
        );
        $template_html['subject'] = 'Your Return Request status has been updated!';
        $template_html['text_content'] = 'Dear {customer_full_name},Greetings from {shop_name}!We are pleased
            to inform you that status
			of your return request for the following item in your order  {order_reference} has been updated by the store
			owner.{item_details}Return id for this request is : #{return_id}Return Status has been changed from
            {previous_status}
			to {current_status}.To know more about the return request you have to login to our store and
            go to the returns section of our
			store.{shop_name}24x7 Customer Support &bull; Flexible Payment Options &bull;
            Largest Collection &bull; Easy Returns';
        return $template_html;
    }

    protected function getDefaultReturnCompletedEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'customer_email' => '{customer_email}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'signin_link' => '{signin_link}',
                'previous_status' => '{previous_status}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'item_details' => '{item_details}',
                'current_status' => '{current_status}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_return_completed_email.tpl'
        );
        $template_html['subject'] = 'Your Return Request has been completed!';
        $template_html['text_content'] = 'Dear {customer_full_name},Greetings from {shop_name}!We are pleased to inform
			you that your return request for the following item in your order
            {order_reference} has been marked completed
			by the store owner.{item_details}Return id for this request is : #{return_id}To know more about
			the return request you need  login to our store and go to the returns section of our
			store.{shop_name}24x7 Customer Support &bull; Flexible Payment Options &bull; Largest Collection &bull;
            Easy Returns';
        return $template_html;
    }

    protected function getDefaultReturnCompletedDiscountEmail()
    {
        $this->context->smarty->assign(
            array(
                'customer_full_name' => '{customer_full_name}',
                'customer_email' => '{customer_email}',
                'shop_name' => '{shop_name}',
                'order_reference' => '{order_reference}',
                'order_history_link' => '{order_history_link}',
                'ticket_number' => '{ticket_number}',
                'return_id' => '{return_id}',
                'signin_link' => '{signin_link}',
                'coupon_code' => '{coupon_code}',
                'shop_url' => '{shop_url}',
                'shop_logo' => '{shop_logo}',
                'item_details' => '{item_details}',
                'amount' => '{amount}'
            )
        );
        $template_html = array();
        $template_html['body'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_return_completed_discount_email.tpl'
        );
        $template_html['subject'] = 'Your Return Request has been completed!';
        $template_html['text_content'] = 'Dear {customer_full_name},Greetings from {shop_name}!We are pleased to inform
			you that your return request for the following item in your order
            {order_reference} has been marked completed
			by the store owner.{item_details}Return id for this request is : #{return_id}To know more about
			the return request you need  login to our store and go to the returns section of our
			store.{shop_name}24x7 Customer Support &bull; Flexible Payment Options &bull; Largest Collection &bull;
            Easy Returns';
        return $template_html;
    }

    public function getDefaultReturnGuidelines()
    {
        $this->context->smarty->assign('brace_comment1', 'it can be found in Return Slip for this Return Request');
        $html = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/default_return_guidelines.tpl'
        );
        return $html;
    }

    /**
     * Get default policy for product based on category for return request
     * @param int $product_id
     * @param int $category_id
     * @return int
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    public function getDefaultPolicy($product_id, $category_id)
    {
        $get_policy_id = 'select rd.return_data_id from ' . _DB_PREFIX_ . 'velsof_return_data as
		rd, ' . _DB_PREFIX_ . 'velsof_return_policy_product as
		rpp,' . _DB_PREFIX_ . 'velsof_return_data_lang as rdl where
		rd.return_data_id = rpp.return_data_id and rd.return_data_id = rdl.return_data_id
        and rd.policy = 1 AND rd.active = 1 AND
		(rpp.id_categories = ' . (int) $category_id . ' or rpp.id_product = ' . (int) $product_id . ') and rdl.id_shop=' . (int) $this->context->shop->id;
        $policy_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_policy_id);
        // changes by rishabh jain for marketplace compatibility
        if (Module::isEnabled('kbmarketplace') && class_exists('KbSellerProduct') && class_exists('KbSeller')) {
            $mp_config = json_decode(Configuration::get('KB_MARKETPLACE_CONFIG'), true);
            $id_seller = 0;
            if (isset($mp_config['enable_return_manager_compatibility']) && $mp_config['enable_return_manager_compatibility'] == 1) {
                $id_seller = call_user_func(array('KbSellerProduct', 'getSellerIdByProductId'), $product_id);
                /*
                 * We have added the compatibility with our deal manager plugin and we are using the function of that module class.
                 */
                if ($id_seller) {
                    if (!$policy_id) {
                        return -1;
                    }
                }
            }
        }
        // changes over
        $settings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        $exceptional_product = array();
        $exceptional_category = array();
        /**
         * If exceptional product or category is set then we will not consider the default policy
         * @date 28-03-2023
         * @commenter Prvind Panday
         */
        if (isset($settings['policy']['ex_product']) && $settings['policy']['ex_product'] != '') {
            $exceptional_product = explode(',', $settings['policy']['ex_product']);
        }
        if (isset($settings['policy']['ex_category']) && $settings['policy']['ex_category'] != '') {
            $exceptional_category = explode(',', $settings['policy']['ex_category']);
        }

        /**
         * If exceptional product or category is set then we will not consider the default policy
         * @date 28-03-2023
         * @commenter Prvind Panday
         */
        if ($policy_id && is_array($policy_id)) {
            $return_data_id = $policy_id['return_data_id'];
            if (in_array($product_id, $exceptional_product)) {
                $return_data_id = -1;
            } elseif (in_array($category_id, $exceptional_category)) {
                $return_data_id = -1;
            } elseif ($policy_id['return_data_id'] == 0) {
                $return_data_id = -1;
            }
        } else {
            /**
             * If no policy is set for the product then we will check for the category
             * @date 28-03-2023
             * @commenter Prvind Panday
             */
            $get_no_policy = 'select return_data_id from ' . _DB_PREFIX_ . 'velsof_return_policy_product as
			rpp where rpp.id_categories = ' . (int) $category_id;
            $no_policy_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_no_policy);
            if (in_array($product_id, $exceptional_product)) {
                $return_data_id = -1;
            } elseif (in_array($category_id, $exceptional_category)) {
                $return_data_id = -1;
            } elseif (isset($no_policy_id['return_data_id']) && $no_policy_id['return_data_id'] == 0) {
                $return_data_id = -1;
            } elseif (isset($settings['policy']['default']) && $settings['policy']['default'] != 0) {
                $return_data_id = $settings['policy']['default'];
            } else {
                $return_data_id = -1;
            }
        }
        return $return_data_id;
    }

    /*
     * This function is used just to include all the menu texts to translations files.
     */

    private function menuTranslationsIncludeFunction()
    {
        $this->l('Credit');
        $this->l('Refund');
        $this->l('Replacement');
        $this->l('credit');
        $this->l('refund');
        $this->l('replacement');
    }
    public function leftTextTransaltions()
    {
        /*
         * Keep menu translation strings registered for the translator export.
         * 21-07-2026
         */
        $this->menuTranslationsIncludeFunction();
        //left over transaltions
        $this->l('ReturnSlip', 'common');
        $this->l('Return Authorization Label', 'common');
        $this->l('Return Mailing Label', 'common');
        $this->l('Cut this label and affix to the outside of your return package.', 'common');
        $this->l('FROM', 'common');
        $this->l('Return Id', 'common');
        $this->l('If you do not want to use the above mailing label, you can send your return package using a carrier of your choice to the following address. You will need to pay for return postage costs.', 'common');
        $this->l('Return Authorization Label', 'common');
        $this->l('Cut this and place inside the return package with your name and signature at the bottom.', 'common');
        $this->l('Return Id', 'common');
        $this->l('Order', 'common');
        $this->l('Item Description', 'common');
        $this->l('Total Price', 'common');
        $this->l('Quantity', 'common');
        $this->l('TO WHOM IT MAY CONCERN', 'common');
        $this->l('I hereby declare that this return package contains all the items with there accessories', 'common');
        $this->l('(if any) related to this return request with', 'common');
        $this->l('Return Id', 'common');
        $this->l('against', 'common');
        $this->l('Order', 'common');
        $this->l('I also declare that the items in this package are as it is and are not tempered with.', 'common');
        $this->l('You can reject the return request if anything like this is found.', 'common');
        $this->l('Your Sincerly', 'common');
        $this->l('Please find in attachements the return slip for this return request.', 'common');
        $this->l('IMAGE', 'common');
        $this->l('ITEM', 'common');
        $this->l('QTY', 'common');
        $this->l('PRICE', 'common');
        $this->l('Phone', 'common');
    }

    public function getModuleTranslationByLanguage($module, $string, $source, $language, $sprintf = null, $js = false)
    {
        $modules = array();
        /*
         * Load module translation file when context language is available.
         * 21-07-2026
         */
        $name = $module instanceof Module ? $module->name : $module;
        if (isset(Context::getContext()->language)) {
            $file = _PS_MODULE_DIR_ . $name . '/translations/' . $language . '.php';
            if (file_exists($file)) {
                include($file);
                /*
                 * $_MODULE is defined by the included translation file.
                 * 21-07-2026
                 */
                if (isset($_MODULE) && is_array($_MODULE)) {
                    $modules = $_MODULE;
                }
            }
        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        if ($modules == null) {
            if ($sprintf !== null) {
                $string = Translate::checkAndReplaceArgs($string, $sprintf);
            }

            return str_replace('"', '&quot;', $string);
        }

        $current_key = Tools::strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $source) . '_' . $key;
        $default_key = Tools::strtolower('<{' . $name . '}prestashop>' . $source) . '_' . $key;

        if ('controller' == Tools::substr($source, -10, 10)) {
            $file = Tools::substr($source, 0, -10);
            $current_key_file = Tools::strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $file) . '_' . $key;
            $default_key_file = Tools::strtolower('<{' . $name . '}prestashop>' . $file) . '_' . $key;
        }

        if (isset($current_key_file) && !empty($modules[$current_key_file])) {
            $ret = stripslashes($modules[$current_key_file]);
        } elseif (isset($default_key_file) && !empty($modules[$default_key_file])) {
            $ret = stripslashes($modules[$default_key_file]);
        } elseif (!empty($modules[$current_key])) {
            $ret = stripslashes($modules[$current_key]);
        } elseif (!empty($modules[$default_key])) {
            $ret = stripslashes($modules[$default_key]);
        } else {
            /*
             * Fall back to original string when no module translation key matches.
             * 21-07-2026
             */
            $ret = stripslashes($string);
        }

        if ($sprintf !== null) {
            $ret = Translate::checkAndReplaceArgs($ret, $sprintf);
        }

        if ($js) {
            $ret = addslashes($ret);
        } else {
            $ret = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');
        }
        return $ret;
    }

    public function getIsoCode($return_id)
    {
        $query = 'Select id_lang from ' . _DB_PREFIX_ . 'velsof_rm_order where id_rm_order = ' . (int) $return_id;
        $language_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        $isoCode = Language::getIsoById((int) $language_id);
        if (!$isoCode) {
            $isoCode = Language::getIsoById($this->context->language->id);
        }
        return $isoCode;
    }

    //changes by vishal on 20 july 2020 for resolving the product replacement issue
    public function kbgetProductAttribute($id_product)
    {
        $kbattributes_id = Product::getProductAttributesIds($id_product);
        $attr_name = array();

        //changes by vishal on 20 july 2020 for resolving the product replacement issue

        foreach ($kbattributes_id as $key => $value) {
            $sql8 = 'SELECT quantity FROM `' . _DB_PREFIX_ . 'stock_available` Where id_product = ' . (int) $id_product . ' and id_product_attribute=' . (int)$value['id_product_attribute'];
            if (Db::getInstance()->ExecuteS($sql8)[0]['quantity'] <= 0) {
                unset($kbattributes_id[$key]);
            }
        }
        //changes end

        foreach ($kbattributes_id as $key => $value) {
            $attr_name[$key]['product_attribute_id'] = $value['id_product_attribute'];
            $atrribute_id = $value['id_product_attribute'];
            $attr_name[$key]['product_attribute_name'] = $this->getKbAttributeName($id_product, $atrribute_id);
        }

        return $attr_name;
    }

    public function getKbAttributeName($id_product, $id_product_attribute = null, $id_lang = null)
    {
        // use the lang in the context if $id_lang is not defined
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        // creates the query object
        $query = new DbQuery();

        // selects different names, if it is a combination
        if ($id_product_attribute) {
            $query->select('IFNULL(CONCAT(GROUP_CONCAT(DISTINCT agl.`name`, \' - \', al.name SEPARATOR \', \')),"-") as attr_name,pl.name as name');
        }

        // adds joins & where clauses for combinations
        if ($id_product_attribute) {
            $query->from('product_attribute', 'pa');
            $query->join(Shop::addSqlAssociation('product_attribute', 'pa'));
            $query->innerJoin('product_lang', 'pl', 'pl.id_product = pa.id_product AND pl.id_lang = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl'));
            $query->leftJoin('product_attribute_combination', 'pac', 'pac.id_product_attribute = pa.id_product_attribute');
            $query->leftJoin('attribute', 'atr', 'atr.id_attribute = pac.id_attribute');
            $query->leftJoin('attribute_lang', 'al', 'al.id_attribute = atr.id_attribute AND al.id_lang = ' . (int) $id_lang);
            $query->leftJoin('attribute_group_lang', 'agl', 'agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = ' . (int) $id_lang);
            $query->where('pa.id_product = ' . (int) $id_product . ' AND pa.id_product_attribute = ' . (int) $id_product_attribute);
        }
        return Db::getInstance()->getValue($query);
    }
    //changes end
    /*
     * Function defined to remove mails folder from the themes
     * NASep2023 DeleteMail Directory
     * @date 18-09-2023
     * @author Nikhil Aggarwal
     * @commenter Nikhil Aggarwal
     */
    public static function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
        rmdir(_PS_THEME_DIR_ . 'modules/returnmanager');
    }
    // Changes end by Nikhil
}
