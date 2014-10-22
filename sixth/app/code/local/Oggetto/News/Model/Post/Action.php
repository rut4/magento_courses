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
 * Post action processing model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Post_Action extends Varien_Object
{
    /**
     * Processing mass action event
     *
     * @param array $postIds Post ids
     * @return void
     */
    public function processMassaction(array $postIds)
    {
        $this->setData([
            'post_ids' => array_unique($postIds)
        ]);

        Mage::getSingleton('index/indexer')->processEntityAction(
            $this, Oggetto_News_Model_Post::ENTITY, Mage_Index_Model_Event::TYPE_MASS_ACTION
        );
    }
}
