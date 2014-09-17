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
 * Category resource model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Resource_Category extends Oggetto_News_Model_Resource_Entity
{
    /**
     * Category tree object
     * @var Varien_Data_Tree_Db
     */
    protected $_tree;

    /**
     * Initialization with main table and id field name
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('news/category', 'entity_id');
    }

    /**
     * Retrieve category tree object
     *
     * @return Varien_Data_Tree_Db
     */
    protected function _getTree()
    {
        if (!$this->_tree) {
            $this->_tree = Mage::getResourceModel('news/category_tree')->load();
        }
        return $this->_tree;
    }

    /**
     * Process category data before delete
     * update children count for parent category
     * delete child categories
     *
     * @param Mage_Core_Model_Abstract $object Category
     * @return Oggetto_News_Model_Resource_Category
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeDelete($object);

        $parentIds = $object->getParentIds();
        if ($parentIds) {
            $childDecrease = $object->getChildrenCount() + 1; // +1 is itself
            $data = array('children_count' => new Zend_Db_Expr('children_count - ' . $childDecrease));
            $where = array('entity_id IN(?)' => $parentIds);
            $this->_getWriteAdapter()->update($this->getMainTable(), $data, $where);
        }
        $this->deleteChildren($object);
        return $this;
    }

    /**
     * Delete children categories of specific category
     *
     * @param Varien_Object $object Category
     * @return Oggetto_News_Model_Resource_Category
     */
    public function deleteChildren(Varien_Object $object)
    {
        $adapter = $this->_getWriteAdapter();
        $pathField = $adapter->quoteIdentifier('path');
        $select = $adapter->select()
            ->from($this->getMainTable(), array('entity_id'))
            ->where($pathField . ' LIKE :c_path');
        $childrenIds = $adapter->fetchCol($select, array('c_path' => $object->getPath() . '/%'));
        if (!empty($childrenIds)) {
            $adapter->delete(
                $this->getMainTable(),
                array('entity_id IN (?)' => $childrenIds)
            );
        }
        /**
         * Add deleted children ids to object
         * This data can be used in after delete event
         */
        $object->setDeletedChildrenIds($childrenIds);
        return $this;
    }

    /**
     * Process category data after save category object
     *
     * @param Mage_Core_Model_Abstract $object Category
     * @return Oggetto_News_Model_Resource_Category
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (substr($object->getPath(), -1) == '/') {
            $object->setPath($object->getPath() . $object->getId());
            $this->_savePath($object);
        }

        if (!$object->getInitialSetupFlag() && !$object->getUrlPath()) {
            $this->_saveUrlPath($object);
        }
        return parent::_afterSave($object);
    }

    /**
     * Save url path
     *
     * @param Mage_Core_Model_Abstract $object The category which has been saved
     * @return void
     */
    protected function _saveUrlPath(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $urlKeys = [];
            $idsPath = $this->getCategoryPathById($object->getId());
            foreach (explode('/', $idsPath) as $id) {
                $urlKeys[] = Mage::getModel('news/category')->load($id)->getUrlKey();
            }
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                ['url_path' => trim(implode('/', $urlKeys), '/')],
                ['entity_id = ?' => $object->getId()]
            );
        }
    }

    /**
     * Update url path after move
     *
     * @param Oggetto_News_Model_Category $category Category
     * @return void
     */
    public function updateUrlPath($category)
    {
        $this->_saveUrlPath($category);
    }

    /**
     * Update path field
     *
     * @param Oggetto_News_Model_Category $object Category
     * @return Oggetto_News_Model_Resource_Category
     */
    protected function _savePath($object)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('path' => $object->getPath()),
                array('entity_id = ?' => $object->getId())
            );
        }
        return $this;
    }

    /**
     * Get maximum position of child categories by specific tree path
     *
     * @param string $path Category path
     * @return int
     */
    protected function _getMaxPosition($path)
    {
        $adapter = $this->getReadConnection();
        $positionField = $adapter->quoteIdentifier('position');
        $level = count(explode('/', $path));
        $bind = array(
            'c_level' => $level,
            'c_path' => $path . '/%'
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), 'MAX(' . $positionField . ')')
            ->where($adapter->quoteIdentifier('path') . ' LIKE :c_path')
            ->where($adapter->quoteIdentifier('level') . ' = :c_level');

        $position = $adapter->fetchOne($select, $bind);
        if (!$position) {
            $position = 0;
        }
        return $position;
    }

    /**
     * Get children categories count
     *
     * @param int $categoryId Category id
     * @return int
     */
    public function getChildrenCount($categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'children_count')
            ->where('entity_id = :entity_id');
        $bind = array('entity_id' => $categoryId);
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Check if category id exist
     *
     * @param int $entityId Category id
     * @return bool
     */
    public function checkId($entityId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id = :entity_id');
        $bind = array('entity_id' => $entityId);
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Check array of categories identifiers
     *
     * @param array $ids Category ids
     * @return array
     */
    public function verifyIds(array $ids)
    {
        if (empty($ids)) {
            return array();
        }
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id IN(?)', $ids);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Get count of active/not active children categories
     *
     * @param Oggetto_News_Model_Category $category     Category
     * @param bool                        $isActiveFlag Is active flag
     * @return int
     */
    public function getChildrenAmount($category, $isActiveFlag = true)
    {
        $bind = array(
            'active_flag' => $isActiveFlag,
            'c_path' => $category->getPath() . '/%'
        );
        $select = $this->_getReadAdapter()->select()
            ->from(array('m' => $this->getMainTable()), array('COUNT(m.entity_id)'))
            ->where('m.path LIKE :c_path')
            ->where('status' . ' = :active_flag');
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Return parent categories of category
     *
     * @param Oggetto_News_Model_Category $category Category
     * @return array
     */
    public function getParentCategories($category)
    {
        $pathIds = array_reverse(explode('/', $category->getPath()));
        $categories = Mage::getResourceModel('news/category_collection')
            ->addFieldToFilter('entity_id', array('in' => $pathIds))
            ->load()
            ->getItems();
        return $categories;
    }

    /**
     * Return child categories
     *
     * @param Oggetto_News_Model_Category $category Category
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function getChildrenCategories($category)
    {
        $collection = $category->getCollection();
        $collection
            ->addIdFilter($category->getChildCategories())
            ->setOrder('position', Varien_Db_Select::SQL_ASC)
            ->load();
        return $collection;
    }

    /**
     * Return children ids of category
     *
     * @param Oggetto_News_Model_Category $category  Category
     * @param boolean                     $recursive Is recursive
     * @return array
     */
    public function getChildren($category, $recursive = true)
    {
        $bind = [
            'c_path' => $category->getPath() . '/%'
        ];
        $select = $this->_getReadAdapter()->select()
            ->from(['m' => $this->getMainTable()], 'entity_id')
            ->where('status = ?', 1)
            ->where($this->_getReadAdapter()->quoteIdentifier('path') . ' LIKE :c_path');
        if (!$recursive) {
            $select->where($this->_getReadAdapter()->quoteIdentifier('level') . ' <= :c_level');
            $bind['c_level'] = $category->getLevel() + 1;
        }
        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }

    /**
     * Process category data before saving
     * prepare path and increment children count for parent categories
     *
     * @param Mage_Core_Model_Abstract $object Category
     * @return Oggetto_News_Model_Resource_Category
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getInitialSetupFlag()) {
            $urlKey = $object->getData('url_key');
            if ($urlKey == '') {
                $urlKey = $object->getName();
            }
            $urlKey = $this->formatUrlKey($urlKey);
            $validKey = false;
            while (!$validKey) {
                $entityId = $this->checkUrlPath($urlKey, $object->getStoreId(), false);
                if ($entityId == $object->getId() || empty($entityId)) {
                    $validKey = true;
                } else {
                    $parts = explode('-', $urlKey);
                    $last = $parts[count($parts) - 1];
                    if (!is_numeric($last)) {
                        $urlKey = $urlKey . '-1';
                    } else {
                        $suffix = '-' . ($last + 1);
                        unset($parts[count($parts) - 1]);
                        $urlKey = implode('-', $parts) . $suffix;
                    }
                }
            }
            $object->setData('url_key', $urlKey);
        }
        parent::_beforeSave($object);
        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }
        if ($object->getLevel() === null) {
            $object->setLevel(1);
        }
        if (!$object->getId() && !$object->getInitialSetupFlag()) {
            $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            $path = explode('/', $object->getPath());
            $level = count($path);
            $object->setLevel($level);
            if ($level) {
                $object->setParentId($path[$level - 1]);
            }
            $object->setPath($object->getPath() . '/');
            $toUpdateChild = explode('/', $object->getPath());
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                ['children_count'  => new Zend_Db_Expr('children_count+1')],
                ['entity_id IN(?)' => $toUpdateChild]
            );
        }
        return $this;
    }

    /**
     * Retrieve categories
     *
     * @param int            $parent         Parent category node
     * @param int            $recursionLevel Recursion level
     * @param boolean|string $sorted         Is sorted
     * @param boolean        $asCollection   As collection
     * @param boolean        $toLoad         To load
     * @return Varien_Data_Tree_Node_Collection|Oggetto_News_Model_Resource_Category_Collection
     */
    public function getCategories($parent, $recursionLevel = 0, $sorted = false, $asCollection = false, $toLoad = true)
    {
        $tree = Mage::getResourceModel('news/category_tree');
        $nodes = $tree->loadNode($parent)
            ->loadChildren($recursionLevel)
            ->getChildren();
        $tree->addCollectionData(null, $sorted, $parent, $toLoad, true);
        if ($asCollection) {
            return $tree->getCollection();
        }
        return $nodes;
    }

    /**
     * Return all children ids of category (with category id)
     *
     * @param Oggetto_News_Model_Category $category Category
     * @return array
     */
    public function getAllChildren($category)
    {
        $children = $this->getChildren($category);
        $myId = array($category->getId());
        $children = array_merge($myId, $children);
        return $children;
    }

    /**
     * Check category is forbidden to delete.
     *
     * @param int $categoryId Category
     * @return boolean
     */
    public function isForbiddenToDelete($categoryId)
    {
        return ($categoryId == Mage::helper('news/category')->getRootCategoryId());
    }

    /**
     * Get category path value by its id
     *
     * @param int $categoryId Category id
     * @return string
     */
    public function getCategoryPathById($categoryId)
    {
        $select = $this->getReadConnection()->select()
            ->from($this->getMainTable(), array('path'))
            ->where('entity_id = :entity_id');
        $bind = array('entity_id' => (int)$categoryId);
        return $this->getReadConnection()->fetchOne($select, $bind);
    }

    /**
     * Move category to another parent node
     *
     * @param Oggetto_News_Model_Category $category        Category
     * @param Oggetto_News_Model_Category $newParent       New category parent
     * @param null|int                    $afterCategoryId Id category after
     * @return Oggetto_News_Model_Resource_Category
     */
    public function changeParent(
        Oggetto_News_Model_Category $category,
        Oggetto_News_Model_Category $newParent,
        $afterCategoryId = null
    ) {
        $childrenCount = $this->getChildrenCount($category->getId()) + 1;
        $table = $this->getMainTable();
        $adapter = $this->_getWriteAdapter();
        $levelFiled = $adapter->quoteIdentifier('level');
        $pathField = $adapter->quoteIdentifier('path');

        /**
         * Decrease children count for all old category parent categories
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count - ' . $childrenCount)),
            array('entity_id IN(?)' => $category->getParentIds())
        );
        /**
         * Increase children count for new category parents
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count + ' . $childrenCount)),
            array('entity_id IN(?)' => $newParent->getPathIds())
        );

        $position = $this->_processPositions($category, $newParent, $afterCategoryId);

        $newPath = sprintf('%s/%s', $newParent->getPath(), $category->getId());
        $newLevel = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $category->getLevel();

        /**
         * Update children nodes path
         */
        $adapter->update(
            $table,
            array(
                'path' => new Zend_Db_Expr('REPLACE(' . $pathField . ',' .
                        $adapter->quote($category->getPath() . '/') . ', ' . $adapter->quote($newPath . '/') . ')'
                    ),
                'level' => new Zend_Db_Expr($levelFiled . ' + ' . $levelDisposition)
            ),
            array($pathField . ' LIKE ?' => $category->getPath() . '/%')
        );
        /**
         * Update moved category data
         */
        $data = array(
            'path' => $newPath,
            'level' => $newLevel,
            'position' => $position,
            'parent_id' => $newParent->getId()
        );
        $adapter->update($table, $data, array('entity_id = ?' => $category->getId()));
        // Update category object to new data
        $category->addData($data);
        return $this;
    }

    /**
     * Process positions of old parent category children and new parent category children.
     * Get position for moved category
     *
     * @param Oggetto_News_Model_Category $category        Category
     * @param Oggetto_News_Model_Category $newParent       New category parent
     * @param null|int                    $afterCategoryId Id category after
     * @return int
     */
    protected function _processPositions($category, $newParent, $afterCategoryId)
    {
        $table = $this->getMainTable();
        $adapter = $this->_getWriteAdapter();
        $positionField = $adapter->quoteIdentifier('position');

        $bind = [
            'position' => new Zend_Db_Expr($positionField . ' - 1')
        ];
        $where = [
            'parent_id = ?' => $category->getParentId(),
            $positionField . ' > ?' => $category->getPosition()
        ];
        $adapter->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterCategoryId) {
            $select = $adapter->select()
                ->from($table, 'position')
                ->where('entity_id = :entity_id');
            $position = $adapter->fetchOne($select, array('entity_id' => $afterCategoryId));
            $bind = [
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            ];
            $where = [
                'parent_id = ?' => $newParent->getId(),
                $positionField . ' > ?' => $position
            ];
            $adapter->update($table, $bind, $where);
        } elseif ($afterCategoryId !== null) {
            $position = 0;
            $bind = [
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            ];
            $where = [
                'parent_id = ?' => $newParent->getId(),
                $positionField . ' > ?' => $position
            ];
            $adapter->update($table, $bind, $where);
        } else {
            $select = $adapter->select()
                ->from($table, ['position' => new Zend_Db_Expr('MIN(' . $positionField . ')')])
                ->where('parent_id = :parent_id');
            $position = $adapter->fetchOne($select, ['parent_id' => $newParent->getId()]);
        }
        $position += 1;
        return $position;
    }

    /**
     * Check url path
     *
     * @param string $urlPath Url path
     * @param int    $storeId Store id
     * @param bool   $active  Is active
     * @return string
     */
    public function checkUrlPath($urlPath, $storeId, $active = true)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_initCheckUrlPathSelect($urlPath, $stores);
        if ($active) {
            $select->where('e.status = ?', $active);
        }
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Init the check select by url path
     *
     * @param string $urlPath Url path
     * @return Zend_Db_Select
     */
    protected function _initCheckUrlPathSelect($urlPath)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(['e' => $this->getMainTable()])
            ->where('e.url_path = ?', $urlPath);
        return $select;
    }
}
