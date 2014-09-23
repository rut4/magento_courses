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
 * Test post model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Model_Category extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_News_Model_Category', Mage::getModel('news/category'));
    }

    /**
     * Test is initialized with resource model
     *
     * @return void
     */
    public function testIsInitializedWithResourceModel()
    {
        $this->assertInstanceOf('Oggetto_News_Model_Resource_Category', Mage::getModel('news/category')->getResource());
    }

    /**
     * Test sets "created at" date while saving first time
     *
     * @return void
     */
    public function testSetsCreatedAtDateWhileSavingFirstTime()
    {
        $date = Mage::getModel('core/date')->gmtDate();

        $dateMock = $this->_prepareDateMock($date);

        $this->replaceByMock('singleton', 'core/date', $dateMock);

        $category = $this->_prepareCategoryMock($date);

        $category->expects($this->once())
            ->method('setCreatedAt')
            ->with($this->equalTo($date));

        $category->expects($this->any())
            ->method('isObjectNew')
            ->will($this->returnValue(true));

        $category->save();
    }

    /**
     * Test sets "updated at" date while saving any more first time
     *
     * @return void
     */
    public function testSetsUpdatedAtDateWhileSavingAnyMoreFirstTime()
    {
        $date = Mage::getModel('core/date')->gmtDate();

        $dateMock = $this->_prepareDateMock($date);

        $this->replaceByMock('singleton', 'core/date', $dateMock);

        $category = $this->_prepareCategoryMock($date);

        $category->expects($this->any())
            ->method('isObjectNew')
            ->will($this->returnValue(false));

        $category->save();
    }

    /**
     * Prepare category mock
     *
     * @param string $date Gmt date
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareCategoryMock($date)
    {
        $category = $this->getModelMock(
            'news/category',
            ['isObjectNew', 'setCreatedAt', 'setUpdatedAt', '_hasModelChanged']
        );

        $category->expects($this->once())
            ->method('_hasModelChanged')
            ->will($this->returnValue(true));

        $category->expects($this->once())
            ->method('setUpdatedAt')
            ->with($this->equalTo($date));

        return $category;
    }

    /**
     * Prepare date mock
     *
     * @param string $date Gmt date
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareDateMock($date)
    {
        $dateMock = $this->getModelMock('core/date', ['gmtDate']);

        $dateMock->expects($this->once())
            ->method('gmtDate')
            ->will($this->returnValue($date));

        return $dateMock;
    }
    
    /**
     * Test returns category url with specified url path
     * 
     * @param int    $callNumber Call number
     * @param string $urlPath    Url path
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testReturnsCategoryUrlWithSpecifiedUrlPath($callNumber, $urlPath)
    {
        $category = $this->getModelMock('news/category', ['getUrlPath']);

        $category->expects($this->any())
            ->method('getUrlPath')
            ->will($this->returnValue($urlPath));

        $url = $this->getModelMock('core/url', ['getDirectUrl']);

        $url->expects($this->once())
            ->method('getDirectUrl')
            ->with($this->equalTo($urlPath))
            ->will($this->returnValue($urlPath));

        $this->replaceByMock('model', 'core/url', $url);

        $this->assertEquals($this->expected($callNumber)->getCategoryUrl(), $category->getCategoryUrl());
    }

    /**
     * Test returns category url when url path does not exist
     *
     * @return void
     */
    public function testReturnsCategoryUrlWhenUrlKeyDoesNotExist()
    {
        $session = $this->getModelMock('core/session', ['start']);
        $this->replaceByMock('singleton', 'core/session', $session);

        $category = $this->getModelMock('news/category', ['getUrlPath', 'getId']);

        $category->expects($this->once())
            ->method('getUrlPath')
            ->will($this->returnValue(null));

        $category->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $this->assertEquals(Mage::getBaseUrl() . 'news/category/view/id/42/', $category->getCategoryUrl());
    }

    /**
     * Test checks url key via resource model
     *
     * @param int    $callNumber Call number
     * @param string $urlKey     Url key
     * @param bool   $active     Should checks only active posts
     * @param int    $id         Post id
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testChecksUrlKeyViaResourceModel($callNumber, $urlKey, $active, $id)
    {
        $resource = $this->getResourceModelMock('news/category', ['checkUrlKey']);

        $resource->expects($this->once())
            ->method('checkUrlKey')
            ->with(
                $this->equalTo($urlKey),
                $this->equalTo($active)
            )
            ->will($this->returnValue($id));

        $this->replaceByMock('resource_model', 'news/category', $resource);

        $this->assertEquals(
            $this->expected($callNumber)->getCheckResult(),
            Mage::getModel('news/category')->checkUrlKey($urlKey, $active)
        );
    }

    /**
     * Test checks url key via resource model
     *
     * @param int    $callNumber Call number
     * @param string $urlPath    Url path
     * @param bool   $active     Should checks only active posts
     * @param int    $id         Post id
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testChecksUrlPathViaResourceModel($callNumber, $urlPath, $active, $id)
    {
        $resource = $this->getResourceModelMock('news/category', ['checkUrlPath']);

        $resource->expects($this->once())
            ->method('checkUrlPath')
            ->with(
                $this->equalTo($urlPath),
                $this->equalTo($active)
            )
            ->will($this->returnValue($id));

        $this->replaceByMock('resource_model', 'news/category', $resource);

        $this->assertEquals(
            $this->expected($callNumber)->getCheckResult(),
            Mage::getModel('news/category')->checkUrlPath($urlPath, $active)
        );
    }

    /**
     * Test saves relation with post via post instance after save
     *
     * @return void
     */
    public function testSavesRelationWithPostViaPostInstanceAfterSave()
    {
        $category = $this->getModelMock('news/category', ['getPostInstance', '_hasModelChanged']);

        $category->expects($this->once())
            ->method('_hasModelChanged')
            ->will($this->returnValue(true));

        $categoryPost = $this->getModelMock('news/category_post', ['saveCategoryRelation']);

        $categoryPost->expects($this->once())
            ->method('saveCategoryRelation')
            ->with($this->equalTo($category));

        $category->expects($this->once())
            ->method('getPostInstance')
            ->will($this->returnValue($categoryPost));

        $category->save();
    }

    /**
     * Test returns category post instance
     *
     * @return void
     */
    public function testReturnsCategoryPostInstance()
    {
        $this->assertInstanceOf(
            'Oggetto_News_Model_Category_Post',
            Mage::getModel('news/category')->getPostInstance()
        );
    }


    /**
     * Test sets selected posts for category from collection
     *
     * @param int   $callNumber Call number
     * @param array $posts      Posts
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testSetsSelectedPostsForCategoryFromCollection($callNumber, $posts)
    {
        $category = $this->getModelMock('news/category', [
            'hasSelectedPosts',
            'setSelectedPosts',
            'getData',
            'getSelectedPostsCollection'
        ]);

        $category->expects($this->once())
            ->method('hasSelectedPosts')
            ->will($this->returnValue(false));

        $category->expects($this->once())
            ->method('setSelectedPosts')
            ->with($this->equalTo($posts));

        $category->expects($this->once())
            ->method('getData')
            ->with($this->equalTo('selected_posts'))
            ->will($this->returnValue($posts));

        $collection = $this->getResourceModelMock('news/category_post_collection', ['getItems']);

        $collection->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($posts));

        $category->expects($this->once())
            ->method('getSelectedPostsCollection')
            ->will($this->returnValue($collection));

        $this->assertEquals(
            $this->expected($callNumber)->getSelectedPosts(),
            $category->getSelectedPosts()
        );
    }

    /**
     * Test retrieves selected post collection from category post instance
     *
     * @return void
     */
    public function testRetrievesSelectedPostCollectionFromCategoryPostInstance()
    {
        $post = $this->getModelMock('news/category', ['getPostInstance']);

        $postCategory = $this->getModelMock('news/category_post', ['getPostsCollection']);

        $collection = new Varien_Data_Collection_Db;

        $postCategory->expects($this->once())
            ->method('getPostsCollection')
            ->with($this->equalTo($post))
            ->will($this->returnValue($collection));

        $post->expects($this->once())
            ->method('getPostInstance')
            ->will($this->returnValue($postCategory));

        $this->assertEquals($collection, $post->getSelectedPostsCollection());
    }

    /**
     * Test returns tree model resource instance
     *
     * @return void
     */
    public function testReturnsTreeModelResourceInstance()
    {
        $this->assertInstanceOf(
            'Oggetto_News_Model_Resource_Category_Tree',
            Mage::getModel('news/category')->getTreeModel()
        );
    }

    /**
     * Test moves itself in category tree
     *
     * @return void
     */
    public function testMovesItselfInCategoryTree()
    {

        $children = $this->getModelMock('news/category', ['getId', 'move']);

        $children->expects($this->once())
            ->method('move')
            ->with($this->equalTo(42), $this->equalTo(0));

        $children->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(43));

        $parent = $this->getModelMock('news/category', ['getId']);

        $parent->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $category = $this->getModelMock('news/category', [
            'getId',
            'getParentId',
            'getResource',
            'setAffectedCategoryIds',
            'getChildrenCategories',
            'setMovedCategoryId'
        ]);

        $resource = $this->getResourceModelMock('news/category', [
            'beginTransaction',
            'changeParent',
            'commit',
            'updateUrlPath'
        ]);

        $resource->expects($this->atLeastOnce())
            ->method('beginTransaction')
            ->will($this->returnSelf());

        $resource->expects($this->any())
            ->method('changeParent')
            ->with(
                $this->equalTo($category),
                $this->equalTo($parent),
                0
            )
            ->will($this->returnSelf());

        $resource->expects($this->atLeastOnce())
            ->method('commit')
            ->will($this->returnSelf());

        $resource->expects($this->any())
            ->method('updateUrlPath')
            ->with($this->equalTo($category));

        $this->replaceByMock('resource_model', 'news/category', $resource);

        $category->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(42));

        $category->expects($this->once())
            ->method('setMovedCategoryId')
            ->with($this->equalTo(42));

        $category->expects($this->any())
            ->method('getParentId')
            ->will($this->returnValue(2));

        $category->expects($this->any())
            ->method('setAffectedCategoryIds')
            ->with(
                $this->equalTo([
                    42,
                    2,
                    1,
                ])
            )
            ->will($this->returnSelf());

        $category->expects($this->atLeastOnce())
            ->method('getChildrenCategories')
            ->will($this->returnValue([$children]));

        $model = $this->getModelMock('news/category', ['load']);

        $model->expects($this->any())
            ->method('load')
            ->with($this->equalTo(1))
            ->will($this->returnValue($parent));

        $this->replaceByMock('model', 'news/category', $model);

        $this->assertEquals($category, $category->move(1, 0));
    }
    
    /**
     * Test returns parent category
     *
     * @return void
     */
    public function testReturnsParentCategory()
    {
        $parentId = 42;

        $category = $this->getModelMock('news/category', ['getParentId']);

        $category->expects($this->once())
            ->method('getParentId')
            ->will($this->returnValue($parentId));

        $parent = Mage::getModel('news/category', ['entity_id' => $parentId]);

        $model = $this->getModelMock('news/category', ['load']);

        $model->expects($this->once())
            ->method('load')
            ->with($this->equalTo($parentId))
            ->will($this->returnValue($parent));

        $this->replaceByMock('model', 'news/category', $model);

        $this->assertEquals($parent, $category->getParentCategory());
    }


}
