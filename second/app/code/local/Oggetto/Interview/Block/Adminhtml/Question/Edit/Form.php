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
 * Question form edit adminhtml block class
 *
 * @category   Oggetto
 * @package    Oggetto_Interview
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Interview_Block_Adminhtml_Question_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('interview_question_form');
        $this->setTitle($this->__('Question Information'));
    }

    /**
     * Prepare form with fields
     *
     * @return Mage_Adminhtml_Block_Widget_Form Prepared form
     */
    public function _prepareForm()
    {
        $question = Mage::registry('question');

        $form = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
            'method'    => 'post'
        ]);

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend'    => $this->__('Question Information'),
            'class'     => 'fieldset-wide',
        ]);

        if ($question->getId()) {
            $fieldset->addField('question_id', 'hidden', [
                'name'  => 'question_id'
            ]);
        }

        $fieldset->addField('name', 'text', [
            'name'      => 'name',
            'label'     => $this->__('Name'),
            'title'     => $this->__('Name'),
            'required'  => true
        ]);

        $fieldset->addField('email', 'text', [
            'name'      => 'email',
            'label'     => $this->__('Email'),
            'title'     => $this->__('Email'),
            'required'  => true
        ]);

        $fieldset->addField('text', 'text', [
            'name'      => 'text',
            'label'     => $this->__('Text'),
            'title'     => $this->__('Text'),
            'required'  => true
        ]);

        $fieldset->addField('created_at', 'datetime', [
            'name'      => 'created_at',
            'format'    => 'Y-MM-dd HH:mm:ss',
            'label'     => $this->__('Created at'),
            'title'     => $this->__('Created at'),
            'readonly'  => true
        ]);

        $fieldset->addField('status', 'select', [
            'options'   => Mage::getModel('interview/question_status')->toOptionArray(),
            'name'      => 'status',
            'label'     => $this->__('Status'),
            'title'     => $this->__('Status'),
            'required'  => true,
        ]);

        $fieldset->addField('answer', 'text', [
            'name'      => 'answer',
            'label'     => $this->__('Answer'),
            'title'     => $this->__('Answer'),
            'required'  => false
        ]);

        $form->setValues($question->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}