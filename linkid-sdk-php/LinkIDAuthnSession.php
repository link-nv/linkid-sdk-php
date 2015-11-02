<?php

require_once('LinkIDQRInfo.php');

class LinkIDAuthnSession
{

    public $sessionId;
    public $qrCodeInfo;

    /**
     * LinkIDAuthnSession constructor.
     * @param $sessionId
     * @param $qrCodeInfo
     */
    public function __construct($sessionId, $qrCodeInfo)
    {
        $this->sessionId = $sessionId;
        $this->qrCodeInfo = $qrCodeInfo;
    }


}
