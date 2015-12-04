<?php

require_once('../../LinkIDClient.php');
require_once('../ExampleConfig.php');

date_default_timezone_set('UTC'); // needed for parsing dates

$client = new LinkIDClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);

$walletOrganizationId = "urn:linkid:wallet:leaseplan";
$userId = "9e4d2818-d9d4-454c-9b1d-1f067a1f7469";

// Get wallet info

$walletInfo = $client->walletGetInfo($userId, $walletOrganizationId);

print("<h2>Wallet info</h2>");
print("<pre>");
print_r($walletInfo);
print("</pre>");

// Add / remove credit

$walletId = "6e2cc86f-4178-46e5-a483-ca5fd0ebd4a1";
$walletCoin = "urn:linkid:wallet:coin:coffee";

$client->walletAddCredit($userId, $walletId, 500, null, $walletCoin);
$client->walletRemoveCredit($userId, $walletId, 500, null, $walletCoin);

?>
