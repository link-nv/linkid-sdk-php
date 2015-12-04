<?php

require_once('LinkIDWalletReportTransaction.php');

class LinkIDWalletReport
{
    /**
     * @var int
     */
    public $total;
    /**
     * @var array
     */
    public $walletTransactions;

    /**
     * LinkIDWalletReport constructor.
     * @param int $total
     * @param array $walletTransactions
     */
    public function __construct($total, array $walletTransactions)
    {
        $this->total = $total;
        $this->walletTransactions = $walletTransactions;
    }


}