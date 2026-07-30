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
class AdminRmTicketSystemController extends ModuleAdminControllerCore
{
    /*
     * Custom Smarty instance for admin ticket templates.
     * 21-07-2026
     * @var Smarty
     */
    public $custom_smarty;

    /*
     * Ticket status labels keyed by RmTicket status constants.
     * 21-07-2026
     * @var array
     */
    public $ticket_statuses = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->allow_export = false;
        $this->lang = false;
        $this->context = Context::getContext();
        
        parent::__construct();
        
        $this->custom_smarty = new Smarty();
        $this->custom_smarty->setTemplateDir(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/');
        require_once _PS_MODULE_DIR_ . $this->module->name. '/classes/RmTicket.php';
        require_once _PS_MODULE_DIR_ . $this->module->name. '/classes/common.php';
        $this->ticket_statuses = array(
            RmTicket::STATUS_PENDING => $this->module->l('Pending', 'AdminRmTicketSystemController'),
            RmTicket::STATUS_OPEN => $this->module->l('Open', 'AdminRmTicketSystemController'),
            RmTicket::STATUS_CLOSED => $this->module->l('Closed', 'AdminRmTicketSystemController'),
        );
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS($this->getModulePath().'views/css/mp_ticket_admin.css');
        $this->addJS($this->getModulePath().'views/js/admin/kb-mp-ticket.js');
        $this->addJS($this->getModulePath().'views/js/velovalidation.js');
    }
    public function initContent()
    {
        /**
         * If we are editing a ticket, we need to display the view page by calling the renderView() method
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if (Tools::getValue('id_rm_ticket', 0)) {
            $this->content = $this->renderView();
        }
        parent::initContent();
        /*
         * Read flash messages via Cookie magic methods for Addons validator.
         * 21-07-2026
         */
        if ($this->context->cookie->__isset('mp_ticket_redirect_error')) {
            $this->errors[] = $this->context->cookie->__get('mp_ticket_redirect_error');
            $this->context->cookie->__unset('mp_ticket_redirect_error');
        }

        if ($this->context->cookie->__isset('mp_ticket_redirect_success')) {
            $this->confirmations[] = $this->context->cookie->__get('mp_ticket_redirect_success');
            $this->context->cookie->__unset('mp_ticket_redirect_success');
        }
    }
    
    protected function getModulePath()
    {
        return _PS_MODULE_DIR_.$this->module->name.'/';
    }

