<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;

class AstrologerCallController extends Controller
{
       public function talkList(Request $request)
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

        $getIntakeForm = Http::withoutVerifying()->post(env('APP_URL') . '/api/chatRequest/getIntakeForm', [
            'token' => $token,
        ])->json();

        $isSessionavailable = Http::withoutVerifying()->post(env('APP_URL') . '/api/checkFreeSessionAvailable', [
            'token' => $token,
        ])->json();

        $isFreeChat = DB::table('systemflag')->where('name', 'FirstFreeChat')->select('value')->first();
        $isFreeAvailable=true;
        if ($isFreeChat->value == 1) {
            if ($userId) {
                $isChatRequest = DB::table('chatrequest')->where('userId', $userId)->where('chatStatus', '=', 'Completed')->first();
                $isCallRequest = DB::table('callrequest')->where('userId', $userId)->where('callStatus', '=', 'Completed')->first();
                if ($isChatRequest || $isCallRequest) {
                    $isFreeAvailable = false;
                } else {
                    $isFreeAvailable = true;
                }
            }
        } else {
            $isFreeAvailable = false;
        }

        $getAstrologer = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstrologer', ['sortBy' => $sortBy,'astrologerCategoryId'=>$astrologerCategoryId,'userId'=>$userId,'s' => $searchTerm])->json();
        // dd( $getAstrologer);
        $getAstrologerCategory = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstrologerCategory')->json();

        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

        return view('frontend.pages.astrologer-call-list', [
            'getAstrologer' => $getAstrologer,
            'getAstrologerCategory' => $getAstrologerCategory,
            'sortBy' => $sortBy,
            'astrologerCategoryId' => $astrologerCategoryId,
            'getIntakeForm' => $getIntakeForm,
            'isSessionavailable' => $isSessionavailable,
            'isFreeAvailable' => $isFreeAvailable,
            'searchTerm' => $searchTerm,
            'currency' => $currency,
        ]);

    }


    public function call(Request $request)
    {

        if(!authcheck())
            return redirect()->route('front.home');

        $callrequest=DB::table('callrequest')->where('userId',authcheck()['id'])->where('id',$request->callId)->first();
        // dd($callrequest);

        if($callrequest->callStatus!='Confirmed')
            return redirect()->route('front.home');

            $session = new Session();
            $token = $session->get('token');

        Artisan::call('cache:clear');


        $getUserNotification = Http::withoutVerifying()->post(env('APP_URL') . '/api/getUserNotification', [
            'token' => $token,
        ])->json();



        $getAstrologer = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstrologerById', [
            'astrologerId' => $request->astrologerId,
        ])->json();
        // dd($getAstrologer);

        $getSystemFlag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag');
        // Convert the response JSON array to a collection
        $recordList = $getSystemFlag['recordList'];

        $agoraAppIdObject = null;
        foreach ($recordList as $item) {
            if ($item['name'] === 'AgoraAppId') {
                $agoraAppIdObject = $item;
                break;
            }
        }
        $agoraAppIdValue = $agoraAppIdObject['value'];


        return view('frontend.pages.callpage', [
            'getAstrologer' => $getAstrologer,
            'callrequest' => $callrequest,
            'getUserNotification' => $getUserNotification,
            'agoraAppIdValue' => $agoraAppIdValue,
        ]);
    }


    public function getMyCall(Request $request)
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



        return view('frontend.pages.my-calls', [
            'getUserById' => $getUserById,
            'getProfile' => $getProfile,
            'currency' => $currency,

        ]);
    }


}
