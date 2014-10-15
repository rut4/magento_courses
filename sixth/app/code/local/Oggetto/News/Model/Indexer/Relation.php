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
 * Category model
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Indexer_Relation extends Mage_Index_Model_Indexer_Abstract
{
    protected $_matchedEntities = [
        Oggetto_News_Model_Category::ENTITY => [
            Mage_Index_Model_Event::TYPE_REINDEX,
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        ],
        Oggetto_News_Model_Post::ENTITY => [
            Mage_Index_Model_Event::TYPE_REINDEX,
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        ]
    ];

    /**
     * Initialization with resource indexer model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('news/indexer_relation');
    }

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('news')->__('Oggetto News Relation');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('news')->__('Oggetto News category - post relation index');
    }

    /**
     * Register indexer required data inside event object
     *
     * @param Mage_Index_Model_Event $event Indexer event
     * @return void
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $entity = $event->getDataObject();
        if ($event->getEntity() === Oggetto_News_Model_Category::ENTITY) {
            if ($entity->getId()) {
                $event->setData('category_id', $entity->getId());
            } elseif ($entity->getCategoryIds()) {
                $event->setData('category_ids', $entity->getCategoryIds());
            }
        } elseif ($event->getEntity() === Oggetto_News_Model_Post::ENTITY) {
            if ($entity->getId()) {
                $event->setData('post_id', $entity->getId());
            } elseif ($entity->getPostIds()) {
                $event->setData('post_ids', $entity->getPostIds());
            }
        }

    }

    /**
     * Process event based on event state data
     *
     * @param Mage_Index_Model_Event $event Indexer event
     * @return void
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getData('post_id') || $event->getData('post_ids')
            || $event->getData('category_id') || $event->getData('category_ids')) {
            $this->callEventHandler($event);
        }
    }
}
