<?php

require_once('LinkIDWSSoapClient.php');
require_once('LinkIDPaymentOrder.php');
require_once('LinkIDParkingSession.php');
require_once('LinkIDReportDateFilter.php');
require_once('LinkIDReportApplicationFilter.php');
require_once('LinkIDReportWalletFilter.php');
require_once('LinkIDWalletReportTransaction.php');

/*
 * linkID Reporting WS client
 *
 * @author Wim Vandenhaute
 */

class LinkIDReportingClient
{

    private $client;

    /**
     * Constructor
     *
     * @param $linkIDHost string the linkID host ( https://<linkIDHost>/linkid-ws-username
     * @param $username string the WS-Security username
     * @param $password string the WS-Security password
     * @param array $options [optional]
     *
     */
    public function __construct($linkIDHost, $username, $password, array $options = array())
    {

        $wsdlLocation = "https://" . $linkIDHost . "/linkid-ws-username/reporting40?wsdl";

        $this->client = new LinkIDWSSoapClient($wsdlLocation);
        $this->client->__setUsernameToken($username, $password, 'PasswordDigest');

    }

    /**
     * @param $walletOrganizationId String the wallet OrganizationID
     * @param null $dateFilter LinkIDReportDateFilter optional date filter
     * @param null $applicationFilter
     * @param null $walletFilter
     *
     * @throws Exception something went wrong, check the error code in the exception
     * @return LinkIDWalletReportTransaction[] list of wallet transactions matching your search.
     */
    public function getWalletReport($walletOrganizationId, $dateFilter = null, $applicationFilter = null, $walletFilter = null)
    {
        $requestParams = new stdClass;

        $requestParams->walletOrganizationId = $walletOrganizationId;

        if (null != $dateFilter) {
            $requestParams->dateFilter = new stdClass;
            $requestParams->dateFilter->startDate = $dateFilter->startDate->format(DateTime::ATOM);
            if (null != $dateFilter->endDate) {
                $requestParams->dateFilter->endDate = $dateFilter->endDate->format(DateTime::ATOM);
            }
        }

        if (null != $applicationFilter) {
            $requestParams->applicationFilter = new stdClass;
            $requestParams->applicationFilter->applicationName = $applicationFilter->applicationName;
        }

        if (null != $walletFilter) {
            $requestParams->walletFilter = new stdClass;
            $requestParams->walletFilter->walletId = $walletFilter->walletId;
            $requestParams->walletFilter->userId = $walletFilter->userId;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->walletReport($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        if (!isset($response->transactions)) {
            return null;
        }
        $xmlTransactions = $response->transactions;

        // payment transactions
        $transactions = array();
        if (is_array($xmlTransactions)) {
            foreach ($xmlTransactions as $xmlTransaction) {
                $transactions[] = parseLinkIDWalletReportTransaction($xmlTransaction);
            }
        } else {
            $transactions[] = parseLinkIDWalletReportTransaction($xmlTransactions);
        }


        return $transactions;

    }
}