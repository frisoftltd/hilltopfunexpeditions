<?php

namespace App\Http\Controllers\Agency;

use App\Models\Commission;
use App\Http\Controllers\Controller;

class CommissionController extends Controller
{
    public function index()
    {
        $pageTitle = 'Commissions';
        $agencyId = auth('agency')->id();

        $totalPaid = Commission::where('agency_id', $agencyId)->where('status', Commission::COLLECTED)->sum('commission_amount');
        $totalOwed = Commission::where('agency_id', $agencyId)->where('status', Commission::OWED)->sum('commission_amount');

        $commissions = Commission::with('tourBooking.tour_package')
            ->where('agency_id', $agencyId)
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view($this->activeTemplate . 'agency.commission.index', compact('pageTitle', 'totalPaid', 'totalOwed', 'commissions'));
    }
}
