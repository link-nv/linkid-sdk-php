<?php

require_once('../_include.php');

$transactionId = $_GET['txn_id'];
// fetch payment state for this transaction
$paymentState = SAML2_PaymentUtils::getPaymentStatus($transactionId);

//echo "<pre>".print_r($result, true)."</pre>";
?>

<html>
<head>


    <h1>linkID Mobile Payment status update POC</h1>

</head>

<body>

<?php
print $transactionId . ' : ' . $paymentState;
?>

</body>

</html>

