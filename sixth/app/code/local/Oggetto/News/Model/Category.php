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
 * Category model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Category extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'news_category';
    const CACHE_TAG = 'news_category';

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'news_category';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'category';
    protected $_postInstance = null;

    /**
     * Class constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('news/category');
    }

    /**
     * Before save category
     *
     * @return Oggetto_News_Model_Category
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * Get the url to the category details page
     *
     * @return string
     */
    public function getCategoryUrl()
    {
        if (($url = $this->getUrlPath()) || ($url = $this->getUrlKey())) {
            return Mage::getModel('core/url')->getDirectUrl($url);
        }
        return Mage::getUrl('news/category/view', ['id' => $this->getId()]);
    }

    /**
     * Check URL key
     *
     * @param string $urlKey Url key
     * @param bool   $active Is active
     * @return bool
     */
    public function checkUrlKey($urlKey, $active = true)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $active);
    }

    /**
     * Check URL path
     *
     * @param string $urlPath Url key
     * @param bool   $active  Is active
     * @return bool
     */
    public function checkUrlPath($urlPath, $active = true)
    {
        return $this->_getResource()->checkUrlPath($urlPath, $active);
    }


    /**
     * Save category relation
     *
     * @return Oggetto_News_Model_Category
     */
    protected function _afterSave()
    {
        $this->getPostInstance()->saveCategoryRelation($this);
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
        return parent::_afterSave();
    }

    /**
     * Get post relation model
     *
     * @return Oggetto_News_Model_Category_Post
     */
    public function getPostInstance()
    {
        if (!$this->_postInstance) {
            $this->_postInstance = Mage::getSingleton('news/category_post');
        }
        return $this->_postInstance;
    }

    /**
     * Get selected posts array
     *
     * @return array
     */
    public function getSelectedPosts()
    {
        if (!$this->hasSelectedPosts()) {
            $this->setSelectedPosts(
                array_values($this->getSelectedPostsCollection()->getItems())
            );
        }
        return $this->getData('selected_posts');
    }

    /**
     * Retrieve collection selected posts
     *
     * @return Oggetto_News_Model_Resource_Category_Post_Collection
     */
    public function getSelectedPostsCollection()
    {
        return $this->getPostInstance()->getPostsCollection($this);
    }

    /**
     * Get the tree model
     *
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('news/category_tree');
    }

    /**
     * get tree model instance
     *
     * @return Oggetto_News_Model_Resource_Category_Tree
     */
    public function getTreeModelInstance()
    {
        if (is_null($this->_treeModel)) {
            $this->_treeModel = Mage::getResourceSingleton('news/category_tree');
        }
        return $this->_treeModel;
    }

    /**
     * Move category
     *
     * @param int $parentId        New parent category id
     * @param int $afterCategoryId Category id after which we have put current category
     * @throws Exception
     * @return Oggetto_News_Model_Category
     */
    public function move($parentId, $afterCategoryId)
    {
        $parent = Mage::getModel('news/category')->load($parentId);
        $this->_checkCategoryIds($parent);
        $this->setMovedCategoryId($this->getId());
        /** @var Oggetto_News_Model_Resource_Category $resource */
        $resource = $this->_getResource()->beginTransaction();
        try {
            $resource
                ->changeParent($this, $parent, $afterCategoryId)
                ->commit();
            $this->setAffectedCategoryIds([$this->getId(), $this->getParentId(), $parentId]);
            $resource->updateUrlPath($this);

            $this->_moveChildCategories();
        } catch (Exception $e) {
            $resource->rollBack();
            throw $e;
        }
        Mage::app()->cleanCache([self::CACHE_TAG]);
        return $this;
    }

    /**
     * Check category and its parent ids for moving
     *
     * @param Oggetto_News_Model_Category $parent Category parent
     * @throws Mage_Core_Exception
     * @return void
     */
    protected function _checkCategoryIds($parent)
    {
        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('news')
                    ->__('Category move operation is not possible: the new parent category was not found.')
            );
        }
        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('news')->__('Category move operation is not possible: the current category was not found.')
            );
        } elseif ($parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('news')
                    ->__('Category move operation is not possible: parent category is equal to child category.')
            );
        }
    }

    /**
     * Move child categories after moving
     *
     * @return void
     */
    protected function _moveChildCategories()
    {
        $afterId = 0;
        foreach ($this->getChildrenCategories() as $children) {
            $children->move($this->getId(), $afterId);
            $afterId = $children->getId();
        }
    }

    /**
     * Get the parent category
     *
     * @return Oggetto_News_Model_Category
     */
    public function getParentCategory()
    {
        if (!$this->hasParentCategory()) {
            $this->setData('parent_category', Mage::getModel('news/category')->load($this->getParentId()));
        }
        return $this->_getData('parent_category');
    }

    /**
     * Get all parent categories ids
     *
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), [$this->getId()]);
    }

    /**
     * Get all categories children
     *
     * @param bool $asArray Should get categories as array
     * @return array|string
     */
    public function getAllChildren($asArray = false)
    {
        $children = $this->getResource()->getAllChildren($this);
        if ($asArray) {
            return $children;
        } else {
            return implode(',', $children);
        }
    }

    /**
     * Get all categories children
     *
     * @return string
     */
    public function getChildCategories()
    {
        return implode(',', $this->getResource()->getChildren($this, false));
    }

    /**
     * Check id
     *
     * @param int $id Category Id
     * @return bool
     */
    public function checkId($id)
    {
        return $this->_getResource()->checkId($id);
    }

    /**
     * Get array categories ids which are part of category path
     *
     * @return array
     */
    public function getPathIds()
    {
        if (!$this->hasPathIds()) {
            $this->setPathIds(explode('/', $this->getPath()));
        }
        return $this->getData('path_ids');
    }

    /**
     * Retrieve level
     *
     * @return int
     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return $this->_getLevelFromPath();
        }
        return $this->getData('level');
    }

    /**
     * Get level from category path
     *
     * @return int
     */
    protected function _getLevelFromPath()
    {
        return count(explode('/', $this->getPath())) - 1;
    }

    /**
     * Verify category ids
     *
     * @param array $ids Category IDs
     * @return bool
     */
    public function verifyIds(array $ids)
    {
        return $this->getResource()->verifyIds($ids);
    }

    /**
     * Check if category can be deleted
     *
     * @return Oggetto_News_Model_Category
     */
    protected function _beforeDelete()
    {
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            Mage::throwException(Mage::helper('news')->__("Can't delete root category."));
        }
        return parent::_beforeDelete();
    }

    /**
     * Get the categories
     *
     * @param Oggetto_News_Model_Category $parent         Parent category
     * @param int                         $recursionLevel Recursion level
     * @param bool                        $sorted         Is sorted
     * @param bool                        $asCollection   Should as collection
     * @param bool                        $toLoad         To load
     * @return array
     */
    public function getCategories(
        Oggetto_News_Model_Category $parent,
        $recursionLevel = 0, $sorted = false, $asCollection = false, $toLoad = true
    ) {
        return $this->getResource()->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
    }

    /**
     * Return parent categories of current category
     *
     * @return array
     */
    public function getParentCategories()
    {
        return $this->getResource()->getParentCategories($this);
    }

    /**
     * Return children categories of current category
     *
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function getChildrenCategories()
    {
        return $this->getResource()->getChildrenCategories($this);
    }

    /**
     * Check if parents are enabled
     *
     * @return bool
     */
    public function getStatusPath()
    {
        $parents = $this->getParentCategories();
        $rootId = Mage::helper('news/category')->getRootCategoryId();
        foreach ($parents as $parent) {
            if (!$parent->getStatus() && $parent->getId() != $rootId) {
                return false;
            }
        }
        return $this->getStatus();
    }

    /**
     * Get default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        return [
            'status' => 1
        ];
    }
}
