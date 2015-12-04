<?php

class LinkIDWalletTransaction
{

    public $walletId;
    public $creationDate;
    public $transactionId;
    public $amount;
    public $currency;
    public $walletCoin;
    public $refundAmount;
    public $paymentDescription;

    function __construct($walletId, $creationDate, $transactionId, $amount, $currency, $walletCoin, $refundAmount, $paymentDescription)
    {
        $this->walletId = $walletId;
        $this->creationDate = $creationDate;
        $this->transactionId = $transactionId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->walletCoin = $walletCoin;
        $this->refundAmount = $refundAmount;
        $this->paymentDescription = $paymentDescription;
    }

}

function parseLinkIDWalletTransaction($xmlWalletTransaction)
{
    return new LinkIDWalletTransaction(
        isset($xmlWalletTransaction->walletId) ? $xmlWalletTransaction->walletId : null,
        isset($xmlWalletTransaction->creationDate) ? $xmlWalletTransaction->creationDate : null,
        isset($xmlWalletTransaction->transactionId) ? $xmlWalletTransaction->transactionId : null,
        isset($xmlWalletTransaction->amount) ? $xmlWalletTransaction->amount : null,
        isset($xmlWalletTransaction->currency) ? parseLinkIDCurrency($xmlWalletTransaction->currency) : null,
        isset($xmlWalletTransaction->walletCoin) ? $xmlWalletTransaction->walletCoin : null,
        isset($xmlWalletTransaction->refundAmount) ? $xmlWalletTransaction->refundAmount : null,
        isset($xmlWalletTransaction->paymentDescription) ? $xmlWalletTransaction->paymentDescription : null
        );
}