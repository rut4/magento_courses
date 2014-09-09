<?php

class Oggetto_Blog_Test_Model_Resource_Category extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test post resource available via alias
     *
     * @return void
     */
    public function testIsAvailableViaAlias()
    {
        $this->assertInstanceOf('Oggetto_Blog_Model_Resource_Post', Mage::getResourceModel('blog/post'));
    }

    /**
     * Test post resource initializations with main table
     *
     * @return void
     */
    public function testInitializationsWithMainTable()
    {
        $this->assertEquals('oggetto_blog_post', Mage::getResourceModel('blog/post')->getMainTable());
    }

    /**
     * Test post resource initializations with id field name
     *
     * @return void
     */
    public function testInitializationsWithIdFieldName()
    {
        $this->assertEquals('post_id', Mage::getResourceModel('blog/post')->getIdFieldName());
    }
}
