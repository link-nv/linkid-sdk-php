<?php

require_once('LinkIDPaymentContext.php');
require_once('LinkIDCallback.php');

class LinkIDAuthenticationContext
{

    public $applicationName;
    public $applicationFriendlyName;
    public $language;
    //
    public $authenticationMessage;
    public $finishedMessage;
    //
    public $paymentContext;
    public $callback;
    //
    public $identityProfile;
    public $sessionExpiryOverride;
    public $theme;
    //
    public $mobileLandingSuccess;
    public $mobileLandingError;
    public $mobileLandingCancel;
    //
    public $attributeSuggestions;

    /**
     * @param string $applicationName
     * @param string $applicationFriendlyName
     * @param string $language
     * @param string $authenticationMessage
     * @param string $finishedMessage
     * @param LinkIDPaymentContext $paymentContext
     * @param LinkIDCallback $callback
     * @param string $identityProfile
     * @param int $sessionExpiryOverride
     * @param string $theme
     * @param string $mobileLandingSuccess
     * @param string $mobileLandingError
     * @param string $mobileLandingCancel
     * @param array $attributeSuggestions
     */
    public function __construct($applicationName, $applicationFriendlyName = null, $language = "en",
                                $authenticationMessage = null, $finishedMessage = null, $paymentContext = null, $callback = null,
                                $identityProfile = null, $sessionExpiryOverride = -1, $theme = null,
                                $mobileLandingSuccess = null, $mobileLandingError = null, $mobileLandingCancel = null,
                                $attributeSuggestions = null)
    {
        $this->applicationName = $applicationName;
        $this->applicationFriendlyName = $applicationFriendlyName;
        $this->language = $language;
        $this->authenticationMessage = $authenticationMessage;
        $this->finishedMessage = $finishedMessage;
        $this->paymentContext = $paymentContext;
        $this->callback = $callback;
        $this->identityProfile = $identityProfile;
        $this->sessionExpiryOverride = $sessionExpiryOverride;
        $this->theme = $theme;
        $this->mobileLandingSuccess = $mobileLandingSuccess;
        $this->mobileLandingError = $mobileLandingError;
        $this->mobileLandingCancel = $mobileLandingCancel;
        $this->attributeSuggestions = $attributeSuggestions;
    }


}