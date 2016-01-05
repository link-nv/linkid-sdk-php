<?php

require_once('LinkIDWSSoapClient.php');
require_once('LinkIDAuthenticationContext.php');
require_once('LinkIDSaml2.php');
require_once('LinkIDAuthnSession.php');
require_once('LinkIDAuthPollResponse.php');
require_once('LinkIDThemes.php');
require_once('LinkIDLocalization.php');
require_once('LinkIDLTQRContent.php');
require_once('LinkIDLTQRPushContent.php');
require_once('LinkIDLTQRPushResponse.php');
require_once('LinkIDLTQRLockType.php');
require_once('LinkIDLTQRSession.php');
require_once('LinkIDLTQRClientSession.php');
require_once('LinkIDLTQRInfo.php');
require_once('LinkIDPaymentStatus.php');
require_once('LinkIDPaymentOrder.php');
require_once('LinkIDParkingSession.php');
require_once('LinkIDReportDateFilter.php');
require_once('LinkIDReportApplicationFilter.php');
require_once('LinkIDReportWalletFilter.php');
require_once('LinkIDWalletReport.php');
require_once('LinkIDWalletReportTransaction.php');
require_once('LinkIDWalletInfoReport.php');
require_once('LinkIDWalletInfo.php');
require_once('LinkIDWalletReportInfo.php');

/*
 * linkID WS client
 *
 * @author Wim Vandenhaute
 */

