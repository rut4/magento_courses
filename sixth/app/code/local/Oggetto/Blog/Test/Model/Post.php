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
 * the Oggetto Blog module to newer versions in the future.
 * If you wish to customize the Oggetto Blog module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Blog post test
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Blog_Test_Model_Post extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test post model is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_Blog_Model_Post', Mage::getModel('blog/post'));
    }

    /**
     * Test post model initializations with resource model
     *
     * @return void
     */
    public function testInitializationsWithResourceModel()
    {
        $this->assertInstanceOf('Oggetto_Blog_Model_Resource_Post', Mage::getModel('blog/post')->getResource());
    }

    /**
     * Test post model adds placing date while saving
     *
     * @return void
     */
    public function testAddsPlacingDateWhileSaving()
    {
        $post = $this->getModelMock('blog/post', ['addPlacingDate']);

        $post->expects($this->once())
            ->method('addPlacingDate')
            ->with($this->equalTo(now()))
            ->will($this->returnSelf());
    }
}