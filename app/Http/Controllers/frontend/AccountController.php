<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;

class AccountController extends Controller
{
    public function getMyAccount(Request $request)
    {

        if(!authcheck())
            return redirect()->route('front.home');

        $session = new Session();
        $token = $session->get('token');

        $getuserdetails = Http::withoutVerifying()->post(env('APP_URL') . '/api/getUserdetails', [
            'token' => $token,
        ])->json();


        return view('frontend.pages.my-account', [
            'getuserdetails'=>$getuserdetails

        ]);
    }

    public function deleteAccount(Request $req)
    {
        try {

            $userId = authcheck()['id'];
            DB::table('users')->where('id', $userId)->delete();

            return redirect()->route('front.home');
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function getMyFollowing(Request $request)
    {


        if(!authcheck())
            return redirect()->route('front.home');

            Artisan::call('cache:clear');
        $session = new Session();
        $token = $session->get('token');

        $getfollowing = Http::withoutVerifying()->post(env('APP_URL') . '/api/getFollower', [
            'token' => $token,
        ])->json();


        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

        // dd($getfollowing);


        return view('frontend.pages.my-following', [
            'getfollowing'=>$getfollowing,
            'currency' => $currency,

        ]);
    }

}
