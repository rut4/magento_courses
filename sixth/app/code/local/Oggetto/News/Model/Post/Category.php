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
 * Post category model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Post_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('news/post_category');
    }

    /**
     * Save data for post - category relation
     *
     * @param Oggetto_News_Model_Post $post Post
     * @return Oggetto_News_Model_Post_Category
     */
    public function savePostRelation($post)
    {
        $data = $post->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->savePostRelation($post, $data);
        }
        return $this;
    }

    /**
     * Get categories for post
     *
     * @param Oggetto_News_Model_Post $post Post
     * @return Oggetto_News_Model_Resource_Post_Category_Collection
     */
    public function getCategoriesCollection($post)
    {
        $collection = Mage::getResourceModel('news/post_category_collection')
            ->addPostFilter($post);
        return $collection;
    }
}
