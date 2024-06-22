<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageManagementController extends Controller
{

    public function privacyPolicy(Request $request)
	{

        try {

            $privacy=DB::table('pages')->where('type','privacy')->first();
            return view('frontend.pages.privacy-policy',compact('privacy'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}

    public function termscondition(Request $request)
	{

        try {

            $terms=DB::table('pages')->where('type','terms')->first();
            return view('frontend.pages.terms-condition',compact('terms'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}
}
