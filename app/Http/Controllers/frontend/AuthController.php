<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthController extends Controller
{
    public function logout(Request $request)
    {

        if(!authcheck())
        return redirect()->route('front.home');


        $session = new Session();
        $token = $session->get('token');
        $logout = Http::withoutVerifying()->post(env('APP_URL') . '/api/logout', [
            'token' => $token,
        ])->json();

        $session = new Session();
        $session->remove('token');

        return response()->json([
            "message" => "Logout User Successfully",
        ], 200);
    }

    public function userLogin(Request $request)
    {

       return view('frontend.pages.user-login');
    }


    public function validateOTLtoken($token)
    {
        $client_id = DB::table('systemflag')->where('name', 'otplessClientId')->select('value')->first();
        $secret_key = DB::table('systemflag')->where('name', 'otplessSecretKey')->select('value')->first();
         $curl = curl_init();

         curl_setopt_array($curl, array(
         CURLOPT_URL => 'https://auth.otpless.app/auth/userInfo',
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => 'token='.$token.'&client_id='.$client_id->value.'&client_secret='.$secret_key->value.'',
         CURLOPT_HTTPHEADER => array(
             'Content-Type: application/x-www-form-urlencoded'
         ),
         ));

         $response = curl_exec($curl);

         curl_close($curl);
         return json_decode($response,true);
    }


    public function verifyOTL(Request $request)
    {

       $verifiedData=$this->validateOTLtoken($request->otl_token);

       if($verifiedData['authentication_details']['phone']){
        $login = Http::withoutVerifying()->post(env('APP_URL') . '/api/loginAppUser', [
            'contactNo' => $verifiedData['authentication_details']['phone']['phone_number'],
            'countryCode' => $verifiedData['authentication_details']['phone']['country_code'],
        ])->json();
        $session = new Session();
        $session->set('token',$login['token']);
        return redirect()->back();
       }elseif($verifiedData['authentication_details']['email']){
            $login = Http::withoutVerifying()->post(env('APP_URL') . '/api/loginAppUser', [
                'email' => $verifiedData['authentication_details']['email']['email'],
            ])->json();
            $session = new Session();
            $session->set('token',$login['token']);
            return redirect()->back();
       }

    }


}
