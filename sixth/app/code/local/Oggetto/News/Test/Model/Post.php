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
    public function testIsAvailableViaAlias() {
        $this->assertInstanceOf('Oggetto_News_Model_Post', Mage::getModel('news/post'));
    }

    /**
     * Test sets "created at" date while saving first time
     *
     * @return void
     */
    public function testSetsCreateAtDateWhileSavingFirstTime() {
        ini_set('display_errors', 1);
        $dateMock = $this->getModelMock('core/date', ['gmtDate']);
        $date = Mage::getModel('core/date')->gmtDate();
        $dateMock->expects($this->once())
            ->method('gmtDate')
            ->will($this->returnValue($date));

        $this->replaceByMock('singleton', 'core/date', $dateMock);
//        $post = $this->getModelMock('news/post', ['isObjectNew', 'setCreatedAt']);
//
//        $post->expects($this->once())
//            ->method('isObjectNew')
//            ->will($this->returnValue(true));
//
//        $post->expects($this->once())
//            ->method('setCreatedAt')
//            ->with($this->equalTo($date));
//
//        $post->save();
    }
}
