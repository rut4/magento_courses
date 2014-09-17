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
 * Post admin grid block
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Adminhtml_Post_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('postGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @return Oggetto_News_Block_Adminhtml_Post_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('news/post')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid collection
     *
     * @return Oggetto_News_Block_Adminhtml_Post_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header' => Mage::helper('news')->__('Id'),
            'index' => 'entity_id',
            'type' => 'number'
        ]);
        $this->addColumn('title', [
            'header' => Mage::helper('news')->__('Title'),
            'align' => 'left',
            'index' => 'title'
        ]);
        $this->addColumn('status', [
            'header' => Mage::helper('news')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => [
                '1' => Mage::helper('news')->__('Enabled'),
                '0' => Mage::helper('news')->__('Disabled')
            ]
        ]);
        $this->addColumn('url_key', [
            'header' => Mage::helper('news')->__('URL key'),
            'index' => 'url_key'
        ]);
        $this->addColumn('created_at', [
            'header' => Mage::helper('news')->__('Created at'),
            'index' => 'created_at',
            'width' => '120px',
            'type' => 'datetime'
        ]);
        $this->addColumn('action',
            [
                'header' => Mage::helper('news')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => Mage::helper('news')->__('Edit'),
                        'url'     => ['base' => '*/*/edit'],
                        'field'   => 'id'
                    ]
                ],
                'filter' => false,
                'is_system' => true,
                'sortable' => false,
            ]);
        $this->addExportType('*/*/exportCsv', Mage::helper('news')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('news')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('news')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action
     *
     * @return Oggetto_News_Block_Adminhtml_Post_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('post');
        $this->getMassactionBlock()->addItem('delete', [
            'label' => Mage::helper('news')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('news')->__('Are you sure?')
        ]);
        $this->getMassactionBlock()->addItem('status', [
            'label' => Mage::helper('news')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', ['_current' => true]),
            'additional' => [
                'status' => [
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('news')->__('Status'),
                    'values' => [
                        '1' => Mage::helper('news')->__('Enabled'),
                        '0' => Mage::helper('news')->__('Disabled')
                    ]
                ]
            ]
        ]);
        return $this;
    }

    /**
     * Get the row url
     *
     * @param Oggetto_News_Model_Post $row Post row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }

    /**
     * Get the grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * After collection load
     *
     * @return Oggetto_News_Block_Adminhtml_Post_Grid
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
