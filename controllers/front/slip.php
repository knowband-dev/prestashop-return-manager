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
//start:changes made by aayushi on 14 Nov 2018 to resolve the issues related to return slip
//include(_PS_ROOT_DIR_ . '/init.php');
//end:changes made by aayushi on 14 Nov 2018 to resolve the issues related to return slip
class ReturnManagerSlipModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $settings = json_decode(Configuration::get('VELSOF_RETURNMANAGER'), true);
        if (isset($settings['enable']) && $settings['enable'] == 1) {
            /**
             * if return slip is enabled then generate the return slip by calling the function generateReturnSlip else redirect to home page
             * @date 21-02-2023
             * @commenter Prvind Panday
             */
            if (isset($settings['enable_return_slip']) && $settings['enable_return_slip'] == 1) {
                parent::initContent();
                $common_obj = new Common();
                $common_obj->generateReturnSlip(Tools::getValue('return_id'), 'click');
            } else {
                Tools::redirect('index.php');
            }
        } else {
            Tools::redirect('index.php');
        }
        unset($settings);
    }
}
