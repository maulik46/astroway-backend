<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Kundali;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;


class KundaliController extends Controller
{
    public function getPanchang(Request $request)
    {
        Artisan::call('cache:clear');
        $panchangDate=$request->panchangDate?:Carbon::now();


        $getPanchang = Http::withoutVerifying()->post(env('APP_URL') . '/api/get/panchang', [
            'panchangDate' => $panchangDate,
        ])->json();
        // dd($getPanchang);

        return view('frontend.pages.panchang', [
            'getPanchang' => $getPanchang,

        ]);
    }

    public function getkundali(Request $request)
    {
        Artisan::call('cache:clear');

        $session = new Session();
        $token = $session->get('token');


        $getkundaliprice = Http::withoutVerifying()->post(env('APP_URL') . '/api/pdf/price', [
            'token' => $token,
        ])->json();

        $getkundali = Http::withoutVerifying()->post(env('APP_URL') . '/api/getkundali', [
            'token' => $token,
        ])->json();

        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();
            // dd( $getkundaliprice);

        return view('frontend.pages.kundali', [
            'getkundali' => $getkundali,
            'getkundaliprice' => $getkundaliprice,
            'currency' => $currency,

        ]);
    }

    public function kundaliMatch(Request $request)
    {

        return view('frontend.pages.kundali-matching', [


        ]);
    }

    public function kundaliMatchReport(Request $request)
    {
        $KundaliMatching = Http::withoutVerifying()->post(env('APP_URL') . '/api/KundaliMatching/report', [
            'male_kundli_id' => $request->male_kundli_id,
            'female_kundli_id' => $request->female_kundli_id,
        ])->json();

        $kundalimale = Kundali::where('id', $request->male_kundli_id)->first();
        $kundalifemale = Kundali::where('id', $request->female_kundli_id)->first();
        // dd($kundalimale);

        return view('frontend.pages.kundali-match-report', [
            'KundaliMatching' => $KundaliMatching,
            'kundalimale' => $kundalimale,
            'kundalifemale' => $kundalifemale,

        ]);
    }


}
