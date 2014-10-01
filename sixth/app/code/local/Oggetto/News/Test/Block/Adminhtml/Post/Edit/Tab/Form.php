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
 * Post edit form tab test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Adminhtml_Post_Edit_Tab_Form extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test prepares form
     *
     * @return void
     */
    public function testPreparesForm()
    {
        $this->_mockHelper();
        $this->_mockSession();

        $wysiwygConfig = new Varien_Object;
        $wysiwyg = $this->getModelMock('cms/wysiwyg_config', ['getConfig']);
        $wysiwyg->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($wysiwygConfig));
        $this->replaceByMock('singleton', 'cms/wysiwyg_config', $wysiwyg);

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_form',
            ['_initFormValues', '_toHtml', '_afterHtml']);

        $block->toHtml();

        /** @var Varien_Data_Form $form */
        $form = $block->getForm();

        $this->assertEquals('post_', $form->getHtmlIdPrefix());
        $this->assertEquals('post', $form->getFieldNameSuffix());

        $fieldset = $form->getElement('post_form');
        $this->assertEquals('Post', $fieldset->getLegend());

        $this->assertEquals([
            'label'     => 'Title',
            'name'      => 'title',
            'required'  => true,
            'class'     => 'required-entry',
            'type'      => 'text',
            'ext_type'  => 'textfield',
            'html_id'   => 'title',
            'container' => $fieldset
        ], $form->getElement('title')->getData());

        $this->assertEquals([
            'label'     => 'Text',
            'name'      => 'text',
            'required'  => true,
            'class'     => 'required-entry',
            'type'      => 'textarea',
            'ext_type'  => 'textarea',
            'config'    => $wysiwygConfig,
            'html_id'   => 'text',
            'container' => $fieldset,
            'rows'      => 2,
            'cols'      => 15
        ], $form->getElement('text')->getData());

        $this->assertEquals([
            'label'     => 'Url key',
            'name'      => 'url_key',
            'type'      => 'text',
            'ext_type'  => 'textfield',
            'html_id'   => 'url_key',
            'note'      => 'Relative to Website Base URL',
            'container' => $fieldset
        ], $form->getElement('url_key')->getData());

        $this->assertEquals([
            'label'     => 'Status',
            'name'      => 'status',
            'type'      => 'select',
            'ext_type'  => 'combobox',
            'html_id'   => 'status',
            'values'    => [
                [
                    'value' => 1,
                    'label' => 'Enabled'
                ],
                [
                    'value' => 0,
                    'label' => 'Disabled'
                ]
            ],
            'container' => $fieldset
        ], $form->getElement('status')->getData());
    }

    /**
     * Test initializations form values
     *
     * @return void
     */
    public function testInitializationsFormValues()
    {
        $this->_mockHelper();
        $this->_mockSession();

        $form = $this->getMock('Varien_Object', ['setValues']);

        $form->expects($this->once())
            ->method('setValues')
            ->with($this->equalTo([
                'title' => 'Foo'
            ]));

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_form',
            ['_toHtml', '_afterHtml', 'getForm', '_prepareForm']);

        $block->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form));

        $post = $this->getModelMock('news/post', ['getDefaultValues', 'getData']);

        $post->expects($this->once())
            ->method('getDefaultValues')
            ->will($this->returnValue(null));

        $post->expects($this->once())
            ->method('getData')
            ->will($this->returnValue([
                'title' => 'Foo'
            ]));

        Mage::unregister('current_post');
        Mage::register('current_post', $post);

        $block->toHtml();
    }

    /**
     * Test initializations form values from session
     *
     * @return void
     */
    public function testInitializationsFormValuesFromSession()
    {
        $this->_mockHelper();
        $this->_mockSession();

        $session = $this->getModelMock('adminhtml/session', ['getPostData', 'setPostData']);

        $session->expects($this->once())
            ->method('getPostData')
            ->will($this->returnValue([
                'title' => 'Foo'
            ]));

        $session->expects($this->once())
            ->method('setPostData')
            ->with($this->equalTo(null));

        $this->replaceByMock('singleton', 'adminhtml/session', $session);

        $form = $this->getMock('Varien_Object', ['setValues']);

        $form->expects($this->once())
            ->method('setValues')
            ->with($this->equalTo([
                'title' => 'Foo'
            ]));

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_form',
            ['_toHtml', '_afterHtml', 'getForm', '_prepareForm']);

        $block->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form));

        $post = $this->getModelMock('news/post', ['getDefaultValues']);

        $post->expects($this->once())
            ->method('getDefaultValues')
            ->will($this->returnValue(null));

        Mage::unregister('current_post');
        Mage::register('current_post', $post);

        $block->toHtml();
    }

    /**
     * Mock helper for translations
     *
     * @return void
     */
    protected function _mockHelper()
    {
        $helper = $this->getHelperMock('news/data', ['__']);

        $helper->expects($this->any())
            ->method('__')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $this->replaceByMock('helper', 'news', $helper);
    }

    /**
     * Mock session method start
     *
     * @return void
     */
    protected function _mockSession()
    {
        $this->replaceByMock('singleton', 'core/session', $this->getModelMock('core/session', ['start']));
    }
}
