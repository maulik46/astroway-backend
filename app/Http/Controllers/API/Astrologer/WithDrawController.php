<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WithDrawController extends Controller
{
    public function sendWithdrawRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'astrologerId',
                'withdrawAmount'
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
                'withdrawAmount' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $withdrawRequest = array('astrologerId' => $req->astrologerId,
                'withdrawAmount' => $req->withdrawAmount,
                'status' => 'Pending',
                'paymentMethod' => $req->paymentMethod,
                'upiId' => $req->upiId,
                'accountNumber' => $req->accountNumber,
                'ifscCode' => $req->ifscCode,
                'accountHolderName' => $req->accountHolderName,
                'createdBy' => $id,
                'modifiedBy' => $id,
				'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            );

            DB::table('withdrawrequest')->insert($withdrawRequest);
            $amount = DB::table('user_wallets')
                ->join('astrologers', 'astrologers.userId', '=', 'user_wallets.userId')
                ->where('astrologers.id', '=', $req->astrologerId)
                ->select('amount', 'user_wallets.id')->get();
            if ($amount && count($amount) > 0) {
                $userWallet = array(
                    'amount' => $amount[0]->amount - $req->withdrawAmount,
                );
                DB::table('user_wallets')
                    ->where('id', $amount[0]->id)
                    ->update($userWallet);
            }
            return response()->json([
                'message' => 'Send Withdrawl Request Successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }



      public function updateWithdrawRequest(Request $req)
    {
        // DB::beginTransaction();
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'id',
                'astrologerId',
                'withdrawAmount'
            );
            $validator = Validator::make($data, [
                'id' => 'required',
                'astrologerId' => 'required',
                'withdrawAmount' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $withdrawRequest = array('astrologerId' => $req->astrologerId,
                'withdrawAmount' => $req->withdrawAmount,
                'status' => 'Pending',
                'paymentMethod' => $req->paymentMethod,
                'upiId' => $req->upiId,
                'accountNumber' => $req->accountNumber,
                'ifscCode' => $req->ifscCode,
                'accountHolderName' => $req->accountHolderName,
                'createdBy' => $id,
                'modifiedBy' => $id,
				'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            );
            $withDrawAmount = DB::table('withdrawrequest')
                ->where('id', $req->id)
                ->get();

            $amount = DB::table('user_wallets')
            ->join('astrologers', 'astrologers.userId', '=', 'user_wallets.userId')
            ->where('astrologers.id', '=', $req->astrologerId)
            ->select('amount', 'user_wallets.id')->get();

            if($req->withdrawAmount>$withDrawAmount[0]->withdrawAmount+$amount[0]->amount)
            {
                return response()->json([
                    'message' => 'Withdrawl Amount Exceeded',
                    'status' => 400,
                ], 400);
            }
            DB::table('withdrawrequest')
                ->where('id', $req->id)
                ->update($withdrawRequest);



            $userWallet = array(
                'amount' => ($amount[0]->amount + $withDrawAmount[0]->withdrawAmount) - $req->withdrawAmount,
            );


            DB::table('user_wallets')
                ->where('id', $amount[0]->id)
                ->update($userWallet);

            return response()->json([
                'message' => 'Request update & send to admin successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            // DB::rollback();
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function getWithdrawRequest(Request $req)
    {
        try {
            $withdrawRequest = DB::table('withdrawrequest')
                ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
            if ($req->astrologerId) {
                $withdrawRequest = $withdrawRequest->where('astrologers.id', '=', $req->astrologerId);
                $walletTransaction = DB::table('wallettransaction')
                ->join('astrologers', 'astrologers.userId', '=', 'wallettransaction.userId')
                ->select('wallettransaction.*')
                ->where('astrologers.id', '=', $req->astrologerId)
                ->get();

            $payment = DB::table('payment')
                ->join('astrologers', 'astrologers.userId', 'payment.userId')
                ->where('astrologers.id', $req->astrologerId)
                ->select('payment.*',DB::raw("'recharge' as transactionType"))
                ->orderBy('payment.id', 'DESC')
                ->get();

                // Merge wallet and payment data based on transaction type
            $mergedData = $walletTransaction->merge($payment);

            // Order the merged data by created_at in descending order
            $mergedData = $mergedData->sortByDesc('created_at')->values();



            } else {
                $walletTransaction = [];
            }
            $withdrawRequest = $withdrawRequest->select('withdrawrequest.*', 'astrologers.name', 'astrologers.contactNo', 'astrologers.profileImage', 'astrologers.userId');

            $withdrawRequest = $withdrawRequest->orderBy('id', 'DESC');
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $withdrawRequest->skip($req->startIndex);
                $withdrawRequest->take($req->fetchRecord);
            }
            $withdrawRequest = $withdrawRequest->get();

            $amount = 0;
            $totalPending = 0;
            $totalEarning = 0;
            $totalAmount = 0;
            if ($req->astrologerId) {
                $amount = DB::Table('user_wallets')
                    ->join('astrologers', 'astrologers.userId', '=', 'user_wallets.userId')
                    ->where('astrologers.id', '=', $req->astrologerId)
                    ->select('amount')
                    ->get();
                if ($withdrawRequest && count($withdrawRequest) > 0) {
                    $totalAmount = DB::Table('withdrawrequest')
                        ->where('astrologerId', '=', $req->astrologerId)
                        ->where('status', '=', 'Released')
                        ->sum('withdrawAmount');
                }
                $totalPending = DB::table('withdrawrequest')
                    ->where('status', '=', 'Pending')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->sum('withdrawAmount');
				$totalEarning = DB::table('withdrawrequest')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->sum('withdrawAmount');
            }

            $withdrawRequestCount = DB::table('withdrawrequest')
                ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
            if ($req->astrologerId) {
                $withdrawRequestCount = $withdrawRequestCount->where('astrologers.id', '=', $req->astrologerId);
            }
            $withdrawRequestCount = $withdrawRequestCount->count();
            $withdrawl = array(
                'withdrawl' => $withdrawRequest,
                'walletTransaction' => $mergedData && count($mergedData) > 0 ? $mergedData : [],
                'walletAmount' => $amount && count($amount) > 0? $amount[0]->amount : 0,
                'totalPending' => (string)($totalPending ? $totalPending : 0),
                "totalEarning" => $totalEarning || ($amount && count($amount) > 0) ? $totalEarning + $amount[0]->amount : 0,
                'withdrawAmount' => $totalAmount ? $totalAmount : 0
            );


            return response()->json([
                'message' => 'Get Withdrawl request successfully',
                'status' => 200,
                'recordList' => $withdrawl,
                'totalRecords' => $withdrawRequestCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function releaseAmount(Request $req)
    {
        try {

            $data = $req->only(
                'id',
            );
            $validator = Validator::make($data, [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $withdrawRequest = array('status' => 'Released',
            );
            DB::table('withdrawrequest')
                ->where('id', $req->id)
                ->update($withdrawRequest);
            return response()->json([
                'message' => 'Request update & send to admin successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function getWithdrawMethod(Request $req)
    {
        try {
            $withdrawMethod = DB::table('withdrawmethods')
                ->get();

            return response()->json([
                'message' => 'Get Withdrawl method successfully',
                'status' => 200,
                'recordList' => $withdrawMethod,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    // public function getWithdrawMethod(Request $req)
    // {
    //     try {
    //         $astrologerId = $req->astrologerId;

    //         $withdrawMethods = DB::table('withdrawmethods')
    //             ->get();

    //         if ($withdrawMethods->isEmpty()) {
    //             return response()->json([
    //                 'error' => true,
    //                 'message' => 'No withdrawal methods found.',
    //                 'status' => 404,
    //             ], 404);
    //         }

    //         // Find the method with method_id 1 (Bank Account)
    //         $bankMethod = $withdrawMethods->where('method_id', 1)->first();

    //         if (!$bankMethod) {
    //             return response()->json([
    //                 'error' => true,
    //                 'message' => 'Bank Account withdrawal method not found.',
    //                 'status' => 404,
    //             ], 404);
    //         }

    //         // Get bank details for the specified astrologer ID
    //         $bankDetails = DB::table('withdrawrequest')
    //         ->where('astrologerId', $astrologerId)
    //         ->where('paymentMethod', 1)
    //         ->latest('created_at')
    //         ->select('accountNumber', 'ifscCode', 'accountHolderName')
    //         ->first();

    //         // Add bank details to the first method if it's the bank account method
    //         if ($bankDetails) {
    //             $bankMethod->bankDetails = $bankDetails;
    //         }

    //         return response()->json([
    //             'message' => 'Get Withdrawal methods successfully',
    //             'status' => 200,
    //             'recordList' => $withdrawMethods,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => true,
    //             'message' => $e->getMessage(),
    //             'status' => 500,
    //         ], 500);
    //     }
    // }


}
