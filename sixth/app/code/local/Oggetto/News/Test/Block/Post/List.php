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
 * Post list block test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Post_List extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_News_Block_Post_List',
            $this->getBlockMock('news/post_list'));
    }

    /**
     * Test initializations itself with post collection
     * 
     * @return void
     */
    public function testInitializationsItselfWithPostCollection()
    {
        $posts = $this->getResourceModelMock('news/post_collection', ['addFieldToFilter', 'setOrder']);

        $posts->expects($this->once())
            ->method('addFieldToFilter')
            ->with(
                $this->equalTo('status'),
                $this->equalTo(1)
            )
            ->will($this->returnSelf());

        $posts->expects($this->once())
            ->method('setOrder')
            ->with(
                $this->equalTo('created_at'),
                $this->equalTo('asc')
            );

        $this->replaceByMock('resource_model', 'news/post_collection', $posts);

        $block = new Oggetto_News_Block_Post_List;

        $this->assertEquals($posts, $block->getPosts());
    }

    /**
     * Test add pager block while preparing
     *
     * @return void
     */
    public function testAddPagerBlockWhilePreparing()
    {
        $block = $this->getBlockMock('news/post_list',
            ['getLayout', 'createBlock', 'getPosts', 'setChild']);

        $posts = $this->getResourceModelMock('news/post_collection', ['load']);

        $posts->expects($this->once())
            ->method('load');

        $block->expects($this->any())
            ->method('getPosts')
            ->will($this->returnValue($posts));

        $block->expects($this->once())
            ->method('getLayout')
            ->will($this->returnSelf());

        $pager = $this->getMock('Mage_Page_Block_Html_Pager', ['setCollection']);

        $pager->expects($this->once())
            ->method('setCollection')
            ->with($this->equalTo($posts))
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
        $block = $this->getBlockMock('news/post_list', ['getChildHtml']);
        
        $pager = new Mage_Page_Block_Html_Pager;
        
        $block->expects($this->once())
            ->method('getChildHtml')
            ->with($this->equalTo('pager'))
            ->will($this->returnValue($pager));
        
        $this->assertEquals($pager, $block->getPagerHtml());
    }
}
