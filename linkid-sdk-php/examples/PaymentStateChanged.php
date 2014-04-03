<?php

require_once('../LinkIDPaymentClient.php');

$linkIDHost = "192.168.5.14:8443";
// $linkIDHost = "demo.linkid.be";

// get order reference
if (!isset($_REQUEST['orderRef'])) {
    return;
}
$orderReference = $_REQUEST['orderRef'];

$client = new LinkIDPaymentClient($linkIDHost);
$paymentState = $client->getStatus($orderReference);

print("<h2>Payment State</h2>");
print($paymentState);

?>