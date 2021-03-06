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
 * News textarea attribute WYSIWYG button
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Adminhtml_Helper_Wysiwyg extends Varien_Data_Form_Element_Textarea
{
    /**
     * Retrieve additional html and put it at the end of element html
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        $disabled = ($this->getDisabled() || $this->getReadonly());
        $html .= Mage::getSingleton('core/layout')
            ->createBlock('adminhtml/widget_button', '', [
                'label' => Mage::helper('catalog')->__('WYSIWYG Editor'),
                'type' => 'button',
                'disabled' => $disabled,
                'class' => ($disabled) ? 'disabled btn-wysiwyg' : 'btn-wysiwyg',
                'onclick' => 'catalogWysiwygEditor.open(\''
                    . Mage::helper('adminhtml')->getUrl('*/*/wysiwyg') . '\', \'' . $this->getHtmlId() . '\')'
            ])->toHtml();
        return $html;
    }
}
