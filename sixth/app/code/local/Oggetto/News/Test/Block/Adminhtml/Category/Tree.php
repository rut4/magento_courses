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
 * Category admin tree block test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Adminhtml_Category_Tree extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test initializations itself in constructor
     *
     * @return void
     */
    public function testInitializationsItselfInConstructor()
    {
        $this->replaceByMock('singleton', 'core/session', $this->getModelMock('core/session', ['start']));
        
        $block = $this->getMockBuilder('Oggetto_News_Block_Adminhtml_Category_Tree')
            ->disableOriginalConstructor()
            ->setMethods(['setTemplate', 'setUseAjax'])
            ->getMock();

        $block->expects($this->once())
            ->method('setTemplate')
            ->with($this->equalTo('news/category/tree.phtml'));
        
        $block->expects($this->once())
            ->method('setUseAjax')
            ->with($this->equalTo(true));

        $reflected = new ReflectionClass('Oggetto_News_Block_Adminhtml_Category_Tree');
        $constructor = $reflected->getConstructor();
        $constructor->invoke($block);
    }

    /**
     * Test prepares layout
     *
     * @return void
     */
    public function testPreparesLayout()
    {
        $block = $this->getBlockMock('news/adminhtml_category_tree', ['getUrl', 'getLayout', 'setChild']);
        
        $block->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('*/*/add'), $this->equalTo([
                '_current' => true,
                'id'       => null,
                '_query'   => false
            ]))
            ->will($this->returnValue('foo/bar/add'));

        $layout = $this->getModelMock('core/layout', ['createBlock']);

        $button = $this->getMock('Varien_Object', ['setData']);

        $button->expects($this->at(0))
            ->method('setData')
            ->with($this->equalTo([
                'label' => 'Add Child Category',
                'onclick' => "addNew('foo/bar/add', false)",
                'class' => 'add',
                'id' => 'add_child_category_button',
                'style' => ''
            ]))
            ->will($this->returnSelf());

        $button->expects($this->at(1))
            ->method('setData')
            ->with($this->equalTo([
                'label' => 'Add Root Category',
                'onclick' => "addNew('foo/bar/add', true)",
                'class' => 'add',
                'id' => 'add_root_category_button'
            ]))
            ->will($this->returnSelf());

        $layout->expects($this->any())
            ->method('createBlock')
            ->with($this->equalTo('adminhtml/widget_button'))
            ->will($this->returnValue($button));

        $block->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $block->expects($this->at(2))
            ->method('setChild')
            ->with(
                $this->equalTo('add_sub_button'),
                $this->equalTo($button)
            );

        $block->expects($this->at(4))
            ->method('setChild')
            ->with(
                $this->equalTo('add_root_button'),
                $this->equalTo($button)
            );

        $block->setLayout($layout);
    }
}
