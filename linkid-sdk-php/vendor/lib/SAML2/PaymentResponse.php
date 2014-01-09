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
     * Possible return values are:
     *
     *     STARTED                // payment is being processed
     *     DEFERRED               // deferred payment
     *     WAITING_FOR_UPDATE     // linkID stopped waiting for status update, SP will be informed on payment status change
     *     FAILED                 // payment has failed
     *     PAYED                  // completed
     *
     * @return string the payment state of the transaction
     */
    public function getPaymentState()
    {
        return $this->paymentState;
    }

    /**
     * @return string the transaction ID, a UUID
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }


}