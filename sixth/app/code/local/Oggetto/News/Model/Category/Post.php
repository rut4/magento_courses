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
 * Category post model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Category_Post extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('news/category_post');
    }

    /**
     * Save data for category - post relation
     *
     * @param Oggetto_News_Model_Category $category Category
     * @return Oggetto_News_Model_Category_Post
     */
    public function saveCategoryRelation($category)
    {
        $data = $category->getPostsData();
        if (!is_null($data)) {
            $this->_getResource()->saveCategoryRelation($category, $data);
        }
        return $this;
    }

    /**
     * Get posts for category
     *
     * @param Oggetto_News_Model_Category $category Category
     * @return Oggetto_News_Model_Resource_Category_Post_Collection
     */
    public function getPostsCollection($category)
    {
        return Mage::getResourceModel('news/category_post_collection')
            ->addCategoryFilter($category);
    }
}
