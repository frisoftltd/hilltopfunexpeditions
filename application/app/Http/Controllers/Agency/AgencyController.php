<?php

namespace App\Http\Controllers\Agency;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\Artwork;
use App\Models\Deposit;
use App\Models\OrderItem;
use App\Lib\FormProcessor;
use App\Models\Collection;
use App\Models\Withdrawal;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Lib\GoogleAuthenticator;
use App\Models\ArtworkCommission;
use App\Http\Controllers\Controller;

class AgencyController extends Controller
{
    public function home()
    {
        $pageTitle = 'Dashboard';
        $agency =  agency();
        $myBooked =  TourPackage::with('tour_bookings.deposit', 'tour_bookings.user', 'tour_bookings.owner', 'TourPackagePrimaryImage')->where('user_id',auth('agency')->id())->where('user_type','agency')->paginate(getPaginate());
     
        $widget['total_tour_package'] =  TourPackage::where('user_type','agency')->where('user_id', auth('agency')->id())->count();
        $widget['total_approved_tour_package'] =  TourPackage::agencyApproved()->count();
        $widget['total_pending_tour_package'] =  TourPackage::agencyPending()->count();
        $widget['total_support_ticker'] =  SupportTicket::where('agency_id',auth('agency')->id())
        ->count();
        $widget['total_bookings'] =  TourBooking::where('owner_id',auth('agency')->id())->where('owner_type','agency')->count();
        $widget['total_open_support_ticker'] =  SupportTicket::where('agency_id',auth('agency')->id())
        ->where('status',0)->count();

        $widget['total_transaction'] =  Transaction::where('agency_id', $agency->id)->count();
        $widget['balance'] =   $agency->balance;

        $withdrawalsReport = Withdrawal::selectRaw("SUM(amount) as amount, MONTHNAME(created_at) as month_name, MONTH(created_at) as month_num")
        ->whereYear('created_at', date('Y'))
        ->whereStatus(1)
        ->where('user_id', $agency->id)
        ->groupBy('month_name', 'month_num')
        ->orderBy('month_num')
        ->get();
        $withdrawalsChart['labels'] = $withdrawalsReport->pluck('month_name');
        $withdrawalsChart['values'] = $withdrawalsReport->pluck('amount');

        $tour_packageReport = TourBooking::with('tour_package')
        ->selectRaw("SUM(price) as price, MONTHNAME(created_at) as month_name, MONTH(created_at) as month_num")
        ->whereYear('created_at', date('Y'))
        ->where('owner_id', $agency->id)
        ->where('owner_type', 'agency')
        ->groupBy('month_name', 'month_num')
        ->orderBy('month_num')
        ->get();
        
        $tourPackageChart['labels'] = $tour_packageReport->pluck('month_name');
        $tourPackageChart['values'] = $tour_packageReport->pluck('price');
      
        return view($this->activeTemplate . 'agency.dashboard', compact('pageTitle','widget','withdrawalsChart','myBooked','tourPackageChart'));
    }
    public function show2faForm()
    {
        $general = gs();
        $ga = new GoogleAuthenticator();
        $user = agency();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->site_name, $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate.'agency.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }
    public function create2fa(Request $request)
    {
        $user = agency();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code,$request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }
    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = agency();
        $response = verifyG2fa($user,$request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }
    public function transactions(Request $request)
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('agency_id',agencyId());

        if ($request->search) {
            $transactions = $transactions->where('trx',$request->search);
        }

        if ($request->type) {
            $transactions = $transactions->where('trx_type',$request->type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark',$request->remark);
        }

        $transactions = $transactions->orderBy('id','desc')->paginate(getPaginate());
        return view($this->activeTemplate.'agency.transactions', compact('pageTitle','transactions','remarks'));
    }
    public function kycForm()
    {
        if (agency()->kv == 2) {
            $notify[] = ['error','Your KYC is under review'];
            return to_route('agency.home')->withNotify($notify);
        }
        if (agency()->kv == 1) {
            $notify[] = ['error','You are already KYC verified'];
            return to_route('agency.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act','kyc')->first();
        return view($this->activeTemplate.'agency.kyc.form', compact('pageTitle','form'));
    }

    public function kycData()
    {
        $agency = agency();
        $pageTitle = 'KYC Data';
        return view($this->activeTemplate.'agency.kyc.info', compact('pageTitle','agency'));
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act','kyc')->first();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);
        $user = agency();
        $user->kyc_data = $userData;
        $user->kv = 2;
        $user->save();

        $notify[] = ['success','KYC data submitted successfully'];
        return to_route('agency.home')->withNotify($notify);

    }
    public function attachmentDownload($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name).'- attachments.'.$extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }
    public function userData()
    {
        $user = agency();
        if ($user->reg_step == 1) {
            return to_route('agency.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate.'agency.user_data', compact('pageTitle','user'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = agency();
        if ($user->reg_step == 1) {
            return to_route('agency.home');
        }
        $request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = [
            'country'=>$user->address->country,
            'address'=>$request->address,
            'state'=>$request->state,
            'zip'=>$request->zip,
            'city'=>$request->city,
        ];
        $user->reg_step = 1;
        $user->save();

        $notify[] = ['success','Registration process completed successfully'];
        return to_route('agency.home')->withNotify($notify);

    }
    public function artworkCommission(){
        $pageTitle  = 'Artwork Commissions';
        $artworkCommissions = ArtworkCommission::with( 'artwork')->where('agency_id',agencyId())->getSearch(['artwork:title'])->latest()->paginate(getPaginate());
        return view($this->activeTemplate.'agency.commissions', compact('pageTitle','artworkCommissions'));
    }
    public function artworkOrders(){
        $pageTitle  = 'Artwork Orders';
        $orders = OrderItem::with( ['order','artwork'])->where('agency_id',agencyId())->getSearch(['artwork:title'])->latest()->paginate(getPaginate());
        return view($this->activeTemplate.'agency.orders', compact('pageTitle','orders'));
    }

}
