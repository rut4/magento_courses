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
 * Post grid block
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Blog_Block_Adminhtml_Post_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setId('blog_post_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Get collection class alias
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'blog/post_collection';
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'post_id',
            [
                'header'    => $this->__('ID'),
                'index'     => 'post_id'
            ]
        );
        $this->addColumn(
            'category_id',
            [
                'header' => $this->__('Category ID'),
                'index' => 'category_id'
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => $this->__('Title'),
                'index' => 'title'
            ]
        );
        $this->addColumn(
            'text',
            [
                'header' => $this->__('Text'),
                'index' => 'text'
            ]
        );
        $this->addColumn(
            'placing_date',
            [
                'header' => $this->__('Placing Date'),
                'index' => 'placing_date',
                'type' => 'datetime'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass actions
     *
     * @return $this|Mage_Adminhtml_Block_Widget_Grid Block with prepared mass actions
     */
    protected function _prepareMassAction()
    {
        $this->setMassactionIdField('post_id');
        $this->getMassactionBlock()->setFormFieldName('post_id');
        $this->getMassactionBlock()
            ->addItem('delete', [
                'label'     => $this->__('Delete'),
                'url'       => $this->getUrl('*/*/massDelete', ['' => '']),
                'confirm'   => $this->__('Are you sure?')
            ]);
        return $this;
    }

    /**
     * Get row url
     *
     * @param Oggetto_Blog_Model_Post $post Post row
     * @return string Row url
     */
    public function getRowUrl($post)
    {
        return $this->getUrl('*/*/edit', ['id' => $post->getId()]);
    }
}
