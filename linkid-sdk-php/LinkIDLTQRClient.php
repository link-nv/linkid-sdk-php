<?php

require_once('LinkIDWSSoapClient.php');
require_once('LinkIDPaymentContext.php');
require_once('LinkIDCallback.php');
require_once('LinkIDLTQRSession.php');
require_once('LinkIDLTQRClientSession.php');
require_once('LinkIDLTQRInfo.php');
require_once('LinkIDLTQRPollingConfiguration.php');

/*
 * linkID LTQR WS client
 *
 * @author Wim Vandenhaute
 */

class LinkIDLTQRClient
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

        $wsdlLocation = "https://" . $linkIDHost . "/linkid-ws-username/ltqr50?wsdl";

        $this->client = new LinkIDWSSoapClient($wsdlLocation);
        $this->client->__setUsernameToken($username, $password, 'PasswordDigest');

    }

    /**
     * Fetch a set of client sessions.
     *
     * @param null $ltqrReferences Optional list of ltqrReferences to fetch. If none are specified, all LTQR sessions and client session are returned.
     * @param null $paymentOrderReferences Optional list of Payment order References to fetch. If none are specified, all are fetched for the LTQR References
     *                               specified above.
     * @param null $clientSessionIds optional list of client session IDs
     *
     * returns list of client sessions
     * @return array
     * @throws Exception
     */
    public function pull($ltqrReferences = null, $paymentOrderReferences = null, $clientSessionIds = null)
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
        $response = $this->client->pull($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        $clientSessions = array();
        foreach ($response->success as $session) {

            $qrCodeImage = base64_decode($session->encodedQR);

            $clientSessions[] = new LinkIDLTQRClientSession($qrCodeImage, $session->qrContent, $session->ltqrReference,
                $session->clientSessionId, $session->userId, $session->created,
                $session->paymentOrderReference, $session->paymentStatus);
        }

        return $clientSessions;
    }

    /**
     * Remove a set of client sessions.
     *
     * @param null $ltqrReferences Optional list of LTQR References to remove. If none are specified, all LTQR sessions and client session are removed.
     * @param null $paymentOrderReferences Optional list of Payment order References to remove. If none are specified, all are removed for the LTQR References specified above.
     * @param null $clientSessionIds optional list of client session IDs to remove
     * @throws Exception
     */
    public function remove($ltqrReferences = null, $paymentOrderReferences = null, $clientSessionIds = null)
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
        $response = $this->client->remove($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        // all good, return
        return;
    }

    /**
     * @param null $ltqrReferences
     * @return array
     * @throws Exception
     */
    public function info($ltqrReferences = null)
    {
        $requestParams = new stdClass;

        if (null != $ltqrReferences) {
            $requestParams->ltqrReferences = array();
            foreach ($ltqrReferences as $ltqrReference) {
                $requestParams->ltqrReferences[] = $ltqrReference;
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->info($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        $infos = array();
        foreach ($response->success as $ltqrInfo) {

            $qrCodeImage = isset($ltqrInfo->encodedQR) ? base64_decode($ltqrInfo->encodedQR) : null;

            // payment context
            $paymentContext = null;
            if (isset($ltqrInfo->paymentContext)) {
                $paymentContext = parseLinkIDPaymentContext($ltqrInfo->paymentContext);
            }

            // callback
            $callback = null;
            if (isset($ltqrInfo->callback)) {
                $callback = parseLinkIDCallback($ltqrInfo->callback);
            }

            // polling configuration
            $pollingConfiguration = null;
            if (isset($ltqrInfo->pollingConfiguration)) {
                $pollingConfiguration = parseLinkIDLTQRPollingConfiguration($ltqrInfo->pollingConfiguration);
            }

            $infos[] = new LinkIDLTQRInfo(
                isset($ltqrInfo->ltqrReference) ? $ltqrInfo->ltqrReference : null,
                isset($ltqrInfo->sessionId) ? $ltqrInfo->sessionId : null,
                isset($ltqrInfo->created) ? $ltqrInfo->created : null,
                $qrCodeImage,
                isset($ltqrInfo->qrContent) ? $ltqrInfo->qrContent : null,
                isset($ltqrInfo->authenticationMessage) ? $ltqrInfo->authenticationMessage : null,
                isset($ltqrInfo->finishedMessage) ? $ltqrInfo->finishedMessage : null,
                isset($ltqrInfo->oneTimeUse) ? $ltqrInfo->oneTimeUse : false,
                isset($ltqrInfo->expiryDate) ? $ltqrInfo->expiryDate : null,
                isset($ltqrInfo->expiryDuration) ? $ltqrInfo->expiryDuration : null,
                $paymentContext,
                $callback,
                isset($ltqrInfo->identityProfiles) ? $ltqrInfo->identityProfiles : null,
                isset($ltqrInfo->sessionExpiryOverride) ? $ltqrInfo->sessionExpiryOverride : null,
                isset($ltqrInfo->theme) ? $ltqrInfo->theme : null,
                isset($ltqrInfo->mobileLandingSuccess) ? $ltqrInfo->mobileLandingSuccess : null,
                isset($ltqrInfo->mobileLandingError) ? $ltqrInfo->mobileLandingError : null,
                isset($ltqrInfo->mobileLandingCancel) ? $ltqrInfo->mobileLandingCancel : null,
                $pollingConfiguration,
                isset($ltqrInfo->waitForUnlock) ? $ltqrInfo->waitForUnlock : false,
                isset($ltqrInfo->locked) ? $ltqrInfo->locked : false,
                isset($ltqrInfo->ltqrStatusLocation) ? $ltqrInfo->ltqrStatusLocation : null
            );
        }

        return $infos;
    }
}