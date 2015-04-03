<?php

require_once('../../LinkIDReportingClient.php');
require_once('../ExampleConfig.php');

date_default_timezone_set('UTC'); // needed for parsing dates

$client = new LinkIDReportingClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);

// Orders lookup on date

$orders = $client->getPaymentReport(new DateTime('2015-04-01'));

print("<h2>Payment orders since 2015-04-01</h2>");
print("<pre>");
print_r($orders);
print("</pre>");

// Orders lookup on orderReferences

$orderReferences = array();
$orderReferences[] = "QR-SHOP-0a0337b4-1fa2-4a89-a93c-e59e94bd41f5";
$orderReferences[] = "QR-SHOP-0f3ce411-548a-4785-a7a5-feb07687f91a";
$orders = $client->getPaymentReport(null, null, $orderReferences, null);

print("<h2>Payment orders filtered with order references list</h2>");
print("<pre>");
print_r($orders);
print("</pre>");

// Orders lookup on mandateReferences

$mandateReferences = array();
$mandateReferences[] = "c4d8edae-22e4-4c55-8486-5ba3bec3d0ad";
$mandateReferences[] = "9ae4d903-b3b5-4a03-9e60-92ac7bdafcd6";
$orders = $client->getPaymentReport(null, null, null, $mandateReferences);

print("<h2>Payment orders filtered with mandate references list</h2>");
print("<pre>");
print_r($orders);
print("</pre>");

// Parking session lookup on date

$sessions = $client->getParkingReport(new DateTime('2014-01-01'));

print("<h2>Parking sessions since 2014-01-01</h2>");
print("<pre>");
print_r($sessions);
print("</pre>");

?>
