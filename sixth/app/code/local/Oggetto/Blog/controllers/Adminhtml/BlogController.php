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

    /**
     * Get tree json
     *
     * @return void
     */
    public function treeJsonAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->isAjax()) {
            $root = '/root';
            $collection = ['first', 'second'];
            $jsonArray = [];
            foreach ($collection as $item) {
                $jsonArray[] = [
                    'text'  => $item,
                    'id'    => strtr(base64_encode($root . '/' . $item), '+/=', ':_-'),
                    'cls'   => 'folder'
                ];
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($jsonArray));
        }
    }
}
