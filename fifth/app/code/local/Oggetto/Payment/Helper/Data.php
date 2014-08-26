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
 * Data helper class
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @subpackage Helper
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
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

    /**
     * Get order ID
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->_getOrder()->getIncrementId();
    }

    /**
     * Get order grand total
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->_getOrder()->getGrandTotal();
    }

    /**
     * Get order item
     *
     * @return string
     */
    public function getItemsNames()
    {
        $items = $this->_getOrder()->getAllItems();

        $itemsNames = array_map(function ($item) {
            /** @var Mage_Sales_Model_Order_Item $item */
            return $item->getName();
        }, $items);

        return implode(',', $itemsNames);
    }

    /**
     * Get success url
     *
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->_getUrl('checkout/onepage/success');
    }

    /**
     * Get error url
     *
     * @return string
     */
    public function getErrorUrl()
    {
        return $this->_getUrl('checkout/onepage/error');
    }

    /**
     * Get payment url
     *
     * @return string
     */
    public function getPaymentReportUrl()
    {
        return $this->_getUrl('oggetto/payment/report');
    }

    /**
     * Get gateway
     *
     * @return string
     */
    public function getGatewayUrl()
    {
        return Mage::getStoreConfig('payment/oggetto/gateway_url');
    }

    /**
     * Get store secret key
     *
     * @return string
     */
    public function getSecretKey()
    {
        return Mage::getStoreConfig('payment/oggetto/secret_key');
    }

    /**
     * Get hash by array
     *
     * @param array $filledFields Fields to make hash
     * @return string
     */
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

    /**
     * Get values to send request
     *
     * @return array
     */
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

    /**
     * Get request array
     *
     * @return array
     */
    public function getRequestFields()
    {
        $filledFields = array_combine($this->_requestFields, $this->_getValues());
        $hash = $this->getSignature($filledFields);
        $filledFields['hash'] = $hash;
        return $filledFields;
    }

    /**
     * Get formatted price
     *
     * @param string $price Price
     * @return string
     */
    public function formatPriceWithComma($price)
    {
        return number_format($price, 4, ',', '');
    }

}
