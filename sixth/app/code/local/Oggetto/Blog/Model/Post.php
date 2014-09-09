<?php
class Oggetto_Blog_Model_Post extends Mage_Core_Model_Abstract
{
    /**
     * Initialization with resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('blog/post');
    }

    public function save()
    {
        $this->setPlacingData(now());
        return parent::save();
    }
}
