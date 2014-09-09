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
 * Post resource
 *
 * @category   Oggetto
 * @package    Oggetto_Blog
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Blog_Model_Resource_Post extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization with main table and id field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('blog/post', 'post_id');
    }

    /**
     * Add placing date to data
     *
     * @param Oggetto_Blog_Model_Post $object Post model
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave($object)
    {
        $object->setPlacingDate(now());
    }
}
