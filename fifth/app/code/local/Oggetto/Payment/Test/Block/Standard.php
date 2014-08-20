<?php
class Oggetto_Payment_Test_Block_Standard extends EcomDev_PHPUnit_Test_Case_Controller
{
    public function testGetsPaymentFormFields()
    {
        $helper = $this->getHelperMock('oggettopayment/data');

        $helper->expects($this->any())
            ->method('getOrderId')
            ->will($this->returnValue(42));

        $helper->expects($this->any())
            ->method('getTotal')
            ->will($this->returnValue(12345678910));

        $helper->expects($this->any())
            ->method('getItemsNames')
            ->will($this->returnValue('foo(1),bar(3),baz(9)'));

        $helper->expects($this->any())
            ->method('getSuccessUrl')
            ->will($this->returnValue('http://success.url'));

        $helper->expects($this->any())
            ->method('getErrorUrl')
            ->will($this->returnValue('http://error.url'));

        $helper->expects($this->any())
            ->method('getPaymentReportUrl')
            ->will($this->returnValue('http://report.url'));

        $helper->expects($this->any())
            ->method('getSecretKey')
            ->will($this->returnValue('/~/!:<<?'));

        $block = $this->getBlockMock('oggettopayment/standard', ['helper']);

        $block->expects($this->any())
            ->method('helper')
            ->with($this->equalTo('oggettopayment'))
            ->will($this->returnValue($helper));

        $expected = [
            'order_id'              => 42,
            'total'                 => 12345678910,
            'items'                 => 'foo(1),bar(3),baz(9)',
            'success_url'           => 'http://success.url',
            'error_url'             => 'http://error.url',
            'payment_report_url'    => 'http://report.url',
            'hash'                  => md5('error_url:http://error.url|items:foo(1),bar(3),baz(9)|order_id:42|payment_report_url:http://report.url'
                                        . '|success_url:http://success.url|total:12345678910|/~/!:<<?')
        ];

        $this->assertEquals(
            $expected,
            $block->getFields()
        );
    }

    public function testGetsGatewayUrl()
    {
        $helper = $this->getHelperMock('oggettopayment/data');

        $helper->expects($this->once())
            ->method('getGatewayUrl')
            ->will($this->returnValue('http://gateway.url'));

        $block = $this->getBlockMock('oggettopayment/standard', ['helper']);

        $block->expects($this->any())
            ->method('helper')
            ->with($this->equalTo('oggettopayment'))
            ->will($this->returnValue($helper));

        $this->assertEquals(
            'http://gateway.url',
            $block->getGatewayUrl()
        );
    }
}