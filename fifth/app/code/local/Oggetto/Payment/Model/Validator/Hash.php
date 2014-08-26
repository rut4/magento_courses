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
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Payment_Model_Validator_Hash implements Oggetto_Payment_Model_Validator_IValidator
{
    /**
     * Validate hash report
     *
     * @param Oggetto_Payment_Model_Report $report
     * @return bool
     */
    public function validate(Oggetto_Payment_Model_Report $report)
    {
        $fields = [
            'total'     => $report->getTotal(),
            'status'    => $report->getStatus(),
            'order_id'  => $report->getOrderId()
        ];

        return $report->getHash() == Mage::helper('oggettopayment')->getSignature($fields);
    }
}