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
 * Post list block
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Blog_Block_Post_List extends Mage_Core_Block_Template
{
    protected $_postCollection;

    /**
     * Prepare layout with pagination
     *
     * @return Mage_Core_Block_Template
     */
    protected function _prepareLayout()
    {
        $this->_postCollection = Mage::getModel('blog/post')->getCollection();

        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit([10 => 10]);
        $pager->setCollection($this->_postCollection);

        $this->setChild('pager', $pager);

        return $this;
    }

    /**
     * Get post collection
     *
     * @return object
     */
    public function getPostCollection()
    {
        return $this->_postCollection;
    }

    /**
     * Get category path
     *
     * @param Oggetto_Blog_Model_Post $post Post model
     * @return string
     */
    public function getCategoryPath(Oggetto_Blog_Model_Post $post)
    {
        return $post->getCategoryPath();
    }
}
