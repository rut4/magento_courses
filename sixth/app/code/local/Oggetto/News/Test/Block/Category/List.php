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
 * Category list block test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Category_List extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_News_Block_Category_List',
            $this->getBlockMock('news/category_list'));
    }

    /**
     * Test initializations itself with category collection
     * 
     * @return void
     */
    public function testInitializationsItselfWithCategoryCollection()
    {
        $categories = $this->getResourceModelMock('news/post_collection', ['addFieldToFilter', 'setOrder']);

        $categories->expects($this->once())
            ->method('addFieldToFilter')
            ->with(
                $this->equalTo('status'),
                $this->equalTo(1)
            )
            ->will($this->returnSelf());

        $categories->expects($this->once())
            ->method('setOrder')
            ->with(
                $this->equalTo('main_table.position'),
                $this->equalTo('asc')
            );

        $this->replaceByMock('resource_model', 'news/category_collection', $categories);

        $block = new Oggetto_News_Block_Category_List;

        $this->assertEquals($categories, $block->getCategories());
    }

    /**
     * Test prepare only the first level categories while preparing layout
     * 
     * @return void
     */
    public function testPrepareOnlyTheFirstLevelCategoriesWhilePreparingLayout()
    {
        $categories = $this->getResourceModelMock('news/category_collection', ['addFieldToFilter']);

        $categories->expects($this->once())
            ->method('addFieldToFilter')
            ->with($this->equalTo('level'), $this->equalTo(1));

        $block = $this->getMockBuilder('Oggetto_News_Block_Category_List')
            ->disableOriginalConstructor()
            ->setMethods(['getCategories', 'getDisplayMode'])
            ->getMock();

        $block->expects($this->once())
            ->method('getCategories')
            ->will($this->returnValue($categories));

        $block->expects($this->once())
            ->method('getDisplayMode')
            ->will($this->returnValue(1));

        $block->setLayout(new Mage_Core_Model_Layout());
    }

    /**
     * Test add pager block if should display categories as list
     *
     * @return void
     */
    public function testAddPagerBlockIfShouldDisplayCategoriesAsList()
    {
        $block = $this->getMockBuilder('Oggetto_News_Block_Category_List')
            ->disableOriginalConstructor()
            ->setMethods(['getDisplayMode', 'getCategories', 'getLayout', 'createBlock', 'setChild'])
            ->getMock();

        $block->expects($this->once())
            ->method('getDisplayMode')
            ->will($this->returnValue(0));

        $categories = $this->getResourceModelMock('news/category_collection', ['load']);

        $categories->expects($this->once())
            ->method('load');

        $block->expects($this->any())
            ->method('getCategories')
            ->will($this->returnValue($categories));

        $block->expects($this->once())
            ->method('getLayout')
            ->will($this->returnSelf());

        $pager = $this->getMock('Mage_Page_Block_Html_Pager', ['setCollection']);

        $pager->expects($this->once())
            ->method('setCollection')
            ->with($this->equalTo($categories))
            ->will($this->returnSelf());

        $block->expects($this->once())
            ->method('createBlock')
            ->with(
                $this->equalTo('page/html_pager'),
                $this->anything()
            )
            ->will($this->returnValue($pager));

        $block->expects($this->once())
            ->method('setChild')
            ->with(
                $this->equalTo('pager'),
                $this->equalTo($pager)
            );

        $block->setLayout(new Mage_Core_Model_Layout);
    }

    /**
     * Test returns pager html
     *
     * @return void
     */
    public function testReturnsPagerHtml()
    {
        $block = $this->getBlockMock('news/category_list', ['getChildHtml']);

        $pager = new Mage_Page_Block_Html_Pager;

        $block->expects($this->once())
            ->method('getChildHtml')
            ->with($this->equalTo('pager'))
            ->will($this->returnValue($pager));

        $this->assertEquals($pager, $block->getPagerHtml());
    }

    /**
     * Test gets display mode from helper
     * 
     * @return void
     */
    public function testGetsDisplayModeFromHelper()
    {
        $mode = 1;

        $helper = $this->getHelperMock('news/category', ['getDisplayMode']);

        $helper->expects($this->once())
            ->method('getDisplayMode')
            ->will($this->returnValue($mode));

        $this->replaceByMock('helper', 'news/category', $helper);

        $block = new Oggetto_News_Block_Category_List;

        $this->assertEquals($mode, $block->getDisplayMode());
    }

    /**
     * Test returns recursion level from helper
     * 
     * @return void
     */
    public function testReturnsRecursionLevelFromHelper()
    {
        $recursion = 0;

        $helper = $this->getHelperMock('news/category', ['getRecursion']);

        $helper->expects($this->once())
            ->method('getRecursion')
            ->will($this->returnValue($recursion));

        $this->replaceByMock('helper', 'news/category', $helper);

        $block = new Oggetto_News_Block_Category_List;

        $this->assertEquals($recursion, $block->getRecursion());
    }

    /**
     * Test does not draw disabled category
     *
     * @return void
     */
    public function testDoesNotDrawDisabledCategory()
    {
        $category = $this->getModelMock('news/category', ['getStatus']);

        $category->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue(0));

        $block = new Oggetto_News_Block_Category_List;

        $this->assertEquals('', $block->drawCategory($category));
    }


    /**
     * Test does not draw category when level greater than recursion
     *
     * @return void
     */
    public function testDoesNotDrawCategoryWithLevelGreaterThanRecursion()
    {
        $recursion = 2;
        $level = 3;

        $category = $this->getModelMock('news/category', ['getStatus']);

        $category->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue(1));

        $block = $this->getBlockMock('news/category_list', ['getRecursion']);

        $block->expects($this->once())
            ->method('getRecursion')
            ->will($this->returnValue($recursion));

        $this->assertEquals('', $block->drawCategory($category, $level));
    }
}
