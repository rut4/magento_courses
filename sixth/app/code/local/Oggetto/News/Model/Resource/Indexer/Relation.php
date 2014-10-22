<?php
/**
 * Oggetto Web yandex market extension for Magento
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
 * the Oggetto Yandex Market module to newer versions in the future.
 * If you wish to customize the Oggetto Yandex Market module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Price indexer resource
 *
 * @category   Oggetto
 * @package    Oggetto_YandexMarket
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Resource_Indexer_Relation extends Mage_Index_Model_Resource_Abstract
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('news/category_post_index', 'rel_id');
        $this->_setResource('news');
    }

    /**
     * Reindex post(s)
     *
     * @param int $postId     Post id
     * @param int $categoryId Category id
     * @return void
     */
    protected function _reindexEntity($postId = null, $categoryId = null)
    {
        $select = $this->_getWriteAdapter()->select();

        $select->from(['relation' => $this->getTable('news/category_post')], ['category_id']);

        if (!is_null($postId)) {
            $select->where('relation.post_id = ?', $postId);
        }

        if (!is_null($categoryId)) {
            $select->where('relation.category_id = ?', $categoryId);
        }

        $categoryIds = [];
        $currentCategories = $this->_getIndexAdapter()->fetchAll($select);
        do {
            $select = $this->_getWriteAdapter()->select();

            $select->from(['category' => $this->getTable('news/category')], ['parent_id'])
                ->where('entity_id IN(?)', $currentCategories)
                ->where('parent_id != 1');

            $fetched = $this->_getIndexAdapter()->fetchAll($select);
            $currentCategories = $fetched;
            $categoryIds = array_merge($categoryIds, $fetched);
        } while (count($fetched));

        $indexRelations = $this->_buildRelations($postId, $categoryIds);

        if (!empty($indexRelations)) {
            $this->_getIndexAdapter()->insertMultiple($this->getMainTable(), $indexRelations);
        }
    }

    /**
     * Reindex post(s)
     *
     * @param int $postId Post id
     * @return void
     */
    protected function _reindexPost($postId = null)
    {
        $this->_removePostRelations($postId);
        $this->_reindexEntity($postId);
    }

    /**
     * Reindex post(s)
     *
     * @param int $categoryId Category id
     * @return void
     */
    protected function _reindexCategory($categoryId)
    {
        $this->_removeCategoryRelations($categoryId);
        $this->_reindexEntity(null, $categoryId);
    }

    /**
     * Remove post relations by post id
     *
     * @param int $postId Post id
     * @return void
     */
    protected function _removePostRelations($postId)
    {
        if (is_null($postId)) {
            $this->_getIndexAdapter()->delete($this->getMainTable());
            return;
        }

        $this->_getIndexAdapter()->delete(
            $this->getMainTable(),
            ['post_id = ?' => $postId]
        );
    }

    /**
     * Remove category relations by post id
     *
     * @param int $categoryId Category id
     * @return void
     */
    protected function _removeCategoryRelations($categoryId)
    {
        if (is_null($categoryId)) {
            $this->_getIndexAdapter()->delete($this->getMainTable());
            return;
        }

        $this->_getIndexAdapter()->delete(
            $this->getMainTable(),
            ['category_id = ?' => $categoryId]
        );
    }

    /**
     * Create relations between posts and categories
     *
     * @param array $postIds     Post ids
     * @param array $categoryIds Category ids
     * @return array
     */
    protected function _buildRelations($postIds, $categoryIds)
    {
        $select = $this->_getWriteAdapter()->select();

        $select->from(['post' => $this->getTable('news/post')], ['post_id' => 'post.entity_id']);
        if (!is_null($postIds)) {
            $select->where('post.entity_id IN(?)', is_array($postIds) ? $postIds : [$postIds]);
        }
        $select->joinCross(
            ['category' => $this->getTable('news/category')],
            ['category_id' => 'category.entity_id']
        );
        if (!is_null($categoryIds)) {
            $select->where('category.entity_id IN(?)', is_array($categoryIds) ? $categoryIds : [$categoryIds]);
        }
        $select->columns(['url_path' => "CONCAT(category.url_path, '/', post.url_key)"]);

        return $this->_getIndexAdapter()->fetchAll($select);
    }

    /**
     * Reindex on posts mass action
     *
     * @param Mage_Index_Model_Event $event Index event
     * @return void
     */
    public function reindexAll($event)
    {
        $this->_reindexPost();
    }

    /**
     * Reindex on post save
     *
     * @param Mage_Index_Model_Event $event Index event
     * @return void
     */
    public function newsPostSave($event)
    {
        $this->_reindexPost($event->getData('post_id'));
    }

    /**
     * Reindex on category save
     *
     * @param Mage_Index_Model_Event $event Index event
     * @return void
     */
    public function newsCategorySave($event)
    {
        $this->_reindexCategory($event->getData('category_id'));
    }
}
