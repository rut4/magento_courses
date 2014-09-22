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
 * Post helper
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Helper
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Helper_Post extends Mage_Core_Helper_Abstract
{
    /**
     * Get the url to the posts list page
     *
     * @return string
     */
    public function getPostsUrl()
    {
        if ($listKey = Mage::getStoreConfig('news/post/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct' => $listKey));
        }
        return Mage::getUrl('news/post/index');
    }

    /**
     * Get url rewrite for list
     *
     * @return string|null
     */
    public function getUrlRewriteForList()
    {
        return Mage::getStoreConfig('news/post/url_rewrite_list');
    }

    /**
     * Check if breadcrumbs can be used
     *
     * @return bool
     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('news/post/breadcrumbs');
    }

    /**
     * Get prefix for post
     *
     * @return string|null
     */
    public function getPrefix()
    {
        if ($prefix = Mage::getStoreConfig('news/post/prefix')) {
            return $prefix . '/';
        }
    }

    /**
     * Get suffix for post
     *
     * @return string|null
     */
    public function getSuffix()
    {
        if ($suffix = Mage::getStoreConfig('news/post/suffix')) {
            return '.' . $suffix;
        }
    }
}
