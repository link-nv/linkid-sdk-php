<?php

require_once('LinkIDHawsClient.php');
require_once('LinkIDSaml2.php');

/*
 * LinkID Login Configuration Utility class
 *
 * @author Wim Vandenhaute
 */

class LinkIDLoginConfig {

    public $forceRegistration;
    public $targetURI;
    public $linkIDLandingPage;

    /**
     * Constructor
     */
    public function __construct($linkIDHost) {

        $this->forceRegistration = null != $_REQUEST["mobileForceReg"];
        $this->targetURI = $_REQUEST["return_uri"];

        if ($this->forceRegistration) {
            $this->linkIDLandingPage = "https://" . $linkIDHost . "/linkid-mobile/reg-min";
        } else {
            $this->linkIDLandingPage = "https://" . $linkIDHost . "/linkid-mobile/auth-min";
        }
    }

    public function generateRedirectURL($sessionId) {
        return $this->linkIDLandingPage . "?hawsId=" . $sessionId;
    }

}

/**
 * Handles sending a linkID authentication request and validation/parsing a linkID authentication response
 */
function handleLinkID($authnContextParam, $linkIDHost, $linkIDAppName, $linkIDLanguage, $loginPage, $linkIDWSUsername, $linkIDWSPassword) {

    date_default_timezone_set('UTC'); // needed for parsing dates
    if (!isset($_SESSION)) {
        session_start();
    }

    $sessionId = urldecode($_REQUEST["hawsId"]);

    /*
     * Check if "force" query param is present.
     * If set, an authentication will be started, regardless if the user was already logged in.
     * For e.g. linkID payments...
     */
    $forceAuthentication = null != $_REQUEST["force"];

    /*
     * If a SAML2 response was found but no authentication context was on the session we received a
     * SAML2 authentication response.
     */
    if (null != $sessionId && null == $_SESSION[$authnContextParam]) {

        $saml2Util = $_SESSION["linkID.saml2Util"];
        $loginConfig = $_SESSION["linkID.loginConfig"];

        // fetch authentication response from linkID
        $hawsClient = new LinkIDHawsClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);
        $authnResponse = $hawsClient->pull($sessionId);

        // validate/parse
        $authnContext = $saml2Util->parseAuthnResponse($authnResponse);
        $_SESSION[$authnContextParam] = $authnContext;

        // finalize
        finalize($loginConfig);

        return;
    }

    /*
     * No authentication context found so not yet logged in.
     *
     * Generate a SAML2 authentication request and store in the hiddenfield.
     * Put the used authentication utility class on the session.
     */
    if (null == $_SESSION[$authnContextParam] || $forceAuthentication) {

        if (forceAuthentication) {
            unset($_SESSION[$authnContextParam]);
        }

        $loginConfig = new LinkIDLoginConfig($linkIDHost);
        $_SESSION["linkID.loginConfig"] = $loginConfig;

        // print("LoginConfig: " . $loginConfig->forceRegistration . "," . $loginConfig->targetURI . "," . $loginConfig->linkIDLandingPage);

        // construct the authentication request
        $saml2Util = new LinkIDSaml2();
        $_SESSION["linkID.saml2Util"] = $saml2Util;

        // device context
        $deviceContext = getLinkIDContext();

        // TODO: attribute suggestions
        $attributeSuggestions = getLinkIDAttributeSuggestions();

        // TODO: payment context

        // generate authn request
        $authnRequest = $saml2Util->generateAuthnRequest($linkIDAppName, $loginConfig, $loginPage, $deviceContext, $attributeSuggestions);

        // push authn request to linkID
        $hawsClient = new LinkIDHawsClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);
        $sessionId = $hawsClient->push($authnRequest, $linkIDLanguage);

        // redirect
        header("Location: " . $loginConfig->generateRedirectURL($sessionId));
    }
}

function finalize($loginConfig) {

    print("<html>");
    print("<head>");
    print("<script type=\"text/javascript\">");
    print("window.top.location.replace(\"" . $loginConfig->targetURI . "\");");
    print("</script>");
    print("</head>");
    print("<body>");
    print("<noscript><p>You are successfully logged in. Since your browser does not support JavaScript, you must close this popup window and refresh the original window manually.</p></noscript>");
    print("</body>");
    print("</html>");
    session_write_close();
    exit();
}

/**
 * Specify a custom context to be shown on the linkID mobile app
 */
function setLinkIDContext($context) {

    if (!isset($_SESSION)) {
        session_start();
    }

    $_SESSION['linkID.deviceContext'] = $context;

}

function getLinkIDContext() {

    if (!isset($_SESSION)) {
        session_start();
    }

    return $_SESSION['linkID.deviceContext'];

}

/**
 * Specify an array of attribute suggestions to be used in the identity part of the linkID login process.
 */
function setLinkIDAttributeSuggestions($attributeSuggestions) {

    if (!isset($_SESSION)) {
        session_start();
    }

    $_SESSION['linkID.attributeSuggestions'] = $attributeSuggestions;

}

function getLinkIDAttributeSuggestions() {

    if (!isset($_SESSION)) {
        session_start();
    }

    return $_SESSION['linkID.attributeSuggestions'];

}

?>