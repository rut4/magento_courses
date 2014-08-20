<?php

class Oggetto_Payment_PaymentController extends Mage_Core_Controller_Front_Action
{
    public function redirectAction()
    {
        Mage::log('redirect action visited');
        $this->loadLayout();
        $block = $this->getLayout()
            ->createBlock('Oggetto_Payment_Block_Standard', 'payment', ['template' => 'oggetto/payment/redirect.phtml']);
        $this->getLayout()
            ->getBlock('content')
            ->append($block);
        $this->renderLayout();
    }

    public function reportAction()
    {
        Mage::log('report action visited');
        if ($this->getRequest()->isPost()) {

            /*
            /* Your gateway's code to make sure the reponse you
            /* just got is from the gatway and not from some weirdo.
            /* This generally has some checksum or other checks,
            /* and is provided by the gateway.
            /* For now, we assume that the gateway's response is valid
            */

            $validated = true;
            $orderId = '123'; // Generally sent by gateway

            if ($validated) {
                // Payment was successful, so update the order's state, send order email and move to the success page
                $order = Mage::getModel('sales/order');
                $order->loadByIncrementId($orderId);
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');

                $order->sendNewOrderEmail();
                $order->setEmailSent(true);

                $order->save();

                Mage::getSingleton('checkout/session')->unsQuoteId();

                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', ['_secure' => true]);
            } else {
                // There is a problem in the response we got
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', ['_secure' => true]);
            }
        } else
            Mage_Core_Controller_Varien_Action::_redirect('');
    }

    // The cancel action is triggered when an order is to be cancelled
    public function cancelAction()
    {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if ($order->getId()) {
                // Flag the order as 'cancelled' and save it
                $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
            }
        }
    }
}