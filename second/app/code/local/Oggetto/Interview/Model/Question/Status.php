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
 * Question status model
 *
 * @category   Oggetto
 * @package    Oggetto_Interview
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_Interview_Model_Question_Status
{
    /**
     * Questions statuses
     */
    const NOT_ANSWERED = 0;
    const ANSWERED = 1;

    /**
     * Get two statuses of question for DB
     *
     * @return array Status of customer question
     */
    public function toOptionArray()
    {
        return [
            self::NOT_ANSWERED  => Mage::helper('interview')->__('Not answered'),
            self::ANSWERED      => Mage::helper('interview')->__('Answered')
        ];
    }
}
