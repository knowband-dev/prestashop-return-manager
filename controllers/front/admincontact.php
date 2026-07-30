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
 * @copyright 2015 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class ReturnManagerAdminContactModuleFrontController extends ModuleFrontController
{
    /*
     * Module directory path for asset registration.
     * 21-07-2026
     * @var string
     */
    public $module_dir = '';

    public function __construct()
    {
        $this->context = Context::getContext();

        parent::__construct();

        require_once _PS_MODULE_DIR_ . $this->module->name . '/classes/RmTicket.php';
        require_once _PS_MODULE_DIR_ . $this->module->name . '/classes/common.php';
    }

    /**
     * @return bool
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->module_dir = _PS_MODULE_DIR_ . 'returnmanager/';
        $this->addCSS($this->module_dir . 'views/css/front_ticket.css');
        $this->addJS($this->module_dir . 'views/js/front_ticket.js');
        /*
         * FrontController::setMedia must return bool.
         * 21-07-2026
         */
        return true;
    }

    public function initContent()
    {
        /**
         * check if customer is logged in or not, if not then show error message and return invalid page, else check if return id is missing or invalid, if yes then show error message and return invalid page
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if (!$this->context->customer->isLogged()) {
            $this->context->smarty->assign('message', $this->module->l('Return Request Could not be Found.Kindly login to create a ticket.', 'admincontact'));
            $this->context->smarty->assign(
                'referer_link',
                (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null)
            );
            $this->setTemplate('module:returnmanager/views/templates/front/invalid_page.tpl');
        } else if (!Tools::getIsset('id_return') || Tools::getValue('id_return') == '') {
            $this->context->smarty->assign('message', $this->module->l('Return Request Could not be Found.', 'admincontact'));
            $this->context->smarty->assign(
                'referer_link',
                (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null)
            );
            $this->setTemplate('module:returnmanager/views/templates/front/invalid_page.tpl');
        } else {
            /**
             * If return id is valid then check if return request is valid or not, if not then show error message and return invalid page, else show create ticket form
             * @date 21-02-2023
             * @commenter Prvind Panday
             */
            $id_return = Tools::getValue('id_return');
            $id_customer = $this->context->customer->id;
            if (!$this->isValidReturnRequest($id_return, $id_customer)) {
                $this->context->smarty->assign('message', $this->module->l('Return Request Could not be Found.', 'admincontact'));
                $this->context->smarty->assign(
                    'referer_link',
                    (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null)
                );
                $this->setTemplate('module:returnmanager/views/templates/front/invalid_page.tpl');
            } else {

                $RmTicket = new RmTicket();
                /**
                 * Checking for the previously created tickets for the same order.
                 * @date 09-04-2024
                 * @author Ravi Kant Gupta
                 */
                $ticket = $RmTicket->getTicketIdByReturnId($id_return);
                if (!empty($ticket)) {
                    //If the ticket is already created for the return request then redirect to the ticket view page.
                    Tools::redirect(
                        $this->context->link->getModuleLink(
                            'returnmanager',
                            'customerticketview',
                            array(
                                'id_rm_ticket' => $ticket
                            ),
                            (bool) Configuration::get('PS_SSL_ENABLED')
                        )
                    );
                } else {

                    /**
                     * Return the form as the return request is valid and customer is logged in
                     * @date 21-02-2023
                     * @commenter Prvind Panday
                     */
                    $this->context->smarty->assign('id_return', Tools::getValue('id_return'));
                    $this->renderForm();
                }
            }
        }

        /*
         * Cookie flash messages via magic accessors for validator.
         * 21-07-2026
         */
        if ($this->context->cookie->__isset('redirect_error')) {
            $this->errors[] = $this->context->cookie->__get('redirect_error');
            $this->context->cookie->__unset('redirect_error');
        }

        if ($this->context->cookie->__isset('redirect_success')) {
            $confirmations = $this->context->cookie->__get('redirect_success');
            $this->context->cookie->__unset('redirect_success');
            $this->context->smarty->assign('confirmations', $confirmations);
        }

        parent::initContent();
    }

    /**
     * Render Form for admin contact
     * @date 21-02-2023
     * @commenter Prvind Panday
     * @return void
     */
    public function renderForm()
    {
        $customer_info = array();
        /**
         * If customer is logged in then get customer information and assign it to smarty variable
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if ($this->context->customer->logged) {
            $customer_info = array(
                'cus_fname' => $this->context->customer->firstname,
                'cus_lname' => $this->context->customer->lastname,
                'cus_email' => $this->context->customer->email
            );
        }
        $this->context->smarty->assign('is_logged', $this->context->customer->isLogged());
        $this->context->smarty->assign('customer_info', $customer_info);
        $this->setTemplate('module:returnmanager/views/templates/front/new_ticket.tpl');
    }

    /**
     * function to handle the ajax request for creating new ticket
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    public function postProcess()
    {
        parent::postProcess();
        /**
         * If ajax request is for creating new ticket then create new ticket.
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if (Tools::isSubmit('kbmpss_new_ticket') && $data = Tools::getValue('kbmpss_new_ticket', array())) {
            $id_return = (int) Tools::getValue('id_return');
            $ticket = new RmTicket();
            $ticket->id_return = $id_return;
            $ticket->ticket_number = RmTicket::generateTicketNumber();
            $ticket->subject = $data['subject'];
            $ticket->cus_email = $data['cus_email'];
            $ticket->cus_fname = $data['cus_fname'];
            $ticket->cus_lname = $data['cus_lname'];
            $ticket->phone_number = $data['phone_number'];
            $ticket->status = RmTicket::STATUS_PENDING;
            $data['message'] = addslashes($data['message']);

            /**
             * If ticket is created successfully then create a thread for the ticket and send email to customer and admin for the ticket creation
             * @date 21-02-2023
             * @commenter Prvind Panday
             */
            if ($ticket->save(true)) {
                $data['id_rm_ticket'] = $ticket->id;
                $data['reply_by'] = RmTicket::REPLY_BY_CUSTOMER;
                $data['is_approved'] = 1;
                RmTicket::insertThread($data);
                $id_customer = 0;
                $id_customer = RmTicket::getIdCustomerByReturnId($id_return);
                $email_data = array();
                $email_data['track_url'] = $this->context->link->getModuleLink(
                    'returnmanager',
                    'customerticketview',
                    array(
                        'id_rm_ticket' => $ticket->id
                    ),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );
                $email_data['subject'] = $ticket->subject;
                $email_data['return_id'] = $ticket->id_return;
                $email_data['message'] = $data['message'];
                $email_data['id_customer'] = $id_customer;
                $email_data['ticket_number'] = $ticket->ticket_number;
                /*
                 * Guard Module::getInstanceByName before email send.
                 * 21-07-2026
                 */
                $common_obj = Module::getInstanceByName('returnmanager');
                if ($common_obj instanceof Common) {
                    $common_obj->sendNotificationEmail('new_ticket_client', $email_data);
                    $common_obj->sendNotificationEmail('new_ticket_admin', $email_data);
                }
            } else {
                /**
                 * If ticket is not created successfully then redirect to the same page with error message
                 * @date 21-02-2023
                 * @commenter Prvind Panday
                 */
                $this->context->cookie->__set('redirect_error', $this->module->l('Some error occurred while submitting your ticket. Please try again later.', 'admincontact'));
                Tools::redirect(
                    $this->context->link->getModuleLink(
                        $this->module->name,
                        'admincontact',
                        array(
                            'id_return' => $id_return
                        ),
                        (bool) Configuration::get('PS_SSL_ENABLED')
                    )
                );
            }
            Tools::redirect(
                $this->context->link->getModuleLink(
                    $this->module->name,
                    'customerticketview',
                    array(
                        'id_rm_ticket' => $ticket->id,
                        'show_confirmation_ticket' => 1
                    ),
                    (bool) Configuration::get('PS_SSL_ENABLED')
                )
            );
        }
    }

    /**
     * Function to check if the return request is valid or not
     * @date 21-02-2023
     * @commenter Prvind Panday
     * @param int $id_return
     * @param int $id_customer
     * @return int
     */
    public function isValidReturnRequest($id_return, $id_customer)
    {
        /*
         * Use (int) casts and Db:: correct case for Addons validator.
         * 21-07-2026
         */
        $sql = 'select count(*) from ' . _DB_PREFIX_ . 'velsof_rm_order WHERE id_rm_order = ' . (int) $id_return . ' and id_customer = ' . (int) $id_customer;
        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
}
