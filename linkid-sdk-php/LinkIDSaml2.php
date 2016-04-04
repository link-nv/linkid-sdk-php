<?php

require_once('LinkIDAuthnContext.php');
require_once('LinkIDAttribute.php');
require_once('LinkIDPaymentContext.php');
require_once('LinkIDCallback.php');

/*
 * LinkID SAML v2.0 Utility class
 *
 * @author Wim Vandenhaute
 */

class LinkIDSaml2
{

    /**
     * @param LinkIDAuthenticationContext $authenticationContext
     * @return string the SAML v2.0 authentication request
     */
    public function generateAuthnRequest($authenticationContext)
    {

        $requestID = $this->gen_uuid();

        $issueInstant = gmdate('Y-m-d\TH:i:s\Z');

        $authnRequest = "<saml2p:AuthnRequest xmlns:saml2p=\"urn:oasis:names:tc:SAML:2.0:protocol\" ";
        $authnRequest .= "AssertionConsumerServiceURL=\"http://foo.bar\" ForceAuthn=\"false\" ";
        $authnRequest .= "ID=\"" . $requestID . "\" ";
        $authnRequest .= "IssueInstant=\"" . $issueInstant . "\" ";
        $authnRequest .= "ProtocolBinding=\"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST\" Version=\"2.0\">";

        $authnRequest .= "<saml2:Issuer xmlns:saml2=\"urn:oasis:names:tc:SAML:2.0:assertion\">" . $authenticationContext->applicationName . "</saml2:Issuer>";

        $authnRequest .= "<saml2p:NameIDPolicy AllowCreate=\"true\"/>";

        $authnRequest .= "<saml2p:Extensions>";

        $authnRequest .= "<saml2:DeviceContext xmlns:saml2=\"urn:oasis:names:tc:SAML:2.0:assertion\">";

        if (null != $authenticationContext->authenticationMessage) {
            $authnRequest .= "<saml2:Attribute Name=\"linkID.authenticationMessage\">";

            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">";
            $authnRequest .= $authenticationContext->authenticationMessage;
            $authnRequest .= "</saml2:AttributeValue>";

            $authnRequest .= "</saml2:Attribute>";
        }
        if (null != $authenticationContext->finishedMessage) {
            $authnRequest .= "<saml2:Attribute Name=\"linkID.finishedMessage\">";

            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">";
            $authnRequest .= $authenticationContext->finishedMessage;
            $authnRequest .= "</saml2:AttributeValue>";

            $authnRequest .= "</saml2:Attribute>";
        }

        if (null != $authenticationContext->identityProfile) {
            $authnRequest .= "<saml2:Attribute Name=\"linkID.identityProfile\">";

            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">";
            $authnRequest .= $authenticationContext->identityProfile;
            $authnRequest .= "</saml2:AttributeValue>";

            $authnRequest .= "</saml2:Attribute>";
        }
        if (null != $authenticationContext->sessionExpiryOverride) {
            $authnRequest .= "<saml2:Attribute Name=\"linkID.sessionExpiryOverride\">";

            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">";
            $authnRequest .= $authenticationContext->sessionExpiryOverride;
            $authnRequest .= "</saml2:AttributeValue>";

            $authnRequest .= "</saml2:Attribute>";
        }
        if (null != $authenticationContext->theme) {
            $authnRequest .= "<saml2:Attribute Name=\"linkID.theme\">";

            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">";
            $authnRequest .= $authenticationContext->theme;
            $authnRequest .= "</saml2:AttributeValue>";

            $authnRequest .= "</saml2:Attribute>";
        }
        if (null != $authenticationContext->notificationLocation) {
            $authnRequest .= "<saml2:Attribute Name=\"linkID.notificationLocation\">";

            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">";
            $authnRequest .= $authenticationContext->notificationLocation;
            $authnRequest .= "</saml2:AttributeValue>";

            $authnRequest .= "</saml2:Attribute>";
        }

        if (null != $authenticationContext->mobileLandingSuccess) {
            $authnRequest .= "<saml2:Attribute Name=\"linkID.mobileLandingSuccess\">";

            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">";
            $authnRequest .= $authenticationContext->mobileLandingSuccess;
            $authnRequest .= "</saml2:AttributeValue>";

            $authnRequest .= "</saml2:Attribute>";
        }
        if (null != $authenticationContext->mobileLandingError) {
            $authnRequest .= "<saml2:Attribute Name=\"linkID.mobileLandingError\">";

            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">";
            $authnRequest .= $authenticationContext->mobileLandingError;
            $authnRequest .= "</saml2:AttributeValue>";

            $authnRequest .= "</saml2:Attribute>";
        }
        if (null != $authenticationContext->mobileLandingCancel) {
            $authnRequest .= "<saml2:Attribute Name=\"linkID.mobileLandingCancel\">";

            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">";
            $authnRequest .= $authenticationContext->mobileLandingCancel;
            $authnRequest .= "</saml2:AttributeValue>";

            $authnRequest .= "</saml2:Attribute>";
        }

        $authnRequest .= "</saml2:DeviceContext>";

        /**
         * Optional linkID attribute suggestions
         */
        if (null != $authenticationContext->attributeSuggestions) {

            $authnRequest .= "<saml2:SubjectAttributes xmlns:saml2=\"urn:oasis:names:tc:SAML:2.0:assertion\">";

            foreach ($authenticationContext->attributeSuggestions as $key => $value) {

                // determine type
                $type = gettype($value);
                $xsType = "xs:string";
                $xsValue = $value;

                if ($type == "boolean") {
                    $xsType = "xs:boolean";
                } else if ($type == "integer") {
                    $xsType = "xs:integer";
                } else if ($type == "double") {
                    $xsType = "xs:float";
                } else if ($type == "string") {
                    $xsType = "xs:string";
                } else if ($type == "object" && $value instanceof DateTime) {
                    $xsType = "xs:dateTime";
                    $xsValue = $value->format(DateTime::ATOM);
                } else {
                    continue;
                }


                $authnRequest .= "<saml2:Attribute Name=\"" . $key . "\">";
                $authnRequest .= "<saml2:AttributeValue xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xsi:type=\"" . $xsType . "\">" . $xsValue . "</saml2:AttributeValue>";
                $authnRequest .= "</saml2:Attribute>";

            }

            $authnRequest .= "</saml2:SubjectAttributes>";
        }

        /**
         * Optional callback
         */
        if (null != $authenticationContext->callback) {

            $authnRequest .= "<saml2:Callback xmlns:saml2=\"urn:oasis:names:tc:SAML:2.0:assertion\">";

            $authnRequest .= "<saml2:Attribute Name=\"Callback.location\">";
            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->callback->location . "</saml2:AttributeValue>";
            $authnRequest .= "</saml2:Attribute>";

            $authnRequest .= "<saml2:Attribute Name=\"Callback.inApp\">";
            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:boolean\">" . ($authenticationContext->callback->inApp ? "true" : "false") . "</saml2:AttributeValue>";
            $authnRequest .= "</saml2:Attribute>";

            if (null != $authenticationContext->callback->appSessionId) {
                $authnRequest .= "<saml2:Attribute Name=\"Callback.appSessionId\">";
                $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->callback->appSessionId . "</saml2:AttributeValue>";
                $authnRequest .= "</saml2:Attribute>";
            }

            $authnRequest .= "</saml2:Callback>";

        }

        /**
         * Optional payment context
         */
        if (null != $authenticationContext->paymentContext) {

            $authnRequest .= "<saml2:PaymentContext xmlns:saml2=\"urn:oasis:names:tc:SAML:2.0:assertion\">";

            $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.amount\">";
            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->amount->amount . "</saml2:AttributeValue>";
            $authnRequest .= "</saml2:Attribute>";

            if (null != $authenticationContext->paymentContext->amount) {

                if (null != $authenticationContext->paymentContext->amount->walletCoin) {
                    $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.walletCoin\">";
                    $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->amount->walletCoin . "</saml2:AttributeValue>";
                    $authnRequest .= "</saml2:Attribute>";
                } else {
                    $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.currency\">";
                    $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . linkIDCurrencyToString($authenticationContext->paymentContext->amount->currency) . "</saml2:AttributeValue>";
                    $authnRequest .= "</saml2:Attribute>";
                }
            }

            $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.description\">";
            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->description . "</saml2:AttributeValue>";
            $authnRequest .= "</saml2:Attribute>";

            if (null != $authenticationContext->paymentContext->orderReference) {
                $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.orderReference\">";
                $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->orderReference . "</saml2:AttributeValue>";
                $authnRequest .= "</saml2:Attribute>";
            }

            $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.validationTime\">";
            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->validationTime . "</saml2:AttributeValue>";
            $authnRequest .= "</saml2:Attribute>";

            $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.mandate\">";
            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:boolean\">" . (null != $authenticationContext->paymentContext->mandate ? "true" : "false") . "</saml2:AttributeValue>";
            $authnRequest .= "</saml2:Attribute>";

            if (null != $authenticationContext->paymentContext->mandate) {

                if (null != $authenticationContext->paymentContext->mandate->description) {
                    $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.mandateDescription\">";
                    $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->mandate->description . "</saml2:AttributeValue>";
                    $authnRequest .= "</saml2:Attribute>";
                }
                if (null != $authenticationContext->paymentContext->mandate->reference) {
                    $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.mandateReference\">";
                    $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->mandate->reference . "</saml2:AttributeValue>";
                    $authnRequest .= "</saml2:Attribute>";

                }
            }

            if (null != $authenticationContext->paymentContext->paymentMenu) {

                $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.menuResultSuccess\">";
                $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->paymentMenu->menuResultSuccess . "</saml2:AttributeValue>";
                $authnRequest .= "</saml2:Attribute>";

                $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.menuResultCanceled\">";
                $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->paymentMenu->menuResultCancel . "</saml2:AttributeValue>";
                $authnRequest .= "</saml2:Attribute>";

                $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.menuResultPending\">";
                $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->paymentMenu->menuResultPending . "</saml2:AttributeValue>";
                $authnRequest .= "</saml2:Attribute>";

                $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.menuResultError\">";
                $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->paymentMenu->menuResultError . "</saml2:AttributeValue>";
                $authnRequest .= "</saml2:Attribute>";

            }

            $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.allowPartial\">";
            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:boolean\">" . ($authenticationContext->paymentContext->allowPartial ? "true" : "false") . "</saml2:AttributeValue>";
            $authnRequest .= "</saml2:Attribute>";

            $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.onlyWallets\">";
            $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:boolean\">" . ($authenticationContext->paymentContext->onlyWallets ? "true" : "false") . "</saml2:AttributeValue>";
            $authnRequest .= "</saml2:Attribute>";

            if (null != $authenticationContext->paymentContext->paymentStatusLocation) {
                $authnRequest .= "<saml2:Attribute Name=\"PaymentContext.statusLocation\">";
                $authnRequest .= "<saml2:AttributeValue xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:type=\"xs:string\">" . $authenticationContext->paymentContext->paymentStatusLocation . "</saml2:AttributeValue>";
                $authnRequest .= "</saml2:Attribute>";
            }

            $authnRequest .= "</saml2:PaymentContext>";
        }

        $authnRequest .= "</saml2p:Extensions>";

        $authnRequest .= "</saml2p:AuthnRequest>";

        return $authnRequest;
    }

