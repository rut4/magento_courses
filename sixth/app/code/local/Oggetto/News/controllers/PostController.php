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
 * Post front contrller
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_PostController extends Mage_Core_Controller_Front_Action
{
    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('news/post')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb('home', array(
                        'label' => Mage::helper('news')->__('Home'),
                        'link' => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb('posts', array(
                        'label' => Mage::helper('news')->__('Posts'),
                        'link' => '',
                    )
                );
            }
        }
        $this->renderLayout();
    }

    /**
     * Init post
     *
     * @return Oggetto_News_Model_Post
     */
    protected function _initPost()
    {
        $postId = $this->getRequest()->getParam('id', 0);
        $post = Mage::getModel('news/post')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($postId);
        if (!$post->getId()) {
            return false;
        } elseif (!$post->getStatus()) {
            return false;
        }
        return $post;
    }

    /**
     * View post
     *
     * @return void
     */
    public function viewAction()
    {
        $post = $this->_initPost();
        if (!$post) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_post', $post);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('news-post news-post' . $post->getId());
        }
        if (Mage::helper('news/post')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb('home', array(
                        'label' => Mage::helper('news')->__('Home'),
                        'link' => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb('posts', array(
                        'label' => Mage::helper('news')->__('Posts'),
                        'link' => Mage::helper('news/post')->getPostsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb('post', array(
                        'label' => $post->getTitle(),
                        'link' => '',
                    )
                );
            }
        }
        $this->renderLayout();
    }
}