    /**
     * Function to render the list of tickets
     * @date 21-02-2023
     * @commenter Prvind Panday
     * @return string
     */
    public function renderView()
    {
        $ticket = new RmTicket(Tools::getValue('id_rm_ticket', 0));
        /**
         * If the ticket is not loaded, redirect to the list page
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if (!Validate::isLoadedObject($ticket)) {
            /*
             * Store redirect error via Cookie::__set for validator compatibility.
             * 21-07-2026
             */
            $this->context->cookie->__set('mp_ticket_redirect_error', $this->module->l('Cannot load object.'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminRmTicketSystem'));
            return '';
        }
        $is_closed = 0;
        if ($ticket->status == 2) {
            $is_closed = 1;
        }
        $topic = array();
        
        $ticket_threads = $ticket->getThreads();
        $timeline_ticket_states = array();
        $timeline_ticket_priority = array();

        $issue_thread_id = 0;
        /*
         * Default ticket summary when no threads exist.
         * 21-07-2026
         */
        $ticket_summary = '';
        /**
         * If the ticket has threads, then we need to display the first thread as the issue
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if (count($ticket_threads) > 0) {
            $ticket_summary = $ticket_threads[0]['message'];
            $current_state = null;
            $current_priority = null;
            $is_issue = true;
            foreach ($ticket_threads as $tmp) {
                if ($is_issue) {
                    $is_issue = false;
                    $issue_thread_id = $tmp['id_rm_ticket_thread'];
                    $current_state = $ticket->status;
                    continue;
                }
            }
        }

        $tpl  = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_.'returnmanager/views/templates/admin/view.tpl'
        );
        $tpl->assign(array(
            'ticket' => $ticket,
            'ticket_summary' => $ticket_summary,
            'customer_name' => $ticket->cus_fname.' '.$ticket->cus_lname,
            'customer_email' => $ticket->cus_email,
            'customer_phone_number' => $ticket->phone_number,
            'issue' => '',
            'ticket_states' => $this->ticket_statuses,
            'timelined_threads' => array_reverse($ticket_threads),
            'issue_thread_id' => $issue_thread_id,
            'timeline_ticket_states' => $timeline_ticket_states,
            'post_reply_action' => self::$currentIndex . '&token=' . $this->token
        ));
        return $tpl->fetch();
    }

    /**
     * function to handle the ajax requests made to this controller
     * @date 21-02-2023
     * @commenter Prvind Panday
     */
    public function postProcess()
    {
        parent::postProcess();
        /**
         * If the request is to change the state of the ticket, then we need to handle it here
         * @date 21-02-2023
         * @commenter Prvind Panday
         */
        if (Tools::getIsset('quickstatechange')) {
            $this->redirect_after = self::$currentIndex . '&token=' . $this->token.'&id_rm_ticket='.Tools::getValue('id_rm_ticket', 0);
            if (Tools::getIsset('id_rm_ticket')) {
                $obj = new RmTicket(Tools::getValue('id_rm_ticket', 0));
                /**
                 * If the ticket is not loaded, then we need to redirect to the list page
                 * @date 21-02-2023
                 * @commenter Prvind Panday
                 */
                if (!Validate::isLoadedObject($obj)) {
                    $this->context->cookie->__set('mp_ticket_redirect_error', $this->module->l('Cannot load object', 'AdminRmTicketSystemController'));
                    $this->redirect();
                }

                $obj->status  = Tools::getValue('state', 0);
                /**
                 * If the ticket is saved successfully, then we need to redirect to the list page with message of success else we need to redirect to the list page with error message
                 * @date 21-02-2023
                 * @commenter Prvind Panday
                 */
                if ($obj->save()) {
                    $this->context->cookie->__set('mp_ticket_redirect_success', $this->module->l('Ticket state has been changed', 'AdminRmTicketSystemController'));
                } else {
                    $this->context->cookie->__set('mp_ticket_redirect_error', $this->module->l('Cannot update state', 'AdminRmTicketSystemController'));
                }
                $this->redirect();
            } else {
                $this->context->cookie->__set('mp_ticket_redirect_error', $this->module->l('Cannot load object', 'AdminRmTicketSystemController'));
                $this->redirect();
            }
        } elseif (Tools::getIsset('submitMpTicketReply')) {
            /**
             * If the request is to post a reply to the ticket, then we need to handle it here
             * @date 21-02-2023
             * @commenter Prvind Panday
             */
            $this->redirect_after = self::$currentIndex . '&token=' . $this->token.'&id_rm_ticket='.Tools::getValue('id_rm_ticket', 0);
            if (Tools::getIsset('id_rm_ticket')) {
                $obj = new RmTicket(Tools::getValue('id_rm_ticket', 0));
                if (!Validate::isLoadedObject($obj)) {
                    $this->context->cookie->__set('mp_ticket_redirect_error', $this->module->l('Cannot load object', 'AdminRmTicketSystemController'));
                    $this->redirect();
                }
                // changes by rishabh jai for chat system
                if (Tools::getIsset('mp_ticket_state')) {
                    $obj->status = Tools::getValue('mp_ticket_state');
                    if ($obj->save(true)) {
                        $this->context->cookie->__set('mp_ticket_redirect_success', $this->module->l('Reply has been sent to customer.', 'AdminRmTicketSystemController'));
                    } else {
                        $this->context->cookie->__set('mp_ticket_redirect_success', $this->module->l('Reply has been sent to customer but error in updating ticket.', 'AdminRmTicketSystemController'));
                    }
                }
                $data = array();
                $data['message'] = addslashes(Tools::getValue('mp_reply_message', ''));
                $data['id_rm_ticket'] = $obj->id;
                $data['reply_by'] = RmTicket::REPLY_BY_ADMIN;
                $data['is_approved'] = '1';
                
                /**
                 * If the thread is saved successfully, we will send notification email to the customer
                 * @date 21-02-2023
                 * @commenter Prvind Panday
                 */
                if (RmTicket::insertThread($data)) {
                    $id_customer = 0;
                    $id_customer = RmTicket::getIdCustomerByReturnId($obj->id_return);
                    $email_data = array();
                    $email_data['track_url'] = $this->context->link->getModuleLink(
                        'returnmanager',
                        'customerticketview',
                        array(
                            'id_rm_ticket' => Tools::getValue('id_rm_ticket', 0)
                        ),
                        (bool)Configuration::get('PS_SSL_ENABLED')
                    );
                    $email_data['subject'] = $obj->subject;
                    $email_data['message'] = $data['message'];
                    $email_data['id_customer'] = $id_customer;
                    $email_data['return_id'] = $obj->id_return;
                    $email_data['ticket_number'] = $obj->ticket_number;
                    /*
                     * Guard Module::getInstanceByName result before calling module methods.
                     * 21-07-2026
                     */
                    $common_obj = Module::getInstanceByName('returnmanager');
                    if ($common_obj instanceof Common) {
                        $common_obj->sendNotificationEmail('admin_reply_client', $email_data);
                    }
                } else {
                    $this->context->cookie->__set('mp_ticket_redirect_error', $this->module->l('Cannot post reply', 'AdminRmTicketSystemController'));
                }
                if (Tools::getValue('id_rm_ticket', 0)) {
                    $this->content = $this->renderView();
                }
            } else {
                $this->context->cookie->__set('mp_ticket_redirect_error', $this->module->l('Cannot load object', 'AdminRmTicketSystemController'));
                $this->redirect();
            }
        } else {
            if (Tools::getValue('id_rm_ticket', 0)) {
                $this->content = $this->renderView();
            }
        }
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
}
