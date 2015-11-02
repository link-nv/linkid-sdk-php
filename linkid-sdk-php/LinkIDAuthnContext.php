<?php

require_once('LinkIDPaymentResponse.php');
require_once('LinkIDAttribute.php');

/*
 * LinkID Authentication context
 *
 * @author Wim Vandenhaute
 */

class LinkIDAuthnContext
{

    /**
     * @var string the linkID user ID
     */
    public $userId;
    /**
     * @var array the linkID user's attributes
     */
    public $attributes;
    /**
     * @var LinkIDPaymentResponse|null optional payment response if applies
     */
    public $paymentResponse;

    /**
     * Constructor
     */
    public function __construct($userId, $attributes, $paymentResponse)
    {

        $this->userId = $userId;
        $this->attributes = $attributes;
        $this->paymentResponse = $paymentResponse;
    }
}
