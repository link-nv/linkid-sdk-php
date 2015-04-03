<?php

class LinkIDWalletTransaction
{

    public $walletId;
    public $creationDate;
    public $transactionId;
    public $amount;
    public $currency;

    function __construct($walletId, $creationDate, $transactionId, $amount, $currency)
    {
        $this->walletId = $walletId;
        $this->creationDate = $creationDate;
        $this->transactionId = $transactionId;
        $this->amount = $amount;
        $this->currency = $currency;
    }


}