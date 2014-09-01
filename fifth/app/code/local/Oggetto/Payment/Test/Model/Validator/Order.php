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
 * Report order validator class
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Payment_Test_Model_Validator_Order extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test order validator checks report order with success
     *
     * @dataProvider dataProvider
     * @param int $total    Total
     * @param int $orderId  Order id
     * @return void
     */
    public function testValidatesReportOrderWithSuccess($total, $orderId)
    {
        $reportMock = $this->_prepareReportMock($total, $orderId);

        $helperMock = $this->_prepareHelperMock($total);
        $this->replaceByMock('helper', 'oggettopayment', $helperMock);

        $orderMock = $this->_prepareOrderMock($total, $orderId);
        $this->replaceByMock('model', 'sales/order', $orderMock);

        $this->assertTrue(Mage::getModel('oggettopayment/validator_order')->validate($reportMock));
    }

    /**
     * Test order validator checks report order with fail
     *
     * @dataProvider dataProvider
     * @param int $reportTotal      Total in report
     * @param int $reportOrderId    Order id in report
     * @param int $total            Total
     * @param int $orderId          Order id
     * @return void
     */
    public function testValidatesReportOrderWithFail($reportTotal, $reportOrderId, $total, $orderId)
    {
        $reportMock = $this->_prepareReportMock($reportTotal, $reportOrderId);

        $helperMock = $this->_prepareHelperMock($total);
        $this->replaceByMock('helper', 'oggettopayment', $helperMock);

        $orderMock = $this->_prepareOrderMock($total, $orderId);
        $this->replaceByMock('model', 'sales/order', $orderMock);

        $this->assertFalse(Mage::getModel('oggettopayment/validator_order')->validate($reportMock));
    }

    /**
     * Prepare report mock
     *
     * @param int       $total      Total
     * @param int       $orderId    Order id
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareReportMock($total, $orderId)
    {
        $reportMock = $this->getModelMock('oggettopayment/report', ['getTotal', 'getOrderId']);

        $reportMock->expects($this->any())
            ->method('getTotal')
            ->will($this->returnValue($total));

        $reportMock->expects($this->once())
            ->method('getOrderId')
            ->will($this->returnValue($orderId));

        return $reportMock;
    }

    /**
     * Prepare helper mock
     *
     * @param int $total Total
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareHelperMock($total)
    {
        $helperMock = $this->getHelperMock('oggettopayment/data', ['formatPriceWithComma']);

        $helperMock->expects($this->any())
            ->method('formatPriceWithComma')
            ->with($this->equalTo($total))
            ->will($this->returnValue(number_format($total, 4, ',', '')));

        return $helperMock;
    }

    /**
     * Prepare order mock
     *
     * @param int $total    Total
     * @param int $orderId  Order id
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareOrderMock($total, $orderId)
    {
        $orderMock = $this->getModelMock('sales/order',
            ['getCollection', 'getLastItem', 'getIncrementId', 'getGrandTotal']);

        $orderMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnSelf());

        $orderMock->expects($this->once())
            ->method('getLastItem')
            ->will($this->returnSelf());

        $orderMock->expects($this->once())
            ->method('getIncrementId')
            ->will($this->returnValue($orderId));

        $orderMock->expects($this->any())
            ->method('getGrandTotal')
            ->will($this->returnValue($total));

        return $orderMock;
    }
}
