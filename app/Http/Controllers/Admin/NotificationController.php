<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

define('LOGINPATH', '/admin/login');

class NotificationController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;
    public function addNotification()
    {
        return view('pages.notification-list');
    }

    public function addNotificationApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                Notification::create([
                    'title' => $req->title,
                    'description' => $req->description,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                return redirect()->route('notifications');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Get Skill Api

    public function getNotification(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $notifications = Notification::query();
                $notifications->orderBy("id", "DESC");
                $notificationCount = $notifications->count();
                $notifications->skip($paginationStart);
                $notifications->take($this->limit);
                $notifications = $notifications->get();
                $totalPages = ceil($notificationCount / $this->limit);
                $totalRecords = $notificationCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                $users = DB::Table('users')
                ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('isDelete', '=', false)
                    ->where('isActive', '=', true)
                // ->where('user_roles.roleId', '=', 3)
                    ->select('users.*','user_roles.roleId')
                    ->get();

                //  filters by wallet never recharged
                $wallet_empty = DB::table('users')
                ->leftJoin('user_wallets', 'users.id', '=', 'user_wallets.userId')
                ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                ->whereNull('user_wallets.userId') // Users who don't have any wallet records
                ->where('users.isDelete', '=', false)
                ->where('users.isActive', '=', true)
                ->where('user_roles.roleId', '=', 3)
                ->select('users.*')
                ->get();

                $usersNotUsedFree = DB::table('users')
                ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('chatrequest')
                        ->whereColumn('chatrequest.userId', 'users.id')
                        ->where('chatrequest.chatStatus', '=', 'Completed');
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('callrequest')
                        ->whereColumn('callrequest.userId', 'users.id')
                        ->where('callrequest.callStatus', '=', 'Completed');
                })
                ->where('users.isDelete', '=', false)
                ->where('users.isActive', '=', true)
                ->where('user_roles.roleId', '=', 3)
                ->select('users.*')
                ->get();

                return view('pages.notification-list', compact('notifications', 'users', 'totalPages', 'totalRecords', 'start', 'end', 'page','wallet_empty','usersNotUsedFree'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function editNotification()
    {
        return view('pages.notification-list');
    }

    public function editNotificationApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $notification = Notification::find($req->filed_id);
                if ($notification) {
                    $notification->title = $req->title;
                    $notification->description = $req->did;
                    $notification->update();
                }
                return redirect()->route('notifications');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function notifcationStatus(Request $request)
    {
        return view('pages.notification-list');
    }

    public function notifcationStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {

                $notification = Notification::find($request->status_id);
                if ($notification) {
                    $notification->isActive = !$notification->isActive;
                    $notification->update();
                }
                return redirect()->route('notifications');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }



    public function sendNotification(Request $req)
    {
        try {
            $notification = Notification::find($req->notification_id);
            $req->userIds = ($req->userIds === ['all']) ? [] : $req->userIds;

            if ($req->userIds && count(json_decode(json_encode($req->userIds))) > 0) {
                // Send notification to specific users
                foreach (json_decode(json_encode($req->userIds)) as $user) {
                    $this->sendNotificationToUser($notification, $user);
                }
            } elseif ($req->role && $req->role == 'User') {
                // Send notification to all users
                $this->sendNotificationToAllUsers($notification);
            } elseif ($req->role && $req->role == 'Astrologer') {
                // Send notification to all astrologers
                $this->sendNotificationToAstrologers($notification);
            } elseif ($req->role == 'User Never Recharged') {
                // Send notification to users who never recharged
                $this->sendNotificationToUsersNeverRecharged($notification);
            } elseif ($req->role == 'User Not Used Free Chat/Call') {
                // Send notification to users who haven't used free chat/call
                $this->sendNotificationToUsersNotUsedFreeChatOrCall($notification);
            } else {
                // Send notification to all users by default
                $this->sendNotificationToAll($notification);
            }

            return response()->json([
                'success' => ['Send Notification Successfully'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => [$e->getMessage()]]);
        }
    }




    private function sendNotificationToUser($notification, $user)
    {
        $userDeviceDetail = DB::table('user_device_details')->where('userId', '=', $user)->get();

        if ($userDeviceDetail->isNotEmpty()) {
            $response = FCMService::send(
                $userDeviceDetail,
                [
                    'title' => $notification->title,
                    'body' => ['description' => $notification->description],
                ]
            );

            $response = collect([$response]);

            if (isset($response[0]['success']) && $response[0]['success'] == 1) {
                $notificationData = [
                    'userId' => $user,
                    'title' => $notification->title,
                    'description' => $notification->description,
                    'notificationId' => $req->notification_id,
                    'createdBy' => auth()->user()->id,
                    'modifiedBy' => auth()->user()->id,
                ];

                DB::table('user_notifications')->insert($notificationData);
            }
        }
    }

    private function sendNotificationToAllUsers($notification)
    {
        //$userDeviceDetails = DB::table('user_device_details')->get();
    $userDeviceDetails = DB::table('user_device_details')
            ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
            ->where('user_roles.roleId', '=', 3)
            ->where('isActive', 1)
            ->where('isDelete', 0)
            ->select('user_device_details.*')
            ->get();

        if ($userDeviceDetails->isNotEmpty()) {
            foreach ($userDeviceDetails as $detail) {
                $this->sendNotificationToUser($notification, $detail->userId);
            }
        }
    }

    private function sendNotificationToAll($notification)
    {
        $userDeviceDetails = DB::table('user_device_details')->get();

        if ($userDeviceDetails->isNotEmpty()) {
            foreach ($userDeviceDetails as $detail) {
                $this->sendNotificationToUser($notification, $detail->userId);
            }
        }
    }

    private function sendNotificationToAstrologers($notification)
    {
        $userDeviceDetail = DB::table('user_device_details')
            ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
            ->where('user_roles.roleId', '=', 2)
            ->where('isActive', 1)
            ->where('isDelete', 0)
            ->select('user_device_details.*')
            ->get();

        if ($userDeviceDetail->isNotEmpty()) {
            foreach ($userDeviceDetail as $detail) {
                $this->sendNotificationToUser($notification, $detail->userId);
            }
        }
    }

    private function sendNotificationToUsersNeverRecharged($notification)
    {
        $userDeviceDetails = DB::table('user_device_details')
            ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
            ->where('user_roles.roleId', '=', 3)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_wallets')
                    ->whereRaw('user_device_details.userId = user_wallets.userId');
            })
            ->get();

        if ($userDeviceDetails->isNotEmpty()) {
            foreach ($userDeviceDetails as $detail) {
                $this->sendNotificationToUser($notification, $detail->userId);
            }
        }
    }

    private function sendNotificationToUsersNotUsedFreeChatOrCall($notification)
    {
        $userDeviceDetails = DB::table('user_device_details')
            ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
            ->where('user_roles.roleId', '=', 3)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('chatrequest')
                    ->whereRaw('user_device_details.userId = chatrequest.userId')
                    ->orWhereExists(function ($subquery) {
                        $subquery->select(DB::raw(1))
                            ->from('callrequest')
                            ->whereRaw('user_device_details.userId = callrequest.userId');
                    });
            })
            ->get();

        if ($userDeviceDetails->isNotEmpty()) {
            foreach ($userDeviceDetails as $detail) {
                $this->sendNotificationToUser($notification, $detail->userId);
            }
        }
    }




}
