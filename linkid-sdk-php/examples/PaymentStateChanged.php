<?php

require_once('../LinkIDPaymentClient.php');

$linkIDHost = "192.168.5.14:8443";
// $linkIDHost = "demo.linkid.be";

$client = new LinkIDPaymentClient($linkIDHost);
$paymentState = $client->getStatus("f6fda91a73624045a0d7457aa5ef29d1");

print("<h2>Payment State</h2>");
print($paymentState);

?>