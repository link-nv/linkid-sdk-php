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

        $wsdlLocation = "http://" . $linkIDHost . "/linkid-ws-username/payment30?wsdl";

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

        $paymentTransactions = array();
        $walletTransactions = array();

        // payment transactions
        if (isset($response->paymentDetails->paymentTransactions)) {
            $xmlPaymentTransactions = $response->paymentDetails->paymentTransactions;
            if (is_array($xmlPaymentTransactions)) {
                foreach ($xmlPaymentTransactions as $xmlPaymentTransaction) {
                    $paymentTransactions[] = parseLinkIDPaymentTransaction($xmlPaymentTransaction);
                }
            } else {
                $paymentTransactions[] = parseLinkIDPaymentTransaction($xmlPaymentTransactions);
            }
        }

        // wallet transactions
        if (isset($response->paymentDetails->walletTransactions)) {
            $xmlWalletTransactions = $response->paymentDetails->walletTransactions;
            if (is_array($xmlWalletTransactions)) {
                foreach ($xmlWalletTransactions as $xmlWalletTransaction) {
                    $walletTransactions[] = parseLinkIDWalletTransaction($xmlWalletTransaction);
                }
            } else {
                $walletTransactions[] = parseLinkIDWalletTransaction($xmlWalletTransactions);
            }
        }

        $paymentDetails = new LinkIDPaymentDetails($paymentTransactions, $walletTransactions);

        return new LinkIDPaymentStatus(parseLinkIDPaymentState($response->paymentStatus), $response->captured, $response->amountPayed, $paymentDetails);
    }

}