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
 * Category admin controller
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Adminhtml_News_CategoryController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init category
     *
     * @return Oggetto_News_Model_Category
     */
    protected function _initCategory()
    {
        $categoryId = (int)$this->getRequest()->getParam('id', false);
        $category = Mage::getModel('news/category');
        if ($categoryId) {
            $category->load($categoryId);
        } else {
            $category->setData($category->getDefaultValues());
        }
        if ($activeTabId = (string)$this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setCategoryActiveTabId($activeTabId);
        }
        Mage::register('category', $category);
        Mage::register('current_category', $category);
        return $category;
    }

    /**
     * Index action redirects to edit
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('edit');
    }

    /**
     * Add new category form
     *
     * @return void
     */
    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsCategoryActiveTabId();
        $this->_forward('edit');
    }

    /**
     * Edit category page
     *
     * @return void
     */
    public function editAction()
    {
        $params['_current'] = true;
        $redirect = false;
        $categoryId = (int)$this->getRequest()->getParam('id');
        $_prevCategoryId = Mage::getSingleton('admin/session')->getLastEditedCategory(true);
        if ($_prevCategoryId && !$this->getRequest()->getQuery('isAjax') && !$this->getRequest()->getParam('clear')) {
            $this->getRequest()->setParam('id', $_prevCategoryId);
        }
        if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }
        if (!($category = $this->_initCategory())) {
            return;
        }
        $this->_title($categoryId ? $category->getName() : $this->__('New Category'));
        $data = Mage::getSingleton('adminhtml/session')->getCategoryData(true);
        if (isset($data['category'])) {
            $category->addData($data['category']);
        }
        if ($this->getRequest()->getQuery('isAjax')) {
            $breadcrumbsPath = $category->getPath();
            if (empty($breadcrumbsPath)) {
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getCategoryDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    } else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }
            Mage::getSingleton('admin/session')->setLastEditedCategory($category->getId());
            $this->loadLayout();
            $eventResponse = new Varien_Object(array(
                'content' => $this->getLayout()->getBlock('category.edit')->getFormHtml()
                    . $this->getLayout()
                        ->getBlock('category.tree')
                        ->getBreadcrumbsJavascript($breadcrumbsPath, 'editingCategoryBreadcrumbs'),
                'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
            ));
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($eventResponse->getData()));
            return;
        }
        $this->loadLayout();
        $this->_title(Mage::helper('news')->__('Oggetto News'))
            ->_title(Mage::helper('news')->__('Categories'));
        $this->_setActiveMenu('system/news/category');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setContainerCssClass('category');

        $this->_addBreadcrumb(
            Mage::helper('news')->__('Manage Categories'),
            Mage::helper('news')->__('Manage Categories')
        );
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * Get tree node (Ajax version)
     *
     * @return void
     */
    public function categoriesJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setCategoryIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setCategoryIsTreeWasExpanded(false);
        }
        if ($categoryId = (int)$this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $categoryId);
            if (!$category = $this->_initCategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('news/adminhtml_category_tree')->getTreeJson($category)
            );
        }
    }

    /**
     * Move category action
     *
     * @return void
     */
    public function moveAction()
    {
        $category = $this->_initCategory();
        if (!$category) {
            $this->getResponse()->setBody(Mage::helper('news')->__('Category move error'));
            return;
        }
        $parentNodeId = $this->getRequest()->getPost('pid', false);
        $prevNodeId = $this->getRequest()->getPost('aid', false);
        try {
            $category->move($parentNodeId, $prevNodeId);
            $category->save();
            $this->getResponse()->setBody("SUCCESS");
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setBody(Mage::helper('news')->__('Category move error'));
            Mage::logException($e);
        }
    }

    /**
     * Retrieve category tree
     *
     * @return void
     */
    public function treeAction()
    {
        $category = $this->_initCategory();
        $block = $this->getLayout()->createBlock('news/adminhtml_category_tree');
        $root = $block->getRoot();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(
            array(
                'data' => $block->getTree(),
                'parameters' => array(
                    'text' => $block->buildNodeName($root),
                    'draggable' => false,
                    'allowDrop' => ($root->getIsVisible()) ? true : false,
                    'id' => (int)$root->getId(),
                    'expanded' => (int)$block->getIsWasExpanded(),
                    'category_id' => (int)$category->getId(),
                    'root_visible' => (int)$root->getIsVisible()
                )
            )
        ));
    }

    /**
     * Build response for refresh input element 'path' in form
     *
     * @return void
     */
    public function refreshPathAction()
    {
        if ($id = (int)$this->getRequest()->getParam('id')) {
            $category = Mage::getModel('news/category')->load($id);
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(array(
                    'id' => $id,
                    'path' => $category->getPath(),
                ))
            );
        }
    }

    /**
     * Delete category action
     *
     * @return void
     */
    public function deleteAction()
    {
        if ($id = (int)$this->getRequest()->getParam('id')) {
            try {
                $category = Mage::getModel('news/category')->load($id);
                Mage::getSingleton('admin/session')->setCategoryDeletedPath($category->getPath());

                $category->delete();
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess(Mage::helper('news')->__('The category has been deleted.'));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current' => true)));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addError(Mage::helper('news')->__('An error occurred while trying to delete the category.'));
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current' => true)));
                Mage::logException($e);
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current' => true, 'id' => null)));
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/news/category');
    }

    /**
     * Category save action
     *
     * @return void
     */
    public function saveAction()
    {
        if (!$category = $this->_initCategory()) {
            return;
        }
        $refreshTree = 'false';
        if ($data = $this->getRequest()->getPost('category')) {
            $category->addData($data);
            if (!$category->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    $parentId = Mage::helper('news/category')->getRootCategoryId();
                }
                $parentCategory = Mage::getModel('news/category')->load($parentId);
                $category->setPath($parentCategory->getPath());
            }
            try {
                $posts = $this->getRequest()->getPost('category_posts', -1);
                if ($posts != -1) {
                    $postData = [];
                    parse_str($posts, $postData);
                    $category->setPostsData($postData);
                }
                $category->save();
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess(Mage::helper('news')->__('The category has been saved.'));
                $refreshTree = 'true';
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage())->setCategoryData($data);
                Mage::logException($e);
                $refreshTree = 'false';
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $category->getId()));
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, ' . $refreshTree . ');</script>'
        );
    }

    /**
     * Show post grid
     *
     * @return void
     */
    public function postsgridAction()
    {
        if (!$category = $this->_initCategory()) {
            return;
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('news/adminhtml_category_edit_tab_post', 'category.post.grid')->toHtml()
        );
    }
}
