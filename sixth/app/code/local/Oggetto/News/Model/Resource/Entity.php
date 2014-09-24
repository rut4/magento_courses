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
 * News entity resource model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Resource_Entity extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Construct method
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Check url key
     *
     * @param string $urlKey Url key
     * @param bool   $active Is active
     * @return string
     */
    public function checkUrlKey($urlKey, $active = true)
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
     * Init the check select by url key
     *
     * @param string $urlKey Url key
     * @return Zend_Db_Select
     */
    protected function _initCheckUrlKeySelect($urlKey)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(['e' => $this->getMainTable()])
            ->where('e.url_key = ?', $urlKey);
        return $select;
    }
}