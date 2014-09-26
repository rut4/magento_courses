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
 * Category view block test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Category_View extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_News_Block_Category_View',
            $this->getBlockMock('news/category_view'));
    }

    /**
     * Test returns current category from registry
     *
     * @return void
     */
    public function testReturnsCurrentPostFromRegistry()
    {
        $category = Mage::getModel('news/category');

        Mage::unregister('current_category');
        Mage::register('current_category', $category);

        $block = new Oggetto_News_Block_Category_View;

        $this->assertEquals($category, $block->getCurrentCategory());
    }
}
