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
 * Post edit tabs test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Adminhtml_Post_Edit_Tabs extends EcomDev_PHPUnit_Test_Case
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

        $block = $this->getMockBuilder('Oggetto_News_Block_Adminhtml_Post_Edit_Tabs')
            ->disableOriginalConstructor()
            ->setMethods(['setId', 'setDestElementId', 'setTitle'])
            ->getMock();

        $block->expects($this->once())
            ->method('setId')
            ->with($this->equalTo('post_tabs'));

        $block->expects($this->once())
            ->method('setDestElementId')
            ->with($this->equalTo('edit_form'));

        $block->expects($this->once())
            ->method('setTitle')
            ->with($this->equalTo('Post'));

        $reflected = new ReflectionClass('Oggetto_News_Block_Adminhtml_Post_Edit_Tabs');
        $constructor = $reflected->getConstructor();
        $constructor->invoke($block);
    }

    /**
     * Test returns current post
     *
     * @return void
     */
    public function testReturnsCurrentPost()
    {
        $block = $this->getMockBuilder('Oggetto_News_Block_Adminhtml_Post_Edit_Tabs')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $post = Mage::getModel('news/post');

        Mage::unregister('current_post');
        Mage::register('current_post', $post);

        $this->assertEquals($post, $block->getPost());
    }
}
