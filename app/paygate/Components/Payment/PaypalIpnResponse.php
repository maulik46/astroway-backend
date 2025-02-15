<?php

namespace App\paygate\Components\Payment;

use App\Service\PaypalService;

class PaypalIpnResponse
{
    /**
     * @var paypalService - RazorpayService
     */
    protected $paypalService;

    //construt method
    public function __construct()
    {
        //create paypal instance
        $this->paypalService = new PaypalService();
    }

    public function getPaypalPaymentData()
    {
        //get paypal payment request data
        $paypalData = $this->paypalService->processIpnRequest();

        //return response data
        return $paypalData;
    }
}
