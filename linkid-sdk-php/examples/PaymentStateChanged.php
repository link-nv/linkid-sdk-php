<?php

require_once('ExampleConfig.php');
require_once('../LinkIDPaymentClient.php');

// get order reference
if (!isset($_REQUEST['orderRef'])) {
    return;
}
$orderReference = $_REQUEST['orderRef'];

$client = new LinkIDPaymentClient($linkIDHost);
$paymentState = $client->getStatus($orderReference);

print("<h2>Payment State</h2>");
print_r($paymentState);