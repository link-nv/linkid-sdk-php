<?php

require_once('../_include.php');

$as = new SimpleSAML_Auth_Simple('example-mobile');

if (!$as->isAuthenticated()) {

    // initiate linkID login
    $as->login(array(
        'saml:idp' => 'linkID',
        'ReturnTo' => 'https://192.168.5.14/example-mobile/loggedin.php',
        'ErrorURL' => 'http://192.168.5.14/example-mobile/failed.php',
        'linkID:mobileMinimal' => 'true',
        'saml:Extensions' => SAML2_PaymentUtils::createLinkIDPaymentExtension(500, "Testing...", null, 5, true, false),
    ));

} else {

    header("Location: ./index.php");

}

?>