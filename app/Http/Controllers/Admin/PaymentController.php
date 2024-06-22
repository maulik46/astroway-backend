<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\UserModel\UserWallet;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Session\Session;

// for payment Response
use App\paygate\Components\Payment\CoingateResponse;
use App\paygate\Components\Payment\PaytmResponse;
use App\paygate\Components\Payment\PaystackResponse;
use App\paygate\Components\Payment\StripeResponse;
use App\paygate\Components\Payment\RazorpayResponse;
use App\paygate\Components\Payment\InstamojoResponse;
use App\paygate\Components\Payment\IyzicoResponse;
use App\paygate\Components\Payment\PaypalIpnResponse;
use App\paygate\Components\Payment\MercadopagoResponse;
use App\paygate\Components\Payment\PayUmoneyResponse;
use App\paygate\Components\Payment\MollieResponse;
use App\paygate\Components\Payment\RavepayResponse;
use App\paygate\Components\Payment\PagseguroResponse;
use Carbon\Carbon;
use Exception;

class PaymentController extends Controller
{

   public function payment(Request $req)
   {


      $user = Payment::where('payment.id',$req->payid)
      ->join('users','users.id','payment.userId')
      ->select('payment.*','users.id as userId','users.name','users.email','users.contactNo')
      ->orderBy('payment.id','DESC')
      ->where('payment.paymentStatus','pending')
      ->first();

      // Session::put('payid',$req->payid);
      // Using the Session facade to set a session variable
      $session = new Session();
      $session->set('pay_id',$req->payid);

      if($user){

         return view('payment.payment',compact('user'));
      }else{
         dd("Invalid Payment Id");
      }








   }

   public function paymentprocess()
   {

    return view('payment.payment-process');
   }


   public function paymentsresponse(Request $request)
   {

      $requestData = $_REQUEST;
      $payload = @file_get_contents('php://input');


      // Get Config Data
      $configData = configItem();

      // Get Request Data when payment success or failed
      // $requestData = $_REQUEST;
      // $payload = @file_get_contents('php://input');


      if ($requestData['paymentOption'] == 'coingate') {
         if ($requestData['status'] == 'success') {
            // Create payment success response data.
            $paymentResponseData = [
                  'status'   => true
            ];
            // Send data to payment response.
            return redirect()->to($this->payment_Response($paymentResponseData));
         } else if ($requestData['status'] == 'cancel') {
            // Create payment failed response data.
            $paymentResponseData = [
                  'status'   => false
            ];
            // Send data to payment response function
            return redirect()->to($this->payment_Response($paymentResponseData));
         }
      } else if ($requestData['paymentOption'] == 'coingate' && !empty($payload)) {

         if (isset($payload['status']) && $payload['status'] == 'paid') {
            // Then create a data for success paypal data
            $paymentResponseData = [
                  'status'    => true,
                  'rawData'   => (array) $payload,
                  'data'     => $this->preparePaymentData($payload['order_id'], $payload['price_amount'], $payload['id'], 'coingate')
            ];
            // Send data to payment response function for further process
            return redirect()->to($this->payment_Response($paymentResponseData));

         }else{
            $paymentResponseData = [
                  'status'    => $payload['status'],
                  'rawData'   => (array) $payload,
                  'data'     => $this->preparePaymentData($payload['order_id'], $payload['price_amount'], $payload['id'], 'coingate')
            ];
            // Send data to payment response function for further process
            return redirect()->to($this->payment_Response($paymentResponseData));
         }
      }



      // Check payment Method is paytm
      if ($requestData['paymentOption'] == 'paytm') {
         // Get Payment Response instance
         $paytmResponse  = new PaytmResponse();

         // Fetch payment data using payment response instance
         $paytmData = $paytmResponse->getPaytmPaymentData($requestData);

         // Check if payment status is success
         if ($paytmData['STATUS'] == 'TXN_SUCCESS') {
            // Create payment success response data.
            $paymentResponseData = [
                  'status'   => true,
                  'rawData'  => $paytmData,
                  'data'     => $this->preparePaymentData($paytmData['ORDERID'], $paytmData['TXNAMOUNT'], $paytmData['TXNID'], 'paytm')
            ];
            // Send data to payment response.
            return redirect()->to($this->payment_Response($paymentResponseData));
         } else {
            // Create payment failed response data.
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'  => $paytmData,
                  'data'     => $this->preparePaymentData($paytmData['ORDERID'], $paytmData['TXNAMOUNT'], $paytmData['TXNID'], 'paytm')
            ];
            // Send data to payment response function
            return redirect()->to($this->payment_Response($paymentResponseData));
         }
         // Check payment method is instamojo
      } elseif ($requestData['paymentOption'] == 'instamojo') {
         // Check if payment successfully procced
         if ($requestData['payment_status'] == "Credit") {
            // Get Instance of instamojo response service
            $instamojoResponse  = new InstamojoResponse();

            // fetch payment data from instamojo response instance
            $instamojoData = $instamojoResponse->getInstamojoPaymentData($requestData);

            // Prepare data for payment response
            $paymentResponseData = [
                  'status'   => true,
                  'rawData'  => $instamojoData,
                  'data'     => $this->preparePaymentData($requestData['orderId'], $instamojoData['amount'], $instamojoData['payment_id'], 'instamojo')
            ];
            // Send data to payment response
            return redirect()->to($this->payment_Response($paymentResponseData));
            // Check if payment failed then send failed response
         } else {
            // Prepare data for failed response data
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'  => $requestData,
                  'data'     => $this->preparePaymentData($requestData['orderId'], $instamojoData['amount'], null, 'instamojo')
            ];
            // Send data to payment response function
            return redirect()->to($this->payment_Response($paymentResponseData));
         }

