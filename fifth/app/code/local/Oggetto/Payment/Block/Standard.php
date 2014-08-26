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
 * Block standard payment
 *
 * @category   Oggetto
 * @package    Oggetto_Payment
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Payment_Block_Standard extends Mage_Core_Block_Template
{
    /**
     * Get this module helper
     *
     * @return Oggetto_Payment_Helper_Data
     */
    protected function _helper()
    {
        return $this->helper('oggettopayment');
    }

    /**
     * Get gateway form request fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->_helper()->getRequestFields();
    }

    /**
     * Get gateway url
     *
     * @return string
     */
    public function getGatewayUrl()
    {
        return $this->_helper()->getGatewayUrl();
    }
}