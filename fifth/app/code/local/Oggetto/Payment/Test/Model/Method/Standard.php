<?php
class Oggetto_Payment_Test_Model_Method_Standard extends EcomDev_PHPUnit_Test_Case
{
    public function getOrderPlaceRedirectUrl()
    {
        /** @var Oggetto_Payment_Model_Method_Standard $model */
        $model = Mage::getModel('oggettopayment/method_standard');
        $this->assertEquals(
            Mage::getUrl('payment/payment/redirect', ['_secure' => true]),
            $model->getOrderPlaceRedirectUrl()
        );
    }
}