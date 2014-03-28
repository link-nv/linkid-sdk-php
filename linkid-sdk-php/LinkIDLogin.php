<?php

require_once('LinkIDHawsClient.php');
require_once('LinkIDLoginConfig.php');
require_once('LinkIDSaml2.php');

/*
 * linkID Configuration
 */
$linkIDHost = "192.168.5.14:8080";
// $linkIDHost = "demo.linkid.be";

$linkIDAppName = "demo-test";

// language to be used in the iframe
$linkIDLanguage = "en";

// username/password
$linkIDWSUsername = "demo-test";
$linkIDWSPassword = "08427E9F-6355-4DE4-B992-B1AB93CEE9D4";

// location of this page, linkID will post its authentication response back to this location.
$loginPage = "http://localhost/~wvdhaute/linkid-sdk-php/LinkIDLogin.php";

/*
 * linkID authentication context session attribute
 *
 * After a successful authentication with linkID this will hold the returned
 * AuthenticationProtocolContext object which contains the linkID user ID,
 * used authentication device(s) and optionally the returned linkID attributes
 * for the application.
 */
$authnContext = "linkID.authnContext";

handleLinkID($authnContext, $linkIDHost, $linkIDAppName, $linkIDLanguage, $loginPage, $linkIDWSUsername, $linkIDWSPassword);




/**
 * Handles sending a linkID authentication request and validation/parsing a linkID authentication response
 */
function handleLinkID($authnContext, $linkIDHost, $linkIDAppName, $linkIDLanguage, $loginPage, $linkIDWSUsername, $linkIDWSPassword) {

    $sessionId = urldecode($_GET["hawsId"]);

    /*
     * Check if "force" query param is present.
     * If set, an authentication will be started, regardless if the user was already logged in.
     * For e.g. linkID payments...
     */
    $forceAuthentication = null != $_GET["force"];

    /*
     * If a SAML2 response was found but no authentication context was on the session we received a
     * SAML2 authentication response.
     */
    if (null != $sessionId && null == $_SESSION[$authnContext]) {
        // TODO...
    }

    /*
     * No authentication context found so not yet logged in.
     *
     * Generate a SAML2 authentication request and store in the hiddenfield.
     * Put the used authentication utility class on the session.
     */
    if (null == $_SESSION[$authnContext] || $forceAuthentication) {

        if (forceAuthentication) {
            unset($_SESSION[$authnContext]);
        }

        $loginConfig = new LinkIDLoginConfig($linkIDHost);

        // print("LoginConfig: " . $loginConfig->forceRegistration . "," . $loginConfig->targetURI . "," . $loginConfig->linkIDLandingPage);

        // construct the authentication request
        $saml2Util = new LinkIDSaml2();
        $_SESSION["linkID.saml2Util"] = $saml2Util;

        // TODO: device context

        // TODO: attribute suggestions

        // TODO: payment context

        // generate authn request
        $authnRequest = $saml2Util->generateAuthnRequest($linkIDAppName, $loginConfig, $loginPage);

        // push authn request to linkID
        $hawsClient = new LinkIDHawsClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);
        $sessionId = $hawsClient->push($authnRequest, 'en');

        // redirect
        header("Location: " . $loginConfig->generateRedirectURL($sessionId));
    }
}

?>

