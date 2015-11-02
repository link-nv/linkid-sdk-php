<?php

require_once('LinkIDWSSoapClient.php');
require_once('LinkIDAuthenticationContext.php');
require_once('LinkIDSaml2.php');
require_once('LinkIDAuthnSession.php');
require_once('LinkIDAuthPollResponse.php');

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

    // Helper methods

    public function convertQRCodeInfo($xmlQrCodeInfo)
    {
        return new LinkIDQRInfo(base64_decode($xmlQrCodeInfo->qrEncoded), $xmlQrCodeInfo->qrEncoded, $xmlQrCodeInfo->qrURL, $xmlQrCodeInfo->qrContent, $xmlQrCodeInfo->mobile, $xmlQrCodeInfo->targetBlank);
    }
}
