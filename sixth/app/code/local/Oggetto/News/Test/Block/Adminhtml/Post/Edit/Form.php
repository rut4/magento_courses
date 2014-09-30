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
 * Post edit form test
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Test
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Test_Block_Adminhtml_Post_Edit_Form extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test prepares form
     *
     * @return void
     */
    public function testPreparesForm()
    {
        $block = $this->getBlockMock('news/adminhtml_post_edit_form', ['getUrl']);

        $block->expects($this->any())
            ->method('getUrl')
            ->with(
                $this->equalTo('*/*/save'),
                $this->anything()
            )
            ->will($this->returnValue('foo/bar/save/id/42'));

        $block->toHtml();

        $form = $block->getForm();

        $this->assertEquals('edit_form', $form->getId());
        $this->assertEquals('foo/bar/save/id/42', $form->getAction());
        $this->assertEquals('post', $form->getMethod());
        $this->assertEquals('multipart/form-data', $form->getEnctype());
        $this->assertTrue($form->getUseContainer());

    }
}
