<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agency;
use App\Models\Commission;
use App\Http\Controllers\Controller;

class CommissionController extends Controller
{
    public function index()
    {
        $pageTitle = 'Commissions';

        $totalCollected = Commission::where('status', Commission::COLLECTED)->sum('commission_amount');
        $totalOwed = Commission::where('status', Commission::OWED)->sum('commission_amount');
        $grandTotal = $totalCollected + $totalOwed;

        $agencyBreakdown = Agency::query()
            ->whereHas('commissions')
            ->withSum(['commissions as collected_amount' => function ($query) {
                $query->where('status', Commission::COLLECTED);
            }], 'commission_amount')
            ->withSum(['commissions as owed_amount' => function ($query) {
                $query->where('status', Commission::OWED);
            }], 'commission_amount')
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        $owedCommissions = Commission::with('agency', 'tourBooking.tour_package')
            ->where('status', Commission::OWED)
            ->orderBy('id', 'desc')
            ->paginate(getPaginate(), ['*'], 'owed_page');

        return view('admin.commission.index', compact('pageTitle', 'totalCollected', 'totalOwed', 'grandTotal', 'agencyBreakdown', 'owedCommissions'));
    }

    public function markPaid($id)
    {
        $commission = Commission::where('status', Commission::OWED)->findOrFail($id);
        $commission->status = Commission::COLLECTED;
        $commission->paid_at = now();
        $commission->save();

        $notify[] = ['success', 'Commission marked as paid'];
        return back()->withNotify($notify);
    }
}
