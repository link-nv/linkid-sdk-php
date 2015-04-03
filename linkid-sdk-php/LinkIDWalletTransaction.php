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

function parseLinkIDWalletTransaction($xmlWalletTransaction)
{
    return new LinkIDWalletTransaction(
        isset($xmlWalletTransaction->walletId) ? $xmlWalletTransaction->walletId : null,
        isset($xmlWalletTransaction->creationDate) ? $xmlWalletTransaction->creationDate : null,
        isset($xmlWalletTransaction->transactionId) ? $xmlWalletTransaction->transactionId : null,
        isset($xmlWalletTransaction->amount) ? $xmlWalletTransaction->amount : null,
        isset($xmlWalletTransaction->currency) ? parseLinkIDCurrency($xmlWalletTransaction->currency) : null);
}