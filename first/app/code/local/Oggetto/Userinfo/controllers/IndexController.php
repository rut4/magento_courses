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
 * the Oggetto Userinfo module to newer versions in the future.
 * If you wish to customize the Oggetto Userinfo module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Userinfo
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index controller
 *
 * @category   Oggetto
 * @package    Oggetto_Userinfo
 * @subpackage controllers
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Userinfo_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $userInfo = Mage::getModel('userinfo/userinfo');
            $userInfo->setName($request->getPost('name'));
            $userInfo->setEmail($request->getPost('email'));
            $userInfo->setText($request->getPost('text'));

            $userInfo->save();
        }
        $this->loadLayout();
        $this->renderLayout();
    }
}
