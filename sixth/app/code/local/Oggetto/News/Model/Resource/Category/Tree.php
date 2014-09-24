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
 * Category tree resource model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Resource_Category_Tree extends Varien_Data_Tree_Dbp
{
    const ID_FIELD = 'entity_id';
    const PATH_FIELD = 'path';
    const ORDER_FIELD = 'order';
    const LEVEL_FIELD = 'level';

    /**
     * Categories resource collection
     * @var Oggetto_News_Model_Resource_Category_Collection
     */
    protected $_collection;
    protected $_storeId;

    /**
     * Inactive categories ids
     * @var array
     */
    protected $_inactiveCategoryIds = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct(
            $resource->getConnection('news_write'),
            $resource->getTableName('news/category'),
            [
                Varien_Data_Tree_Dbp::ID_FIELD => 'entity_id',
                Varien_Data_Tree_Dbp::PATH_FIELD => 'path',
                Varien_Data_Tree_Dbp::ORDER_FIELD => 'position',
                Varien_Data_Tree_Dbp::LEVEL_FIELD => 'level',
            ]
        );
    }

    /**
     * Get categories collection
     *
     * @param boolean $sorted Is sorted
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function getCollection($sorted = false)
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_getDefaultCollection($sorted);
        }
        return $this->_collection;
    }

    /**
     * Set the collection
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $collection Category collection
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function setCollection($collection)
    {
        if (!is_null($this->_collection)) {
            destruct($this->_collection);
        }
        $this->_collection = $collection;
        return $this;
    }

    /**
     * Get the default collection
     *
     * @param boolean $sorted Is sorted
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    protected function _getDefaultCollection($sorted = false)
    {
        $collection = Mage::getModel('news/category')->getCollection();
        if ($sorted) {
            if (is_string($sorted)) {
                $collection->setOrder($sorted);
            } else {
                $collection->setOrder('name');
            }
        }
        return $collection;
    }

    /**
     * Executing parents move method and cleaning cache after it
     *
     * @param Varien_Data_Tree_Node $category  Category
     * @param Varien_Data_Tree_Node $newParent New category parent
     * @param Varien_Data_Tree_Node $prevNode  Previous node
     * @return void
     */
    public function move($category, $newParent, $prevNode = null)
    {
        Mage::getResourceSingleton('news/category')->move($category->getId(), $newParent->getId());
        parent::move($category, $newParent, $prevNode);
        $this->_afterMove($category, $newParent, $prevNode);
    }

    /**
     * Move tree after
     *
     * @param Varien_Data_Tree_Node $category  Category
     * @param Varien_Data_Tree_Node $newParent New category parent
     * @param Varien_Data_Tree_Node $prevNode  Previous node
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    protected function _afterMove($category, $newParent, $prevNode)
    {
        Mage::app()->cleanCache([Oggetto_News_Model_Category::CACHE_TAG]);
        return $this;
    }

    /**
     * Load whole category tree, that will include specified categories ids.
     *
     * @param array $ids               Category ids
     * @param bool  $addCollectionData Should add collection data
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function loadByIds($ids, $addCollectionData = true)
    {
        $levelField = $this->_conn->quoteIdentifier('level');
        $pathField = $this->_conn->quoteIdentifier('path');
        // load first two levels, if no ids specified
        $ids = $this->_prepareIds($ids, $levelField);
        $where = [];
        // collect paths of specified IDs and prepare to collect all their parents and neighbours
        $select = $this->_conn->select()
            ->from($this->_table, ['path', 'level'])
            ->where('entity_id IN (?)', $ids);
        $where[] = $levelField . '=0';

        foreach ($this->_conn->fetchAll($select) as $item) {
            $where = array_merge($where, $this->_getConditionsForNode($item, $levelField, $pathField));
        }

        $select = $this->_getAllRequiredRecords($addCollectionData, $where);

        // get array of records and add them as nodes to the tree
        $arrNodes = $this->_conn->fetchAll($select);
        if (!$arrNodes) {
            return false;
        }
        $this->addChildNodes($this->_getChildItems($arrNodes), '', null);
        return $this;
    }

    /**
     * Prepare ids to select
     *
     * @param array|string $ids        Ids
     * @param int          $levelField Node level
     * @return array
     */
    protected function _prepareIds($ids, $levelField)
    {
        if (empty($ids)) {
            $select = $this->_conn->select()
                ->from($this->_table, 'entity_id')
                ->where($levelField . ' <= 2');
            $ids = $this->_conn->fetchCol($select);
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        foreach ($ids as $key => $id) {
            $ids[$key] = (int)$id;
        }
        return $ids;
    }

    /**
     * Get conditions for node
     *
     * @param array  $item       Node
     * @param string $levelField Level field
     * @param string $pathField  Path field
     * @return array
     */
    protected function _getConditionsForNode(array $item, $levelField, $pathField)
    {
        $where = [];
        $pathIds = explode('/', $item['path']);
        $level = (int)$item['level'];
        while ($level > 0) {
            $pathIds[count($pathIds) - 1] = '%';
            $path = implode('/', $pathIds);
            $where[] = "$levelField=$level AND $pathField LIKE '$path'";
            array_pop($pathIds);
            $level--;
        }
        return $where;
    }

    /**
     * Get all required records
     *
     * @param bool  $addCollectionData Should add collection data
     * @param array $where             Where conditions
     * @return Zend_Db_Select
     */
    protected function _getAllRequiredRecords($addCollectionData, array $where)
    {
        if ($addCollectionData) {
            $select = $this->_createCollectionDataSelect();
        } else {
            $select = clone $this->_select;
            $select->order($this->_orderField . ' ' . Varien_Db_Select::SQL_ASC);
        }
        $select->where(implode(' OR ', $where));
        return $select;
    }

    /**
     * Get child items for nodes
     *
     * @param array $arrNodes Nodes
     * @return array
     */
    protected function _getChildItems(array $arrNodes)
    {
        $childrenItems = [];
        foreach ($arrNodes as $key => $nodeInfo) {
            $pathToParent = explode('/', $nodeInfo[$this->_pathField]);
            array_pop($pathToParent);
            $pathToParent = implode('/', $pathToParent);
            $childrenItems[$pathToParent][] = $nodeInfo;
        }
        return $childrenItems;
    }

    /**
     * Load array of category parents
     *
     * @param string $path              Category path
     * @param bool   $addCollectionData Should add collection data
     * @param bool   $withRootNode      Should load with root node
     * @return array
     */
    public function loadBreadcrumbsArray($path, $addCollectionData = true, $withRootNode = false)
    {
        $pathIds = explode('/', $path);
        if (!$withRootNode) {
            array_shift($pathIds);
        }
        $result = [];
        if (!empty($pathIds)) {
            if ($addCollectionData) {
                $select = $this->_createCollectionDataSelect(false);
            } else {
                $select = clone $this->_select;
            }
            $select
                ->where('main_table.entity_id IN(?)', $pathIds)
                ->order($this->_conn->getLengthSql('main_table.path') . ' ' . Varien_Db_Select::SQL_ASC);
            $result = $this->_conn->fetchAll($select);
        }
        return $result;
    }

    /**
     * Obtain select for categories
     *
     * @param bool $sorted Is sorted
     * @return Zend_Db_Select
     */
    protected function _createCollectionDataSelect($sorted = true)
    {
        $select = $this->_getDefaultCollection($sorted ? $this->_orderField : false)->getSelect();
        return $select;
    }

    /**
     * Get real existing category ids by specified ids
     *
     * @param array $ids Category ids
     * @return array
     */
    public function getExistingCategoryIdsBySpecifiedIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $select = $this->_conn->select()
            ->from($this->_table, ['entity_id'])
            ->where('entity_id IN (?)', $ids);
        return $this->_conn->fetchCol($select);
    }

    /**
     * Add collection data
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $collection Category collection
     * @param boolean                                         $sorted     Is sorted
     * @param array                                           $exclude    Categories to exclude
     * @param boolean                                         $toLoad     To load
     * @param boolean                                         $onlyActive Add only active
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function addCollectionData(
        $collection = null, $sorted = false, $exclude = [], $toLoad = true, $onlyActive = false
    ) {
        if (is_null($collection)) {
            $collection = $this->getCollection($sorted);
        } else {
            $this->setCollection($collection);
        }
        $nodeIds = $this->_getNodeIds($exclude);
        $collection->addIdFilter($nodeIds);
        $this->_excludeDisabledNodes($collection, $onlyActive);
        if ($toLoad) {
            $collection->load();
            $this->_fillNodeDataFromCollection($collection);
            foreach ($this->getNodes() as $node) {
                if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                    $this->removeNode($node);
                }
            }
        }
        return $this;
    }

    /**
     * Get node ids except excluded
     *
     * @param array $exclude Excluded nodes
     * @return array
     */
    protected function _getNodeIds($exclude)
    {
        if (!is_array($exclude)) {
            $exclude = [$exclude];
        }
        $nodeIds = [];
        foreach ($this->getNodes() as $node) {
            if (!in_array($node->getId(), $exclude)) {
                $nodeIds[] = $node->getId();
            }
        }
        return $nodeIds;
    }

    /**
     * Exclude disabled nodes
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $collection Resource category collection
     * @param bool                                            $onlyActive Should select only active nodes
     * @return void
     */
    protected function _excludeDisabledNodes($collection, $onlyActive)
    {
        if (!$onlyActive) {
            return;
        }
        $disabledIds = $this->_getDisabledIds($collection);
        if ($disabledIds) {
            $collection->addFieldToFilter('entity_id', ['nin' => $disabledIds]);
        }
        $collection->addFieldToFilter('status', 1);
    }

    /**
     * Fill node data from collection
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $collection Resource category collection
     * @return void
     */
    protected function _fillNodeDataFromCollection($collection)
    {
        foreach ($collection as $category) {
            if ($this->getNodeById($category->getId())) {
                $this->getNodeById($category->getId())->addData($category->getData());
            }
        }
    }

    /**
     * Add inactive categories ids
     *
     * @param array $ids Category ids
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function addInactiveCategoryIds($ids)
    {
        if (!is_array($this->_inactiveCategoryIds)) {
            $this->_initInactiveCategoryIds();
        }
        $this->_inactiveCategoryIds = array_merge($ids, $this->_inactiveCategoryIds);
        return $this;
    }

    /**
     * Retrieve inactive categories ids
     *
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    protected function _initInactiveCategoryIds()
    {
        $this->_inactiveCategoryIds = [];
        return $this;
    }

    /**
     * Retrieve inactive categories ids
     *
     * @return array
     */
    public function getInactiveCategoryIds()
    {
        if (!is_array($this->_inactiveCategoryIds)) {
            $this->_initInactiveCategoryIds();
        }
        return $this->_inactiveCategoryIds;
    }

    /**
     * Return disable category ids
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $collection Category collection
     * @return array
     */
    protected function _getDisabledIds($collection)
    {
        $this->_inactiveItems = $this->getInactiveCategoryIds();
        $this->_inactiveItems = array_merge(
            $this->_getInactiveItemIds($collection),
            $this->_inactiveItems
        );
        $allIds = $collection->getAllIds();
        $disabledIds = [];

        foreach ($allIds as $id) {
            $parents = $this->getNodeById($id)->getPath();
            foreach ($parents as $parent) {
                if (!$this->_getItemIsActive($parent->getId())) {
                    $disabledIds[] = $id;
                    continue;
                }
            }
        }
        return $disabledIds;
    }

    /**
     * Retrieve inactive category item ids
     *
     * @param Oggetto_News_Model_Resource_Category_Collection $collection Category collection
     * @return array
     */
    protected function _getInactiveItemIds($collection)
    {
        $filter = $collection->getAllIdsSql();
        $table = Mage::getSingleton('core/resource')->getTable('news/category');
        $bind = [
            'cond' => 0,
        ];
        $select = $this->_conn->select()
            ->from(['d' => $table], ['d.entity_id'])
            ->where('d.entity_id IN (?)', new Zend_Db_Expr($filter))
            ->where('status = :cond');
        return $this->_conn->fetchCol($select, $bind);
    }

    /**
     * Check is category items active
     *
     * @param int $id Category id
     * @return boolean
     */
    protected function _getItemIsActive($id)
    {
        return !in_array($id, $this->_inactiveItems);
    }
}
