<?php

require_once('LinkIDPaymentState.php');
require_once('LinkIDPaymentMethodType.php');
require_once('LinkIDCurrency.php');

class LinkIDPaymentTransaction
{

    public $paymentMethodType;
    public $paymentMethod;
    public $paymentState;
    public $creationDate;
    public $authorizationDate;
    public $capturedDate;
    public $docdataReference;
    public $amount;
    public $currency;

    function __construct($paymentMethodType, $paymentMethod, $paymentState, $creationDate, $authorizationDate, $capturedDate, $docdataReference, $amount, $currency)
    {
        $this->paymentMethodType = $paymentMethodType;
        $this->paymentMethod = $paymentMethod;
        $this->paymentState = $paymentState;
        $this->creationDate = $creationDate;
        $this->authorizationDate = $authorizationDate;
        $this->capturedDate = $capturedDate;
        $this->docdataReference = $docdataReference;
        $this->amount = $amount;
        $this->currency = $currency;
    }


}