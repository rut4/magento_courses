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
 * Category edit form
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Adminhtml_Category_Edit_Form extends Oggetto_News_Block_Adminhtml_Category_Abstract
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('news/category/edit/form.phtml');
    }

    /**
     * Prepare layout
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Form=
     */
    protected function _prepareLayout()
    {
        $category = $this->getCategory();
        $categoryId = (int)$category->getId();
        $this->setChild('tabs',
            $this->getLayout()->createBlock('news/adminhtml_category_edit_tabs', 'tabs')
        );
        $this->_setupSaveButton();

        if (!in_array($categoryId, $this->getRootIds())) {
            $this->_setupDeleteButton($categoryId);
        }

        $this->_setupResetButton($category);

        return parent::_prepareLayout();
    }

    /**
     * Setup save button
     *
     * @return void
     */
    protected function _setupSaveButton()
    {
        $this->setChild('save_button',
            $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'   => Mage::helper('news')->__('Save Category'),
                    'onclick' => "categorySubmit('" . $this->getSaveUrl() . "', true)",
                    'class'   => 'save'
                ])
        );
    }

    /**
     * Setup delete button
     *
     * @param int $categoryId Category id
     * @return void
     */
    protected function _setupDeleteButton($categoryId)
    {
        $this->setChild('delete_button',
            $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'   => Mage::helper('news')->__('Delete Category'),
                    'onclick' => "categoryDelete('" .
                        $this->getUrl('*/*/delete', ['_current' => true]) . "', true, {$categoryId})",
                    'class'   => 'delete'
                ])
        );
    }

    /**
     * Setup reset button
     *
     * @param Oggetto_News_Model_Category $category Category model
     * @return void
     */
    protected function _setupResetButton($category)
    {
        $resetPath = $category ? '*/*/edit' : '*/*/add';
        $this->setChild('reset_button',
            $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'   => Mage::helper('news')->__('Reset'),
                    'onclick' => "categoryReset('" . $this->getUrl($resetPath, ['_current' => true]) . "',true)"
                ])
        );
    }

    /**
     * get html for delete button
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * get html for save button
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * get html for reset button
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Get html for tabs
     *
     * @return string
     */
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    /**
     * Get the form header
     *
     * @return string
     */
    public function getHeader()
    {
        if ($this->getCategoryId()) {
            return $this->getCategoryName();
        } else {
            return Mage::helper('news')->__('New Category');
        }
    }

    /**
     * Get delete url
     *
     * @param array $args Arguments
     * @return string
     */
    public function getDeleteUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }


    /**
     * Return URL for refresh input element 'path' in form
     *
     * @param array $args Arguments
     * @return string
     */
    public function getRefreshPathUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }

    /**
     * Check if request is ajax
     *
     * @return bool
     */
    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }

    /**
     * Get in json format
     *
     * @return string
     */
    public function getPostsJson()
    {
        $posts = $this->getCategory()->getSelectedPosts();
        if (!empty($posts)) {
            $positions = $this->_getPostPositions($posts);
            return Mage::helper('core')->jsonEncode($positions);
        }
        return '{}';
    }

    /**
     * Get post id => position array
     *
     * @param array $posts Posts
     * @return array
     */
    protected function _getPostPositions($posts)
    {
        $positions = [];
        foreach ($posts as $post) {
            $positions[$post->getId()] = $post->getPosition();
        }
        return $positions;
    }
}
