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
 * Category admin block abstract
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Adminhtml_Category_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * Get current category
     *
     * @return Oggetto_News_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('category');
    }

    /**
     * Get current category id
     *
     * @return int
     */
    public function getCategoryId()
    {
        if ($category = $this->getCategory()) {
            return $category->getId();
        }
        return null;
    }

    /**
     * Get current category Name
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getCategory()->getName();
    }

    /**
     * Get the root
     *
     * @param Oggetto_News_Model_Category $parentNodeCategory Parent category
     * @param int                         $recursionLevel     Recursion level
     * @return Varien_Data_Tree_Node
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (!is_null($root)) {
            return $root;
        }
        $rootId = Mage::helper('news/category')->getRootCategoryId();
        $tree = Mage::getResourceSingleton('news/category_tree')
            ->load(null, $recursionLevel);
        if ($this->getCategory()) {
            $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
        }
        $tree->addCollectionData($this->getCategoryCollection());
        $root = $tree->getNodeById($rootId);
        $root->setName(Mage::helper('news')->__('Root'));
        Mage::register('root', $root);
        return $root;
    }

    /**
     * Get specific node
     *
     * @param Oggetto_News_Model_Category $parentNodeCategory Parent category
     * @param int                         $recursionLevel     Recursion level
     * @return Varien_Data_Tree_Node
     */
    public function getNode($parentNodeCategory, $recursionLevel = 2)
    {
        $tree = Mage::getResourceModel('news/category_tree');
        $nodeId = $parentNodeCategory->getId();
        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);
        if ($node && $nodeId != Mage::helper('news/category')->getRootCategoryId()) {
            $node->setIsVisible(true);
        } elseif ($node && $node->getId() == Mage::helper('news/category')->getRootCategoryId()) {
            $node->setName(Mage::helper('news')->__('Root'));
        }
        $tree->addCollectionData($this->getCategoryCollection());
        return $node;
    }

    /**
     * Get url for saving data
     *
     * @param array $args Parameters
     * @return string
     */
    public function getSaveUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    /**
     * Return root ids
     *
     * @return array
     */
    public function getRootIds()
    {
        return [Mage::helper('news/category')->getRootCategoryId()];
    }
}
