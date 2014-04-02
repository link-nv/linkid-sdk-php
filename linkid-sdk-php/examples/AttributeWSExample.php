<?php

require_once('../LinkIDAttributeClient.php');

$linkIDHost = "192.168.5.14:8443";
// $linkIDHost = "demo.linkid.be";

// username/password
$linkIDWSUsername = "demo-test";
$linkIDWSPassword = "08427E9F-6355-4DE4-B992-B1AB93CEE9D4";

$userId = "2b35dbab-2ba2-403b-8c36-a8399c3af3d5";

$client = new LinkIDAttributeClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);
$attributes = $client->getAttributes($userId);
//$attributes = $client->getAttributes($userId,"profile.address");

print("<h2>Attribute for " . $userId . "</h2>");
print("<pre>");
print_r($attributes);
print("</pre>");

?>
