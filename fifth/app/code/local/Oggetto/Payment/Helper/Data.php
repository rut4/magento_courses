<?php
class Oggetto_Payment_Helper_Data extends Mage_Core_Helper_Data
{
    /** @var Mage_Sales_Model_Order $_order */
    protected $_order;

    protected $_fields = [
        'order_id',
        'total',
        'items',
        'success_url',
        'error_url',
        'payment_report_url',
        'hash'
    ];

    public function __construct()
    {
        $this->_loadOrder();
    }

    protected function _getOrder()
    {
        if (is_null($this->_order)) {
            $this->_loadOrder();
        }
        return $this->_order;
    }

    protected function _fillFields()
    {
        return array_combine($this->_fields, $this->_getValues());
    }

    protected function _getValues()
    {
        return [
            $this->getOrderId(),
            $this->getTotal(),
            $this->getItemsNames(),
            $this->getSuccessUrl(),
            $this->getErrorUrl(),
            $this->getPaymentReportUrl(),
            $this->getSignature()
        ];
    }

    protected function _loadOrder()
    {
        $this->_order = Mage::getModel('sales/order');
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $this->_order->loadByIncrementId($orderId);
    }

    public function getGatewayUrl()
    {
        return Mage::getStoreConfig('payment/oggetto/gateway_url');
    }

    public function getFields()
    {
        return $this->_fillFields();
    }

    public function getOrderId()
    {
        return $this->_getOrder()->getId();
    }

    public function getTotal()
    {
        return $this->_getOrder()->getGrandTotal();
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
        return $this->_getUrl('payment/payment/report');
    }

    public function getSignature()
    {
        $filledFields = $this->_fillFields();
        ksort($filledFields);
        $sign = '';
        array_map(function ($field, $value) use (&$sign) {
            $sign .= "{$field}:{$value}|";
        }, array_keys($filledFields), array_values($filledFields));
        $sign .= $this->_getSecretKey();
        return md5($sign);
    }

    protected function _getSecretKey()
    {
        return 'ZnVjayB0aGUgZHVjaw==';
    }

}
