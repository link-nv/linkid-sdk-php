<?php

require_once('LinkIDPaymentState.php');
require_once('LinkIDQRInfo.php');

class LinkIDLTQRClientSession
{
    public $ltqrReference;
    public $qrCodeInfo;
    public $clientSessionId;
    public $userId;
    public $created;

    public $paymentOrderReference;
    public $paymentState;

    /**
     * LinkIDLTQRClientSession constructor.
     * @param string $ltqrReference
     * @param LinkIDQRInfo $qrCodeInfo
     * @param string $clientSessionId
     * @param string $userId
     * @param DateTime $created
     * @param string $paymentOrderReference
     * @param LinkIDPaymentState $paymentState
     */
    public function __construct($ltqrReference, $qrCodeInfo, $clientSessionId, $userId, $created, $paymentOrderReference, $paymentState)
    {
        $this->ltqrReference = $ltqrReference;
        $this->qrCodeInfo = $qrCodeInfo;
        $this->clientSessionId = $clientSessionId;
        $this->userId = $userId;
        $this->created = $created;
        $this->paymentOrderReference = $paymentOrderReference;
        $this->paymentState = $paymentState;
    }


}
