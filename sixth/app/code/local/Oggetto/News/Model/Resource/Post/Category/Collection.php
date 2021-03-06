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
 * Post - Category relation resource model collection
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Model_Resource_Post_Category_Collection extends Oggetto_News_Model_Resource_Category_Collection
{
    /**
     * Remember if fields have been joined
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * Add post filter
     *
     * @param Oggetto_News_Model_Post|int $post Post
     * @return Oggetto_News_Model_Resource_Post_Category_Collection
     */
    public function addPostFilter($post)
    {
        if ($post instanceof Oggetto_News_Model_Post) {
            $post = $post->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.post_id = ?', $post);
        return $this;
    }

    /**
     * Join the link table
     *
     * @return Oggetto_News_Model_Resource_Post_Category_Collection
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                ['related' => $this->getTable('news/post_category')],
                'related.category_id = main_table.entity_id',
                ['position']
            );
            $this->_joinedFields = true;
        }
        return $this;
    }
}
