<?php

/*
 * LinkID SAML v2.0 Utility class
 *
 * @author Wim Vandenhaute
 */

class LinkIDSaml2 {

    private $expectedChallenge;
    private $expectedAudience;

    public function generateAuthnRequest($appName, $loginConfig, $loginPage) {

        $this->expectedChallenge = mt_rand();
        $this->expectedAudience = $appName;

        $issueInstant = gmdate('Y-m-d\TH:i:s\Z');

        $authnRequest  = "<saml2p:AuthnRequest xmlns:saml2p=\"urn:oasis:names:tc:SAML:2.0:protocol\" ";
        $authnRequest .= "AssertionConsumerServiceURL=\"" . $loginPage . "\" ";
        $authnRequest .= "Destination=\"" . $loginConfig->linkIDLandingPage . "\" ForceAuthn=\"false\" ";
        $authnRequest .= "ID=\"" . $this->expectedChallenge . "\" ";
        $authnRequest .= "IssueInstant=\"" . $issueInstant . "\" ";
        $authnRequest .= "ProtocolBinding=\"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST\" Version=\"2.0\">";

        $authnRequest .= "<saml2:Issuer xmlns:saml2=\"urn:oasis:names:tc:SAML:2.0:assertion\">" . $appName . "</saml2:Issuer>";

        $authnRequest .= "<saml2p:NameIDPolicy AllowCreate=\"true\"/>";
        $authnRequest .= "</saml2p:AuthnRequest>";

        return $authnRequest;
    }

}
?>

