<?php

class Oggetto_Payment_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    public function setUp()
    {
        parent::setUp();
        $sessionMock = $this->getModelMock('checkout/session', ['getLastRealOrderId', 'start']);

        $sessionMock->expects($this->at(0))
            ->method('getLastRealOrderId')
            ->will($this->returnValue(10000042));

        $sessionMock->expects($this->at(1))
            ->method('getLastRealOrderId')
            ->will($this->returnValue(10000321));

        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);

        $this->_sessionIsMocked = true;
    }

    /**
     * @loadFixture
     */
    public function testGetsOrderId()
    {
        /** @var Oggetto_Payment_Helper_Data $helper */
        $helper = Mage::helper('oggettopayment');
        $this->assertEquals(10000042, $helper->getOrderId());

        $helper = Mage::helper('oggettopayment');
        $this->assertEquals(10000321, $helper->getOrderId());
    }

    /**
     * @loadFixture
     */
    public function testGetsTotal()
    {
        /** @var Oggetto_Payment_Helper_Data $helper */
        $helper = Mage::helper('oggettopayment');
        $this->assertEquals('2239,1900', $helper->getTotal());

        $helper = Mage::helper('oggettopayment');
        $this->assertEquals('22,0000', $helper->getTotal());
    }

    /**
     * @loadFixture
     */
    public function testGetsItemsNames()
    {
        /** @var Oggetto_Payment_Helper_Data $helper */
        $helper = Mage::helper('oggettopayment');
        $this->assertEquals('Foo(1),Bar(3),Baz(5)', $helper->getItemsNames());

        $helper = Mage::helper('oggettopayment');
        $this->assertEquals('Qux(10)', $helper->getItemsNames());
    }

//    public function testGetSuccessUrl()
//    {
//        /** @var Oggetto_Payment_Helper_Data $helper */
//        $helper = Mage::helper('oggettopayment');
//        $this->assertEquals(Mage::getUrl('checkout/onepage/success'), $helper->getSuccessUrl());
//    }
}