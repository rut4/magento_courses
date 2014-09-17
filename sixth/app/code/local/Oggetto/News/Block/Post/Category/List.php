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
 * Post Categories list block
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Post_Category_List extends Oggetto_News_Block_Category_List
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $post = $this->getPost();
        if ($post) {
            $this->getCategories()->addPostFilter($post->getId());
            $this->getCategories()->unshiftOrder('related_post.position', 'ASC');
        }
    }

    /**
     * Prepare the layout
     *
     * @return Oggetto_News_Block_Post_Category_List
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * Get the current post
     *
     * @return Oggetto_News_Model_Post
     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }
}
