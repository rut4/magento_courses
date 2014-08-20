<?php
class Oggetto_Payment_Helper_Data extends Mage_Core_Helper_Data
{
    /** @var Mage_Sales_Model_Order $_order */
    protected $_order;

    /**
     * Get order
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        return $order;
    }

    public function getOrderId()
    {
        return $this->_getOrder()->getIncrementId();
    }

    public function getTotal()
    {
        return str_replace('.', ',', $this->_getOrder()->getGrandTotal());
    }

    public function getItemsNames()
    {
        $items = $this->_getOrder()->getAllItems();

        $itemsNames = array_map(function ($item) {
            /** @var Mage_Sales_Model_Order_Item $item */
            return $item->getName();
        }, $items);

        return implode(',', $itemsNames);
    }

    public function getSuccessUrl()
    {
        return $this->_getUrl('checkout/onepage/success');
    }

    public function getErrorUrl()
    {
        return $this->_getUrl('checkout/onepage/error');
    }

    public function getPaymentReportUrl()
    {
        return $this->_getUrl('oggetto/payment/report');
    }

    public function getGatewayUrl()
    {
        return Mage::getStoreConfig('payment/oggetto/gateway_url');
    }

    public function getSecretKey()
    {
        return 'ZnVjayB0aGUgZHVjaw==';
    }
}
