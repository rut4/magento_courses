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
     * Additional buttons on category page
     */
    protected $_additionalButtons = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('news/category/edit/form.phtml');
    }

    /**
     * prepare the layout
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
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('news')->__('Save Category'),
                    'onclick' => "categorySubmit('" . $this->getSaveUrl() . "', true)",
                    'class' => 'save'
                ])
        );
        // Delete button
        if (!in_array($categoryId, $this->getRootIds())) {
            $this->setChild('delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData([
                        'label' => Mage::helper('news')->__('Delete Category'),
                        'onclick' => "categoryDelete('" .
                            $this->getUrl('*/*/delete', array('_current' => true)) . "', true, {$categoryId})",
                        'class' => 'delete'
                    ])
            );
        }

        // Reset button
        $resetPath = $category ? '*/*/edit' : '*/*/add';
        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('news')->__('Reset'),
                    'onclick' => "categoryReset('" . $this->getUrl($resetPath, array('_current' => true)) . "',true)"
                ])
        );
        return parent::_prepareLayout();
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
     * Retrieve additional buttons html
     *
     * @return string
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->_additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }
        return $html;
    }

    /**
     * Add additional button
     *
     * @param string $alias  Button alias
     * @param array  $config Button config
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Form
     */
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        $this->setChild($alias . '_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->addData($config)
        );
        $this->_additionalButtons[$alias] = $alias . '_button';
        return $this;
    }

    /**
     * Remove additional button
     *
     * @param string $alias Button alias
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Form
     * @author Ultimate Module Creator
     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->_additionalButtons[$alias])) {
            $this->unsetChild($this->_additionalButtons[$alias]);
            unset($this->_additionalButtons[$alias]);
        }
        return $this;
    }

    /**
     * get html for tabs
     *
     * @return string
     * @author Ultimate Module Creator
     */
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    /**
     * get the form header
     *
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeader()
    {
        if ($this->getCategoryId()) {
            return $this->getCategoryName();
        } else {
            return Mage::helper('news')->__('New Root Category');
        }
    }

    /**
     * get the delete url
     *
     * @param array $args
     * @return string
     * @author Ultimate Module Creator
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
     * @param array $args
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRefreshPathUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }

    /**
     * check if request is ajax
     *
     * @return bool
     * @author Ultimate Module Creator
     */
    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }

    /**
     * get  in json format
     *
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPostsJson()
    {
        $posts = $this->getCategory()->getSelectedPosts();
        if (!empty($posts)) {
            $positions = array();
            foreach ($posts as $post) {
                $positions[$post->getId()] = $post->getPosition();
            }
            return Mage::helper('core')->jsonEncode($positions);
        }
        return '{}';
    }
}
