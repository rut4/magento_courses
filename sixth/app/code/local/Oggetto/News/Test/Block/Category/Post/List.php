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
 * Category related post list block test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Category_Post_List extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_News_Block_Category_Post_List',
            $this->getBlockMock('news/category_post_list'));
    }

    /**
     * Test initializations itself with category related posts
     *
     * @return void
     */
    public function testInitializationsItselfWithCategoryRelatedPosts()
    {
        $categoryId = 42;

        $block = $this->getMockBuilder('Oggetto_News_Block_Category_Post_List')
            ->setMethods(['getCategory', 'getPosts'])
            ->disableOriginalConstructor()
            ->getMock();

        $category = $this->getModelMock('news/category', ['getId']);

        $category->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($categoryId));

        $block->expects($this->once())
            ->method('getCategory')
            ->will($this->returnValue($category));

        $posts = $this->getResourceModelMock('news/post_collection', ['addCategoryFilter', 'unshiftOrder']);

        $posts->expects($this->once())
            ->method('addCategoryFilter')
            ->with($this->equalTo($categoryId));

        $posts->expects($this->once())
            ->method('unshiftOrder')
            ->with(
                $this->equalTo('related_category.position'),
                $this->equalTo('ASC')
            );

        $block->expects($this->any())
            ->method('getPosts')
            ->will($this->returnValue($posts));

        $reflected = new ReflectionClass('Oggetto_News_Block_Category_Post_List');
        $constructor = $reflected->getConstructor();
        $constructor->invoke($block);
    }

    /**
     * Test returns post url by category
     *
     * @return void
     */
    public function testReturnsPostUrlByCategory()
    {
        $postUrl = 'foo/bar';

        $block = $this->getMockBuilder('Oggetto_News_Block_Category_Post_List')
            ->setMethods(['getCategory'])
            ->disableOriginalConstructor()
            ->getMock();

        $category = Mage::getModel('news/category');

        $block->expects($this->once())
            ->method('getCategory')
            ->will($this->returnValue($category));

        $post = $this->getModelMock('news/post', ['getPostUrlByCategory']);

        $post->expects($this->once())
            ->method('getPostUrlByCategory')
            ->with($this->equalTo($category))
            ->will($this->returnValue($postUrl));

        $this->assertEquals($postUrl, $block->getPostUrl($post));
    }

    /**
     * Test stubs parent prepare layout method
     *
     * @return void
     */
    public function testStubsParentPrepareLayoutMethod()
    {
        $block = $this->getMockBuilder('Oggetto_News_Block_Category_Post_List')
            ->setMethods(['getPosts'])
            ->disableOriginalConstructor()
            ->getMock();

        $block->expects($this->never())
            ->method('getPosts');

        $this->assertEquals($block, $block->setLayout(new Mage_Core_Model_Layout()));
    }

    /**
     * Test returns current category from register
     *
     * @return void
     */
    public function testReturnsCurrentCategoryFromRegister()
    {
        $block = new Oggetto_News_Block_Category_Post_List;

        $category = Mage::getModel('news/category');
        Mage::unregister('current_category');
        Mage::register('current_category', $category);

        $this->assertEquals($block->getCategory(), $category);
    }
}
