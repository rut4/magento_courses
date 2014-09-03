<?php
class Oggetto_Blog_Adminhtml_BlogController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Tree action
     *
     * @return void
     */
    public function treeAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
