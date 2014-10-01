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
 * Related entities column renderer test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Adminhtml_Helper_Column_Renderer_Relation extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test renders relation
     *
     * @return void
     */
    public function testRendersRelation()
    {
        $post = $this->getModelMock('news/post', ['getId']);

        $post->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $column = $this->getMock('Varien_Object', ['getBaseLink', 'getData']);

        $column->expects($this->once())
            ->method('getBaseLink')
            ->will($this->returnValue('foo/bar/'));

        $column->expects($this->at(1))
            ->method('getData')
            ->with($this->equalTo('params'))
            ->will($this->returnValue([
                'id' => 'getId'
            ]));

        $column->expects($this->at(2))
            ->method('getData')
            ->with($this->equalTo('static'))
            ->will($this->returnValue([
                'status' => 1
            ]));

        $block = $this->getBlockMock('news/adminhtml_helper_column_renderer_relation',
            ['getColumn', 'getUrl', '_getValue']);

        $block->expects($this->once())
            ->method('_getValue')
            ->with($this->equalTo($post))
            ->will($this->returnValue('PostTitle'));

        $block->expects($this->any())
            ->method('getColumn')
            ->will($this->returnValue($column));

        $block->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo('foo/bar/'),
                $this->equalTo([
                    'id'     => 42,
                    'status' => 1
                ])
            )
            ->will($this->returnValue('foo/bar/post/id/42'));

        $this->assertEquals('<a href="foo/bar/post/id/42" target="_blank">PostTitle</a>', $block->render($post));
    }

    /**
     * Test renders relation
     *
     * @return void
     */
    public function testRendersRelationWithoutBaseUrl()
    {
        $post = $this->getModelMock('news/post', ['getTitle']);

        $post->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue('postTitle'));

        $column = $this->getMock('Varien_Object', ['getBaseLink', 'getEditable', 'getEditOnly', 'getGetter']);

        $column->expects($this->once())
            ->method('getBaseLink')
            ->will($this->returnValue(null));

        $column->expects($this->once())
            ->method('getEditable')
            ->will($this->returnValue(true));

        $column->expects($this->once())
            ->method('getEditOnly')
            ->will($this->returnValue(false));

        $column->expects($this->any())
            ->method('getGetter')
            ->will($this->returnValue('getTitle'));

        $block = $this->getBlockMock('news/adminhtml_helper_column_renderer_relation',
            ['getColumn']);

        $block->expects($this->any())
            ->method('getColumn')
            ->will($this->returnValue($column));

        $this->assertEquals(
            'postTitle<input type="text" class="input-text " name="" value="postTitle"/>',
            $block->render($post));
    }
}
