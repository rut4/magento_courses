<?php
/**
 * Oggetto News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Oggetto
 * @package        Oggetto_News
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Post view block
 *
 * @category   Oggetto
 * @package    Oggetto_News
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_News_Block_Post_View extends Mage_Core_Block_Template
{
    /**
     * Get the current post
     *
     * @return Oggetto_News_Model_Post|null
     */
    public function getCurrentPost()
    {
        return Mage::registry('current_post');
    }
}
