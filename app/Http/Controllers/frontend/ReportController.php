<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;

class ReportController extends Controller
{
    public function reportList(Request $request)
    {

        $userId='';
		if(authcheck()){
		$userId=authcheck()['id'];
		}

        $session = new Session();
        $token = $session->get('token');

        Artisan::call('cache:clear');
        $sortBy = $request->sortBy;
        $astrologerCategoryId=(int)$request->astrologerCategoryId;
        $searchTerm = $request->input('s');



        $getAstrologer = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstrologer', ['sortBy' => $sortBy,'astrologerCategoryId'=>$astrologerCategoryId,'userId'=>$userId,'s' => $searchTerm])->json();
        // dd( $getAstrologer);
        $getAstrologerCategory = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstrologerCategory')->json();
        $getReportType = Http::withoutVerifying()->post(env('APP_URL') . '/api/getReportType')->json();

        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

        return view('frontend.pages.astrologer-report-list', [
            'getAstrologer' => $getAstrologer,
            'getAstrologerCategory' => $getAstrologerCategory,
            'sortBy' => $sortBy,
            'astrologerCategoryId' => $astrologerCategoryId,
            'searchTerm' => $searchTerm,
            'currency' => $currency,
            'getReportType' => $getReportType,
        ]);

    }


    public function getMyReport(Request $request)
    {
        Artisan::call('cache:clear');

        if(!authcheck())
            return redirect()->route('front.home');

        $session = new Session();
        $token = $session->get('token');

        $getUserById = Http::withoutVerifying()->post(env('APP_URL') . '/api/getUserById',[
            'userId' => authcheck()['id'],
        ])->json();

            // dd($getUserById);

        $getProfile = Http::withoutVerifying()->post(env('APP_URL') . '/api/getProfile',[
            'token' => $token,
        ])->json();


        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();

        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();



        return view('frontend.pages.my-report', [
            'getUserById' => $getUserById,
            'getProfile' => $getProfile,
            'currency' => $currency,

        ]);
    }

}
