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
 * Post admin edit tabs
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Adminhtml_Post_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('news')->__('Post'));
    }

    /**
     * Before render html
     *
     * @return Oggetto_News_Block_Adminhtml_Post_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_post', [
            'label'   => Mage::helper('news')->__('Post'),
            'title'   => Mage::helper('news')->__('Post'),
            'content' => $this->getLayout()->createBlock('news/adminhtml_post_edit_tab_form')->toHtml(),
        ]);
        $this->addTab('categories', [
            'label' => Mage::helper('news')->__('Categories'),
            'url'   => $this->getUrl('*/*/categories', ['_current' => true]),
            'class' => 'ajax'
        ]);
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve post entity
     *
     * @return Oggetto_News_Model_Post
     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }
}
