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
 * Post related category list block test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Post_Category_List extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_News_Block_Post_Category_List',
            $this->getBlockMock('news/post_category_list'));
    }

    /**
     * Test initializations itself with post related categories
     *
     * @return void
     */
    public function testInitializationsItselfWithPostRelatedCategories()
    {
        $postId = 42;

        $block = $this->getMockBuilder('Oggetto_News_Block_Post_Category_List')
            ->setMethods(['getPost', 'getCategories'])
            ->disableOriginalConstructor()
            ->getMock();

        $post = $this->getModelMock('news/post', ['getId']);

        $post->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($postId));

        $block->expects($this->once())
            ->method('getPost')
            ->will($this->returnValue($post));

        $categories = $this->getResourceModelMock('news/category_collection', ['addPostFilter', 'unshiftOrder']);

        $categories->expects($this->once())
            ->method('addPostFilter')
            ->with($this->equalTo($postId));

        $categories->expects($this->once())
            ->method('unshiftOrder')
            ->with(
                $this->equalTo('related_post.position'),
                $this->equalTo('ASC')
            );

        $block->expects($this->any())
            ->method('getCategories')
            ->will($this->returnValue($categories));

        $reflected = new ReflectionClass('Oggetto_News_Block_Post_Category_List');
        $constructor = $reflected->getConstructor();
        $constructor->invoke($block);
    }

    /**
     * Test stubs parent prepare layout method
     *
     * @return void
     */
    public function testStubsParentPrepareLayoutMethod()
    {
        $block = $this->getMockBuilder('Oggetto_News_Block_Post_Category_List')
            ->setMethods(['getCategories'])
            ->disableOriginalConstructor()
            ->getMock();

        $block->expects($this->never())
            ->method('getCategories');

        $this->assertEquals($block, $block->setLayout(new Mage_Core_Model_Layout()));
    }

    /**
     * Test returns current post from register
     *
     * @return void
     */
    public function testReturnsCurrentPostFromRegister()
    {
        $block = new Oggetto_News_Block_Post_Category_List;

        $post = Mage::getModel('news/post');
        Mage::unregister('current_post');
        Mage::register('current_post', $post);

        $this->assertEquals($block->getPost(), $post);
    }
}
