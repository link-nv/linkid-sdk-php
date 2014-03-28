<?php

require_once('LinkIDHawsClient.php');

/*
 * HAWS WebService client test
 *
 *
 */

function generateSamlRequest($acs, $destination, $issuer) {

    $id = mt_rand();
    $issueInstant = gmdate('Y-m-d\TH:i:s\Z');

    $authnRequest  = "<saml2p:AuthnRequest xmlns:saml2p=\"urn:oasis:names:tc:SAML:2.0:protocol\" ";
    $authnRequest .= "AssertionConsumerServiceURL=\"" . $acs . "\" ";
    $authnRequest .= "Destination=\"" . $destination . "\" ForceAuthn=\"false\" ";
    $authnRequest .= "ID=\"" . $id . "\" ";
    $authnRequest .= "IssueInstant=\"" . $issueInstant . "\" ";
    $authnRequest .= "ProtocolBinding=\"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST\" Version=\"2.0\">";

    $authnRequest .= "<saml2:Issuer xmlns:saml2=\"urn:oasis:names:tc:SAML:2.0:assertion\">" . $issuer . "</saml2:Issuer>";

    $authnRequest .= "<saml2p:NameIDPolicy AllowCreate=\"true\"/>";
    $authnRequest .= "</saml2p:AuthnRequest>";

    return $authnRequest;
}

$hawsClient = new LinkIDHawsClient('http://192.168.0.198:8080/linkid-ws-username/haws?wsdl', 'demo-test','08427E9F-6355-4DE4-B992-B1AB93CEE9D4');
$sessionId = $hawsClient->push(generateSamlRequest("http://192.168.5.14:9090/login", "http://192.168.0.198:8080/linkid-mobile/auth-min", "demo-test"), 'en');
print($sessionId);
?>

