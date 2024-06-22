<?php

use App\Http\Controllers\Admin\PageManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\frontend\AccountController;
use App\Http\Controllers\frontend\Astrologer\AuthController as AstrologerAuthController;
use App\Http\Controllers\frontend\AstrologerCallController;
use App\Http\Controllers\frontend\AstrologerChatController;
use App\Http\Controllers\frontend\AstrologerController;
use App\Http\Controllers\frontend\AuthController;
use App\Http\Controllers\frontend\BlogController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\HoroscopeController;
use App\Http\Controllers\frontend\KundaliController;
use App\Http\Controllers\frontend\PageManagementController as FrontendPageManagementController;
use App\Http\Controllers\frontend\ProductController;
use App\Http\Controllers\frontend\ReportController;
use App\Http\Controllers\frontend\WalletController;
use Symfony\Component\HttpFoundation\Session\Session;


$session = new Session();
$token = $session->get('token');
header('Authorization:Bearer '.$token);
// header('Content-Type:application/json');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization');
header('Accept:application/json');
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Route::get('/', function () {
//     return view('home');
// });
// Route::get('/home', function () {
//     return view('home');
// });

Route::get('privacyPolicy', [PageManagementController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::get('terms-condition', [PageManagementController::class, 'termscondition'])->name('termscondition');



 // Payment Related
 Route::get('payment', [PaymentController::class, 'payment'])->name('payment');
 Route::post('payment', [PaymentController::class, 'payment'])->name('payment');
 Route::get('payment-success', [PaymentController::class, 'paymentsuccess']);
 Route::post('payment-success', [PaymentController::class, 'paymentsuccess'])->name('payment-success');
 Route::get('payment-failed', [PaymentController::class, 'paymentfailed'])->name('payment-faileds');
 Route::post('payment-failed', [PaymentController::class, 'paymentfailed'])->name('payment-failed');
 Route::get('payment-response', [PaymentController::class, 'paymentsresponse']);
 Route::post('payment-response', [PaymentController::class, 'paymentsresponse'])->name('payment-response');
//  Route::get('payment-process', [PaymentController::class, 'paymentprocess']);
 Route::post('payment-process', [PaymentController::class, 'paymentprocess'])->name('payment-process');
 Route::get('payment-pending', [PaymentController::class, 'paymentpending']);
 Route::post('payment-pending', [PaymentController::class, 'paymentpending'])->name('payment-pending');
 Route::get('payu-merchant-form', [PaymentController::class, 'payumerchantform'])->name('payumerchantform');
 Route::get('paytm-merchant-form', [PaymentController::class, 'paytmmerchantform'])->name('paytmmerchantform');



//  Frontend


// Route::group(['prefix' => 'web'], function () {

    Route::post('/verifyOTL', [AuthController::class, 'verifyOTL'])->name('front.verifyOTL');

    Route::get('/', [HomeController::class, 'home'])->name('front.home');
    Route::get('/reportlist', [ReportController::class, 'reportList'])->name('front.reportList');
    Route::get('/talklist', [AstrologerCallController::class, 'talkList'])->name('front.talkList');
    Route::get('/chatlist', [AstrologerChatController::class, 'chatList'])->name('front.chatList');
    Route::get('/astrologerdeatails', [AstrologerController::class, 'astrologerDetails'])->name('front.astrologerDetails');
    Route::get('/panchang', [KundaliController::class, 'getPanchang'])->name('front.getPanchang');
    Route::get('/dailyhoroscope', [HoroscopeController::class, 'dailyHoroscope'])->name('front.dailyHoroscope');
    Route::get('/horoscope', [HoroscopeController::class, 'horoScope'])->name('front.horoScope');
    Route::get('/blog', [BlogController::class, 'getBlog'])->name('front.getBlog');
    Route::get('/blog-details', [BlogController::class, 'getBlogDetails'])->name('front.getBlogDetails');
    Route::get('/products', [ProductController::class, 'getproducts'])->name('front.getproducts');
    Route::get('/product-details', [ProductController::class, 'getproductDetails'])->name('front.getproductDetails');
    Route::get('/kundali', [KundaliController::class, 'getkundali'])->name('front.getkundali');
    Route::get('/kundali-matching', [KundaliController::class, 'kundaliMatch'])->name('front.kundaliMatch');
    Route::get('/kundali-match-report', [KundaliController::class, 'kundaliMatchReport'])->name('front.kundaliMatchReport');
    Route::get('/liveastrologers', [AstrologerController::class, 'getLiveAstro'])->name('front.getLiveAstro');
    Route::get('/live', [AstrologerController::class, 'LiveAstroDetails'])->name('front.LiveAstroDetails');
    Route::get('/my-account', [AccountController::class, 'getMyAccount'])->name('front.getMyAccount');
    Route::get('/my-wallet', [WalletController::class, 'getMyWallet'])->name('front.getMyWallet');
    Route::get('/wallet-recharge', [WalletController::class, 'walletRecharge'])->name('front.walletRecharge');
    Route::get('/verifyOtp', [AuthController::class, 'verifyOtp'])->name('front.verifyOtp');
    Route::get('/logout', [AuthController::class, 'logout'])->name('front.logout');
    Route::post('/updateprofile', [AccountController::class, 'updateprofile'])->name('front.updateprofile');
    Route::get('/deleteAccount', [AccountController::class, 'deleteAccount'])->name('front.deleteAccount');
    Route::get('/checkout', [ProductController::class, 'checkout'])->name('front.checkout');
    Route::get('/my-orders', [ProductController::class, 'myOrders'])->name('front.myOrders');
    Route::get('/my-reports', [ReportController::class, 'getMyReport'])->name('front.getMyReport');
    Route::get('/chat', [AstrologerChatController::class, 'chat'])->name('front.chat');
    Route::get('/my-chats', [AstrologerChatController::class, 'getMyChat'])->name('front.getMyChat');
    Route::get('/my-chat-history', [AstrologerChatController::class, 'getChatHistory'])->name('front.getChatHistory');
    Route::get('/call', [AstrologerCallController::class, 'call'])->name('front.call');
    Route::get('/audiocall', [AstrologerCallController::class, 'audiocall'])->name('front.audiocall');
    Route::get('/my-calls', [AstrologerCallController::class, 'getMyCall'])->name('front.getMyCall');
    Route::get('/my-following', [AccountController::class, 'getMyFollowing'])->name('front.getMyFollowing');
    Route::get('privacy-policy', [FrontendPageManagementController::class, 'privacyPolicy'])->name('front.privacyPolicy');
    Route::get('terms-condition', [FrontendPageManagementController::class, 'termscondition'])->name('front.termscondition');


    Route::get('/astrologer/{id?}/stories', [HomeController::class, 'getAstrologerStories'])->name('front.getAstrologerStories');
    Route::post('/astrologer/viewstory', [HomeController::class, 'viewstory'])->name('front.viewstory');



// });
