<?php

class Oggetto_Payment_PaymentController extends Mage_Core_Controller_Front_Action
{
    public function redirectAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function reportAction()
    {
        if ($this->getRequest()->isPost()) {
            /** @var Oggetto_Payment_Model_Report $report */
            $report = Mage::getModel('oggettopayment/report');
            $report->init($this->getRequest());
            try {
                $report->validate();
                $report->response();
                $this->getResponse()->setHttpResponseCode(200);
            } catch (Oggetto_Payment_Model_Exception_Validate $e) {
                Mage::logException($e);
                $this->getResponse()->setHttpResponseCode(400);
            } catch (Exception $e) {
                Mage::logException($e);
                $this->getResponse()->setHttpResponseCode(500);
            }
        }
    }
}