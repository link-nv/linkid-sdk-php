<?php

require_once('LinkIDWSSoapClient.php');
require_once('LinkIDPaymentOrder.php');
require_once('LinkIDParkingSession.php');

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
     */
    public function __construct($linkIDHost, $username, $password)
    {

        $wsdlLocation = "http://" . $linkIDHost . "/linkid-ws-username/reporting20?wsdl";

        $this->client = new LinkIDWSSoapClient($wsdlLocation);
        $this->client->__setUsernameToken($username, $password, 'PasswordDigest');

    }

    /**
     * @param $startDate DateTime optional start date, ignored if orderReferences or mandateReferences are specified
     * @param $endDate DateTime optional end date, not specified means till now
     * @param $orderReferences array optional list of order references
     * @param $mandateReferences array optional list of mandate references
     *
     * @return LinkIDPaymentOrder[] list of payment orders matching your search.
     */
    public function getPaymentReport($startDate = null, $endDate = null, $orderReferences = null, $mandateReferences = null)
    {
        $requestParams = new stdClass;

        if (null != $startDate) {
            $requestParams->startDate = $startDate->format(DateTime::ATOM);
        }
        if (null != $endDate) {
            $requestParams->endDate = $endDate->format(DateTime::ATOM);
        }
        if (null != $orderReferences) {
            $requestParams->orderReferences = array();
            foreach ($orderReferences as $orderReference) {
                $requestParams->orderReferences[] = $orderReference;
            }
        }
        if (null != $mandateReferences) {
            $requestParams->mandateReferences = array();
            foreach ($mandateReferences as $mandateReference) {
                $requestParams->mandateReferences[] = $mandateReference;
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->paymentReport($requestParams);

        if (!isset($response->orders)) {
            return null;
        }
        $xmlOrders = $response->orders;

        // payment transactions
        $orders = array();
        if (is_array($xmlOrders)) {
            foreach ($xmlOrders as $xmlOrder) {
                $orders[] = parseLinkIDPaymentOrder($xmlOrder);
            }
        } else {
            $orders[] = parseLinkIDPaymentOrder($xmlOrders);
        }


        return $orders;
    }


    /**
     * @param $startDate DateTime optional start date, ignored if barCodes,... are specified
     * @param $endDate DateTime optional end date, not specified means till now
     * @param $barCodes array optional list of barcodes
     * @param $ticketNumbers array optional list of ticket numbers
     * @param $dtaKeys array optional list of dta keys
     * @param $parkings array optional list of parkings
     *
     * @return LinkIDParkingSession[] list of parking sessions matching your search.
     */
    public function getParkingReport($startDate = null, $endDate = null, $barCodes = null, $ticketNumbers = null, $dtaKeys = null, $parkings = null)
    {

        $requestParams = new stdClass;

        if (null != $startDate) {
            $requestParams->startDate = $startDate->format(DateTime::ATOM);
        }
        if (null != $endDate) {
            $requestParams->endDate = $endDate->format(DateTime::ATOM);
        }
        if (null != $barCodes) {
            $requestParams->barCodes = array();
            foreach ($barCodes as $barCode) {
                $requestParams->barCodes[] = $barCode;
            }
        }
        if (null != $ticketNumbers) {
            $requestParams->ticketNumbers = array();
            foreach ($ticketNumbers as $ticketNumber) {
                $requestParams->ticketNumbers[] = $ticketNumber;
            }
        }
        if (null != $dtaKeys) {
            $requestParams->dtaKeys = array();
            foreach ($dtaKeys as $dtaKey) {
                $requestParams->dtaKeys[] = $dtaKey;
            }
        }
        if (null != $parkings) {
            $requestParams->parkings = array();
            foreach ($parkings as $parking) {
                $requestParams->parkings[] = $parking;
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->parkingReport($requestParams);

        if (!isset($response->sessions)) {
            return null;
        }
        $xmlSessions = $response->sessions;

        // payment transactions
        $sessions = array();
        if (is_array($xmlSessions)) {
            foreach ($xmlSessions as $xmlSession) {
                $sessions[] = parseLinkIDParkingSession($xmlSession);
            }
        } else {
            $sessions[] = parseLinkIDParkingSession($xmlSessions);
        }


        return $sessions;
    }
}