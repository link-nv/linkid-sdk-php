<?php

require_once('LinkIDLTQRContent.php');

class LinkIDLTQRPushContent
{
    /**
     * @var LinkIDLTQRContent
     */
    public $content;
    /**
     * @var string
     */
    public $userAgent;
    /**
     * @var LinkIDLTQRLockType
     */
    public $lockType;

    /**
     * LinkIDLTQRPushContent constructor.
     * @param LinkIDLTQRContent $content
     * @param string $userAgent
     * @param LinkIDLTQRLockType $lockType
     */
    public function __construct(LinkIDLTQRContent $content, $userAgent, $lockType)
    {
        $this->content = $content;
        $this->userAgent = $userAgent;
        $this->lockType = $lockType;
    }


}