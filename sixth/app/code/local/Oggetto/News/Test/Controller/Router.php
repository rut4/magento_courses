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
 * Router test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Controller_Router extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test matches url rewrite for category list
     *
     * @param string $path   Path info
     * @param string $urlKey Url
     * @dataProvider dataProvider
     * @return void
     */
    public function testMatchesUrlRewriteForCategoryList($path, $urlKey)
    {
        $categoryHelper = $this->getHelperMock('news/category');

        $categoryHelper->expects($this->once())
        ->method('getUrlRewriteForList')
        ->will($this->returnValue($urlKey));

        $this->replaceByMock('helper', 'news/category', $categoryHelper);

        $postHelper = $this->getHelperMock('news/post');

        $this->replaceByMock('helper', 'news/post', $postHelper);

        $request = $this->_prepareCategoryRequest($path, $urlKey);

        $request->expects($this->once())
            ->method('setActionName')
            ->with($this->equalTo('index'))
            ->will($this->returnSelf());

        $router = new Oggetto_News_Controller_Router;

        $this->assertTrue($router->match($request));
    }

    /**
     * Test matches url rewrite for category list
     *
     * @param string $path            Path info
     * @param string $urlKey          Url key
     * @param string $prefix          Prefix
     * @param string $suffix          Suffix
     * @param int    $keyCheckResult  Id by key
     * @param int    $pathCheckResult Id by path
     * @param int    $id              Category id
     * @dataProvider dataProvider
     * @return void
     */
    public function testMatchesCategoryUrl($path, $urlKey, $prefix, $suffix, $keyCheckResult, $pathCheckResult, $id)
    {
        $categoryHelper = $this->getHelperMock('news/category', ['getPrefix', 'getSuffix', 'getUrlRewriteForList']);

        $categoryHelper->expects($this->once())
            ->method('getPrefix')
            ->will($this->returnValue($prefix));

        $categoryHelper->expects($this->once())
            ->method('getSuffix')
            ->will($this->returnValue($suffix));

        $this->replaceByMock('helper', 'news/category', $categoryHelper);
        $this->replaceByMock('helper', 'news/post', $this->getHelperMock('news/post'));

        $request = $this->_prepareCategoryRequest($path, $urlKey);

        $request->expects($this->once())
            ->method('setActionName')
            ->with($this->equalTo('view'))
            ->will($this->returnSelf());

        $request->expects($this->once())
            ->method('setParam')
            ->with($this->equalTo('id'), $this->equalTo($id));

        $category = $this->getModelMock('news/category', ['checkUrlPath', 'checkUrlKey', 'load', 'getStatusPath']);

        $category->expects($this->once())
            ->method('checkUrlPath')
            ->with($this->equalTo($urlKey))
            ->will($this->returnValue($pathCheckResult));

        $category->expects($this->any())
            ->method('checkUrlKey')
            ->with($this->equalTo($urlKey))
            ->will($this->returnValue($keyCheckResult));

        $category->expects($this->once())
            ->method('load')
            ->with($this->equalTo($id))
            ->will($this->returnSelf());

        $category->expects($this->once())
            ->method('getStatusPath')
            ->will($this->returnValue(true));

        $this->replaceByMock('model', 'news/category', $category);

        $router = new Oggetto_News_Controller_Router;

        $this->assertTrue($router->match($request));
    }

    /**
     * Test matches url rewrite for category list
     *
     * @return void
     */
    public function testDoesNotMatchCategoryUrlWhenItsWrong()
    {
        $this->replaceByMock('helper', 'news/category', $this->getHelperMock('news/category'));
        $this->replaceByMock('helper', 'news/post', $this->getHelperMock('news/post'));

        $request = $this->getMock('Zend_Controller_Request_Http');

        $request->expects($this->any())
            ->method('getPathInfo')
            ->will($this->returnValue('/foo'));

        $category = $this->getModelMock('news/category', ['checkUrlPath', 'checkUrlKey']);

        $category->expects($this->once())
            ->method('checkUrlPath')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue(false));

        $category->expects($this->any())
            ->method('checkUrlKey')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue(false));

        $this->replaceByMock('model', 'news/category', $category);

        $router = new Oggetto_News_Controller_Router;

        $this->assertFalse($router->match($request));
    }

    /**
     * Prepare category request mock
     *
     * @param string $path   Path
     * @param string $urlKey Url key
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareCategoryRequest($path, $urlKey)
    {
        $request = $this->getMock('Zend_Controller_Request_Http');

        $request->expects($this->any())
            ->method('getPathInfo')
            ->will($this->returnValue($path));

        $request->expects($this->once())
            ->method('setModuleName')
            ->with($this->equalTo('news'))
            ->will($this->returnSelf());

        $request->expects($this->once())
            ->method('setControllerName')
            ->with($this->equalTo('category'))
            ->will($this->returnSelf());

        $request->expects($this->once())
            ->method('setAlias')
            ->with(
                $this->equalTo(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS),
                $urlKey
            );
        return $request;
    }

    /**
     * Prepare category request mock
     *
     * @param string $path   Path
     * @param string $urlKey Url key
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _preparePostRequest($path, $urlKey)
    {
        $request = $this->getMock('Zend_Controller_Request_Http');

        $request->expects($this->any())
            ->method('getPathInfo')
            ->will($this->returnValue($path));

        $request->expects($this->once())
            ->method('setModuleName')
            ->with($this->equalTo('news'))
            ->will($this->returnSelf());

        $request->expects($this->once())
            ->method('setControllerName')
            ->with($this->equalTo('post'))
            ->will($this->returnSelf());

        $request->expects($this->once())
            ->method('setAlias')
            ->with(
                $this->equalTo(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS),
                $urlKey
            );
        return $request;
    }

    /**
     * Test matches url rewrite for category list
     *
     * @param string $path            Path info
     * @param string $urlKey          Url key
     * @param string $prefix          Prefix
     * @param string $suffix          Suffix
     * @param int    $keyCheckResult  Id by key
     * @param int    $pathCheckResult Id by path
     * @param int    $id              Category id
     * @dataProvider dataProvider
     * @return void
     */
    public function testMatchesPostUrl($path, $urlKey, $prefix, $suffix, $keyCheckResult, $pathCheckResult, $id)
    {
        $this->replaceByMock('helper', 'news/category', $this->getHelperMock('news/category'));

        $postHelper = $this->getHelperMock('news/post', ['getPrefix', 'getSuffix', 'getUrlRewriteForList']);

        $postHelper->expects($this->once())
            ->method('getPrefix')
            ->will($this->returnValue($prefix));

        $postHelper->expects($this->once())
            ->method('getSuffix')
            ->will($this->returnValue($suffix));

        $this->replaceByMock('helper', 'news/post', $postHelper);

        $request = $this->_preparePostRequest($path, $urlKey);

        $request->expects($this->once())
            ->method('setActionName')
            ->with($this->equalTo('view'))
            ->will($this->returnSelf());

        $request->expects($this->once())
            ->method('setParam')
            ->with($this->equalTo('id'), $this->equalTo($id));

        $post = $this->getModelMock('news/post', ['checkUrlPath', 'checkUrlKey']);

        $post->expects($this->once())
            ->method('checkUrlPath')
            ->with($this->equalTo($urlKey))
            ->will($this->returnValue($pathCheckResult));

        $post->expects($this->any())
            ->method('checkUrlKey')
            ->with($this->equalTo($urlKey))
            ->will($this->returnValue($keyCheckResult));

        $this->replaceByMock('model', 'news/post', $post);

        $router = new Oggetto_News_Controller_Router;

        $this->assertTrue($router->match($request));
    }


}