         // Check if payment method is iyzico.
      } elseif ($requestData['paymentOption'] == 'iyzico') {
         // Check if payment status is success for iyzico.
         if ($requestData['status'] == 'success') {
            // Get iyzico response.
            $iyzicoResponse  = new IyzicoResponse();

            // fetch payment data using iyzico response instance.
            $iyzicoData = $iyzicoResponse->getIyzicoPaymentData($requestData);
            $rawResult = json_decode($iyzicoData->getRawResult(), true);

            // Check if iyzico payment data is success
            // Then create a array for success data
            if ($iyzicoData->getStatus() == 'success') {
                  $paymentResponseData = [
                     'status'   => true,
                     'rawData'  => (array) $iyzicoData,
                     'data'     => $this->preparePaymentData($requestData['orderId'], $rawResult['price'], $rawResult['conversationId'], 'iyzico')
                  ];
                  // Send data to payment response
                  return redirect()->to($this->payment_Response($paymentResponseData));
                  // If payment failed then create data for failed
            } else {
                  // Prepare failed payment data
                  $paymentResponseData = [
                     'status'   => false,
                     'rawData'  => (array) $iyzicoData,
                     'data'     => $this->preparePaymentData($requestData['orderId'], $rawResult['price'], $rawResult['conversationId'], 'iyzico')
                  ];
                  // Send data to payment response
                  return redirect()->to($this->payment_Response($paymentResponseData));
            }
            // Check before 3d payment process payment failed
         } else {
            // Prepare failed payment data
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'  => $requestData,
                  'data'     => $this->preparePaymentData($requestData['orderId'], $rawResult['price'], null, 'iyzico')
            ];
            // Send data to process response
            return redirect()->to($this->payment_Response($paymentResponseData));
         }

         // Check Paypal payment process
      } elseif ($requestData['paymentOption'] == 'paypal') {
         // Get instance of paypal
         $paypalIpnResponse  = new PaypalIpnResponse();

         // fetch paypal payment data
         $paypalIpnData = $paypalIpnResponse->getPaypalPaymentData();
         $rawData = json_decode($paypalIpnData, true);

         // Note : IPN and redirects will come here
         // Check if payment status exist and it is success
         if (isset($requestData['payment_status']) and $requestData['payment_status'] == "Completed") {
            // Then create a data for success paypal data
            $paymentResponseData = [
                  'status'    => true,
                  'rawData'   => (array) $paypalIpnData,
                  'data'     => $this->preparePaymentData($rawData['invoice'], $rawData['payment_gross'], $rawData['txn_id'], 'paypal')
            ];
            // Send data to payment response function for further process
            return redirect()->to($this->payment_Response($paymentResponseData));
            // Check if payment not successful
         } else {
            // Prepare payment failed data
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'  => [],
                  'data'     => $this->preparePaymentData($rawData['invoice'], $rawData['payment_gross'], null, 'paypal')
            ];
            // Send data to payment response function for further process
            return redirect()->to($this->payment_Response($paymentResponseData));
         }

         // Check Paystack payment process
      } elseif ($requestData['paymentOption'] == 'paystack') {
         $requestData = json_decode($requestData['response'], true);

         // Check if status key exists and payment is successfully completed
         if (isset($requestData['status']) and $requestData['status'] == "success") {

            // Create data for payment success
            $paymentResponseData = [
                  'status'   => true,
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($requestData['data']['reference'], $requestData['data']['amount'], $requestData['data']['reference'], 'paystack')
            ];
            // Send data to payment response for further process
            return redirect()->to($this->payment_Response($paymentResponseData));

            // If paystack payment is failed
         } else {

            // Prepare data for failed payment
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($requestData['data']['reference'], $requestData['data']['amount'], $requestData['data']['reference'], 'paystack')
            ];
            // Send data to payment response to further process
            return redirect()->to($this->payment_Response($paymentResponseData));
         }

         // Check Stripe payment process
      } elseif ($requestData['paymentOption'] == 'stripe') {
         $stripeResponse = new StripeResponse();

         $stripeData = $stripeResponse->retrieveStripePaymentData($requestData['stripe_session_id']);

         // Check if payment charge status key exist in stripe data and it success
         if (isset($stripeData['status']) and $stripeData['status'] == "succeeded") {
            // Prepare data for success
            $paymentResponseData = [
                  'status'   => true,
                  'rawData'   => $stripeData,
                  'data'     => $this->preparePaymentData($requestData['orderId'], $stripeData->amount, $stripeData->charges->data[0]['balance_transaction'], 'stripe')
            ];

            // Check if stripe data is failed
         } else {
            // Prepare failed payment data
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'   => $stripeData,
                  'data'     => $this->preparePaymentData($requestData['orderId'], $stripeData->amount, null, 'stripe')
            ];
         }

         // Send data to payment response for further process
         return redirect()->to($this->payment_Response($paymentResponseData));

         // Check Razorpay payment process
      } elseif ($requestData['paymentOption'] == 'razorpay') {
         $orderId = $requestData['orderId'];

         $requestData = json_decode($requestData['response'], true);

         // Check if razorpay status exist and status is success
         if (isset($requestData['status']) and $requestData['status'] == 'captured') {
            // prepare payment data
            $paymentResponseData = [
                  'status'   => true,
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($orderId, $requestData['amount'], $requestData['id'], 'razorpay')
            ];
            // send data to payment response
            return redirect()->to($this->payment_Response($paymentResponseData));
            // razorpay status is failed
         } else {
            // prepare payment data for failed payment
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($orderId, $requestData['amount'], $requestData['id'], 'razorpay')
            ];
            // send data to payment response
            return redirect()->to($this->payment_Response($paymentResponseData));
         }
      } elseif ($requestData['paymentOption'] == 'authorize-net') {
         $orderId = $requestData['order_id'];

         $requestData = json_decode($requestData['response'], true);

         // Check if razorpay status exist and status is success
         if (isset($requestData['status']) and $requestData['status'] == 'success') {
            // prepare payment data
            $paymentResponseData = [
                  'status'   => true,
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($orderId, $requestData['amount'], $requestData['transaction_id'], 'authorize-net')
            ];
            // send data to payment response
            return redirect()->to($this->payment_Response($paymentResponseData));
            // razorpay status is failed
         } else {
            // prepare payment data for failed payment
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($orderId, $requestData['amount'], $requestData['transaction_id'], 'authorize-net')
            ];
            // send data to payment response
            return redirect()->to($this->payment_Response($paymentResponseData));
         }
      } elseif ($requestData['paymentOption'] == 'mercadopago') {
         if ($requestData['collection_status'] == 'approved') {
            $paymentResponseData = [
                  'status'   => true,
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($requestData['order_id'], $requestData['amount'], $requestData['collection_id'], 'mercadopago')
            ];
         } elseif ($requestData['collection_status'] == 'pending') {
            $paymentResponseData = [
                  'status'   => 'pending',
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($requestData['order_id'], $requestData['amount'], $requestData['collection_id'], 'mercadopago')
            ];
         } else {
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($requestData['order_id'], $requestData['amount'], $requestData['collection_id'], 'mercadopago')
            ];
         }
         return redirect()->to($this->payment_Response($paymentResponseData));
      } elseif ($requestData['paymentOption'] == 'mercadopago-ipn') {
         $mercadopagoResponse = new MercadopagoResponse();
         $mercadopagoIpnData = $mercadopagoResponse->getMercadopagoPaymentData($requestData);

         // Ipn data recieved here are as following
         //$mercadopagoIpnData['status'] = 'total_paid or not_paid';
         //$mercadopagoIpnData['message'] = 'Message';
         //$mercadopagoIpnData['raw_data'] = 'Raw Ipn Data';
      } elseif ($requestData['paymentOption'] == 'payumoney') {
         $payUmoneyResponse = new PayUmoneyResponse();
         $payUmoneyResponseData = $payUmoneyResponse->getPayUmoneyPaymentResponse($requestData);
         if ($payUmoneyResponseData['status'] == 'success') {
            $paymentResponseData = [
                  'status'    => true,
                  'order_id'  => $payUmoneyResponseData['raw_Data'],
                  'rawData'   => $payUmoneyResponseData['raw_Data'],
                  'data'      => $this->preparePaymentData($payUmoneyResponseData['order_id'], $payUmoneyResponseData['amount'], $payUmoneyResponseData['txn_id'], 'payumoney')
            ];
         } elseif ($payUmoneyResponseData['status'] == 'failed') {
            $paymentResponseData = [
                  'status'    => false,
                  'order_id'  => '',
                  'rawData'   => $payUmoneyResponseData['raw_Data'],
                  'data'      => $this->preparePaymentData($payUmoneyResponseData['order_id'], $payUmoneyResponseData['amount'], $payUmoneyResponseData['txn_id'], 'payumoney')
            ];
         }

         return redirect()->to($this->payment_Response($paymentResponseData));
      } elseif ($requestData['paymentOption'] == 'mollie') {
         $paymentResponseData = [
            'status'    => true,
            'order_id'  => $requestData['order_id'],
            'rawData'   => $requestData,
            'data'      => $this->preparePaymentData($requestData['order_id'], $requestData['amount'], null, 'mollie')
         ];

         return redirect()->to($this->payment_Response($paymentResponseData));
      } elseif ($requestData['paymentOption'] == 'mollie-webhook') {
         $mollieResponse = new MollieResponse();
         $webhookData = $mollieResponse->retrieveMollieWebhookData($requestData);

         // mollie webhook data received here with following option
         // $webhookData['status']; - payment status (paid|open|pending|failed|expired|canceled|refund|chargeback)
         // $webhookData['raw_data']; - webhook all raw data
         // $webhookData['message']; - if payment failed then message

         // Check Ravepay payment process
      } elseif ($requestData['paymentOption'] == 'ravepay') {
         $requestData = json_decode($requestData['response'], true);

         //Check if status key exists and payment is successfully completed
         if (isset($requestData['body']['status']) and $requestData['body']['status'] == "success") {
            // Create data for payment success
            $paymentResponseData = [
                  'status'   => true,
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($requestData['body']['data']['txref'], $requestData['body']['data']['amount'], $requestData['body']['data']['txid'], 'ravepay')
            ];
            // Send data to payment response for further process
            return redirect()->to($this->payment_Response($paymentResponseData));
            // If ravepay payment is failed
         } else {
            // Prepare data for failed payment
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'   => $requestData,
                  'data'     => $this->preparePaymentData($requestData['body']['data']['txref'], $requestData['body']['data']['amount'], $requestData['body']['data']['txid'], 'ravepay')
            ];
            // Send data to payment response to further process
            return redirect()->to($this->payment_Response($paymentResponseData));
         }

         // Check Pagseguro payment process
      } elseif ($requestData['paymentOption'] == 'pagseguro') {
         // Get Payment Response instance
         $pagseguroResponse  = new PagseguroResponse();

         // Fetch payment data using payment response instance
         $pagseguroData = $pagseguroResponse->fetchTransactionByRefrenceId($requestData['reference_id']);

         //handling errors
         if (isset($pagseguroData['status']) and $pagseguroData['status'] == 'error') {
            //throw exception when generate errors
            throw new Exception($pagseguroData['message']);
         }

         //transaction status
         //1 - Awaiting payment, 2 - In analysis, 3 - Pay, 4 - Available, 5 - In dispute,
         //6 - Returned, 7 - Canceled
         $txnStatus = $pagseguroData['responseData']->getTransactions()[0]->getStatus();

         //collect transaction code
         $transactionCode = $pagseguroData['responseData']->getTransactions()[0]->getCode();

         // Fetch transaction data by transaction code
         $transactionData = $pagseguroResponse->fetchTransactionByTxnCode($transactionCode);

         // Check if payment status is success
         if ($transactionData['status'] == 'success' and $txnStatus == 3 and $transactionData['responseData']->getReference() == $requestData['reference_id']) {
            // Create payment success response data.
            $paymentResponseData = [
                  'status'   => true,
                  'rawData'  => $transactionData['responseData'],
                  'data'     => $this->preparePaymentData(
                     $transactionData['responseData']->getReference(),
                     $transactionData['responseData']->getGrossAmount(),
                     $transactionData['responseData']->getCode(),
                     'pagseguro'
                  )
            ];
            // Send data to payment response.
            return redirect()->to($this->payment_Response($paymentResponseData));
         } else {
            // Create payment failed response data.
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'  => $paytmData,
                  'data'     => $this->preparePaymentData(
                     $transactionData['responseData']->getReference(),
                     $transactionData['responseData']->getGrossAmount(),
                     $transactionData['responseData']->getCode(),
                     'pagseguro'
                  )
            ];
            // Send data to payment response function
            return redirect()->to($this->payment_Response($paymentResponseData));
         }
      } else if ($requestData['paymentOption'] == 'paypal-checkout') {

         $rawData = json_decode($requestData['response'], true);
         $amount = $rawData['purchase_units'][0]['payments']['captures'][0]['amount']['value'];

         // Check if payment status exist and it is success
         if (isset($rawData['status']) and $rawData['status'] == "COMPLETED") {
            // Then create a data for success paypal data
            $paymentResponseData = [
                  'status'    => true,
                  'rawData'   => (array) $requestData,
                  'data'     => $this->preparePaymentData($requestData['orderId'], $amount, $rawData['id'], 'paypal-checkout')
            ];
            // Send data to payment response function for further process
            return redirect()->to($this->payment_Response($paymentResponseData));
            // Check if payment not successful
         } else {
            // Prepare payment failed data
            $paymentResponseData = [
                  'status'   => false,
                  'rawData'  => [],
                  'data'     => $this->preparePaymentData($requestData['orderId'], $amount, null, 'paypal-checkout')
            ];
            // Send data to payment response function for further process
            return redirect()->to($this->payment_Response($paymentResponseData));
            // return redirect()->to($this->payment_Response($paymentResponseData));
         }
      }


   //  return view('payment.payment-response',compact('requestData','payload'));
   }


   /*
 * This payment used for get Success / Failed data for any payment method.
 *
 * @param array $paymentResponseData - contains : status and rawData
 *
 */


