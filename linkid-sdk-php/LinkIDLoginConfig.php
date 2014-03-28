<?php

/*
 * LinkID Login Configuration Utility class
 *
 * @author Wim Vandenhaute
 */

class LinkIDLoginConfig {

    public $forceRegistration;
    public $targetURI;
    public $linkIDLandingPage;

    /**
     * Constructor
     */
    public function __construct($linkIDHost) {

        $this->forceRegistration = null != $_REQUEST["mobileForceReg"];
        $this->targetURI = $_REQUEST["return_uri"];

        if ($this->forceRegistration) {
            $this->linkIDLandingPage = "https://" . $linkIDHost . "/linkid-mobile/reg-min";
        } else {
            $this->linkIDLandingPage = "https://" . $linkIDHost . "/linkid-mobile/auth-min";
        }
    }

    public function generateRedirectURL($sessionId) {
        return $this->linkIDLandingPage . "?hawsId=" . $sessionId;
    }

}
?>