<?php

require_once('LinkIDWalletTransaction.php');

class LinkIDWalletReportTransaction extends LinkIDWalletTransaction
{

    public $userId;
    public $applicationName;

    function __construct($walletId, $creationDate, $transactionId, $amount, $currency, $userId, $applicationName)
    {
        $this->walletId = $walletId;
        $this->creationDate = $creationDate;
        $this->transactionId = $transactionId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->userId = $userId;
        $this->applicationName = $applicationName;
    }

}

function parseLinkIDWalletReportTransaction($xmlWalletReportTransaction)
{
    return new LinkIDWalletReportTransaction(
        isset($xmlWalletReportTransaction->walletId) ? $xmlWalletReportTransaction->walletId : null,
        isset($xmlWalletReportTransaction->creationDate) ? $xmlWalletReportTransaction->creationDate : null,
        isset($xmlWalletReportTransaction->transactionId) ? $xmlWalletReportTransaction->transactionId : null,
        isset($xmlWalletReportTransaction->amount) ? $xmlWalletReportTransaction->amount : null,
        isset($xmlWalletReportTransaction->currency) ? parseLinkIDCurrency($xmlWalletReportTransaction->currency) : null,
        isset($xmlWalletReportTransaction->userId) ? $xmlWalletReportTransaction->userId : null,
        isset($xmlWalletReportTransaction->applicationName) ? $xmlWalletReportTransaction->applicationName : null
    );
}