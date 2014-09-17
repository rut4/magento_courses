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
 * post - category relation edit block
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Adminhtml_Post_Edit_Tab_Category extends Oggetto_News_Block_Adminhtml_Category_Tree
{
    protected $_categoryIds = null;
    protected $_selectedNodes = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('news/post/edit/tab/category.phtml');
    }

    /**
     * Retrieve currently edited post
     *
     * @return Oggetto_News_Model_Post
     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }

    /**
     * Return array with  IDs which the post is linked to
     *
     * @return array
     */
    public function getCategoryIds()
    {
        if (is_null($this->_categoryIds)) {
            $categories = $this->getPost()->getSelectedCategories();
            $ids = array();
            foreach ($categories as $category) {
                $ids[] = $category->getId();
            }
            $this->_categoryIds = $ids;
        }
        return $this->_categoryIds;
    }

    /**
     * Forms string out of getCategoryIds()
     *
     * @return string
     */
    public function getIdsString()
    {
        return implode(',', $this->getCategoryIds());
    }

    /**
     * Returns root node and sets 'checked' flag (if necessary)
     *
     * @return Varien_Data_Tree_Node
     */
    public function getRootNode()
    {
        $root = $this->getRoot();
        if ($root && in_array($root->getId(), $this->getCategoryIds())) {
            $root->setChecked(true);
        }
        return $root;
    }

    /**
     * Returns root node
     *
     * @param Oggetto_News_Model_Category|null $parentNodeCategory
     * @param int $recursionLevel
     * @return Varien_Data_Tree_Node
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('category_root');
        if (is_null($root)) {
            $rootId = Mage::helper('news/category')->getRootCategoryId();
            $ids = $this->getSelectedCategoryPathIds($rootId);
            $tree = Mage::getResourceSingleton('news/category_tree')
                ->loadByIds($ids, false, false);
            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            $root = $tree->getNodeById($rootId);
            Mage::register('category_root', $root);
        }
        return $root;
    }

    /**
     * Returns array with configuration of current node
     *
     * @param Varien_Data_Tree_Node $node  Category node
     * @param int                   $level How deep is the node in the tree
     * @return array
     */
    protected function _getNodeJson($node, $level = 1)
    {
        $item = parent::_getNodeJson($node, $level);
        if ($this->_isParentSelectedCategory($node)) {
            $item['expanded'] = true;
        }
        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }
        return $item;
    }

    /**
     * Returns whether $node is a parent (not exactly direct) of a selected node
     *
     * @param Varien_Data_Tree_Node $node Category node
     * @return bool
     */
    protected function _isParentSelectedCategory($node)
    {
        $result = false;
        // Contains string with all category IDs of children (not exactly direct) of the node
        $allChildren = $node->getAllChildren();
        if ($allChildren) {
            $selectedCategoryIds = $this->getCategoryIds();
            $allChildrenArr = explode(',', $allChildren);
            for ($i = 0, $cnt = count($selectedCategoryIds); $i < $cnt; $i++) {
                $isSelf = $node->getId() == $selectedCategoryIds[$i];
                if (!$isSelf && in_array($selectedCategoryIds[$i], $allChildrenArr)) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Returns array with nodes those are selected (contain current post)
     *
     * @return array
     */
    protected function _getSelectedNodes()
    {
        if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            $root = $this->getRoot();
            foreach ($this->getCategoryIds() as $categoryId) {
                if ($root) {
                    $this->_selectedNodes[] = $root->getTree()->getNodeById($categoryId);
                }
            }
        }
        return $this->_selectedNodes;
    }

    /**
     * Returns JSON-encoded array of  children
     *
     * @param int $categoryId Category ID
     * @return string
     */
    public function getCategoryChildrenJson($categoryId)
    {
        $category = Mage::getModel('news/category')->load($categoryId);
        $node = $this->getRoot($category, 1)->getTree()->getNodeById($categoryId);
        if (!$node || !$node->hasChildren()) {
            return '[]';
        }
        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }
        return Mage::helper('core')->jsonEncode($children);
    }

    /**
     * Returns URL for loading tree
     *
     * @param null $expanded Is expanded
     * @return string
     */
    public function getLoadTreeUrl($expanded = null)
    {
        return $this->getUrl('*/*/categoriesJson', ['_current' => true]);
    }

    /**
     * Return distinct path ids of selected
     *
     * @param mixed $rootId Root category Id for context
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedCategoryPathIds($rootId = false)
    {
        $ids = [];
        $categoryIds = $this->getCategoryIds();
        if (empty($categoryIds)) {
            return [];
        }
        $collection = Mage::getResourceModel('news/category_collection');
        if ($rootId) {
            $collection->addFieldToFilter('parent_id', $rootId);
        } else {
            $collection->addFieldToFilter('entity_id', ['in' => $categoryIds]);
        }

        foreach ($collection as $item) {
            if ($rootId && !in_array($rootId, $item->getPathIds())) {
                continue;
            }
            foreach ($item->getPathIds() as $id) {
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }

    /**
     * Get node label
     *
     * @param Varien_Object $node Category node
     * @return string
     */
    public function buildNodeName($node)
    {
        $result = parent::buildNodeName($node);
        $result .= '<a target="_blank" href="'
            . $this->getUrl('adminhtml/news_category/index', ['id' => $node->getId(), 'clear' => 1])
            . '"><em>' . $this->__(' - Edit') . '</em></a>';
        return $result;
    }
}
