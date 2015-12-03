<?php

require_once('LinkIDWSSoapClient.php');
require_once('LinkIDAuthenticationContext.php');
require_once('LinkIDSaml2.php');
require_once('LinkIDAuthnSession.php');
require_once('LinkIDAuthPollResponse.php');
require_once('LinkIDThemes.php');
require_once('LinkIDLocalization.php');
require_once('LinkIDLTQRContent.php');
require_once('LinkIDLTQRLockType.php');
require_once('LinkIDLTQRSession.php');
require_once('LinkIDLTQRClientSession.php');

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

        $wsdlLocation = "http://" . $linkIDHost . "/linkid-ws-username/linkid31?wsdl";

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
        return $saml2->parseAuthnResponse($response->success->any);
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
        $request->logoEncoded = $favoritesConfiguration->logoEncoded;
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
