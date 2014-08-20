<?php

class Oggetto_Payment_Block_Standard extends Mage_Core_Block_Template
{
    protected $_fields = [
        'order_id',
        'total',
        'items',
        'success_url',
        'error_url',
        'payment_report_url'
    ];

    /**
     * @return Oggetto_Payment_Helper_Data
     */
    protected function _helper()
    {
        return $this->helper('oggettopayment');
    }

    protected function _fillFields()
    {
        return array_combine($this->_fields, $this->_getValues());
    }

    protected function _getValues()
    {
        return [
            $this->_helper()->getOrderId(),
            $this->_helper()->getTotal(),
            $this->_helper()->getItemsNames(),
            $this->_helper()->getSuccessUrl(),
            $this->_helper()->getErrorUrl(),
            $this->_helper()->getPaymentReportUrl()
        ];
    }

    public function getFields()
    {
        $filledFields = $this->_fillFields();
        $hash = $this->_getSignature($filledFields);
        $filledFields['hash'] = $hash;
        return $filledFields;
    }

    protected function _getSignature($filledFields)
    {
        ksort($filledFields);
        $sign = '';
        array_map(function ($field, $value) use (&$sign) {
            $sign .= "{$field}:{$value}|";
        }, array_keys($filledFields), array_values($filledFields));
        $sign .= $this->_helper()->getSecretKey();
        return md5($sign);
    }

    public function getGatewayUrl()
    {
        return $this->_helper()->getGatewayUrl();
    }
}