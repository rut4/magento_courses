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
 * Post admin controller
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Adminhtml_News_PostController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init the post
     *
     * @return Oggetto_News_Model_Post
     */
    protected function _initPost()
    {
        $postId = (int)$this->getRequest()->getParam('id');
        $post = Mage::getModel('news/post');
        if ($postId) {
            $post->load($postId);
        }
        Mage::register('current_post', $post);
        return $post;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('news')->__('Oggetto News'))
            ->_title(Mage::helper('news')->__('Posts'));
        $this->renderLayout();
    }

    /**
     * Grid action
     *
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * Edit post
     *
     * @return void
     */
    public function editAction()
    {
        $postId = $this->getRequest()->getParam('id');
        $post = $this->_initPost();
        if ($postId && !$post->getId()) {
            $this->_getSession()->addError(Mage::helper('news')->__('This post no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getPostData(true);
        if (!empty($data)) {
            $post->setData($data);
        }
        Mage::register('post_data', $post);
        $this->loadLayout();
        $this->_title(Mage::helper('news')->__('Oggetto News'))
            ->_title(Mage::helper('news')->__('Posts'));
        if ($post->getId()) {
            $this->_title($post->getTitle());
        } else {
            $this->_title(Mage::helper('news')->__('Add post'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * New post
     *
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save post
     *
     * @return void
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('post');
        if (!$data) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('news')->__('Unable to find post to save.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $post = $this->_initPost();
            $post->addData($data);
            $categories = $this->getRequest()->getPost('category_ids', -1);
            if ($categories != -1) {
                $categories = explode(',', $categories);
                $categories = array_unique($categories);
                $post->setCategoriesData($categories);
            }
            $post->save();
            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('news')->__('Post was successfully saved'));
            Mage::getSingleton('adminhtml/session')->setFormData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['id' => $post->getId()]);
                return;
            }
            $this->_redirect('*/*/');
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setPostData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('news')->__('There was a problem saving the post.'));
            Mage::getSingleton('adminhtml/session')->setPostData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
    }

    /**
     * Delete post - action
     *
     * @return void
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') <= 0) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('news')->__('Could not find post to delete.'));
            $this->_redirect('*/*/');
            return;
        }
        try {
            $post = Mage::getModel('news/post');
            $post->setId($this->getRequest()->getParam('id'))->delete();
            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('news')->__('Post was successfully deleted.'));
            $this->_redirect('*/*/');
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('news')->__('There was an error deleting post.'));
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            Mage::logException($e);
            return;
        }
    }

    /**
     * Mass delete post - action
     *
     * @return void
     */
    public function massDeleteAction()
    {
        $postIds = $this->getRequest()->getParam('post');
        if (!is_array($postIds)) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('news')->__('Please select posts to delete.'));
            $this->_redirect('*/*/index');
            return;
        }
        try {
            foreach ($postIds as $postId) {
                $post = Mage::getModel('news/post');
                $post->setId($postId)->delete();
            }
            Mage::getSingleton('adminhtml/session')
                ->addSuccess(
                    Mage::helper('news')->__('Total of %d posts were successfully deleted.',
                        count($postIds))
                );
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')
                ->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('news')->__('There was an error deleting posts.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Mass status change
     *
     * @return void
     */
    public function massStatusAction()
    {
        try {
            $idCount = $this->_updateStatus();
            if (!$idCount) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('news')->__('Please select posts.'));
                $this->_redirect('*/*/index');
                return;
            }
            $this->_getSession()
                ->addSuccess($this->__('Total of %d posts were successfully updated.', $idCount));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('news')->__('There was an error updating posts.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Update post status
     *
     * @return int
     */
    protected function _updateStatus()
    {
        $postIds = $this->getRequest()->getParam('post');
        if (!is_array($postIds)) {
            return null;
        }
        foreach ($postIds as $postId) {
            Mage::getSingleton('news/post')->load($postId)
                ->setStatus($this->getRequest()->getParam('status'))
                ->setIsMassupdate(true)
                ->save();
        }
        return count($postIds);
    }

    /**
     * Get categories action
     *
     * @return void
     */
    public function categoriesAction()
    {
        $this->_initPost();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get child categories  action
     *
     * @return void
     */
    public function categoriesJsonAction()
    {
        $this->_initPost();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('news/adminhtml_post_edit_tab_category')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * Export as csv
     *
     * @return void
     */
    public function exportCsvAction()
    {
        $fileName = 'post.csv';
        $content = $this->getLayout()->createBlock('news/adminhtml_post_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export as MsExcel
     *
     * @return void
     */
    public function exportExcelAction()
    {
        $fileName = 'post.xls';
        $content = $this->getLayout()->createBlock('news/adminhtml_post_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export as xml
     *
     * @return void
     */
    public function exportXmlAction()
    {
        $fileName = 'post.xml';
        $content = $this->getLayout()->createBlock('news/adminhtml_post_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/news/post');
    }
}
