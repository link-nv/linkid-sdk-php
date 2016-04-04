<?php

require_once('LinkIDPaymentAmount.php');
require_once('LinkIDPaymentMandate.php');
require_once('LinkIDPaymentMenu.php');

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

    // optional payment menu return URLs (returnPaymentMenuURL)
    /* @var $paymentMenu LinkIDPaymentMenu */
    public $paymentMenu;

    // mandates
    /* @var $mandate LinkIDPaymentMandate */
    public $mandate;

    public $allowPartial; // allow partial payments via wallets, this flag does make sense if you allow normal payment methods
    public $onlyWallets; // allow only wallets for this payment

    public $paymentStatusLocation; // optional payment status location, if not specified the default location(s) in the linkID application configuration will be used

    /**
     * Constructor
     * @param LinkIDPaymentAmount $amount
     * @param $description
     * @param null $orderReference
     * @param null $profile
     * @param int $validationTime
     * @param LinkIDPaymentMandate $mandate
     * @param bool $allowPartial
     * @param bool $onlyWallets
     * @param null $paymentStatusLocation
     */
    public function __construct(LinkIDPaymentAmount $amount, $description, $orderReference = null, $profile = null, $validationTime = 5,
                                LinkIDPaymentMandate $mandate = null,
                                $allowPartial = false, $onlyWallets = false, $paymentStatusLocation = null)
    {

        $this->amount = $amount;
        $this->description = $description;

        $this->orderReference = $orderReference;
        $this->profile = $profile;
        $this->validationTime = $validationTime;

        $this->mandate = $mandate;

        $this->allowPartial = $allowPartial;
        $this->onlyWallets = $onlyWallets;

        $this->paymentStatusLocation = $paymentStatusLocation;
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
        $linkIDPaymentMandate,
        isset($xmlPaymentContext->allowPartial) ? $xmlPaymentContext->allowPartial : false,
        isset($xmlPaymentContext->onlyWallets) ? $xmlPaymentContext->onlyWallets : false,
        isset($xmlPaymentContext->paymentStatusLocation) ? $xmlPaymentContext->paymentStatusLocation : null
    );
}