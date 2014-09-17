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
 * Category collection resource model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Resource_Category_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * Initialization with model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('news/category');
    }

    /**
     * Add Id filter
     *
     * @param array $categoryIds Category IDs
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addIdFilter($categoryIds)
    {
        if (is_array($categoryIds)) {
            if (empty($categoryIds)) {
                $condition = '';
            } else {
                $condition = ['in' => $categoryIds];
            }
        } elseif (is_numeric($categoryIds)) {
            $condition = $categoryIds;
        } elseif (is_string($categoryIds)) {
            $ids = explode(',', $categoryIds);
            if (empty($ids)) {
                $condition = $categoryIds;
            } else {
                $condition = ['in' => $ids];
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Add category path filter
     *
     * @param string $regexp Path regexp filter
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addPathFilter($regexp)
    {
        $this->addFieldToFilter('path', ['regexp' => $regexp]);
        return $this;
    }

    /**
     * Add category path filter
     *
     * @param array|string $paths Paths
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }
        $write = $this->getResource()->getWriteConnection();
        $cond = array();
        foreach ($paths as $path) {
            $cond[] = $write->quoteInto('e.path LIKE ?', "$path%");
        }
        if ($cond) {
            $this->getSelect()->where(join(' OR ', $cond));
        }
        return $this;
    }

    /**
     * Add category level filter
     *
     * @param int|string $level
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addLevelFilter($level)
    {
        $this->addFieldToFilter('level', array('lteq' => $level));
        return $this;
    }

    /**
     * Add root category filter
     *
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addRootLevelFilter()
    {
        $this->addFieldToFilter('path', array('neq' => '1'));
        $this->addLevelFilter(1);
        return $this;
    }

    /**
     * Add order field
     *
     * @param string $field
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addOrderField($field)
    {
        $this->setOrder($field, self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Add active category filter
     *
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addStatusFilter($status = 1)
    {
        $this->addFieldToFilter('status', $status);
        return $this;
    }

    /**
     * get categories as array
     *
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'entity_id', $labelField = 'name', $additional = array())
    {
        $res = array();
        $additional['value'] = $valueField;
        $additional['label'] = $labelField;

        foreach ($this as $item) {
            if ($item->getId() == Mage::helper('news/category')->getRootCategoryId()) {
                continue;
            }
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
            }
            $res[] = $data;
        }
        return $res;
    }

    /**
     * get options hash
     *
     * @param string $valueField Value field
     * @param string $labelField Name field
     * @return array
     */
    protected function _toOptionHash($valueField = 'entity_id', $labelField = 'name')
    {
        $res = [];
        foreach ($this as $item) {
            if ($item->getId() == Mage::helper('news/category')->getRootCategoryId()) {
                continue;
            }
            $res[$item->getData($valueField)] = $item->getData($labelField);
        }
        return $res;
    }

    /**
     * Add the post filter to collection
     *
     * @param Oggetto_News_Model_Post|int $post Post
     * @return Oggetto_News_Model_Resource_Category_Collection
     */
    public function addPostFilter($post)
    {
        if ($post instanceof Oggetto_News_Model_Post) {
            $post = $post->getId();
        }
        if (!isset($this->_joinedFields['post'])) {
            $this->getSelect()->join(
                ['related_post' => $this->getTable('news/category_post')],
                'related_post.category_id = main_table.entity_id',
                ['position']
            );
            $this->getSelect()->where('related_post.post_id = ?', $post);
            $this->_joinedFields['post'] = true;
        }
        return $this;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
