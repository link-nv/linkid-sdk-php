<?php

require_once('ExampleConfig.php');
require_once('../LinkIDClient.php');
require_once('../LinkIDSaml2.php');
require_once('../LinkIDLoginConfig.php');

date_default_timezone_set('UTC'); // needed for DateTime
if (!isset($_SESSION)) {
    session_start();
}

$client = new LinkIDClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);

if (!isset($_SESSION["linkIDSession"])) {

    $paymentContext = new LinkIDPaymentContext(new LinkIDPaymentAmount(100, LinkIDCurrency::EUR, null), "PHP Payment description");
    $paymentContext->paymentStatusLocation = "https://www.linkid.be";

    $authenticationContext = new LinkIDAuthenticationContext("example-mobile", null, "en",
        "PHP Authn Message", "PHP Finished Message",
        $paymentContext, new LinkIDCallback("https://www.google.be"),
        "linkid_basic", 60, "ugent", null, null, null, null);

    $linkIDAuthnSession = $client->authStart($authenticationContext, null);

    // store on session
    $_SESSION["linkIDSession"] = $linkIDAuthnSession;

    // show QR code
    $qrCodeImage = imagecreatefromstring($linkIDAuthnSession->qrCodeInfo->qrImage);
    if ($qrCodeImage != false) {
        header('Content-Type: image/png');
        imagepng($qrCodeImage);
        imagedestroy($qrCodeImage);
    } else {
        echo "Not a valid QR code";
    }

} else {

    // poll existing linkID authentication session
    $linkIDAuthnSession = $_SESSION["linkIDSession"];

    $linkIDPollResponse = $client->authPoll($linkIDAuthnSession->sessionId, "en");

    // output linkID poll response
    if (null != $linkIDPollResponse->authenticationContext) {

        print("<h2>User: " . $linkIDPollResponse->authenticationContext->userId . "</h2>");

        print ("<a href=\"wslogout.php\">Restart</a>");

        print("<h3>Attributes</h3>");
        print("<pre>");
        print_r($linkIDPollResponse->authenticationContext->attributes);
        print("</pre>");


        print("<h3>Payment response</h3>");
        print("<pre>");
        print_r($linkIDPollResponse->authenticationContext->paymentResponse);
        print("</pre>");

    } else {

        print ("<a href=\"wslogout.php\">Restart</a>");
        print("<pre>");
        print("AuthenticationState: " . $linkIDPollResponse->authenticationState . "\n");
        print("PaymentState: " . $linkIDPollResponse->paymentState . "\n");
        print("PaymentMenuURL: " . $linkIDPollResponse->paymentMenuURL . "\n");
        print("</pre>");

    }

}

?>