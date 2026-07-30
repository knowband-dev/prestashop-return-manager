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
require_once _PS_MODULE_DIR_ . '/returnmanager/classes/RmTicket.php';

class ReturnManagerManagerModuleFrontController extends ModuleFrontController
{
    /*
     * AJAX JSON payload (explicit array type for Addons validator).
     * 21-07-2026
     * @var array
     */
    protected $kb_json = array();
    private $module_dir = '';

    public function init()
    {
        parent::init();
        $this->module_dir = _PS_MODULE_DIR_ . 'returnmanager/';
    }

    /**
     * Set Media files for this controller
     * @date 21-02-2023
     * @commenter Prvind Panday
     * @return bool
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('chosen');
        $this->addCSS($this->module_dir . 'views/css/velsof_rm_front.css');
        $this->addJS($this->module_dir . 'views/js/velsof_rm_front.js');
        $this->addJS($this->module_dir . 'views/js/jquery.autocomplete.js');
        $this->addCSS(_PS_MODULE_DIR_ . 'returnmanager/views/css/notifications/jquery.notyfy.css');
        $this->addCSS(_PS_MODULE_DIR_ . 'returnmanager/views/css/notifications/default.css');
        $this->addCSS(_PS_MODULE_DIR_ . 'returnmanager/views/css/notifications/jquery.gritter.css');
        $this->addJS(_PS_MODULE_DIR_  . 'returnmanager/views/js/notifications/jquery.gritter.min.js');
        $this->addJS(_PS_MODULE_DIR_  . 'returnmanager/views/js/notifications/jquery.notyfy.js');
        $this->addJS(_PS_MODULE_DIR_  . 'returnmanager/views/js/notifications.js');
        $this->addCSS(_PS_MODULE_DIR_ . 'returnmanager/views/css/velsof_rm_spinner.css');
        //changes by vishal on 20 july 2020 for resolving the product replacement issue
        $this->addJS(_PS_MODULE_DIR_  . 'returnmanager/views/js/select2.js');
        $this->addJS(_PS_MODULE_DIR_  . 'returnmanager/views/js/select2.min.js');
        //changes end
        /*
         * FrontController::setMedia must return bool.
         * 21-07-2026
         */
        return true;
    }

    /**
     * function to handle the ajax request made to this controller.
     * @date 21-02-2023
     * @author 
     * @commenter Prvind Panday
     */
    public function postProcess()
    {
        /**
         * if ajax request is made then call the function according to the method
         * @date 21-02-2023
         * @commenter Prvind Panday
         */

        if (Tools::isSubmit('ajax') && Tools::getValue('ajax')) {
            $common = new Common();
            /**
             * switch case to call the function according to the method, all the methods are defined in the Common class
             * @method getGuestOrder to get the order details of guest customer
             * @method getOrderDetails to get the order details of logged in customer
             * @method getCustomerOrders to get the list of orders of logged in customer
             * @method getRequestForm to get the return request form
             * @method submitReturnRequest to submit the return request
             * @method getRequestCancelForm to get the cancel request form
             * @method submitCancelRequest to submit the cancel request
             * @method kbgetProductAttribute to get the product attribute
             * @method kbgetRequestForm to get the multiple product return request form
             * @method submitMultipleReturnRequest to submit the multiple product return request
             * @method getReturnRequestDetails to get the return request details
             * @method getReturnRequestHistory to get the return request history
             * @date 21-02-2023
             * @commenter Prvind Panday
             */
            switch (Tools::getValue('method')) {
                case 'getGuestOrder':
                    $this->kb_json = $common->getGuestOrder(
                        trim(Tools::getValue('rm_reference_id')),
                        trim(Tools::getValue('rm_customer_email'))
                    );
                    break;
                case 'getOrderDetails':
                    /*
                     * Use getOrder for logged-in order detail AJAX (getOrderDetails was missing).
                     * 21-07-2026
                     */
                    $this->kb_json = $common->getOrder(true, (int) Tools::getValue('id_order'));
                    break;
                case 'getCustomerOrders':
                    $this->kb_json = $this->getCustomerOrders();
                    break;

                case 'getRequestForm':
                    $this->kb_json = $common->getRequestForm();
                    break;

                    //changes by vishal on 20 july 2020 for resolving the product replacement issue
                case 'kbgetProductAttribute':
                    $this->kb_json = $common->kbgetProductAttribute(Tools::getValue('rm_return_product'));
                    break;
                    //changes end

                    /*
                 * Start Code Added By Priyanshu on 23-March-2020 to call the Mutltiple Product Return Request function.
                 * Functionality: To implement Complete Order Return Functionality.
                 */
                case 'kbgetRequestForm':
                    $this->kb_json = $common->kbgetRequestForm();
                    break;
                    /*
                 * End Code Added By Priyanshu on 23-March-2020 to call the Mutltiple Product Return Request function.
                 * Functionality: To implement Complete Order Return Functionality.
                 */

                    /*
                 * Start Code Added By Vishal on 15-July-2020 to call the Order Cancel Request function.
                 * Functionality: To implement Complete Order Cancel Functionality.
                 */
                case 'getRequestCancelForm':
                    $this->kb_json = $common->getRequestCancelForm();
                    break;
                case 'submitCancelRequest':
                    $this->kb_json = $common->submitCancelRequest();
                    break;
                    /*
                 * End Code Added By Priyanshu on 23-March-2020 to call the Mutltiple Product Return Request function.
                 * Functionality: To implement Complete Order Return Functionality.
                 */

                case 'submitReturnRequest':
                    $this->kb_json = $common->submitReturnRequest();
                    break;
                    /*
                 * Start Code Added By Priyanshu on 23-March-2020 to call the submit function in case of Mutltiple Product Return Request.
                 * Functionality: To implement Complete Order Return Functionality.
                 */
                case 'submitReturnMultipleRequest':
                    $this->kb_json = $common->submitReturnMultipleRequest();
                    break;
                    /*
                 * End Code Added By Priyanshu on 23-March-2020 to call the submit function in case of Mutltiple Product Return Request.
                 * Functionality: To implement Complete Order Return Functionality.
                 */
                case 'cancelReturnRequest':
                    break;
                case 'ajaxproductaction':
                    echo $this->ajaxproductlist();
                    die;
            }
            echo json_encode($this->kb_json);
            die;
        }
    }

    /* Start Code Added By Priyanshu on 12-September-2020 to implement the Specific Product Selection Functionality */

    public function ajaxproductlist()
    {
        $query = Tools::getValue('q', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            die();
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $productIds = Tools::getValue('productIds', false);
        if ($productIds && $productIds != 'NaN') {
            $productIds = implode(',', array_map('intval', explode(',', $productIds)));
        } else {
            $productIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', false);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', false);

        /*
         * Boolean flags must not be passed to pSQL — use ternary SQL fragments instead.
         * 21-07-2026
         */
        $sql = 'SELECT p.`id_product`, `reference`, pl.name
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = '
            . 'p.id_product AND pl.id_lang = '
            . '' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		WHERE p.active = 1 and (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
            (!empty($productIds) ? ' AND p.id_product NOT IN (' . pSQL($productIds) . ') ' : ' ') .
            ($excludeVirtuals ? 'AND p.id_product NOT IN (SELECT pd.id_product FROM '
                . '`' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '');

        $items = Db::getInstance()->executeS($sql);
        if ($items) {
            foreach ($items as $item) {
                echo trim($item['name']) . (!empty($item['reference']) ?
                    ' (ref: ' . $item['reference'] . ')' : '') .
                    '|' . (int) ($item['id_product']) . "\n";
            }
        }
    }
    /* End Code Added By Priyanshu on 12-September-2020 to implement the Specific Product Selection Functionality */


    public function initContent()
    {
        parent::initContent();
        $order_canceled_id = Configuration::get('PS_OS_CANCELED');
        $this->context->smarty->assign('order_canceled_id', $order_canceled_id);
        $data = Configuration::get('VELSOF_RETURNMANAGER');
        $returnmanager_data = json_decode($data, true);
        $this->context->smarty->assign('return_data', $returnmanager_data);

        /**
         * if the module is enabled then only show the return request form
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if (isset($returnmanager_data['enable']) && $returnmanager_data['enable'] == 1) {
            /**
             * if the customer is logged in then show the return request form with previous orders else only show the return request form
             * @date 21-02-2023
             * @commenter Prvind Panday
             */
            if (!$this->context->customer->isLogged()) {
                /*
                 * Guest users are redirected to login; skip unused Smarty assign / always-false isLogged.
                 * 21-07-2026
                 */
                /**
                 * If customer is not logged in then redirecting the customer to login page
                 * @date 20-05-2024
                 * @commenter Ravi Kant Gupta
                 */
                Tools::redirect(
                    $this->context->link->getPageLink('my-account', true)
                );
            } else {
                $this->getCustomerOrders();
                $this->setTemplate('module:returnmanager/views/templates/front/order_detail.tpl');
            }
        } else {
            Tools::redirect('index.php');
        }
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta'])) {
            $page['meta']['title'] = sprintf(
                '%s | ' . $this->module->l('Return Manager'),
                Configuration::get('PS_SHOP_NAME')
            );
        }
        return $page;
    }

    protected function addReturnManagerToBreadcrumb()
    {
        return array(
            'title' => $this->module->l('Return Manager', 'manager'),
            'url' => $this->context->link->getModuleLink('returnmanager', 'manager')
        );
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addReturnManagerToBreadcrumb();
        return $breadcrumb;
    }

    /**
     * Get customer orders and assign to smarty
     * @return array    $orders
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    private function getCustomerOrders()
    {
        $get_orders_qry = 'select id_order from ' . _DB_PREFIX_ . 'orders
			where id_customer=' . (int) $this->context->customer->id . '
			and id_shop=' . (int) $this->context->shop->id .
            ' order by date_upd desc';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_orders_qry);
        $orders = array();
        $common = new Common();
        if ($result && count($result) > 0) {
            foreach ($result as $res) {
                $orders[] = $common->getOrder(true, $res['id_order']);
            }
        }
        $customer_info = array(
            'firstname' => $this->context->customer->firstname,
            'lastname' => $this->context->customer->lastname,
            'email' => $this->context->customer->email,
        );
        /**
         * Get return history of customer, if return history is not null then add the comments in the return history array 
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        $return_history = $common->getReturnHistory($this->context->customer->id);
        if ($return_history != null) {
            $i = 0;
            foreach ($return_history as $return) {
                $return_history[$i]['comment'] = nl2br($return['comment']);
                $i++;
            }
        }
        /*
         * Shop base URL via Tools::getShopDomain / getShopDomainSsl helpers.
         * 21-07-2026
         */
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $ps_base_url = Tools::getShopDomainSsl(true);
        } else {
            $ps_base_url = Tools::getShopDomain(true);
        }

        $this->context->smarty->assign(array(
            'isLogged' => true,
            'orders' => $orders,
            'customer_info' => $customer_info,
            'return_history' => $return_history,
            'path' => $ps_base_url . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_),
            'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager'),
            'kb_admin_link' => $this->context->link->getModuleLink('returnmanager', 'manager', array('method' => 'ajaxproductaction', 'ajax' => true)),
        ));

        /**
         * Get the return slip setting from the module configuration, if enable then assign the return slip to smarty
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        $settings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        if (isset($settings['enable_return_slip']) && $settings['enable_return_slip'] == 1) {
            $this->context->smarty->assign('return_slip', 1);
        } else {
            $this->context->smarty->assign('return_slip', 0);
        }
        /*
         * Start Code Added By Priyanshu on 23-March-2020 to assign the smarty variable for complete order Return Functionality
         * Functionality: To implement Complete Order Return Functionality.
         */
        if (isset($settings['enable_order_return']) && $settings['enable_order_return'] == 1) {
            $this->context->smarty->assign('enable_complete_order_return', 1);
        } else {
            $this->context->smarty->assign('enable_complete_order_return', 0);
        }
        /*
         * End Code Added By Priyanshu on 23-March-2020 to assign the smarty variable for complete order Return Functionality
         * Functionality: To implement Complete Order Return Functionality.
         */
        // changes over
        unset($settings);

        $order_canceled_id = Configuration::get('PS_OS_CANCELED');
        $this->context->smarty->assign('order_canceled_id', $order_canceled_id);
        /* Start Code Added By Priyanshu on 12-September-2020 to implement the Specific Product Selection Functionality */
        // $this->context->smarty->assign('kb_admin_link', Context::getContext()->link->getAdminLink('AdminModules', true).'&configure=returnmanager&ajaxproductaction=true');
        /* End Code Added By Priyanshu on 12-September-2020 to implement the Specific Product Selection Functionality */

        /**
         * if the ajax request is true then return the template with the order details
         * @date 21-02-2023
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
        if (Tools::isSubmit('ajax') && Tools::getValue('ajax')) {
            $arr = array(
                'template' => $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . 'returnmanager/views/templates/front/order_detail_content.tpl'
                )
            );
            return $arr;
        }
        /*
         * Non-AJAX path still returns orders for callers expecting an array.
         * 21-07-2026
         */
        return $orders;
    }
}
