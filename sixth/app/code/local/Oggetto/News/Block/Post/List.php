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
 * Post list block
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Post_List extends Mage_Core_Block_Template
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $posts = Mage::getResourceModel('news/post_collection')
            ->addFieldToFilter('status', 1);
        $posts->setOrder('title', 'asc');
        $this->setPosts($posts);
    }

    /**
     * Prepare the layout
     *
     * @return Oggetto_News_Block_Post_List
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'news.post.html.pager')
            ->setCollection($this->getPosts());
        $this->setChild('pager', $pager);
        $this->getPosts()->load();
        return $this;
    }

    /**
     * Get the pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
