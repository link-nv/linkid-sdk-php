<?php

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

    /**
     * LinkIDLTQRContent constructor.
     * @param $authenticationMessage
     * @param $finishedMessage
     * @param $paymentContext
     * @param $callback
     * @param $identityProfile
     * @param $sessionExpiryOverride
     * @param $theme
     * @param $mobileLandingSuccess
     * @param $mobileLandingError
     * @param $mobileLandingCancel
     * @param $pollingConfiguration
     * @param $ltqrStatusLocation
     * @param $expiryDate
     * @param $expiryDuration
     * @param $waitForUnblock
     */
    public function __construct($authenticationMessage, $finishedMessage, $paymentContext, $callback, $identityProfile, $sessionExpiryOverride, $theme, $mobileLandingSuccess, $mobileLandingError, $mobileLandingCancel, $pollingConfiguration, $ltqrStatusLocation, $expiryDate, $expiryDuration, $waitForUnblock)
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
    }


}