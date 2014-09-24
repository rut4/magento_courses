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
        $oldCategoryIds = $this->_getOldCategoryIds($post);
        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);
        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $this->_insertRelations($post, $insert, $write);
        }
        if (!empty($delete)) {
            $this->_removeRelations($post, $delete, $write);
        }
        $post->getResource()->updateUrlPath($post);
        return $this;
    }

    /**
     * Get old category ids
     *
     * @param Oggetto_News_Model_Post $post Post model
     * @return array
     */
    protected function _getOldCategoryIds($post)
    {
        $oldCategories = $post->getSelectedCategories();
        $oldCategoryIds = [];
        foreach ($oldCategories as $category) {
            $oldCategoryIds[] = $category->getId();
        }
        return $oldCategoryIds;
    }

    /**
     * Insert relations
     *
     * @param Oggetto_News_Model_Post     $post   Post model
     * @param array                       $insert Category relations to insert
     * @param Varien_Db_Adapter_Interface $write  Write adapter
     * @return void
     */
    protected function _insertRelations($post, $insert, $write)
    {
        $data = [];
        foreach ($insert as $categoryId) {
            if (empty($categoryId)) {
                continue;
            }
            $data[] = [
                'category_id' => (int)$categoryId,
                'post_id'     => (int)$post->getId(),
                'position'    => 1
            ];
        }
        if ($data) {
            $write->insertMultiple($this->getMainTable(), $data);
        }
    }

    /**
     * Remove relations
     *
     * @param Oggetto_News_Model_Post     $post   Post model
     * @param array                       $delete Category relations to remove
     * @param Varien_Db_Adapter_Interface $write  Write adapter
     * @return void
     */
    protected function _removeRelations($post, $delete, $write)
    {
        foreach ($delete as $categoryId) {
            $where = [
                'post_id = ?'     => (int)$post->getId(),
                'category_id = ?' => (int)$categoryId,
            ];
            $write->delete($this->getMainTable(), $where);
        }
    }
}
