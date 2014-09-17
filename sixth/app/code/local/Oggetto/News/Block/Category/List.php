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
            ->addFieldToFilter('status', 1);;
        $categories->getSelect()->order('main_table.position');
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
        if ($this->_getDisplayMode() == 0) {
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
     * Get the display mode
     *
     * @return int
     */
    protected function _getDisplayMode()
    {
        return Mage::getStoreConfigFlag('news/category/tree');
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
        $html = '';
        $recursion = $this->getRecursion();
        if ($recursion !== '0' && $level >= $recursion) {
            return '';
        }
        if (!$category->getStatus()) {
            return '';
        }
        $children = $category->getChildrenCategories();
        $activeChildren = array();
        if ($recursion == 0 || $level < $recursion - 1) {
            foreach ($children as $child) {
                if ($child->getStatus()) {
                    $activeChildren[] = $child;
                }
            }
        }
        $html .= '<li>';
        $html .= '<a href="' . $category->getCategoryUrl() . '">' . $category->getName() . '</a>';
        if (count($activeChildren) > 0) {
            $html .= '<ul>';
            foreach ($children as $child) {
                $html .= $this->drawCategory($child, $level + 1);
            }
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
        if (!$this->hasData('recursion')) {
            $this->setData('recursion', Mage::getStoreConfig('news/category/recursion'));
        }
        return $this->getData('recursion');
    }
}
