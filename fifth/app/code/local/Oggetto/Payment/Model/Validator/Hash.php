<?php
class Oggetto_Payment_Model_Validator_Hash implements Oggetto_Payment_Model_Validator_IValidator
{
    public function validate(Oggetto_Payment_Model_Report $report)
    {
        $fields = [
            'total'     => $report->getTotal(),
            'status'    => $report->getStatus(),
            'order_id'  => $report->getOrderId()
        ];

        return $report->getHash() == Mage::helper('oggettopayment')->getSignature($fields);
    }
}
