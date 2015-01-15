<?php

require_once('LinkIDPaymentResponse.php');

/*
 * LinkID Payment Status
 *
 * @author Wim Vandenhaute
 */

class LinkIDPaymentStatus
{

    public $paymentState;
    public $captured;

    /**
     * Constructor
     */
    public function __construct($paymentState, $captured)
    {

        $this->paymentState = $paymentState;
        $this->captured = $captured;
    }
}