<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

define('MONTHGROUP', 'month(created_at)');

class DashboardController extends Controller
{
    public $path;
	
	public function termscond(Request $request)
	{
		try {
  
            return response()->json([
                'response' => url('terms-condition'),
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
	
	public function privacyPolicy(Request $request)
	{
		
        try {

            return response()->json([
                'response' => url('privacyPolicy'),
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

    public function getDashboard(Request $request)
    {
        try {

            if (Auth::guard('web')->check()) {
                $totalCallRequest = DB::table('callrequest')
                    ->count();
                $totalChatRequest = DB::table('chatrequest')
                    ->count();
                $totalReportRequest = DB::table('user_reports')
                    ->count();
                $totalCustomer = DB::table('users')
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('user_roles.roleId', '=', '3')
                    ->where('users.isActive', '=', true)
                    ->where('users.isDelete', '=', false)
                    ->count();
                $totalAstrologer = DB::table('astrologers')
                    ->count();

                $totalOrders = DB::table('order_request')
                    ->where('orderType','=','astromall')
                    ->count();

                $totalStories = DB::table('astrologer_stories')
                ->count();
             

                $totalEarning = DB::table('admin_get_commissions')
                    ->sum('amount');
                $topAstrologers = DB::table('astrologers')
                    ->where('isVerified', '=', true)
                    ->orderBy('totalOrder', 'desc')
                    ->limit(10)
                    ->get();
                if ($topAstrologers && count($topAstrologers) > 0) {
                    foreach ($topAstrologers as $astrologer) {
                        $allSkill = array_map('intval', explode(',', $astrologer->allSkill));
                        $languages = array_map('intval', explode(',', $astrologer->languageKnown));
                        $allSkill = DB::table('skills')
                            ->whereIn('id', $allSkill)
                            ->select('name')
                            ->get();
                        $skill = $allSkill->pluck('name')->all();
                        $astrologer->allSkill = implode(",", $skill);
                        $languageKnown = DB::table('languages')
                            ->whereIn('id', $languages)
                            ->select('languageName')
                            ->get();
                        $languageKnown = $languageKnown->pluck('languageName')->all();
                        $astrologer->languageKnown = implode(",", $languageKnown);
                        $totalCall = DB::table('callrequest')
                            ->where('astrologerId', '=', $astrologer->id)
                            ->count();
                        $astrologer->totalCallRequest = $totalCall;
                        $totalChat = DB::table('chatrequest')
                            ->where('astrologerId', '=', $astrologer->id)
                            ->count();
                        $astrologer->totalChatRequest = $totalChat;
                    }
                }
                $currentDate = Carbon::now();
                $last12Months = [];
                $last12Months[] = $currentDate->format('Y-m');
                for ($i = 1; $i <= 11; $i++) {
                    $lastMonth = $currentDate->subMonth();
                    $last12Months[] = $lastMonth->format('Y-m');
                }
                $last12Months = array_reverse($last12Months);
                $call = [];
                $chat = [];
                $report = [];
                $ti = [];
                for ($i = 0; $i < count($last12Months); $i++) {
                    $last12monthyear = array_map('intval', explode('-', $last12Months[$i]))[0];
                    $last12monthofmonth = array_map('intval', explode('-', $last12Months[$i]))[1];
                    $callRequest = DB::table('callrequest')
                        ->selectRaw('month(created_at) as callMonth')
                        ->selectRaw('count(id) as totalCall')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $chatRequest = DB::table('chatrequest')
                        ->selectRaw('month(created_at) as chatMonth')
                        ->selectRaw('count(id) as totalChat')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $reportRequest = DB::table('user_reports')
                        ->selectRaw('month(created_at) as month')
                        ->selectRaw('count(id) as totalReport')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $monthyCommission = DB::table('admin_get_commissions')
                        ->selectRaw('month(created_at) as month')
                        ->selectRaw('sum(amount) as totalEarning')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $dateObj = DateTime::createFromFormat('!m', $last12monthofmonth);
                    $data = array(
                        'callMonth' => $dateObj->format('M'),
                        'callYear' => $last12monthyear,
                        'totalCall' => $callRequest && count($callRequest) > 0 ? $callRequest[0]->totalCall : 0,
                    );
                    $chatData = array(
                        'chatMonth' => $dateObj->format('M'),
                        'chatYear' => $last12monthyear,
                        'totalChat' => $chatRequest && count($chatRequest) > 0 ? $chatRequest[0]->totalChat : 0,
                    );
                    $reportData = array(
                        'month' => $dateObj->format('M'),
                        'reportYear' => $last12monthyear,
                        'totalReport' => $reportRequest && count($reportRequest) > 0 ? $reportRequest[0]->totalReport : 0,
                    );
                    $monthCommission = array(
                        'month' => $dateObj->format('M'),
                        'commissionYear' => $last12monthyear,
                        'totalEarning' => $monthyCommission && count($monthyCommission) > 0 ? $monthyCommission[0]->totalEarning : 0,
                    );
                    array_push($call, $data);
                    array_push($chat, $chatData);
                    array_push($report, $reportData);
                    array_push($ti, $monthCommission);
                }
                $unverifiedAstrologer = DB::table('astrologers')
                    ->where('isVerified', '=', 'false')
                    ->get();
                foreach ($unverifiedAstrologer as $astrologers) {
                    $allSkill = array_map('intval', explode(',', $astrologers->allSkill));
                    $languages = array_map('intval', explode(',', $astrologers->languageKnown));
                    $allSkill = DB::table('skills')
                        ->whereIn('id', $allSkill)
                        ->select('name')
                        ->get();
                    $skill = $allSkill->pluck('name')->all();
                    $astrologers->allSkill = implode(",", $skill);
                    $languageKnown = DB::table('languages')
                        ->whereIn('id', $languages)
                        ->select('languageName')
                        ->get();
                    $languageKnown = $languageKnown->pluck('languageName')->all();
                    $astrologers->languageKnown = implode(",", $languageKnown);
                }
                $dashboardData = ([
                    "totalCallRequest" => $totalCallRequest,
                    "totalChatRequest" => $totalChatRequest,
                    "totalReportRequest" => $totalReportRequest,
                    "topAstrologer" => $topAstrologers,
                    "totalEarning" => $totalEarning,
                    "totalCustomer" => $totalCustomer,
                    "totalAstrologer" => $totalAstrologer,
                    "monthlyCommission" => $ti,
                    "monthlyCallRequest" => $call,
                    "monthlyChatRequest" => $chat,
                    "monthlyReportRequest" => $report,
                    "unverifiedAstrologer" => $unverifiedAstrologer,
                    "totalOrders" =>$totalOrders,
                    "totalStories" =>$totalStories
                ]);
                $labels = [];
                $data = [];
                $callData = [];
                $chatData = [];
                $reportData = [];
                $dashboardData = [$dashboardData];
                foreach ($dashboardData[0]['monthlyCommission'] as $label) {
                    $la = $label['month'] . ' ' . $label['commissionYear'];
                    array_push($labels, $la);
                    array_push($data, $label['totalEarning']);
                }

                foreach ($dashboardData[0]['monthlyCallRequest'] as $call) {
                    array_push($callData, $call['totalCall']);
                }
                foreach ($dashboardData[0]['monthlyChatRequest'] as $chat) {
                    array_push($chatData, $chat['totalChat']);
                }
                foreach ($dashboardData[0]['monthlyReportRequest'] as $report) {
                    array_push($reportData, $report['totalReport']);
                }
                $result = $dashboardData;
                return view('pages.dashboard-overview-1', compact('result', 'labels', 'data', 'callData', 'chatData', 'reportData'));
            } else {
                return redirect('admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }

    }

    public function verifiedAstrologer(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $eid = $request->filed_id;
                $astrologer = Astrologer::find($eid);
                $astrologer->isVerified = !$astrologer->isVerified;
                $astrologer->update();
                return redirect()->route('getDashboard');
            } else {
                return redirect('admin/login');
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
