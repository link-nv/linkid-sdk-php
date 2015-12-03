<?php

require_once('LinkIDWSSoapClient.php');
require_once('LinkIDAuthenticationContext.php');
require_once('LinkIDSaml2.php');
require_once('LinkIDAuthnSession.php');
require_once('LinkIDAuthPollResponse.php');
require_once('LinkIDThemes.php');
require_once('LinkIDLocalization.php');

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

        $wsdlLocation = "http://" . $linkIDHost . "/linkid-ws-username/linkid30?wsdl";

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

    // TODO: impl me
    public function ltqrPush($content, $userAgent, $lockType)
    {

    }


    // Helper methods

    public function convertLocalizedImages($xmlLocalizedImages)
    {
        if (null == $xmlLocalizedImages) return null;

        $imageMap = array();

        foreach ($xmlLocalizedImages as $image) {
            $imageMap[$image->language] = new LinkIDLocalizedImage($image->url, isset($image->language) ? $image->language : null);
        }

        return new LinkIDLocalizedImages($imageMap);
    }

    public function convertQRCodeInfo($xmlQrCodeInfo)
    {
        return new LinkIDQRInfo(base64_decode($xmlQrCodeInfo->qrEncoded), $xmlQrCodeInfo->qrEncoded, $xmlQrCodeInfo->qrURL, $xmlQrCodeInfo->qrContent, $xmlQrCodeInfo->mobile, $xmlQrCodeInfo->targetBlank);
    }
}
