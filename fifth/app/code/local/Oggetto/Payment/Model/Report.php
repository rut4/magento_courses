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
 * Report payment class
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 * @method getHash()
 * @method getTotal()
 * @method getOrderId()
 * @method getStatus()
 */
class Oggetto_Payment_Model_Report extends Mage_Core_Model_Abstract
{
    const RESPONSE_PAYMENT_STATUS_SUCCESS   = 1;
    const RESPONSE_PAYMENT_STATUS_ERROR     = 2;

    /**
     * Init report
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return void
     */
    public function init(Mage_Core_Controller_Request_Http $request)
    {
        $this->setStatus($request->getPost('status'));
        $this->setTotal($request->getPost('total'));
        $this->setOrderId($request->getPost('order_id'));
        $this->setHash($request->getPost('hash'));
    }

    /**
     * Validate report
     *
     * @throws Oggetto_Payment_Model_Exception_Validate
     * @return void
     */
    public function validate()
    {
        foreach ($this->_validatorsFactory() as $validator) {
            /** @var Oggetto_Payment_Model_Validator_IValidator $validator */
            if (!$validator->validate($this)) {
                throw new Oggetto_Payment_Model_Exception_Validate('Validation error.');
            }
        }
    }

    /**
     * Report validators
     *
     * @return array
     */
    protected function _validatorsFactory()
    {
        return [
            Mage::getModel('oggettopayment/validator_hash'),
            Mage::getModel('oggettopayment/validator_order')
        ];
    }

    /**
     * Process on report
     *
     * @return void
     */
    public function process()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->getOrderId());

        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $order->getInvoiceCollection()->getLastItem();

        if ($this->getStatus() == self::RESPONSE_PAYMENT_STATUS_SUCCESS) {
            if ($invoice->canCapture()) {
                $invoice->capture();
            }
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');

            $order->sendNewOrderEmail();
            $order->setEmailSent(true);
        } else if ($this->getStatus() == self::RESPONSE_PAYMENT_STATUS_ERROR) {
            if ($invoice->canCancel()) {
                $invoice->cancel();
            }

            $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway canceled the payment.');
        }
        $order->save();
    }
}
