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
 * Post edit test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Adminhtml_Post_Edit extends EcomDev_PHPUnit_Test_Case
{
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
     * Test initializations itself in constructor
     *
     * @return void
     */
    public function testInitializationsItselfInConstructor()
    {
        $this->_mockHelper();

        $this->replaceByMock('singleton', 'core/session', $this->getModelMock('core/session', ['start']));

        $block = $this->getMockBuilder('Oggetto_News_Block_Adminhtml_Post_Edit')
            ->disableOriginalConstructor()
            ->setMethods(['updateButton', 'addButton'])
            ->getMock();

        $block->expects($this->at(0))
            ->method('updateButton')
            ->with(
                $this->equalTo('save'),
                $this->equalTo('label'),
                $this->equalTo('Save Post')
            );

        $block->expects($this->at(1))
            ->method('updateButton')
            ->with(
                $this->equalTo('delete'),
                $this->equalTo('label'),
                $this->equalTo('Delete Post')
            );

        $block->expects($this->once())
            ->method('addButton')
            ->with(
                $this->equalTo('saveandcontinue'),
                $this->equalTo([
                    'label' => 'Save And Continue Edit',
                    'onclick' => 'saveAndContinueEdit()',
                    'class'   => 'save'
                ]),
                $this->lessThan(0)
            );

        $reflected = new ReflectionClass('Oggetto_News_Block_Adminhtml_Post_Edit');
        $constructor = $reflected->getConstructor();
        $constructor->invoke($block);

        $script = "function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";

        $this->assertGreaterThan(0, strpos($block->getFormScripts(), $script));

        $layout = $this->getModelMock('core/layout', ['createBlock']);

        $layout->expects($this->at(0))
            ->method('createBlock')
            ->with(
                $this->equalTo('news/adminhtml_post_edit_form')
            )
            ->will($this->returnValue(new Mage_Core_Block_Template()));

        $block->setLayout($layout);
    }

    /**
     * Test returns header when post is set
     *
     * @return void
     */
    public function testReturnsHeaderWhenPostIsSet()
    {
        $block = $this->getMockBuilder('Oggetto_News_Block_Adminhtml_Post_Edit')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        Mage::unregister('current_post');
        $post = $this->getModelMock('news/post', ['getId', 'getTitle']);
        
        $post->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));
        
        $post->expects($this->once())
            ->method('getTitle')
            ->will($this->returnValue('Foo'));
        
        Mage::register('current_post', $post);

        $helper = $this->getHelperMock('news/data', ['__']);

        $helper->expects($this->once())
            ->method('__')
            ->with($this->equalTo("Edit Post '%s'"), $this->equalTo('Foo'))
            ->will($this->returnValue('Edit Post Foo'));

        $this->replaceByMock('helper', 'news', $helper);

        $this->assertEquals('Edit Post Foo', $block->getHeaderText());
    }

    /**
     * Test returns header when post isn't set
     *
     * @return void
     */
    public function testReturnsHeaderWhenPostIsNotSet()
    {
        $this->_mockHelper();

        $block = $this->getMockBuilder('Oggetto_News_Block_Adminhtml_Post_Edit')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        Mage::unregister('current_post');

        $this->assertEquals('Add Post', $block->getHeaderText());
    }
}
