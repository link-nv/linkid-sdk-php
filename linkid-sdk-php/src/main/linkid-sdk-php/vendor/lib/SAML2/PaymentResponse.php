<?php

class SAML2_PaymentResponse
{

    private $transactionId;
    private $paymentState;

    function __construct(DOMElement $dom)
    {
        foreach ($dom->childNodes AS $child) {

            if ($child->getAttribute("Name") == "PaymentResponse.state") {
                $this->paymentState = $child->firstChild->textContent;
            } else if ($child->getAttribute("Name") == "PaymentResponse.txnId") {
                $this->transactionId = $child->firstChild->textContent;
            }
        }
        SimpleSAML_Logger::debug("  --> Payment response: " . $this->transactionId . ' - ' . $this->paymentState);
    }

    /**
     * @return mixed
     */
    public function getPaymentState()
    {
        return $this->paymentState;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }


}