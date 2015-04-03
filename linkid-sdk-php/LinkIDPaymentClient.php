<?php

require_once('LinkIDWSSoapClient.php');
require_once('LinkIDPaymentState.php');
require_once('LinkIDPaymentStatus.php');

/*
 * linkID Payment WS client
 *
 * @author Wim Vandenhaute
 */

class LinkIDPaymentClient
{

    private $client;

    /**
     * Constructor
     */
    public function __construct($linkIDHost, $username, $password)
    {

        $wsdlLocation = "https://" . $linkIDHost . "/linkid-ws-username/payment30?wsdl";

        $this->client = new LinkIDWSSoapClient($wsdlLocation);
        $this->client->__setUsernameToken($username, $password, 'PasswordDigest');
    }

    public function getStatus($orderReference)
    {

        $requestParams = array(
            'orderReference' => $orderReference
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->status($requestParams);

        if (null == $response) throw new Exception("Failed to get payment status...");

        if (null == $response->paymentStatus) return LinkIDPaymentState::STARTED;

        $xmlPaymentTransactions = $response->paymentDetails->paymentTransactions;
        $xmlWalletTransactions = $response->paymentDetails->walletTransactions;

        // payment transactions
        $paymentTransactions = array();
        if (is_array($xmlPaymentTransactions)) {
            foreach ($xmlPaymentTransactions as $xmlPaymentTransaction) {
                $paymentTransactions[] = $this->convertPaymentTransaction($xmlPaymentTransaction);
            }
        } else {
            $paymentTransactions[] = $this->convertPaymentTransaction($xmlPaymentTransactions);
        }

        // wallet transactions
        $walletTransactions = array();
        if (is_array($xmlWalletTransactions)) {
            foreach ($xmlWalletTransactions as $xmlWalletTransaction) {
                $walletTransactions[] = $this->convertWalletTransaction($xmlWalletTransaction);
            }
        } else {
            $walletTransactions[] = $this->convertPaymentTransaction($xmlWalletTransactions);
        }

        $paymentDetails = new LinkIDPaymentDetails($paymentTransactions, $walletTransactions);

        return new LinkIDPaymentStatus(parseLinkIDPaymentState($response->paymentStatus), $response->captured, $response->amountPayed, $paymentDetails);
    }

    private function convertPaymentTransaction($xmlPaymentTransaction)
    {
        return new LinkIDPaymentTransaction(parseLinkIDPaymentMethodType($xmlPaymentTransaction->paymentMethodType), $xmlPaymentTransaction->paymentMethod,
            parseLinkIDPaymentState($xmlPaymentTransaction->paymentState), $xmlPaymentTransaction->creationDate, $xmlPaymentTransaction->authorizationDate,
            $xmlPaymentTransaction->capturedDate, $xmlPaymentTransaction->docdataReference, $xmlPaymentTransaction->amount, parseLinkIDCurrency($xmlPaymentTransaction->currency));
    }

    private function convertWalletTransaction($xmlWalletTransaction)
    {
        return new LinkIDWalletTransaction($xmlWalletTransaction->walletId, $xmlWalletTransaction->creationDate, $xmlWalletTransaction->transactionId,
            $xmlWalletTransaction->amount, parseLinkIDCurrency($xmlWalletTransaction->currency));
    }

}