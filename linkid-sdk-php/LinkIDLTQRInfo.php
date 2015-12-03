<?php

require_once('LinkIDQRInfo.php');
require_once('LinkIDLTQRContent.php');
require_once('LinkIDLTQRLockType.php');

class LinkIDLTQRInfo
{

    public $ltqrReference;
    public $sessionId;
    public $created;
    //
    public $qrCodeInfo;
    //
    public $content;
    //
    public $lockType;
    public $locked;
    //
    public $waitForUnblock;
    public $blocked;

    /**
     * LinkIDLTQRInfo constructor.
     * @param string $ltqrReference
     * @param string $sessionId
     * @param DateTime $created
     * @param LinkIDQRInfo $qrCodeInfo
     * @param LinkIDLTQRContent $content
     * @param LinkIDLTQRLockType $lockType
     * @param bool $locked
     * @param bool $waitForUnblock
     * @param bool $blocked
     */
    public function __construct($ltqrReference, $sessionId, $created, $qrCodeInfo, $content, $lockType, $locked, $waitForUnblock, $blocked)
    {
        $this->ltqrReference = $ltqrReference;
        $this->sessionId = $sessionId;
        $this->created = $created;
        $this->qrCodeInfo = $qrCodeInfo;
        $this->content = $content;
        $this->lockType = $lockType;
        $this->locked = $locked;
        $this->waitForUnblock = $waitForUnblock;
        $this->blocked = $blocked;
    }


}