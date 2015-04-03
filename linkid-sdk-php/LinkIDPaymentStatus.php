<?php

require_once('LinkIDPaymentDetails.php');

/*
 * LinkID Payment Status
 *
 * @author Wim Vandenhaute
 */

class LinkIDPaymentStatus
{

    public $paymentState;
    public $captured;
    public $amountPayed;
    public $paymentDetails;

    /**
     * Constructor
     */
    public function __construct($paymentState, $captured, $amountPayed, $paymentDetails)
    {

        $this->paymentState = $paymentState;
        $this->captured = $captured;
        $this->amountPayed = $amountPayed;
        $this->paymentDetails = $paymentDetails;
    }
}