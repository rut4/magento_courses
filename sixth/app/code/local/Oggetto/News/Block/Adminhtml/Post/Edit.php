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
 * Post admin edit form
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Adminhtml_Post_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'news';
        $this->_controller = 'adminhtml_post';
        $this->updateButton('save', 'label', Mage::helper('news')->__('Save Post'));
        $this->updateButton('delete', 'label', Mage::helper('news')->__('Delete Post'));
        $this->addButton('saveandcontinue', [
            'label'   => Mage::helper('news')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save',
        ], -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Get the edit form header
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_post') && Mage::registry('current_post')->getId()) {
            return Mage::helper('news')
                ->__("Edit Post '%s'", $this->escapeHtml(Mage::registry('current_post')->getTitle()));
        } else {
            return Mage::helper('news')->__('Add Post');
        }
    }
}
