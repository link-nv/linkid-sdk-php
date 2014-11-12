<?php

/*
 * LinkID Payment context
 *
 * @author Wim Vandenhaute
 */

class LinkIDPaymentContext
{

    public $amount;
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

    // PaymentAddBrowser constants
    const PAYMENT_ADD_BROWSER_NOT_ALLOWED = 0;
    const PAYMENT_ADD_BROWSER_REDIRECT = 1;

    /**
     * Constructor
     */
    public function __construct($amount, $description, $orderReference = null, $profile = null, $validationTime = 5,
                                $paymentAddBrowser = LinkIDPaymentContext::PAYMENT_ADD_BROWSER_NOT_ALLOWED, $allowDeferredPay = false,
                                $mandate = false, $mandateDescription = null, $mandateReference = null)
    {

        $this->amount = $amount;
        $this->description = $description;

        $this->orderReference = $orderReference;
        $this->profile = $profile;
        $this->validationTime = $validationTime;
        $this->paymentAddBrowser = $paymentAddBrowser;
        $this->allowDeferredPay = $allowDeferredPay;

        $this->mandate = $mandate;
        $this->mandateDescription = $mandateDescription;
        $this->mandateReference = $mandateReference;
    }

    public function convertPaymentAddBrowser()
    {

        if (LinkIDPaymentContext::PAYMENT_ADD_BROWSER_REDIRECT == $this->paymentAddBrowser) {
            return "REDIRECT";
        }

        // default
        return "NOT_ALLOWED";
    }
}