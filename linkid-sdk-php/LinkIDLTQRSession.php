<?php

require_once('LinkIDQRInfo.php');

class LinkIDLTQRSession
{

    public $ltqrReference;
    public $qrCodeInfo;

    public $paymentOrderReference;

    /**
     * LinkIDLTQRSession constructor.
     * @param string $ltqrReference
     * @param LinkIDQRInfo $qrCodeInfo
     * @param string $paymentOrderReference
     */
    public function __construct($ltqrReference, $qrCodeInfo, $paymentOrderReference)
    {

        $this->ltqrReference = $ltqrReference;
        $this->qrCodeInfo = $qrCodeInfo;
        $this->paymentOrderReference = $paymentOrderReference;
    }
}