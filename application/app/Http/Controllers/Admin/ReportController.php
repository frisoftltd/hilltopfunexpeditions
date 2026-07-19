<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction(Request $request)
    {
    
        $pageTitle = 'Transaction Logs';

        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::with('user')->orderBy('id','desc')->where('user_id', '!=', 0);
        if ($request->search) {
            $search = request()->search;
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('trx', 'like', "%$search%")->orWhereHas('user', function ($user) use ($search) {
                    $user->where('username', 'like', "%$search%");
                });
            });
        }

        if ($request->type) {
            $transactions = $transactions->where('trx_type',$request->type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark',$request->remark);
        }

        //date search
        if($request->date) {
            $date = explode('-',$request->date);
            $request->merge([
                'start_date'=> trim($date[0]),
                'end_date'  => trim($date[1])
            ]);
            $request->validate([
                'start_date'    => 'required|date_format:m/d/Y',
                'end_date'      => 'nullable|date_format:m/d/Y'
            ]);
            if($request->end_date) {
                $endDate = Carbon::parse($request->end_date)->addHours(23)->addMinutes(59)->addSecond(59);
                $transactions   = $transactions->whereBetween('created_at', [Carbon::parse($request->start_date), $endDate]);
            }else{
                $transactions   = $transactions->whereDate('created_at', Carbon::parse($request->start_date));
            }
        }

        $transactions = $transactions->paginate(getPaginate());
        return view('admin.reports.transactions', compact('pageTitle', 'transactions','remarks'));
    }

    public function loginHistory(Request $request)
    {
       
        $loginLogs = UserLogin::orderBy('id','desc')->with('user')->where('user_id', '!=', 0);
        $pageTitle = 'User Login History';
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'User Login History - ' . $search;
            $loginLogs = $loginLogs->whereHas('user', function ($query) use ($search) {
                $query->where('username', $search);
            });
        }
        $loginLogs = $loginLogs->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
     
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip',$ip)->orderBy('id','desc')->where('user_id', '!=', 0)->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs','ip'));

    }

    public function notificationHistory(Request $request){
        $pageTitle = 'Notification History';
        $logs = NotificationLog::orderBy('id','desc');
        $search = $request->search;
        if ($search) {
            $logs = $logs->whereHas('user', function ($user) use ($search) {
                $user->where('username', 'like',"%$search%");
            });
        }
        $logs = $logs->with('user')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle','logs'));
    }

    public function emailDetails($id){
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle','email'));
    }

    // agency
    public function agencyTransaction(Request $request)
    {
        $pageTitle = 'Agency Transaction Logs';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::with('agency')->where('agency_id', '!=', 0)->orderBy('id', 'desc');
        if ($request->search) {
            $search = request()->search;
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('trx', 'like', "%$search%")->orWhereHas('agency', function ($user) use ($search) {
                    $user->where('username', 'like', "%$search%");
                });
            });
        }


        if ($request->type) {
            $transactions = $transactions->where('trx_type', $request->type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }

        //  date search
        if ($request->date) {
            $date = explode('-', $request->date);
            $request->merge([
                'start_date' => trim($date[0]),
                'end_date' => trim($date[1])
            ]);
            $request->validate([
                'start_date' => 'required|date_format:m/d/Y',
                'end_date' => 'nullable|date_format:m/d/Y'
            ]);
            if ($request->end_date) {
                $endDate = Carbon::parse($request->end_date)->addHours(23)->addMinutes(59)->addSecond(59);
                $transactions = $transactions->whereBetween('created_at', [Carbon::parse($request->start_date), $endDate]);
            } else {
                $transactions = $transactions->whereDate('created_at', Carbon::parse($request->start_date));
            }
        }

        $transactions = $transactions->paginate(getPaginate());
        return view('admin.agencies.reports.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }
    public function agencyLoginHistory(Request $request)
    {
        $loginLogs = UserLogin::orderBy('id', 'desc')->with('agency')->where('agency_id', '!=', 0);
        $pageTitle = 'Agency Login History';
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Agency Login History - ' . $search;
            $loginLogs = $loginLogs->whereHas('agency', function ($query) use ($search) {
                $query->where('username', $search);
            });
        }
        $loginLogs = $loginLogs->paginate(getPaginate());
        return view('admin.agencies.reports.logins', compact('pageTitle', 'loginLogs'));
    }
    public function agencyLoginIpHistory($ip)
    {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip', $ip)->where('agency_id', '!=', 0)->orderBy('id', 'desc')->with('agency')->paginate(getPaginate());
        return view('admin.agencies.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));

    }
    public function agencyNotificationHistory(Request $request)
    {

        $pageTitle = 'Notification History';
        $logs = NotificationLog::orderBy('id', 'desc');
        $search = $request->search;
        if ($search) {
            $logs = $logs->whereHas('agency', function ($user) use ($search) {
                $user->where('username', 'like', "%$search%");
            });

        }
        $logs = $logs->with('agency')->whereNotNull('agency_id')->paginate(getPaginate());
        return view('admin.agencies.reports.notification_history', compact('pageTitle', 'logs'));
    }
    public function agencyEmailDetails($id)
    {
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.agencies.reports.email_details', compact('pageTitle', 'email'));
    }
}
