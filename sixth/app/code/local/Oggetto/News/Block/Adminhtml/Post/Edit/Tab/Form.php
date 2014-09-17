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
 * Post edit form tab
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Adminhtml_Post_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @return Oggetto_News_Block_Adminhtml_Post_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('post_');
        $form->setFieldNameSuffix('post');
        $this->setForm($form);
        $fieldset = $form->addFieldset('post_form', ['legend' => Mage::helper('news')->__('Post')]);
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();

        $fieldset->addField('title', 'text', [
            'label' => Mage::helper('news')->__('Title'),
            'name' => 'title',
            'required' => true,
            'class' => 'required-entry'
        ]);

        $fieldset->addField('text', 'editor', [
            'label' => Mage::helper('news')->__('Text'),
            'name' => 'text',
            'config' => $wysiwygConfig,
            'required' => true,
            'class' => 'required-entry'
        ]);
        $fieldset->addField('url_key', 'text', [
            'label' => Mage::helper('news')->__('Url key'),
            'name' => 'url_key',
            'note' => Mage::helper('news')->__('Relative to Website Base URL')
        ]);
        $fieldset->addField('status', 'select', [
            'label' => Mage::helper('news')->__('Status'),
            'name' => 'status',
            'values' => [
                [
                    'value' => 1,
                    'label' => Mage::helper('news')->__('Enabled')
                ],
                [
                    'value' => 0,
                    'label' => Mage::helper('news')->__('Disabled')
                ]
            ]
        ]);
        $formValues = Mage::registry('current_post')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = [];
        }
        if (Mage::getSingleton('adminhtml/session')->getPostData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getPostData());
            Mage::getSingleton('adminhtml/session')->setPostData(null);
        } elseif (Mage::registry('current_post')) {
            $formValues = array_merge($formValues, Mage::registry('current_post')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
