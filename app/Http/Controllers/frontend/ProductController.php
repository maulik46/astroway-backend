<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function getproducts(Request $request)
    {

        Artisan::call('cache:clear');
        $getproductCategory = Http::withoutVerifying()->post(env('APP_URL') . '/api/getproductCategory')->json();

        // Determine the current page from the request
        $currentPage = $request->query('page', 1);
        $productCategoryId=(int)$request->productCategoryId;
        $searchTerm = $request->input('s');


        // Fetch products from the API with pagination
        $getAstromallProduct = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstromallProduct', [
            'startIndex' => ($currentPage - 1) * 6,
            'fetchRecord' => 6,
            'productCategoryId'=>$productCategoryId,
            's' => $searchTerm
        ]);

        // Extract the paginated data from the API response
        $data = $getAstromallProduct->json();

        $totalRecords = count($data['recordList']);

        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();


        return view('frontend.pages.products', [
            'getAstromallProduct' => $getAstromallProduct,
            'getproductCategory' => $getproductCategory,
            'currentPage' => $currentPage,
            'perPage' => 6,
            'totalRecords' => $totalRecords,
            'productCategoryId' => $productCategoryId,
            'searchTerm' => $searchTerm,
            'currency' => $currency,
        ]);
    }
    public function getproductDetails(Request $request)
    {
        Artisan::call('cache:clear');
        $getAstromallProduct = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstromallProduct')->json();

        $getproductdetails = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstromallProductById', [
            'id' => $request->id,])->json();

        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

            // dd( $getproductdetails );

        return view('frontend.pages.product-details', [
            'getproductdetails' => $getproductdetails,
            'getAstromallProduct' => $getAstromallProduct,
            'currency' => $currency,


        ]);
    }
    public function checkout(Request $request)
    {
        Artisan::call('cache:clear');
        if(!authcheck())
            return redirect()->route('front.home');

        $userId=authcheck()['id'];

        $getAstromallProduct = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstromallProduct')->json();
        $getOrderAddress = Http::withoutVerifying()->post(env('APP_URL') . '/api/getOrderAddress', [
            'userId' => $userId,])->json();

        $getproductdetails = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstromallProductById', [
            'id' => $request->id,])->json();

        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();

        $getsystemflag = collect($getsystemflag['recordList']);
        $gstvalue = $getsystemflag->where('name', 'Gst')->first();
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

        return view('frontend.pages.checkout', [
            'getproductdetails' => $getproductdetails,
            'getAstromallProduct' => $getAstromallProduct,
            'getOrderAddress' => $getOrderAddress,
            'gstvalue' => $gstvalue,
            'currency' => $currency,


        ]);
    }

    public function myOrders(Request $request)
    {
        Artisan::call('cache:clear');

        if(!authcheck())
            return redirect()->route('front.home');


        $getUserById = Http::withoutVerifying()->post(env('APP_URL') . '/api/getUserById',[
            'userId' => authcheck()['id'],
        ])->json();




        return view('frontend.pages.my-orders', [
            'getUserById' => $getUserById,



        ]);
    }
}
