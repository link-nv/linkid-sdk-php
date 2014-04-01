<?php

/*
 * LinkID Payment Response
 *
 * @author Wim Vandenhaute
 */

class LinkIDPaymentResponse {

    public $orderReference;
    public $paymentState;

    // payment state constants
    const STARTED            = 0;       // payment is being processed
    const DEFERRED           = 1;       // deferred payment
    const WAITING_FOR_UPDATE = 2;       // linkID stopped waiting for status update, SP will be informed on payment status change
    const FAILED             = 3;       // payment has failed
    const REFUNDED           = 4;       // payment has been refunded
    const REFUND_STARTED     = 5;       // payment refund has started
    const PAYED              = 6;       // completed


    /**
     * Constructor
     */
    public function __construct($orderReference,$paymentState) {

        $this->orderReference = $orderReference;
        $this->paymentState = parseLinkIDPaymentState($paymentState);
    }
}

function parseLinkIDPaymentState($paymentState) {

    if ($paymentState == "STARTED") {
        return LinkIDPaymentResponse::STARTED;
    } else if ($paymentState == "DEFERRED") {
        return LinkIDPaymentResponse::DEFERRED;
    } else if ($paymentState == "WAITING_FOR_UPDATE") {
        return LinkIDPaymentResponse::WAITING_FOR_UPDATE;
    } else if ($paymentState == "FAILED") {
        return LinkIDPaymentResponse::FAILED;
    } else if ($paymentState == "REFUNDED") {
        return LinkIDPaymentResponse::REFUNDED;
    } else if ($paymentState == "REFUND_STARTED") {
        return LinkIDPaymentResponse::REFUND_STARTED;
    } else if ($paymentState == "PAYED" || $paymentState == "AUTHORIZED") {
        return LinkIDPaymentResponse::PAYED;
    }

}

?>