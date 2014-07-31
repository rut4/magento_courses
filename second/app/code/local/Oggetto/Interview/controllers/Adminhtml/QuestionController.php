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
 * Adminhtml controller
 *
 * @category   Oggetto
 * @package    Oggetto_Interview
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Interview_Adminhtml_QuestionController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function editAction()
    {
        $this->_initAction();

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('interview/question');

        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This question no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getResourceName());

    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer/question')
            ->_title($this->__('Customers'))->_title($this->__('Questions'))
            ->_addBreadcrumb($this->__('Customers'), $this->__('Customers'))
            ->_addBreadcrumb($this->__('Questions'), $this->__('Questions'));

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/question');
    }
}
