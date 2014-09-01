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
 * Data helper test class
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Payment_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Replace sessions by mock
     *
     * @param int $orderId Order id
     * @return void
     */
    protected function _replaceSessionByMock($orderId)
    {
        $sessionMock = $this->getModelMock('checkout/session', ['getLastRealOrderId', 'start']);

        $sessionMock->expects($this->any())
            ->method('getLastRealOrderId')
            ->will($this->returnValue($orderId));

        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);
    }

    /**
     * Test helper returns gateway url
     *
     * @return void
     */
    public function testReturnsGatewayUrl()
    {
        $this->assertEquals(
            Mage::getStoreConfig('payment/oggetto/gateway_url'),
            Mage::helper('oggettopayment')->getGatewayUrl()
        );
    }

    /**
     * Test helper formats price with comma
     *
     * @dataProvider dataProvider
     * @param string $price Price
     * @param string $expected Expected formatted price
     * @return void
     */
    public function testFormatsPriceWithComma($price, $expected)
    {
        $this->assertEquals(
            $expected,
            Mage::helper('oggettopayment')->formatPriceWithComma($price)
        );
    }

    /**
     * Test data helper returns request fields
     *
     * @dataProvider dataProvider
     * @param int   $orderId    Order id
     * @param int   $grandTotal Grand total
     * @param array $itemNames  Item names
     * @return void
     */
    public function testReturnsRequestFields($orderId, $grandTotal, $itemNames)
    {
        $this->_replaceSessionByMock($orderId);

        $orderMock = $this->getModelMock('sales/order', [
            'loadByIncrementId',
            'getIncrementId',
            'getGrandTotal',
            'getAllItems'
        ]);

        $orderItems = [];

        foreach ($itemNames as $name) {
            $itemMock = $this->getModelMock('sales/order_item', ['getName']);

            $itemMock->expects($this->once())
                ->method('getName')
                ->will($this->returnValue($name));

            $orderItems[] = $itemMock;
        }

        $orderMock->expects($this->any())
            ->method('loadByIncrementId')
            ->with($this->equalTo($orderId))
            ->will($this->returnSelf());

        $orderMock->expects($this->once())
            ->method('getIncrementId')
            ->will($this->returnValue($orderId));

        $orderMock->expects($this->once())
            ->method('getGrandTotal')
            ->will($this->returnValue($grandTotal));

        $orderMock->expects($this->once())
            ->method('getAllItems')
            ->will($this->returnValue($orderItems));

        $this->replaceByMock('model', 'sales/order', $orderMock);

        $expected = [
            'order_id'              => $orderId,
            'total'                 => number_format($grandTotal, 4, ',', ''),
            'items'                 => implode(',', $itemNames),
            'success_url'           => Mage::getUrl('checkout/onepage/success'),
            'error_url'             => Mage::getUrl('checkout/onepage/error'),
            'payment_report_url'    => Mage::getUrl('oggetto/payment/report')
        ];

        ksort($expected);

        $sign = '';
        array_map(function ($key, $value) use (&$sign) {
            $sign .= "{$key}:{$value}|";
        }, array_keys($expected), array_values($expected));
        $sign .= Mage::getStoreConfig('payment/oggetto/secret_key');

        $expected['hash'] = md5($sign);

        $this->assertEquals($expected, Mage::helper('oggettopayment')->getRequestFields());

    }
}