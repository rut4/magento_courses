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
 * Category - post relation edit block
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Adminhtml_Category_Edit_Tab_Post extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('post_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_posts' => 1));
        }
    }

    /**
     * Prepare the post collection
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tab_Post
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('news/post_collection');
        if ($this->getCategory()->getId()) {
            $constraint = 'related.category_id=' . $this->getCategory()->getId();
        } else {
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            ['related' => $collection->getTable('news/category_post')],
            'related.post_id=main_table.entity_id AND ' . $constraint,
            ['position']);
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare mass action grid
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tab_Post
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare the grid columns
     *
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tab_Post
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_posts', [
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_posts',
            'values' => $this->_getSelectedPosts(),
            'align' => 'center',
            'index' => 'entity_id'
        ]);
        $this->addColumn('title', [
            'header' => Mage::helper('news')->__('Title'),
            'align' => 'left',
            'index' => 'title',
            'renderer' => 'news/adminhtml_helper_column_renderer_relation',
            'params' => [
                'id' => 'getId'
            ],
            'base_link' => 'adminhtml/news_post/edit'
        ]);
        $this->addColumn('position', [
            'header' => Mage::helper('news')->__('Position'),
            'name' => 'position',
            'width' => 60,
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'position',
            'editable' => true
        ]);
    }

    /**
     * Retrieve selected posts
     *
     * @return array
     */
    protected function _getSelectedPosts()
    {
        $posts = $this->getCategoryPosts();
        if (!is_array($posts)) {
            $posts = array_keys($this->getSelectedPosts());
        }
        return $posts;
    }

    /**
     * Retrieve selected posts
     *
     * @return array
     */
    public function getSelectedPosts()
    {
        $posts = [];
        $selected = Mage::registry('current_category')->getSelectedPosts();
        if (!is_array($selected)) {
            $selected = [];
        }
        foreach ($selected as $post) {
            $posts[$post->getId()] = ['position' => $post->getPosition()];
        }
        return $posts;
    }

    /**
     * Get row url
     *
     * @param Oggetto_News_Model_Post $item Post
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/postsGrid', [
            'id' => $this->getCategory()->getId()
        ]);
    }

    /**
     * Get the current category
     *
     * @return Oggetto_News_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Add filter
     *
     * @param object $column Column
     * @return Oggetto_News_Block_Adminhtml_Category_Edit_Tab_Post
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_posts') {
            $postIds = $this->_getSelectedPosts();
            if (empty($postIds)) {
                $postIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $postIds]);
            } else {
                if ($postIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $postIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
