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
 * Category list block
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Category_List extends Mage_Core_Block_Template
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $categories = Mage::getResourceModel('news/category_collection')
            ->addFieldToFilter('status', 1);
        $categories->setOrder('position', 'asc');
        $this->setCategories($categories);
    }

    /**
     * Prepare the layout
     *
     * @return Oggetto_News_Block_Category_List
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getCategories()->addFieldToFilter('level', 1);
        if ($this->getDisplayMode() == 0) {
            $pager = $this->getLayout()->createBlock('page/html_pager', 'news.categories.html.pager')
                ->setCollection($this->getCategories());
            $this->setChild('pager', $pager);
            $this->getCategories()->load();
        }
        return $this;
    }

    /**
     * Get the pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get display mode
     *
     * @return int
     */
    public function getDisplayMode()
    {
        return Mage::helper('news/category')->getDisplayMode();
    }

    /**
     * Draw category
     *
     * @param Oggetto_News_Model_Category $category Category
     * @param int                         $level    Level
     * @return int
     */
    public function drawCategory($category, $level = 0)
    {
        if (!$category->getStatus()) {
            return '';
        }
        $recursion = $this->getRecursion();
        if ($recursion !== '0' && $level >= $recursion) {
            return '';
        }
        $html = '';
        $children = $category->getChildrenCategories();
        $activeChildren = $this->_getActiveChildren($level, $recursion, $children);
        $html .= '<li>';
        $html .= '<a href="' . $category->getCategoryUrl() . '">' . $category->getName() . '</a>';
        if (count($activeChildren) > 0) {
            $html .= '<ul>';
            $html = $this->_drawChildren($level, $children, $html);
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

    /**
     * Get recursion
     *
     * @return int
     */
    public function getRecursion()
    {
        if (!$this->hasRecursion()) {
            $this->setRecursion(Mage::helper('news/category')->getRecursion());
        }
        return $this->getData('recursion');
    }

    /**
     * Get active children
     *
     * @param int                                             $level     Level
     * @param int                                             $recursion Recursion
     * @param Oggetto_News_Model_Resource_Category_Collection $children  Child categories
     * @return array
     */
    protected function _getActiveChildren($level, $recursion, $children)
    {
        $activeChildren = [];
        if ($recursion == 0 || $level < $recursion - 1) {
            $activeChildren = array_filter(
                array_values($children->getItems()),
                function ($child) {
                    return $child->getStatus();
                }
            );
        }
        return $activeChildren;
    }

    /**
     * Draw children
     *
     * @param int                                             $level    Category level
     * @param Oggetto_News_Model_Resource_Category_Collection $children Children collection
     * @param string                                          $html     Output html
     * @return string
     */
    protected function _drawChildren($level, $children, $html)
    {
        foreach ($children as $child) {
            $html .= $this->drawCategory($child, $level + 1);
        }
        return $html;
    }
}
