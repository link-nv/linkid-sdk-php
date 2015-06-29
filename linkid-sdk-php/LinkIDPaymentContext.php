<?php

require_once('LinkIDPaymentAmount.php');
require_once('LinkIDPaymentMandate.php');
require_once('LinkIDPaymentMenu.php');
require_once('LinkIDPaymentAddBrowser.php');

/*
 * LinkID Payment context
 *
 * @author Wim Vandenhaute
 */

class LinkIDPaymentContext
{

    /* @var $amount LinkIDPaymentAmount */
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
    /* @var $paymentMenu LinkIDPaymentMenu */
    public $paymentMenu;

    // mandates
    /* @var $mandate LinkIDPaymentMandate */
    public $mandate;

    public $allowPartial; // allow partial payments via wallets, this flag does make sense if you allow normal payment methods
    public $onlyWallets; // allow only wallets for this payment

    /**
     * Constructor
     */
    public function __construct(LinkIDPaymentAmount $amount, $description, $orderReference = null, $profile = null, $validationTime = 5,
                                $paymentAddBrowser = LinkIDPaymentAddBrowser::NOT_ALLOWED, $allowDeferredPay = false,
                                LinkIDPaymentMandate $mandate = null, $allowPartial = false, $onlyWallets = false)
    {

        $this->amount = $amount;
        $this->description = $description;

        $this->orderReference = $orderReference;
        $this->profile = $profile;
        $this->validationTime = $validationTime;
        $this->paymentAddBrowser = $paymentAddBrowser;
        $this->allowDeferredPay = $allowDeferredPay;

        $this->mandate = $mandate;

        $this->allowPartial = $allowPartial;
        $this->onlyWallets = $onlyWallets;
    }

}

function parseLinkIDPaymentContext($xmlPaymentContext)
{

    $amount = isset($xmlPaymentContext->amount) ? $xmlPaymentContext->amount : null;
    $currency = isset($xmlPaymentContext->currency) ? parseLinkIDCurrency($xmlPaymentContext->currency) : null;
    $walletCoin = isset($xmlPaymentContext->walletCoin) ? $xmlPaymentContext->walletCoin : null;

    /* @var $linkIDPaymentAmount LinkIDPaymentAmount */
    $linkIDPaymentAmount = new LinkIDPaymentAmount($amount, $currency, $walletCoin);

    $mandateDescription = isset($xmlPaymentContext->mandateDescription) ? $xmlPaymentContext->mandateDescription : null;
    $mandateReference = isset($xmlPaymentContext->mandateReference) ? $xmlPaymentContext->mandateReference : null;

    /* @var $linkIDPaymentMandate LinkIDPaymentMandate */
    $linkIDPaymentMandate = null;
    if (null != $mandateDescription) {
        $linkIDPaymentMandate = new LinkIDPaymentMandate($mandateDescription, $mandateReference);
    }

    return new LinkIDPaymentContext(
        $linkIDPaymentAmount,
        isset($xmlPaymentContext->currency) ? parseLinkIDCurrency($xmlPaymentContext->currency) : null,
        isset($xmlPaymentContext->description) ? $xmlPaymentContext->description : null,
        isset($xmlPaymentContext->orderReference) ? $xmlPaymentContext->orderReference : null,
        isset($xmlPaymentContext->profile) ? $xmlPaymentContext->profile : null,
        isset($xmlPaymentContext->validationTime) ? $xmlPaymentContext->validationTime : null,
        isset($xmlPaymentContext->paymentAddBrowser) ? $xmlPaymentContext->paymentAddBrowser : null,
        isset($xmlPaymentContext->allowDeferredPay) ? $xmlPaymentContext->allowDeferredPay : null,
        $linkIDPaymentMandate,
        isset($xmlPaymentContext->allowPartial) ? $xmlPaymentContext->allowPartial : false,
        isset($xmlPaymentContext->onlyWallets) ? $xmlPaymentContext->onlyWallets : false
    );
}