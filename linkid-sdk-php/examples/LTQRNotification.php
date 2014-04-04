<?php

 require_once('ExampleConfig.php');
 require_once('../LinkIDLTQRClient.php');

 // get order reference
 if (!isset($_REQUEST['orderRef'])) {
     return;
 }
 $orderReference = $_REQUEST['orderRef'];

 // get order reference
 if (!isset($_REQUEST['clientSessionId'])) {
     return;
 }
 $clientSessionId = $_REQUEST['clientSessionId'];

 $client = new LinkIDLTQRClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);
 $clientSessions = $client->pull(array($orderReference), array($clientSessionId));

 print("<h2>LTQR Client Sessions</h2>");

 print("<pre>");
 print_r($clientSessions);
 print("</pre>");


?>