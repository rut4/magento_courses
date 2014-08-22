<?php

class Oggetto_Payment_Block_Standard extends Mage_Core_Block_Template
{
    /**
     * @return Oggetto_Payment_Helper_Data
     */
    protected function _helper()
    {
        return $this->helper('oggettopayment');
    }

    public function getFields()
    {
        return $this->_helper()->getRequestFields();
    }

    public function getGatewayUrl()
    {
        return $this->_helper()->getGatewayUrl();
    }
}