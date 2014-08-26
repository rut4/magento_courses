<?php
/**
 * Oggetto Web payment extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto Payment module to newer versions in the future.
 * If you wish to customize the Oggetto Payment module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment controller
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Payment_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Redirect to payment gateway
     *
     * @return void
     */
    public function redirectAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Report from gateway action
     *
     * @return void
     */
    public function reportAction()
    {
        if ($this->getRequest()->isPost()) {
            /** @var Oggetto_Payment_Model_Report $report */
            $report = Mage::getModel('oggettopayment/report');
            $report->init($this->getRequest());
            try {
                $report->validate();
                $report->process();
                $this->getResponse()->setHttpResponseCode(200);
            } catch (Oggetto_Payment_Model_Exception_Validate $e) {
                Mage::logException($e);
                $this->getResponse()->setHttpResponseCode(400);
            } catch (Exception $e) {
                Mage::logException($e);
                $this->getResponse()->setHttpResponseCode(500);
            }
        }
    }
}