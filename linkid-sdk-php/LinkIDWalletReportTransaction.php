<?php

require_once('LinkIDWalletTransaction.php');
require_once('LinkIDWalletReportType.php');
require_once('LinkIDWalletReportInfo.php');

class LinkIDWalletReportTransaction extends LinkIDWalletTransaction
{

    public $id;
    public $userId;
    public $applicationName;
    public $applicationFriendly;
    public $type;
    public $reportInfo;

    function __construct($id, $walletId, $creationDate, $refundedDate, $committedDate, $transactionId, $amount, $currency,
                         $walletCoin, $refundAmount, $paymentDescription, $userId, $applicationName,
                         $applicationFriendly, $type, $reportInfo)
    {
        parent::__construct($walletId, $creationDate, $refundedDate, $committedDate, $transactionId, $amount, $currency,
            $walletCoin, $refundAmount, $paymentDescription);

        $this->id = $id;
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
        isset($xmlWalletReportTransaction->id) ? $xmlWalletReportTransaction->id : null,
        isset($xmlWalletReportTransaction->walletId) ? $xmlWalletReportTransaction->walletId : null,
        isset($xmlWalletReportTransaction->creationDate) ? $xmlWalletReportTransaction->creationDate : null,
        isset($xmlWalletReportTransaction->refundedDate) ? $xmlWalletReportTransaction->refundedDate : null,
        isset($xmlWalletReportTransaction->committedDate) ? $xmlWalletReportTransaction->committedDate : null,
        isset($xmlWalletReportTransaction->transactionId) ? $xmlWalletReportTransaction->transactionId : null,
        isset($xmlWalletReportTransaction->amount) ? $xmlWalletReportTransaction->amount : null,
        isset($xmlWalletReportTransaction->currency) ? parseLinkIDCurrency($xmlWalletReportTransaction->currency) : null,
        isset($xmlWalletReportTransaction->walletCoin) ? $xmlWalletReportTransaction->walletCoin : null,
        isset($xmlWalletReportTransaction->refundAmount) ? $xmlWalletReportTransaction->refundAmount : null,
        isset($xmlWalletReportTransaction->paymentDescription) ? $xmlWalletReportTransaction->paymentDescription : null,
        isset($xmlWalletReportTransaction->userId) ? $xmlWalletReportTransaction->userId : null,
        isset($xmlWalletReportTransaction->applicationName) ? $xmlWalletReportTransaction->applicationName : null,
        isset($xmlWalletReportTransaction->applicationFriendly) ? $xmlWalletReportTransaction->applicationFriendly : null,
        isset($xmlWalletReportTransaction->type) ? parseLinkIDWalletReportType($xmlWalletReportTransaction->type) : null,
        $reportInfo
    );
}