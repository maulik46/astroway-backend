<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;

class AstrologerChatController extends Controller
{
    public function chatList(Request $request)
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

        $getAstrologerCategory = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstrologerCategory')->json();

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

        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();



        return view('frontend.pages.astrologer-chat-list', [
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

    public function chat(Request $request)
    {

        if(!authcheck())
            return redirect()->route('front.home');

        $chatrequest=DB::table('chatrequest')->where('userId',authcheck()['id'])->where('id',$request->chatId)->first();

        if($chatrequest->chatStatus!='Confirmed')
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


        return view('frontend.pages.chatpage', [
            'getAstrologer' => $getAstrologer,
            'chatrequest' => $chatrequest,
            'getUserNotification' => $getUserNotification,
        ]);
    }


    public function getMyChat(Request $request)
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



        return view('frontend.pages.my-chats', [
            'getUserById' => $getUserById,
            'getProfile' => $getProfile,
            'currency' => $currency,

        ]);
    }


    public function getChatHistory(Request $request)
    {

        if(!authcheck())
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

        $getUserHistoryReview = Http::withoutVerifying()->post(env('APP_URL') . '/api/getUserHistoryReview', [
            'userId' => authcheck()['id'],
            'astrologerId' => $request->astrologerId,
        ])->json();




        return view('frontend.pages.chat-history', [
            'getAstrologer' => $getAstrologer,
            'getUserNotification' => $getUserNotification,
            'getUserHistoryReview' => $getUserHistoryReview,
        ]);
    }
}
