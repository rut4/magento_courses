<?php

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
}
