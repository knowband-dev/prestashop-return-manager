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
/*
 * Corrected Db class case (Db::getInstance) for Addons validator compatibility.
 * 21-07-2026
 */
class RmTicket extends ObjectModel
{
    /**
     * constants defined for the class
     * @date 05-02-2023
     * @commenter Prvind Panday
     */
    const STATUS_PENDING = 0;
    const STATUS_OPEN = 1;
    const STATUS_CLOSED = 2;

    const REPLY_BY_CUSTOMER = 1;
    const REPLY_BY_ADMIN = 2;

    public $id;
    public $ticket_number;
    public $id_return;
    public $cus_fname;
    public $cus_lname;
    public $cus_email;
    public $phone_number;
    public $subject;
    public $status;
    public $date_add;
    public $date_upd;

    /*
     * Table definition for the class
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @comment $definition is a static variable of ObjectModel class
     */
    public static $definition = array(
        'table' => 'kb_rm_ticket',
        'primary' => 'id_rm_ticket',
        'fields' => array(
            'ticket_number' => array('type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber'),
            'id_return' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'cus_fname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true),
            'cus_lname' => array('type' => self::TYPE_STRING, 'validate' => 'isName'),
            'cus_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true),
            'phone_number' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber'),
            'subject' =>   array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'status' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    /*
     * Function insertThread is used to insert the thread of the ticket when customer reply to the ticket or add a new ticket after return request
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param array $data
     * @return boolean
     */
    public static function insertThread($data)
    {
        /*
         * $data = array(
         *  'id_rm_ticket' => (int) $id_rm_ticket,
         *  'message' => (string) $message,
         *  'reply_by' => (int) $reply_by,
         *  'is_approved' => (int) $is_approved
         * );
         * Checked if data is array and not empty
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if (!is_array($data) || empty($data)) {
            return false;
        }

        /*
         * Query to add the thread of the ticket into the database
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'kb_rm_ticket_thread 
            (id_rm_ticket, message, reply_by, is_approved, date_add) 
            VALUES (' . (int) $data['id_rm_ticket'] . ', "' . pSQL($data['message'])
            . '", ' . (int) $data['reply_by'] . ', ' . (int)$data['is_approved'] . ', "' . pSQL(date('Y-m-d H:i:s')) . '")';

        /*
         * Execute the query and return the result
         * @date 05-02-2023
         * @commenter Prvind Panday
         * @comment if query executed successfully then return true else return false
         */
        if (Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Function generateTicketNumber is used to generate the unique ticket number
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @return string $code
     */
    public static function generateTicketNumber()
    {
        $length = 15;
        $code = '';
        $chars = '0123456789';
        $maxlength = Tools::strlen($chars);
        if ($length > $maxlength) {
            $length = $maxlength;
        }
        $i = 0;
        /*
         * Generate the random string
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        while ($i < $length) {
            $char = Tools::substr($chars, mt_rand(0, $maxlength - 1), 1);
            if (!strstr($code, $char)) {
                $code .= $char;
                $i++;
            }
        }
        /*
         * Check if the random ticket number generated above already exists in the database or not. If exists then generate the new ticket number else again create a random number and check if it exists in the database or not
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $sql = 'SELECT id_rm_ticket FROM ' . _DB_PREFIX_ . 'kb_rm_ticket where ticket_number = "' . pSQL($code) . '"';

        if ((int) Db::getInstance()->getValue($sql) == 0) {
            return $code;
        }
        return self::generateTicketNumber();
    }

    /*
     * Function getSellerTickets is used to get the tickets of the seller
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param int $id_seller
     * @param boolean $only_count
     * @param int $status
     * @param int $start
     * @param int $limit
     * @param string $filter_string
     * @return array
     */
    public static function getSellerTickets(
        $id_seller,
        $only_count = false,
        $status = false,
        $start = null,
        $limit = null,
        $filter_string = null
    ) {

        /*
         * Query to get the tickets of the seller from the database basis of the id_seller
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $sql = 'Select {{COLUMN}} from ' . _DB_PREFIX_ . 'kb_rm_ticket Where id_seller = ' . (int)$id_seller;
        /*
         * Check if the status is not false then add the status in the query
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ($status !== false) {
            $sql .= ' AND status  = "' . (int) $status . '"';
        }
        /*
         * Check if the filter_string is not empty then add the filter_string in the query
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if (!empty($filter_string)) {
            $sql .= $filter_string;
        }

        /*
         * Check if the only_count is true then add the count in the query else add the * in the query
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ($only_count) {
            /*
             * Replace the {{COLUMN}} with the count in the query
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $sql = str_replace('{{COLUMN}}', 'COUNT(id_ss_ticket) as total', pSQL($sql));
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            /*
             * Replace the {{COLUMN}} with the * in the query
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $col_string = '* ';
            $sql = str_replace('{{COLUMN}}', $col_string, $sql);
            /*
             * Add the order by and limit in the query
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $sql .= ' ORDER BY id_ss_ticket DESC';

            if ((int) $start >= 0 && (int) $limit > 0) {
                $sql .= ' LIMIT ' . (int) $start . ', ' . (int) $limit;
            } elseif ((int) $limit > 0) {
                $sql .= ' LIMIT ' . (int) $limit;
            }
            /*
             * Execute the query and return the result
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    /*
     * Function getThreads is used to get the threads of the ticket
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param boolean $only_approved
     * @return array
     */
    public function getThreads($only_approved = false)
    {
        /*
         * Query to get the threads of the ticket from the database basis of the id_rm_ticket
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_rm_ticket_thread '
            . 'where id_rm_ticket = ' . (int) $this->id;

        /*
         * Check if the only_approved is true then add the is_approved in the query
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ($only_approved) {
            $sql .= ' AND is_approved = 1';
        }
        /*
         * Add the order by in the query
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $sql .= ' ORDER BY id_rm_ticket_thread ASC';
        $threads = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        return $threads;
    }

    /*
     * Function getLastMessageDate is used to get the last message date of the ticket
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param boolean $only_approved
     * @param int $id_ss_ticket
     * @return array
     */
    public static function getLastMessageDate($id_ss_ticket, $only_approved = false)
    {
        /*
         * Query to get the last message date of the ticket from the database basis of the id_rm_ticket
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $sql = 'select MAX(date_add) as last_message_date from ' . _DB_PREFIX_ . 'kb_rm_ticket_thread'
            . ' WHERE id_ss_ticket = ' . (int)$id_ss_ticket;
        /*
         * Check if the only_approved is true then add the is_approved in the query
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ($only_approved) {
            $sql .= ' AND is_approved = 1';
        }
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /*
     * Function getTicketCount is used to get the count of the ticket
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param array $statuses
     * @return array
     */
    public static function getTicketCount($statuses = array())
    {
        /*
         * Query to get count ticket from the database
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $sql = 'select COUNT(*) as total from ' . _DB_PREFIX_ . 'kb_rm_ticket WHERE 1';
        /*
         * Check if the statuses is array and not empty then add the status in the query
         * @date 05-02-2023
         * @commenter Prvind Panday
         * @comment implode is used to convert the array into string
         */
        if (is_array($statuses) && !empty($statuses)) {
            $sql .= ' AND status IN (' . implode(',', $statuses) . ')';
        } elseif (!is_array($statuses) && (int)$statuses >= 0) {
            $sql .= ' AND status = ' . (int)$statuses;
        }
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(pSQL($sql));
    }

    /*
     * Function getTicketByNumberAndEmail is used to get the ticket by number and email
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param string $ticket_number
     * @param string $email
     * @return array
     */
    public static function getTicketByNumberAndEmail($ticket_number, $email)
    {
        /*
         * Query to get the ticket from the database basis of the ticket_number and email
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $sql = 'select * from ' . _DB_PREFIX_ . 'kb_rm_ticket WHERE ticket_number = "'
            . pSQL($ticket_number) . '" AND cus_email = "' . pSQL($email) . '"';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    /*
     * Function getCustomerTickets is used to get the customer tickets
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param string $email
     * @param boolean $count
     * @return array
     */
    public static function getCustomerTickets($email, $count = false)
    {
        /*
         * Query to get the customer tickets from the database basis of the email, if count is true then get the count of the tickets
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        if ($count) {
            $sql = 'select COUNT(*) as total from ' . _DB_PREFIX_ . 'kb_rm_ticket WHERE cus_email = "' . pSQL($email) . '"';
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            /*
             * Query to get the customer tickets from the database basis of the email
             * @date 05-02-2023
             * @commenter Prvind Panday
             */
            $sql = 'select * from ' . _DB_PREFIX_ . 'kb_rm_ticket WHERE cus_email = "' . pSQL($email) . '"';
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    /*
     * Function getTicketIdByReturnId is used to get the ticket id by return id
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param int $id_return
     * @return array
     */
    public static function getTicketIdByReturnId($id_return)
    {
        /*
         * Query to get the ticket id from the database basis of the id_return
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $sql = 'select id_rm_ticket from ' . _DB_PREFIX_ . 'kb_rm_ticket WHERE  id_return = "' . pSQL($id_return) . '"';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /*
     * Function getStateLabelById is used to get the return state label by id
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param int $id_status
     * @return array
     */
    public static function getStateLabelById($id_status)
    {
        /*
         * Module::getInstanceByName is used to get the instance of the module
         * RmTicket::STATUS_PENDING is used to get the pending status
         * RmTicket::STATUS_OPEN is used to get the open status
         * RmTicket::STATUS_CLOSED is used to get the closed status
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $module = Module::getInstanceByName('returnmanager');
        $ticket_statuses = array(
            RmTicket::STATUS_PENDING => $module->l('Pending', 'MyTickets'),
            RmTicket::STATUS_OPEN => $module->l('Open', 'MyTickets'),
            RmTicket::STATUS_CLOSED => $module->l('Closed', 'MyTickets'),
        );
        return $ticket_statuses[$id_status];
    }

    /*
     * Function getIdCustomerByReturnId is used to get the customer id by return id
     * @date 05-02-2023
     * @commenter Prvind Panday
     * @param int $id_return
     * @return array
     */
    public static function getIdCustomerByReturnId($id_return)
    {
        /*
         * Query to get the customer id from the database basis of the id_return
         * @date 05-02-2023
         * @commenter Prvind Panday
         */
        $sql = 'select id_customer from ' . _DB_PREFIX_ . 'velsof_rm_order WHERE  id_rm_order = "' . pSQL($id_return) . '"';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
}
