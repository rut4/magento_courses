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
 * Post - Category relation model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Resource_Post_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialization with main table and id field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('news/post_category', 'rel_id');
    }

    /**
     * Save post - category relations
     *
     * @param Oggetto_News_Model_Post $post        Post
     * @param array                   $categoryIds Category ids
     * @return Oggetto_News_Model_Resource_Post_Category
     */
    public function savePostRelation($post, $categoryIds)
    {
        if (is_null($categoryIds)) {
            return $this;
        }
        $oldCategories = $post->getSelectedCategories();
        $oldCategoryIds = array();
        foreach ($oldCategories as $category) {
            $oldCategoryIds[] = $category->getId();
        }
        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);
        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }
                $data[] = array(
                    'category_id' => (int)$categoryId,
                    'post_id' => (int)$post->getId(),
                    'position' => 1
                );
            }
            if ($data) {
                $write->insertMultiple($this->getMainTable(), $data);
            }
        }
        if (!empty($delete)) {
            foreach ($delete as $categoryId) {
                $where = array(
                    'post_id = ?' => (int)$post->getId(),
                    'category_id = ?' => (int)$categoryId,
                );
                $write->delete($this->getMainTable(), $where);
            }
        }
        $post->getResource()->updateUrlPath($post);
        return $this;
    }
}
