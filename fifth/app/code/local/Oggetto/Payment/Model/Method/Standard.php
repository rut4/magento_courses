<?php
class Oggetto_Payment_Model_Method_Standard extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Unique internal payment method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = 'oggetto';

    protected $_canCapture              = true;
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = true;
    protected $_canUseForMultishipping  = false;

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('oggetto/payment/redirect', ['_secure' => true]);
    }

}
