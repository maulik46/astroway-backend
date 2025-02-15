<?php

namespace App\paygate\Components\Payment;

use App\Service\MercadopagoService;

class MercadopagoResponse
{
    /**
     * @var mercadopagoService - mercadopagoService
     */
    protected $mercadopagoService;

    // construt method
    public function __construct()
    {
        //create mercadopago instance
        $this->mercadopagoService = new MercadopagoService();
    }

    public function getMercadopagoPaymentData($requestData)
    {
        //get bitpay payment request data
        $bitpayData = $this->mercadopagoService->prepareIpnRequestData($requestData);

        //return response data
        return $bitpayData;
    }
}
