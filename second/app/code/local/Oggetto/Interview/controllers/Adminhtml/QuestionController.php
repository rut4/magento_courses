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
 * the Oggetto Interview module to newer versions in the future.
 * If you wish to customize the Oggetto Interview module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Interview
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml question controller
 *
 * @category   Oggetto
 * @package    Oggetto_Interview
 * @subpackage controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Interview_Adminhtml_QuestionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action display grid
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Create new question
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit question
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $question = Mage::getModel('interview/question');

        if ($id) {
            $question->load($id);

            if (!$question->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This question no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($question->getResourceName());

        $data = Mage::getSingleton('adminhtml/session')->getQuestionData(true);
        if (!empty($data)) {
            $question->setData($data);
        }

        Mage::register('question', $question);

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Question') : $this->__('New Question'), $id ? $this->__('Edit Question') : $this->__('New Question'))
            ->_addContent($this->getLayout()->createBlock('interview/adminhtml_question_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();

    }

    /**
     * Save question
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $question = Mage::getSingleton('interview/question');
            $question->setData($postData);

            try {
                $question->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The question has been saved.'));

                $this->_redirect('*/*/');

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this question.'));
            }
        }

        Mage::getSingleton('adminhtml/session')->setQuestionData($postData);
        $this->_redirectReferer();
    }

    /**
     * Delete question
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $question = Mage::getModel('interview/question');

        if ($id) {
            try {
                $question->setId($id)
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The question has been deleted.'));

                $this->_redirect('*/*/');

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while deleting this question.'));
            }
        }

        $this->_redirectReferer();
    }

    /**
     * Mass delete questions
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select questions.'));
        } else {
            try {
                $question = Mage::getModel('interview/question');
                foreach ($ids as $id) {
                    $question->setId($id)
                        ->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d question(s) has been deleted.', count($ids)));

                $this->_redirectReferer();

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while deleting questions.'));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Mass change status questions
     */
    public function massChangeStatusAction()
    {
        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select questions.'));
        } else {
            try {
                $question = Mage::getModel('interview/question');
                foreach ($ids as $id) {
                    $question->load($id);
                    $question->setStatus((int)!$question->getStatus());
                    $question->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d question(s) has been changed status.', count($ids)));

                $this->_redirectReferer();

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while changing status questions.'));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Init action method
     *
     * @return Oggetto_Interview_Adminhtml_QuestionController $this Controller
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer/question')
            ->_title($this->__('Customers'))->_title($this->__('Questions'))
            ->_addBreadcrumb($this->__('Customers'), $this->__('Customers'))
            ->_addBreadcrumb($this->__('Questions'), $this->__('Questions'));

        return $this;
    }

    /**
     * Is allowed method
     *
     * @return bool Is allowed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/question');
    }
}
