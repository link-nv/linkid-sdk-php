<?php

require_once('LinkIDPaymentContext.php');
require_once('LinkIDCallback.php');
require_once('LinkIDLTQRPollingConfiguration.php');
require_once('LinkIDFavoritesConfiguration.php');

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 03/11/15
 * Time: 09:36
 */
class LinkIDLTQRContent
{
    public $authenticationMessage;
    public $finishedMessage;
    public $paymentContext;
    public $callback;
    public $identityProfile;
    public $sessionExpiryOverride;
    public $theme;
    public $mobileLandingSuccess;
    public $mobileLandingError;
    public $mobileLandingCancel;
    public $pollingConfiguration;
    public $ltqrStatusLocation;
    public $expiryDate;
    public $expiryDuration;
    public $waitForUnblock;
    public $favoritesConfiguration;
    public $notificationLocation;

    /**
     * LinkIDLTQRContent constructor.
     * @param string $authenticationMessage
     * @param string $finishedMessage
     * @param LinkIDPaymentContext $paymentContext
     * @param LinkIDCallback $callback
     * @param string $identityProfile
     * @param long $sessionExpiryOverride
     * @param string $theme
     * @param string $mobileLandingSuccess
     * @param string $mobileLandingError
     * @param string $mobileLandingCancel
     * @param LinkIDLTQRPollingConfiguration $pollingConfiguration
     * @param string $ltqrStatusLocation
     * @param DateTime $expiryDate
     * @param long $expiryDuration
     * @param bool $waitForUnblock
     * @param LinkIDFavoritesConfiguration $favoritesConfiguration
     * @param string $notificationLocation
     */
    public function __construct($authenticationMessage, $finishedMessage, $paymentContext, $callback, $identityProfile,
                                $sessionExpiryOverride, $theme, $mobileLandingSuccess, $mobileLandingError,
                                $mobileLandingCancel, $pollingConfiguration, $ltqrStatusLocation, $expiryDate,
                                $expiryDuration, $waitForUnblock, $favoritesConfiguration, $notificationLocation)
    {
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
        $this->pollingConfiguration = $pollingConfiguration;
        $this->ltqrStatusLocation = $ltqrStatusLocation;
        $this->expiryDate = $expiryDate;
        $this->expiryDuration = $expiryDuration;
        $this->waitForUnblock = $waitForUnblock;
        $this->favoritesConfiguration = $favoritesConfiguration;
        $this->notificationLocation = $notificationLocation;
    }


}