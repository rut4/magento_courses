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

$this->startSetup();
$table = $this->getConnection()
    ->newTable($this->getTable('news/category'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ], 'Category ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable' => false,
    ], 'Name')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
    ], 'Parent id')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [], 'Path')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
    ], 'Position')
    ->addColumn('level', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
    ], 'Level')
    ->addColumn('children_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
    ], 'Children count')
    ->addColumn('url_key', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [], 'URL key')
    ->addColumn('url_path', Varien_Db_Ddl_Table::TYPE_TEXT, 511, [], 'URL path')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [], 'Category Status')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [], 'Category Modification Time')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [], 'Category Creation Time')
    ->setComment('Category Table');
$this->getConnection()->createTable($table);

$table = $this->getConnection()
    ->newTable($this->getTable('news/post'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'nullable' => false,
        'primary' => true
    ], 'Post ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable' => false
    ], 'Title')
    ->addColumn('text', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable' => false
    ], 'Text')
    ->addColumn('url_key', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [], 'URL key')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [], 'Post Status')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [], 'Post Modification Time')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [], 'Post Creation Time')
    ->setComment('Post Table');
$this->getConnection()->createTable($table);

$table = $this->getConnection()
    ->newTable($this->getTable('news/category_post'))
    ->addColumn('rel_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary' => true
    ], 'Relation ID')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => false,
        'default' => '0'
    ], 'Category ID')
    ->addColumn('post_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => false,
        'default' => '0'
    ], 'Post ID')
    ->addColumn('url_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [], 'URL path')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable' => false,
        'default' => '0'
    ], 'Position')
    ->addForeignKey(
        $this->getFkName('news/category_post', 'category_id', 'news/category', 'entity_id'),
        'category_id',
        $this->getTable('news/category'),
        'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $this->getFkName('news/category_post', 'post_id', 'news/post', 'entity_id'),
        'post_id',
        $this->getTable('news/post'),
        'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addIndex(
        $this->getIdxName(
            'news/category_post',
            ['category_id', 'post_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['category_id', 'post_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('Category to Post Linkage Table');
$this->getConnection()->createTable($table);

$this->endSetup();
