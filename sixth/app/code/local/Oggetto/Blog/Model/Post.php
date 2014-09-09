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
 * the Oggetto Blog module to newer versions in the future.
 * If you wish to customize the Oggetto Blog module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Blog post
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Blog_Model_Post extends Mage_Core_Model_Abstract
{
    /**
     * Initialization with resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('blog/post');
    }

    /**
     * Get category path
     *
     * @return string
     */
    public function getCategoryPath()
    {
        $path = '';
        $category = Mage::getModel('blog/category')->load($this->getCategoryId());

        do {
            $path = "{$category->getName()}/{$path}";
            $category->load($category->getParentId());
        } while ($category->getId() != $category->getParentCategoryId());

        return $path ;
    }
}
