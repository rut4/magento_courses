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
 * Category - Post relation model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Resource_Category_Post extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialization resource model with main table and id field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('news/category_post', 'rel_id');
    }

    /**
     * Save category - post relations
     *
     * @param Oggetto_News_Model_Category $category Category
     * @param array                       $data     Data array
     * @return Oggetto_News_Model_Resource_Category_Post
     */
    public function saveCategoryRelation($category, $data)
    {
        if (!is_array($data)) {
            $data = [];
        }

        $adapter = $this->_getWriteAdapter();
        $bind = [
            ':category_id' => (int)$category->getId(),
        ];
        $select = $adapter->select()
            ->from($this->getMainTable(), ['rel_id', 'post_id'])
            ->where('category_id = :category_id');

        $related = $adapter->fetchPairs($select, $bind);
        $deleteIds = [];
        foreach ($related as $relId => $postId) {
            if (!isset($data[$postId])) {
                $deleteIds[] = (int)$relId;
            }
        }
        if (!empty($deleteIds)) {
            $adapter->delete($this->getMainTable(), [
                'rel_id IN (?)' => $deleteIds,
            ]);
        }

        foreach ($data as $postId => $info) {
            $adapter->insertOnDuplicate($this->getMainTable(), [
                'category_id' => $category->getId(),
                'post_id' => $postId,
                'position' => @$info['position']
            ], ['position']);
        }
        return $this;
    }
}
