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
 * the Oggetto Blog module to newer versions in the future.
 * If you wish to customize the Oggetto Blog module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin post controller
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Blog_Adminhtml_PostController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Display post grid
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Create new question
     *
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit question
     *
     * @return void
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $post = Mage::getModel('blog/post');

        if ($id) {
            $post->load($id);

            if (!$post->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This post no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($post->getResourceName());

        $data = Mage::getSingleton('adminhtml/session')->getQuestionData(true);
        if (!empty($data)) {
            $post->setData($data);
        }

        Mage::register('post', $post);

        $this->_initAction()
            ->_addBreadcrumb(
                $id ? $this->__('Edit Post') : $this->__('New Post'),
                $id ? $this->__('Edit Post') : $this->__('New Post')
            )
            ->_addContent(
                $this->getLayout()
                    ->createBlock('blog/adminhtml_post_edit')
                    ->setData('action', $this->getUrl('*/*/save'))
            )
            ->renderLayout();

    }

    /**
     * Save question
     *
     * @return void
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $post = Mage::getSingleton('blog/post');
            $post->setData($postData);
            try {
                $post->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The post has been saved.'));

                $this->_redirect('*/*/');

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($this->__('An error occurred while saving this post.'));
            }
        }

        Mage::getSingleton('adminhtml/session')->setQuestionData($postData);
        $this->_redirectReferer();
    }

    /**
     * Delete question
     *
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('post_id');
        $post = Mage::getModel('blog/post');

        if ($id) {
            try {
                $post->setId($id)
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The post has been deleted.'));

                $this->_redirect('*/*/');

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($this->__('An error occurred while deleting this post.'));
            }
        }

        $this->_redirectReferer();
    }

    /**
     * Mass delete questions
     *
     * @return void
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('post_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select post(s).'));
        } else {
            try {
                $post = Mage::getModel('blog/post');
                foreach ($ids as $id) {
                    $post->setId($id)
                        ->delete();
                }
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess($this->__('Total of %d post(s) has been deleted.', count($ids)));

                $this->_redirectReferer();

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while deleting post.'));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Init action
     *
     * @return Oggetto_Blog_Adminhtml_PostController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('blog/post')
            ->_title($this->__('Oggetto Blog'))->_title($this->__('Posts'))
            ->_addBreadcrumb($this->__('Oggetto Blog'), $this->__('Oggetto Blog'))
            ->_addBreadcrumb($this->__('Posts'), $this->__('Posts'));

        return $this;
    }

    /**
     * Is post grid allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('blog/post');
    }
}
