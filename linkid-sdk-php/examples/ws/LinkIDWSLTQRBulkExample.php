<?php

require_once('../../LinkIDClient.php');
require_once('../ExampleConfig.php');

date_default_timezone_set('UTC'); // needed for parsing dates

$client = new LinkIDClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);


$paymentContext = new LinkIDPaymentContext(new LinkIDPaymentAmount(500, LinkIDCurrency::EUR, null), "LTQR Test");
$authenticationMessage = "PHP LTQR";
$finishedMessage = "PHP LTQR Finished";
$callback = new LinkIDCallback("https://www.linkid.be");
$sessionExpiryOverride = 10;
$theme = "ugent";
$ltqrContent = new LinkIDLTQRContent($authenticationMessage, $finishedMessage, $paymentContext, $callback, null, $sessionExpiryOverride, $theme, null, null, null, null, null, null, null, false, null);

$contents = array();
for ($i = 0; $i < 2; $i++) {
    $contents[] = new LinkIDLTQRPushContent($ltqrContent, null, LinkIDLTQRLockType::NEVER);
}

$results = $client->ltqrBulkPush($contents);

print("<h2>LTQR Bulk results</h2>");

print("<pre>");
print_r($results);
print("</pre>");


?>
