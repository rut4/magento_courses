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
 * Post grid test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Adminhtml_Post_Grid extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test initializations itself in constructor
     *
     * @return void
     */
    public function testInitializationsItselfInConstructor()
    {
        $block = $this->getMockBuilder('Oggetto_News_Block_Adminhtml_Post_Grid')
            ->disableOriginalConstructor()
            ->setMethods(['setId', 'setDefaultSort', 'setDefaultDir', 'setSaveParametersInSession', 'setUseAjax'])
            ->getMock();

        $block->expects($this->once())
            ->method('setId')
            ->with($this->equalTo('postGrid'));

        $block->expects($this->once())
            ->method('setDefaultSort')
            ->with($this->equalTo('entity_id'));

        $block->expects($this->once())
            ->method('setDefaultDir')
            ->with($this->equalTo('ASC'));

        $block->expects($this->once())
            ->method('setSaveParametersInSession')
            ->with($this->equalTo(true));

        $block->expects($this->once())
            ->method('setUseAjax')
            ->with($this->equalTo(true));

        $reflected = new ReflectionClass('Oggetto_News_Block_Adminhtml_Post_Grid');
        $constructor = $reflected->getConstructor();
        $constructor->invoke($block);
    }
    
    /**
     * Test sets post collection
     * 
     * @return void
     */
    public function testSetsPostCollection()
    {
        $this->replaceByMock('singleton', 'core/session', $this->getModelMock('core/session', ['start']));
        $posts = $this->getResourceModelMock('news/post_collection', ['walk']);

        $posts->expects($this->once())
            ->method('walk')
            ->with($this->equalTo('afterLoad'));

        $post = $this->getModelMock('news/post', ['getCollection']);
        
        $post->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($posts));

        $this->replaceByMock('model', 'news/post', $post);

        $block = $this->getBlockMock('news/adminhtml_post_grid',
            ['_prepareColumns', '_prepareMassaction', 'getCollection', '_prepareMassactionBlock']);

        $block->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($posts));

        $block->toHtml();
    }

    /**
     * Test prepares columns and export types
     *
     * @loadExpectation
     * @return void
     */
    public function testPreparesColumnsAndExportTypes()
    {
        $this->_mockHelper();

        $this->replaceByMock('singleton', 'core/session', $this->getModelMock('core/session', ['start']));

        $block = $this->getBlockMock('news/adminhtml_post_grid',
            [
                'addColumn',
                '_prepareCollection',
                '_prepareMassaction',
                '_prepareMassactionBlock',
                '_afterLoadCollection',
                'addExportType'
            ]);

        for ($i = 0; $i < 6; $i++) {
            $block->expects($this->at($i))
                ->method('addColumn')
                ->with(
                    $this->equalTo($this->expected($i)->getId()),
                    $this->equalTo($this->expected($i)->getParams())
                );
        }

        $block->expects($this->at(6))
            ->method('addExportType')
            ->with($this->equalTo('*/*/exportCsv'), $this->equalTo('CSV'));


        $block->expects($this->at(7))
            ->method('addExportType')
            ->with($this->equalTo('*/*/exportExcel'), $this->equalTo('Excel'));


        $block->expects($this->at(8))
            ->method('addExportType')
            ->with($this->equalTo('*/*/exportXml'), $this->equalTo('XML'));

        $block->toHtml();
    }

    /**
     * Test prepares massaction
     *
     * @return void
     */
    public function testPreparesMassaction()
    {
        $this->_mockHelper();

        $block = $this->getBlockMock('news/adminhtml_post_grid',
            [
                'addColumn',
                '_prepareCollection',
                '_prepareColumns',
                '_afterLoadCollection',
                'setMassactionIdField',
                'getMassactionBlock',
                'getMassactionBlockName',
                'getLayout',
                'setChild',
                'getUrl',
                '_prepareMassactionColumn'
            ]);

        $massBlock = $this->_prepareMassactionMock();

        $layout = $this->getModelMock('core/layout', ['createBlock']);

        $block->expects($this->any())
            ->method('setChild')
            ->with(
                $this->equalTo('massaction'),
                $this->equalTo($massBlock)
            );

        $block->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $layout->expects($this->any())
            ->method('createBlock')
            ->with($this->equalTo('massaction'))
            ->will($this->returnValue($massBlock));

        $block->expects($this->once())
            ->method('getMassactionBlockName')
            ->will($this->returnValue('massaction'));

        $block->expects($this->any())
            ->method('getMassactionBlock')
            ->will($this->returnValue($massBlock));

        $block->expects($this->any())
            ->method('getUrl')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $block->expects($this->once())
            ->method('setMassactionIdField')
            ->with($this->equalTo('entity_id'));

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
     * Prepare mass action block mock
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareMassactionMock()
    {
        $massBlock = $this->getBlockMock('adminhtml/widget_grid_massaction',
            ['setFormFieldName', 'addItem', 'isAvailable']);

        $massBlock->expects($this->once())
            ->method('isAvailable')
            ->will($this->returnValue(true));

        $massBlock->expects($this->once())
            ->method('setFormFieldName')
            ->with($this->equalTo('post'));

        $massBlock->expects($this->at(1))
            ->method('addItem')
            ->with(
                $this->equalTo('delete'),
                $this->equalTo([
                    'label'   => 'Delete',
                    'url'     => '*/*/massDelete',
                    'confirm' => 'Are you sure?'
                ])
            );

        $massBlock->expects($this->at(2))
            ->method('addItem')
            ->with(
                $this->equalTo('status'),
                $this->equalTo([
                    'label'      => 'Change status',
                    'url'        => '*/*/massStatus',
                    'additional' => [
                        'status' => [
                            'name'   => 'status',
                            'type'   => 'select',
                            'class'  => 'required-entry',
                            'label'  => 'Status',
                            'values' => [
                                '1' => 'Enabled',
                                '0' => 'Disabled'
                            ]
                        ]
                    ]
                ])
            );
        return $massBlock;
    }

    /**
     * Test return grid row url
     *
     * @return void
     */
    public function testReturnsGridRowUrl()
    {
        $post = $this->getModelMock('news/post', ['getId']);

        $post->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $block = $this->getBlockMock('news/adminhtml_post_grid', ['getUrl']);

        $block->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo('*/*/edit'),
                $this->equalTo([
                    'id' => 42
                ])
            )
            ->will($this->returnValue('foo'));

        $this->assertEquals('foo', $block->getRowUrl($post));
    }

    /**
     * Test returns grid url
     *
     * @return void
     */
    public function testReturnsGridUrl()
    {
        $block = $this->getBlockMock('news/adminhtml_post_grid', ['getUrl']);

        $block->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo('*/*/grid'),
                $this->equalTo([
                    '_current' => true
                ])
            )
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $block->getGridUrl());
    }
}
