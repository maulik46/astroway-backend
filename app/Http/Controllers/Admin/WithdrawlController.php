<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Gift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\services\FCMService;
use Carbon\Carbon;

define('LOGINPATH', '/admin/login');

class WithdrawlController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function setWithdrawlPage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $withdrawRequest = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
                $withdrawRequest = $withdrawRequest->select('withdrawrequest.*', 'astrologers.name', 'astrologers.contactNo', 'astrologers.profileImage', 'astrologers.userId');

                $withdrawRequest = $withdrawRequest->orderBy('id', 'DESC');
                $withdrawRequest->skip($paginationStart);
                $withdrawRequest->take($this->limit);
                $withdrawlRequest = $withdrawRequest->get();

                $withdrawRequestCount = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
                $withdrawRequestCount = $withdrawRequestCount->count();
                $totalPages = ceil($withdrawRequestCount / $this->limit);
                $totalRecords = $withdrawRequestCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.withdrawl', compact('withdrawlRequest', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function getWithDrawlRequest(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $withdrawRequest = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId')
                    ->join('withdrawmethods', 'withdrawmethods.id', '=', 'withdrawrequest.paymentMethod');
                $withdrawRequest = $withdrawRequest->select('withdrawrequest.*', 'astrologers.name', 'astrologers.contactNo', 'astrologers.profileImage', 'astrologers.userId','withdrawmethods.id as withdrawmethodid','withdrawmethods.method_name','withdrawmethods.method_id');

                $withdrawRequest = $withdrawRequest->orderBy('id', 'DESC');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $withdrawRequest = $withdrawRequest->where(function ($q) use ($searchString) {
                        $q->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $withdrawRequest->skip($paginationStart);
                $withdrawRequest->take($this->limit);
                $withdrawlRequest = $withdrawRequest->get();

                $withdrawRequestCount = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
                if ($searchString) {
                    $withdrawRequestCount = $withdrawRequestCount->where(function ($q) use ($searchString) {
                        $q->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $withdrawRequestCount = $withdrawRequestCount->count();

                $totalPages = ceil($withdrawRequestCount / $this->limit);
                $totalRecords = $withdrawRequestCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.withdrawl', compact('withdrawlRequest', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function releaseAmount(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $withdrawRequest = array('status' => 'Released',
                );
                DB::table('withdrawrequest')
                    ->where('id', $request->del_id)
                    ->update($withdrawRequest);

                $userDeviceDetail = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', 'withdrawrequest.astrologerId')
                    ->join('user_device_details', 'user_device_details.userId', 'astrologers.userId')
                    ->where('withdrawrequest.id', '=', $request->del_id)
                    ->select('user_device_details.*','withdrawrequest.withdrawAmount')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => $userDeviceDetail[0]->withdrawAmount.' Receive from astroway admin',
                            'body' => ['description' => 'Payment release from admin successfully','notificationType'=>7],
                        ]
                    );
                }
                return redirect()->route('withdrawalRequests');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }



    // Wallet History 
    public function getWalletHistory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
            
                    $wallet = DB::table('payment')
                        ->join('users', 'users.id', '=', 'payment.userId')
                        ->select('payment.*','users.name as userName','users.profile as userProfile','users.contactNo as userContact')
                        ->where('payment.paymentStatus', 'success') 
                        ->orWhere('payment.paymentStatus', 'failed');


                $wallet = $wallet->orderBy('id', 'DESC');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $wallet = $wallet->where(function ($q) use ($searchString) {
                        $q->where('users.name', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $wallet->skip($paginationStart);
                $wallet->take($this->limit);
                $wallet = $wallet->get();
                

                $walletCount = DB::table('payment')
                ->join('users', 'users.id', '=', 'payment.userId')
                ->select('payment.*','users.name as userName','users.profile as userProfile')
                ->where('payment.paymentStatus', 'success') 
                ->orWhere('payment.paymentStatus', 'failed');
                if ($searchString) {
                    $walletCount = $walletCount->where(function ($q) use ($searchString) {
                        $q->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $walletCount = $walletCount->count();

                $totalPages = ceil($walletCount / $this->limit);
                $totalRecords = $walletCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.wallet-history', compact('wallet', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }


 
    public function getwithdrawalMethods(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $methods=DB::table('withdrawmethods')->get();
                return view('pages.withdrawl-methods', compact('methods',));
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }


    public function withdrawStatusApi(Request $request)
    {try {
        if (Auth::guard('web')->check()) {
            $affected = DB::table('withdrawmethods')
                            ->where('id', $request->status_id)
                            ->update(['isActive' => DB::raw('NOT isActive')]); 
            if ($affected > 0) {
                return redirect()->route('withdrawalMethods');
            }
        } else {
            return redirect(LOGINPATH);
        }
        
    } catch (Exception $e) {
        return dd($e->getMessage());
    }
    }


    public function editwithdrawApi(Request $req)
    {
        try {
            // if (Auth::guard('web')->check()) {
            $affected = DB::table('withdrawmethods')
                            ->where('id', $req->filed_id)
                            ->update(['method_name' => $req->name,'updated_at' => Carbon::now()]);
            
            if ($affected > 0) {
                return redirect()->route('withdrawalMethods');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    
}
