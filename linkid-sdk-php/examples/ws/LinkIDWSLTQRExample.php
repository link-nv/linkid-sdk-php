<?php

require_once('../../LinkIDClient.php');
require_once('../ExampleConfig.php');

date_default_timezone_set('UTC'); // needed for parsing dates

$client = new LinkIDClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);


/**
 * Push LTQR session
 */

$paymentContext = new LinkIDPaymentContext(new LinkIDPaymentAmount(500, LinkIDCurrency::EUR, null), "LTQR Test");
$authenticationMessage = "PHP LTQR";
$finishedMessage = "PHP LTQR Finished";
$callback = new LinkIDCallback("https://www.linkid.be");
$sessionExpiryOverride = 10;
$theme = "ugent";
$favoritesConfiguration = new LinkIDFavoritesConfiguration("PHP Title", "PHP info", null, null, null);

$ltqrContent = new LinkIDLTQRContent($authenticationMessage, $finishedMessage, $paymentContext, $callback, null, $sessionExpiryOverride, $theme, null, null, null, null, null, null, null, false, $favoritesConfiguration);


$ltqrSession = $client->ltqrPush($ltqrContent, null, LinkIDLTQRLockType::NEVER);

print("<h2>LTQR Session</h2>");
print("<ul><li>URL : " . $ltqrSession->qrCodeInfo->qrCodeURL);
print("<li>LTQR reference: " . $ltqrSession->ltqrReference . "</ul>");

$imgData = base64_encode($ltqrSession->qrCodeInfo->qrImage);
print("<img src='data:image/png;base64, $imgData' />");


/**
 * Change LTQR session
 */

$ltqrContent->authenticationMessage = "Change PHP LTQR";
$ltqrContent->paymentContext = null;
$ltqrSession = $client->ltqrChange($ltqrSession->ltqrReference, $ltqrContent, null, true, true);

print("<h2>LTQR Session</h2>");
print("<ul><li>URL : " . $ltqrSession->qrCodeInfo->qrCodeURL);
print("<li>LTQR reference: " . $ltqrSession->ltqrReference . "</ul>");

$imgData = base64_encode($ltqrSession->qrCodeInfo->qrImage);
print("<img src='data:image/png;base64, $imgData' />");


/**
 * Fetch client sessions
 */

$ltqrReferences = array();
$ltqrReferences[] = "bd3d9618-97de-420f-bfc1-ec1f9286ec62";

$clientSessions = $client->ltqrPull($ltqrReferences);

print("<h2>LTQR Client Sessions</h2>");

print("<pre>");
print_r($clientSessions);
print("</pre>");

/**
 * Fetch session info
 */

$ltqrReferences = array();
$ltqrReferences[] = "bd3d9618-97de-420f-bfc1-ec1f9286ec62";

$sessionInfo = $client->ltqrInfo($ltqrReferences, null);

print("<h2>LTQR Session info</h2>");

print("<pre>");
print_r($sessionInfo);
print("</pre>");

?>
