<?php

/*
 * LinkID LTQR Session
 *
 * @author Wim Vandenhaute
 */

class LinkIDLTQRSession
{

    public $ltqrReference;
    public $qrCodeInfo;

    public $paymentOrderReference;

    /**
     * Constructor
     */
    public function __construct($ltqrReference, $qrCodeInfo, $paymentOrderReference)
    {

        $this->ltqrReference = $ltqrReference;
        $this->qrCodeInfo = $qrCodeInfo;
        $this->paymentOrderReference = $paymentOrderReference;
    }
}