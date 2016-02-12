<?php

require_once('LinkIDWalletTransaction.php');
require_once('LinkIDWalletReportType.php');
require_once('LinkIDWalletReportInfo.php');

class LinkIDWalletReportTransaction extends LinkIDWalletTransaction
{

    public $userId;
    public $applicationName;
    public $applicationFriendly;
    public $type;
    public $reportInfo;

    function __construct($walletId, $creationDate, $refundedDate, $committedDate, $transactionId, $amount, $currency,
                         $walletCoin, $userId, $applicationName, $applicationFriendly, $type, $reportInfo)
    {
        $this->walletId = $walletId;
        $this->creationDate = $creationDate;
        $this->refundedDate = $refundedDate;
        $this->committedDate = $committedDate;
        $this->transactionId = $transactionId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->walletCoin = $walletCoin;
        $this->userId = $userId;
        $this->applicationName = $applicationName;
        $this->applicationFriendly = $applicationFriendly;
        $this->type = $type;
        $this->reportInfo = $reportInfo;
    }

}

function parseLinkIDWalletReportTransaction($xmlWalletReportTransaction)
{
    $reportInfo = null;
    if (isset($xmlWalletReportTransaction->reportInfo)) {
        $reportInfo = new LinkIDWalletReportInfo(
            isset($xmlWalletReportTransaction->reportInfo->reference) ? $xmlWalletReportTransaction->reportInfo->reference : null,
            isset($xmlWalletReportTransaction->reportInfo->description) ? $xmlWalletReportTransaction->reportInfo->description : null
        );
    }

    return new LinkIDWalletReportTransaction(
        isset($xmlWalletReportTransaction->walletId) ? $xmlWalletReportTransaction->walletId : null,
        isset($xmlWalletReportTransaction->creationDate) ? $xmlWalletReportTransaction->creationDate : null,
        isset($xmlWalletReportTransaction->refundedDate) ? $xmlWalletReportTransaction->refundedDate : null,
        isset($xmlWalletReportTransaction->committedDate) ? $xmlWalletReportTransaction->committedDate : null,
        isset($xmlWalletReportTransaction->transactionId) ? $xmlWalletReportTransaction->transactionId : null,
        isset($xmlWalletReportTransaction->amount) ? $xmlWalletReportTransaction->amount : null,
        isset($xmlWalletReportTransaction->currency) ? parseLinkIDCurrency($xmlWalletReportTransaction->currency) : null,
        isset($xmlWalletReportTransaction->walletCoin) ? $xmlWalletReportTransaction->walletCoin : null,
        isset($xmlWalletReportTransaction->userId) ? $xmlWalletReportTransaction->userId : null,
        isset($xmlWalletReportTransaction->applicationName) ? $xmlWalletReportTransaction->applicationName : null,
        isset($xmlWalletReportTransaction->applicationFriendly) ? $xmlWalletReportTransaction->applicationFriendly : null,
        isset($xmlWalletReportTransaction->type) ? parseLinkIDWalletReportType($xmlWalletReportTransaction->type) : null,
        $reportInfo
    );
}