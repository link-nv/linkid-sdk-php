<?php

require_once('LinkIDPaymentDetails.php');

/*
 * LinkID Payment Status
 *
 * @author Wim Vandenhaute
 */

class LinkIDPaymentStatus
{

    public $orderReference;
    public $userId;
    public $paymentState;
    public $authorized;
    public $captured;
    public $amountPayed;
    public $amount;
    public $currency;
    public $description;
    public $profile;
    public $created;
    public $mandateReference;
    public $paymentDetails;

    function __construct($orderReference, $userId, $paymentState, $authorized, $captured, $amountPayed, $amount, $currency, $description, $profile, $created, $mandateReference, $paymentDetails)
    {
        $this->orderReference = $orderReference;
        $this->userId = $userId;
        $this->paymentState = $paymentState;
        $this->authorized = $authorized;
        $this->captured = $captured;
        $this->amountPayed = $amountPayed;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;
        $this->profile = $profile;
        $this->created = $created;
        $this->mandateReference = $mandateReference;
        $this->paymentDetails = $paymentDetails;
    }


}