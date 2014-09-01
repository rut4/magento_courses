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
 * Test report model
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Payment_Test_Model_Report extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test report inits itself from request
     *
     * @dataProvider dataProvider
     * @param int       $status     Status
     * @param int       $total      Total
     * @param int       $orderId    Order id
     * @param string    $hash       Hash
     * @return void
     */
    public function testInitializationsItselfFromHttpRequest($status, $total, $orderId, $hash)
    {
        $requestMock = $this->getModelMock('core/abstract', ['getPost']);

        $requestMock->expects($this->at(0))
            ->method('getPost')
            ->with($this->equalTo('status'))
            ->will($this->returnValue($status));

        $requestMock->expects($this->at(1))
            ->method('getPost')
            ->with($this->equalTo('total'))
            ->will($this->returnValue($total));

        $requestMock->expects($this->at(2))
            ->method('getPost')
            ->with($this->equalTo('order_id'))
            ->will($this->returnValue($orderId));

        $requestMock->expects($this->at(3))
            ->method('getPost')
            ->with($this->equalTo('hash'))
            ->will($this->returnValue($hash));

        $reportMock = $this->getModelMock('oggettopayment/report', ['setStatus', 'setTotal', 'setOrderId', 'setHash']);

        $reportMock->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo($status));

        $reportMock->expects($this->once())
            ->method('setTotal')
            ->with($this->equalTo($total));

        $reportMock->expects($this->once())
            ->method('setOrderId')
            ->with($this->equalTo($orderId));

        $reportMock->expects($this->once())
            ->method('setHash')
            ->with($this->equalTo($hash));

        $reportMock->init($requestMock);


    }
}
