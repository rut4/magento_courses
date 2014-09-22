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
 * Post helper test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Helper_Post extends EcomDev_PHPUnit_Test_Case
{

    /**
     * Test is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_News_Helper_Post', Mage::helper('news/post'));
    }

    /**
     * Test retrieves prefix from config
     *
     * @loadFixture postPath
     * @return void
     */
    public function testRetrievesPrefixFromConfig()
    {
        Mage::app()->setCurrentStore('default');
        $this->assertEquals(Mage::helper('news/post')->getPrefix(), 'posts/');
    }

    /**
     * Test retrieves suffix from config
     *
     * @loadFixture postPath
     * @return void
     */
    public function testRetrievesSuffixFromConfig()
    {
        Mage::app()->setCurrentStore('default');
        $this->assertEquals(Mage::helper('news/post')->getSuffix(), '.foo');
    }

    /**
     * Test does not return prefix if not set
     *
     * @loadFixture emptyPrefixAndSuffix
     * @return void
     */
    public function testDoesNotReturnPrefixIfNotSet()
    {
        Mage::app()->setCurrentStore('default');
        $this->assertEquals(Mage::helper('news/post')->getPrefix(), null);
    }

    /**
     * Test does not return suffix if not set
     *
     * @loadFixture emptyPrefixAndSuffix
     * @return void
     */
    public function testDoesNotReturnSuffixIfNotSet()
    {
        Mage::app()->setCurrentStore('default');
        $this->assertEquals(Mage::helper('news/post')->getSuffix(), null);
    }
}
