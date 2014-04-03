<?php

 require_once('../../LinkIDLTQRClient.php');
 require_once('../ExampleConfig.php');

 date_default_timezone_set('UTC'); // needed for parsing dates

 /**
  * Push LTQR session
  */


 $paymentContext = new LinkIDPaymentContext(500, "LTQR Test");

 $client = new LinkIDLTQRClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);
 //$client->push($paymentContext, $true, new DateTime(), 500);
 $ltqrSession = $client->push($paymentContext);

 print("<h2>LTQR Session</h2>");
 print("<ul><li>URL : " . $ltqrSession->qrCodeURL);
 print("<li>Orderreference: " . $ltqrSession->orderReference . "</ul>");

 $imgData = base64_encode($ltqrSession->qrCodeImage);
 print("<img src='data:image/png;base64, $imgData' />");


 /**
  * Fetch client sessions
  */


 $orderReferences = array();
 $orderReferences[] = "c8b2bdcd-1782-474b-b9aa-22a4b864c57e";
 $orderReferences[] = "f00";
// $clientSessionIds = array();
// $clientSessionIds[] = "f00";
// $clientSessionIds[] = "bar";
 print("<h2>Fetch client sessions for " . $orderReferences[0] . "</h2>");

 $clientSessions = $client->pull($orderReferences, $clientSessionIds);

 print("<h2>LTQR Client Sessions</h2>");

 print("<pre>");
 print_r($clientSessions);
 print("</pre>");


 /**
  * Remove client sessions
  */

 $orderReferences = array();
 $orderReferences[] = "c8b2bdcd-1782-474b-b9aa-22a4b864c57e";
 $orderReferences[] = "f00";

 $client->remove($orderReferences, $clientSessionIds);

?>
