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
 * Blog controller
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Blog_Adminhtml_BlogController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Tree action
     *
     * @return void
     */
    public function treeAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get tree json
     *
     * @return void
     */
    public function treeJsonAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->isAjax()) {
            $root = '/root';
            $collection = ['first', 'second'];
            $jsonArray = [];
            foreach ($collection as $item) {
                $jsonArray[] = [
                    'text'  => $item,
                    'id'    => strtr(base64_encode($root . '/' . $item), '+/=', ':_-'),
                    'cls'   => 'folder'
                ];
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($jsonArray));
        }
    }
}
