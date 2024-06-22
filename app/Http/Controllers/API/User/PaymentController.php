<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Payment;
use App\Models\UserModel\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    public function addPayment(Request $req)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            $id = $user->id;



            $data = $req->only(
                'amount',
                'cashback_amount',
            );

            $validator = Validator::make($data, [
                'amount' => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }

            // Create a new payment record
            $payment = Payment::create([
                'amount' => $req->amount,
                'cashback_amount' => $req->cashback_amount,
                'userId' => $id,
                'paymentStatus' => 'pending',
                'createdBy' => $id,
                'modifiedBy' => $id,
            ]);

            $lastPayment = Payment::where('userId', $id)->latest()->first();

            return response()->json([
                'status' => 200,
                'message' => 'Click on url to add payment',
                'recordList' => $lastPayment,
                'url' => env('APP_URL') . "/payment?payid={$lastPayment->id}"

            ], 200);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }

    }

    public function getRechargeAmount()
    {
        try {
            $rechargeAmount = DB::table('rechargeamount')->get();
            return response()->json([
                'status' => 200,
                'message' => 'RechargeAmount Get Successfully',
                'recordList' => $rechargeAmount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
