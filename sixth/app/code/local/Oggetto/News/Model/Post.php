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
 * Post model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Post extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'news_post';
    const CACHE_TAG = 'news_post';

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'news_post';
    protected $_cacheTag    = 'news_post';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'post';
    protected $_categoryInstance = null;

    /**
     * Class constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('news/post');
    }

    /**
     * Get post url by category
     *
     * @param Oggetto_News_Model_Category $category Category
     * @return string
     */
    public function getPostUrlByCategory($category)
    {
        $categoryPrefix = '';
        if ($categoryUrl = $category->getCategoryUrl()) {
            $categoryPrefix = $categoryUrl . '/';
        }
        return $categoryPrefix . $this->getUrlKey();
    }

    /**
     * Before save post
     *
     * @return Oggetto_News_Model_Post
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        Mage::getSingleton('index/indexer')->logEvent(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Get the url to the post details page
     *
     * @return string
     */
    public function getPostUrl()
    {
        if ($urlKey = $this->getUrlKey()) {
            $urlKey = $this->_prependPrefix($urlKey);
            $urlKey = $this->_appendSuffix($urlKey);

            return Mage::getModel('core/url')->getDirectUrl($urlKey);
        }
        return Mage::getUrl('news/post/view', ['id' => $this->getId()]);
    }

    /**
     * Check URL key
     *
     * @param string $urlKey Url key
     * @param bool   $active Is active
     * @return bool
     */
    public function checkUrlKey($urlKey, $active = true)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $active);
    }

    /**
     * Check URL path
     *
     * @param string $urlPath Url path
     * @return bool
     */
    public function checkUrlPath($urlPath)
    {
        return $this->_getResource()->checkUrlPath($urlPath);
    }

    /**
     * Get the post text
     *
     * @return string
     */
    public function getText()
    {
        $text = $this->getData('text');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($text);
        return $html;
    }

    /**
     * Save post relation
     *
     * @return Oggetto_News_Model_Post
     */
    protected function _afterSave()
    {
        $this->getCategoryInstance()->savePostRelation($this);
        Mage::getSingleton('index/indexer')->indexEvents(
            self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
        return parent::_afterSave();
    }

    /**
     * Get category relation model
     *
     * @return Oggetto_News_Model_Post_Category
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('news/post_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * Get selected categories array
     *
     * @return array
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $this->setSelectedCategories(
                array_values($this->getSelectedCategoriesCollection()->getItems())
            );
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @return Oggetto_News_Model_Resource_Post_Category_Collection
     */
    public function getSelectedCategoriesCollection()
    {
        return $this->getCategoryInstance()->getCategoriesCollection($this);
    }

    /**
     * Get default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        return [
            'status' => 1
        ];
    }

    /**
     * Prepend prefix to url key
     *
     * @param string $urlKey url key
     * @return string
     */
    protected function _prependPrefix($urlKey)
    {
        if ($prefix = Mage::helper('news/post')->getPrefix()) {
            $urlKey = $prefix . '/' . $urlKey;
        }
        return $urlKey;
    }

    /**
     * Append suffix to url key
     *
     * @param string $urlKey url key
     * @return string
     */
    protected function _appendSuffix($urlKey)
    {
        if ($suffix = Mage::helper('news/post')->getSuffix()) {
            $urlKey .= '.' . $suffix;
        }
        return $urlKey;
    }
}
