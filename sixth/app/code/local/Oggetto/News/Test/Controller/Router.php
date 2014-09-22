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

        $request = $this->getMock('Zend_Controller_Request_Http');

        $request->expects($this->once())
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
        ->method('setActionName')
        ->with($this->equalTo('index'))
        ->will($this->returnSelf());

        $request->expects($this->once())
        ->method('setAlias')
        ->with(
            $this->equalTo(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS),
            $urlKey
        );

        $router = new Oggetto_News_Controller_Router;

        $this->assertTrue($router->match($request));
    }

    /**
     * Test matches url rewrite for category list
     *
     * @param string $path   Path info
     * @param string $urlKey Url
     * @dataProvider dataProvider
     * @return void
     */
    public function testMatchesCategoryUrl($path, $urlKey)
    {
        $this->markTestIncomplete();
        $this->replaceByMock('helper', 'news/category', $this->getHelperMock('news/category'));
        $this->replaceByMock('helper', 'news/post', $this->getHelperMock('news/post'));

        $request = $this->getMock('Zend_Controller_Request_Http');

        $request->expects($this->once())
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
            ->method('setActionName')
            ->with($this->equalTo('index'))
            ->will($this->returnSelf());

        $request->expects($this->once())
            ->method('setAlias')
            ->with(
                $this->equalTo(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS),
                $urlKey
            );

        $router = new Oggetto_News_Controller_Router;

        $this->assertTrue($router->match($request));
    }

}
