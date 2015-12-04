<?php

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 04/12/15
 * Time: 13:07
 */
class LinkIDWalletInfoReport
{

    public $walletId;
    public $created;
    public $removed;
    public $userId;
    public $organizationId;
    public $organization;
    public $balance;

    public function __construct($walletId, $created, $removed, $userId, $organizationId, $organization, $balance)
    {
        $this->walletId = $walletId;
        $this->created = $created;
        $this->removed = $removed;
        $this->userId = $userId;
        $this->organizationId = $organizationId;
        $this->organization = $organization;
        $this->balance = $balance;
    }


}

function parseLinkIDWalletInfoReport($xmlWalletInfoReport)
{
    return new LinkIDWalletInfoReport(
        isset($xmlWalletInfoReport->walletId) ? $xmlWalletInfoReport->walletId : null,
        isset($xmlWalletInfoReport->created) ? $xmlWalletInfoReport->created : null,
        isset($xmlWalletInfoReport->removed) ? $xmlWalletInfoReport->removed : null,
        isset($xmlWalletInfoReport->userId) ? $xmlWalletInfoReport->userId : null,
        isset($xmlWalletInfoReport->organizationId) ? $xmlWalletInfoReport->organizationId : null,
        isset($xmlWalletInfoReport->organization) ? $xmlWalletInfoReport->organization : null,
        isset($xmlWalletInfoReport->balance) ? $xmlWalletInfoReport->balance : null
    );
}