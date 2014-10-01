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
 * Category admin edit form test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Adminhtml_Category_Edit extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test initialisations itself in constructor
     *
     * @return void
     */
    public function testInitializationItselfInConstructor()
    {
        $this->replaceByMock('singleton', 'core/session', $this->getModelMock('core/session', ['start']));

        $block = $this->getMockBuilder('Oggetto_News_Block_Adminhtml_Category_Edit')
            ->disableOriginalConstructor()
            ->setMethods(['setTemplate'])
            ->getMock();

        $block->expects($this->at(1))
            ->method('setTemplate')
            ->with($this->equalTo('news/category/edit.phtml'));

        $reflected = new ReflectionClass('Oggetto_News_Block_Adminhtml_Category_Edit');
        $constructor = $reflected->getConstructor();
        $constructor->invoke($block);

        $layout = $this->getModelMock('core/layout', ['createBlock']);

        $layout->expects($this->at(0))
            ->method('createBlock')
            ->with(
                $this->equalTo('news/adminhtml_category_edit_form')
            )
            ->will($this->returnValue(new Mage_Core_Block_Template()));

        $block->setLayout($layout);
    }
}
