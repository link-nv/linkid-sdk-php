<?php

abstract class LinkIDWalletReportType
{

    const USER_TRANSACTION = 1;
    const WALLET_ADD = 2;
    const WALLET_REMOVE = 2;
    const WALLET_UNREMOVE = 2;
    const APPLICATION_ADD_CREDIT_INITIAL = 2;
    const APPLICATION_ADD_CREDIT = 2;
    const APPLICATION_REMOVE_CREDIT = 2;
    const APPLICATION_REFUND = 2;

}

function parseLinkIDWalletReportType($type)
{

    if (null == $type) return null;

    if ($type == "USER_TRANSACTION") {
        return LinkIDWalletReportType::USER_TRANSACTION;
    } else if ($type == "WALLET_ADD") {
        return LinkIDWalletReportType::WALLET_ADD;
    } else if ($type == "WALLET_REMOVE") {
        return LinkIDWalletReportType::WALLET_REMOVE;
    } else if ($type == "WALLET_UNREMOVE") {
        return LinkIDWalletReportType::WALLET_UNREMOVE;
    } else if ($type == "APPLICATION_ADD_CREDIT_INITIAL") {
        return LinkIDWalletReportType::APPLICATION_ADD_CREDIT_INITIAL;
    } else if ($type == "APPLICATION_ADD_CREDIT") {
        return LinkIDWalletReportType::APPLICATION_ADD_CREDIT;
    } else if ($type == "APPLICATION_REMOVE_CREDIT") {
        return LinkIDWalletReportType::APPLICATION_REMOVE_CREDIT;
    } else if ($type == "APPLICATION_REFUND") {
        return LinkIDWalletReportType::APPLICATION_REFUND;
    }

    throw new Exception("Unexpected wallet report type: " . $type);

}
