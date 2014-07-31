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

try {
    $installer = $this;
    $installer->startSetup();
    $table = $installer->getConnection()->newTable($installer->getTable('interview/question'))
        ->addColumn('question_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true,
            ), 'Question ID')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable' => false,
            'length'   => 255,
            ), 'Name')
        ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable' => false,
            'length'   => 255,
            ), 'Email')
        ->addColumn('text', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
            ), 'Text')
        ->setComment('Questions table');
    $installer->getConnection()->createTable($table);
    $installer->endSetup();
} catch (Exception $e) {
    Mage::logException($e);
}
