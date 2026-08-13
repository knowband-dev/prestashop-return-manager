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
 */

//start:changes made by aayushi on 15 Nov 2018 to resolve the issue of currency symbol by replacing dompdf library
// include_once(_PS_MODULE_DIR_ . 'returnmanager/vendor/dompdf/dompdf/autoload.inc.php');
include_once(_PS_MODULE_DIR_ . 'returnmanager/vendor/dompdf/dompdf8/vendor/autoload.php');
//include_once(_PS_MODULE_DIR_ . 'returnmanager/vendor/dompdf/dompdf_config.inc.php');
//close:changes made by aayushi on 15 Nov 2018 to resolve the issue of currency symbol by replacing dompdf library
include_once(_PS_MODULE_DIR_ . 'returnmanager/vendor/barcodes.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/classes/common.php';
include_once dirname(__FILE__) . '/classes/RmTicket.php';

/**
 * The parent class is extending the "Module" core class.
 * So no need to extend "Module" core class here in this class.
 */
class ReturnManager extends Common
{

    private $data_form = array();
    protected $product_data;
    protected $json = array();
    /*
     * Module author ETH address (declared for Addons validator).
     * 21-07-2026
     * @var string
     */
    public $author_address;
    /*
     * Install-time error messages.
     * 21-07-2026
     * @var array
     */
    protected $custom_errors = array();
    /*
     * Default module settings payload.
     * 21-07-2026
     * @var array
     */
    protected $returndata_form = array();
    const PARENT_TAB_CLASS = 'AdminReturnManagerConfigure';
    const SELL_CLASS_NAME = 'SELL';

    public function __construct()
    {
        parent::__construct();

        $this->name = 'returnmanager';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'Knowband';
        $this->module_key = '2a2e51296e66453d802ff6b2714aedcf';
        $this->author_address = '0x2C366b113bd378672D4Ee91B75dC727E857A54A6';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->displayName = $this->l('Returns Manager');
        $this->description = $this->l('It allows customers to return products by using a user friendly interface.');
        /*
         * Keep menu translation helper referenced for export / validator.
         * 21-07-2026
         */
        $this->kbRegisterMenuTranslations();

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('VELSOF_RETURNMANAGER')) {
            $this->warning = $this->l('No name provided');
        }
    }

    /**
     * Install the module and register the hooks to display the banner.
     * @return boolean
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // to delete the phpunit folder if exist
        $phpunit_dir_path = _PS_MODULE_DIR_ . $this->name . '/vendor/dompdf/dompdf/lib/php-svg-lib/';
        if (is_dir($phpunit_dir_path)) {
            $this->rrmdir($phpunit_dir_path);
        }
        if (
            !parent::install() ||
            !$this->registerHook('displayCustomerAccount') ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayAdminProductsExtra') ||
            !$this->registerHook('actionProductSave') ||
            !$this->registerHook('actionExportGDPRData') ||
            !$this->registerHook('actionDeleteGDPRCustomer') ||
            !$this->registerHook('displayBackOfficeTop') ||
            !$this->registerHook('displayNav1')
        ) {
            return false;
        }

        // Create module Tables
        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_return_data` (
			  `return_data_id` int(11) NOT NULL AUTO_INCREMENT,
			  `reason` enum("1","0") NOT NULL,
			  `status` enum("1","0") NOT NULL,
			  `policy` enum("1","0") NOT NULL,
			  `whopayshipping` enum("c","so") NOT NULL,
			  `refund_days` int(4) NOT NULL DEFAULT 0,
			  `credit_days` int(4) NOT NULL DEFAULT 0,
			  `replacement_days` int(4) NOT NULL DEFAULT 0,
			  `active` enum("1","0") NOT NULL DEFAULT 1,
                          `editable` enum("1","0") NOT NULL DEFAULT 1,
			  `date_added` datetime NOT NULL,
			  `date_updated` datetime NOT NULL,
                          `credit_min_days` int(4) NOT NULL DEFAULT 0,
                          `refund_min_days` int(4) NOT NULL DEFAULT 0,
                          `replacement_min_days` int(4) NOT NULL DEFAULT 0,
                          `cancel` enum("1","0") NULL DEFAULT "0",
			  PRIMARY KEY (`return_data_id`)
			) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query);

        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_return_data_lang` (
            `return_data_id` int(11) NOT NULL,
            `id_shop` int(11) unsigned NOT NULL DEFAULT 1,
            `id_lang` int(10) unsigned NOT NULL,
            `value` text NOT NULL,
            `terms` text NOT NULL,
            `credit_message` text NOT NULL,
            `refund_message` text NOT NULL,
            `replacement_message` text NOT NULL
              ) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query);
        // chnages by rishabh jain
        $select_datatype = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . _DB_NAME_ . '" AND TABLE_NAME="' . _DB_PREFIX_ . 'velsof_return_data" AND column_name="credit_min_days"';
        $data_type = Db::getInstance()->getValue($select_datatype);
        if (empty($data_type)) {
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'velsof_return_data ADD COLUMN `credit_min_days` int(4) NOT NULL DEFAULT 0');
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'velsof_return_data ADD COLUMN `refund_min_days` int(4) NOT NULL DEFAULT 0');
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'velsof_return_data ADD COLUMN `replacement_min_days` int(4) NOT NULL DEFAULT 0');
        }
        // changes over

        // chnages by Vishal for adding cancel functionality
        $kb_reason_set = 0;
        $select_datatype = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . _DB_NAME_ . '" AND TABLE_NAME="' . _DB_PREFIX_ . 'velsof_return_data" AND column_name="cancel"';
        $data_type = Db::getInstance()->getValue($select_datatype);
        if (empty($data_type)) {
            Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'velsof_return_data ADD COLUMN `cancel` enum("1","0") NULL DEFAULT "0"');
            $kb_reason_set = 1;
        }

        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_rm_cancel` (
            `id_rm_cancel` int(11) NOT NULL AUTO_INCREMENT,
            `id_order_return` int(11) NOT NULL DEFAULT 0,
            `id_customer` int(11) NOT NULL,
            `id_order` int(11) NOT NULL,
            `id_shop` int(11) NOT NULL,
            `id_lang` int(11) NOT NULL,
            `id_cancel_reason` int(11) NOT NULL,
            `rm_other_reason` text NOT NULL,
            `comment` text NOT NULL,
            `active` enum("1","2","3","4","5") NOT NULL DEFAULT 1,
            `date_add` datetime NOT NULL,
            `date_update` datetime NOT NULL,
             PRIMARY KEY (`id_rm_cancel`)
            ) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query);
        // changes over

        /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_return_status_text_lang` (
            `return_data_id` int(11) NOT NULL,
            `id_shop` int(11) unsigned NOT NULL DEFAULT 1,
            `id_lang` int(10) unsigned NOT NULL,
            `status_message` text NOT NULL
              ) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query);
        /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */

        /* changes by rishabh jain for creating backup of coupon details */
        $query_coupon_data = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_return_coupon_data` (
			  `id_coupon_details` int(11) NOT NULL AUTO_INCREMENT,
			  `id_return` int(4) NOT NULL DEFAULT 0,
			  `id_cart_rule` int(4) NOT NULL DEFAULT 0,
			  `id_shop` int(4) NOT NULL DEFAULT 0,
                          PRIMARY KEY (`id_coupon_details`)
			) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query_coupon_data);
        /* changes over */
        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_return_policy_product` (
			`return_data_id` int(11) NOT NULL,
			`id_product` int(11) NOT NULL,
			`id_categories` int(11) NOT NULL,
			INDEX (`return_data_id`), INDEX (`id_product`), INDEX (`id_categories`))';
        Db::getInstance()->execute($query);

        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_rm_order` (
            `id_rm_order` int(11) NOT NULL AUTO_INCREMENT,
            `id_order_return` int(11) NOT NULL DEFAULT 0,
            `id_customer` int(11) NOT NULL,
            `id_order` int(11) NOT NULL,
            `id_shop` int(11) NOT NULL,
            `id_lang` int(11) NOT NULL,
            `id_rm_policy` int(11) NOT NULL,
            `return_type` varchar(50) NOT NULL,
            `days_applicable` int(4) NOT NULL,
            `id_rm_reason` int(11) NOT NULL,
            `whopayshipping` enum("c","so") NOT NULL,
            `comment` text NOT NULL,
            `id_order_detail` int(11) NOT NULL,
            `quantity` int(4) NOT NULL,
            `active` enum("1","2","3","4","5") NOT NULL DEFAULT 1,
            `date_add` datetime NOT NULL,
            `date_update` datetime NOT NULL,
             PRIMARY KEY (`id_rm_order`)
            ) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query);

        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_rm_status` (
				`id_rm_order` int(11) NOT NULL,
				`id_rm_status` int(11) NOT NULL,
				`date_add` datetime NOT NULL
				)';
        Db::getInstance()->execute($query);
        /* changes aded by rishabh on 10th july 2018 to add 1 more table to store multiple return address */

        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_rm_address` (
            `id_address` int(10) NOT NULL AUTO_INCREMENT,
            `id_country` int(10) NOT NULL,
            `id_state` int(10) DEFAULT NULL,
            `title` varchar(128) NOT NULL,
            `address1` varchar(128) NOT NULL,
            `address2` varchar(128) DEFAULT NULL,
            `postcode` varchar(12) NOT NULL,
            `city` varchar(64) NOT NULL,
            `active` tinyint(4) NOT NULL,
            `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_address`)
          ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';
        Db::getInstance()->execute($query);

        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_rm_return_address` (
            `id_return` int(12) NOT NULL,
            `id_address` int(12) NOT NULL,
             PRIMARY KEY (`id_return`)
          ) ENGINE=INNODB DEFAULT CHARSET=utf8';
        Db::getInstance()->execute($query);

        /* changes over */

        //Create Email templates table
        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_rm_email` (
			`id_template` int(10) NOT NULL auto_increment,
			`id_lang` int(10) NOT NULL,
			`id_shop` INT(11) NOT NULL DEFAULT  "0",
			`iso_code` char(4) NOT NULL,
			`template_name` varchar(255) NOT NULL,
			`text_content` text NOT NULL,
			`subject` varchar(255) NOT NULL,
			`body` text NOT NULL,
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_template`),
			INDEX (  `id_lang` )
			) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query);

        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_return_slip_data` (
			`id_slip_data` int(10) NOT NULL auto_increment,
			`id_lang` int(10) NOT NULL,
			`id_shop` INT(11) NOT NULL DEFAULT  "0",
			`iso_code` char(4) NOT NULL,
			`address` enum("1","0") NOT NULL,
			`guideline` enum("1","0") NOT NULL,
			`html_content` text NOT NULL,
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_slip_data`),
			INDEX (  `id_lang` )
			) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query);

        //Create Success Messages table
        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_rm_success_messages` (
			`id_message` int(10) NOT NULL auto_increment,
			`id_lang` int(10) NOT NULL,
			`id_shop` INT(11) NOT NULL DEFAULT  "0",
			`iso_code` char(4) NOT NULL,
			`message_name` varchar(255) NOT NULL,
			`content` text NOT NULL,
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_message`),
			INDEX (  `id_lang` )
			) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query);
        // changes by rishabh jain
        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'velsof_rm_comment` (
                `rm_comment_id` int(11) NOT NULL AUTO_INCREMENT,
                `return_id` int(11) NOT NULL,
                `comment` text,
                `date_added` datetime NOT NULL,
                `user_id` int(11) NOT NULL,
                PRIMARY KEY (`rm_comment_id`)
                ) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query);
        // changes over

        // changes by rishabh jain for creating table foe return chst
        $create_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kb_rm_ticket` (
            `id_rm_ticket` int(10) UNSIGNED NOT NULL auto_increment,
            `ticket_number` VARCHAR(50) NOT NULL,
            `id_return` int(10) UNSIGNED NOT NULL,
            `cus_fname` varchar(50) NOT NULL,
            `cus_lname` varchar(50) NULL DEFAULT NULL,
            `cus_email` varchar(100) NOT NULL,
            `phone_number` VARCHAR(50) NULL DEFAULT NULL,
            `subject` TEXT,
            `status` tinyint(1) NOT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
             PRIMARY KEY (`id_rm_ticket`))';

        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($create_table)) {
            $this->custom_errors[] = $this->l('Error while installing database.');
            return false;
        }

        /*
         * Start Code Added By Priyanshu on 23-March-2020 to Create table for the Custom field functionality
         * Functionality: To implement the Custom Fields functionality on the Return Form.
         */

        $create_custom_field_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kb_rm_custom_fields` (
				`id_velsof_rm_custom_fields` int(10) NOT NULL AUTO_INCREMENT,
				`type` enum("textbox","selectbox","textarea","radio","checkbox") NOT NULL,				
				`required` tinyint(1) NOT NULL,
				`active` tinyint(1) NOT NULL,
				`default_value` varchar(1000) NOT NULL,
				`validation_type` varchar(50) NOT NULL,
				PRIMARY KEY (`id_velsof_rm_custom_fields`)
				)  CHARACTER SET utf8 COLLATE utf8_general_ci';

        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($create_custom_field_table)) {
            $this->custom_errors[] = $this->l('Error while installing database.');
            return false;
        }

        $create_custom_fields_lang_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kb_rm_custom_fields_lang` (
				`id_velsof_rm_custom_fields_lang` int(10) NOT NULL AUTO_INCREMENT,
				`id_velsof_rm_custom_fields` int(10) NOT NULL,
				`id_lang` int(10) NOT NULL,
				`field_label` varchar(250) NOT NULL,
				`field_help_text` varchar(1000) NOT NULL,
				PRIMARY KEY (`id_velsof_rm_custom_fields_lang`)
				)  CHARACTER SET utf8 COLLATE utf8_general_ci';

        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($create_custom_fields_lang_table)) {
            $this->custom_errors[] = $this->l('Error while installing database.');
            return false;
        }

        $create_table_custom_fields_options_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kb_rm_custom_field_options_lang` (
				`id_velsof_rm_custom_field_options_lang` int(10) NOT NULL AUTO_INCREMENT,
				`id_velsof_rm_custom_fields` int(10) NOT NULL,
				`id_lang` int(10) NOT NULL,
				`option_value` varchar(100) NOT NULL,
				`option_label` varchar(1000) NOT NULL,
				PRIMARY KEY (`id_velsof_rm_custom_field_options_lang`)
			       )  CHARACTER SET utf8 COLLATE utf8_general_ci';

        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($create_table_custom_fields_options_table)) {
            $this->custom_errors[] = $this->l('Error while installing database.');
            return false;
        }

        $create_table_custom_fields_data = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kb_rm_fields_data` (
				`id_velsof_rm_fields_data` int(10) NOT NULL AUTO_INCREMENT,
				`id_velsof_rm_custom_fields` int(10) NOT NULL,
				`id_order` int(10) NOT NULL,
                                `id_rm_order` int(11) NOT NULL,
                                `id_order_return` int(11) NOT NULL DEFAULT 0,
				`id_shop` int(10) NOT NULL,
				`id_lang` int(10) NOT NULL,
				`field_value` varchar(1000) NOT NULL,
				PRIMARY KEY (`id_velsof_rm_fields_data`)
			       )  CHARACTER SET utf8 COLLATE utf8_general_ci';

        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($create_table_custom_fields_data)) {
            $this->custom_errors[] = $this->l('Error while installing database.');
            return false;
        }

        /*
         * End Code Added By Priyanshu on 23-March-2020 to Create table for the Custom field functionality
         * Functionality: To implement the Custom Fields functionality on the Return Form.
         */

        $create_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kb_rm_ticket_thread` (
            `id_rm_ticket_thread` int(10) UNSIGNED NOT NULL auto_increment,
            `id_rm_ticket` int(10) UNSIGNED NOT NULL,
            `message` TEXT NOT NULL,
            `reply_by` tinyint(1) NOT NULL,
            `is_approved` TINYINT(1) NOT NULL DEFAULT "0",
            `date_add` datetime NOT NULL,
             PRIMARY KEY (`id_rm_ticket_thread`))';

        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($create_table)) {
            $this->custom_errors[] = $this->l('Error while installing database.');
            return false;
        }
        $alter_table = 'ALTER TABLE  `' . _DB_PREFIX_ . 'velsof_rm_order` change `active` `active` enum("1","2","3","4","5") NOT NULL DEFAULT 1';
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($alter_table)) {
            $this->custom_errors[] = $this->l('Error while installing database.');
            return false;
        }
        // changes over
        $check_col_sql = 'SELECT count(*) FROM information_schema.COLUMNS
                              WHERE COLUMN_NAME = "image_path"
                              AND TABLE_NAME = "' . _DB_PREFIX_ . 'velsof_rm_order"
                              AND TABLE_SCHEMA = "' . _DB_NAME_ . '"';
        $check_col = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_col_sql);
        if ((int) $check_col == 0) {
            $query = 'ALTER TABLE `' . _DB_PREFIX_ . 'velsof_rm_order` ADD `image_path` TEXT NULL AFTER `quantity`';
            Db::getInstance()->execute($query);
        }

        /*
         * Start Code Added By Priyanshu on 23-March-2020 to add new Column ( product_id ) in the velsof_rm_order table.
         * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
         */
        $check_col_sql = 'SELECT count(*) FROM information_schema.COLUMNS
                              WHERE COLUMN_NAME = "product_id"
                              AND TABLE_NAME = "' . _DB_PREFIX_ . 'velsof_rm_order"
                              AND TABLE_SCHEMA = "' . _DB_NAME_ . '"';
        $check_col = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_col_sql);
        if ((int) $check_col == 0) {
            $query = 'ALTER TABLE `' . _DB_PREFIX_ . 'velsof_rm_order` ADD `product_id` int(11) DEFAULT NULL AFTER `id_order`';
            Db::getInstance()->execute($query);
        }

        /*
         * End Code Added By Priyanshu on 23-March-2020 to add new Column ( product_id ) in the velsof_rm_order table.
         * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
         */

        //changes by vishal on 20 july 2020 for resolving the product replacement issue
        $check_col_sql = 'SELECT count(*) FROM information_schema.COLUMNS
                              WHERE COLUMN_NAME = "replaced_product_attribute_id"
                              AND TABLE_NAME = "' . _DB_PREFIX_ . 'velsof_rm_order"
                              AND TABLE_SCHEMA = "' . _DB_NAME_ . '"';
        $check_col = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_col_sql);
        if ((int) $check_col == 0) {
            $query = 'ALTER TABLE `' . _DB_PREFIX_ . 'velsof_rm_order` ADD `replaced_product_attribute_id` int(11) DEFAULT NULL AFTER `product_id`';
            Db::getInstance()->execute($query);
        }
        //changes end

        /**
         * To enter the return policy and address into the database table
         * RKGMay2024 return policy
         * @date 20-05-2024
         * @author Ravi Kant Gupta
         */
        $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data
        values("","0","0","1","",' . (int)99 . ',' . (int) 99 . ',' .
            (int) 99 . ',"1","1",now(),now(),' . (int) 0 . ',' . (int) 0 . ',' . (int) 0 . ',"0")';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);

        $id = Db::getInstance()->Insert_ID();

        foreach (Language::getLanguages(false) as $lang) {
            $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang values(' . (int) $id . ',' .
                (int) $this->context->shop->id . ',' . (int) $lang['id_lang'] . ',"'
                . pSQL('Return Policy') . '","' .
                pSQL('Terms and Conditions') . '",
                "' . pSQL('Credit withing 3 working days') . '", "' .
                pSQL('Refund will be processed withing 3 working days') . '",
                "' . pSQL('Replacement will be processed within 7 working days') . '")';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
        }

        $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_rm_address(id_country,id_state,title,address1,address2,postcode,city,active) values (' . (int) 109 . ',' . (int) 325 . ',"' . pSQL('Demo Address') . '","' . pSQL('address1') . '","' . pSQL('address_new_line2') . '","' . pSQL('123456') . '","' . pSQL('Delhi') . '",1)';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);

        if (!Configuration::get('VELSOF_RETURN_MANAGER_DEFAULT_VALUES_CHECK')) {
            $reason_arr = array(
                array(
                    'Wrong Product',
                    'so'
                ),
                array(
                    'Wrong Attribute',
                    'so'
                ),
                array(
                    'Size Issue',
                    'c'
                ),
                array(
                    'Damaged Product',
                    'c'
                ),
                array(
                    'Change Product',
                    'c'
                )
            );
            foreach ($reason_arr as $reason) {
                //changes by vishal for adding cancel functionality
                $inserting_reason = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data values("","1","0","0","' .
                    pSQL($reason[1]) . '","","","","1","1",now(),now(),"0","0","0","0")';
                //change end
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($inserting_reason);
                $reason_id = Db::getInstance()->Insert_ID();
                /**
                 * Start Changes to fix the issue of Language data not being saved for disabled languages
                 * Replacing the param true in getLanguages(true) with false
                 * NAMar2024 language_issue
                 * @date 08-03-2024
                 * @modifier Nikhil Aggarwal
                 */
                foreach (Language::getLanguages(false) as $lang) {
                    // Changes end by Nikhil Aggarwal
                    foreach (Shop::getCompleteListOfShopsID() as $shop_id) {
                        $inserting_reason_lang = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang
							values(' . (int) $reason_id . ',' . (int) $shop_id . ',' . (int) $lang['id_lang'] .
                            ',"' . pSQL($reason[0]) . '","","","","")';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($inserting_reason_lang);
                    }
                }
            }

            $status_arr = array(
                'In Progress',
                'Processing',
                'Returned',
                'Rejected',
                'Awaiting Delivery'
            );
            foreach ($status_arr as $status) {
                //changes by vishal for adding cancel functionality
                $qry = 'insert into ' . _DB_PREFIX_ .
                    'velsof_return_data values("","0","1","0","","","","","1","0",now(),now(),"","","","0")';
                //changes end
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                $id = Db::getInstance()->Insert_ID();
                /**
                 * Start Changes to fix the issue of Language data not being saved for disabled languages
                 * Replacing the param true in getLanguages(true) with false
                 * NAMar2024 language_issue
                 * @date 08-03-2024
                 * @modifier Nikhil Aggarwal
                 */
                foreach (Language::getLanguages(false) as $lang) {
                    // Changes end by Nikhil Aggarwal
                    foreach (Shop::getCompleteListOfShopsID() as $shop_id) {
                        $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang
							values(' . (int) $id . ',' . (int) $shop_id . ',' . (int) $lang['id_lang'] . ',"' .
                            pSQL($status) . '","","","","")';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    }
                }
            }

            //changes by vishal for adding cancel functionality
            $cancel_arr = array(
                'Order Cretaed by Mistake',
                'Product is not required anymore',
                'Cheaper alternative available for lesser price',
                'Product is being delivered to a wrong address',
                'Order would not Arrive on Time',
                'Need to change Payment Method'
            );
            foreach ($cancel_arr as $cancel) {
                //changes by vishal for adding cancel functionality
                $qry = 'insert into ' . _DB_PREFIX_ .
                    'velsof_return_data values("","0","0","0","","","","","1","0",now(),now(),"","","","1")';
                //changes end
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                $id = Db::getInstance()->Insert_ID();
                /**
                 * Start Changes to fix the issue of Language data not being saved for disabled languages
                 * Replacing the param true in getLanguages(true) with false
                 * NAMar2024 language_issue
                 * @date 08-03-2024
                 * @modifier Nikhil Aggarwal
                 */
                foreach (Language::getLanguages(false) as $lang) {
                    // Changes end by Nikhil Aggarwal
                    foreach (Shop::getCompleteListOfShopsID() as $shop_id) {
                        $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang
							values(' . (int) $id . ',' . (int) $shop_id . ',' . (int) $lang['id_lang'] . ',"' .
                            pSQL($cancel) . '","","","","")';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    }
                }
            }
            //changes end

            Configuration::updateGlobalValue('VELSOF_RETURN_MANAGER_DEFAULT_VALUES_CHECK', 1);
        }

        if ($kb_reason_set) {
            $cancel_arr = array(
                'Order Cretaed by Mistake',
                'Product is not required anymore',
                'Cheaper alternative available for lesser price',
                'Product is being delivered to a wrong address',
                'Order would not Arrive on Time',
                'Need to change Payment Method'
            );
            foreach ($cancel_arr as $cancel) {
                //changes by vishal for adding cancel functionality
                $qry = 'insert into ' . _DB_PREFIX_ .
                    'velsof_return_data values("","0","0","0","","","","","1","0",now(),now(),"","","","1")';
                //changes end
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                $id = Db::getInstance()->Insert_ID();
                /**
                 * Start Changes to fix the issue of Language data not being saved for disabled languages
                 * Replacing the param true in getLanguages(true) with false
                 * NAMar2024 language_issue
                 * @date 08-03-2024
                 * @modifier Nikhil Aggarwal
                 */
                foreach (Language::getLanguages(false) as $lang) {
                    // Changes end by Nikhil Aggarwal
                    foreach (Shop::getCompleteListOfShopsID() as $shop_id) {
                        $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang
							values(' . (int) $id . ',' . (int) $shop_id . ',' . (int) $lang['id_lang'] . ',"' .
                            pSQL($cancel) . '","","","","")';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    }
                }
            }
        }

        if (!Configuration::get('VELSOF_RETURN_MANAGER_MAIL_CHECK')) {
            $mail_dir = dirname(__FILE__) . '/mails/en';

            if (Context::getContext()->language->iso_code != 'en') {
                $new_dir = dirname(__FILE__) . '/mails/' . Context::getContext()->language->iso_code;
                $this->copyfolder($mail_dir, $new_dir);
            }

            Configuration::updateGlobalValue('VELSOF_RETURN_MANAGER_MAIL_CHECK', 1);
            Configuration::updateGlobalValue(
                'VELSOF_RETURN_MANAGER_DEFAULT_TEMPLATE_LANG',
                Context::getContext()->language->iso_code
            );
        }

        $this->returndata_form = $this->getDefaultSettings();

        $order_stauses = array();
        Configuration::updateValue('VELSOF_RETURNMANAGER_ORDER_STATUS', json_encode($order_stauses));
        /* Start Code Added By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
        $policy_stauses = 0;
        Configuration::updateValue('VELSOF_RETURNMANAGER_POLICY_STATUS', $policy_stauses);
        /* End Code Added By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
        //changes by vishal for adding cancel functioanlity
        $order_cancel_stauses = array();
        Configuration::updateValue('VELSOF_RETURNMANAGER_CANCEL_STATUS', json_encode($order_cancel_stauses));
        //changes end
        Configuration::updateGlobalValue('VELSOF_RETURNMANAGER', json_encode($this->returndata_form));

        // changes by rishabh jain for customer chat admin controller
        if (!$this->addTabForCustomerChat()) {
            return false;
        }
        // chnages over

        /* Start Changes Added By Priyanshu on 8-March-2021 to add the Return Manager Tab in the Left Menu */
        $this->installKbTabs();
        /* End Changes Added By Priyanshu on 8-March-2021 to add the Return Manager Tab in the Left Menu */

        return true;
    }

    /**
     *
     * @return boolean
     * Function defined by Priyanshu to install the configuration tab (8-March-2021)
     */

    /*
     * Resolve tab id by class_name via DB lookup (avoids deprecated Tab helper).
     * 21-07-2026
     * @param string $class_name
     * @return int
     */
    protected function kbGetTabIdByClassName($class_name)
    {
        return (int) Db::getInstance()->getValue(
            'SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name` = "' . pSQL($class_name) . '"'
        );
    }

    public function installKbTabs()
    {
        $parentTab = new Tab();
        $parentTab->name = array();
        /**
         * Start Changes to fix the issue of Language data not being saved for disabled languages
         * Replacing the param true in getLanguages(true) with false
         * NAMar2024 language_issue
         * @date 08-03-2024
         * @modifier Nikhil Aggarwal
         */
        foreach (Language::getLanguages(false) as $lang) {
            // Changes end by Nikhil Aggarwal
            $parentTab->name[$lang['id_lang']] = $this->l('Knowband Return Manager');
        }

        $parentTab->class_name = self::PARENT_TAB_CLASS;
        $parentTab->module = $this->name;
        $parentTab->active = true;
        $parentTab->id_parent = $this->kbGetTabIdByClassName(self::SELL_CLASS_NAME);
        $parentTab->icon = 'bookmark';
        $parentTab->add();

        $id_parent_tab = (int) $this->kbGetTabIdByClassName(self::PARENT_TAB_CLASS);
        $admin_menus = $this->adminSubMenus();

        foreach ($admin_menus as $menu) {
            $tab = new Tab();
            /**
             * Start Changes to fix the issue of Language data not being saved for disabled languages
             * Replacing the param true in getLanguages(true) with false
             * NAMar2024 language_issue
             * @date 08-03-2024
             * @modifier Nikhil Aggarwal
             */
            foreach (Language::getLanguages(false) as $lang) {
                // Changes end by Nikhil Aggarwal
                if ($this->getModuleTranslationByLanguage($this->name, $menu['name'], $this->name, $lang['iso_code']) != '') {
                    $tab->name[$lang['id_lang']] = $this->getModuleTranslationByLanguage($this->name, $menu['name'], $this->name, $lang['iso_code']);
                } else {
                    $tab->name[$lang['id_lang']] = $menu['name'];
                }
            }
            $tab->class_name = $menu['class_name'];
            $tab->module = $this->name;
            $tab->active = (bool) $menu['active'];
            $tab->id_parent = $id_parent_tab;
            $tab->add();
        }
        return true;
    }

    /**
     *
     * @return array
     */
    public function adminSubMenus()
    {
        $subMenu = array(
            array(
                'class_name' => 'AdminReturnManager',
                'name' => $this->l('General Settings'),
                'active' => true,
            ),
        );

        return $subMenu;
    }

    public function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object) && !is_link($dir . "/" . $object)) {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public function addTabForCustomerChat()
    {
        $id_parent_tab = (int) $this->kbGetTabIdByClassName('SELL');
        $tab = new Tab();
        /**
         * Start Changes to fix the issue of Language data not being saved for disabled languages
         * Replacing the param true in getLanguages(true) with false
         * NAMar2024 language_issue
         * @date 08-03-2024
         * @modifier Nikhil Aggarwal
         */
        foreach (Language::getLanguages(false) as $lang) {
            // Changes end by Nikhil Aggarwal
            $tab->name[$lang['id_lang']] = $this->l('Customer Tickets');
        }

        $tab->class_name = 'AdminRmTicketSystem';
        $tab->module = $this->name;
        $tab->active = false;
        $tab->id_parent = $id_parent_tab;
        if ($tab->add()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * uninstall module and delete configuration values also delete the tab and deregister hooks
     * @return boolean
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    public function uninstall()
    {
        if (
            !parent::uninstall() ||
            !Configuration::deleteByName('VELSOF_RETURNMANAGER') ||
            !$this->unregisterHook('displayNav1') ||
            !$this->unregisterHook('displayAdminProductsExtra') ||
            !$this->unregisterHook('actionProductSave') ||
            !$this->unregisterHook('actionExportGDPRData') ||
            !$this->unregisterHook('displayHeader') ||
            !$this->unregisterHook('actionDeleteGDPRCustomer') ||
            !$this->unregisterHook('displayBackOfficeTop') ||
            !$this->unregisterHook('displayCustomerAccount')
        ) {
            return false;
        }

        /* Start Changes Added By Priyanshu on 8-March-2021 to remove the Return Manager Tab from the Left Menu */
        $this->unInstallKbTabs();
        /* End Changes Added By Priyanshu on 8-March-2021 to remove the Return Manager Tab from the Left Menu */

        return true;
    }

    /*
     * Added by Priyanshu to uninstall the admin tabs on 8-March-2021
     */
    protected function unInstallKbTabs()
    {
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $idTab = $this->kbGetTabIdByClassName(self::PARENT_TAB_CLASS);
            if ($idTab != 0) {
                $tab = new Tab($idTab);
                if ($tab->delete()) {
                    $subMenuList = $this->adminSubMenus();
                    if (!empty($subMenuList)) {
                        foreach ($subMenuList as $subList) {
                            $idTab = $this->kbGetTabIdByClassName($subList['class_name']);
                            if ($idTab != 0) {
                                $tab = new Tab($idTab);
                                $tab->delete();
                            }
                        }
                    }
                }
            }
        } else {
            $parentTab = new Tab($this->kbGetTabIdByClassName(self::PARENT_TAB_CLASS));
            $parentTab->delete();

            $admin_menus = $this->adminSubMenus();

            foreach ($admin_menus as $menu) {
                $sql = 'SELECT id_tab FROM `' . _DB_PREFIX_ . 'tab` WHERE class_name = "' . pSQL($menu['class_name']) . '" 
                    AND module = "' . pSQL($this->name) . '"';
                $id_tab = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                $tab = new Tab((int) $id_tab);
                $tab->delete();
            }
        }
        return true;
    }

    /**
     * Function to copy folder from one location to another
     * @param string $source
     * @param string $destination
     * @return bool
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    public function copyfolder($source, $destination)
    {
        $directory = opendir($source);
        if ($directory === false) {
            return false;
        }
        if (!is_dir($destination)) {
            mkdir($destination);
        }
        while (($file = readdir($directory)) != false) {
            Tools::copy($source . '/' . $file, $destination . '/' . $file);
        }
        closedir($directory);
        /*
         * Explicit bool return for Addons validator.
         * 21-07-2026
         */
        return true;
    }

    public function downloadSimplifiedFiles($file_name)
    {
        $files_dir = $this->getCommonFilesPath();
        $file = $files_dir . $file_name;
        $file_rename = basename($file);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_rename . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    private function getCommonFilesPath()
    {
        return _PS_IMG_DIR_ . 'velsof_return/';
    }

    /**
     * hookactiondeletegdprcustomer is executed if any customer request to delete his/her data 
     * @param array $customer
     * @return boolean
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            if (Module::isEnabled('returnmanager')) {
                $config = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
                if (($config['enable'] == 1) && ($config['enable_gdpr_delete'] == 1)) {
                    $customerFound = false;
                    $sqlCustomer = "SELECT id_customer FROM " . _DB_PREFIX_ . "customer WHERE email = '" . pSQL($customer['email']) . "'";
                    $customerData = Db::getInstance()->getRow($sqlCustomer);
                    if (!Tools::isEmpty($customerData) && $customerData) {
                        $sqlSelectReturn = "SELECT * FROM " . _DB_PREFIX_ . "velsof_rm_order WHERE id_customer = '" . (int) $customerData['id_customer'] . "'";
                        $res = Db::getInstance()->ExecuteS($sqlSelectReturn);
                        if (count($res)) {
                            $sqlDeleteStatus = "DELETE FROM " . _DB_PREFIX_ . "velsof_rm_status WHERE id_rm_order IN ( Select distinct id_rm_order from " . _DB_PREFIX_ . "velsof_rm_order where id_customer = '" . (int) $customerData['id_customer'] . "')";
                            Db::getInstance()->execute($sqlDeleteStatus);
                            $sqlDeleteOrder = "DELETE FROM " . _DB_PREFIX_ . "velsof_rm_order WHERE id_customer = '" . (int) $customerData['id_customer'] . "'";
                            Db::getInstance()->execute($sqlDeleteOrder);
                            $customerFound = true;
                        }
                    }
                    if ($customerFound) {
                        return json_encode(true);
                    } else {
                        return json_encode($this->l('Return Manager: No user found with this email.', 'abandonedcart_core'));
                    }
                }
            }
        }
        /*
         * Explicit false when GDPR delete does not apply.
         * 21-07-2026
         */
        return false;
    }
    public function hookActionExportGDPRData($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            if (Module::isEnabled('returnmanager')) {
                $config = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
                if ($config['enable'] == 1) {
                    $export_data = array();
                    $sqlCustomer = "SELECT id_customer FROM " . _DB_PREFIX_ . "customer WHERE email = '" . pSQL($customer['email']) . "'";
                    $customer_data = Db::getInstance()->getRow($sqlCustomer);
                    if (count($customer_data) && $customer_data) {
                        $getCustomerReturnOrderSql = "SELECT * from " . _DB_PREFIX_ . "velsof_rm_order where id_customer = '" . (int) $customer_data['id_customer'] . "'";
                        $getCustomerReturnOrderList = Db::getInstance()->executeS($getCustomerReturnOrderSql);
                        if (count($getCustomerReturnOrderList)) {
                            foreach ($getCustomerReturnOrderList as $key => $return_details) {
                                $return_data = $this->getReturnData($return_details['id_rm_order']);
                                if (count($return_data)) {
                                    $current = current($return_data[0]);
                                    $end = end($return_data[0]);
                                    $export_data[] = array(
                                        $this->l('Return ID') => $return_data[1]['return_id'],
                                        $this->l('Cutomer Name') => $return_data[1]['cust_name'],
                                        $this->l('Email') => $return_data[1]['email'],
                                        $this->l('Product Name') => $return_data[1]['product_name'],
                                        $this->l('Product Attribute') => $return_data[1]['product_attr'],
                                        $this->l('Quantity') => $return_data[1]['quantity'],
                                        $this->l('Unit Price(Inc Tax)') => $return_data[1]['unit_price_tax_incl'],
                                        $this->l('Return Reason') => $return_data[1]['reason'],
                                        $this->l('Order Reference') => $return_data[1]['order_reference'],
                                        $this->l('Order Shipping Charge') => $return_data[1]['order_shipping'],
                                        $this->l('Order Total') => $return_data[1]['order_total'],
                                        $this->l('Order Date') => $return_data[1]['order_date'],
                                        $this->l('Current Status') => $current['status'],
                                        $this->l('Return Date') => $end['date'],
                                    );
                                }
                            }
                        }
                    }
                    if (count($export_data)) {
                        return json_encode($export_data);
                    } else {
                        return json_encode($this->l('Return Manager : No User found with this email.'));
                    }
                }
            }
        }
        /*
         * Explicit false when GDPR export does not apply.
         * 21-07-2026
         */
        return false;
    }

    /**
     * any request made from configuration form in admin panel is handled here, this function is responsible to display the configuration forms in the admin panel
     * @date 28-03-2023
     * @commenter Prvind Panday
     */
    public function getContent()
    {
        /*
         * Start Code Added By Priyanshu on 23-March-2020 to Process actions on Custom Fields.
         * Functionality: To implement the Custom Fields functionality on the Return Form.
         */
        if (Tools::isSubmit('custom_fields_action')) {
            $json = array();
            switch (Tools::getValue('custom_fields_action')) {
                case 'deleteCustomFieldRow':
                    $id_velsof_rm_custom_fields = Tools::getValue('id_velsof_rm_custom_fields');
                    $this->deleteWholeRowData($id_velsof_rm_custom_fields);
                    //Called deleteWholeRowData
                    // no break
                case 'addCustomFieldForm':
                    $custom_field_form_values = Tools::getValue('custom_fields');
                    $id_velsof_rm_custom_fields = $this->addNewCustomField($custom_field_form_values);
                    $result_custom_fields_details = $this->getRowDataCurrentLang($id_velsof_rm_custom_fields);
                    $json['response'] = $result_custom_fields_details[0];
                    break;
                case 'editCustomFieldForm':
                    $custom_field_form_values = Tools::getValue('edit_custom_fields');
                    $id_velsof_rm_custom_fields = $this->editCustomField($custom_field_form_values);
                    $result_custom_fields_details = $this->getRowDataCurrentLang($id_velsof_rm_custom_fields);
                    $json['response'] = $result_custom_fields_details[0];
                    break;
                case 'displayEditCustomFieldForm':
                    $id_velsof_rm_custom_fields = Tools::getValue('id');
                    $show_option_field = 0;
                    $result_custom_fields_details_basic = $this->getFieldDetailsBasic($id_velsof_rm_custom_fields);

                    // Setting variable value so that the options field can be showed or hidden by default
                    if ($result_custom_fields_details_basic[0]['type'] == 'selectbox' || $result_custom_fields_details_basic[0]['type'] == 'radio' || $result_custom_fields_details_basic[0]['type'] == 'checkbox') {
                        $show_option_field = 1;
                    }

                    $array_fields_lang = $this->getFieldLangs($id_velsof_rm_custom_fields);
                    $array_fields_options = $this->getFieldOptions($id_velsof_rm_custom_fields);

                    $this->context->smarty->assign('id_velsof_rm_custom_fields', $id_velsof_rm_custom_fields);
                    $this->context->smarty->assign('custom_field_basic_details', $result_custom_fields_details_basic[0]);
                    $this->context->smarty->assign('custom_field_lang_details', $array_fields_lang);
                    $this->context->smarty->assign('custom_field_option_details', $array_fields_options);
                    $this->context->smarty->assign('language_current', $this->context->language->id);
                    /**
                     * Start Changes to fix the issue of Language data not being saved for disabled languages
                     * Passing the param in getLanguages() as false
                     * NAMar2024 language_issue
                     * @date 08-03-2024
                     * @modifier Nikhil Aggarwal
                     */
                    $this->context->smarty->assign('languages', Language::getLanguages(false));
                    // Changes end by Nikhil Aggarwal
                    $this->context->smarty->assign('show_option_field', $show_option_field);
                    $this->context->smarty->assign('module_dir_url', _MODULE_DIR_);
                    $json['response'] = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'returnmanager/views/templates/admin/edit_form_custom_fields.tpl');
                    break;
            }
            echo json_encode($json);
            die;
        }

        /*
         * End Code Added By Priyanshu on 23-March-2020 to Process actions on Custom Fields.
         * Functionality: To implement the Custom Fields functionality on the Return Form.
         */

        if (Tools::isSubmit('ajax')) {
            $this->ajaxProcess(Tools::getValue('method'));
        }

        if (Tools::isSubmit('getDownloadFile')) {
            $fileName = Tools::getValue('file');
            $this->downloadSimplifiedFiles($fileName);
        }

        $this->addBackOfficeMedia();
        $output = null;

        if (Tools::isSubmit('submit_form')) {
            $post_data = Tools::getValue('velsof_return');
            $custom_data = Tools::getValue('velsof_return_custom');
            $custom_link_data = Tools::getValue('velsof_return_link');
            $custom_data['js'] = urlencode($custom_data['js']);
            $msg_data = Tools::getValue('velsof_return_msg');
            $slip_data = Tools::getValue('velsof_return_slip');
            if (Tools::getIsset('selectItemkb_order_statuses')) {
                $order_stauses = array();
                $order_stauses = Tools::getValue('selectItemkb_order_statuses');
                Configuration::updateValue('VELSOF_RETURNMANAGER_ORDER_STATUS', json_encode($order_stauses));
            }
            /* Start Code Added By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
            if (Tools::getIsset('kb_policy_statuses')) {
                $policy_stauses = Tools::getValue('kb_policy_statuses');
                Configuration::updateValue('VELSOF_RETURNMANAGER_POLICY_STATUS', $policy_stauses);
            }
            /* End Code Added By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
            //changes by vishal for adding order cancellation functioanlity
            if (Tools::getIsset('selectItemkb_cancel_statuses')) {
                $order_stauses = array();
                $order_stauses = Tools::getValue('selectItemkb_cancel_statuses');
                Configuration::updateValue('VELSOF_RETURNMANAGER_CANCEL_STATUS', json_encode($order_stauses));
            }
            //changes end
            /**
             * Start Changes to fix the issue of Language data not being saved for disabled languages
             * Replacing the param true in getLanguages(true) with false
             * NAMar2024 language_issue
             * @date 08-03-2024
             * @modifier Nikhil Aggarwal
             */
            $languages = Language::getLanguages(false);
            // Changes end by Nikhil Aggarwal
            $Custom_field_block_title = array();
            foreach ($languages as $lang) {
                $Custom_field_block_title[$lang['id_lang']] = Tools::getValue('custom_field_title_' . $lang['id_lang']);
            }
            $custom_data['custom_block_title'] = $Custom_field_block_title;
            $custom_link_data['link_html'] = Tools::htmlentitiesUTF8($custom_link_data['link_html']);
            $custom_link_data['link_html_class'] = Tools::htmlentitiesUTF8($custom_link_data['link_html_class']);
            unset($post_data['enable_header_menu']);
            unset($post_data['enable_chat']);
            unset($post_data['enable_image_upload']);
            unset($post_data['enable_cancel_return']);
            unset($post_data['enable_cancel']);
            unset($post_data['credit']);
            unset($post_data['replacement']);
            unset($post_data['enable_address']);
            unset($post_data['enable_product_selection_replacement']);
            unset($post_data['enable_custom_field']);
            unset($custom_data['css']);
            unset($custom_data['js']);

            Configuration::updateValue('VELSOF_RETURNMANAGER', json_encode($post_data));
            Configuration::updateValue('VELSOF_RETURNMANAGER_LINK', json_encode($custom_link_data));
            Configuration::updateValue('VELSOF_RETURNMANAGER_CUSTOM', json_encode($custom_data));

            $msg_arr = array(
                'credit' => $msg_data['credit_post_message'],
                'refund' => $msg_data['refund_post_message'],
                'replace' => $msg_data['replacement_post_message'],
                //changes by vishal for adding cancel functionailty
                'cancel' => $msg_data['cancel_post_message']
                //changes end
            );

            $this->saveMessagesData($msg_arr, Language::getIsoById((int) $post_data['success_messages_lang']));
            $this->saveReturnSlipData($slip_data, Language::getIsoById((int) $post_data['return_slip_lang']));
            $output .= $this->displayConfirmation($this->l('Settings has been updated successfully'));
        }

        if (!is_writable($this->getTemplateDir())) {
            $output .= $this->displayError(
                $this->l('Please give read/write permission to ') . $this->getTemplateDir() . $this->l(' directory.')
            );
        }
        if (!is_writable(_PS_IMG_DIR_)) {
            $output .= $this->displayError(
                $this->l('Please give read/write permission to ') . _PS_IMG_DIR_ . $this->l(' directory.')
            );
        } else {
            if (!file_exists(_PS_IMG_DIR_ . 'velsof_return')) {
                mkdir(_PS_IMG_DIR_ . 'velsof_return', 0777, true);
            }
        }

        if (!$this->getPolicy()) {
            $output .= $this->displayError(
                $this->l('Please create atleast one Return Policy and map category to it (Go to Return Policy Tab)')
            );
        }
        /**
         * Start Changes to fix the issue of Language data not being saved for disabled languages
         * Replacing the param true in getLanguages(true) with false
         * NAMar2024 language_issue
         * @date 08-03-2024
         * @modifier Nikhil Aggarwal
         */
        $store_languages = Language::getLanguages(false);
        // Changes end by Nikhil Aggarwal
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $ps_base_url = Tools::getShopDomainSsl(true);
        } else {
            $ps_base_url = Tools::getShopDomain(true);
        }
        $this->smarty->assign('img_lang_dir', $ps_base_url . __PS_BASE_URI__ .
            str_replace(_PS_ROOT_DIR_ . '/', '', _PS_LANG_IMG_DIR_));

        $settings = Configuration::get('VELSOF_RETURNMANAGER');
        $this->data_form = json_decode($settings, true);

        //changes by vishal for adding cancel functionality
        $this->data_form['cancel_post_message'] = $this->getMessageByName('cancel', $store_languages[0]['iso_code']);
        //changes end
        $this->data_form['credit_post_message'] = $this->getMessageByName('credit', $store_languages[0]['iso_code']);
        $this->data_form['refund_post_message'] = $this->getMessageByName('refund', $store_languages[0]['iso_code']);
        $this->data_form['replacement_post_message'] = $this->getMessageByName(
            'replace',
            $store_languages[0]['iso_code']
        );
        if ($this->getReturnSlipDataByLanguage('address', $store_languages[0]['iso_code'])) {
            $this->data_form['return_slip_address'] = $this->getReturnSlipDataByLanguage(
                'address',
                $store_languages[0]['iso_code']
            );
        }
        if ($this->getReturnSlipDataByLanguage('guide', $store_languages[0]['iso_code'])) {
            $this->data_form['return_slip_guidelines'] = $this->getReturnSlipDataByLanguage(
                'guide',
                $store_languages[0]['iso_code']
            );
        }

        $this->data_form['custom_data'] = json_decode(Configuration::get('VELSOF_RETURNMANAGER_CUSTOM'), true);

        /**
         * Start changes to fix the issue of accessing index on null
         * NAAug2023 null
         * @date 08-08-2023
         * @author Nikhil Aggarwal
         */
        /**
         * Start Changes to fix the issue of custom CSS not being saved when custom JS is not present.
         * Earlier I was checking that if in the custom data, if the custom JS is present then the custom JS will be decoded. Otherwise, the custom data will be blank.
         * So, now I have checked that if in the Custom Data, the Custom JS is not present or blank, then we will pass the Custom JS as blank instead of passing the Custom Data as blank.
         * NAMar2024 custom_css
         * @date 08-03-2024
         * @modifier Nikhil Aggarwal
         */
        if (isset($this->data_form['custom_data']) && is_array($this->data_form['custom_data'])) {
            if (isset($this->data_form['custom_data']['js']) && $this->data_form['custom_data']['js'] != '') {
                $this->data_form['custom_data']['js'] = urldecode($this->data_form['custom_data']['js']);
            } else {
                $this->data_form['custom_data']['js'] = '';
            }
        }
        // Changes end by Nikhil
        if (Configuration::get('VELSOF_RETURNMANAGER_LINK')) {
            $return_hook_link = json_decode(Configuration::get('VELSOF_RETURNMANAGER_LINK'), true);
            $return_hook_link['link_html'] = Tools::htmlentitiesDecodeUTF8($return_hook_link['link_html']);
            $return_hook_link['link_html_class'] = Tools::htmlentitiesDecodeUTF8($return_hook_link['link_html_class']);
            $this->data_form['link_html'] = $return_hook_link['link_html'];
            $this->data_form['link_html_class'] = $return_hook_link['link_html_class'];
        }
        $shop_id = $this->context->shop->id;
        $cat_qry = 'SELECT c.id_category AS id_category, cl1.name AS name, c.id_parent FROM ' .
            _DB_PREFIX_ . 'category c
            LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl1 ON (c.id_category = cl1.id_category)
            WHERE cl1.id_lang = ' . (int) $this->context->language->id . ' AND cl1.id_shop = ' .
            (int) $this->context->shop->id . '
            and c.id_category > 1 GROUP BY c.id_category ORDER BY id_category';
        $category_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($cat_qry);
        $categories = array();
        foreach ($category_data as $cat) {
            if ($cat['id_parent'] == 0) {
                $categories[] = $cat;
            } else {
                $cat_path = $this->createCatLevel(
                    array(
                        $cat['name']
                    ),
                    $cat['id_parent'],
                    $category_data
                );
                $path = implode(' >> ', array_reverse($cat_path));
                $categories[] = array(
                    'id_category' => $cat['id_category'],
                    'name' => $path
                );
            }
        }
        $shop_id = Context::getContext()->shop->id;
        $select_all_status_lang = 'select data.return_data_id, lang.value, lang.id_lang from ' .
            _DB_PREFIX_ . 'velsof_return_data
            data, ' . _DB_PREFIX_ . 'velsof_return_data_lang lang where active="1" and id_shop=' . (int) $shop_id . '
            and data.status = "1" and data.return_data_id= lang.return_data_id';
        $all_status_lang = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($select_all_status_lang);

        $reason_detail = $this->select('reason');
        $status_detail = $this->select('status');
        $policy_detail = $this->select('policy');
        $address_detail = $this->select('address');
        //changes by vishal for adding cancel functionaliity
        $cancel_detail = $this->select('cancel');
        //changes end

        $return_pending = $this->getReturns();
        //changes by vishal for adding cancel functionality
        $cancel_order = $this->getCancels();
        if (isset($cancel_order['data']) && $cancel_order['data'] != null) {
            $i = 0;
            foreach ($cancel_order['data'] as $return_pen) {
                $cancel_order['data'][$i]['comment'] = nl2br($return_pen['comment']);
                $i++;
            }
        }
        //changes end
        if (isset($return_pending['data']) && $return_pending['data'] != null) {
            $i = 0;
            foreach ($return_pending['data'] as $return_pen) {
                $return_pending['data'][$i]['comment'] = nl2br($return_pen['comment']);
                $i++;
            }
        }
        // added address form by rishabh
        // Generate countries list
        $countries = Country::getCountries($this->context->language->id, true);
        $list = '';
        $id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        foreach ($countries as $country) {
            $selected = ((int) $country['id_country'] === $id_country) ? ' selected="selected"' : '';
            $list .= '<option value="' . (int) $country['id_country'] . '"' . $selected . '>' . htmlentities($country['name'], ENT_COMPAT, 'UTF-8') . '</option>';
        }
        $state_query = 'Select id_state,name from ' . _DB_PREFIX_ . 'state where id_country = ' . (int) $id_country;
        $state_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($state_query);
        // Assign vars
        $this->context->smarty->assign(
            array(
                'countries_list' => $list,
                'countries' => $countries,
                'sl_country' => (int) $id_country,
            )
        );
        $this->context->smarty->assign('state_list', $state_list);
        $this->context->smarty->assign('number_state', count($state_list));

        // end
        /* Start Added by Anshul Mittal on "24-08-2017" to add a functionality of email editing before sending it to customer */
        $this->context->smarty->assign('send_email_lang', $this->context->language->id);
        /* End Added by Anshul Mittal on "24-08-2017" to add a functionality of email editing before sending it to customer */
        $return_active = $this->getReturns(2);
        $archive_returns = $this->getReturns(4);

        // changes by rishabh jain for canceled return
        $cancel_returns = $this->getReturns(5);

        // changes over
        $order_controller = $this->context->link->getAdminLink('AdminOrders');
        $this->context->smarty->assign('return_history', $return_active);
        $this->context->smarty->assign('return_pending', $return_pending);
        //changes by vishal for adding cancel functionality
        $this->context->smarty->assign('kb_order_data', OrderState::getOrderStates($this->context->language->id));
        $this->context->smarty->assign('cancel_pending', $cancel_order);
        $this->context->smarty->assign('cancel_complete_order', $this->getCancels(2));
        //changes end
        // changes by rishabh jain for canceled return
        $this->context->smarty->assign('cancel_returns', $cancel_returns);
        // changes over
        $this->context->smarty->assign('archive_returns', $archive_returns);
        $this->context->smarty->assign('customer_controller', $this->context->link->getAdminLink('AdminCustomers'));
        $this->context->smarty->assign('order_controller', $order_controller);
        $orderStatuses = array();
        $statuses = OrderState::getOrderStates((int) Context::getContext()->language->id);
        foreach ($statuses as $status) {
            $orderStatuses[] = array(
                'id_option' => $status['id_order_state'],
                'name' => $status['name']
            );
        }
        // changes by rishabh jain for order status selection functionality
        $selected_order_status = array();
        $selected_order_status = json_decode(Configuration::get('VELSOF_RETURNMANAGER_ORDER_STATUS'), true);
        $this->context->smarty->assign('selected_order_status', $selected_order_status);
        // changes over
        /* Start Code Added By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
        $selected_policy_status = Configuration::get('VELSOF_RETURNMANAGER_POLICY_STATUS');
        $this->context->smarty->assign('selected_policy_status', $selected_policy_status);
        /* End Code Added By Priyanshu on 16-March-2021 to implement the functionality to calulate days according to the selected order status */
        //changes by vishal for adding cancel functionality
        $selected_cancel_status = array();
        $selected_cancel_status = json_decode(Configuration::get('VELSOF_RETURNMANAGER_CANCEL_STATUS'), true);
        $this->context->smarty->assign('selected_cancel_status', $selected_cancel_status);
        //changes end
        $base_path = ($ps_base_url . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_));
        $this->smarty->assign(
            array(
                'velsof_return' => $this->data_form,
                'available_order_status' => $orderStatuses,
                'action' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules') .
                    '&configure=' . $this->name,
                'cancel_action' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'reasons' => $reason_detail,
                'status' => $status_detail,
                //changes by vishal for adding cancel functionality
                'cancel_detail' => $cancel_detail,
                'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager'),
                //changes end
                'address' => $address_detail,
                'status_lang_detail' => $all_status_lang,
                'policy' => $policy_detail,
                'category' => $categories,
                'path' => $base_path,
                'ad' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_),
                'iso' => $this->context->language->iso_code,
                /**
                 * Start Changes to fix the issue of Language data not being saved for disabled languages
                 * Replacing the param true in getLanguages(true) with false
                 * NAMar2024 language_issue
                 * @date 08-03-2024
                 * @modifier Nikhil Aggarwal
                 */
                'languages' => Language::getLanguages(false),
                'count_languages' => count(Language::getLanguages(false)),
                // Changes end by Nikhil Aggarwal
                'templates_list' => $this->getTemplatesListArray()
            )
        );
        /*
         * Start Code Added By Priyanshu on 23-March-2020 to Create Custom Fields Form in the Admin panel.
         * Functionality: To implement the Custom Fields functionality on the Return Form.
         */
        $custom_field_type_array = array(
            array(
                'id' => null,
                'label' => $this->l('Select Type'),
            ),
            array(
                'id' => 'text',
                'label' => 'Text'
            ),
            array(
                'id' => 'select',
                'label' => 'Select'
            ),
            array(
                'id' => 'radio',
                'label' => 'Radio'
            ),
            array(
                'id' => 'checkbox',
                'label' => 'Checkbox'
            ),
            array(
                'id' => 'textarea',
                'label' => 'Text Area'
            ),
        );

        $current_language_id = $this->context->language->id;

        // Getting the details of custom fields
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_rm_custom_fields cf ';
        $query = $query . 'JOIN ' . _DB_PREFIX_ . 'kb_rm_custom_fields_lang cfl ';
        $query = $query . 'ON cf.id_velsof_rm_custom_fields = cfl.id_velsof_rm_custom_fields ';
        $query = $query . 'WHERE id_lang = ' . (int) $current_language_id;

        $result_custom_fields_details = Db::getInstance()->executeS($query);
        foreach ($result_custom_fields_details as $key => $field_details) {
            $result_custom_fields_details[$key]['type'] = $this->getCustomFieldsTypeTranslatedText($field_details['type']);
        }

        $this->smarty->assign('custom_fields_details', $result_custom_fields_details);
        $this->smarty->assign('custom_field_type_array', $custom_field_type_array);
        $this->context->smarty->assign('language_current', $current_language_id);
        /*
         * End Code Added By Priyanshu on 23-March-2020 to Create Custom Fields Form in the Admin panel.
         * Functionality: To implement the Custom Fields functionality on the Return Form.
         */
        /* Start Code Added by Priyanshu on 18-March-2021 to implement the functionality to show Return listing count on Top of the Admin panel */
        if (Tools::getValue('return_listing') !== '') {
            if (Tools::getValue('return_listing') == 'ordercanceled' || Tools::getValue('return_listing') == 'ordercomplete' || Tools::getValue('return_listing') == 'pendingreturn' || Tools::getValue('return_listing') == 'activereturn' || Tools::getValue('return_listing') == 'canceledreturn') {
                $active_listing = Tools::getValue('return_listing');
            } else {
                $active_listing = 'default';
            }
        } else {
            $active_listing = 'default';
        }

        // changes done by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL
        $order_controller = $this->context->link->getAdminLink('AdminOrders');
        $oc = explode('?', $order_controller);
        $customer_controller = $this->context->link->getAdminLink('AdminCustomers');
        $cc = explode('?', $customer_controller);

        $this->context->smarty->assign('customer_controller', $customer_controller);
        $this->context->smarty->assign('customer_controller1', $cc['0']);
        $this->context->smarty->assign('customer_controller2', $cc['1']);
        $this->context->smarty->assign('order_controller', $order_controller);
        $this->context->smarty->assign('order_controller1', $oc['0']);
        $this->context->smarty->assign('order_controller2', $oc['1']);
        // changes done by Kanishka Kannoujia on 17-06-2022 for the correction of order and customer URL


        $this->context->smarty->assign('active_listing', $active_listing);
        /**
         * Start changes to fix the issue of using modifier directly in tpl
         * NAAug2023 modifier
         * @date 09-08-2023
         * @author Nikhil Aggarwal
         */
        $this->context->smarty->registerPlugin("modifier", "impl", "implode");
        // Changes end by Nikhil
        /* End Code Added by Priyanshu on 18-March-2021 to implement the functionality to show Return listing count on Top of the Admin panel */
        $output .= $this->display(__FILE__, 'views/templates/admin/admin_returnmanager.tpl');
        return $output;
    }

    /*
     * Function to get the translated text for the Custom Field Type.
     * Functionality: To implement the Custom Fields functionality on the Return Form.
     * Added By Priyanshu on 23-March-2020
     */
    private function getCustomFieldsTypeTranslatedText($type_value)
    {
        $final_txt = '';
        switch ($type_value) {
            case 'textbox':
                $final_txt = $this->l('Text Box');
                break;
            case 'selectbox':
                $final_txt = $this->l('Select Box');
                break;
            case 'textarea':
                $final_txt = $this->l('Text Area');
                break;
            case 'radio':
                $final_txt = $this->l('Radio Buttons');
                break;
            case 'checkbox':
                $final_txt = $this->l('Check Boxes');
                break;
        }
        return $final_txt;
    }

    /*
     * Function to fetch details of a saved Custom field.
     * Functionality: To implement the Custom Fields functionality on the Return Form.
     * Added By Priyanshu on 23-March-2020
     */
    public function getFieldDetailsBasic($id_velsof_rm_custom_fields)
    {
        //Getting all values of a custom field to pass it in the edit form tpl file which is randered when edit icon is clicked
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_rm_custom_fields cf ';
        $query = $query . 'WHERE cf.id_velsof_rm_custom_fields = "' . (int) $id_velsof_rm_custom_fields . '"';
        return Db::getInstance()->executeS($query);
    }

    /*
     * Function used to fetch the fields language value.
     * Functionality: To implement the Custom Fields functionality on the Return Form.
     * Added By Priyanshu on 23-March-2020
     */

    public function getFieldLangs($id_velsof_rm_custom_fields)
    {
        $query_field_lang = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_rm_custom_fields_lang cfl ';
        $query_field_lang .= 'WHERE cfl.id_velsof_rm_custom_fields = "' . (int) $id_velsof_rm_custom_fields . '"';
        $result_custom_fields_details_field_lang = Db::getInstance()->executeS($query_field_lang);
        //Converting array into suitable format
        $array_fields_lang = array();
        foreach ($result_custom_fields_details_field_lang as $lang_data) {
            $array_fields_lang[$lang_data['id_lang']] = array(
                'field_label' => $lang_data['field_label'],
                'field_help_text' => $lang_data['field_help_text'],
            );
        }
        return $array_fields_lang;
    }

    /*
     * Function used to fetch the field options.
     * Functionality: To implement the Custom Fields functionality on the Return Form.
     * Added By Priyanshu on 23-March-2020
     */

    public function getFieldOptions($id_velsof_rm_custom_fields)
    {
        $query_field_options = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_rm_custom_field_options_lang cfol ';
        $query_field_options .= 'WHERE cfol.id_velsof_rm_custom_fields = "' . (int) $id_velsof_rm_custom_fields . '"';
        $result_custom_fields_details_field_options = Db::getInstance()->executeS($query_field_options);
        //Converting array into suitable format and converting into raw format again
        $array_fields_options = array();
        foreach ($result_custom_fields_details_field_options as $lang_data) {
            $option_value = $lang_data['option_value'];
            $option_label = $lang_data['option_label'];
            $array_fields_options[$lang_data['id_lang']] .= "$option_value|$option_label";
        }
        return $array_fields_options;
    }

    /*
     * Function to delete Custom Field data from the database.
     * Functionality: To implement the Custom Fields functionality on the Return Form.
     * Added By Priyanshu on 23-March-2020
     */
    public function deleteWholeRowData($id_velsof_rm_custom_fields)
    {
        $where_delete = 'id_velsof_rm_custom_fields = ' . (int) $id_velsof_rm_custom_fields;
        Db::getInstance()->delete('kb_rm_custom_fields', $where_delete);
        Db::getInstance()->delete('kb_rm_custom_fields_lang', $where_delete);
        Db::getInstance()->delete('kb_rm_custom_field_options_lang', $where_delete);
    }

    /*
     * Function to Create a new Custom Field.
     * Functionality: To implement the Custom Fields functionality on the Return Form.
     * Added By Priyanshu on 23-March-2020
     */

    public function addNewCustomField($custom_field_form_values)
    {
        $type = $custom_field_form_values['type'];
        $required = $custom_field_form_values['required'];
        $active = $custom_field_form_values['active'];
        $default_value = $custom_field_form_values['default_value'];
        $validation_type = $custom_field_form_values['validation_type'];

        // Making validation type none
        if ($type == 'selectbox' || $type == 'checkbox' || $type == 'radio') {
            $validation_type = 0;
        }

        $labels = $custom_field_form_values['field_label'];
        // Calling the function which processes multilang field data
        $labels = $this->processMultilangFieldValues($labels);


        $help_texts = $custom_field_form_values['help_text'];
        // Calling the function which processes multilang field data
        $help_texts = $this->processMultilangFieldValues($help_texts);

        $field_options = $custom_field_form_values['field_options'];
        // Calling the function which processes multilang field data
        $field_options = $this->processMultilangFieldValues($field_options);

        // Save data into kb_rm_custom_fields table
        $field_data = array(
            'type' => pSQL($type),
            'required' => pSQL($required),
            'active' => pSQL($active),
            'default_value' => pSQL($default_value),
            'validation_type' => pSQL($validation_type),
        );
        Db::getInstance()->insert('kb_rm_custom_fields', $field_data);

        // Getting the last inserted id
        $id_velsof_rm_custom_fields = Db::getInstance()->Insert_ID();

        // Save data into kb_rm_custom_fields_lang table
        $this->saveFieldLangs($id_velsof_rm_custom_fields, $labels, $help_texts);

        // Saving the data into kb_rm_custom_field_options_lang table
        $this->saveFieldOptions($id_velsof_rm_custom_fields, $field_options);
        return $id_velsof_rm_custom_fields;
    }

    public function editCustomField($custom_field_form_values)
    {
        $id_velsof_rm_custom_fields = $custom_field_form_values['id_velsof_rm_custom_fields'];
        $type = $custom_field_form_values['type'];
        $required = $custom_field_form_values['required'];
        $active = $custom_field_form_values['active'];
        $default_value = $custom_field_form_values['default_value'];
        $validation_type = $custom_field_form_values['validation_type'];

        $labels = $custom_field_form_values['field_label'];
        //Calling the function which processes multilang field data
        $labels = $this->processMultilangFieldValues($labels);

        $help_texts = $custom_field_form_values['help_text'];
        // Calling the function which processes multilang field data
        $help_texts = $this->processMultilangFieldValues($help_texts);

        $field_options = $custom_field_form_values['field_options'];
        // Calling the function which processes multilang field data
        $field_options = $this->processMultilangFieldValues($field_options);

        // Making validation type none
        if ($type == 'selectbox' || $type == 'checkbox' || $type == 'radio') {
            $validation_type = 0;
            // Start: Code Added by Anshul to add the new custom field type
        } elseif ($type == 'date') {
            $validation_type = 'isDate';
        } elseif ($type == 'file') {
            $validation_type = 'isFile';
        }
        // End:Code Added by Anshul to add the new custom field type

        // Updating the value into kb_rm_custom_fields table
        $update_field_data = array(
            'type' => pSQL($type),
            'required' => pSQL($required),
            'active' => pSQL($active),
            'default_value' => pSQL($default_value),
            'validation_type' => pSQL($validation_type),
        );
        $where = 'id_velsof_rm_custom_fields = ' . (int) $id_velsof_rm_custom_fields;
        Db::getInstance()->update('kb_rm_custom_fields', $update_field_data, $where);

        // Delete previously saved data from kb_rm_custom_fields_lang table
        $where_delete = 'id_velsof_rm_custom_fields = ' . (int) $id_velsof_rm_custom_fields;
        Db::getInstance()->delete('kb_rm_custom_fields_lang', $where_delete);

        // Insert new data into the table
        $this->saveFieldLangs($id_velsof_rm_custom_fields, $labels, $help_texts);

        // Delete the previously saved data from kb_rm_custom_field_options_lang table
        $where_delete = 'id_velsof_rm_custom_fields = ' . (int) $id_velsof_rm_custom_fields;
        Db::getInstance()->delete('kb_rm_custom_field_options_lang', $where_delete);

        // Insert new data into kb_rm_custom_field_options_lang table
        $this->saveFieldOptions($id_velsof_rm_custom_fields, $field_options);

        return $id_velsof_rm_custom_fields;
    }

    /**
     * Function which processes all the multilang field values and sets default values in empty indexes
     * @param array $arary_filed_values
     * @return array
     */
    public function processMultilangFieldValues($arary_filed_values)
    {
        $arr_empty_indexes = array();
        $flag_first = 0;
        /*
         * Default empty-label fill value when no language has content yet.
         * 21-07-2026
         */
        $default_label_value = '';
        foreach ($arary_filed_values as $id_lang => $field_value) {
            // If field_value is empty then store the languade id in the array so that we can process it later
            if (empty($field_value)) {
                $arr_empty_indexes[] = $id_lang;
            } else {
                // If first label with some value is found
                if ($flag_first == 0) {
                    $default_label_value = $field_value;
                    $flag_first = 1;
                }
            }
        }

        // Setting the value of first field into all the empty labels
        foreach ($arr_empty_indexes as $id_lang) {
            $arary_filed_values[$id_lang] = $default_label_value;
        }
        return $arary_filed_values;
    }

    /**
     * Function to save the multilangual data into the database
     * @param int|string $id_velsof_rm_custom_fields
     * @param array $labels
     * @param array $help_texts
     */
    public function saveFieldLangs($id_velsof_rm_custom_fields, $labels, $help_texts)
    {
        foreach ($labels as $id_lang => $label) {
            $field_data_lang = array(
                'id_velsof_rm_custom_fields' => (int) $id_velsof_rm_custom_fields,
                'id_lang' => (int) $id_lang,
                'field_label' => pSQL((string) $label),
                'field_help_text' => pSQL((string) $help_texts[$id_lang]),
            );
            Db::getInstance()->insert('kb_rm_custom_fields_lang', $field_data_lang);
        }
    }

    /**
     * Function to save the options data into the database
     * Function modified by RS for fixing the pSQL() errors reported by PrestaShop Addons team
     * @param int|string $id_velsof_rm_custom_fields
     * @param array $field_options
     */
    public function saveFieldOptions($id_velsof_rm_custom_fields, $field_options)
    {
        foreach ($field_options as $id_lang => $option_lang_wise) {
            $array_options = explode("\n", $option_lang_wise);
            foreach ($array_options as $option) {
                if (!empty($option)) {
                    // Exploding the option textbox rows using |. On doing this we will get option value on 0th index and option label on 1st index
                    $array_option_data = explode('|', $option);
                    $option_data_lang = array(
                        'id_velsof_rm_custom_fields' => (int) $id_velsof_rm_custom_fields,
                        'id_lang' => (int) $id_lang,
                        'option_value' => pSQL((string) $array_option_data[0]),
                        'option_label' => pSQL((string) $array_option_data[1])
                    );
                    Db::getInstance()->insert('kb_rm_custom_field_options_lang', $option_data_lang);
                }
            }
        }
    }

    /**
     * Returns the row data of current selected language from custom fields tables
     * Function modified by RS for fixing the pSQL() errors reported by PrestaShop Addons team
     * @param int|string $id_velsof_rm_custom_fields
     */
    public function getRowDataCurrentLang($id_velsof_rm_custom_fields)
    {
        $current_language_id = $this->context->language->id;
        // Getting details of the row
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_rm_custom_fields cf ';
        $query = $query . 'JOIN ' . _DB_PREFIX_ . 'kb_rm_custom_fields_lang cfl ';
        $query = $query . 'ON cf.id_velsof_rm_custom_fields = cfl.id_velsof_rm_custom_fields ';
        $query = $query . 'WHERE cf.id_velsof_rm_custom_fields = "' . (int) $id_velsof_rm_custom_fields . '" AND
			id_lang = "' . (int) $current_language_id . '"';
        return Db::getInstance()->executeS($query);
    }

    private function createCatLevel($cat_name, $id_parent, $categories)
    {
        foreach ($categories as $cat) {
            if ($cat['id_category'] == $id_parent) {
                $cat_name[] = $cat['name'];
                if ($cat['id_parent'] == 0) {
                    return $cat_name;
                } else {
                    $cat_name = $this->createCatLevel($cat_name, $cat['id_parent'], $categories);
                }
            }
        }
        return $cat_name;
    }

    private function getTemplatesListArray()
    {
        return array(
            'new_ret_cust' => $this->l('New Return Request Notice (Customer)'),
            'new_ret_adm' => $this->l('New Return Request Notice (Admin)'),
            'ret_app' => $this->l('Return Request Approved'),
            'ret_den' => $this->l('Return Request Denied'),
            'ret_stat' => $this->l('Return Request Status Change'),
            'ret_comp' => $this->l('Return Request Completed non discounted'),
            'ret_comp_discount' => $this->l('Return Request Completed with coupon code'),
            'ret_cancel' => $this->l('Cancellation of Return Request (Customer)'),
            'ret_cancel_admin' => $this->l('Cancellation of Return Request (Admin)'),
            // customer chat mails
            'new_ticket_client' => $this->l('Generation of new Ticket(Customer)'),
            'new_ticket_admin' => $this->l('Generation of new Ticket (Admin)'),
            'client_reply_client' => $this->l('Customer reply on ticket (Customer)'),
            'client_reply_admin' => $this->l('Customer reply on ticket (Admin)'),
            'admin_reply_client' => $this->l('Admin reply on ticket (Customer)'),
            /*
             * Start Code Added By Priyanshu on 23-March-2020 to show the replacement mails in the Mailing list in the admin panel.
             * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
             */
            'amount_adjust_to_admin' => $this->l('Amount Adjust while replacement (Admin)'),
            'amount_adjust_to_client' => $this->l('Amount Adjust while replacement (Customer)'),
            /*
             * End Code Added By Priyanshu on 23-March-2020 to show the replacement mails in the Mailing list in the admin panel.
             * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
             */
            //            'status_change_client' => $this->l('Ticket Status Change (Customer)'),
            //changes by vishal for adding cancel order functionality
            'new_cancel_cust' => $this->l('New Cancellation Request Notice (Customer)'),
            'new_cancel_adm' => $this->l('New Cancellation Request Notice (Admin)'),
            'cancel_app' => $this->l('Cancellation Request Approved'),
            'cancel_den' => $this->l('Cancellation Request Denied'),
            //changes end
        );
    }

    private function ajaxProcess($method)
    {
        $this->json = array();
        switch ($method) {
            case 'policy_to_product_mapping':
                $this->json = $this->productPolicyMapping();
                break;
            case 'get_mapped_product':
                $this->json = $this->getMappedProduct();
                break;
            case 'getCategoryProduct':
                $this->json = $this->getCategoryProduct();
                break;
            case 'changeAddressStatus':
                $this->changeAddressStatus();
                die();
            case 'AddData':
                $this->addData();
                die;
            case 'getData':
                $this->json = $this->getData();
                break;
            case 'getStateList':
                $this->json = $this->getStateList();
                die();
            case 'delete':
                $this->delete();
                die;
                //changes by vishal on 28 dec 2020 for adding delete all category mapping button
            case 'delete_all_category_mapping':
                $sqlDelete = "DELETE FROM " . _DB_PREFIX_ . "velsof_return_policy_product";
                if (Db::getInstance()->execute($sqlDelete)) {
                    return 1;
                } else {
                    return 0;
                }
                //changes end
                //changes by vishal for adding cancel functionality
            case 'getNextCancelListingPage':
                $this->json = $this->getCancels(Tools::getValue('active_status'));
                break;
            case 'approvecancel':
                $cancel_id = Tools::getValue('ret');
                /* Start Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $temp_allow = array();
                $temp_allow['subject'] = Tools::getValue('subject_email_allow');
                $temp_allow['body'] = Tools::getValue('body_email_allow');
                $temp_allow['body'] = str_replace("&amp;", "#####@@@@@@", $temp_allow['body']);
                $temp_allow['body'] = str_replace("#####@@@@@@", "&;", $temp_allow['body']);
                $temp_allow['body'] = str_replace("@@@@@@@@@@@@", "&", $temp_allow['body']);
                /* End Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $update_return = 'update ' . _DB_PREFIX_ .
                    'velsof_rm_cancel set active=2, date_update=now() where id_rm_cancel=' . (int) $cancel_id . ' and
                    id_shop=' . (int) $this->context->shop->id;
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_return);
                $get_return_data = 'select id_order,id_customer, id_rm_cancel as cancel_id from ' .
                    _DB_PREFIX_ . 'velsof_rm_cancel
                    where id_rm_cancel = ' . (int) $cancel_id;
                $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return_data);
                //order state change code
                $kb_order_obj = new Order($return_data['id_order']);
                $history = new OrderHistory();

                $history->id_order = $kb_order_obj->id;
                $history->id_employee = (int) $this->context->employee->id;
                $use_existings_payment = false;
                if (!$kb_order_obj->hasInvoice()) {
                    $use_existings_payment = true;
                }
                $history->changeIdOrderState((int) Tools::getValue('order_state'), $kb_order_obj, $use_existings_payment);
                //change by vishal for resolving order status update issue.
                $carrier = new Carrier($kb_order_obj->id_carrier, $kb_order_obj->id_lang);
                $templateVars = array();
                /*
                 * Use Order::getShippingNumber() — shipping_number property removed in newer PS.
                 * 21-07-2026
                 */
                $kb_shipping_number = method_exists($kb_order_obj, 'getShippingNumber')
                    ? (string) $kb_order_obj->getShippingNumber()
                    : '';
                if (
                    $history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $kb_shipping_number !== ''
                ) {
                    $templateVars = array(
                        '{followup}' => str_replace('@', $kb_shipping_number, $carrier->url)
                    );
                }

                // Save all changes (legacy advanced stock sync block removed for PS 8+ compatibility)
                $history->addWithemail(true, $templateVars);
                //changes end
                $this->json['mail_sent'] = $this->sendNotificationEmail('cancel_app', $return_data, $temp_allow, 1);
                break;
            case 'denyCancel':
                $cancel_id = Tools::getValue('ret');
                /* Start Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $temp_deny = array();
                $temp_deny['subject'] = Tools::getValue('subject_email_deny');
                $temp_deny['body'] = Tools::getValue('body_email_deny');
                $temp_deny['body'] = str_replace("&amp;", "#####@@@@@@", $temp_deny['body']);
                $temp_deny['body'] = str_replace("#####@@@@@@", "&;", $temp_deny['body']);
                $temp_deny['body'] = str_replace("@@@@@@@@@@@@", "&", $temp_deny['body']);
                /* End Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $update_return = 'update ' . _DB_PREFIX_ .
                    'velsof_rm_cancel set active=3, date_update=now() where id_rm_cancel=' . (int) $cancel_id . ' and
                    id_shop=' . (int) $this->context->shop->id;
                $get_return_data = 'select id_order,id_customer, id_rm_cancel as cancel_id from ' .
                    _DB_PREFIX_ . 'velsof_rm_cancel
                    where id_rm_cancel = ' . (int) $cancel_id;
                $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return_data);
                /* Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer */
                $this->json['mail_sent'] = $this->sendNotificationEmail('cancel_den', $return_data, $temp_deny, 1);
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_return);
                break;
                //changes end

            case 'approveReturn':
                $return_id = Tools::getValue('ret');
                /* Start Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $temp_allow = array();
                $temp_allow['subject'] = Tools::getValue('subject_email_allow');
                $temp_allow['body'] = Tools::getValue('body_email_allow');
                $temp_allow['body'] = str_replace("&amp;", "#####@@@@@@", $temp_allow['body']);
                $temp_allow['body'] = str_replace("#####@@@@@@", "&;", $temp_allow['body']);
                $temp_allow['body'] = str_replace("@@@@@@@@@@@@", "&", $temp_allow['body']);
                /* End Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $update_return = 'update ' . _DB_PREFIX_ .
                    'velsof_rm_order set active=2, date_update=now() where id_rm_order=' . (int) $return_id . ' and
                    id_shop=' . (int) $this->context->shop->id;
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_return);
                //changes by vishal on 20 july 2020 for resolving the product replacement issue
                $get_return_data = 'select id_order, id_order_detail, return_type, product_id, replaced_product_attribute_id, id_customer, id_rm_order as return_id from ' .
                    //changes end
                    _DB_PREFIX_ . 'velsof_rm_order
                    where id_rm_order = ' . (int) $return_id;
                $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return_data);
                $settings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
                if (isset($settings['enable_return_slip']) && $settings['enable_return_slip'] == 1) {
                    $this->generateReturnSlip((int) $return_id);
                }
                /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                $get_previous_status = 'select id_rm_status from ' . _DB_PREFIX_ . 'velsof_rm_status where
                        id_rm_order = ' . (int) $return_id . ' order by date_add desc';
                $previous_status_id = $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_previous_status);

                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT status_message from
                    ' . _DB_PREFIX_ . 'velsof_return_status_text_lang where return_data_id = ' . (int) $previous_status_id['id_rm_status'] . ' and
                    id_lang=' . (int) $this->context->language->id . ' and id_shop=' .
                    (int) $this->context->shop->id);
                $return_data['current_status_text_message'] = $result['status_message'];
                /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                /* Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer */

                $this->json['mail_sent'] = $this->sendNotificationEmail('ret_app', $return_data, $temp_allow);
                unset($settings);
                break;
            case 'denyReturn':
                $return_id = Tools::getValue('ret');
                /* Start Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $temp_deny = array();
                $temp_deny['subject'] = Tools::getValue('subject_email_deny');
                $temp_deny['body'] = Tools::getValue('body_email_deny');
                $temp_deny['body'] = str_replace("&amp;", "#####@@@@@@", $temp_deny['body']);
                $temp_deny['body'] = str_replace("#####@@@@@@", "&;", $temp_deny['body']);
                $temp_deny['body'] = str_replace("@@@@@@@@@@@@", "&", $temp_deny['body']);
                /* End Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $update_return = 'update ' . _DB_PREFIX_ .
                    'velsof_rm_order set active=3, date_update=now() where id_rm_order=' . (int) $return_id . ' and
                    id_shop=' . (int) $this->context->shop->id;
                $get_return_data = 'select id_order, id_order_return, id_customer, id_rm_order as return_id from ' .
                    _DB_PREFIX_ . 'velsof_rm_order
                    where id_rm_order = ' . (int) $return_id;
                $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return_data);
                /* Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer */
                $this->json['mail_sent'] = $this->sendNotificationEmail('ret_den', $return_data, $temp_deny);
                $this->updateRMATables('return_denied', $return_data);
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_return);
                break;
            case 'completeReturn':
                $return_id = Tools::getValue('ret');
                $is_generate_coupon = (int) Tools::getValue('is_generate_coupon', 0);
                $is_update_inventory = (int) Tools::getValue('is_update_inventory', 0);
                /* Start Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $temp_comp = array();
                $temp_comp['subject'] = Tools::getValue('subject_email_comp');
                $temp_comp['body'] = Tools::getValue('body_email_comp');
                $temp_comp['body'] = str_replace("&amp;", "#####@@@@@@", $temp_comp['body']);
                $temp_comp['body'] = str_replace("#####@@@@@@", "&;", $temp_comp['body']);
                $temp_comp['body'] = str_replace("@@@@@@@@@@@@", "&", $temp_comp['body']);
                /* End Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $update_return = 'update ' . _DB_PREFIX_ .
                    'velsof_rm_order set active=4, date_update=now() where id_rm_order=' . (int) $return_id . ' and
                    id_shop=' . (int) $this->context->shop->id;
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_return);

                $get_return_data = 'select id_order, return_type, id_order_detail,product_id, replaced_product_attribute_id, quantity, id_order_return, id_customer, id_rm_order as return_id from ' .
                    _DB_PREFIX_ . 'velsof_rm_order
                    where id_rm_order = ' . (int) $return_id;
                $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return_data);

                //changes by vihal for fix error : coupon is send after completing the return request
                $settings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);

                //changes end

                /* change started 16th July 2019
                 * @author Rishabh Jain
                 * To update the inventory if return type is refund
                 */
                // changes started by rishabh jain to update the inventory
                if ($is_update_inventory == 1) {
                    if (isset($return_data['id_order_detail']) && $return_data['id_order_detail'] != '') {
                        $get_name = 'select product_name,product_attribute_id,product_id,unit_price_tax_incl
                                        from ' . _DB_PREFIX_ . 'order_detail where id_order_detail=' . (int) $return_data['id_order_detail'] .
                            ' and id_shop=' . (int) $this->context->shop->id;
                        $pro_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_name);
                        $id_product_attribute = $pro_name['product_attribute_id'];
                        $tmp_stock_qty = StockAvailable::getQuantityAvailableByProduct(
                            $pro_name['product_id'],
                            $id_product_attribute,
                            $this->context->shop->id
                        );

                        StockAvailable::setQuantity(
                            $pro_name['product_id'],
                            $id_product_attribute,
                            (int) ($tmp_stock_qty + $return_data['quantity'])
                        );
                    }
                }
                $coupon_code = '';
                $is_coupon_exists = 0;
                if ($is_generate_coupon) {
                    $id_customer = $return_data['id_customer'];
                    $order_detail_obj = new OrderDetail($return_data['id_order_detail']);
                    $coupon_amount = (float) $order_detail_obj->unit_price_tax_incl * $return_data['quantity'];
                    $query_coupon_data = 'Select id_coupon_details from `' . _DB_PREFIX_ . 'velsof_return_coupon_data` where id_return = ' . (int) $return_data['id_order_return'];
                    $is_coupon_exists = Db::getInstance()->getValue($query_coupon_data);
                    if ($is_coupon_exists) {
                        $query_coupon_rule = 'Select id_cart_rule from `' . _DB_PREFIX_ . 'velsof_return_coupon_data` where id_return = ' . (int) $return_data['id_order_return'];
                        $id_cart_rule = (int) Db::getInstance()->getValue($query_coupon_rule);
                        $cart_rule_obj = new CartRule($id_cart_rule);
                        $coupon_code = $cart_rule_obj->code;
                    } else {
                        $coupon_code = $this->generatecoupon($return_data, $id_customer);
                    }
                    $order_obj = new Order($return_data['id_order']);
                    $reduction_currency = $order_obj->id_currency;
                    $curr_obj = new Currency($reduction_currency);
                    $return_data['coupon_code'] = $coupon_code;
                    $return_data['amount'] = $this->kbFormatPrice($coupon_amount, $curr_obj);
                    /* Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer */
                    $this->json['mail_sent'] = $this->sendNotificationEmail('ret_comp_discount', $return_data, $temp_comp);
                } else {
                    /* Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer */
                    $this->json['mail_sent'] = $this->sendNotificationEmail('ret_comp', $return_data, $temp_comp);
                }
                // to update the inventory
                /* changes over */
                $this->updateRMATables('return_completed', $return_data);
                break;
            case 'getReturnData':
                $return_id = Tools::getValue('ret');
                $order_controller = $this->context->link->getAdminLink('AdminOrders');
                $this->context->smarty->assign('order_controller', $order_controller);
                $this->context->smarty->assign('return_detail', $this->getReturnData($return_id));
                echo $this->display(__FILE__, 'views/templates/admin/return_detail.tpl');
                die;
            case 'getInternalNoteData':
                $return_id = Tools::getValue('ret');
                $internalNoteSql = 'Select comment ,date_added, concat(c.firstname ," ", c.lastname) as name from ' . _DB_PREFIX_ . 'velsof_rm_comment rc join ' . _DB_PREFIX_ . 'employee c on rc.user_id = c.id_employee where return_id = ' . (int) $return_id . ' order by rc.date_added desc';
                $internalNotes = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($internalNoteSql);
                $this->context->smarty->assign('internal_note_list', $internalNotes);
                $this->context->smarty->assign('rm_current_return_id', $return_id);
                echo $this->display(__FILE__, 'views/templates/admin/internal_note.tpl');
                die;
            case 'addInternalNote':
                $return_id = Tools::getValue('ret');
                $note = Tools::getValue('note');
                $insertNoteSql = 'Insert into ' . _DB_PREFIX_ . 'velsof_rm_comment (return_id,comment,user_id, date_added) values ("' . (int) $return_id . '", "' . pSQL($note) . '", "' . (int) $this->context->cookie->id_employee . '", now())';
                echo Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($insertNoteSql);
                die;
            case 'changeReturnStatus':
                $return_id = Tools::getValue('ret');
                $status_id = Tools::getValue('stat');
                /* Start Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $temp_status = array();
                $temp_status['subject'] = Tools::getValue('subject_email_status');
                $temp_status['body'] = Tools::getValue('body_email_status');
                $temp_status['body'] = str_replace("&amp;", "#####@@@@@@", $temp_status['body']);
                $temp_status['body'] = str_replace("#####@@@@@@", "&;", $temp_status['body']);
                $temp_status['body'] = str_replace("@@@@@@@@@@@@", "&", $temp_status['body']);
                /* End Added by Anshul Mittal on 25-08-2017 to add a functionality of email editing before sending it to customer */
                $get_return_data = 'select id_order, id_customer, id_rm_order as return_id from ' .
                    _DB_PREFIX_ . 'velsof_rm_order
                    where id_rm_order = ' . (int) $return_id;
                $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return_data);
                $check_query = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_status where id_rm_order=' .
                    (int) $return_id . ' and id_rm_status=' . (int) $status_id;
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_query);
                if ($result && is_array($result)) {
                    $get_previous_status = 'select id_rm_status from ' . _DB_PREFIX_ . 'velsof_rm_status where
                        id_rm_order = ' . (int) $return_id . ' order by date_add desc';
                    $previous_status_id = $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_previous_status);
                    $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT value from
                        ' . _DB_PREFIX_ . 'velsof_return_data_lang where return_data_id = ' .
                        (int) $previous_status_id['id_rm_status'] . ' and
                        id_lang=' . (int) $this->context->language->id . ' and id_shop=' .
                        (int) $this->context->shop->id);
                    $return_data['previous_status'] = $status['value'];
                    $update_status = 'update ' . _DB_PREFIX_ . 'velsof_rm_status set date_add=now() where
                                            id_rm_order=' . (int) $return_id . ' and id_rm_status=' . (int) $status_id;
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_status);
                    //                    $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT value from
                    //                    ' . _DB_PREFIX_ . 'velsof_return_data_lang where return_data_id = ' . (int) $result['id_rm_status'] .
                    //                        ' and
                    //                    id_lang=' . (int) $this->context->language->id . ' and id_shop=' . (int) $this->context->shop->id);
                    //                    $return_data['previous_status'] = $status['value'];
                } else {
                    $get_previous_status = 'select id_rm_status from ' . _DB_PREFIX_ . 'velsof_rm_status where
                        id_rm_order = ' . (int) $return_id . ' order by date_add desc';
                    $previous_status_id = $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_previous_status);
                    $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT value from
                        ' . _DB_PREFIX_ . 'velsof_return_data_lang where return_data_id = ' .
                        (int) $previous_status_id['id_rm_status'] . ' and
                        id_lang=' . (int) $this->context->language->id . ' and id_shop=' .
                        (int) $this->context->shop->id);
                    $return_data['previous_status'] = $status['value'];
                    $add_status = 'insert into ' . _DB_PREFIX_ .
                        'velsof_rm_status (`id_rm_order`,`id_rm_status`,`date_add`)
                        values (' . (int) $return_id . ',' . (int) $status_id . ',now())';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($add_status);
                }
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT value from
                    ' . _DB_PREFIX_ . 'velsof_return_data_lang where return_data_id = ' . (int) $status_id . ' and
                    id_lang=' . (int) $this->context->language->id . ' and id_shop=' .
                    (int) $this->context->shop->id);
                $return_data['current_status'] = $result['value'];
                /* Edited by Anshul Mittal On 25-08-2017 to add a functionality of email editing before sending it to customer */

                /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT status_message from
                    ' . _DB_PREFIX_ . 'velsof_return_status_text_lang where return_data_id = ' . (int) $status_id . ' and
                    id_lang=' . (int) $this->context->language->id . ' and id_shop=' .
                    (int) $this->context->shop->id);
                $return_data['current_status_text_message'] = $result['status_message'];
                /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */

                $result['mail_sent'] = $this->sendNotificationEmail('ret_stat', $return_data, $temp_status);
                echo json_encode($result);
                die;
            case 'getArchives':
                $from = Tools::getValue('from_date');
                $to = Tools::getValue('to_date');
                $custom_return_id = Tools::getValue('return_id');
                $customer_name = Tools::getValue('customer_name');
                $product_name = Tools::getValue('product_name');
                $order_id = Tools::getValue('order_id');
                $status_id = Tools::getValue('status_id');
                $order_by = Tools::getValue('order_by');
                $order_dir = Tools::getValue('order_dir');
                $this->json = $this->getReturns(4, $from, $to, $custom_return_id, $customer_name, $product_name, $order_id, $status_id, $order_by, $order_dir);
                break;
            case 'writeArchiveExcel':
                $from = Tools::getValue('from_date');
                $to = Tools::getValue('to_date');
                echo $this->writeExcel($from, $to);
                die;
            case 'getOrder':
                $this->json = $this->getOrderAdmin(
                    trim(Tools::getValue('rm_reference_id')),
                    trim(Tools::getValue('rm_customer_email'))
                );
                break;
            case 'getRequestForm':
                $this->json = $this->getRequestForm();
                break;
            case 'submitReturnRequest':
                $this->json = $this->submitReturnRequest();
                //changes by vishal on 14 august 2020 for adding functionality to return from admin
                if (!isset($this->json['error'])) {
                    $this->json['return_data'] = $this->getPendingReturnData($this->json['kb_return_id']);
                }
                //changes end
                break;
            case 'loadEmailTemplate':
                $selected_lang = Tools::getValue('selected_lang');
                $selected_temp = Tools::getValue('selected_temp');

                $this->json = $this->loadEmailTemplate($selected_lang, $selected_temp);
                break;
            case 'saveEmailTemplate':
                $this->json = $this->saveEmailTemplate();
                break;
            case 'getNextReturnsListingPage':
                $this->json = $this->getReturns(Tools::getValue('active_status'));
                break;
            case 'getMessagesData':
                $this->json = $this->getMessagesData((int) Tools::getValue('selected_lang'));
                break;
            case 'getReturnSlipData':
                $lang_iso = Language::getIsoById((int) Tools::getValue('selected_lang'));
                $this->json['address'] = $this->getReturnSlipDataByLanguage('address', $lang_iso);
                $this->json['guidelines'] = $this->getReturnSlipDataByLanguage('guide', $lang_iso);
                break;
            case 'getReturnmanagerCustomFeildDetail':
                $this->json = $this->getReturnmanagerCustomFeildDetail((int) Tools::getValue('rm_order_id'));
                break;
                /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to send Test Email */
            case 'sendTestMail':
                $email = Tools::getValue('email');
                $temp_status = array();
                $temp_status['subject'] = Tools::getValue('subject_test_email');
                $temp_status['body'] = Tools::getValue('body_test_email');
                $temp_status['body'] = str_replace("&amp;", "#####@@@@@@", $temp_status['body']);
                $temp_status['body'] = str_replace("#####@@@@@@", "&;", $temp_status['body']);
                $temp_status['body'] = str_replace("@@@@@@@@@@@@", "&", $temp_status['body']);
                $return_data = array();
                $return_data['test_email'] = $email;
                $this->json['mail_sent'] = $this->sendNotificationEmail('ret_test_email', $return_data, $temp_status);
                break;
                /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to send Test Email */
        }
        header('Content-Type: application/json', true);
        echo json_encode($this->json);
        die;
    }

    public function saveEmailTemplate()
    {
        $template_data = Tools::getValue('rm_email');
        $json = array();
        $template_data['text_content'] = Tools::getValue('text_content');
        if ($template_data['subject'] == '') {
            $json['success'] = false;
            $json['error'] = $this->l('Template subject can not be left blank.');
        } elseif ($template_data['content'] == '') {
            $json['success'] = false;
            $json['error'] = $this->l('Template content can not be left blank.');
        }
        if (isset($json['error'])) {
            return $json;
        }
        $qry = '';
        if ($template_data['template_id'] > 0) {
            $qry = 'UPDATE ' . _DB_PREFIX_ . 'velsof_rm_email set
				text_content = "' . Tools::htmlentitiesUTF8($template_data['text_content'])
                . '", subject = "' . Tools::htmlentitiesUTF8($template_data['subject']) . '",
				body="' . Tools::htmlentitiesUTF8($template_data['content']) . '", date_upd=now() where
				id_template = ' . (int) $template_data['template_id'];
        } else {
            $qry = 'INSERT into ' . _DB_PREFIX_ . 'velsof_rm_email values ("", ' .
                (int) $template_data['template_lang'] . ',
				' . (int) $this->context->shop->id . ', "' .
                pSQL(Language::getIsoById((int) $template_data['template_lang'])) . '",
				"' . pSQL($template_data['template_name']) . '", "' .
                Tools::htmlentitiesUTF8($template_data['text_content']) . '",
				"' . Tools::htmlentitiesUTF8($template_data['subject']) . '", "' .
                Tools::htmlentitiesUTF8($template_data['content']) . '",
				now(), now())';
        }
        if (Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry)) {
            $json['success'] = true;
            $json['msg'] = $this->l('Email template updated successfully.');
        } else {
            $json['success'] = false;
            $json['error'] = $this->l('Unable to update email template.');
        }
        return $json;
    }

    public function writeExcel($from_date, $to_date)
    {
        if ($from_date == null) {
            $to = date('Y-m-d', time());
            $from = date('Y-m-d', strtotime('last month'));
        } else {
            $to = date('Y-m-d', strtotime($to_date));
            $from = date('Y-m-d', strtotime($from_date));
        }
        $get_returns = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_order od where od.active=4 and
            od.id_shop=' . (int) $this->context->shop->id . ' and
            (date(od.date_update) between "' . pSQL($from) . '" and "' . pSQL($to) . '") order by date_update desc';
        $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_returns);
        $directory = _PS_MODULE_DIR_ . 'returnmanager/reports/';
        if (!is_writable($directory)) {
            return '1';
        }
        $currency = (string) $this->context->currency->iso_code;
        $f = fopen($directory . 'Archives_List.csv', 'w+');
        $header = array(
            $this->l('Order'),
            $this->l('Customer Name'),
            $this->l('Customer Email'),
            $this->l('Product'),
            $this->l('Price') . '(' . $currency . ')',
            $this->l('Quantity'),
            $this->l('Shipping Paid By'),
            $this->l('Return Type'),
            $this->l('Return Reason'),
            $this->l('Request Date'),
            $this->l('Customer Notes'),
            $this->l('Current Status')
        );
        fputcsv($f, $header);

        foreach ($return_data as $return) {
            $data_to_write = array();
            $odr_obj = new Order($return['id_order']);
            $data_to_write[] = $odr_obj->reference;
            $cust_obj = new Customer($return['id_customer']);
            $data_to_write[] = $cust_obj->firstname . ' ' . $cust_obj->lastname;
            $data_to_write[] = $cust_obj->email;

            $get_name = 'select product_name,unit_price_tax_incl from ' . _DB_PREFIX_ . 'order_detail
				where id_order_detail=' . (int) $return['id_order_detail'] . ' and id_shop=' .
                (int) $this->context->shop->id;
            $pro_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_name);
            $data_to_write[] = $pro_name['product_name'];
            $data_to_write[] = number_format((float) $pro_name['unit_price_tax_incl'], 2, '.', '');
            $data_to_write[] = $return['quantity'];
            if ($return['whopayshipping'] == 'c') {
                $data_to_write[] = $this->l('Customer');
            } else {
                $data_to_write[] = $this->l('Store Owner');
            }

            $data_to_write[] = $this->l(Tools::ucfirst($return['return_type']));

            $get_stat_name = 'select l.value from ' . _DB_PREFIX_ . 'velsof_return_data_lang l,' .
                _DB_PREFIX_ . 'velsof_return_data
				d where l.id_lang=' . (int) $this->context->language->id . '
                and l.id_shop=' . (int) $this->context->shop->id . ' and d.return_data_id=' .
                (int) $return['id_rm_reason'] . ' and
				l.return_data_id=d.return_data_id';
            $status_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_stat_name);
            $data_to_write[] = $status_name['value'];
            //            $data_to_write[] = date('d-M-Y', strtotime($return['date_add']));
            /**
             * Start Changes to fix the issue of 500 error because of the different number of parameters in the function
             * In PS8 and above, only two params are allowed in the displayDate(). So, adding the PS version check
             * NAFeb2024 displaydate
             * @date 06-02-2024
             * @modifier Nikhil Aggarwal
             */
            if (_PS_VERSION_ >= '8.0.0') {
                $data_to_write[] = Tools::displayDate($return['date_add']);
            } else {
                $data_to_write[] = Tools::displayDate($return['date_add'], $this->context->language->id);
            }
            // Changes end by Nikhil Aggarwal
            $data_to_write[] = $return['comment'];
            $get_status = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_status where id_rm_order=' .
                (int) $return['id_rm_order'] . ' order by date_add desc';
            $return_status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_status);

            $get_stat_name = 'select value from ' . _DB_PREFIX_ . 'velsof_return_data_lang where id_lang=' .
                (int) $this->context->language->id . '
                and id_shop=' . (int) $this->context->shop->id . ' and return_data_id=' .
                (int) $return_status['id_rm_status'];
            $status_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_stat_name);
            $data_to_write[] = $status_name['value'];
            fputcsv($f, $data_to_write);
        }
        unset($cust_obj);
        unset($odr_obj);
        fclose($f);
        return 'returnmanager/reports/Archives_List.csv';
    }

    public function getActiveReturnData($return_id)
    {
        $get_returns = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_order od where od.active=2 and od.id_rm_order=' .
            (int) $return_id . '
            and od.id_shop=' . (int) $this->context->shop->id;
        $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_returns);
        $return_history = array();
        $return_history['return_id'] = $return_id;
        $get_name = 'select product_name,product_attribute_id,product_id,unit_price_tax_incl
            from ' . _DB_PREFIX_ . 'order_detail where id_order_detail=' . (int) $return_data['id_order_detail'] .
            ' and id_shop=' . (int) $this->context->shop->id;
        $pro_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_name);

        if ($pro_name['product_attribute_id'] != 0) {
            $name_attr = explode(' - ', $pro_name['product_name']);
            $return_history['product_name'] = $name_attr[0];
            $return_history['product_attr'] = $name_attr[1];
        } else {
            $return_history['product_name'] = $pro_name['product_name'];
            $return_history['product_attr'] = '';
        }
        $cust_obj = new Customer($return_data['id_customer']);
        $return_history['cust_email'] = $cust_obj->email;
        $return_history['return_type'] = $this->l(Tools::ucfirst($return_data['return_type']));
        $return_history['comment'] = $return_data['comment'];
        $return_history['quantity'] = $return_data['quantity'];
        //        $return_history['request_date'] = date('d-M-Y', strtotime($return_data['date_add']));
        /**
         * Start Changes to fix the issue of 500 error because of the different number of parameters in the function
         * In PS8 and above, only two params are allowed in the displayDate(). So, adding the PS version check
         * NAFeb2024 displaydate
         * @date 06-02-2024
         * @modifier Nikhil Aggarwal
         */
        if (_PS_VERSION_ >= '8.0.0') {
            $return_history['request_date'] = Tools::displayDate($return_data['date_add']);
        } else {
            $return_history['request_date'] = Tools::displayDate($return_data['date_add'], $this->context->language->id);
        }
        // Changes end by Nikhil Aggarwal
        $return_history['order_id'] = $return_data['id_order'];
        $return_history['customer_id'] = $return_data['id_customer'];
        $return_history['unit_price_tax_incl'] = $this->kbFormatPrice(Tools::convertPrice($pro_name['unit_price_tax_incl']));
        $get_status = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_status where id_rm_order=' .
            (int) $return_data['id_rm_order'] . ' order by date_add desc';
        $return_status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_status);

        $get_stat_name = 'select value from ' . _DB_PREFIX_ . 'velsof_return_data_lang where id_lang=' .
            (int) $this->context->language->id . '
            and id_shop=' . (int) $this->context->shop->id . ' and return_data_id=' .
            (int) $return_status['id_rm_status'];
        $status_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_stat_name);
        $return_history['status'] = $status_name['value'];
        $return_history['status_id'] = $return_status['id_rm_status'];
        $return_history['customer_controller'] = $this->context->link->getAdminLink('AdminCustomers');
        return $return_history;
    }

    public function getPendingReturnData($return_id)
    {
        $get_return = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_order od where od.id_rm_order=' . $return_id . ' and
            od.id_shop=' . (int) $this->context->shop->id . ' and od.id_lang=' .
            (int) $this->context->language->id;
        $return = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return);
        $get_reason_name = 'select l.value from ' . _DB_PREFIX_ . 'velsof_return_data_lang l,' .
            _DB_PREFIX_ . 'velsof_return_data d where
            l.id_lang=' . (int) $this->context->language->id . '
            and l.id_shop=' . (int) $this->context->shop->id . ' and d.return_data_id=' .
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
        $image = new Image($product_image[0]['id_image']);
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $ps_base_url = Tools::getShopDomainSsl(true);
        } else {
            $ps_base_url = Tools::getShopDomain(true);
        }
        $return_data['pro_img'] = $ps_base_url . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
        if ($pro_name['product_attribute_id'] != 0) {
            $name_attr = explode(' - ', $pro_name['product_name']);
            $return_data['product_name'] = $name_attr[0];
            $return_data['product_attr'] = $name_attr[1];
        } else {
            $return_data['product_name'] = $pro_name['product_name'];
            $return_data['product_attr'] = '';
        }

        if ($return['whopayshipping'] == 'c') {
            $shipp = 'Customer';
        } else {
            $shipp = 'Store Owner';
        }
        $cust_obj = new Customer($return['id_customer']);
        $odr_obj = new Order($return['id_order']);
        $return_data['cust_name'] = $cust_obj->firstname . ' ' . $cust_obj->lastname;
        $return_data['product_link'] = $this->context->link->getProductLink($pro_name['product_id']);
        /*
         * Start Code Added By Priyanshu on 23-March-2020
         * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
         */
        //changes by vishal on 20 july 2020 for resolving the product replacement issue
        if (!empty($return['product_id'])) {
            $return_data['replacedwith_product_link'] = $this->context->link->getProductLink($return['product_id'], null, null, null, null, null, $return['replaced_product_attribute_id']);
            $kbproduct_obj = new Product();
            $kbreplaced_product_name = $kbproduct_obj->getProductName($return['product_id'], $return['replaced_product_attribute_id'], $return['id_lang']);
            $return_data['replacedwith_product_name'] = $kbreplaced_product_name;
        }
        //changes end
        /*
         * End Code Added By Priyanshu on 23-March-2020
         * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
         */
        $return_data['order_reference'] = $odr_obj->reference;
        $return_data['id_order'] = $return['id_order'];
        $return_data['id_customer'] = $return['id_customer'];
        $return_data['quantity'] = $return['quantity'];
        $return_data['comment'] = $return['comment'];
        $return_data['unit_price_tax_incl'] = $this->kbFormatPrice(Tools::convertPrice($pro_name['unit_price_tax_incl']));
        $return_data['return_type'] = $this->l(Tools::ucfirst($return['return_type']));
        $return_data['whopayshipping'] = $shipp;
        $return_data['order_controller'] = $this->context->link->getAdminLink('AdminOrders');
        $return_data['customer_controller'] = $this->context->link->getAdminLink('AdminCustomers');
        unset($cust_obj);
        unset($odr_obj);
        return $return_data;
    }

    //changes by vishal for adding order cancellation functionality
    public function getCancels($active = 1)
    {
        $page_number = 1;
        if (Tools::getValue('inc_page_number') && Tools::getValue('inc_page_number') > 1) {
            $page_number = (int) Tools::getValue('inc_page_number');
        }
        /* Start Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
        $filter_condition = '';
        $custom_cancel_id = Tools::getValue('cancel_id', false);
        if ($custom_cancel_id) {
            $custom_cancel_id = trim($custom_cancel_id);
            $filter_condition .= ' and od.id_rm_cancel LIKE "%' . pSQL($custom_cancel_id) . '%"';
        }

        $customer_name = Tools::getValue('customer_name', false);
        if ($customer_name) {
            $customer_name = trim($customer_name);
            $filter_condition .= ' and CONCAT (cus.firstname, " ", cus.lastname) LIKE "%' . pSQL($customer_name) . '%"';
        }

        $order_id = Tools::getValue('order_id', false);
        if ($order_id) {
            $order_id = trim($order_id);
            $filter_condition .= ' and ods.reference LIKE "%' . pSQL($order_id) . '%"';
        }

        $order_condition = '';
        if (Tools::getIsset('order_by') && Tools::getIsset('order_dir')) {
            $order_condition = Tools::getValue('order_by') . ' ' . Tools::getValue('order_dir');
        } else {
            $order_condition = 'od.date_update desc';
        }
        /* End Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */

        if ($active == 2) {
            /* Start Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
            //            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_cancel od where od.active=2 and
            //                od.id_shop=' . (int) $this->context->shop->id .
            //                ' order by date_update desc';
            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_cancel od join ' . _DB_PREFIX_ . 'orders ods on (od.id_order = ods.id_order AND od.id_shop = ods.id_shop AND ods.id_lang = ods.id_lang AND od.id_customer = ods.id_customer) join ' . _DB_PREFIX_ . 'customer cus on (od.id_customer = cus.id_customer AND od.id_shop = cus.id_shop) where od.active=2 and
                od.id_shop=' . (int) $this->context->shop->id . $filter_condition .
                ' order by ' . $order_condition;
            /* End Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
        } else {
            /* Start Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
            //            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_cancel od where od.active=1 and
            //                od.id_shop=' . (int) $this->context->shop->id .
            //                ' order by date_update desc';
            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_cancel od join ' . _DB_PREFIX_ . 'orders ods on (od.id_order = ods.id_order AND od.id_shop = ods.id_shop AND ods.id_lang = ods.id_lang AND od.id_customer = ods.id_customer) join ' . _DB_PREFIX_ . 'customer cus on (od.id_customer = cus.id_customer AND od.id_shop = cus.id_shop) where od.active=1 and
                od.id_shop=' . (int) $this->context->shop->id . $filter_condition .
                ' order by ' . $order_condition;
            /* End Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
        }
        $total_records = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            str_replace('{COLUMNS}', 'count(*) as total', $get_returns)
        );

        if ($total_records['total'] <= 0) {
            return array(
                'flag' => false,
                'pagination' => ''
            );
        }

        if ($page_number < 1) {
            $page_number = 1;
        }

        $total_pages = ceil((int) $total_records['total'] / self::ITEM_PER_PAGE);

        $page_position = (($page_number - 1) * self::ITEM_PER_PAGE);

        $get_returns .= ' LIMIT ' . $page_position . ', ' . self::ITEM_PER_PAGE;
        $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(str_replace('{COLUMNS}', '*', $get_returns));
        $return_history = array();
        $flag = 0;
        if ($return_data && count($return_data) > 0) {
            foreach ($return_data as $return) {
                if ($return['id_cancel_reason'] != 0) {
                    $get_stat_name = 'select l.value from ' . _DB_PREFIX_ . 'velsof_return_data_lang l,' .
                        _DB_PREFIX_ . 'velsof_return_data d
					where l.id_shop=' . (int) $this->context->shop->id . ' and d.return_data_id=' .
                        (int) $return['id_cancel_reason'] . ' and
					l.return_data_id=d.return_data_id';
                    $status_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_stat_name);
                    $return_history[$flag]['reason'] = $status_name['value'];
                } else {
                    $return_history[$flag]['reason'] = $return['rm_other_reason'];
                }
                $return_history[$flag]['reason_id'] = $return['id_cancel_reason'];
                $return_history[$flag]['cancel_id'] = $return['id_rm_cancel'];

                $cust_obj = new Customer($return['id_customer']);
                $odr_obj = new Order($return['id_order']);

                $return_history[$flag]['cust_name'] = $cust_obj->firstname . ' ' . $cust_obj->lastname;
                $return_history[$flag]['cust_email'] = $cust_obj->email;

                $return_history[$flag]['cancel_id'] = $return['id_rm_cancel'];

                $return_history[$flag]['comment'] = $return['comment'];
                if (!Validate::isLoadedObject($odr_obj)) {
                    $return_history[$flag]['order_reference'] = 'XXXXXXXXX';
                } else {
                    $return_history[$flag]['order_reference'] = $odr_obj->reference;
                }
                $return_history[$flag]['order_id'] = $return['id_order'];
                $return_history[$flag]['customer_id'] = $return['id_customer'];

                $get_email_lang = 'select id_lang from ' . _DB_PREFIX_ . 'velsof_rm_cancel where id_shop=' . (int) $this->context->shop->id . ' and id_rm_cancel=' .
                    (int) $return['id_rm_cancel'];
                $get_email_lang = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_email_lang);

                $return_history[$flag]['id_lang'] = $get_email_lang['id_lang'];
                $flag++;
            }
            unset($cust_obj);
            unset($odr_obj);
            if (!empty($return_history)) {
                $paging = $this->customPaginator($total_records['total'], (int) $total_pages,
                    'getNextCancelListingPage',
                    $active,
                    $page_number
                );
                return array(
                    'flag' => true,
                    'data' => $return_history,
                    'pagination' => $paging['paging'],
                    'start_serial' => $paging['serial']
                );
            } else {
                return array(
                    'flag' => false,
                    'pagination' => ''
                );
            }
        } else {
            return array(
                'flag' => false,
                'pagination' => ''
            );
        }
    }
    //changes end
    public function getReturns($active = 0, $from_date = null, $to_date = null, $custom_return_id = false, $customer_name = false, $product_name = false, $order_id = false, $status_id = false, $order_by = false, $order_dir = false)
    {
        $page_number = 1;
        if (Tools::getValue('inc_page_number') && Tools::getValue('inc_page_number') > 1) {
            $page_number = (int) Tools::getValue('inc_page_number');
        }
        /* Start Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
        $filter_condition = '';
        if ($custom_return_id) {
            $custom_return_id = trim($custom_return_id);
            $filter_condition .= ' and od.id_rm_order LIKE "%' . pSQL($custom_return_id) . '%"';
        } else {
            $custom_return_id = Tools::getValue('return_id', false);
            if ($custom_return_id) {
                $custom_return_id = trim($custom_return_id);
                $filter_condition .= ' and od.id_rm_order LIKE "%' . pSQL($custom_return_id) . '%"';
            }
        }
        if ($customer_name) {
            $customer_name = trim($customer_name);
            $filter_condition .= ' and CONCAT (cus.firstname, " ", cus.lastname) LIKE "%' . pSQL($customer_name) . '%"';
        } else {
            $customer_name = Tools::getValue('customer_name', false);
            if ($customer_name) {
                $customer_name = trim($customer_name);
                $filter_condition .= ' and CONCAT (cus.firstname, " ", cus.lastname) LIKE "%' . pSQL($customer_name) . '%"';
            }
        }
        if ($product_name) {
            $product_name = trim($product_name);
            $filter_condition .= ' and pl.product_name LIKE "%' . pSQL($product_name) . '%"';
        } else {
            $product_name = Tools::getValue('product_name', false);
            if ($product_name) {
                $product_name = trim($product_name);
                $filter_condition .= ' and pl.product_name LIKE "%' . pSQL($product_name) . '%"';
            }
        }
        $customer_email = Tools::getValue('customer_email', false);
        if ($customer_email) {
            $customer_email = trim($customer_email);
            $filter_condition .= ' and cus.email LIKE "%' . pSQL($customer_email) . '%"';
        }
        if ($order_id) {
            $order_id = trim($order_id);
            $filter_condition .= ' and ods.reference LIKE "%' . pSQL($order_id) . '%"';
        } else {
            $order_id = Tools::getValue('order_id', false);
            if ($order_id) {
                $order_id = trim($order_id);
                $filter_condition .= ' and ods.reference LIKE "%' . pSQL($order_id) . '%"';
            }
        }

        if ($status_id) {
            $status_id = trim($status_id);
            $filter_condition .= ' and od.id_rm_order IN (SELECT id_rm_order from ' . _DB_PREFIX_ . 'velsof_rm_status where id_rm_status = ' . (int)$status_id . ')';
        } else {
            $status_id = Tools::getValue('status_id', false);
            if ($status_id) {
                $status_id = trim($status_id);
                $filter_condition .= ' and od.id_rm_order IN (SELECT id_rm_order from ' . _DB_PREFIX_ . 'velsof_rm_status where id_rm_status = ' . (int) $status_id . ')';
            }
        }
        $order_condition = '';
        if (Tools::getIsset('order_by') && Tools::getIsset('order_dir')) {
            $order_condition = Tools::getValue('order_by') . ' ' . Tools::getValue('order_dir');
        } else {
            $order_condition = 'od.date_update desc';
        }
        /* End Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */

        if ($active == 5) {
            /* Start Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
            //            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_order od where od.active=5 and
            //                od.id_shop=' . (int) $this->context->shop->id .
            //                ' order by date_update desc';


            /**
             * To fetch the admin cancel list as well as user cancel list
             * @date 08-04-2024
             * @author Ravi Kant Gutpa
             */
            //  Added or od.active=3 to fetch the admin cancel list
            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_order od join ' . _DB_PREFIX_ . 'orders ods on (od.id_order = ods.id_order AND od.id_shop = ods.id_shop AND ods.id_lang = ods.id_lang AND od.id_customer = ods.id_customer) join ' . _DB_PREFIX_ . 'customer cus on (od.id_customer = cus.id_customer AND od.id_shop = cus.id_shop) join ' . _DB_PREFIX_ . 'order_detail pl on (od.id_order = pl.id_order AND od.id_shop = pl.id_shop AND od.id_order_detail = pl.id_order_detail) where od.active=5 or od.active=3 and
                od.id_shop=' . (int) $this->context->shop->id . $filter_condition .
                ' order by ' . $order_condition;
            /* End Code Modified by Ravi Kant Gupta on 08-April-2024 to fetch the admin cancel list as well as user cancel list  */

            /* End Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
        } elseif ($active == 2) {
            /* Start Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
            //            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_order od where od.active=2 and
            //                od.id_shop=' . (int) $this->context->shop->id .
            //                ' order by date_update desc';
            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_order od join ' . _DB_PREFIX_ . 'orders ods on (od.id_order = ods.id_order AND od.id_shop = ods.id_shop AND ods.id_lang = ods.id_lang AND od.id_customer = ods.id_customer) join ' . _DB_PREFIX_ . 'customer cus on (od.id_customer = cus.id_customer AND od.id_shop = cus.id_shop) join ' . _DB_PREFIX_ . 'order_detail pl on (od.id_order = pl.id_order AND od.id_shop = pl.id_shop AND od.id_order_detail = pl.id_order_detail) where od.active=2 and
                od.id_shop=' . (int) $this->context->shop->id . $filter_condition .
                ' order by ' . $order_condition;
            /* End Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
        } elseif ($active == 4) {
            if ($from_date == null) {
                $today = date('Y-m-d', time());
                $last_month = date('Y-m-d', strtotime('last month'));
            } else {
                $today = date('Y-m-d', strtotime($to_date));
                $last_month = date('Y-m-d', strtotime($from_date));
            }
            /* Start Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
            //            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_order od where od.active=4 and
            //                od.id_shop=' . (int) $this->context->shop->id . ' and
            //                (date(od.date_update) between "' . pSQL($last_month) . '" and "' . pSQL($today) . '")
            //order by date_update desc';
            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_order od  join ' . _DB_PREFIX_ . 'orders ods on (od.id_order = ods.id_order AND od.id_shop = ods.id_shop AND ods.id_lang = ods.id_lang AND od.id_customer = ods.id_customer) join ' . _DB_PREFIX_ . 'customer cus on (od.id_customer = cus.id_customer AND od.id_shop = cus.id_shop)  join ' . _DB_PREFIX_ . 'order_detail pl on (od.id_order = pl.id_order AND od.id_shop = pl.id_shop AND od.id_order_detail = pl.id_order_detail)  where od.active=4 and
                od.id_shop=' . (int) $this->context->shop->id . ' and
                (date(od.date_update) between "' . pSQL($last_month) . '" and "' . pSQL($today) . '")' . $filter_condition .
                ' order by ' . $order_condition;
            /* End Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
        } else {
            /* Start Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
            //            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_order od where od.active=1 and
            //                od.id_shop=' . (int) $this->context->shop->id .
            //                ' order by date_update desc';
            $get_returns = 'select {COLUMNS} from ' . _DB_PREFIX_ . 'velsof_rm_order od   join ' . _DB_PREFIX_ . 'orders ods on (od.id_order = ods.id_order AND od.id_shop = ods.id_shop AND ods.id_lang = ods.id_lang AND od.id_customer = ods.id_customer)  join ' . _DB_PREFIX_ . 'customer cus on (od.id_customer = cus.id_customer AND od.id_shop = cus.id_shop)  join ' . _DB_PREFIX_ . 'order_detail pl on (od.id_order = pl.id_order AND od.id_shop = pl.id_shop AND od.id_order_detail = pl.id_order_detail)  where od.active=1 and
                od.id_shop=' . (int) $this->context->shop->id . $filter_condition .
                ' order by ' . $order_condition;
            /* End Code Modified by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
        }
        $total_records = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            str_replace('{COLUMNS}', 'count(*) as total', $get_returns)
        );
        if ($total_records['total'] <= 0) {
            return array(
                'flag' => false,
                'pagination' => ''
            );
        }

        if ($page_number < 1) {
            $page_number = 1;
        }

        $total_pages = ceil((int) $total_records['total'] / self::ITEM_PER_PAGE);

        $page_position = (($page_number - 1) * self::ITEM_PER_PAGE);

        $get_returns .= ' LIMIT ' . $page_position . ', ' . self::ITEM_PER_PAGE;
        /**
         * To fetch the cancel type of the return request as active= 3 represents requests discarded by Admin and active=5 represents cancelled by Customer
         * @date 08-04-2024
         * @author Ravi Kant Gutpa
         */
        $return_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(str_replace('{COLUMNS}', '*, od.active as cancel_type', $get_returns));

        $return_history = array();
        $flag = 0;
        if ($return_data && count($return_data) > 0) {
            foreach ($return_data as $return) {
                /* Start Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
                $get_status = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_status where id_rm_order=' .
                    (int) $return['id_rm_order'] . ' order by date_add desc';
                $return_status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_status);
                if ($status_id && ($status_id != $return_status['id_rm_status'])) {
                    continue;
                }
                /* Start Code Added By Priyanshu on 31-March-2021 to resolve the Replacement Product not Showng Correctly in Admin Panel Issue */
                $get_return_replaced_product_id_query = 'select od.product_id from ' . _DB_PREFIX_ . 'velsof_rm_order od where id_rm_order=' . (int) $return['id_rm_order'] . ' and
            od.id_shop=' . (int) $this->context->shop->id;
                $get_return_replaced_product_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_return_replaced_product_id_query);
                $return['product_id'] = $get_return_replaced_product_id['product_id'];
                /* End Code Added By Priyanshu on 31-March-2021 to resolve the Replacement Product not Showng Correctly in Admin Panel Issue */
                /* End Code Added by Priyanshu on 24-March-2021 to implement the Search Functionality in All the listing tabs */
                $get_stat_name = 'select l.value from ' . _DB_PREFIX_ . 'velsof_return_data_lang l,' .
                    _DB_PREFIX_ . 'velsof_return_data d
					where l.id_shop=' . (int) $this->context->shop->id . ' and d.return_data_id=' .
                    (int) $return['id_rm_reason'] . ' and
					l.return_data_id=d.return_data_id';
                $status_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_stat_name);
                $return_history[$flag]['reason'] = $status_name['value'];
                $return_history[$flag]['return_id'] = $return['id_rm_order'];
                $get_name = 'select product_name,product_attribute_id,product_id,unit_price_tax_incl
				from ' . _DB_PREFIX_ . 'order_detail where id_order_detail=' . (int) $return['id_order_detail'] .
                    ' and id_shop=' . (int) $this->context->shop->id;
                $pro_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_name);

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
                /*
                 * Start Code Added By Priyanshu on 23-March-2020 to show the Requested Replacement Product in the Admin panel
                 * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                 */
                if (isset($return['product_id']) && !empty($return['product_id'])) {
                    //changes by vishal on 20 july 2020 for resolving the product replacement issue
                    $return_history[$flag]['replacedwith_product_link'] = $this->context->link->getProductLink($return['product_id'], null, null, null, null, null, $return['replaced_product_attribute_id']);
                    $kbproduct_obj = new Product();
                    $kbreplaced_product_name = $kbproduct_obj->getProductName($return['product_id'], $return['replaced_product_attribute_id'], $return['id_lang']);
                    $return_history[$flag]['replacedwith_product_name'] = $kbreplaced_product_name;
                    //changes end
                }
                //                print_R($return_history);
                //                die('as');
                /*
                 * End Code Added By Priyanshu on 23-March-2020 to show the Requested Replacement Product in the Admin panel
                 * Functionality: To provide the fucntionality of choosing the product in case of replacement to the customers.
                 */
                $cust_obj = new Customer($return['id_customer']);
                $odr_obj = new Order($return['id_order']);

                $return_history[$flag]['cust_name'] = $cust_obj->firstname . ' ' . $cust_obj->lastname;
                $return_history[$flag]['cust_email'] = $cust_obj->email;
                if (isset($pro_name['product_id'])) {
                    $return_history[$flag]['product_link'] = $this->context->link->getProductLink(
                        $pro_name['product_id']
                    );
                } else {
                    $return_history[$flag]['product_link'] = 'javascript:void(0)';
                }
                $return_history[$flag]['return_id'] = $return['id_rm_order'];
                /* changes by rishabh jain for customer chat functionality
                 * 09/07/19
                 */
                $return_history[$flag]['is_ticket_exist'] = (int) RmTicket::getTicketIdByReturnId($return['id_rm_order']);

                if ($return_history[$flag]['is_ticket_exist']) {
                    $return_history[$flag]['ticket_link'] = $this->context->link->getAdminLink(
                        'AdminRmTicketSystem',
                        true
                    );
                    $return_history[$flag]['ticket_link'] .= '&id_rm_ticket=' . $return_history[$flag]['is_ticket_exist'];
                }
                /* changes over */

                /* Start Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing */
                $addr_query = 'Select id_address from ' . _DB_PREFIX_ . 'velsof_rm_return_address where id_return = ' . (int) $return['id_rm_order'];
                $addr_value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($addr_query);

                if ($addr_value > 0) {
                    $address_query = 'Select title from ' . _DB_PREFIX_ . 'velsof_rm_address where id_address =' . (int) $addr_value;
                    $address = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($address_query);
                    $return_history[$flag]['address_title'] = $address['title'];
                } else {
                    $return_history[$flag]['address_title'] = $this->l('Default');
                }
                /* End Code Added by Priyanshu on 18-March-2021 to add the Address title Column in the Return Listing */

                /* changes started on 16th July 2019
                 *
                 */
                if ($return['return_type'] == 'refund') {
                    $return_history[$flag]['is_refund_type'] = 1;
                } else {
                    $return_history[$flag]['is_refund_type'] = 0;
                }
                $return_history[$flag]['return_type'] = $this->l(Tools::ucfirst($return['return_type']));
                $return_history[$flag]['comment'] = $return['comment'];
                $imageurl = '';
                if (!Tools::isEmpty($return['image_path'])) {
                    $imageurl = Tools::getShopDomain(true) . _PS_IMG_ . 'velsof_return/' . $return['image_path'];
                }
                $return_history[$flag]['image_path'] = $imageurl;
                $return_history[$flag]['quantity'] = $return['quantity'];
                $return_history[$flag]['whopayshipping'] = $return['whopayshipping'];
                //                $return_history[$flag]['request_date'] = date('d-M-Y', strtotime($return['date_add']));
                // changes done by Kanishka Kannoujia on 17-06-2022 to display correct in the active return list
                //$return_history[$flag]['request_date'] = Tools::displayDate($return['date_add'], $this->context->language->id);
                /**
                 * Start Changes to fix the issue of 500 error because of the different number of parameters in the function
                 * In PS8 and above, only two params are allowed in the displayDate(). So, adding the PS version check
                 * NAFeb2024 displaydate
                 * @date 06-02-2024
                 * @modifier Nikhil Aggarwal
                 */
                if (_PS_VERSION_ >= '8.0.0') {
                    $return_history[$flag]['request_date'] = Tools::displayDate($return['date_update']);
                } else {
                    $return_history[$flag]['request_date'] = Tools::displayDate($return['date_update'], $this->context->language->id);
                }
                // Changes end by Nikhil Aggarwal
                // changes done by Kanishka Kannoujia on 17-06-2022 to display correct in the active return list
                if (!Validate::isLoadedObject($odr_obj)) {
                    $return_history[$flag]['order_reference'] = 'XXXXXXXXX';
                } else {
                    $return_history[$flag]['order_reference'] = $odr_obj->reference;
                }
                $return_history[$flag]['order_id'] = $return['id_order'];
                $return_history[$flag]['customer_id'] = $return['id_customer'];
                /**
                 * To fetch the active value cancel_type =3 for admin and cancel_type = 5 for Customer
                 * @date 08-04-2024
                 * @author Ravi Kant Gutpa
                 */
                $return_history[$flag]['cancel_type'] = $return['cancel_type'];
                // End of change done by Ravi Kant Gupta on 08-04-2024 to display the active value

                $kb_order_currency_obj = new Currency($odr_obj->id_currency);
                $return_history[$flag]['unit_price_tax_incl'] = $this->kbFormatPrice($pro_name['unit_price_tax_incl'], $kb_order_currency_obj);
                $get_status = 'select * from ' . _DB_PREFIX_ . 'velsof_rm_status where id_rm_order=' .
                    (int) $return['id_rm_order'] . ' order by date_add desc';
                $return_status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_status);
                /* Edited by Anshul Mittal on 26-08-2017 to fix the issue of sent email language according to customer */
                $get_stat_name = 'select value, id_lang from ' . _DB_PREFIX_ . 'velsof_return_data_lang where id_shop=' . (int) $this->context->shop->id . ' and return_data_id=' .
                    (int) $return_status['id_rm_status'];

                $get_email_lang = 'select id_lang from ' . _DB_PREFIX_ . 'velsof_rm_order where id_shop=' . (int) $this->context->shop->id . ' and id_rm_order=' .
                    (int) $return['id_rm_order'];
                $get_email_lang = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_email_lang);

                $status_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_stat_name);
                $return_history[$flag]['status'] = $status_name['value'];
                $return_history[$flag]['status_id'] = $return_status['id_rm_status'];
                /* Added by Anshul Mittal on 26-08-2017 to fix the issue of sent email language according to customer */
                $return_history[$flag]['id_lang'] = $get_email_lang['id_lang'];
                $flag++;
            }
            unset($cust_obj);
            unset($odr_obj);
            if (!empty($return_history)) {
                $paging = $this->customPaginator($total_records['total'], (int) $total_pages,
                    'getNextReturnsListingPage',
                    $active,
                    $page_number
                );
                return array(
                    'flag' => true,
                    'data' => $return_history,
                    'pagination' => $paging['paging'],
                    'start_serial' => $paging['serial']
                );
            } else {
                return array(
                    'flag' => false,
                    'pagination' => ''
                );
            }
        } else {
            return array(
                'flag' => false,
                'pagination' => ''
            );
        }
    }

    public function hookDisplayCustomerAccount()
    {
        $hook_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        if (isset($hook_data['enable']) && $hook_data['enable'] == 1) {
            $r_link = $this->context->link->getModuleLink(
                $this->name,
                'manager',
                array(),
                (bool) Configuration::get('PS_SSL_ENABLED')
            );

            /**
             * Start Changes to use the template instead of using the HTML in PHP file
             * Using a TPL file to display the Returns Manager Icon
             * NAFeb2024 return_manager_icon
             * @date 04-02-2024
             * @modifier Nikhil Aggarwal
             */
            $this->context->smarty->assign(
                array(
                    'return_manager_link_hook' => $r_link,
                    'return_manager_text_hook' => $this->l('Returns Manager')
                )
            );
            return $this->display(__FILE__, 'return_manager_icon.tpl');
            // Changes end by Nikhil
        }
    }

    public function hookDisplayHeader()
    {
        $hook_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        $this->context->controller->addCSS($this->_path . 'views/css/velsof_rm_spinner.css');
        $history_page_content = '';
        if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'history') {
            $this->context->controller->addCSS($this->_path . 'views/css/velsof_rm_front.css');
            $this->context->controller->addJS($this->_path . 'views/js/velsof_rm_front.js');
            /* Start Code Added By Priyanshu on 12-September-2020 to implement the Specific Product Selection Functionality */
            $this->context->controller->addJs($this->_path . 'views/js/jquery.autocomplete.js');
            /* End Code Added By Priyanshu on 12-September-2020 to implement the Specific Product Selection Functionality */
            $this->context->controller->addCSS($this->_path . 'views/css/notifications/jquery.notyfy.css');
            $this->context->controller->addCSS($this->_path . 'views/css/notifications/default.css');
            $this->context->controller->addCSS($this->_path . 'views/css/notifications/jquery.gritter.css');
            $this->context->controller->addJS($this->_path . 'views/js/notifications/jquery.gritter.min.js');
            $this->context->controller->addJS($this->_path . 'views/js/notifications/jquery.notyfy.js');
            $this->context->controller->addJS($this->_path . 'views/js/notifications.js');
            $this->context->smarty->assign(
                array(
                    /**
                     * Start Changes to fix the kb_admin_link not defined error on the order history page
                     * Adding variable to be assigned in TPL
                     * NASep2023 kb_admin_link_order_history
                     * @date 19-09-2023
                     * @modifier Nikhil Aggarwal
                     */
                    'kb_admin_link' => $this->context->link->getModuleLink('returnmanager', 'manager', array('method' => 'ajaxproductaction', 'ajax' => true)),
                    // Changes end by Nikhil
                    'isLogged' => ($this->context->customer->isLogged()) ? true : false,
                    'path' => Tools::getShopDomain(true) . __PS_BASE_URI__ .
                        str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_),
                    'module_link' => $this->context->link->getModuleLink('returnmanager', 'manager')
                )
            );
            $history_page_content = $this->display(__FILE__, 'order_history.tpl');
        }
        if (isset($hook_data['enable']) && $hook_data['enable'] == 1) {
            $return_hook_link = json_decode(Configuration::get('VELSOF_RETURNMANAGER_LINK'), true);
            $return_hook_link['link_html'] = Tools::htmlentitiesDecodeUTF8($return_hook_link['link_html']);
            $return_hook_link['link_html_class'] = Tools::htmlentitiesDecodeUTF8($return_hook_link['link_html_class']);
            if ($this->context->controller->php_self == 'module-returnmanager-manager') {
                $custom_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER_CUSTOM'), true);
                $custom_data['js'] = urldecode($custom_data['js']);
                $this->smarty->assign('velsof_return_custom_data', $custom_data);
            }
            $velsof_return_manager = 1;
            $velsof_return_manager_link = $this->context->link->getModuleLink('returnmanager', 'manager');
            $this->smarty->assign(
                array(
                    'velsof_return_manager' => $velsof_return_manager,
                    'velsof_return_manager_link' => $velsof_return_manager_link,
                    'velsof_return_link_data' => $return_hook_link,
                )
            );
            return $history_page_content . $this->display(__FILE__, 'return_link.tpl');
        }
        return $history_page_content;
    }

    public function hookDisplayNav1()
    {
        if (Configuration::get('VELSOF_RETURNMANAGER') !== false) {
            $rm_config = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
            $rm_displaynav1_links = array();
            // changes done by Kanishka Kannoujia on 17-06-2022 to Need to Provde Enable/Disable option for the "Return" Button on the header
            //if (isset($rm_config['enable']) && ($rm_config['enable'] == 1) {
            if (isset($rm_config['enable']) && ($rm_config['enable'] == 1 && isset($rm_config['enable_header_menu']) && $rm_config['enable_header_menu'] == 1)) {
                $front_rm_link = $this->context->link->getModuleLink(
                    $this->name,
                    'manager',
                    array(),
                    (bool) Configuration::get('PS_SSL_ENABLED')
                );
                $rm_displaynav1_links[] = array(
                    'href' => $front_rm_link,
                    'label' => $this->l('Return'),
                    'title' => $this->l('Click here to apply for returns')
                );
                $this->context->smarty->assign('rm_displaynav1_links', $rm_displaynav1_links);
                if ($this->context->smarty->tpl_vars['page']->value['page_name'] == 'module-returnmanager-manager') {
                    $custom_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER_CUSTOM'), true);
                    $custom_data['js'] = urldecode($custom_data['js']);
                    $this->smarty->assign('velsof_return_custom_data', $custom_data);
                }
                return $this->display(__FILE__, 'return_link.tpl');
            }
        }
    }

    /*
     * Add css and javascript
     */

    protected function addBackOfficeMedia()
    {
        //CSS files
        $this->context->controller->addCSS($this->_path . 'views/css/glyphicons_regular.css');

        $this->context->controller->addCSS($this->_path . 'views/css/returnmanager.css');
        $this->context->controller->addCSS($this->_path . 'views/css/bootstrap/bootstrap.css');
        $this->context->controller->addCSS($this->_path . 'views/css/bootstrap/responsive.css');
        $this->context->controller->addCSS($this->_path . 'views/css/theme/fonts/glyphicons/css/glyphicons_regular.css');
        $this->context->controller->addCSS($this->_path . 'views/css/theme/fonts/font-awesome/css/font-awesome.min.css');
        $this->context->controller->addCSS(
            $this->_path . 'views/css/bootstrap/extend/bootstrap-switch/static/stylesheets/bootstrap-switch.css'
        );
        $this->context->controller->addCSS($this->_path . 'views/css/theme/style-light.css');
        $this->context->controller->addCSS($this->_path . 'views/css/kb_rm.css');
        $this->context->controller->addCSS($this->_path . 'views/css/multiple-select.css');
        $this->context->controller->addCSS($this->_path . 'views/css/popup.css');
        //                $this->context->controller->addCSS($this->_path.'views/css/velsof_rm_front.css');
        $this->context->controller->addJs($this->_path . 'views/js/velovalidation.js');
        $this->context->controller->addJs($this->_path . 'views/js/theme/demo/common.js');
        $this->context->controller->addJs($this->_path . 'views/js/tooltip.js');
        $this->context->controller->addJs(
            $this->_path . 'views/js/bootstrap/extend/bootstrap-switch/static/js/bootstrap-switch.js'
        );
        $this->context->controller->addJs($this->_path . 'views/js/jquery.multiple.select.js');
        $this->context->controller->addJs($this->_path . 'views/js/returnmanager.js');
        $this->context->controller->addJs($this->_path . 'views/js/jquery.autocomplete.js');
        /*
         * Load core TinyMCE before tinySetup wrapper — dynamic tiny_mce.js path fails on PS 8+ admin URLs.
         * 21-07-2026
         */
        $this->context->controller->addJs(_PS_JS_DIR_ . 'tiny_mce/tinymce.min.js');
        $this->context->controller->addJs($this->_path . 'views/js/tinymce.inc.js');
    }

    /*
     * Return default settings of the Social Loginizer page
     */
    /* function added by rishabh jain on 17th JUly 2019
     *  to generate coupon for refund type return request
     */
    public function generatecoupon($return_data, $id_customer = 0)
    {
        if ($id_customer) {
            $coupon_amount = 0.0;
            $customer_info = new Customer($id_customer);
            $coupon_code = $this->generateCouponCode();
            $coupon_expiry_date = date('Y-m-d 23:59:59', strtotime('+ 365 days'));
            $rule_desc = $this->l('Return request #') . $return_data['return_id'] . $this->l('Refund');
            $is_used_partial = 1;
            $min_cart_value = 0;
            $percent_reduction = 0;
            $order_obj = new Order($return_data['id_order']);
            $order_detail_obj = new OrderDetail($return_data['id_order_detail']);
            $coupon_amount = (float) $order_detail_obj->unit_price_tax_incl * $return_data['quantity'];

            $reduction_currency = $order_obj->id_currency;
            //insert coupon details
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule  SET
                id_customer = ' . (int) $id_customer . ',
                date_from = "' . pSQL(date('Y-m-d H:i:s', time())) . '",
                date_to = "' . pSQL($coupon_expiry_date) . '",
                description = "' . pSQL($rule_desc) . '",
                quantity = 1, quantity_per_user = 1, priority = 1, partial_use = ' . (int) $is_used_partial . ',
                code = "' . pSQL($coupon_code) . '", minimum_amount = ' . (float) $min_cart_value
                . ', minimum_amount_tax = 0,
                minimum_amount_currency = ' . (int) $reduction_currency . ', minimum_amount_shipping = 0,
                country_restriction = 0, carrier_restriction = 0, group_restriction = 0, cart_rule_restriction = 0,
                product_restriction = 0, shop_restriction = 1,
                free_shipping = 1,
                reduction_percent = ' . (float) $percent_reduction . ', reduction_amount = '
                . (float) $coupon_amount . ',
                reduction_tax = 1, reduction_currency = ' . (int) $reduction_currency . ',
                reduction_product = 0, gift_product = 0, gift_product_attribute = 0,
                highlight = 0, active = 1,
                date_add = "' . pSQL(date('Y-m-d H:i:s', time()))
                . '", date_upd = "' . pSQL(date('Y-m-d H:i:s', time())) . '"';

            Db::getInstance()->execute($sql);
            $cart_rule_id = Db::getInstance()->Insert_ID();

            Db::getInstance()->execute(
                'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_shop
                set id_cart_rule = ' . (int) $cart_rule_id
                    . ', id_shop = ' . (int) $customer_info->id_shop
            );

            Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_lang
                set id_cart_rule = ' . (int) $cart_rule_id . ', id_lang = ' . (int) $customer_info->id_lang . ',
				name = "' . pSQL($rule_desc) . '"');
            // to map the return id with cart rule
            Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'velsof_return_coupon_data
                set id_cart_rule = ' . (int) $cart_rule_id . ', id_return = ' . (int) $return_data['id_order_return'] . ', id_shop = ' . (int) $customer_info->id_shop);
            // changes over
            return $coupon_code;
        } else {
            return '';
        }
    }

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
        // Check if coupon code alredy exist or not
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'cart_rule where code = "' . pSQL($code) . '"';
        $result = Db::getInstance()->executeS($sql);
        if (count($result) == 0) {
            return $code;
        }
        return $this->generateCouponCode();
    }
    /* changes over */
    protected function getDefaultSettings()
    {
        $settings = array(
            'enable' => 0,
            'enable_gdpr_delete' => 0,
            'enable_image_upload' => 0,
            'enable_return_slip' => 0,
            'credit' => 0,
            'refund' => 1,
            'replacement' => 0,
            /* changes done by rishabh on 10th july 2018 to add multiple address tab */
            'enable_address' => 1,
            'enable_chat' => 0,
            //changes by vishal for adding order cancel functioanlity
            'enable_cancel' => 0,
            //changes end
            'enable_cancel_return' => 0,
            'enable_order_status_selection' => 0,
            'enable_order_status_selection_return_policy' => 0,
            'enable_product_selection_replacement' => 0,
            'enable_order_return' => 0,
            /* changes end by rishabh */
            'status' => array(
                'default' => 0
            )
        );
        return $settings;
    }

    public function select($name)
    {
        switch ($name) {
            case 'reason':
                $qry = 'select rd.return_data_id, rd.whopayshipping, rdl.value, rd.editable from '
                    . _DB_PREFIX_ . 'velsof_return_data as rd
                    INNER JOIN ' . _DB_PREFIX_ .
                    'velsof_return_data_lang as rdl on (rd.return_data_id = rdl.return_data_id)
                    where rd.reason = "1" AND rd.active="1"
                    AND rdl.id_shop=' . (int) $this->context->shop->id . ' and rdl.id_lang=' .
                    (int) $this->context->language->id;
                return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
            case 'policy':
                $qry = 'select data.return_data_id, data.refund_days, data.credit_days,data.refund_min_days,data.credit_min_days,data.replacement_min_days,
                    data.replacement_days, rdl.value, rdl.terms from ' . _DB_PREFIX_ . 'velsof_return_data as data
                    INNER JOIN ' . _DB_PREFIX_ . 'velsof_return_data_lang as rdl on
                    (data.return_data_id = rdl.return_data_id)
                    where data.policy = "1" AND data.active="1"
                    AND rdl.id_shop=' . (int) $this->context->shop->id . ' and rdl.id_lang=' .
                    (int) $this->context->language->id;
                return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
            case 'status':
                $qry = 'select data.return_data_id, rdl.value, rdl.terms, data.editable from '
                    . _DB_PREFIX_ . 'velsof_return_data as data
                    INNER JOIN ' . _DB_PREFIX_ . 'velsof_return_data_lang as rdl on
                    (data.return_data_id = rdl.return_data_id)
                    where data.status = "1" AND data.active="1"
                    AND rdl.id_shop=' . (int) $this->context->shop->id . ' and rdl.id_lang=' .
                    (int) $this->context->language->id;
                return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
            case 'address':
                $qry = 'select id_address,id_country, id_state, title, address1,address2,postcode,city,active from ' . _DB_PREFIX_ . 'velsof_rm_address';
                return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
                //changes by vishal for adding cancel functionality
            case 'cancel':
                $qry = 'select data.return_data_id, rdl.value, rdl.terms, data.editable from '
                    . _DB_PREFIX_ . 'velsof_return_data as data
                    INNER JOIN ' . _DB_PREFIX_ . 'velsof_return_data_lang as rdl on
                    (data.return_data_id = rdl.return_data_id)
                    where data.cancel = "1" AND data.active="1"
                    AND rdl.id_shop=' . (int) $this->context->shop->id . ' and rdl.id_lang=' .
                    (int) $this->context->language->id;
                return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
                //changes end
        }
    }

    public function getMappedProduct()
    {
        $category_product = array();
        $policy_id = Tools::getValue('policy_id');
        $category_list = array();
        $mapped_category = array();

        if (Configuration::get('VELSOF_RETURNMANAGER_CATEGORY')) {
            $cat_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER_CATEGORY'), true);
            if (isset($cat_data[$policy_id])) {
                $category_list = $cat_data[$policy_id];
            }
            unset($cat_data[$policy_id]);
        }


        $query = 'select id_categories from  ' . _DB_PREFIX_ . 'velsof_return_policy_product where return_data_id != ' . (int)$policy_id;
        $mapped_categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if (count($mapped_categories) > 0) {
            foreach ($mapped_categories as $key => $value) {
                $mapped_category[] = $value['id_categories'];
            }
        }
        if (empty($mapped_category)) {
            $mapped_category = array();
        }

        if ($category_list && count($category_list) > 0) {
            $qry = 'select DISTINCT(cat.id_product) as id_product, pro.name from ' . _DB_PREFIX_ .
                'category_product as cat
				INNER JOIN ' . _DB_PREFIX_ . 'category_lang as cl on (cl.id_category = cat.id_category)
				INNER JOIN ' . _DB_PREFIX_ . 'product_lang as pro on (cat.id_product = pro.id_product)
				where cat.id_category IN (\'' . pSQL(implode(',', $category_list)) . '\') AND
				pro.id_lang=' . (int) $this->context->language->id . ' AND pro.id_shop=' .
                (int) $this->context->shop->id
                . ' AND cl.id_lang = ' . (int) $this->context->language->id . ' AND cl.id_shop = ' .
                (int) $this->context->shop->id . ' group by pro.name';
            $category_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
        }
        $pro_id_arr = array();
        $mapped_product_qry = 'select DISTINCT(id_product) from ' . _DB_PREFIX_ . 'velsof_return_policy_product where
			return_data_id=' . (int) Tools::getValue('policy_id');
        $product_id_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($mapped_product_qry);
        if ($product_id_list && count($product_id_list) > 0) {
            foreach ($product_id_list as $product) {
                $pro_id_arr[] = $product['id_product'];
            }
        }
        return array(
            'category' => $category_list,
            'product_ids' => $pro_id_arr,
            'category_product' => $category_product,
            'mapped_category' => $mapped_category
        );
    }

    public function getCategoryProduct()
    {
        $mapped_product = array();
        $category_products = array();
        if (Tools::getValue('category') != '') {
            $qry = 'select DISTINCT(cat.id_product) as id_product, pro.name from ' . _DB_PREFIX_ .
                'category_product as cat
				INNER JOIN ' . _DB_PREFIX_ . 'category_lang as cl on (cl.id_category = cat.id_category)
				INNER JOIN ' . _DB_PREFIX_ . 'product_lang as pro on (cat.id_product = pro.id_product)
				where cat.id_category IN (' . pSQL(Tools::getValue('category')) . ') AND
				pro.id_lang=' . (int) $this->context->language->id . ' AND pro.id_shop=' .
                (int) $this->context->shop->id
                . ' AND cl.id_lang = ' . (int) $this->context->language->id . ' AND cl.id_shop = ' .
                (int) $this->context->shop->id;

            $category_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);

            $qry = 'Select DISTINCT(id_product) as id_product from ' . _DB_PREFIX_ . 'velsof_return_policy_product
				WHERE id_categories IN (\'' . pSQL(Tools::getValue('category')) . '\') AND return_data_id = ' .
                (int) Tools::getValue('policy_id');
            $mapped_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
        }
        return array(
            'category_product' => $category_products,
            'mapped_products' => $mapped_product
        );
    }

    public function productPolicyMapping()
    {
        $category_array = array();
        $category_array = explode(',', Tools::getValue('category'));
        $policy_id = Tools::getValue('policy_id');

        $already_mapped = array();
        $already_mapped_category_id = array();

        if (!empty($category_array)) {
            foreach ($category_array as $id_category) {
                $qry = 'select pl.* from ' . _DB_PREFIX_ . 'velsof_return_policy_product as rpp
                                        INNER JOIN ' . _DB_PREFIX_ .
                    'category_lang as pl on(rpp.id_categories = pl.id_category AND pl.id_lang = ' .
                    (int) $this->context->language->id . ')
                                        where rpp.id_categories = ' . (int) $id_category . ' AND rpp.return_data_id != ' .
                    (int) Tools::getValue('policy_id');
                $is_mapped_with_other = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
                if ($is_mapped_with_other && count($is_mapped_with_other) > 0) {
                    $already_mapped[] = $is_mapped_with_other[0]['name'];
                    $already_mapped_category_id[] = $is_mapped_with_other[0]['id_category'];
                }
            }

            $delete_old_setting = 'delete from ' . _DB_PREFIX_ .
                'velsof_return_policy_product where return_data_id=' . (int) Tools::getValue('policy_id');
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($delete_old_setting);
            foreach ($category_array as $id_category) {
                if (!in_array($id_category, $already_mapped_category_id) && $id_category != 0) {
                    $id_product = 0;
                    $mapping = 'insert into ' . _DB_PREFIX_ . 'velsof_return_policy_product
                                                    values(' . (int) Tools::getValue('policy_id') . ',' . (int) $id_product . ',' .
                        (int) $id_category . ')';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($mapping);
                }
            }
        }

        $qry_mapped_category = 'select id_categories from ' . _DB_PREFIX_ . 'velsof_return_policy_product '
            . 'where return_data_id ="' . (int) $policy_id . '"';

        $get_mapped_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry_mapped_category);
        $persist_category = array();
        if ($get_mapped_category != null) {
            foreach ($get_mapped_category as $category) {
                $persist_category[] = $category['id_categories'];
            }
        }
        if (Configuration::get('VELSOF_RETURNMANAGER_CATEGORY')) {
            $cat_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER_CATEGORY'), true);
            $cat_data[$policy_id] = $persist_category;
            Configuration::updateValue('VELSOF_RETURNMANAGER_CATEGORY', json_encode($cat_data));
        } else {
            $data = array();
            $data[$policy_id] = $persist_category;
            Configuration::updateValue('VELSOF_RETURNMANAGER_CATEGORY', json_encode($data));
        }
        return $already_mapped;
    }

    public function addData()
    {
        $json = array();
        switch (Tools::getValue('type')) {
            case 'policy':
                if (Tools::isSubmit('credit_check')) {
                    $credit_days = Tools::getValue('credit_max');
                    $credit_min_days = Tools::getValue('credit_min');
                } else {
                    $credit_days = 0;
                    $credit_min_days = 0;
                }

                if (Tools::isSubmit('refund_check')) {
                    $refund_min_days = Tools::getValue('refund_min');
                    $refund_days = Tools::getValue('refund_max');
                } else {
                    $refund_days = 0;
                    $refund_min_days = 0;
                }

                if (Tools::isSubmit('replacement_check')) {
                    $replacement_min_days = Tools::getValue('replacement_min');
                    $replacement_days = Tools::getValue('replacement_max');
                } else {
                    $replacement_days = 0;
                    $replacement_min_days = 0;
                }

                if (Tools::isSubmit('policy_action_type') && Tools::getValue('policy_action_type') == 0) {
                    //changes by vishal for adding cancel functionality
                    $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data
                        values("","0","0","1","",' . (int) $refund_days . ',' . (int) $credit_days . ',' .
                        (int) $replacement_days . ',"1","1",now(),now(),' . (int) $credit_min_days . ',' . (int) $refund_min_days . ',' . (int) $replacement_min_days . ',"0")';
                    //changes end
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    $id = Db::getInstance()->Insert_ID();
                    /**
                     * Start Changes to fix the issue of Language data not being saved for disabled languages
                     * Replacing the param true in getLanguages(true) with false
                     * NAMar2024 language_issue
                     * @date 08-03-2024
                     * @modifier Nikhil Aggarwal
                     */
                    foreach (Language::getLanguages(false) as $lang) {
                        // Changes end by Nikhil Aggarwal
                        $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang values(' . (int) $id . ',' .
                            (int) $this->context->shop->id . ',' . (int) $lang['id_lang'] . ',"'
                            . pSQL(Tools::getValue('policy_new_' . $lang['id_lang'])) . '","' .
                            pSQL(Tools::getValue('policy_new_term_' . $lang['id_lang'])) . '",
                            "' . pSQL(Tools::getValue('rm_credit_text_' . $lang['id_lang'])) . '", "' .
                            pSQL(Tools::getValue('rm_refund_text_' . $lang['id_lang'])) . '",
                            "' . pSQL(Tools::getValue('rm_replacement_text_' . $lang['id_lang'])) . '")';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    }
                } else {
                    $qry = 'update ' . _DB_PREFIX_ . 'velsof_return_data set date_updated = now(),
                        refund_days=' . (int) $refund_days . ', credit_days=' . (int) $credit_days . ',
                        replacement_days=' . (int) $replacement_days . ', credit_min_days=' . (int) $credit_min_days . ', refund_min_days=' . (int) $refund_min_days . ', replacement_min_days=' . (int) $replacement_min_days . ' where
                        return_data_id=' . (int) Tools::getValue('policy_action_type');
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    /**
                     * Start Changes to fix the issue of Language data not being saved for disabled languages
                     * Replacing the param true in getLanguages(true) with false
                     * NAMar2024 language_issue
                     * @date 08-03-2024
                     * @modifier Nikhil Aggarwal
                     */
                    foreach (Language::getLanguages(false) as $lang) {
                        // Changes end by Nikhil Aggarwal
                        $check_qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_data_lang
                            where return_data_id=' . (int) Tools::getValue('policy_action_type') . ' and
                            id_lang=' . (int) $lang['id_lang'] . ' and id_shop=' . (int) $this->context->shop->id;

                        if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_qry)) {
                            $qry = 'update ' . _DB_PREFIX_ . 'velsof_return_data_lang set
                                value="' . pSQL(Tools::getValue('policy_new_' . $lang['id_lang'])) . '",
                                terms="' . pSQL(Tools::getValue('policy_new_term_' . $lang['id_lang'])) . '",
                                credit_message = "' . pSQL(Tools::getValue('rm_credit_text_' . $lang['id_lang'])) . '",
                                refund_message = "' . pSQL(Tools::getValue('rm_refund_text_' . $lang['id_lang'])) . '",
                                replacement_message = "' . pSQL(Tools::getValue('rm_replacement_text_' .
                                $lang['id_lang'])) . '"
                                where return_data_id=' . (int) Tools::getValue('policy_action_type') . ' and
                                id_lang=' . (int) $lang['id_lang'] . ' and id_shop=' . (int) $this->context->shop->id;
                        } else {
                            $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang values(' .
                                (int) Tools::getValue('policy_action_type') . ','
                                . (int) $this->context->shop->id . ',' . (int) $lang['id_lang'] . ',"'
                                . pSQL(Tools::getValue('policy_new_' . $lang['id_lang'])) . '","' .
                                pSQL(Tools::getValue('policy_new_term_' . $lang['id_lang'])) . '",
                                    "' . pSQL(Tools::getValue('rm_credit_text_' . $lang['id_lang'])) . '", "' .
                                pSQL(Tools::getValue('rm_refund_text_' . $lang['id_lang'])) . '",
                                    "' . pSQL(Tools::getValue('rm_replacement_text_' . $lang['id_lang'])) . '")';
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                        }
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    }
                }

                $policy_detail = $this->select(Tools::getValue('type'));
                $this->smarty->assign('policy', $policy_detail);
                /**
                 * Start changes to fix the issue of using modifier directly in tpl
                 * NAAug2023 modifier
                 * @date 09-08-2023
                 * @author Nikhil Aggarwal
                 */
                $this->context->smarty->registerPlugin("modifier", "impl", "implode");
                // Changes end by Nikhil
                $json['html'] = $this->display(__FILE__, 'views/templates/admin/refresh_policy.tpl');
                $json['policy_data'] = $policy_detail;
                $velsof_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
                if (isset($velsof_data['policy']['default'])) {
                    $json['default_policy'] = $velsof_data['policy']['default'];
                } else {
                    $json['default_policy'] = 0;
                }
                echo json_encode($json);
                break;
            case 'reason':
                if (Tools::isSubmit('reason_action_type') && Tools::getValue('reason_action_type') == 0) {
                    //changes by vishal for adding cancel functionality
                    $inserting_reason = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data
                        values("","1","0","0","' . pSQL(Tools::getValue('charges')) . '","","","","1","1",now(),now(),"","","","")';
                    //changes end
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($inserting_reason);
                    $reason_id = Db::getInstance()->Insert_ID();
                    /**
                     * Start Changes to fix the issue of Language data not being saved for disabled languages
                     * Replacing the param true in getLanguages(true) with false
                     * NAMar2024 language_issue
                     * @date 08-03-2024
                     * @modifier Nikhil Aggarwal
                     */
                    foreach (Language::getLanguages(false) as $lang) {
                        $inserting_reason_lang = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang
                                values(' . (int) $reason_id . ',' . (int) $this->context->shop->id . ','
                            . (int) $lang['id_lang'] . ',"' . pSQL(Tools::getValue('reason_new_' .
                                $lang['id_lang'])) . '","","","","")';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($inserting_reason_lang);
                    }
                } else {
                    $qry = 'update ' . _DB_PREFIX_ . 'velsof_return_data set whopayshipping="' .
                        pSQL(Tools::getValue('charges')) . '", date_updated = now()
                        where return_data_id=' . (int) Tools::getValue('reason_action_type');
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    foreach (Language::getLanguages(false) as $lang) {
                        // Changes end by Nikhil Aggarwal
                        $check_qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_data_lang
                            where return_data_id=' . (int) Tools::getValue('reason_action_type') . ' and
                            id_lang=' . (int) $lang['id_lang'] . ' and id_shop=' . (int) $this->context->shop->id;

                        if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_qry)) {
                            $qry = 'update ' . _DB_PREFIX_ . 'velsof_return_data_lang set
                                value="' . pSQL(Tools::getValue('reason_new_' . $lang['id_lang'])) . '" where
                                return_data_id=' . (int) Tools::getValue('reason_action_type') . ' and
                                id_lang=' . (int) $lang['id_lang'] . ' and id_shop=' . (int) $this->context->shop->id;
                        } else {
                            $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang
                                values(' . (int) Tools::getValue('reason_action_type') . ',' .
                                (int) $this->context->shop->id . ','
                                . (int) $lang['id_lang'] . ',"' .
                                pSQL(Tools::getValue('reason_new_' . $lang['id_lang'])) . '","","","","")';
                        }
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    }
                }

                $reason_detail = $this->select(Tools::getValue('type'));
                $this->smarty->assign('reasons', $reason_detail);
                echo $this->display(__FILE__, 'views/templates/admin/refresh_reason.tpl');
                break;
            case 'status':
                if (Tools::isSubmit('status_action_type') && Tools::getValue('status_action_type') == 0) {
                    //changes by vishal for adding cancel functionality
                    $qry = 'insert into ' . _DB_PREFIX_ .
                        'velsof_return_data values("","0","1","0","","","","","1","1",now(),now(),"","","","")';
                    //changes end
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    $id = Db::getInstance()->Insert_ID();
                    /**
                     * Start Changes to fix the issue of Language data not being saved for disabled languages
                     * Replacing the param true in getLanguages(true) with false
                     * NAMar2024 language_issue
                     * @date 08-03-2024
                     * @modifier Nikhil Aggarwal
                     */
                    foreach (Language::getLanguages(false) as $lang) {
                        // Changes end by Nikhil Aggarwal
                        $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang
                            values(' . (int) $id . ',' . (int) $this->context->shop->id . ',' . (int) $lang['id_lang'] .
                            ',
                            "' . pSQL(Tools::getValue('status_new_' . $lang['id_lang'])) . '","","","","")';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);

                        /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                        $qry_status_text = 'insert into ' . _DB_PREFIX_ . 'velsof_return_status_text_lang
                            values(' . (int) $id . ',' . (int) $this->context->shop->id . ',' . (int) $lang['id_lang'] .
                            ',
                            "' . pSQL(Tools::getValue('status_text_new_' . $lang['id_lang']), true) . '")';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry_status_text);
                        /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                    }
                } else {
                    $qry = 'update ' . _DB_PREFIX_ . 'velsof_return_data set date_updated = now()  where
                        return_data_id=' . (int) Tools::getValue('status_action_type');
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    /**
                     * Start Changes to fix the issue of Language data not being saved for disabled languages
                     * Replacing the param true in getLanguages(true) with false
                     * NAMar2024 language_issue
                     * @date 08-03-2024
                     * @modifier Nikhil Aggarwal
                     */
                    foreach (Language::getLanguages(false) as $lang) {
                        // Changes end by Nikhil Aggarwal
                        $check_qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_data_lang
                            where return_data_id=' . (int) Tools::getValue('status_action_type') . ' and
                            id_lang=' . (int) $lang['id_lang'] . ' and id_shop=' . (int) $this->context->shop->id;

                        if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_qry)) {
                            $qry = 'update ' . _DB_PREFIX_ . 'velsof_return_data_lang set
                                value="' . pSQL(Tools::getValue('status_new_' . $lang['id_lang'])) . '" where
                                return_data_id=' . (int) Tools::getValue('status_action_type') . ' and
                                id_lang=' . (int) $lang['id_lang'] . ' and id_shop=' . (int) $this->context->shop->id;
                        } else {
                            $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang
                            values(' . (int) Tools::getValue('status_action_type') . ',' .
                                (int) $this->context->shop->id . ',' . (int) $lang['id_lang'] . ',
                            "' . pSQL(Tools::getValue('status_new_' . $lang['id_lang'])) . '","","","","")';
                        }
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);

                        /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                        $check_status_message_qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_status_text_lang
                            where return_data_id=' . (int) Tools::getValue('status_action_type') . ' and
                            id_lang=' . (int) $lang['id_lang'] . ' and id_shop=' . (int) $this->context->shop->id;

                        if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_status_message_qry)) {
                            $qry_status_text = 'update ' . _DB_PREFIX_ . 'velsof_return_status_text_lang set
                                status_message="' . pSQL(Tools::getValue('status_text_new_' . $lang['id_lang']), true) . '" where
                                return_data_id=' . (int) Tools::getValue('status_action_type') . ' and
                                id_lang=' . (int) $lang['id_lang'] . ' and id_shop=' . (int) $this->context->shop->id;
                        } else {
                            $qry_status_text = 'insert into ' . _DB_PREFIX_ . 'velsof_return_status_text_lang
                            values(' . (int) Tools::getValue('status_action_type') . ',' .
                                (int) $this->context->shop->id . ',' . (int) $lang['id_lang'] . ',
                            "' . pSQL(Tools::getValue('status_text_new_' . $lang['id_lang']), true) . '")';
                        }
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry_status_text);
                        /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                    }
                }
                $status_detail = $this->select(Tools::getValue('type'));
                $this->smarty->assign('status', $status_detail);
                $this->smarty->assign('refresh_status', json_encode($status_detail));
                echo $this->display(__FILE__, 'views/templates/admin/refresh_status.tpl');

                break;
                //changes by vishal for adding cancel functionality
            case 'cancel':
                if (Tools::isSubmit('cancel_action_type') && Tools::getValue('cancel_action_type') == 0) {
                    //changes by vishal for adding cancel functionality
                    $qry = 'insert into ' . _DB_PREFIX_ .
                        'velsof_return_data values("","0","0","0","","","","","1","1",now(),now(),"","","","1")';
                    //changes end
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    $id = Db::getInstance()->Insert_ID();
                    /**
                     * Start Changes to fix the issue of Language data not being saved for disabled languages
                     * Replacing the param true in getLanguages(true) with false
                     * NAMar2024 language_issue
                     * @date 08-03-2024
                     * @modifier Nikhil Aggarwal
                     */
                    foreach (Language::getLanguages(false) as $lang) {
                        // Changes end by Nikhil Aggarwal
                        $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang
                            values(' . (int) $id . ',' . (int) $this->context->shop->id . ',' . (int) $lang['id_lang'] .
                            ',
                            "' . pSQL(Tools::getValue('cancel_new_' . $lang['id_lang'])) . '","","","","")';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    }
                } else {
                    $qry = 'update ' . _DB_PREFIX_ . 'velsof_return_data set date_updated = now()  where
                        return_data_id=' . (int) Tools::getValue('status_action_type');
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    /**
                     * Start Changes to fix the issue of Language data not being saved for disabled languages
                     * Replacing the param true in getLanguages(true) with false
                     * NAMar2024 language_issue
                     * @date 08-03-2024
                     * @modifier Nikhil Aggarwal
                     */
                    foreach (Language::getLanguages(false) as $lang) {
                        // Changes end by Nikhil Aggarwal
                        $check_qry = 'select * from ' . _DB_PREFIX_ . 'velsof_return_data_lang
                            where return_data_id=' . (int) Tools::getValue('cancel_action_type') . ' and
                            id_lang=' . (int) $lang['id_lang'] . ' and id_shop=' . (int) $this->context->shop->id;

                        if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($check_qry)) {
                            $qry = 'update ' . _DB_PREFIX_ . 'velsof_return_data_lang set
                                value="' . pSQL(Tools::getValue('cancel_new_' . $lang['id_lang'])) . '" where
                                return_data_id=' . (int) Tools::getValue('cancel_action_type') . ' and
                                id_lang=' . (int) $lang['id_lang'] . ' and id_shop=' . (int) $this->context->shop->id;
                        } else {
                            $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_return_data_lang
                            values(' . (int) Tools::getValue('cancel_action_type') . ',' .
                                (int) $this->context->shop->id . ',' . (int) $lang['id_lang'] . ',
                            "' . pSQL(Tools::getValue('cancel_new_' . $lang['id_lang'])) . '","","","","")';
                        }
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                    }
                }
                $status_detail = $this->select(Tools::getValue('type'));
                $this->smarty->assign('cancel_detail', $status_detail);
                echo $this->display(__FILE__, 'views/templates/admin/refresh_cancel.tpl');
                break;
                //changes end
            case 'address':
                if (Tools::isSubmit('address_action_type') && Tools::getValue('address_action_type') == 0) {
                    $qry = 'insert into ' . _DB_PREFIX_ . 'velsof_rm_address(id_country,id_state,title,address1,address2,postcode,city,active) values (' . (int) Tools::getValue('address_new_country') . ',' . (int) Tools::getValue('address_new_state') . ',"' . pSQL(Tools::getValue('address_new_title')) . '","' . pSQL(Tools::getValue('address_new_line1')) . '","' . pSQL(Tools::getValue('address_new_line2')) . '","' . pSQL(Tools::getValue('address_new_zipcode')) . '","' . pSQL(Tools::getValue('address_new_city')) . '",1)';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                } else {
                    $qry = 'update ' . _DB_PREFIX_ . 'velsof_rm_address set id_country = ' . (int) Tools::getValue('address_new_country') . ', id_state=' . (int) Tools::getValue('address_new_state') . ',title="' . pSQL(Tools::getValue('address_new_title')) . '",address1 ="' . pSQL(Tools::getValue('address_new_line1')) . '",address2 = "' . pSQL(Tools::getValue('address_new_line2')) . '",postcode = "' . pSQL(Tools::getValue('address_new_zipcode')) . '", city = "' . (Tools::getValue('address_new_city')) . '" where id_address=' . (int) Tools::getValue('address_new_id');
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($qry);
                }
                $address_detail = $this->select(Tools::getValue('type'));

                $this->smarty->assign('address', $address_detail);
                echo $this->display(__FILE__, 'views/templates/admin/refresh_address.tpl');
                break;
        }
    }

    public function getData()
    {

        switch (Tools::getValue('type')) {
            case 'policy':
                $arr = array();
                $qry = 'select rdl.value, rdl.terms, rdl.credit_message, rdl.refund_message,
                    rdl.replacement_message, rdl.id_lang,
                    rd.refund_days, rd.credit_days, rd.replacement_days, rd.credit_min_days, rd.refund_min_days, rd.replacement_min_days from ' . _DB_PREFIX_
                    . 'velsof_return_data_lang as rdl
                    INNER JOIN ' . _DB_PREFIX_ . 'velsof_return_data as rd on (rd.return_data_id = rdl.return_data_id)
                    WHERE rd.return_data_id = ' . (int) Tools::getValue('policy_id');

                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);

                if ($result && count($result) > 0) {
                    foreach ($result as $rs) {
                        $arr['policy_title'][] = array(
                            'id_lang' => $rs['id_lang'],
                            'text' => $rs['value']
                        );
                        $arr['policy_terms'][] = array(
                            'id_lang' => $rs['id_lang'],
                            'text' => $rs['terms']
                        );
                        $arr['credit_texts'][] = array(
                            'id_lang' => $rs['id_lang'],
                            'text' => $rs['credit_message']
                        );
                        $arr['refund_texts'][] = array(
                            'id_lang' => $rs['id_lang'],
                            'text' => $rs['refund_message']
                        );
                        $arr['replacement_texts'][] = array(
                            'id_lang' => $rs['id_lang'],
                            'text' => $rs['replacement_message']
                        );
                    }
                    $arr['credit_days'] = $rs['credit_days'];
                    $arr['refund_days'] = $rs['refund_days'];
                    $arr['replacement_days'] = $rs['replacement_days'];
                    $arr['refund_min_days'] = $rs['refund_min_days'];
                    $arr['credit_min_days'] = $rs['credit_min_days'];
                    $arr['replacement_min_days'] = $rs['replacement_min_days'];
                }
                return $arr;
            case 'reason':
                $arr = array();
                $qry = 'select rdl.value, rdl.id_lang, rd.whopayshipping from ' . _DB_PREFIX_ .
                    'velsof_return_data_lang as rdl
                    INNER JOIN ' . _DB_PREFIX_ .
                    'velsof_return_data as rd on (rd.return_data_id = rdl.return_data_id)
                    WHERE rd.return_data_id = ' . (int) Tools::getValue('return_id');

                $select_all_reason = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
                if ($select_all_reason && count($select_all_reason) > 0) {
                    foreach ($select_all_reason as $reason) {
                        $arr['reason_text'][] = array(
                            'id_lang' => $reason['id_lang'],
                            'text' => $reason['value']
                        );
                        $arr['charges'] = $reason['whopayshipping'];
                    }
                }
                return $arr;
            case 'status':
                $arr = array();
                $select_status = 'select value,id_lang from ' . _DB_PREFIX_ .
                    'velsof_return_data_lang where return_data_id=' . (int) Tools::getValue('status_id');
                $select_all_status = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($select_status);

                /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                $select_status_text = 'select status_message,id_lang from ' . _DB_PREFIX_ .
                    'velsof_return_status_text_lang where return_data_id=' . (int) Tools::getValue('status_id');
                $select_all_status_text = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($select_status_text);
                /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */

                if ($select_all_status && count($select_all_status) > 0) {
                    foreach ($select_all_status as $stat) {
                        $arr['status_text'][] = array(
                            'id_lang' => $stat['id_lang'],
                            'text' => $stat['value']
                        );
                    }
                }

                /* Start Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */
                if ($select_all_status_text && count($select_all_status_text) > 0) {
                    foreach ($select_all_status_text as $stat_text) {
                        $arr['status_message_text'][] = array(
                            'id_lang' => $stat_text['id_lang'],
                            'text' => $stat_text['status_message']
                        );
                    }
                }
                /* End Code Added By Priyanshu on 8-March-2021 to implement the functionality to add Custom Message for each Return Status */

                return $arr;
                //changes by vishal for adding cancel functionality
            case 'cancel':
                $arr = array();
                $select_cancel = 'select value,id_lang from ' . _DB_PREFIX_ .
                    'velsof_return_data_lang where return_data_id=' . (int) Tools::getValue('cancel_id');
                $select_all_cancel = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($select_cancel);

                if ($select_all_cancel && count($select_all_cancel) > 0) {
                    foreach ($select_all_cancel as $stat) {
                        $arr['cancel_text'][] = array(
                            'id_lang' => $stat['id_lang'],
                            'text' => $stat['value']
                        );
                    }
                }
                return $arr;
                //changes end
            case 'address':
                $arr = array();
                $select_address = 'select * from ' . _DB_PREFIX_ .
                    'velsof_rm_address where id_address=' . (int) Tools::getValue('address_id');
                $select_all_address = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($select_address);
                if ($select_all_address && count($select_all_address) > 0) {
                    foreach ($select_all_address as $stat) {
                        $arr['address_text'][] = array(
                            'state' => $stat['id_state'],
                            'title' => $stat['title'],
                            'id_address' => $stat['id_address'],
                            'id' => $stat['id_address'],
                            'line1' => $stat['address1'],
                            'line2' => $stat['address2'],
                            'city' => $stat['city'],
                            'country' => $stat['id_country'],
                            'zipcode' => $stat['postcode']
                        );
                    }
                }
                return $arr;
        }
    }

    public function delete()
    {
        $delete = 'update ' . _DB_PREFIX_ .
            "velsof_return_data set active='0' where return_data_id=" . (int) Tools::getValue('id');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($delete);
        $remove_mapping = 'delete from ' . _DB_PREFIX_ .
            'velsof_return_policy_product where return_data_id = ' . (int) Tools::getValue('id');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($remove_mapping);
        $json = array();
        switch (Tools::getValue('type')) {
            case 'policy':
                $policy_detail = $this->select(Tools::getValue('type'));
                $this->smarty->assign('policy', $policy_detail);
                /**
                 * Start changes to fix the issue of using modifier directly in tpl
                 * NAAug2023 modifier
                 * @date 09-08-2023
                 * @author Nikhil Aggarwal
                 */
                $this->context->smarty->registerPlugin("modifier", "impl", "implode");
                // Changes end by Nikhil
                $json['html'] = $this->display(__FILE__, 'views/templates/admin/refresh_policy.tpl');
                $json['policy_data'] = $policy_detail;

                $cat_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER_CATEGORY'), true);
                unset($cat_data[(int) Tools::getValue('id')]);
                Configuration::updateValue('VELSOF_RETURNMANAGER_CATEGORY', json_encode($cat_data));

                $velsof_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
                if (isset($velsof_data['policy']['default'])) {
                    $json['default_policy'] = $velsof_data['policy']['default'];
                } else {
                    $json['default_policy'] = 0;
                }
                echo json_encode($json);
                break;
            case 'reason':
                $reason_detail = $this->select(Tools::getValue('type'));
                $this->smarty->assign('reasons', $reason_detail);
                $json['html'] = $this->display(__FILE__, 'views/templates/admin/refresh_reason.tpl');
                echo json_encode($json);
                break;
            case 'status':
                $status_detail = $this->select(Tools::getValue('type'));
                $this->smarty->assign('status', $status_detail);
                $this->smarty->assign('refresh_status', json_encode($status_detail));
                $json['html'] = $this->display(__FILE__, 'views/templates/admin/refresh_status.tpl');
                echo json_encode($json);
                break;
                //changes by vishal for adding cancel functionality
            case 'cancel':
                $status_detail = $this->select(Tools::getValue('type'));
                $this->smarty->assign('cancel_detail', $status_detail);
                $json['html'] = $this->display(__FILE__, 'views/templates/admin/refresh_cancel.tpl');
                echo json_encode($json);
                break;
                //changes end
        }
    }
    public function changeAddressStatus()
    {
        $json = array();
        $status = 0;
        $status = 1 - (int) Tools::getValue('status');
        $delete = 'update ' . _DB_PREFIX_ .
            'velsof_rm_address SET active=' . (int) $status . ' where id_address=' . (int) Tools::getValue('id');
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($delete);
        $address_detail = $this->select('address');
        $this->smarty->assign('address', $address_detail);
        $json['html'] = $this->display(__FILE__, 'views/templates/admin/refresh_address.tpl');
        echo json_encode($json);
    }
    public function getStateList()
    {
        $option = '';
        $state_query = 'Select id_state,name from ' . _DB_PREFIX_ . 'state where id_country = ' . (int) Tools::getValue('country_id');
        $state_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($state_query);
        if (count($state_list) > 0) {
            foreach ($state_list as $state) {
                $option .= '<option value = "' . $state['id_state'] . '">' . htmlentities($state['name'], ENT_COMPAT, 'UTF-8') . '</option>';
            }
        }
        echo $option;
    }

    public function getPolicy()
    {
        $get_policy_qry = 'select return_data_id from ' . _DB_PREFIX_ .
            'velsof_return_data where policy="1" and active="1"';
        $policy_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_policy_qry);
        if ($policy_data) {
            return true;
        } else {
            return false;
        }
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

    /*
     * Expose menu translation strings so the method is referenced for the translator.
     * 21-07-2026
     */
    public function kbRegisterMenuTranslations()
    {
        $this->menuTranslationsIncludeFunction();
    }

    /*
     * This function is used to show Return Listing count on Top of the Admin panel.
     * Added By Priyanshu on 17-March-2021
     */
    public function hookDisplayBackOfficeTop()
    {
        $velsof_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        if (isset($velsof_data['enable']) && $velsof_data['enable'] == 1 && !Tools::isSubmit('ajax')) {
            $cancel_order = $this->getCancels();
            if (isset($cancel_order['data']) && $cancel_order['data'] != null) {
                $cancel_order_count = count($cancel_order['data']);
                $this->context->smarty->assign('cancel_order_count', $cancel_order_count);
            } else {
                $this->context->smarty->assign('cancel_order_count', 0);
            }
            $cancel_order_approved = $this->getCancels(2);
            if (isset($cancel_order_approved['data']) && $cancel_order_approved['data'] != null) {
                $cancel_order_approved_count = count($cancel_order_approved['data']);
                $this->context->smarty->assign('cancel_order_approved_count', $cancel_order_approved_count);
            } else {
                $this->context->smarty->assign('cancel_order_approved_count', 0);
            }
            $return_active = $this->getReturns(2);
            if (isset($return_active['data']) && $return_active['data'] != null) {
                $active_return_count = count($return_active['data']);
                $this->context->smarty->assign('active_return_count', $active_return_count);
            } else {
                $this->context->smarty->assign('active_return_count', 0);
            }
            $return_pending = $this->getReturns();
            if (isset($return_pending['data']) && $return_pending['data'] != null) {
                $pending_return_count = count($return_pending['data']);
                $this->context->smarty->assign('pending_return_count', $pending_return_count);
            } else {
                $this->context->smarty->assign('pending_return_count', 0);
            }
            $cancel_returns = $this->getReturns(5);
            if (isset($cancel_returns['data']) && $cancel_returns['data'] != null) {
                $cancel_return_count = count($cancel_returns['data']);
                $this->context->smarty->assign('cancel_return_count', $cancel_return_count);
            } else {
                $this->context->smarty->assign('cancel_return_count', 0);
            }
            $link = $this->context->link->getAdminLink('AdminModules');
            $module_path = $link . '&configure=' . $this->name;
            $this->context->smarty->assign('module_path', $module_path);
            return $this->display(__FILE__, 'listingCount_admin.tpl');
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        unset($params);
        $velsof_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        if (isset($velsof_data['enable']) && $velsof_data['enable'] == 1) {
            if (Tools::getValue('id_product')) {
                $id_product = Tools::getValue('id_product');
                $get_policy_qry = 'select distinct(return_data_id) from ' . _DB_PREFIX_ . 'velsof_return_policy_product
					where id_product="' . (int) $id_product . '"';
                $policy_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_policy_qry);
                if ($policy_data) {
                    $policy = $policy_data[0]['return_data_id'];
                    $this->context->smarty->assign('velsof_return_policy', $policy);
                }
            }
            $velsof_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
            if (isset($velsof_data['policy']['default']) && ($velsof_data['policy']['default'] != 0)) {
                $this->context->smarty->assign('velsof_default_return_policy', $velsof_data['policy']['default']);
            }

            $policy_detail = $this->select('policy');
            $this->context->smarty->assign('policy', $policy_detail);
            return $this->display(__FILE__, 'views/templates/admin/admin_product_returnmanager.tpl');
        } else {
            return $this->displayError($this->l('Please enable Returns Manager first.'));
        }
    }

    public function hookActionProductSave($params)
    {
        $velsof_data = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        if (isset($velsof_data['enable']) && $velsof_data['enable'] == 1) {
            $id_product = $params['id_product'];
            $cat_id = Tools::getValue('id_category_default');
            $policy_id = Tools::getValue('velsof_return_policy');
            $get_policy_qry = 'select distinct(return_data_id) from ' . _DB_PREFIX_ . 'velsof_return_policy_product
				where id_product="' . (int) $id_product . '"';
            $policy_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_policy_qry);
            if ($policy_data) {
                $update_policy = 'update ' . _DB_PREFIX_ . 'velsof_return_policy_product set
					return_data_id="' . (int) $policy_id . '" where id_product="' . (int) $id_product . '"';
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_policy);
            } else {
                $mapping = 'insert into ' . _DB_PREFIX_ . 'velsof_return_policy_product
				values(' . (int) $policy_id . ',' . (int) $id_product . ',' . (int) $cat_id . ')';
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($mapping);
            }
        }
    }
}
