<?php
/**
 * Oggetto Web shipping extension for Magento
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
 * the Oggetto Shipping module to newer versions in the future.
 * If you wish to customize the Oggetto Shipping module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipping helper
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @subpackage Helper
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Shipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get origin country id from config
     *
     * @return int
     */
    public function getOriginCountryId()
    {
        return Mage::getStoreConfig('shipping/origin/country_id');
    }

    /**
     * Get origin region id from config
     *
     * @return int
     */
    public function getOriginRegionId()
    {
        return Mage::getStoreConfig('shipping/origin/region_id');
    }

    /**
     * Get origin city from config
     *
     * @return string
     */
    public function getOriginCity()
    {
        return Mage::getStoreConfig('shipping/origin/city');
    }
}