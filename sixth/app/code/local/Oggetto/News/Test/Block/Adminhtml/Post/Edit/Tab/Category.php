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
 * Post - category relation edit block test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Adminhtml_Post_Edit_Tab_Category extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test sets template in constructor
     *
     * @return void
     */
    public function testSetsTemplateInConstructor()
    {
        $block = new Oggetto_News_Block_Adminhtml_Post_Edit_Tab_Category;

        $this->assertEquals('news/post/edit/tab/category.phtml', $block->getTemplate());
    }

    /**
     * Test returns category ids as string
     *
     * @return void
     */
    public function testReturnsCategoryIdsAsString()
    {
        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category', ['getCategoryIds']);

        $block->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue([42, 11, 15]));

        $this->assertEquals('42,11,15', $block->getIdsString());
    }

    /**
     * Test returns category ids
     *
     * @return void
     */
    public function testReturnsCategoryIds()
    {
        $categoryCollection = new Varien_Data_Collection();

        $categoryCollection->addItem(new Varien_Object(['id' => 42]));
        $categoryCollection->addItem(new Varien_Object(['id' => 11]));
        $categoryCollection->addItem(new Varien_Object(['id' => 15]));

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category', ['getPost']);

        $post = $this->getModelMock('news/post', ['getSelectedCategories']);

        $post->expects($this->once())
            ->method('getSelectedCategories')
            ->will($this->returnValue($categoryCollection));

        $block->expects($this->once())
            ->method('getPost')
            ->will($this->returnValue($post));

        $this->assertEquals([42, 11, 15], $block->getCategoryIds());
    }

    /**
     * Test returns current post
     *
     * @return void
     */
    public function testReturnsCurrentPost()
    {
        $post = Mage::getModel('news/post');

        Mage::unregister('current_post');
        Mage::register('current_post', $post);

        $this->assertEquals($post, (new Oggetto_News_Block_Adminhtml_Post_Edit_Tab_Category)->getPost());
    }

    /**
     * Test returns root node
     *
     * @return void
     */
    public function testReturnsRootNode()
    {
        $root = $this->getMock('Varien_Object', ['getId', 'setChecked']);

        $root->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $root->expects($this->once())
            ->method('setChecked')
            ->with($this->equalTo(true));

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category', ['getRoot', 'getCategoryIds']);

        $block->expects($this->once())
            ->method('getRoot')
            ->will($this->returnValue($root));

        $block->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue([42]));

        $this->assertEquals($root, $block->getRootNode());
    }

    /**
     * Test returns root with recursion
     *
     * @return void
     */
    public function testReturnsRootWithRecursion()
    {
        $parent = $this->getMock('Varien_Object', ['getId']);

        $parent->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $recursion = 4;

        $root = new Varien_Object;

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category', ['getNode']);

        $block->expects($this->once())
            ->method('getNode')
            ->with($this->equalTo($parent), $this->equalTo($recursion))
            ->will($this->returnValue($root));

        $this->assertEquals($root, $block->getRoot($parent, $recursion));
    }

    /**
     * Test returns root from register without recursion
     *
     * @return void
     */
    public function testReturnsRootFromRegisterWithoutRecursion()
    {
        Mage::unregister('category_root');

        $root = new Varien_Object;

        Mage::register('category_root', $root);

        $this->assertEquals($root, (new Oggetto_News_Block_Adminhtml_Post_Edit_Tab_Category)->getRoot());
    }

    /**
     * Test returns root without recursion
     *
     * @return void
     */
    public function testReturnsRootWithoutRecursion()
    {
        Mage::unregister('category_root');
        $rootId = 42;

        $helper = $this->getHelperMock('news/category', ['getRootCategoryId']);

        $helper->expects($this->any())
            ->method('getRootCategoryId')
            ->will($this->returnValue($rootId));

        $this->replaceByMock('helper', 'news/category', $helper);

        $category = Mage::getModel('news/category');

        $categoryCollection = Mage::getResourceModel('news/category_collection');

        $rootNode = new Varien_Object;

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category',
            ['getSelectedCategoryPathIds', 'getCategory', 'getCategoryCollection']);

        $block->expects($this->once())
            ->method('getSelectedCategoryPathIds')
            ->with($this->equalTo($rootId))
            ->will($this->returnValue([$rootId]));

        $block->expects($this->any())
            ->method('getCategory')
            ->will($this->returnValue($category));

        $block->expects($this->once())
            ->method('getCategoryCollection')
            ->will($this->returnValue($categoryCollection));

        $tree = $this->getResourceModelMock('news/category_tree',
            ['loadByIds', 'loadEnsuredNodes', 'addCollectionData', 'getNodeById']);

        $tree->expects($this->once())
            ->method('loadByIds')
            ->with($this->equalTo([$rootId]), $this->equalTo(false), $this->equalTo(false))
            ->will($this->returnSelf());
        
        $tree->expects($this->once())
            ->method('loadEnsuredNodes')
            ->with($this->equalTo($category), $this->equalTo($rootNode));
        
        $tree->expects($this->once())
            ->method('addCollectionData')
            ->with($this->equalTo($categoryCollection));

        $tree->expects($this->any())
            ->method('getNodeById')
            ->with($this->equalTo($rootId))
            ->will($this->returnValue($rootNode));

        $this->replaceByMock('resource_singleton', 'news/category_tree', $tree);

        $this->assertEquals($rootNode, $block->getRoot());
        $this->assertEquals($rootNode, Mage::registry('category_root'));
    }

    /**
     * Test returns empty array if selected category path ids is empty
     *
     * @return void
     */
    public function testReturnsEmptyArrayIfSelectedCategoryPathIdsIsEmpty()
    {
        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category', ['getCategoryIds']);

        $block->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue([]));

        $this->assertEquals([], $block->getSelectedCategoryPathIds());
    }
    
    /**
     * Test returns selected category path ids
     * 
     * @return void
     */
    public function testReturnsSelectedCategoryPathIds()
    {
        $categoryIds = [11];

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category', ['getCategoryIds']);

        $block->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue($categoryIds));

        $collection = $this->getMock('Varien_Data_Collection', ['addFieldToFilter']);

        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with($this->equalTo('entity_id'), $this->equalTo(['in' => $categoryIds]));

        $this->replaceByMock('resource_model', 'news/category_collection', $collection);

        $item = $this->getModelMock('news/category', ['getPathIds']);

        $item->expects($this->once())
            ->method('getPathIds')
            ->will($this->returnValue($categoryIds));

        $collection->addItem($item);

        $this->assertEquals($categoryIds, $block->getSelectedCategoryPathIds());
    }

    /**
     * Test returns selected category path ids with root id
     *
     * @return void
     */
    public function testReturnsSelectedCategoryPathIdsWithRootId()
    {
        $rootId = 42;
        $pathIds = [42];

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category', ['getCategoryIds']);

        $block->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue($pathIds));

        $collection = $this->getMock('Varien_Data_Collection', ['addFieldToFilter']);

        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with($this->equalTo('parent_id'), $this->equalTo($rootId));

        $this->replaceByMock('resource_model', 'news/category_collection', $collection);

        $item = $this->getModelMock('news/category', ['getPathIds']);

        $item->expects($this->any())
            ->method('getPathIds')
            ->will($this->returnValue($pathIds));

        $collection->addItem($item);

        $this->assertEquals($pathIds, $block->getSelectedCategoryPathIds($rootId));
    }

    /**
     * Test returns selected category path ids with root id that is not in path
     *
     * @return void
     */
    public function testReturnsSelectedCategoryPathIdsWithRootIdThatIsNotInPath()
    {
        $rootId = 42;
        $pathIds = [42];

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category', ['getCategoryIds']);

        $block->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue($pathIds));

        $collection = $this->getMock('Varien_Data_Collection', ['addFieldToFilter']);

        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with($this->equalTo('parent_id'), $this->equalTo($rootId));

        $this->replaceByMock('resource_model', 'news/category_collection', $collection);

        $item = $this->getModelMock('news/category', ['getPathIds']);

        $item->expects($this->any())
            ->method('getPathIds')
            ->will($this->returnValue([]));

        $collection->addItem($item);

        $this->assertEquals([], $block->getSelectedCategoryPathIds($rootId));
    }

    /**
     * Test returns URL for loading tree
     *
     * @return void
     */
    public function testReturnsUrlForLoadingTree()
    {
        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category', ['getUrl']);

        $block->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('*/*/categoriesJson'), $this->equalTo(['_current' => true]))
            ->will($this->returnValue('foo/bar/categoriesJson'));

        $this->assertEquals('foo/bar/categoriesJson', $block->getLoadTreeUrl());
    }

    /**
     * Test returns node label
     *
     * @param int    $nodeId   Node id
     * @param string $nodeName Node name
     * @dataProvider dataProvider
     * @return void
     */
    public function testReturnsNodeLabel($nodeId, $nodeName)
    {
        $this->_mockHelper();

        $node = $this->getMock('Varien_Object', ['getId', 'getName']);

        $node->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($nodeId));

        $node->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($nodeName));

        $block = $this->getBlockMock('news/adminhtml_post_edit_tab_category', ['getUrl', 'escapeHtml']);

        $block->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo('adminhtml/news_category/index'),
                $this->equalTo(['id' => $node->getId(), 'clear' => 1])
            )
            ->will($this->returnValue('adminhtml/news_category/index/id/' . $nodeId . '/clear/1'));

        $block->expects($this->any())
            ->method('escapeHtml')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $expected = $nodeName . '<a target="_blank" href="'
            . 'adminhtml/news_category/index/id/' . $nodeId . '/clear/1'
            . '"><em> - Edit</em></a>';

        $this->assertEquals($expected, $block->buildNodeName($node));
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
}
