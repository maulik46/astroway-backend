<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Astrologer;
use App\Models\UserModel\CallRequest;
use App\services\FCMService;
use Carbon\Carbon;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CallRequestController extends Controller
{
    public function addCallRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'astrologerId',
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            CallRequest::create([
                'astrologerId' => $req['astrologerId'],
                'userId' => $id,
                'callStatus' => 'Pending',
                'created_at' => Carbon::now()->timestamp,
                'isFreeSession' => $req['isFreeSession'],
            ]);

            $userDeviceDetail = DB::table('user_device_details')
                ->JOIN('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
                ->WHERE('astrologers.id', '=', $req->astrologerId)
                ->SELECT('user_device_details.*')
                ->get();

            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                FCMService::send(
                    $userDeviceDetail,
                    [
                        'title' => 'Get Call Request',
                        'body' => [
                            "notificationType" => 2,
                            'description' => '',
                        ],
                    ]
                );
            }
            return response()->json([
                'message' => 'Call Request Send Successfully',
                'status' => 200,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function getCallRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'astrologerId',
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callRequest = DB::table('callrequest')
                ->join('users', 'users.id', '=', 'callrequest.userId')
                ->where('astrologerId', '=', $req->astrologerId)
                ->where('callStatus', '=', 'Pending')
                ->select('users.*', 'callrequest.id as callId');

            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $callRequest->skip($req->startIndex);
                $callRequest->take($req->fetchRecord);
            }
            $callRequest = $callRequest->get();
            if ($callRequest && count($callRequest) > 0) {
                for ($i = 0; $i < count($callRequest); $i++) {
                    $userDeviceDetail = DB::table('user_device_details')->where('userId', $callRequest[$i]->id)->first();
                    $callRequest[$i]->fcmToken = $userDeviceDetail->fcmToken;
                }
            }
            return response()->json([
                'messge' => 'getCallRequest Successfully',
                'status' => 200,
                'recordList' => $callRequest,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function rejectCallRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callRequest = CallRequest::find($req->callId);
            $currenttimestamp = Carbon::now()->timestamp;
            if ($callRequest) {
                $callRequest->callStatus = 'Rejected';
                $callRequest->updated_at = $currenttimestamp;
                $callRequest->update();
                return response()->json([
                    'messge' => 'Reject Call Request Successfully',
                    'status' => 200,
                ], 200);
            }
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function removeFromWaitlist(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callRequest = CallRequest::find($req->callId);
            $callRequest->Delete();
            return response()->json([
                'messge' => 'Remove Call Request Successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function acceptCallRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callRequest = CallRequest::find($req->callId);
            $currenttimestamp = Carbon::now()->timestamp;
            if ($callRequest) {
                $callRequest->callStatus = 'Accepted';
                $callRequest->updated_at = $currenttimestamp;
                $callRequest->update();
            }

            return response()->json([
                'messge' => 'call Request Accept Successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function storeToken(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
                'token',
                'channelName'
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
                'token' => 'required',
                'channelName' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callRequest = CallRequest::find($req->callId);
            $currenttimestamp = Carbon::now()->toDateTimeString();
            if ($callRequest) {
                $callRequest->callStatus = 'Accepted';
                $callRequest->updated_at = $currenttimestamp;
                $callRequest->token = $req->token;
                $callRequest->channelName = $req->channelName;
                $callRequest->update();

            }
            $userDeviceDetail = DB::table('user_device_details')
                ->WHERE('user_device_details.userId', '=', $callRequest->userId)
                ->SELECT('user_device_details.*')
                ->get();

            $astrologer = DB::Table('astrologers')
                ->leftjoin('user_device_details', 'user_device_details.userId', 'astrologers.userId')
                ->where('astrologers.id', '=', $callRequest->astrologerId)
                ->select('name', 'profileImage', 'user_device_details.fcmToken')
                ->get();
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                $response = FCMService::send(
                    $userDeviceDetail,
                    [
                        'title' => 'Accept Call Request',
                        'body' => [
                            "astrologerId" => $callRequest->astrologerId,
                            "astrologerName" => $astrologer[0]->name,
                            "notificationType" => 1,
                            "profile" => $astrologer[0]->profileImage,
                            "token" => $callRequest->token,
                            "channelName" => $callRequest->channelName,
                            "callId" => $callRequest->id,
                            'description' => '',
                            'fcmToken' => $astrologer[0]->fcmToken,
                        ],
                    ]
                );
            }
            return response()->json([
                'messge' => $response,
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function endCall(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'callId',
                'totalMin'
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
                'totalMin' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $callData = DB::table('callrequest')
                ->join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
                ->join('users', 'users.id', '=', 'callrequest.userId')
                ->where('callrequest.id', '=', $req->callId)
                ->select('callrequest.*', 'users.name', 'astrologers.name as astrologerName', 'astrologers.userId as astrologerUserId')
                ->get();
            $totalMin = $req->totalMin / 60;
            $totalMin = round($totalMin);
            $astrologerCommission = 0;
            $deduction = 0;
            $charge = Astrologer::query()
                ->where('id', '=', $callData[0]->astrologerId)
                ->get();
            if (!$callData[0]->isFreeSession) {
                $deduction = $totalMin * $charge[0]->charge;
                $commission = DB::table('commissions')
                    ->where('commissionTypeId', '=', '2')
                    ->where('astrologerId', '=', $callData[0]->astrologerId)
                    ->get();
                if ($commission && count($commission) > 0) {
                    $adminCommission = ($commission[0]->commission * $deduction) / 100;
                } else {
                    $syscommission = DB::table('systemflag')->where('name', 'CallCommission')->select('value')->get();

                    $adminCommission = ($syscommission[0]->value * $deduction) / 100;
                }
                $astrologerCommission = $deduction - $adminCommission;
            }

            $callDatas = array(
                'totalMin' => $totalMin,
                'callStatus' => 'Completed',
                'deduction' => $deduction,
                'callRate' => !$callData[0]->isFreeSession ? $charge[0]->charge : 0,
                'deductionFromAstrologer' => $astrologerCommission,
                'sId' => $req->sId,
                'sId1' => $req->sId1,
            );
            DB::Table('callrequest')
                ->where('id', '=', $req->callId)
                ->update($callDatas);
            $charge[0]->totalOrder = $charge[0]->totalOrder ? $charge[0]->totalOrder + 1 : 1;
            $charges = array(
                'totalOrder' => $charge[0]->totalOrder,
            );
            DB::table('astrologers')
                ->where('id', $charge[0]->id)
                ->update($charges);
            if ($charge[0]->charge > 0) {
                $wallet = DB::table('user_wallets')
                    ->where('userId', '=', $callData[0]->userId)
                    ->get();
                $wallets = array(
                    'userId' => $callData[0]->userId,
                    'amount' => (!$callData[0]->isFreeSession) ? ($wallet[0]->amount - $deduction) : (($wallet && count($wallet) > 0) ? $wallet[0]->amount : 0),
                    'createdBy' => $id,
                    'modifiedBy' => $id,
                );
                if ($wallet && count($wallet) > 0) {
                    DB::table('user_wallets')
                        ->where('id', $wallet[0]->id)
                        ->update($wallets);
                } else {
                    DB::table('user_wallets')->insert($wallets);
                }
                $astrologerWallet = DB::table('user_wallets')
                    ->where('userId', $callData[0]->astrologerUserId)
                    ->get();
                $astrologerWall = array(
                    'userId' => $callData[0]->astrologerUserId,
                    'amount' => $astrologerWallet && count($astrologerWallet) > 0 ? $astrologerWallet[0]->amount + $astrologerCommission : $astrologerCommission,
                    'createdBy' => $id,
                    'modifiedBy' => $id,
                );
                if ($astrologerWallet && count($astrologerWallet) > 0) {
                    DB::table('user_wallets')
                        ->where('id', $callData[0]->astrologerUserId)
                        ->update($astrologerWall);
                } else {
                    DB::Table('user_wallets')->insert($astrologerWall);
                }
            }
            $orderRequest = array(
                'userId' => $callData[0]->userId,
                'astrologerId' => $callData[0]->astrologerId,
                'orderType' => 'call',
                'totalPayable' => $deduction,
                'orderStatus' => 'Complete',
                'totalMin' => $totalMin,
                'callId' => $req->callId,

            );
            DB::Table('order_request')->insert($orderRequest);
            $id = DB::getPdo()->lastInsertId();
            $transaction = array(
                'userId' => $callData[0]->userId,
                'amount' => $deduction,
                'isCredit' => false,
                "transactionType" => 'Call',
                "orderId" => $id,
                "astrologerId" => $callData[0]->astrologerId,
            );
            $astrologerTransaction = array(
                'userId' => $callData[0]->astrologerUserId,
                'amount' => $astrologerCommission,
                'isCredit' => true,
                "transactionType" => 'Call',
                "orderId" => $id,
                "astrologerId" => $callData[0]->astrologerId,
            );
            if(!$callData[0]->isFreeSession){
            if ($commission && count($commission) > 0 ) {
                $adminGetCommission = array(
                    'commissionTypeId' => 1,
                    "amount" => $adminCommission,
                    "commissionId" => $commission && count($commission) > 0 ? $commission[0]->id : null,
                    "orderId" => $id,
                    "createdBy" => $charge[0]->userId,
                    "modifiedBy" => $charge[0]->userId,
                );
                DB::table('admin_get_commissions')->insert($adminGetCommission);
            }
        }
            DB::table('wallettransaction')->insert($transaction);
            DB::table('wallettransaction')->insert($astrologerTransaction);
            return response()->json([
                'message' => 'Call Request End Successfully',
                'status' => 200,
                'recordList' => $deduction,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function rejectCallRequestFromCustomer(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callData = CallRequest::find($req->callId);
            if ($callData) {
                $callData->delete();
            }
            return response()->json([
                'message' => 'Call Request Rejected Successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function acceptCallRequestFromCustomer(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callData = CallRequest::find($req->callId);
            $currenttimestamp = Carbon::now()->timestamp;
            if ($callData) {
                $callData->callStatus = 'Confirmed';
                $callData->deduction = 0;
                $callData->updated_at = $currenttimestamp;
                $callData->totalMin = 0;
                $callData->update();
            }
            return response()->json([
                'message' => 'Call Request Accepted Successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function storeCallRecording(Request $req)
    {
        try {

            $storage = new StorageClient([
                'projectId' => 'astroway-diploy',
                'keyFilePath' => '..\storage\app\public\file.json',
            ]);
            foreach ($storage->buckets() as $bucket) {
                $bucketName = $bucket->name();
            }
            $buckets = $storage->bucket($bucketName);
            $objects = [];
            foreach ($buckets->objects() as $object) {
                array_push($objects, $object->name());
            }
            for ($i = 0; $i < count($objects); $i++) {
                $bucket = $storage->bucket($bucketName);
                $objectss = $bucket->object($objects[$i]);
                $objectss->downloadToFile("storage/callRecording/" . $i . "_" . $objects[$i]);
            }
            return response()->json([
                'message' => $objects,
                'status' => 200,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }

    }

    public function getCallById(Request $req)
    {
        try {
            $callData = DB::table('callrequest')
                ->join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
                ->select('callrequest.*', 'astrologers.name as astrologerName')
                ->where('callrequest.id', '=', $req->callId)
                ->get();
            return response()->json([
                'recordList' => $callData,
                'status' => 200,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function addCallStatus(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                Auth::guard('api')->user()->id;
            }
            $status = array(
                'callStatus' => $req->status,
                'callWaitTime' => ($req->status == 'Offline' || $req->status == 'Online') ? null : $req->waitTime,
            );
            DB::table('astrologers')->where('id', '=', $req->astrologerId)
                ->update($status);
            return response()->json([
                "message" => "Update Astrologer",
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

}
