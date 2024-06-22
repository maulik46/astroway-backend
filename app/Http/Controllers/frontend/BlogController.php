<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class BlogController extends Controller
{
    public function getBlog(Request $request)
    {
        Artisan::call('cache:clear');
        $getblog = Http::withoutVerifying()->post(env('APP_URL') . '/api/getBlog')->json();


        return view('frontend.pages.blogs', [
            'getblog' => $getblog


        ]);
    }
    public function getBlogDetails(Request $request)
    {
        Artisan::call('cache:clear');
        $getblogdetails = Http::withoutVerifying()->post(env('APP_URL') . '/api/getBlogById', [
            'id' => $request->id,])->json();


        return view('frontend.pages.blog-details', [
            'getblogdetails' => $getblogdetails


        ]);
    }




}
