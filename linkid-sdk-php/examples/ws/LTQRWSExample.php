<?php

require_once('../../LinkIDLTQRClient.php');
require_once('../ExampleConfig.php');

date_default_timezone_set('UTC'); // needed for parsing dates

/**
 * Push LTQR session
 */


$ltqrReference = "261d60e6-9a02-4736-924b-1d1631d5bc99";

$paymentContext = new LinkIDPaymentContext(500, "LTQR Test");

$client = new LinkIDLTQRClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);
//$client->push($paymentContext, $true, new DateTime(), 500);
$authenticationMessage = "PHP LTQR";
$finishedMessage = "PHP LTQR Finished";
$callback = new LinkIDCallback("https://www.linkid.be");
$ltqrSession = $client->push($authenticationMessage, $finishedMessage, $paymentContext, false, null, null, $callback, null);

print("<h2>LTQR Session</h2>");
print("<ul><li>URL : " . $ltqrSession->qrCodeURL);
print("<li>LTQR reference: " . $ltqrSession->ltqrReference . "</ul>");

$imgData = base64_encode($ltqrSession->qrCodeImage);
print("<img src='data:image/png;base64, $imgData' />");


/**
 * Fetch client sessions
 */


$ltqrReferences = array();
$ltqrReferences[] = $ltqrReference;
print("<h2>Fetch client sessions for " . $ltqrReferences[0] . "</h2>");

$clientSessions = $client->pull($ltqrReferences);

print("<h2>LTQR Client Sessions</h2>");

print("<pre>");
print_r($clientSessions);
print("</pre>");

/**
 * Change sessions
 */
$paymentContext = new LinkIDPaymentContext(9900, "Changed LTQR Test");
$client->change($ltqrReference, null, null, $paymentContext);


/**
 * Remove client sessions
 */

$ltqrReferences = array();
$ltqrReferences[] = $ltqrReference;

//$client->remove($ltqrReferences);

?>
