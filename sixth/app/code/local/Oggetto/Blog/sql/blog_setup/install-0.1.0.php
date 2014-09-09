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

try {
    /** @var Mage_Core_Model_Resource_Setup $installer */
    $installer = $this;

    $installer->startSetup();

    $connection = $installer->getConnection();

    $table = $connection
        ->newTable($installer->getTable('blog/category'))
        ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true
        ], 'Category id')
        ->addColumn('parent_category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
            'nullable'  => false
        ], 'Parent category id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, [
            'nullable'  => false
        ])
        ->addForeignKey(
            $installer->getFkName('blog/category', 'parent_category_id', 'blog/category', 'category_id'),
            'parent_category_id',
            $installer->getTable('blog/category'),
            'category_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Blog category table');

    $connection->createTable($table);

    $table = $connection
        ->newTable($installer->getTable('blog/post'))
        ->addColumn('post_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true
        ], 'Post id')
        ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => false
        ], 'Category id')
        ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 127, [
            'nullable' => false
        ], 'Post title')
        ->addColumn('text', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
            'nullable' => false
        ], 'Post text')
        ->addColumn('placing_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
            'nullable' => false
        ], 'Placing date')
        ->addForeignKey(
            $installer->getFkName('blog/post', 'category_id', 'blog/category', 'category_id'),
            'category_id',
            $installer->getTable('blog/category'),
            'category_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Blog post table');

    $connection->createTable($table);

    $installer->endSetup();

} catch (Exception $e) {
    Mage::logException($e);
}
