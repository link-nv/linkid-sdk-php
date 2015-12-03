<?php

class LinkIDQRInfo
{
    public $qrImage;
    public $qrEncoded;
    public $qrCodeURL;
    public $qrContent;
    public $mobile;

    /**
     * LinkIDQRInfo constructor.
     * @param $qrImage
     * @param $qrEncoded
     * @param $qrCodeURL
     * @param $qrContent
     * @param $mobile
     */
    public function __construct($qrImage, $qrEncoded, $qrCodeURL, $qrContent, $mobile)
    {
        $this->qrImage = $qrImage;
        $this->qrEncoded = $qrEncoded;
        $this->qrCodeURL = $qrCodeURL;
        $this->qrContent = $qrContent;
        $this->mobile = $mobile;
    }


}