public function payment_Response($paymentResponseData)
{
   // $pay_id=Cache::get('payid');
   $session = new Session();
   $pay_id = $session->get('pay_id');

    // payment status success
    if ($paymentResponseData['status'] === true) {
        // Show payment success page or do whatever you want, like send email, notify to user etc

      $payment = Payment::where('payment.id',$pay_id)
      ->join('users','users.id','payment.userId')
      ->select('payment.*','users.id as userId','users.name','users.email','users.contactNo')
      ->where('payment.paymentStatus','pending')
      ->first();

      $gst_percent= DB::table('systemflag')->where('name','Gst')->first();
      $original_amount = $payment->amount / (1 + ($gst_percent->value / 100));
      $gst_amount=$original_amount*($gst_percent->value/100);



      if ($payment) {
         $payment->update([
            'paymentMode' => $paymentResponseData['data']['payment_gateway'],
            'paymentReference' =>  $paymentResponseData['data']['payment_reference_id'],
            'paymentStatus' => 'success',
            'orderId' => $paymentResponseData['data']['order_id'],
         ]);

         $userWallet = [];

         $userWallet = UserWallet::query()
             ->where('userId', '=', $payment->userId)
             ->get();
         if ($userWallet && count($userWallet) > 0) {
             $userWallet[0]->amount = $userWallet[0]->amount + $payment->amount + $payment->cashback_amount - $gst_amount;
             $userWallet[0]->update();
         } else {
             $wallet = UserWallet::create([
                 'userId' => $payment->userId,
                 'amount' => $payment->amount + $payment->cashback_amount - $gst_amount,
                 'createdBy' => $payment->userId,
                 'modifiedBy' => $payment->userId,
             ]);
         }

         $transaction = array(
            'userId' => $payment->userId,
            'amount' => $payment->cashback_amount,
            'isCredit' => true,
            "transactionType" => 'Cashback',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            );
            if($payment->cashback_amount!=null)
               DB::table('wallettransaction')->insert($transaction);

         $session = new Session();
         $session->remove('pay_id');
      }
        return getAppUrl('payment-success');

    } elseif ($paymentResponseData['status'] === 'pending') {
        // Show payment success page or do whatever you want, like send email, notify to user etc
        return getAppUrl('payment-pending');
    } else {
        // Show payment error page or do whatever you want, like send email, notify to user etc
        $payment = Payment::where('payment.id',$pay_id)
        ->join('users','users.id','payment.userId')
        ->select('payment.*','users.id as userId','users.name','users.email','users.contactNo')
        ->where('payment.paymentStatus','pending')
        ->first();

        if ($payment) {
           $payment->update([
              'paymentMode' => $paymentResponseData['data']['payment_gateway'],
              'paymentReference' =>  $paymentResponseData['data']['payment_reference_id'],
              'paymentStatus' => 'failed',
              'orderId' => $paymentResponseData['data']['order_id'],
           ]);

           }
           $session = new Session();
         $session->remove('pay_id');
        return getAppUrl('payment-failed');
    }
}

/*
* Prepare Payment Data.
*
* @param array $paymentData
*
*/
   public function preparePaymentData($orderId, $amount, $txnId, $paymentGateway)
   {
      return [
         'order_id'              => $orderId,
         'amount'                => $amount,
         'payment_reference_id'  => $txnId,
         'payment_gateway'        => $paymentGateway
      ];
   }


   public function paymentpending()
   {
    return view('payment.payment-pending');
   }

   public function paymentfailed()
   {
    return view('payment.payment-failed');
   }

   public function paymentsuccess(Request $req)
   {


         return view('payment.payment-success');


   }
}


