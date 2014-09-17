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
 * Post collection resource model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Resource_Post_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * constructor
     *
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('news/post');
    }

    /**
     * get posts as array
     *
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _toOptionArray($valueField = 'entity_id', $labelField = 'title', $additional = array())
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * get options hash
     *
     * @param string $valueField
     * @param string $labelField
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _toOptionHash($valueField = 'entity_id', $labelField = 'title')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    /**
     * add the category filter to collection
     *
     * @param mixed (Oggetto_News_Model_Category|int) $category
     * @return Oggetto_News_Model_Resource_Post_Collection
     * @author Ultimate Module Creator
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Oggetto_News_Model_Category) {
            $category = $category->getId();
        }
        if (!isset($this->_joinedFields['category'])) {
            $this->getSelect()->join(
                array('related_category' => $this->getTable('news/post_category')),
                'related_category.post_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_category.category_id = ?', $category);
            $this->_joinedFields['category'] = true;
        }
        return $this;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Varien_Db_Select
     * @author Ultimate Module Creator
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
