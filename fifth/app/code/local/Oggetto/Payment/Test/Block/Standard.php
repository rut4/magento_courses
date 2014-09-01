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
 * Standard payment method block test class
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Payment_Test_Block_Standard extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * Test block returns payment form fields
     *
     * @return void
     */
    public function testReturnsPaymentFormFields()
    {
        $expected = [
            'order_id'              => 42,
            'total'                 => 12345678910,
            'items'                 => 'foo,bar,baz',
            'success_url'           => 'http://success.url',
            'error_url'             => 'http://error.url',
            'payment_report_url'    => 'http://report.url',
            'hash'                  => md5('error_url:http://error.url|items:foo(1),bar(3),baz(9)|order_id:42'
                . '|payment_report_url:http://report.url|success_url:http://success.url|total:12345678910|/~/!:<<?')
        ];

        $helper = $this->getHelperMock('oggettopayment/data', ['getRequestFields']);

        $helper->expects($this->any())
            ->method('getRequestFields')
            ->will($this->returnValue($expected));

        $block = $this->getBlockMock('oggettopayment/standard', ['helper']);

        $block->expects($this->any())
            ->method('helper')
            ->with($this->equalTo('oggettopayment'))
            ->will($this->returnValue($helper));

        $this->assertEquals(
            $expected,
            $block->getFields()
        );
    }

    /**
     * Test block returns gatewat url
     *
     * @return void
     */
    public function testReturnsGatewayUrl()
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