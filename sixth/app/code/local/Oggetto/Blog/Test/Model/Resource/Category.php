<?php

class Oggetto_Blog_Test_Model_Resource_Category extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test category resource available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_Blog_Model_Resource_Category', Mage::getResourceModel('blog/category'));
    }

    /**
     * Test category resource initializations with main table
     *
     * @return void
     */
    public function testInitializationsWithMainTable()
    {
        $this->assertEquals('oggetto_blog_category', Mage::getResourceModel('blog/category')->getMainTable());
    }

    /**
     * Test category resource initializations with id field name
     *
     * @return void
     */
    public function testInitializationsWithIdFieldName()
    {
        $this->assertEquals('category_id', Mage::getResourceModel('blog/category')->getIdFieldName());
    }
}
