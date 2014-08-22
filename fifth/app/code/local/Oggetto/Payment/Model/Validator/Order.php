<?php
class Oggetto_Payment_Model_Validator_Order implements Oggetto_Payment_Model_Validator_IValidator
{
    public function validate(Oggetto_Payment_Model_Report $report)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->getCollection()->getLastItem();

        return $report->getOrderId() == $order->getIncrementId() && $report->getTotal() == Mage::helper('oggettopayment')->formatPriceWithComma($order->getGrandTotal());
    }
}
