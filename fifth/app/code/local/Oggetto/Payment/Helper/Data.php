<?php
class Oggetto_Payment_Helper_Data extends Mage_Core_Helper_Data
{
    /** @var Mage_Sales_Model_Order $_order */
    protected $_order;

    protected $_requestFields = [
        'order_id',
        'total',
        'items',
        'success_url',
        'error_url',
        'payment_report_url'
    ];

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

    public function getSignature($filledFields)
    {
        ksort($filledFields);
        $sign = '';
        array_map(function ($field, $value) use (&$sign) {
            $sign .= "{$field}:{$value}|";
        }, array_keys($filledFields), array_values($filledFields));
        $sign .= $this->getSecretKey();
        return md5($sign);
    }

    protected function _getValues()
    {
        return [
            $this->getOrderId(),
            $this->getTotal(),
            $this->getItemsNames(),
            $this->getSuccessUrl(),
            $this->getErrorUrl(),
            $this->getPaymentReportUrl()
        ];
    }

    public function getRequestFields()
    {
        $filledFields = array_combine($this->_requestFields, $this->_getValues());
        $hash = $this->getSignature($filledFields);
        $filledFields['hash'] = $hash;
        return $filledFields;
    }

    public function formatPriceWithComma($price)
    {
        return number_format($price, 4, ',', '');
    }

}
