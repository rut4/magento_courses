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
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
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
     * Get current category path
     *
     * @return string
     */
    public function getCategoryPath()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getPath();
        }
        return Mage::helper('news/category')->getRootCategoryId();
    }

    /**
     * Check if there is a root category
     *
     * @return bool
     */
    public function hasRootCategory()
    {
        $root = $this->getRoot();
        if ($root && $root->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Get the root
     *
     * @param Oggetto_News_Model_Category|null $parentNodeCategory Parent category
     * @param int                              $recursionLevel     Recursion level
     * @return Varien_Data_Tree_Node
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $rootId = Mage::helper('news/category')->getRootCategoryId();
            $tree = Mage::getResourceSingleton('news/category_tree')
                ->load(null, $recursionLevel);
            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('news/category')->getRootCategoryId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('news/category')->getRootCategoryId()) {
                $root->setName(Mage::helper('news')->__('Root'));
            }
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * Get and register categories root by specified categories IDs
     *
     * @param array $ids Category ids
     * @return Varien_Data_Tree_Node
     */
    public function getRootByIds(array $ids)
    {
        $root = Mage::registry('root');
        if ($root !== null) {
            return $root;
        }
        $categoryTreeResource = Mage::getResourceSingleton('news/category_tree');
        $ids = $categoryTreeResource->getExistingCategoryIdsBySpecifiedIds($ids);
        $tree = $categoryTreeResource->loadByIds($ids);
        $rootId = Mage::helper('news/category')->getRootCategoryId();
        $root = $tree->getNodeById($rootId);
        if ($root && $rootId != Mage::helper('news/category')->getRootCategoryId()) {
            $root->setIsVisible(true);
        } else if ($root && $root->getId() == Mage::helper('news/category')->getRootCategoryId()) {
            $root->setName(Mage::helper('news')->__('Root'));
        }
        $tree->addCollectionData($this->getCategoryCollection());
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
     * Get url for edit
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl("*/news_category/edit", [
            '_current' => true,
            '_query'   => false,
            'id'       => null,
            'parent'   => null
        ]);
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