class LinkIDClient
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

        $wsdlLocation = "https://" . $linkIDHost . "/linkid-ws-username/linkid31?wsdl";

        $this->client = new LinkIDWSSoapClient($wsdlLocation, $options);
        $this->client->__setUsernameToken($username, $password, 'PasswordDigest');

    }

    /**
     * @param LinkIDAuthenticationContext $authenticationContext the linkID authentication context
     * @param null $userAgent optional user agent string, for adding e.g. callback params to the QR code URL, android chrome URL needs to be http://linkidmauthurl/MAUTH/2/zUC8oA/eA==, ...
     * @return LinkIDAuthnSession the linkID authentication session containing the QR code image, URL, sessionId and client authentication context
     * @throws Exception something went wrong...
     */
    public function authStart($authenticationContext, $userAgent = null)
    {
        $saml2 = new LinkIDSaml2();
        $authnRequest = $saml2->generateAuthnRequest($authenticationContext);

        $requestParams = array(
            'any' => $authnRequest,
            'language' => $authenticationContext->language,
            'userAgent' => $userAgent,
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->authStart($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->error . " - " . $response->error->info);
        }

        return new LinkIDAuthnSession($response->success->sessionId, $this->convertQRCodeInfo($response->success->qrCodeInfo));
    }

    /**
     * @param string $sessionId
     * @param string $language
     * @return LinkIDAuthPollResponse
     * @throws Exception
     */
    public function authPoll($sessionId, $language = "en")
    {

        $requestParams = array(
            'sessionId' => $sessionId,
            'language' => $language
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->authPoll($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->error . " - " . $response->error->info);
        }

        $authenticationContext = null;
        if (isset($response->success->authenticationResponse) && null != $response->success->authenticationResponse->any) {

            $xml = new SimpleXMLElement($response->success->authenticationResponse->any);
            $saml2 = new LinkIDSaml2();
            $authenticationContext = $saml2->parseXmlAuthnResponse($xml);
        }

        return new LinkIDAuthPollResponse($response->success->authenticationState,
            isset($response->success->paymentState) ? $response->success->paymentState : null,
            isset($response->success->paymentMenuURL) ? $response->success->paymentMenuURL : null,
            $authenticationContext);
    }

    /**
     * Cancel a linkID authentication / payment session
     *
     * @param $sessionId string ID of session to cancel
     *
     * @throws Exception
     */
    public function authCancel($sessionId)
    {

        $requestParams = array(
            'sessionId' => $sessionId,
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->cancel($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->error . " - " . $response->error->info);
        }

    }

    /**
     * @param $sessionId callback sessionId
     * @return LinkIDAuthPollResponse
     * @throws Exception
     */
    public function callbackPull($sessionId)
    {

        $requestParams = array(
            'sessionId' => $sessionId
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->callbackPull($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->error . " - " . $response->error->info);
        }

        $saml2 = new LinkIDSaml2();
        return $saml2->parseXmlAuthnResponse($response->success->any);
    }

    /**
     * @param $applicationName string name of the application to fetch themes for
     * @return LinkIDThemes the themes found
     * @throws Exception
     */
    public function getThemes($applicationName)
    {
        $requestParams = array(
            'applicationName' => $applicationName
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->configThemes($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->error . " - " . $response->error->info);
        }

        $themes = array();
        foreach ($response->success->themes as $theme) {

            $themes[] = new LinkIDTheme($theme->name, $theme->defaultTheme,
                isset($theme->logo) ? $this->convertLocalizedImages($theme->logo) : null,
                isset($theme->authLogo) ? $this->convertLocalizedImages($theme->authLogo) : null,
                isset($theme->background) ? $this->convertLocalizedImages($theme->background) : null,
                isset($theme->tabletBackground) ? $this->convertLocalizedImages($theme->tabletBackground) : null,
                isset($theme->alternativeBackground) ? $this->convertLocalizedImages($theme->alternativeBackground) : null,
                isset($theme->backgroundColor) ? $theme->backgroundColor : null, isset($theme->textColor) ? $theme->textColor : null);
        }

        return new LinkIDThemes($themes);
    }

    /**
     * @param array $keys localization keys to fetch, array of strings
     * @return array localizations, array of LinkIDLocalization
     * @throws Exception
     */
    public function getLocalization($keys)
    {

        $requestParams = new stdClass;

        if (null != $keys) {
            $requestParams->key = array();
            foreach ($keys as $key) {
                $requestParams->key[] = $key;
            }
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->configLocalization($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->error . " - " . $response->error->info);
        }

        $localizations = array();

        foreach ($response->success->localization as $localization) {
            $values = array();
            foreach ($localization->values as $localizationValue) {
                $values[$localizationValue->languageCode] = $localizationValue->localized;
            }

            $localizations[] = new LinkIDLocalization($localization->key,
                parseLinkIDLocalizationKeyType($localization->type), $values);
        }

        return $localizations;
    }

    /**
     * @param string 0$orderReference
     * @return LinkIDPaymentStatus
     * @throws Exception
     */
    public function getPaymentStatus($orderReference)
    {

        $requestParams = array(
            'orderReference' => $orderReference
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->paymentStatus($requestParams);

        if (null == $response) throw new Exception("Failed to get payment status...");

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        $paymentTransactions = array();
        $walletTransactions = array();

        // payment transactions
        if (isset($response->success->paymentDetails->paymentTransactions)) {
            $xmlPaymentTransactions = $response->success->paymentDetails->paymentTransactions;
            if (is_array($xmlPaymentTransactions)) {
                foreach ($xmlPaymentTransactions as $xmlPaymentTransaction) {
                    $paymentTransactions[] = parseLinkIDPaymentTransaction($xmlPaymentTransaction);
                }
            } else {
                $paymentTransactions[] = parseLinkIDPaymentTransaction($xmlPaymentTransactions);
            }
        }

        // wallet transactions
        if (isset($response->success->paymentDetails->walletTransactions)) {
            $xmlWalletTransactions = $response->success->paymentDetails->walletTransactions;
            if (is_array($xmlWalletTransactions)) {
                foreach ($xmlWalletTransactions as $xmlWalletTransaction) {
                    $walletTransactions[] = parseLinkIDWalletTransaction($xmlWalletTransaction);
                }
            } else {
                $walletTransactions[] = parseLinkIDWalletTransaction($xmlWalletTransactions);
            }
        }

        $paymentDetails = new LinkIDPaymentDetails($paymentTransactions, $walletTransactions);

        return new LinkIDPaymentStatus(
            isset($response->success->orderReference) ? $response->success->orderReference : null,
            isset($response->success->userId) ? $response->success->userId : null,
            isset($response->success->paymentStatus) ? parseLinkIDPaymentState($response->success->paymentStatus) : null,
            isset($response->success->authorized) ? $response->success->authorized : false,
            isset($response->success->captured) ? $response->success->captured : false,
            isset($response->success->amountPayed) ? $response->success->amountPayed : null,
            isset($response->success->amount) ? $response->success->amount : null,
            isset($response->success->refundAmount) ? $response->success->refundAmount : null,
            isset($response->success->currency) ? parseLinkIDCurrency($response->success->currency) : null,
            isset($response->success->walletCoin) ? $response->success->walletCoin : null,
            isset($response->success->description) ? $response->success->description : null,
            isset($response->success->profile) ? $response->success->profile : null,
            isset($response->success->created) ? $response->success->created : null,
            isset($response->mandateReference) ? $response->mandateReference : null,
            $paymentDetails);

    }

    /**
     * @param string $orderReference order reference of order to capture
     * @throws Exception
     */
    public function paymentCapture($orderReference)
    {

        $requestParams = array(
            'orderReference' => $orderReference
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->paymentCapture($requestParams);

        if (null == $response) throw new Exception("Failed to capture payment...");

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }
    }

    /**
     * @param string $orderReference order reference of order to refund
     * @throws Exception
     */
    public function paymentRefund($orderReference)
    {

        $requestParams = array(
            'orderReference' => $orderReference
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->paymentRefund($requestParams);

        if (null == $response) throw new Exception("Failed to refund payment...");

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }
    }

    /**
     * @param string $mandateReference reference of the mandate
     * @param LinkIDPaymentContext $paymentContext payment context
     * @param string $language optional locale
     * @return string the payment order reference
     * @throws Exception
     */
    public function mandatePayment($mandateReference, $paymentContext, $language = "en")
    {

        $requestParams = new stdClass;

        $requestParams->paymentContext = new stdClass;
        $requestParams->paymentContext->amount = $paymentContext->amount;
        $requestParams->paymentContext->currency = linkIDCurrencyToString($paymentContext->currency);
        $requestParams->paymentContext->description = $paymentContext->description;
        $requestParams->paymentContext->orderReference = $paymentContext->orderReference;
        $requestParams->paymentContext->paymentProfile = $paymentContext->profile;
        $requestParams->paymentContext->paymentStatusLocation = $paymentContext->paymentStatusLocation;

        $requestParams->mandateReference = $mandateReference;
        $requestParams->language = $language;

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->mandatePayment($requestParams);

        if (null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        return $response->success->orderReference;

    }

    /**
     * @param LinkIDLTQRContent $content
     * @param string $userAgent
     * @param LinkIDLTQRLockType $lockType
     * @return LinkIDLTQRSession the LTQR session
     * @throws Exception
     */
    public function ltqrPush($content, $userAgent, $lockType)
    {

        $requestParams = new stdClass;

        $requestParams->content = $this->convertLTQRContent($content);
        $requestParams->userAgent = $userAgent;
        $requestParams->lockType = linkIDLTQRLockTypeToString($lockType);

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->ltqrPush($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        return new LinkIDLTQRSession($response->success->ltqrReference, $this->convertQRCodeInfo($response->success->qrCodeInfo),
            isset($response->success->paymentOrderReference) ? $response->success->paymentOrderReference : null);

    }

    /**
     * @param array $contents the LTQR codes to be pushed
     * @return array the created LTQR codes
     * @throws Exception
     */
    public function ltqrBulkPush($contents)
    {
        $requestParams = new stdClass;
        $requestParams->requests = array();
        foreach ($contents as $content) {
            $ltqrPushContent = new stdClass();

            $ltqrPushContent->content = $this->convertLTQRContent($content->content);
            $ltqrPushContent->userAgent = $content->userAgent;
            $ltqrPushContent->lockType = linkIDLTQRLockTypeToString($content->lockType);

            $requestParams->requests[] = $ltqrPushContent;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->ltqrBulkPush($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        $results = array();

        foreach ($response->success->responses as $ltqrPushResponse) {
            if (null != $ltqrPushResponse->success) {
                $results[] = new LinkIDLTQRPushResponse(new LinkIDLTQRSession($ltqrPushResponse->success->ltqrReference,
                    $this->convertQRCodeInfo($ltqrPushResponse->success->qrCodeInfo),
                    isset($ltqrPushResponse->success->paymentOrderReference) ? $ltqrPushResponse->success->paymentOrderReference : null));
            } else {
                $results[] = new LinkIDLTQRPushResponse(null, $ltqrPushResponse->error->errorCode, $ltqrPushResponse->error->errorMessage);
            }
        }

        return $results;
    }

    /**
     * @param string $ltqrReference
     * @param LinkIDLTQRContent $content
     * @param string $userAgent
     * @param bool $unlock
     * @param bool $unblock
     * @return LinkIDLTQRSession
     * @throws Exception
     */
    public function ltqrChange($ltqrReference, $content, $userAgent, $unlock, $unblock)
    {

        $requestParams = new stdClass;

        $requestParams->ltqrReference = $ltqrReference;
        $requestParams->content = $this->convertLTQRContent($content);
        $requestParams->userAgent = $userAgent;
        $requestParams->unlock = $unlock;
        $requestParams->unblock = $unblock;

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->ltqrChange($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        return new LinkIDLTQRSession($response->success->ltqrReference, $this->convertQRCodeInfo($response->success->qrCodeInfo),
            isset($response->success->paymentOrderReference) ? $response->success->paymentOrderReference : null);

    }

    /**
     * @param array $ltqrReferences
     * @param array $paymentOrderReferences
     * @param array $clientSessionIds
     * @return array
     * @throws Exception
     */
    public function ltqrPull($ltqrReferences = null, $paymentOrderReferences = null, $clientSessionIds = null)
    {

        $requestParams = new stdClass;

        if (null != $ltqrReferences) {
            $requestParams->ltqrReferences = array();
            foreach ($ltqrReferences as $ltqrReference) {
                $requestParams->ltqrReferences[] = $ltqrReference;
            }
        }

        if (null != $paymentOrderReferences) {
            $requestParams->paymentOrderReferences = array();
            foreach ($paymentOrderReferences as $paymentOrderReference) {
                $requestParams->paymentOrderReferences[] = $paymentOrderReference;
            }
        }

        if (null != $clientSessionIds) {
            $requestParams->clientSessionIds = array();
            foreach ($clientSessionIds as $clientSessionId) {
                $requestParams->clientSessionIds[] = $clientSessionId;
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->ltqrPull($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        $clientSessions = array();
        foreach ($response->success as $session) {

            $clientSessions[] = new LinkIDLTQRClientSession($session->ltqrReference,
                $this->convertQRCodeInfo($session->qrCodeInfo), $session->clientSessionId, $session->userId,
                $session->created, isset($session->paymentOrderReference) ? $session->paymentOrderReference : null,
                isset($session->paymentStatus) ? $session->paymentStatus : null);
        }

        return $clientSessions;

    }

    /**
     * @param array $ltqrReferences
     * @param array $paymentOrderReferences
     * @param array $clientSessionIds
     * @throws Exception
     */
    public function ltqrRemove($ltqrReferences = null, $paymentOrderReferences = null, $clientSessionIds = null)
    {

        $requestParams = new stdClass;

        if (null != $ltqrReferences) {
            $requestParams->ltqrReferences = array();
            foreach ($ltqrReferences as $ltqrReference) {
                $requestParams->ltqrReferences[] = $ltqrReference;
            }
        }

        if (null != $paymentOrderReferences) {
            $requestParams->paymentOrderReferences = array();
            foreach ($paymentOrderReferences as $paymentOrderReference) {
                $requestParams->paymentOrderReferences[] = $paymentOrderReference;
            }
        }

        if (null != $clientSessionIds) {
            $requestParams->clientSessionIds = array();
            foreach ($clientSessionIds as $clientSessionId) {
                $requestParams->clientSessionIds[] = $clientSessionId;
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->ltqrRemove($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        // all good, return
        return;

    }

    /**
     * @param array $ltqrReferences
     * @param string $userAgent
     * @return array
     * @throws Exception
     */
    public function ltqrInfo($ltqrReferences, $userAgent)
    {

        $requestParams = new stdClass;

        if (null == $ltqrReferences) {
            throw new Exception('No LTQR references to fetch info for...');
        }

        $requestParams->ltqrReferences = array();
        foreach ($ltqrReferences as $ltqrReference) {
            $requestParams->ltqrReferences[] = $ltqrReference;
        }
        $requestParams->userAgent = $userAgent;

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->ltqrInfo($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        $infos = array();
        foreach ($response->success as $ltqrInfo) {

            $infos[] = new LinkIDLTQRInfo(
                isset($ltqrInfo->ltqrReference) ? $ltqrInfo->ltqrReference : null,
                isset($ltqrInfo->sessionId) ? $ltqrInfo->sessionId : null,
                isset($ltqrInfo->created) ? $ltqrInfo->created : null,
                $this->convertQRCodeInfo($ltqrInfo->qrCodeInfo),
                $this->parseLTQRContent($ltqrInfo->content),
                parseLinkIDLTQRLockType($ltqrInfo->lockType),
                isset($ltqrInfo->locked) ? $ltqrInfo->locked : false,
                isset($ltqrInfo->waitForUnblock) ? $ltqrInfo->waitForUnblock : false,
                isset($ltqrInfo->blocked) ? $ltqrInfo->blocked : false
            );
        }

        return $infos;

    }

    /**
     * @param LinkIDReportDateFilter $dateFilter
     * @param LinkIDReportPageFilter $pageFilter
     * @param array $orderReferences
     * @param array $mandateReferences
     * @return array|null
     * @throws Exception
     */
    public function getPaymentReport($dateFilter = null, $pageFilter = null, $orderReferences = null, $mandateReferences = null)
    {

        $requestParams = new stdClass;

        if (null != $dateFilter) {
            $requestParams->dateFilter = new stdClass();
            $requestParams->dateFilter->startDate = $dateFilter->startDate->format(DateTime::ATOM);
            if (null != $dateFilter->endDate) {
                $requestParams->dateFilter->endDate = $dateFilter->endDate->format(DateTime::ATOM);
            }

        }

        if (null != $pageFilter) {
            $requestParams->pageFilter = new stdClass();
            $requestParams->pageFilter->firstResult = $pageFilter->firstResult;
            $requestParams->pageFilter->maxResults = $pageFilter->maxResults;
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

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

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
     * @param LinkIDReportDateFilter $dateFilter
     * @param LinkIDReportPageFilter $pageFilter
     * @param array $barCodes
     * @param array $ticketNumbers
     * @param array $dtaKeys
     * @param array $parkings
     * @return array|null
     * @throws Exception
     */
    public function getParkingReport($dateFilter = null, $pageFilter = null, $barCodes = null, $ticketNumbers = null, $dtaKeys = null, $parkings = null)
    {

        $requestParams = new stdClass;

        if (null != $dateFilter) {
            $requestParams->dateFilter = new stdClass();
            $requestParams->dateFilter->startDate = $dateFilter->startDate->format(DateTime::ATOM);
            if (null != $dateFilter->endDate) {
                $requestParams->dateFilter->endDate = $dateFilter->endDate->format(DateTime::ATOM);
            }

        }

        if (null != $pageFilter) {
            $requestParams->pageFilter = new stdClass();
            $requestParams->pageFilter->firstResult = $pageFilter->firstResult;
            $requestParams->pageFilter->maxResults = $pageFilter->maxResults;
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

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

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

    /**
     * @param string $language
     * @param string $walletOrganizationId
     * @param LinkIDReportApplicationFilter $applicationFilter
     * @param LinkIDReportWalletFilter $walletFilter
     * @param LinkIDReportDateFilter $dateFilter
     * @param LinkIDReportPageFilter $pageFilter
     * @return array|null
     * @throws Exception
     */
    public function getWalletReport($language = "en", $walletOrganizationId, $applicationFilter = null, $walletFilter = null, $dateFilter = null, $pageFilter = null)
    {

        $requestParams = new stdClass;

        $requestParams->language = $language;
        $requestParams->walletOrganizationId = $walletOrganizationId;

        if (null != $dateFilter) {
            $requestParams->dateFilter = new stdClass();
            $requestParams->dateFilter->startDate = $dateFilter->startDate->format(DateTime::ATOM);
            if (null != $dateFilter->endDate) {
                $requestParams->dateFilter->endDate = $dateFilter->endDate->format(DateTime::ATOM);
            }

        }

        if (null != $pageFilter) {
            $requestParams->pageFilter = new stdClass();
            $requestParams->pageFilter->firstResult = $pageFilter->firstResult;
            $requestParams->pageFilter->maxResults = $pageFilter->maxResults;
        }

        if (null != $applicationFilter) {
            $requestParams->applicationFilter = new stdClass();
            $requestParams->applicationFilter->applicationName = $applicationFilter->applicationName;
        }

        if (null != $walletFilter) {
            $requestParams->walletFilter = new stdClass();
            $requestParams->walletFilter->walletId = $walletFilter->walletId;
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


        return new LinkIDWalletReport($response->total, $transactions);

    }

    /**
     * @param string $language
     * @param array $walletIds
     * @return array|null
     * @throws Exception
     */
    public function getWalletInfoReport($language = "en", $walletIds)
    {

        $requestParams = new stdClass;

        $requestParams->language = $language;

        if (null == $walletIds) {
            throw new Exception('Must specify walletIds');
        }

        $requestParams->walletId = array();
        foreach ($walletIds as $walletId) {
            $requestParams->walletId[] = $walletId;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->walletInfoReport($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        if (!isset($response->walletInfo)) {
            return null;
        }
        $xmlWalletInfos = $response->walletInfo;

        // payment transactions
        $walletInfos = array();
        if (is_array($xmlWalletInfos)) {
            foreach ($xmlWalletInfos as $xmlWalletInfo) {
                $walletInfos[] = parseLinkIDWalletInfoReport($xmlWalletInfo);
            }
        } else {
            $walletInfos[] = parseLinkIDWalletInfoReport($xmlWalletInfos);
        }


        return $walletInfos;

    }

    /**
     * @param $userId string the linkID user ID
     * @param $walletOrganizationId string the linkID wallet organization ID
     * @param $amount double optional start balance
     * @param $currency LinkIDCurrency optional start balance currency
     * @param $walletCoin string optional wallet coin
     * @param $reportInfo LinkIDWalletReportInfo optional wallet report info
     *
     * @throws Exception something went wrong enrolling
     *
     * @return string the ID of the linkID wallet that was created
     */
    public function walletEnroll($userId, $walletOrganizationId, $amount, $currency, $walletCoin, $reportInfo)
    {

        $requestParams = array(
            'userId' => $userId,
            'walletOrganizationId' => $walletOrganizationId,
            'amount' => $amount,
            'currency' => $currency,
            'walletCoin' => $walletCoin
        );

        if (null != $reportInfo) {
            $requestParams->reportInfo = new stdClass();
            if (null != $reportInfo->reference) {
                $requestParams->reportInfo->reference = $reportInfo->reference;
            }
            if (null != $reportInfo->description) {
                $requestParams->reportInfo->description = $reportInfo->description;
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->walletEnroll($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        return $response->success->walletId;
    }

    /**
     * @param string $userId
     * @param string $walletOrganizationId
     * @return LinkIDWalletInfo
     * @throws Exception
     */
    public function walletGetInfo($userId, $walletOrganizationId)
    {

        $requestParams = array(
            'userId' => $userId,
            'walletOrganizationId' => $walletOrganizationId
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->walletGetInfo($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        return new LinkIDWalletInfo($response->success->walletId);

    }

    /**
     * @param string $userId string the linkID user ID
     * @param string $walletId string the linkID wallet ID
     * @param int $amount double amount to add
     * @param LinkIDCurrency $currency currency of amount to add
     * @param string $walletCoin string optional wallet coin
     * @param $reportInfo LinkIDWalletReportInfo optional wallet report info
     *
     * @throws Exception something went wrong
     */
    public function walletAddCredit($userId, $walletId, $amount, $currency, $walletCoin, $reportInfo)
    {

        $requestParams = array(
            'userId' => $userId,
            'walletId' => $walletId,
            'amount' => $amount,
            'currency' => isset($curency) ? linkIDCurrencyToString($currency) : null,
            'walletCoin' => $walletCoin
        );

        if (null != $reportInfo) {
            $requestParams->reportInfo = new stdClass();
            if (null != $reportInfo->reference) {
                $requestParams->reportInfo->reference = $reportInfo->reference;
            }
            if (null != $reportInfo->description) {
                $requestParams->reportInfo->description = $reportInfo->description;
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->walletAddCredit($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }
    }

    /**
     * @param string $userId string the linkID user ID
     * @param string $walletId string the linkID wallet ID
     * @param int $amount double amount to remove
     * @param LinkIDCurrency $currency currency of amount to remove
     * @param string $walletCoin string optional wallet coin
     * @param $reportInfo LinkIDWalletReportInfo optional wallet report info
     *
     * @throws Exception something went wrong
     */
    public function walletRemoveCredit($userId, $walletId, $amount, $currency, $walletCoin, $reportInfo)
    {

        $requestParams = array(
            'userId' => $userId,
            'walletId' => $walletId,
            'amount' => $amount,
            'currency' => isset($curency) ? linkIDCurrencyToString($currency) : null,
            'walletCoin' => $walletCoin
        );

        if (null != $reportInfo) {
            $requestParams->reportInfo = new stdClass();
            if (null != $reportInfo->reference) {
                $requestParams->reportInfo->reference = $reportInfo->reference;
            }
            if (null != $reportInfo->description) {
                $requestParams->reportInfo->description = $reportInfo->description;
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->walletRemoveCredit($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }
    }

    /**
     * @param $userId string the linkID user ID
     * @param $walletId string the linkID wallet ID
     *
     * @throws Exception something went wrong
     */
    public function walletRemove($userId, $walletId)
    {

        $requestParams = array(
            'userId' => $userId,
            'walletId' => $walletId
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->walletRemove($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }
    }

    /**
     * @param $userId string the linkID user ID
     * @param $walletId string the linkID wallet ID
     * @param $walletTransactionId string the linkID wallet transaction to commit
     *
     * @throws Exception something went wrong
     */
    public function walletCommit($userId, $walletId, $walletTransactionId)
    {

        $requestParams = array(
            'userId' => $userId,
            'walletId' => $walletId,
            'walletTransactionId' => $walletTransactionId
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->walletCommit($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }
    }

    /**
     * @param $userId string the linkID user ID
     * @param $walletId string the linkID wallet ID
     * @param $walletTransactionId string the linkID wallet transaction to release
     *
     * @throws Exception something went wrong
     */
    public function walletRelease($userId, $walletId, $walletTransactionId)
    {

        $requestParams = array(
            'userId' => $userId,
            'walletId' => $walletId,
            'walletTransactionId' => $walletTransactionId
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->walletRelease($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }
    }

    // Helper methods

    /**
     * @param LinkIDLTQRContent $content the content
     * @return stdClass
     */
    private function convertLTQRContent($content)
    {

        $requestContent = new stdClass();

        $requestContent->authenticationMessage = $content->authenticationMessage;
        $requestContent->finishedMessage = $content->finishedMessage;

        // payment context
        if (null != $content->paymentContext) {
            $requestContent->paymentContext = $this->convertPaymentContext($content->paymentContext);
        }

        // callback
        if (null != $content->callback) {
            $requestContent->callback = $this->convertCallback($content->callback);
        }

        // identity profile
        $requestContent->identityProfile = $content->identityProfile;

        if ($content->sessionExpiryOverride > 0) {
            $requestContent->sessionExpiryOverride = $content->sessionExpiryOverride;
        }
        $requestContent->theme = $content->theme;
        $requestContent->mobileLandingSuccess = $content->mobileLandingSuccess;
        $requestContent->mobileLandingError = $content->mobileLandingError;
        $requestContent->mobileLandingCancel = $content->mobileLandingCancel;

        // polling configuration
        if (null != $content->pollingConfiguration) {
            $requestContent->pollingConfiguration = $this->convertLTQRPollingConfiguration($content->pollingConfiguration);
        }

        if (null != $content->expiryDate) {
            /** @noinspection PhpUndefinedMethodInspection */
            $requestContent->expiryDate = $content->expiryDate->format(DateTime::ATOM);
        }
        if (null != $content->expiryDuration) {
            $requestContent->expiryDuration = $content->expiryDuration;
        }
        $requestContent->waitForUnblock = $content->waitForUnblock;

        if (null != $content->ltqrStatusLocation) {
            $requestContent->ltqrStatusLocation = $content->ltqrStatusLocation;
        }

        if (null != $content->favoritesConfiguration) {
            $requestContent->favoritesConfiguration = $this->convertFavoritesConfiguration($content->favoritesConfiguration);
        }

        return $requestContent;
    }

    /**
     * @param $content stdClass
     * @return LinkIDLTQRContent
     */
    private function parseLTQRContent($content)
    {
        return new LinkIDLTQRContent(
            isset($content->authenticationMessage) ? $content->authenticationMessage : null,
            isset($content->finishedMessage) ? $content->finishedMessage : null,
            isset($content->paymentContext) ? parseLinkIDPaymentContext($content->paymentContext) : null,
            isset($content->callback) ? parseLinkIDCallback($content->callback) : null,
            isset($content->identityProfile) ? $content->identityProfile : null,
            $content->sessionExpiryOverride,
            isset($content->theme) ? $content->theme : null,
            isset($content->mobileLandingSuccess) ? $content->mobileLandingSuccess : null,
            isset($content->mobileLandingError) ? $content->mobileLandingError : null,
            isset($content->mobileLandingCancel) ? $content->mobileLandingCancel : null,
            isset($content->pollingConfiguration) ? parseLinkIDLTQRPollingConfiguration($content->pollingConfiguration) : null,
            isset($content->ltqrStatusLocation) ? $content->ltqrStatusLocation : null,
            isset($content->expiryDate) ? $content->expiryDate : null,
            isset($content->expiryDuration) ? $content->expiryDuration : null,
            isset($content->waitForUnblock) ? $content->waitForUnblock : false,
            isset($content->favoritesConfiguration) ? parseLinkIDFavoritesConfiguration($content->favoritesConfiguration) : null
        );
    }

    /**
     * @param LinkIDPaymentContext $paymentContext
     * @return stdClass
     */
    private function convertPaymentContext($paymentContext)
    {

        $requestPaymentContext = new stdClass;

        $requestPaymentContext->amount = $paymentContext->amount->amount;
        if (null != $paymentContext->amount && null != $paymentContext->amount->walletCoin) {
            $requestPaymentContext->walletCoin = $paymentContext->amount->walletCoin;
        } else {
            $requestPaymentContext->currency = linkIDCurrencyToString($paymentContext->amount->currency);
        }
        $requestPaymentContext->description = $paymentContext->description;
        $requestPaymentContext->orderReference = $paymentContext->orderReference;
        $requestPaymentContext->paymentProfile = $paymentContext->profile;
        $requestPaymentContext->validationTime = $paymentContext->validationTime;
        $requestPaymentContext->allowPartial = $paymentContext->allowPartial;
        $requestPaymentContext->onlyWallets = $paymentContext->onlyWallets;
        $requestPaymentContext->mandate = null != $paymentContext->mandate;
        if (null != $paymentContext->mandate) {
            $requestPaymentContext->mandateDescription = $paymentContext->mandate->description;
            $requestPaymentContext->mandateReference = $paymentContext->mandate->reference;
        }
        $requestPaymentContext->paymentStatusLocation = $paymentContext->paymentStatusLocation;

        return $requestPaymentContext;
    }

    /**
     * @param LinkIDCallback $callback
     * @return stdClass
     */
    private function convertCallback($callback)
    {

        $requestCallback = new stdClass;

        $requestCallback->location = $callback->location;
        $requestCallback->appSessionId = $callback->appSessionId;
        $requestCallback->inApp = $callback->inApp;

        return $requestCallback;

    }

    /**
     * @param LinkIDLTQRPollingConfiguration $pollingConfiguration
     * @return stdClass
     */
    private function convertLTQRPollingConfiguration($pollingConfiguration)
    {

        $request = new stdClass;

        $request->pollAttempts = $pollingConfiguration->pollAttempts;
        $request->pollInterval = $pollingConfiguration->pollInterval;
        $request->paymentPollAttempts = $pollingConfiguration->paymentPollAttempts;
        $request->paymentPollInterval = $pollingConfiguration->paymentPollInterval;

        return $request;

    }

    /**
     * @param LinkIDFavoritesConfiguration $favoritesConfiguration
     * @return stdClass
     */
    private function convertFavoritesConfiguration($favoritesConfiguration)
    {

        $request = new stdClass;

        $request->info = $favoritesConfiguration->info;
        $request->title = $favoritesConfiguration->title;
        $request->logoUrl = $favoritesConfiguration->logoUrl;
        $request->backgroundColor = $favoritesConfiguration->backgroundColor;
        $request->textColor = $favoritesConfiguration->textColor;

        return $request;

    }

    public function convertLocalizedImages($xmlLocalizedImages)
    {
        if (null == $xmlLocalizedImages) return null;

        $imageMap = array();

        foreach ($xmlLocalizedImages as $image) {
            $imageMap[$image->language] = new LinkIDLocalizedImage($image->url, isset($image->language) ? $image->language : null);
        }

        return new LinkIDLocalizedImages($imageMap);
    }

    /**
     * @param stdClass $responseQRCodeInfo
     * @return LinkIDQRInfo
     */
    private function convertQRCodeInfo($responseQRCodeInfo)
    {

        $qrCodeImage = base64_decode($responseQRCodeInfo->qrEncoded);

        return new LinkIDQRInfo($qrCodeImage, $responseQRCodeInfo->qrEncoded, $responseQRCodeInfo->qrURL,
            $responseQRCodeInfo->qrContent, $responseQRCodeInfo->mobile);

    }

}
