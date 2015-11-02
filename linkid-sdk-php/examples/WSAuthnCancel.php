<?php

require_once('ExampleConfig.php');
require_once('../LinkIDClient.php');
require_once('../LinkIDSaml2.php');

if (!isset($_REQUEST['sessionId'])) {
    return;
}
$sessionId = $_REQUEST['sessionId'];

$client = new LinkIDClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);
$client->authCancel($sessionId);

print("Canceled");
