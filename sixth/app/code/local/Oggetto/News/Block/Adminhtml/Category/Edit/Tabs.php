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
 * Category admin edit tabs
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(Mage::helper('news')->__('Category'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * Prepare Layout Content
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $this->addTab('form_category', [
            'label'   => Mage::helper('news')->__('Category'),
            'title'   => Mage::helper('news')->__('Category'),
            'content' => $this->getLayout()->createBlock('news/adminhtml_category_edit_tab_form')->toHtml(),
        ]);
        $this->addTab('posts', [
            'label'   => Mage::helper('news')->__('Posts'),
            'content' =>
                $this->getLayout()
                    ->createBlock('news/adminhtml_category_edit_tab_post', 'category.post.grid')
                    ->toHtml()
        ]);
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve category entity
     *
     * @return Oggetto_News_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }
}
