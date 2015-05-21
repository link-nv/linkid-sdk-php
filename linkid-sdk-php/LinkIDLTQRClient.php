<?php

require_once('LinkIDWSSoapClient.php');
require_once('LinkIDPaymentContext.php');
require_once('LinkIDCallback.php');
require_once('LinkIDLTQRSession.php');
require_once('LinkIDLTQRClientSession.php');

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
     */
    public function __construct($linkIDHost, $username, $password)
    {

        $wsdlLocation = "https://" . $linkIDHost . "/linkid-ws-username/ltqr20?wsdl";

        $this->client = new LinkIDWSSoapClient($wsdlLocation);
        $this->client->__setUsernameToken($username, $password, 'PasswordDigest');

    }

    /**
     * Push a long term QR session to linkID.
     *
     * authenticationMessage Optional authentication message to be shown in the pin view in the mobile app. If there is a payment, this will be ignored.
     * finishedMessage Optional finished message on the final view in the mobile app.
     * paymentContext Optional payment context
     * oneTimeUse     Long term QR session can only be used once
     * expiryDate     Optional expiry date of the long term session.
     * expiryDuration Optional expiry duration of the long term session. Expressed in number of seconds starting from the creation.
     *                       Do not mix this attribute with expiryDate. If so, expiryDate will be preferred.
     * callback Optional callback config
     * identityProfiles Optional identity profiles
     * sessionExpiryOverride optional session expiry (seconds)
     * theme optional theme, if not specified default application theme will be chosen
     *
     * Success object containing the QR in PNG format, the content of the QR code and a type 4 UUID session ID of the created long term session. This
     * session ID will be used in the notifications to the Service Provider.
     */
    public function push($authenticationMessage, $finishedMessage, $paymentContext = null,
                         $oneTimeUse = false, $expiryDate = null, $expiryDuration = null,
                         $callback = null, $identityProfiles = null, $sessionExpiryOverride = null, $theme = null,
                         $mobileLandingSuccess = null, $mobileLandingError = null, $mobileLandingCancel = null)
    {

        $requestParams = new stdClass;

        $requestParams->authenticationMessage = $authenticationMessage;
        $requestParams->finishedMessage = $finishedMessage;

        if (null != $paymentContext) {
            $requestParams->paymentContext = new stdClass;
            $requestParams->paymentContext->amount = $paymentContext->amount;
            $requestParams->paymentContext->currency = linkIDCurrencyToString($paymentContext->currency);
            $requestParams->paymentContext->description = $paymentContext->description;
            $requestParams->paymentContext->orderReference = $paymentContext->orderReference;
            $requestParams->paymentContext->paymentProfile = $paymentContext->profile;
            $requestParams->paymentContext->validationTime = $paymentContext->validationTime;
            $requestParams->paymentContext->allowDeferredPay = $paymentContext->allowDeferredPay;
            $requestParams->paymentContext->allowPartial = $paymentContext->allowPartial;
            $requestParams->paymentContext->onlyWallets = $paymentContext->onlyWallets;
            $requestParams->paymentContext->mandate = $paymentContext->mandate;
            $requestParams->paymentContext->mandateDescription = $paymentContext->mandateDescription;
            $requestParams->paymentContext->mandateReference = $paymentContext->mandateReference;
        }

        if (null != $callback) {
            $requestParams->callback = new stdClass;
            $requestParams->callback->location = $callback->location;
            $requestParams->callback->appSessionId = $callback->appSessionId;
            $requestParams->callback->inApp = $callback->inApp;
        }

        if (null != $identityProfiles) {
            $requestParams->identityProfiles = array();
            foreach ($identityProfiles as $identityProfile) {
                $requestParams->identityProfiles[] = $identityProfile;
            }
        }

        $requestParams->oneTimeUse = $oneTimeUse;
        if (null != $expiryDate) {
            /** @noinspection PhpUndefinedMethodInspection */
            $requestParams->expiryDate = $expiryDate->format(DateTime::ATOM);
        }
        if (null != $expiryDuration) {
            $requestParams->expiryDuration = $expiryDuration;
        }

        if (null != $sessionExpiryOverride) {
            $requestParams->sessionExpiryOverride = $sessionExpiryOverride;
        }
        if (null != $theme) {
            $requestParams->theme = $theme;
        }

        if (null != $mobileLandingSuccess) {
            $requestParams->mobileLandingSuccess = $mobileLandingSuccess;
        }
        if (null != $mobileLandingError) {
            $requestParams->mobileLandingError = $mobileLandingError;
        }
        if (null != $mobileLandingCancel) {
            $requestParams->mobileLandingCancel = $mobileLandingCancel;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->push($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        $qrCodeImage = base64_decode($response->success->encodedQR);

        return new LinkIDLTQRSession($qrCodeImage, $response->success->qrContent, $response->success->ltqrReference,
            isset($response->success->paymentOrderReference) ? $response->success->paymentOrderReference : null);
    }

    /**
     * @param $ltqrReference String LTQR reference, mandatory
     * @param $authenticationMessage String authentication message to be shown in the pin view in the mobile app. If there is a payment, this will be ignored.
     * @param $finishedMessage String finished message on the final view in the mobile app.
     * @param LinkIDPaymentContext $paymentContext Optional payment context
     * @param null $expiryDate Optional expiry date of the long term session.
     * @param null $expiryDuration Optional expiry duration of the long term session. Expressed in number of seconds starting from the creation.
     *                              Do not mix this attribute with expiryDate. If so, expiryDate will be preferred.
     * @param LinkIDCallback $callback Optional callback config
     * @param $identityProfiles String[] identity profiles
     * @param $sessionExpiryOverride int session expiry (seconds)
     * @param $theme string theme, if not specified default application theme will be chosen
     * @return \LinkIDLTQRSession
     * @throws Exception
     */
    public function change($ltqrReference, $authenticationMessage, $finishedMessage, $paymentContext = null,
                           $expiryDate = null, $expiryDuration = null, $callback = null, $identityProfiles = null,
                           $sessionExpiryOverride = null, $theme = null)
    {
        $requestParams = new stdClass;
        $requestParams->ltqrReference = $ltqrReference;

        $requestParams->authenticationMessage = $authenticationMessage;
        $requestParams->finishedMessage = $finishedMessage;

        if (null != $paymentContext) {
            $requestParams->paymentContext = new stdClass;
            $requestParams->paymentContext->amount = $paymentContext->amount;
            $requestParams->paymentContext->currency = linkIDCurrencyToString($paymentContext->currency);
            $requestParams->paymentContext->description = $paymentContext->description;
            $requestParams->paymentContext->orderReference = $paymentContext->orderReference;
            $requestParams->paymentContext->paymentProfile = $paymentContext->profile;
            $requestParams->paymentContext->validationTime = $paymentContext->validationTime;
            $requestParams->paymentContext->allowDeferredPay = $paymentContext->allowDeferredPay;
            $requestParams->paymentContext->allowPartial = $paymentContext->allowPartial;
            $requestParams->paymentContext->onlyWallets = $paymentContext->onlyWallets;
            $requestParams->paymentContext->mandate = $paymentContext->mandate;
            $requestParams->paymentContext->mandateDescription = $paymentContext->mandateDescription;
            $requestParams->paymentContext->mandateReference = $paymentContext->mandateReference;
        }

        if (null != $callback) {
            $requestParams->callback = new stdClass;
            $requestParams->callback->location = $callback->location;
            $requestParams->callback->appSessionId = $callback->appSessionId;
            $requestParams->callback->inApp = $callback->inApp;
        }

        if (null != $identityProfiles) {
            $requestParams->identityProfiles = array();
            foreach ($identityProfiles as $identityProfile) {
                $requestParams->identityProfiles[] = $identityProfile;
            }
        }

        if (null != $expiryDate) {
            /** @noinspection PhpUndefinedMethodInspection */
            $requestParams->expiryDate = $expiryDate->format(DateTime::ATOM);
        }
        if (null != $expiryDuration) {
            $requestParams->expiryDuration = $expiryDuration;
        }

        if (null != $sessionExpiryOverride) {
            $requestParams->sessionExpiryOverride = $sessionExpiryOverride;
        }
        if (null != $theme) {
            $requestParams->theme = $theme;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->change($requestParams);

        if (isset($response->error) && null != $response->error) {
            throw new Exception('Error: ' . $response->error->errorCode);
        }

        $qrCodeImage = base64_decode($response->success->encodedQR);

        return new LinkIDLTQRSession($qrCodeImage, $response->success->qrContent, $response->success->ltqrReference,
            isset($response->success->paymentOrderReference) ? $response->success->paymentOrderReference : null);

    }

    /**
     * Fetch a set of client sessions.
     *
     * ltqrReferences  Optional list of ltqrReferences to fetch. If none are specified, all LTQR sessions and client session are returned.
     * paymentOrderReferences Optional list of Payment order References to fetch. If none are specified, all are fetched for the LTQR References
     *                               specified above.
     * clientSessionIds optional list of client session IDs
     *
     * returns list of client sessions
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
     * ltqrReferences         Optional list of LTQR References to remove. If none are specified, all LTQR sessions and client session are removed.
     * paymentOrderReferences Optional list of Payment order References to remove. If none are specified, all are removed for the LTQR References specified above.
     * clientSessionIds optional list of client session IDs to remove
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
}