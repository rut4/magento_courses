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
 * Report hash validator class
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Payment_Test_Model_Validator_Hash extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test hash validator checks report hash with success
     *
     * @dataProvider dataProvider
     * @param int       $total      Total
     * @param int       $status     Status
     * @param int       $orderId    Order id
     * @param string    $hash       Hash
     * @return void
     */
    public function testValidatesReportHashWithSuccess($total, $status, $orderId, $hash)
    {
        $reportMock = $this->_prepareReportMock($total, $status, $orderId, $hash);

        $helperMock = $this->_prepareHelperMock($total, $status, $orderId, $hash);
        $this->replaceByMock('helper', 'oggettopayment', $helperMock);

        $this->assertTrue(Mage::getModel('oggettopayment/validator_hash')->validate($reportMock));
    }

    /**
     * Test hash validator checks report hash with fail
     *
     * @dataProvider dataProvider
     * @param int       $total          Total
     * @param int       $status         Status
     * @param int       $orderId        Order id
     * @param string    $hashInReport   Hash in report
     * @param string    $hashFromFields Hash from fields
     * @return void
     */
    public function testValidatesReportHashWithFail($total, $status, $orderId, $hashInReport, $hashFromFields)
    {
        $reportMock = $this->_prepareReportMock($total, $status, $orderId, $hashInReport);

        $helperMock = $this->_prepareHelperMock($total, $status, $orderId, $hashFromFields);
        $this->replaceByMock('helper', 'oggettopayment', $helperMock);

        $this->assertFalse(Mage::getModel('oggettopayment/validator_hash')->validate($reportMock));
    }

    /**
     * Prepare report mock
     *
     * @param int       $total      Total
     * @param int       $status     Status
     * @param int       $orderId    Order id
     * @param string    $hash       Hash
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareReportMock($total, $status, $orderId, $hash)
    {
        $reportMock = $this->getModelMock('oggettopayment/report', ['getTotal', 'getStatus', 'getOrderId', 'getHash']);

        $reportMock->expects($this->once())
            ->method('getTotal')
            ->will($this->returnValue($total));

        $reportMock->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue($status));

        $reportMock->expects($this->once())
            ->method('getOrderId')
            ->will($this->returnValue($orderId));

        $reportMock->expects($this->once())
            ->method('getHash')
            ->will($this->returnValue($hash));

        return $reportMock;
    }

    /**
     * Prepare order mock
     *
     * @param int       $total          Total
     * @param int       $status         Status
     * @param int       $orderId        Order id
     * @param string    $hashFromFields Hash
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareHelperMock($total, $status, $orderId, $hashFromFields)
    {
        $helperMock = $this->getHelperMock('oggettopayment/data', ['getSignature']);

        $helperMock->expects($this->once())
            ->method('getSignature')
            ->with([
                'total' => $total,
                'status' => $status,
                'order_id' => $orderId
            ])
            ->will($this->returnValue($hashFromFields));
        return $helperMock;
    }
}
