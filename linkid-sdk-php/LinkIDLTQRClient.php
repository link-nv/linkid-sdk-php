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
}