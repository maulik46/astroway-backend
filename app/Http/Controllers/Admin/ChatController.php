<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\services\FCMService;
use Carbon\Carbon;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class ChatController extends Controller
{


    public function getFireStoredata(Request $req)
{
    try {
        $user = DB::table('tickets')
            ->join('users', 'users.id', '=', 'tickets.userId')
            ->select('users.name as userName', 'users.profile', 'tickets.userId', 'tickets.ticketStatus')
            ->where('tickets.id', '=', $req->id)
            ->get();

        $chatId = $req->id . '_' . $user[0]->userId;
        $data = array(
            'chatId' => $chatId,
            'userName' => $user[0]->userName,
            'userProfile' => $user[0]->profile,
            'userId' => $user[0]->userId,
            "ticketId" => $req->id,
            'ticketStatus' => $user[0]->ticketStatus,
        );

        // Use Guzzle or any HTTP client to make requests to Firestore REST API
        $httpClient = new \GuzzleHttp\Client();
        $response = $httpClient->get("https://firestore.googleapis.com/v1/projects/astroway-diploy/databases/(default)/documents/supportChat/{$chatId}/userschat/{$req->id}/messages");

        // Check if 'documents' key exists in the API response
        $apiResponse = json_decode($response->getBody()->getContents(), true);

        if (isset($apiResponse['documents'])) {
            $messages = $apiResponse['documents'];

            usort($messages, function ($a, $b) {
                return strtotime($a['createTime']) - strtotime($b['createTime']);
            });

            return view('pages.chat', compact('messages', 'data'));
        } else {
            // Handle the case when there are no documents
            $messages = [];
            return view('pages.chat', compact('messages', 'data'));
        }
    } catch (\Exception $e) {
        return dd($e->getMessage());
    }
}

    



    public function createChat(Request $req)
    {
        try {
            $apiEndpoint = "https://firestore.googleapis.com/v1/projects/astroway-diploy/databases/(default)/documents/";
    
            $postData = [
                'fields' => [
                    'message' => ['stringValue' => $req->message],
                    'createdAt' => ['timestampValue' => Carbon::now()->toIso8601String()],
                    'updatedAt' => ['timestampValue' => Carbon::now()->toIso8601String()],
                    'userId1' => ['integerValue' => $req->ticketId],
                    'userId2' => ['integerValue' => $req->senderId],
                    'status' => ['stringValue' => 'OPEN'],
                ],
            ];
    
            $client = new Client();
    
            if ($req->messageCount == 2 || $req->ticketStatus == 'WAITING') {
                $data = ['ticketStatus' => 'OPEN'];
                DB::table('tickets')->where('id', '=', $req->ticketId)->update($data);
    
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                    ->where('tickets.id', '=', $req->ticketId)
                    ->select('user_device_details.*')
                    ->get();
    
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Notification for customer support status update',
                            'body' => ['description' => 'Notification for customer support status update', 'status' => 'OPEN'],
                        ]
                    );
                }
            } else {
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                    ->where('tickets.id', '=', $req->ticketId)
                    ->select('user_device_details.*')
                    ->get();
    
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Receive Message',
                            'body' => ['description' => 'Receive Message'],
                        ]
                    );
                }
            }
    
            $response = $client->post($apiEndpoint . 'supportChat/' . $req->chatId . '/userschat/' . $req->senderId . '/messages', [
                'json' => $postData,
            ]);
    
            $response = $client->post($apiEndpoint . 'supportChat/' . $req->chatId . '/userschat/' . $req->ticketId . '/messages', [
                'json' => $postData,
            ]);
    
            return response()->json([
                'success' => ['Send Message Successfully'],
            ]);
        } catch (\Exception $e) {
            return dd($e->getMessage());
        }
    }
}
