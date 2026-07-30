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
class ReturnManagerCustomerTicketViewModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $this->context = Context::getContext();

        parent::__construct();
        require_once _PS_MODULE_DIR_ . $this->module->name . '/classes/RmTicket.php';
        require_once _PS_MODULE_DIR_ . $this->module->name . '/classes/common.php';
        /*
         * Do not reassign $this->module — parent already injects the Module instance.
         * 21-07-2026
         */

        /**
         * check if ticket id is missing or invalid, if yes then show error message and return invalid page
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if (!Tools::getIsset('id_rm_ticket') || Tools::getValue('id_rm_ticket') == '') {
            $this->context->smarty->assign('message', $this->module->l('Ticket Id is missing.', 'customerticketview'));
            $this->context->smarty->assign(
                'referer_link',
                (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null)
            );
            $this->setTemplate('module:returnmanager/views/templates/front/invalid_page.tpl');
            //$this->setTemplate('invalid_page.tpl');
        } else {
            /**
             * If ticket id is valid then check if ticket is assigned to the logged in customer or not, if not then show error message and return invalid page
             * @date 21-02-2023
             * @commenter Prvind Panday
             */
            $id_rm_ticket = (int) Tools::getValue('id_rm_ticket');
            $ticket = new RmTicket($id_rm_ticket);
            if (!$ticket->id || $ticket->cus_email != $this->context->customer->email) {
                $this->context->smarty->assign('message', $this->module->l('Invalid request.', 'customerticketview'));
                $this->context->smarty->assign(
                    'referer_link',
                    (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null)
                );
                $this->setTemplate('module:returnmanager/views/templates/front/invalid_page.tpl');
                //$this->setTemplate('invalid_page.tpl');
            }
        }
    }

    /**
     * @return bool
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/front_ticket.css');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/front_ticket.js');
        /*
         * FrontController::setMedia must return bool.
         * 21-07-2026
         */
        return true;
    }

    public function initContent()
    {
        $this->viewTicket();

        if ($this->context->cookie->__isset('redirect_success')) {
            $confirmations = $this->context->cookie->__get('redirect_success');
            $this->context->cookie->__unset('redirect_success');
            $this->context->smarty->assign('confirmations', $confirmations);
        }

        parent::initContent();
    }

    /**
     * Function to display the ticket details, ticket threads and post reply form
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    public function viewTicket()
    {
        /**
         * RmTicket object is created to get the ticket details.
         * RmTicket::getThreads() is used to get the ticket threads.
         * RmTicket::STATUS_PENDING, RmTicket::STATUS_OPEN, RmTicket::STATUS_CLOSED are used to get the status of the ticket.
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        $id_rm_ticket = (int) Tools::getValue('id_rm_ticket');
        $ticket_statuses = array(
            RmTicket::STATUS_PENDING => $this->module->l('Pending', 'customerview'),
            RmTicket::STATUS_OPEN => $this->module->l('Open', 'customerview'),
            RmTicket::STATUS_CLOSED => $this->module->l('Closed', 'customerview'),
        );
        $ticket = new RmTicket($id_rm_ticket);
        /* start changes done by rishabh jain on 1st august to hide post reply field when ticket is closed*/
        $is_closed = 0;
        if ($ticket->status == 2) {
            $is_closed = 1;
        }
        /**
         * If the customer is not logged in then redirect the user to the login page
         * @date 09-04-2024
         * @author Ravi Kant Gutpa
         */

        if ($this->context->customer->isLogged() != '1') {
            Tools::redirect(
                $this->context->link->getPageLink('my-account', true)
            );
        } else {
            // Check if the ticket is assigned to the logged in customer or not
            $sql = 'select * from ' . _DB_PREFIX_ . 'kb_rm_ticket WHERE id_rm_ticket = '
                . (int) $id_rm_ticket . ' AND cus_email = "' . pSQL($this->context->customer->email) . '"';
            /*
             * Correct Db class case for Addons validator.
             * 21-07-2026
             */
            $tkt = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            // If the ticket is not assigned to the logged in customer then show error message
            if (empty($tkt)) {
                echo $this->module->l("No open ticket details found", "customerticketview");
                die;
            }
        }
        /* end changes done by Ravi Kant Gupta on 09-04-2024 to prevent unauthorised access on the ticket*/
        $this->context->smarty->assign('is_closed', $is_closed);
        /* channges over */
        $ticket->status = $ticket_statuses[$ticket->status];
        $this->context->smarty->assign('id_rm_ticket', Tools::getValue('id_rm_ticket'));
        $this->context->smarty->assign('ticket', $ticket);
        $this->context->smarty->assign('ticket_threads', $ticket->getThreads(true));
        if (Tools::getIsset('show_confirmation_ticket') || Tools::getValue('show_confirmation_ticket') == 1) {
            $this->context->smarty->assign('show_success', 1);
        }
        if (Tools::getIsset('thread_created_success') || Tools::getValue('thread_created_success') == 1) {
            $this->context->smarty->assign('thread_success_message', 1);
        }
        /**
         * Start Changes to fix the issue of 500 error because of the different number of parameters in the function
         * In PS8 and above, only two params are allowed in the displayDate(). So, adding the PS version check
         * NAFeb2024 displaydate
         * @date 06-02-2024
         * @modifier Nikhil Aggarwal
         */
        $this->context->smarty->assign('ps_version_store', _PS_VERSION_);
        // Changes end by Nikhil Aggarwal
        $this->setTemplate('module:returnmanager/views/templates/front/view_ticket.tpl');
        //$this->setTemplate('view_ticket.tpl');
    }

    /**
     * Function to handle the ajax request made to this controller 
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    public function postProcess()
    {
        parent::postProcess();
        /**
         * If post reply is set to 1 then the reply is posted by the customer, the ticket status is changed to pending and the reply is inserted in the database.
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if (Tools::getIsset('post_reply') && Tools::getValue('post_reply') == 1) {
            $message = Tools::getValue('post_reply_message', '');
            $ticket = new RmTicket(Tools::getValue('id_rm_ticket'));
            $ticket->status = RmTicket::STATUS_PENDING;
            $ticket->update(true);
            $data = array();
            $data['message'] = addslashes($message);
            $data['id_rm_ticket'] = $ticket->id;
            $data['reply_by'] = RmTicket::REPLY_BY_CUSTOMER;
            $data['is_approved'] = 1;

            /**
             * If the reply is inserted successfully then the customer is redirected to the ticket view page with a success message and the customer, admin is notified by email.
             * @date 21-02-2023
             * @commenter Prvind Panday
             */
            if (RmTicket::insertThread($data)) {
                $id_customer = 0;
                $id_customer = RmTicket::getIdCustomerByReturnId($ticket->id_return);
                $email_data = array();
                $email_data['track_url'] = $this->context->link->getModuleLink(
                    'returnmanager',
                    'customerticketview',
                    array(
                        'id_rm_ticket' => Tools::getValue('id_rm_ticket')
                    ),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );
                $email_data['subject'] = $ticket->subject;
                $email_data['message'] = $data['message'];
                $email_data['id_customer'] = $id_customer;
                $email_data['return_id'] = $ticket->id_return;
                $email_data['ticket_number'] = $ticket->ticket_number;
                /*
                 * Guard Module::getInstanceByName before email send.
                 * 21-07-2026
                 */
                $common_obj = Module::getInstanceByName('returnmanager');
                if ($common_obj instanceof Common) {
                    $common_obj->sendNotificationEmail('client_reply_admin', $email_data);
                    $common_obj->sendNotificationEmail('client_reply_client', $email_data);
                }

                Tools::redirect(
                    $this->context->link->getModuleLink(
                        $this->module->name,
                        'customerticketview',
                        array(
                            'id_rm_ticket' => Tools::getValue('id_rm_ticket'),
                            'thread_created_success' => 1
                        ),
                        (bool) Configuration::get('PS_SSL_ENABLED')
                    )
                );
            } else {
                /**
                 * If the reply is not inserted successfully then the customer is redirected to the ticket view page with an error message.
                 * @date 21-02-2023
                 * @commenter Prvind Panday
                 */
                $this->errors[] = $this->module->l('Error occurred while posting your reply. Please try again later.', 'customerticketview');
            }
        }
    }
}
