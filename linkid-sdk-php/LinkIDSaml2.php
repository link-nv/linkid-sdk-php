<?php

/*
 * LinkID SAML v2.0 Utility class
 *
 * @author Wim Vandenhaute
 */

class LinkIDSaml2 {

    public $expectedChallenge;
    public $expectedAudience;

    public function generateAuthnRequest($appName, $loginConfig, $loginPage) {

        $this->expectedChallenge = $this->gen_uuid();
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

    public function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

}
?>