<?php

require_once('../../LinkIDClient.php');
require_once('../ExampleConfig.php');

date_default_timezone_set('UTC'); // needed for parsing dates

$client = new LinkIDClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);

// Orders lookup on date

$orders = $client->getPaymentReport(new LinkIDReportDateFilter(new DateTime('2015-06-20'), null), null);

print("<h2>Payment orders since 2015-04-01</h2>");
print("<pre>");
print_r($orders);
print("</pre>");

// Orders lookup on orderReferences

$orderReferences = array();
$orderReferences[] = "08498c36674647a08c720339cdd79cf7";
$orderReferences[] = "409b3f70dfbe48899b30e9ab92935d7f";
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

$sessions = $client->getParkingReport(new LinkIDReportDateFilter(new DateTime('2014-01-01'), null));

print("<h2>Parking sessions since 2014-01-01</h2>");
print("<pre>");
print_r($sessions);
print("</pre>");

// Wallet transactions lookup on date

$walletOrganizationId = "urn:linkid:wallet:leaseplan";

$walletReport = $client->getWalletReport("en", $walletOrganizationId, null, null, new LinkIDReportDateFilter(new DateTime('2014-01-01')));

print("<h2>Wallet transactions sessions since 2014-01-01</h2>");
print("<pre>");
print_r($walletReport);
print("</pre>");

// Wallet transactions lookup on application name

$walletOrganizationId = "urn:linkid:wallet:leaseplan";
$applicationName = "test-shop";

$walletReport = $client->getWalletReport("en", $walletOrganizationId, new LinkIDReportApplicationFilter($applicationName));

print("<h2>Wallet transactions for application " . $applicationName . "</h2>");
print("<pre>");
print_r($walletReport);
print("</pre>");

// Wallet transactions lookup on wallet

$walletOrganizationId = "urn:linkid:wallet:leaseplan";
$walletId = "ff52177f-8f80-4640-9e86-558f6b1b24c3";
$userId = "e4269366-ddfb-43dc-838d-01569a8c4c22";

$walletReport = $client->getWalletReport("en", $walletOrganizationId, null, new LinkIDReportWalletFilter($walletId, $userId));

print("<h2>Wallet transactions for walletId " . $walletId . " and userId " . $userId . "</h2>");
print("<pre>");
print_r($walletReport);
print("</pre>");

// Wallet info report

$walletIds = array();
$walletIds[] = "123b1c22-e6c5-4ebc-9255-e59b72db5abf";
$walletIds[] = "13ff6203-a086-483a-8e3c-382ce63f9a9a";

$walletInfoReport = $client->getWalletInfoReport("en", $walletIds);

print("<h2>Wallet info report</h2>");
print("<pre>");
print_r($walletInfoReport);
print("</pre>");
