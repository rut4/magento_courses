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
class Oggetto_News_Test_Model_Post extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_News_Model_Post', Mage::getModel('news/post'));
    }

    /**
     * Test is initialized with resource model
     *
     * @return void
     */
    public function testIsInitializedWithResourceModel()
    {
        $this->assertInstanceOf('Oggetto_News_Model_Resource_Post', Mage::getModel('news/post')->getResource());
    }

    /**
     * Test returns post url by category
     *
     * @param int    $callNumber  The number of calls
     * @param string $urlKey      Url key
     * @param string $categoryUrl Category url
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testReturnsPostUrlByCategory($callNumber, $urlKey, $categoryUrl)
    {
        $post = $this->getModelMock('news/post', ['getPostUrl']);

        $post->expects($this->once())
            ->method('getPostUrl')
            ->will($this->returnValue($urlKey));

        $category = $this->getModelMock('news/category', ['getCategoryUrl']);

        $category->expects($this->once())
            ->method('getCategoryUrl')
            ->will($this->returnValue($categoryUrl));

        $this->assertEquals($this->expected($callNumber)->getPostUrl(), $post->getPostUrlByCategory($category));
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

        $post = $this->_preparePostMock($date);

        $post->expects($this->once())
            ->method('setCreatedAt')
            ->with($this->equalTo($date));

        $post->expects($this->any())
            ->method('isObjectNew')
            ->will($this->returnValue(true));

        $post->save();
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

        $post = $this->_preparePostMock($date);

        $post->expects($this->any())
            ->method('isObjectNew')
            ->will($this->returnValue(false));

        $post->save();
    }

    /**
     * Prepare post mock
     *
     * @param string $date Gmt date
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _preparePostMock($date)
    {
        $post = $this->getModelMock('news/post', ['isObjectNew', 'setCreatedAt', 'setUpdatedAt', '_hasModelChanged']);

        $post->expects($this->once())
            ->method('_hasModelChanged')
            ->will($this->returnValue(true));

        $post->expects($this->once())
            ->method('setUpdatedAt')
            ->with($this->equalTo($date));

        return $post;
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
     * Test returns post url when url key exists
     *
     * @param int    $callNumber Call number
     * @param string $prefix     Prefix
     * @param string $suffix     Suffix
     * @param string $urlKey     Url key
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testReturnsPostUrlWhenUrlKeyExists($callNumber, $prefix, $suffix, $urlKey)
    {
        $post = $this->getModelMock('news/post', ['getUrlKey']);

        $post->expects($this->once())
            ->method('getUrlKey')
            ->will($this->returnValue($urlKey));

        $helper = $this->getHelperMock('news/post', ['getPrefix', 'getSuffix']);
        
        $helper->expects($this->once())
            ->method('getPrefix')
            ->will($this->returnValue($prefix));
        
        $helper->expects($this->once())
            ->method('getSuffix')
            ->will($this->returnValue($suffix));

        $this->replaceByMock('helper', 'news/post', $helper);

        $url = $this->getModelMock('core/url', ['getDirectUrl']);

        $url->expects($this->once())
            ->method('getDirectUrl')
            ->with($this->equalTo($this->expected($callNumber)->getPostUrl()));

        $this->replaceByMock('model', 'core/url', $url);

        $post->getPostUrl();
    }

    /**
     * Test returns post url when url key does not exist
     *
     * @return void
     */
    public function testReturnsPostUrlWhenUrlKeyDoesNotExist()
    {
        $session = $this->getModelMock('core/session', ['start']);
        $this->replaceByMock('singleton', 'core/session', $session);

        $post = $this->getModelMock('news/post', ['getUrlKey', 'getId']);

        $post->expects($this->once())
            ->method('getUrlKey')
            ->will($this->returnValue(null));

        $post->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(42));

        $this->assertEquals(Mage::getBaseUrl() . 'news/post/view/id/42/', $post->getPostUrl());
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
        $resource = $this->getResourceModelMock('news/post', ['checkUrlKey']);

        $resource->expects($this->once())
            ->method('checkUrlKey')
            ->with(
                $this->equalTo($urlKey),
                $this->equalTo($active)
            )
            ->will($this->returnValue($id));

        $this->replaceByMock('resource_model', 'news/post', $resource);

        $this->assertEquals(
            $this->expected($callNumber)->getCheckResult(),
            Mage::getModel('news/post')->checkUrlKey($urlKey, $active)
        );
    }

    /**
     * Test checks url key via resource model
     *
     * @param int    $callNumber Call number
     * @param string $urlPath    Url path
     * @param int    $id         Post id
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testChecksUrlPathViaResourceModel($callNumber, $urlPath, $id)
    {
        $resource = $this->getResourceModelMock('news/post', ['checkUrlPath']);

        $resource->expects($this->once())
            ->method('checkUrlPath')
            ->with($this->equalTo($urlPath))
            ->will($this->returnValue($id));

        $this->replaceByMock('resource_model', 'news/post', $resource);

        $this->assertEquals(
            $this->expected($callNumber)->getCheckResult(),
            Mage::getModel('news/post')->checkUrlPath($urlPath)
        );
    }

    /**
     * Test returns filtered text
     *
     * @param int    $callNumber   Call number
     * @param string $text         Text
     * @param string $filteredText Filtered text
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testReturnsFilteredText($callNumber, $text, $filteredText)
    {
        $post = $this->getModelMock('news/post', ['getData']);

        $post->expects($this->once())
            ->method('getData')
            ->with($this->equalTo('text'))
            ->will($this->returnValue($text));

        $filter = $this->getMock('Varien_Filter_Template', ['filter']);

        $filter->expects($this->once())
            ->method('filter')
            ->with($this->equalTo($text))
            ->will($this->returnValue($filteredText));

        $cmsHelper = $this->getHelperMock('cms/data', ['getBlockTemplateProcessor']);

        $cmsHelper->expects($this->once())
            ->method('getBlockTemplateProcessor')
            ->will($this->returnValue($filter));

        $this->replaceByMock('helper', 'cms', $cmsHelper);

        $this->assertEquals($this->expected($callNumber)->getFilteredText(), $post->getText());
    }

    /**
     * Test saves relation with category via category instance after save
     *
     * @return void
     */
    public function testSavesRelationWithCategoryViaCategoryInstanceAfterSave()
    {
        $post = $this->getModelMock('news/post', ['getCategoryInstance', '_hasModelChanged']);

        $post->expects($this->once())
            ->method('_hasModelChanged')
            ->will($this->returnValue(true));

        $postCategory = $this->getModelMock('news/post_category', ['savePostRelation']);

        $postCategory->expects($this->once())
            ->method('savePostRelation')
            ->with($this->equalTo($post));

        $post->expects($this->once())
            ->method('getCategoryInstance')
            ->will($this->returnValue($postCategory));

        $post->save();
    }
    
    /**
     * Test returns post category instance
     * 
     * @return void
     */
    public function testReturnsPostCategoryInstance()
    {
        $this->assertInstanceOf(
            'Oggetto_News_Model_Post_Category',
            Mage::getModel('news/post')->getCategoryInstance()
        );
    }

    /**
     * Test sets selected categories for post from collection
     *
     * @param int   $callNumber Call number
     * @param array $categories Categories
     * @dataProvider dataProvider
     * @loadExpectation
     * @return void
     */
    public function testSetsSelectedCategoriesForPostFromCollection($callNumber, $categories)
    {
        $post = $this->getModelMock('news/post', [
            'hasSelectedCategories',
            'setSelectedCategories',
            'getData',
            'getSelectedCategoriesCollection'
        ]);

        $post->expects($this->once())
            ->method('hasSelectedCategories')
            ->will($this->returnValue(false));

        $post->expects($this->once())
            ->method('setSelectedCategories')
            ->with($this->equalTo($categories));

        $post->expects($this->once())
            ->method('getData')
            ->with($this->equalTo('selected_categories'))
            ->will($this->returnValue($categories));
        
        $collection = $this->getResourceModelMock('news/post_category_collection', ['getItems']);

        $collection->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($categories));

        $post->expects($this->once())
            ->method('getSelectedCategoriesCollection')
            ->will($this->returnValue($collection));

        $this->assertEquals(
            $this->expected($callNumber)->getSelectedCategories(),
            $post->getSelectedCategories()
        );
    }

    /**
     * Test retrieves selected category collection from post category instance
     *
     * @return void
     */
    public function testRetrievesSelectedCategoryCollectionFromPostCategoryInstance()
    {
        $post = $this->getModelMock('news/post', ['getCategoryInstance']);
        
        $postCategory = $this->getModelMock('news/post_category', ['getCategoriesCollection']);

        $collection = new Varien_Data_Collection_Db;

        $postCategory->expects($this->once())
            ->method('getCategoriesCollection')
            ->with($this->equalTo($post))
            ->will($this->returnValue($collection));

        $post->expects($this->once())
            ->method('getCategoryInstance')
            ->will($this->returnValue($postCategory));

        $this->assertEquals($collection, $post->getSelectedCategoriesCollection());
    }

    /**
     * Test returns default values as array
     *
     * @return void
     */
    public function testReturnsDefaultValuesAsArray()
    {
        $this->assertTrue(is_array(Mage::getModel('news/post')->getDefaultValues()));
    }
}