    public function parseXmlAuthnResponse($xmlAuthnResponse)
    {

        $inResponseTo = $xmlAuthnResponse->attributes()->InResponseTo;

        // check status success
        $statusValue = $xmlAuthnResponse->children("urn:oasis:names:tc:SAML:2.0:protocol")->Status[0]->StatusCode[0]->attributes()->Value;
        if ($statusValue != "urn:oasis:names:tc:SAML:2.0:status:Success") {
            return null;
        }

        // get userId
        $userId = (string)$xmlAuthnResponse->children("urn:oasis:names:tc:SAML:2.0:assertion")->Assertion[0]->Subject[0]->NameID[0];

        // check audience
        $audience = (string)$xmlAuthnResponse->children("urn:oasis:names:tc:SAML:2.0:assertion")->Assertion[0]->Conditions[0]->AudienceRestriction[0]->Audience[0];

        // validate NotBefore/NotOnOrAfter conditions
        $notBeforeString = (string)$xmlAuthnResponse->children("urn:oasis:names:tc:SAML:2.0:assertion")->Assertion[0]->Conditions[0]->attributes()->NotBefore;
        $notOnOrAfterString = (string)$xmlAuthnResponse->children("urn:oasis:names:tc:SAML:2.0:assertion")->Assertion[0]->Conditions[0]->attributes()->NotOnOrAfter;
        $this->checkConditionsTime($notBeforeString, $notOnOrAfterString);

        // parse attributes;
        $attributes = $this->getAttributes($xmlAuthnResponse->children("urn:oasis:names:tc:SAML:2.0:assertion")->Assertion[0]->AttributeStatement[0]);

        // parse payment response if any
        $extensions = $xmlAuthnResponse->children("urn:oasis:names:tc:SAML:2.0:protocol")->Extensions[0];
        $paymentResponse = null;
        if (null != $extensions) {
            $paymentResponse = $this->getPaymentResponse($extensions->children("urn:oasis:names:tc:SAML:2.0:assertion")->PaymentResponse[0]);
        }

        return new LinkIDAuthnContext($userId, $attributes, $paymentResponse);
    }

