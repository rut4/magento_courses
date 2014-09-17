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
 * Router
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * Init routes
     *
     * @param Varien_Event_Observer $observer Observer
     * @return Oggetto_News_Controller_Router
     */
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('news', $this);
        return $this;
    }

    /**
     * Validate and match entities and modify request
     *
     * @param Zend_Controller_Request_Http $request Request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $urlKey = trim($request->getPathInfo(), '/');
        $check = [];
        $check['category'] = new Varien_Object([
            'prefix' => Mage::getStoreConfig('news/category/url_prefix'),
            'suffix' => Mage::getStoreConfig('news/category/url_suffix'),
            'list_key' => Mage::getStoreConfig('news/category/url_rewrite_list'),
            'list_action' => 'index',
            'model' => 'news/category',
            'controller' => 'category',
            'action' => 'view',
            'param' => 'id',
            'check_path' => 1
        ]);
        $check['post'] = new Varien_Object([
            'prefix' => Mage::getStoreConfig('news/post/url_prefix'),
            'suffix' => Mage::getStoreConfig('news/post/url_suffix'),
            'list_key' => Mage::getStoreConfig('news/post/url_rewrite_list'),
            'list_action' => 'index',
            'model' => 'news/post',
            'controller' => 'post',
            'action' => 'view',
            'param' => 'id',
            'check_path' => 0
        ]);
        foreach ($check as $key => $settings) {
            if ($settings->getListKey()) {
                if ($urlKey == $settings->getListKey()) {
                    $request->setModuleName('news')
                        ->setControllerName($settings->getController())
                        ->setActionName($settings->getListAction());
                    $request->setAlias(
                        Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                        $urlKey
                    );
                    return true;
                }
            }
            if ($settings['prefix']) {
                $parts = explode('/', $urlKey);
                if ($parts[0] != $settings['prefix'] || count($parts) != 2) {
                    continue;
                }
                $urlKey = $parts[1];
            }
            if ($settings['suffix']) {
                $urlKey = substr($urlKey, 0, -strlen($settings['suffix']) - 1);
            }
            $model = Mage::getModel($settings->getModel());
            $id = $model->checkUrlPath($urlKey);
            if (!$id) {
                $id = $model->checkUrlKey($urlKey);
            }
            if ($id) {
                if ($settings->getCheckPath() && !$model->load($id)->getStatusPath()) {
                    continue;
                }
                $request->setModuleName('news')
                    ->setControllerName($settings->getController())
                    ->setActionName($settings->getAction())
                    ->setParam($settings->getParam(), $id);
                $request->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                    $urlKey
                );
                return true;
            }
        }
        return false;
    }
}
