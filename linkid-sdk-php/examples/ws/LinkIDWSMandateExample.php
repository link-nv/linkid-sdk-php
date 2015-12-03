<?php

require_once('../../LinkIDClient.php');
require_once('../ExampleConfig.php');

date_default_timezone_set('UTC'); // needed for parsing dates

$client = new LinkIDClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);

/**
 * Mandate payment
 */
$mandateReference = "2876527e-850e-4615-975c-94a29ff48fb8";
$paymentContext = new LinkIDPaymentContext(new LinkIDPaymentAmount(500, LinkIDCurrency::EUR, null), "Mandate Test");
$language = "nl";

$orderReference = $client->mandatePayment($mandateReference, $paymentContext, $language);

print("<h2>Mandate</h2>");
print("<li>Orderreference: " . $orderReference . "</ul>");

?>
