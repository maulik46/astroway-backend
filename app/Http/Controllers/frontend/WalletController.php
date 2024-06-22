<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;

class WalletController extends Controller
{
    public function getMyWallet(Request $request)
    {
        Artisan::call('cache:clear');

        if(!authcheck())
            return redirect()->route('front.home');

        $session = new Session();
        $token = $session->get('token');

        $getUserById = Http::withoutVerifying()->post(env('APP_URL') . '/api/getUserById',[
            'userId' => authcheck()['id'],
        ])->json();



        $getProfile = Http::withoutVerifying()->post(env('APP_URL') . '/api/getProfile',[
            'token' => $token,
        ])->json();


        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();

        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();



        return view('frontend.pages.my-wallet', [
            'getUserById' => $getUserById,
            'getProfile' => $getProfile,
            'currency' => $currency,

        ]);
    }
    public function walletRecharge(Request $request)
    {
        Artisan::call('cache:clear');

        if(!authcheck())
            return redirect()->route('front.home');

        $session = new Session();
        $token = $session->get('token');


        $getRechargeAmount = Http::withoutVerifying()->post(env('APP_URL') . '/api/getRechargeAmount')->json();
        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();

        $getsystemflag = collect($getsystemflag['recordList']);
        $gstvalue = $getsystemflag->where('name', 'Gst')->first();
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

        $getamount = collect($getRechargeAmount['recordList']);
        $selectedamount = $getamount->first();

        $getProfile = Http::withoutVerifying()->post(env('APP_URL') . '/api/getProfile',[
            'token' => $token,
        ])->json();





        return view('frontend.pages.wallet-recharge', [
            'getRechargeAmount' => $getRechargeAmount,
            'gstvalue' => $gstvalue,
            'currency' => $currency,
            'selectedamount' => $selectedamount,
            'getProfile' => $getProfile,

        ]);
    }


}
