<?php

require_once('LinkIDCurrency.php');
require_once('LinkIDPaymentAddBrowser.php');

/*
 * LinkID Payment context
 *
 * @author Wim Vandenhaute
 */

class LinkIDPaymentContext
{

    public $amount;
    public $currency;
    public $description;

    // optional order reference, if not specified linkID will generate one in UUID format
    public $orderReference;

    // optional payment profile
    public $profile;

    // maximum time to wait for payment validation, if not specified defaults to 5s
    public $validationTime;

    // whether or not to allow to display the option in the client to add a payment method in the browser, default is not allowed
    public $paymentAddBrowser;

    // whether or not deferred payments are allowed, if a user has no payment token attached to the linkID account
    // linkID can allow for the user to make a deferred payment which he can complete later on from his browser.
    public $allowDeferredPay;

    // optional payment menu return URLs (returnPaymentMenuURL)
    public $paymentMenuResultSuccess;
    public $paymentMenuResultCanceled;
    public $paymentMenuResultPending;
    public $paymentMenuResultError;
    // mandates
    public $mandate;
    public $mandateDescription;
    public $mandateReference;

    public $allowPartial; // allow partial payments via wallets, this flag does make sense if you allow normal payment methods
    public $onlyWallets; // allow only wallets for this payment

    /**
     * Constructor
     */
    public function __construct($amount, $currency, $description, $orderReference = null, $profile = null, $validationTime = 5,
                                $paymentAddBrowser = LinkIDPaymentAddBrowser::NOT_ALLOWED, $allowDeferredPay = false,
                                $mandate = false, $mandateDescription = null, $mandateReference = null, $allowPartial = false, $onlyWallets = false)
    {

        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;

        $this->orderReference = $orderReference;
        $this->profile = $profile;
        $this->validationTime = $validationTime;
        $this->paymentAddBrowser = $paymentAddBrowser;
        $this->allowDeferredPay = $allowDeferredPay;

        $this->mandate = $mandate;
        $this->mandateDescription = $mandateDescription;
        $this->mandateReference = $mandateReference;

        $this->allowPartial = $allowPartial;
        $this->onlyWallets = $onlyWallets;
    }

}

function parseLinkIDPaymentContext($xmlPaymentContext)
{
    return new LinkIDPaymentContext(
        isset($xmlPaymentContext->amount) ? $xmlPaymentContext->amount : null,
        isset($xmlPaymentContext->currency) ? parseLinkIDCurrency($xmlPaymentContext->currency) : null,
        isset($xmlPaymentContext->description) ? $xmlPaymentContext->description : null,
        isset($xmlPaymentContext->orderReference) ? $xmlPaymentContext->orderReference : null,
        isset($xmlPaymentContext->profile) ? $xmlPaymentContext->profile : null,
        isset($xmlPaymentContext->validationTime) ? $xmlPaymentContext->validationTime : null,
        isset($xmlPaymentContext->paymentAddBrowser) ? $xmlPaymentContext->paymentAddBrowser : null,
        isset($xmlPaymentContext->allowDeferredPay) ? $xmlPaymentContext->allowDeferredPay : null,
        isset($xmlPaymentContext->allowPartial) ? $xmlPaymentContext->allowPartial : false,
        isset($xmlPaymentContext->onlyWallets) ? $xmlPaymentContext->onlyWallets : false,
        isset($xmlPaymentContext->mandate) ? $xmlPaymentContext->mandate : false,
        isset($xmlPaymentContext->mandateDescription) ? $xmlPaymentContext->mandateDescription : null,
        isset($xmlPaymentContext->mandateReference) ? $xmlPaymentContext->mandateReference : null
    );
}