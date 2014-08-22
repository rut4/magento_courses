<?php

/**
 * Class Oggetto_Payment_Model_Report
 *
 * @method getHash()
 * @method getTotal()
 * @method getOrderId()
 * @method getStatus()
 */
class Oggetto_Payment_Model_Report extends Mage_Core_Model_Abstract
{
    const RESPONSE_PAYMENT_STATUS_SUCCESS   = 1;
    const RESPONSE_PAYMENT_STATUS_ERROR     = 2;

    public function init(Mage_Core_Controller_Request_Http $request)
    {
        $this->setStatus($request->getPost('status'));
        $this->setTotal($request->getPost('total'));
        $this->setOrderId($request->getPost('order_id'));
        $this->setHash($request->getPost('hash'));
    }

    public function validate()
    {
        foreach ($this->_validatorsFactory() as $validator) {
            /** @var Oggetto_Payment_Model_Validator_IValidator $validator */
            if (!$validator->validate($this)) {
                throw new Oggetto_Payment_Model_Exception_Validate('Validation error.');
            }
        }
    }

    protected function _validatorsFactory()
    {
        return [
            Mage::getModel('oggettopayment/validator_hash'),
            Mage::getModel('oggettopayment/validator_order')
        ];
    }

    public function response()
    {
        if ($this->getStatus() == self::RESPONSE_PAYMENT_STATUS_SUCCESS) {
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($this->getOrderId());
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');

            $order->sendNewOrderEmail();
            $order->setEmailSent(true);

            $order->save();

            Mage::getSingleton('checkout/session')->unsQuoteId();

        } else if ($this->getStatus() == self::RESPONSE_PAYMENT_STATUS_ERROR) {
            if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
                $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
                if ($order->getId()) {
                    // Flag the order as 'cancelled' and save it
                    $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
                }
            }
        }
    }
}
