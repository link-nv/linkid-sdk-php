<?php

require_once('LinkIDLTQRSession.php');

class LinkIDLTQRPushResponse
{

    public $ltqrSession;
    public $errorCode;
    public $errorMessage;

    /**
     * LinkIDLTQRPushResponse constructor.
     * @param $ltqrSession
     * @param $errorCode
     * @param $errorMessage
     */
    public function __construct($ltqrSession, $errorCode = null, $errorMessage = null)
    {
        $this->ltqrSession = $ltqrSession;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }


}