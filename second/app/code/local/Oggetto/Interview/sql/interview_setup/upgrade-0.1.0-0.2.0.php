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
 * @var Mage_Core_Model_Resource_Setup $this
 */

try {
    $installer = $this;
    $installer->startSetup();

    $installer->getConnection()->addColumn(
            $installer->getTable('interview/question'),
            'created_at',
            [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Created At'
            ]
        );
    $installer->getConnection()->addColumn(
        $installer->getTable('interview/question'),
        'answer',
        [
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'default'   => null,
            'comment'   => 'Answer'
        ]
    );
    $installer->getConnection()->addColumn(
            $installer->getTable('interview/question'),
            'status',
            [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'length'    => 1,
                'nullable'  => true,
                'default'   => 0,
                'comment'   => 'Status'
            ]
        );

    $installer->endSetup();
} catch (Exception $e) {
    Mage::logException($e);
}