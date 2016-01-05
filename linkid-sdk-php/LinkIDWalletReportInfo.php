<?php

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 05/01/16
 * Time: 16:34
 */
class LinkIDWalletReportInfo
{

    public $reference;
    public $description;

    /**
     * LinkIDWalletReportInfo constructor.
     * @param $reference
     * @param $description
     */
    public function __construct($reference, $description)
    {
        $this->reference = $reference;
        $this->description = $description;
    }


}