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
 * the Oggetto Interview module to newer versions in the future.
 * If you wish to customize the Oggetto Interview module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Interview
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Question grid adminhtml block class
 *
 * @category   Oggetto
 * @package    Oggetto_Interview
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */

class Oggetto_Interview_Block_Adminhtml_Question_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __constructor()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setId('interview_question_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

    }

    protected function _getCollectionClass()
    {
        return 'interview/question_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header'    => $this->__('ID'),
                'index'     => 'question_id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => $this->__('Name'),
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'email',
            [
                'header' => $this->__('Email'),
                'index' => 'email'
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
            'created_at',
            [
                'header' => $this->__('Created at'),
                'index' => 'created_at',
                'type' => 'datetime'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => $this->__('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => Mage::getModel('interview/question_status')->toOptionArray()
            ]
        );
        $this->addColumn(
            'answer',
            [
                'header' => $this->__('Answer'),
                'index' => 'answer'
            ]
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
