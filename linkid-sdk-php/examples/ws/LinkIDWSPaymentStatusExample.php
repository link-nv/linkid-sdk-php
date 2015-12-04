<?php

require_once('../../LinkIDClient.php');
require_once('../ExampleConfig.php');

date_default_timezone_set('UTC'); // needed for parsing dates

$client = new LinkIDClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);

$orderReference = "739e1d70f5cd43a4be8be49c0268728d";

$paymentStatus = $client->getPaymentStatus($orderReference);

print("<h2>Payment status</h2>");
print("<li>OrderReference: " . $orderReference . "</ul>");
print("<pre>");
print_r($paymentStatus);
print("</pre>");

?>
