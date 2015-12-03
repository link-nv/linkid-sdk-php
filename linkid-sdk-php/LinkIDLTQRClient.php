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