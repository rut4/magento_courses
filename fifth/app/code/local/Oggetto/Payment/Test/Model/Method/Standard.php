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
 * Standard payment method model test class
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Payment_Test_Model_Method_Standard extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * Standard payment method model returns order place redirect url
     *
     * @return void
     */
    public function testReturnsOrderPlaceRedirectUrl()
    {
        /** @var Oggetto_Payment_Model_Method_Standard $model */
        $model = Mage::getModel('oggettopayment/method_standard');
        $this->assertEquals(
            Mage::getUrl('oggetto/payment/redirect', ['_secure' => true]),
            $model->getOrderPlaceRedirectUrl()
        );
    }
}