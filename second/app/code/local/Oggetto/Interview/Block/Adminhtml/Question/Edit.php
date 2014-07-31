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
 * the Oggetto Interview module to newer versions in the future.
 * If you wish to customize the Oggetto Interview module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Interview
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Question row edit adminhtml block class
 *
 * @category   Oggetto
 * @package    Oggetto_Interview
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */

class Oggetto_Interview_Block_Adminhtml_Question_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'interview';
        $this->_question = 'adminhtml_question';

        parent::__construct();
    }

    public function getHeaderText()
    {
        if (Mage::registry('interview')->getId()) {
            return $this->__('Edit');
        } else {
            return $this->__('New');
        }
    }
}
