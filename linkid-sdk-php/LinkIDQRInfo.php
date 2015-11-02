<?php

class LinkIDQRInfo
{
    public $qrImage;
    public $qrEncoded;
    public $qrCodeURL;
    public $qrContent;
    public $mobile;
    public $targetBlank;

    /**
     * LinkIDQRInfo constructor.
     * @param $qrImage
     * @param $qrEncoded
     * @param $qrCodeURL
     * @param $qrContent
     * @param $mobile
     * @param $targetBlank
     */
    public function __construct($qrImage, $qrEncoded, $qrCodeURL, $qrContent, $mobile, $targetBlank)
    {
        $this->qrImage = $qrImage;
        $this->qrEncoded = $qrEncoded;
        $this->qrCodeURL = $qrCodeURL;
        $this->qrContent = $qrContent;
        $this->mobile = $mobile;
        $this->targetBlank = $targetBlank;
    }


}