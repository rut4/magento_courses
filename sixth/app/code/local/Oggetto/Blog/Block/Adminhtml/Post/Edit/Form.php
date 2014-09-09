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
 * Post edit form
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Blog_Block_Adminhtml_Post_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('blog_post_form');
        $this->setTitle($this->__('Question Information'));
    }

    /**
     * Prepare form with fields
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    public function _prepareForm()
    {
        $post = Mage::registry('post');

        $form = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', ['post_id' => $this->getRequest()->getParam('post_id')]),
            'method'    => 'post'
        ]);

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend'    => $this->__('Post'),
            'class'     => 'fieldset-wide',
        ]);

        if ($post->getId()) {
            $fieldset->addField('post_id', 'hidden', [
                'name'  => 'post_id'
            ]);
        }

        $fieldset->addField('category_id', 'text', [
            'name'      => 'category_id',
            'label'     => $this->__('Category ID'),
            'title'     => $this->__('Category ID'),
            'required'  => true
        ]);

        $fieldset->addField('title', 'text', [
            'name'      => 'title',
            'label'     => $this->__('Name'),
            'title'     => $this->__('Name'),
            'required'  => true
        ]);

        $fieldset->addField('text', 'text', [
            'name'      => 'text',
            'label'     => $this->__('Text'),
            'title'     => $this->__('Text'),
            'required'  => true
        ]);

        $fieldset->addField('placing_date', 'datetime', [
            'name'      => 'placing_date',
            'format'    => 'Y-MM-dd HH:mm:ss',
            'label'     => $this->__('Placing Date'),
            'title'     => $this->__('Placing Date'),
            'readonly'  => true
        ]);

        $form->setValues($post->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}