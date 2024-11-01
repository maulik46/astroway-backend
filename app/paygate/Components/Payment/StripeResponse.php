<?php

namespace App\paygate\Components\Payment;

use App\Service\StripeService;

class StripeResponse
{
    /**
     * @var stripeData - StripeData
     */
    protected $stripeService;

    //construct method
    public function __construct()
    {
        //create stripe instance
        $this->stripeService = new StripeService();
    }

    public function retrieveStripePaymentData($sessionId)
    {
        //get stripe payment request data
        return $this->stripeService->retrieveStripeData($sessionId);
    }
}
