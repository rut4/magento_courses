<?php
class Oggetto_Blog_Model_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialization with resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('blog/category');
    }
}
