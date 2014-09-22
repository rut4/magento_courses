<?php
/**
 * Oggetto Web extension for Magento
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
 * the Oggetto News module to newer versions in the future.
 * If you wish to customize the Oggetto News module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category helper
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Helper
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Helper_Category extends Mage_Core_Helper_Abstract
{
    const CATEGORY_ROOT_ID = 1;

    /**
     * Get the root ID
     *
     * @return int
     */
    public function getRootCategoryId()
    {
        return self::CATEGORY_ROOT_ID;
    }

    /**
     * Get the url to the categories list page
     *
     * @return string
     */
    public function getCategoriesUrl()
    {
        if ($listKey = $this->getUrlRewriteForList()) {
            return Mage::getModel('core/url')->getDirectUrl($listKey);
        }
        return Mage::getUrl('news/category/index');
    }

    /**
     * Get url rewrite for list
     *
     * @return string|null
     */
    public function getUrlRewriteForList()
    {
         return Mage::getStoreConfig('news/category/url_rewrite_list');
    }

    /**
     * Check if breadcrumbs can be used
     *
     * @return bool
     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('news/category/breadcrumbs');
    }

    /**
     * Get prefix for category
     *
     * @return string|null
     */
    public function getPrefix()
    {
        if ($prefix = Mage::getStoreConfig('news/category/prefix')) {
            return $prefix . '/';
        }
    }

    /**
     * Get suffix for category
     *
     * @return string|null
     */
    public function getSuffix()
    {
        if ($suffix = Mage::getStoreConfig('news/category/suffix')) {
            return '.' . $suffix;
        }
    }
}
