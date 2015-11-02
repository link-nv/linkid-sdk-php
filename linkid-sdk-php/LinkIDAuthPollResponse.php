<?php

class LinkIDAuthPollResponse
{

    /**
     * @var int authentication state
     */
    public $authenticationState;
    /**
     * @var int payment state if applies
     */
    public $paymentState;
    /**
     * @var string|null payment menu if applies, i.e. if payment is involved and user selected continue in browser
     */
    public $paymentMenuURL;
    /**
     * @var LinkIDAuthnContext|null authentication context ( userId, attributes, payment response ) if done, else null
     */
    public $authenticationContext;

    // authentication state constants
    const AUTH_STATE_STARTED = 0;
    const AUTH_STATE_RETRIEVED = 1;
    const AUTH_STATE_AUTHENTICATED = 2;
    const AUTH_STATE_EXPIRED = 3;
    const AUTH_STATE_FAILED = 4;
    const AUTH_STATE_PAYMENT_ADD = 5;

    /**
     * Constructor
     */
    public function __construct($authenticationState, $paymentState, $paymentMenuURL, $authenticationContext)
    {

        $this->authenticationState = parseLinkIDAuthenticationState($authenticationState);
        $this->paymentState = parseLinkIDWSPaymentState($paymentState);
        $this->paymentMenuURL = $paymentMenuURL;
        $this->authenticationContext = $authenticationContext;
    }
}

function parseLinkIDAuthenticationState($authenticationState)
{

    if (null == $authenticationState) return null;

    if ($authenticationState == "linkid.state.started") {
        return LinkIDAuthPollResponse::AUTH_STATE_STARTED;
    } else if ($authenticationState == "linkid.state.retrieved") {
        return LinkIDAuthPollResponse::AUTH_STATE_RETRIEVED;
    } else if ($authenticationState == "linkid.state.authenticated") {
        return LinkIDAuthPollResponse::AUTH_STATE_AUTHENTICATED;
    } else if ($authenticationState == "linkid.state.expired") {
        return LinkIDAuthPollResponse::AUTH_STATE_EXPIRED;
    } else if ($authenticationState == "linkid.state.failed") {
        return LinkIDAuthPollResponse::AUTH_STATE_FAILED;
    } else if ($authenticationState == "linkid.state.payment.add") {
        return LinkIDAuthPollResponse::AUTH_STATE_PAYMENT_ADD;
    }

    throw new Exception("Unexpected authentication state: " . $authenticationState);

}

function parseLinkIDWSPaymentState($paymentState)
{

    if (null == $paymentState) return null;

    if ($paymentState == "STARTED") {
        return LinkIDPaymentState::STARTED;
    } else if ($paymentState == "WAITING_FOR_UPDATE") {
        return LinkIDPaymentState::WAITING_FOR_UPDATE;
    } else if ($paymentState == "FAILED") {
        return LinkIDPaymentState::FAILED;
    } else if ($paymentState == "REFUNDED") {
        return LinkIDPaymentState::REFUNDED;
    } else if ($paymentState == "REFUND_STARTED") {
        return LinkIDPaymentState::REFUND_STARTED;
    } else if ($paymentState == "AUTHORIZED") {
        return LinkIDPaymentState::PAYED;
    }

    throw new Exception("Unexpected payment state: " . $paymentState);

}