    public function getPaymentResponse($xmlPaymentResponse)
    {

        if (null == $xmlPaymentResponse) return null;

        $orderReference = null;
        $paymentState = null;
        $mandateReference = null;
        $docdataReference = null;
        $paymentMenuURL = null;
        foreach ($xmlPaymentResponse->Attribute as $xmlAttribute) {
            if ($xmlAttribute->attributes()->Name == "PaymentResponse.txnId") {
                $orderReference = (string)$xmlAttribute->AttributeValue[0];
            } else if ($xmlAttribute->attributes()->Name == "PaymentResponse.state") {
                $paymentState = (string)$xmlAttribute->AttributeValue[0];
            } else if ($xmlAttribute->attributes()->Name == "PaymentResponse.mandateRef") {
                $mandateReference = (string)$xmlAttribute->AttributeValue[0];
            } else if ($xmlAttribute->attributes()->Name == "PaymentResponse.docdataRef") {
                $docdataReference = (string)$xmlAttribute->AttributeValue[0];
            } else if ($xmlAttribute->attributes()->Name == "PaymentResponse.menuURL") {
                $paymentMenuURL = (string)$xmlAttribute->AttributeValue[0];
            }
        }

        return new LinkIDPaymentResponse($orderReference, $paymentState, $mandateReference, $docdataReference, $paymentMenuURL);
    }

