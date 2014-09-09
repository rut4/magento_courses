<?php

class Oggetto_Blog_Test_Model_Category extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test category model is available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_Blog_Model_Category', Mage::getModel('blog/category'));
    }

    /**
     * Test category model initializations with resource model
     *
     * @return void
     */
    public function testInitializationsWithResourceModel()
    {
        $this->assertInstanceOf('Oggetto_Blog_Model_Resource_Category', Mage::getModel('blog/category')->getResource());
    }
}
