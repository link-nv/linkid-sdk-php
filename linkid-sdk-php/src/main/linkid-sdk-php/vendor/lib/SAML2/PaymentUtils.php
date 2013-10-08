<?php

class SAML2_PaymentUtils
{

    /**
     * Create a linkID Payment context extension
     *
     * @param int $amount
     * @param string $description
     * @param string $profile
     * @param int $validationTime
     * @param bool $showAddLink
     * @param bool $deferredPay
     * @return SAML2_XML_Chunk payment context object
     */
    public static function createLinkIDPaymentExtension($amount, $description, $profile, $validationTime, $showAddLink, $deferredPay)
    {

        $dom = new DOMDocument();
        $xmlRoot = $dom->createElementNS('urn:oasis:names:tc:SAML:2.0:assertion', 'saml2:PaymentContext');

        SAML2_PaymentUtils::createAttribute($xmlRoot, $dom, "PaymentContext.amount", $amount);
        SAML2_PaymentUtils::createAttribute($xmlRoot, $dom, "PaymentContext.currency", "EUR");
        if (null != $description)
            SAML2_PaymentUtils::createAttribute($xmlRoot, $dom, "PaymentContext.description", $description);
        if (null != $profile)
            SAML2_PaymentUtils::createAttribute($xmlRoot, $dom, "PaymentContext.profile", $profile);
        SAML2_PaymentUtils::createAttribute($xmlRoot, $dom, "PaymentContext.validationTime", $validationTime);

        if ($showAddLink)
            SAML2_PaymentUtils::createAttribute($xmlRoot, $dom, "PaymentContext.addLinkKey", "true");
        else
            SAML2_PaymentUtils::createAttribute($xmlRoot, $dom, "PaymentContext.addLinkKey", "false");

        if ($deferredPay)
            SAML2_PaymentUtils::createAttribute($xmlRoot, $dom, "PaymentContext.deferredPay", "true");
        else
            SAML2_PaymentUtils::createAttribute($xmlRoot, $dom, "PaymentContext.deferredPay", "false");

        $ext[] = new SAML2_XML_Chunk($xmlRoot);
        return $ext;
    }

    public static function createAttribute($xmlRoot, $dom, $name, $value)
    {

        $attribute = $dom->createElement("saml2:Attribute");
        $attribute->setAttribute("Name", $name);
        $attributeValue = $dom->createElement("saml2:AttributeValue", $value);
        $attributeValue->setAttributeNS("http://www.w3.org/2001/XMLSchema-instance", "xsi:type", "xs:string");
        $attribute->appendChild($attributeValue);
        $xmlRoot->appendChild($attribute);
    }

    /**
     * Returns the payment state for the specified payment transaction.
     * Possible return values are:
     *
     * STARTED          : Payment is being processed
     * AUTHORIZED       : Payment is authorized
     * FAILED           : Payment has failed
     *
     * @param string $transactionId the transaction Id
     * @return string PaymentState
     */
    public static function getPaymentStatus($transactionId)
    {

        $paymentPortUrl = "https://demo.linkid.be/linkid-ws/payment?wsdl";
        $client = new SoapClient($paymentPortUrl, array("trace" => 0, "exception" => 1));
        $result = $client->__soapCall("status", array(
            "PaymentStatusRequest" => array("transactionId" => $transactionId)
        ), NULL, null);
        return $result->paymentStatus;
    }
}