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
 * Category front contrller
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_CategoryController
    extends Mage_Core_Controller_Front_Action
{
    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('news/category')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb('home', array(
                        'label' => Mage::helper('news')->__('Home'),
                        'link' => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb('categories', array(
                        'label' => Mage::helper('news')->__('Categories'),
                        'link' => '',
                    )
                );
            }
        }
        $this->renderLayout();
    }

    /**
     * Init category
     *
     * @return Oggetto_News_Model_Category
     */
    protected function _initCategory()
    {
        $categoryId = $this->getRequest()->getParam('id', 0);
        $category = Mage::getModel('news/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);
        if (!$category->getId()) {
            return false;
        } elseif (!$category->getStatus()) {
            return false;
        }
        return $category;
    }

    /**
     * View category
     *
     * @return void
     */
    public function viewAction()
    {
        $category = $this->_initCategory();
        if (!$category) {
            $this->_forward('no-route');
            return;
        }
        if (!$category->getStatusPath()) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_category', $category);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('news-category news-category' . $category->getId());
        }
        if (Mage::helper('news/category')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb('home', array(
                        'label' => Mage::helper('news')->__('Home'),
                        'link' => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb('categories', array(
                        'label' => Mage::helper('news')->__('Categories'),
                        'link' => Mage::helper('news/category')->getCategoriesUrl(),
                    )
                );
                $parents = $category->getParentCategories();
                foreach ($parents as $parent) {
                    if ($parent->getId() != Mage::helper('news/category')->getRootCategoryId()
                        && $parent->getId() != $category->getId()) {
                        $breadcrumbBlock->addCrumb('category-' . $parent->getId(), array(
                            'label' => $parent->getName(),
                            'link' => $link = $parent->getCategoryUrl(),
                        ));
                    }
                }
                $breadcrumbBlock->addCrumb('category', array(
                        'label' => $category->getName(),
                        'link' => '',
                    )
                );
            }
        }
        $this->renderLayout();
    }
}
