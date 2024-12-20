<?php

namespace App\paygate\Components\Payment;

use App\Service\RazorpayService;

class RazorpayResponse
{
    /**
     * @var razorpayService - RazorpayService
     */
    protected $razorpayService;

    //construt method
    public function __construct()
    {
        //create razorpay instance
        $this->razorpayService = new RazorpayService();
    }

    public function getRazorpayPaymentData($requestData)
    {
        //get razorpay payment request data
        $razorpayData = $this->razorpayService->processRazorpayRequest($requestData);

        //return response data
        return $razorpayData;
    }
}
