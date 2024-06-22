<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Horoscope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HoroscopeController extends Controller
{

    public function horoScope(Request $request)
    {
        Artisan::call('cache:clear');
        $gethoroscopesign = Http::withoutVerifying()->post(env('APP_URL') . '/api/getHororscopeSign')->json();


        return view('frontend.pages.horoscopesign', [
            'gethoroscopesign' => $gethoroscopesign,

        ]);
    }

    public function dailyHoroscope(Request $request)
    {

        Artisan::call('cache:clear');
        $gethoroscopesign = Http::withoutVerifying()->post(env('APP_URL') . '/api/getHororscopeSign')->json();

        $horoscope = Http::withoutVerifying()->post(env('APP_URL') . '/api/getDailyHoroscope', [
            'horoscopeSignId' => $request->horoscopeSignId,
        ])->json();


        $signRcd = DB::table('hororscope_signs')->where('id', $request->horoscopeSignId)->get();




        return view('frontend.pages.dailyhoroscope', [
            'horoscope' => $horoscope,
            'gethoroscopesign' => $gethoroscopesign,
            'signRcd' => $signRcd,

        ]);
    }
}
