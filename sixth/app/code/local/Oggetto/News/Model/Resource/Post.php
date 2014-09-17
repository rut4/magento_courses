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
 * Post resource model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Resource_Post extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialization with main table and id field name
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('news/post', 'entity_id');
    }

    /**
     * Check url path
     *
     * @param string $urlPath Url path
     * @return string
     */
    public function checkUrlPath($urlPath)
    {
        $select = $this->_initCheckUrlPathSelect($urlPath);
        $select->join(['p' => $this->getMainTable()], 'p.entity_id = e.post_id', ['p.entity_id']);
        $select->where('p.status = true');

        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('p.entity_id')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Check url key
     *
     * @param string $urlKey  Url key
     * @param int    $storeId Store id
     * @param bool   $active  Is active
     * @return mixed
     */
    public function checkUrlKey($urlKey, $storeId, $active = true)
    {
        $select = $this->_initCheckUrlKeySelect($urlKey);
        if ($active) {
            $select->where('e.status = ?', $active);
        }

        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Check for unique URL key
     *
     * @param Mage_Core_Model_Abstract $object Post
     * @return bool
     */
    public function getIsUniqueUrlKey(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_initCheckUrlKeySelect($object->getData('url_key'));
        if ($object->getId()) {
            $select->where('e.entity_id <> ?', $object->getId());
        }
        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    /**
     * Check if the URL key is numeric
     *
     * @param Mage_Core_Model_Abstract $object Post
     * @return bool
     */
    protected function isNumericUrlKey(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * Check if the URL key is valid
     *
     * @param Mage_Core_Model_Abstract $object Post
     * @return bool
     */
    protected function isValidUrlKey(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
    }

    /**
     * Format string as url key
     *
     * @param string $str String as url key
     * @return string
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }

    /**
     * Init the check select by url path
     *
     * @param string $urlPath Url path
     * @return Zend_Db_Select
     */
    protected function _initCheckUrlPathSelect($urlPath)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(['e' => $this->getTable('news/category_post')], [])
            ->where('e.url_path = ?', $urlPath);
        return $select;
    }

    /**
     * Validate before saving
     *
     * @param Mage_Core_Model_Abstract $object Post
     * @return Oggetto_News_Model_Resource_Post
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $urlKey = $object->getData('url_key');
        if ($urlKey == '') {
            $urlKey = $object->getTitle();
        }
        $urlKey = $this->formatUrlKey($urlKey);
        $validKey = false;
        while (!$validKey) {
            $entityId = $this->checkUrlKey($urlKey, $object->getStoreId(), false);
            if ($entityId == $object->getId() || empty($entityId)) {
                $validKey = true;
            } else {
                $parts = explode('-', $urlKey);
                $last = $parts[count($parts) - 1];
                if (!is_numeric($last)) {
                    $urlKey = $urlKey . '-1';
                } else {
                    $suffix = '-' . ($last + 1);
                    unset($parts[count($parts) - 1]);
                    $urlKey = implode('-', $parts) . $suffix;
                }
            }
        }
        $object->setData('url_key', $urlKey);
        return parent::_beforeSave($object);
    }

    /**
     * Update url path
     *
     * @param Mage_Core_Model_Abstract $post The post which has been saved
     * @return void
     */
    public function updateUrlPath($post)
    {
        if ($post->getId()) {
            foreach ($post->getSelectedCategoriesCollection() as $category) {
                $urlKey = $category->getUrlPath() . '/' . $post->getUrlKey();
                $this->_getWriteAdapter()->update(
                    $this->getTable('news/category_post'),
                    ['url_path' => $urlKey],
                    [
                        'post_id = ?' => $post->getId(),
                        'category_id = ?' => $category->getId()
                    ]
                );
            }
        }
    }

    /**
     * init the check select
     *
     * @param string $urlKey
     * @param array $store
     * @return Zend_Db_Select
     * @author Ultimate Module Creator
     */
    protected function _initCheckUrlKeySelect($urlKey)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(['e' => $this->getMainTable()])
            ->where('e.url_key = ?', $urlKey);
        return $select;
    }

}
