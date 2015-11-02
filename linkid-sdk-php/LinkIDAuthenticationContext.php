<?php

require_once('LinkIDPaymentContext.php');
require_once('LinkIDCallback.php');

class LinkIDAuthenticationContext
{

    /**
     * @var string the technical application name a linkID admin gave you
     */
    public $applicationName;
    /**
     * @var null|string optional friendly name override to be shown in the client
     */
    public $applicationFriendlyName;
    /**
     * @var string optional language of the authentication/payment session
     */
    public $language;
    /**
     * @var null|string optional client authentication message
     */
    public $authenticationMessage;
    /**
     * @var null|string optional client message shown when the authentication has finished
     */
    public $finishedMessage;
    /**
     * @var LinkIDPaymentContext|null optional payment context
     */
    public $paymentContext;
    /**
     * @var LinkIDCallback|null optional callback
     */
    public $callback;
    //
    /**
     * @var null|string optional identity profile override
     */
    public $identityProfile;
    /**
     * @var int optional session expiry override ( default is 15 mins )
     */
    public $sessionExpiryOverride;
    /**
     * @var null|string optional theme override
     */
    public $theme;
    /**
     * @var null|string optional mobile landing page if successful
     */
    public $mobileLandingSuccess;
    /**
     * @var null|string optional mobile landing page if error
     */
    public $mobileLandingError;
    /**
     * @var null|string optional mobile landing page if canceled
     */
    public $mobileLandingCancel;
    /**
     * @var array|null optional map of attribute suggestions
     */
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