    public function getAttributes($attributeStatement)
    {

        $attributes = array();

        foreach ($attributeStatement->Attribute as $xmlAttribute) {

            $attribute = $this->getAttribute($xmlAttribute);

            $attributeList = array();
            if (array_key_exists($attribute->name, $attributes)) {
                $attributeList = $attributes[$attribute->name];
            }

            array_push($attributeList, $attribute);
            $attributes[$attribute->name] = $attributeList;
        }

        return $attributes;
    }

    public function getAttribute($xmlAttribute)
    {

        /** @noinspection PhpUndefinedMethodInspection */
        $name = (string)$xmlAttribute->attributes()->Name;
        /** @noinspection PhpUndefinedMethodInspection */
        $id = (string)$xmlAttribute->attributes("urn:net:lin-k:safe-online:saml")->attributeId;

        $value = null;
        if (isset($xmlAttribute->AttributeValue[0]->Attribute[0])) {

            // compound
            $value = array();
            foreach ($xmlAttribute->AttributeValue[0] as $xmlMemberAttribute) {

                /** @noinspection PhpUndefinedMethodInspection */
                $memberAttribute = new LinkIDAttribute($id, (string)$xmlMemberAttribute->attributes()->Name, $this->getAttributeValue($xmlMemberAttribute->AttributeValue[0]));
                $value[$memberAttribute->name] = $memberAttribute;
            }

        } else if (isset($xmlAttribute->AttributeValue[0]->AttributeValue[0])) {

            // ws compound
            $value = array();
            foreach ($xmlAttribute->AttributeValue[0] as $xmlMemberAttribute) {

                /** @noinspection PhpUndefinedMethodInspection */
                $memberAttribute = new LinkIDAttribute($id, (string)$xmlMemberAttribute->attributes()->Name, $this->getAttributeValue($xmlMemberAttribute->AttributeValue[0]));
                $value[$memberAttribute->name] = $memberAttribute;

            }

        } else {
            $value = $this->getAttributeValue($xmlAttribute->AttributeValue[0]);
        }

        return new LinkIDAttribute($id, $name, $value);
    }

    public function getAttributeValue($xmlAttributeValue)
    {

        date_default_timezone_set('UTC'); // needed for parsing dates

        /** @noinspection PhpUndefinedMethodInspection */
        $type = $xmlAttributeValue->attributes("http://www.w3.org/2001/XMLSchema-instance")->type;

        if ($type == "xs:string") {
            return (string)$xmlAttributeValue;
        } else if ($type == "xs:boolean") {
            return filter_var($xmlAttributeValue, FILTER_VALIDATE_BOOLEAN);
        } else if ($type == "xs:integer" || $type == "xs:int") {
            return (integer)$xmlAttributeValue;
        } else if ($type == "xs:long") {
            return (float)$xmlAttributeValue;
        } else if ($type == "xs:float") {
            return (float)$xmlAttributeValue;
        } else if ($type == "xs:dateTime") {
            return new DateTime((string)$xmlAttributeValue);
        }

        return null;
    }

    public function checkConditionsTime($notBeforeString, $notOnOrAfterString)
    {

        $notBefore = new DateTime($notBeforeString);
        $notOnOrAfter = new DateTime($notOnOrAfterString);
        $now = new DateTime();

        if ($now <= $notBefore) {

            $now->add(new DateInterval('PT' . 5 . 'M'));
            if ($now < $notBefore) throw new Exception("SAML2 assertion invalid: invalid timeframe");
            $now->sub(new DateInterval('PT' . 10 . 'M'));
            if ($now > $notOnOrAfter) throw new Exception("SAML2 assertion invalid: invalid timeframe");

        } else {
            if ($now < $notBefore || $now > $notOnOrAfter) {
                throw new Exception("SAML2 assertion invalid: invalid timeframe");
            }
        }

    }

    public function gen_uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

}