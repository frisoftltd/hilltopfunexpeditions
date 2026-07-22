<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\TourDeparture;
use App\Models\TourPackage;
use App\Traits\TourDepartureService;
use Illuminate\Http\Request;

class TourDepartureController extends Controller
{
    use TourDepartureService;

    public function store(Request $request, $tourPackageId)
    {
        $tourPackage = TourPackage::where('id', $tourPackageId)
            ->where('user_type', 'agency')
            ->where('user_id', auth('agency')->id())
            ->firstOrFail();

        return $this->storeDeparture($request, $tourPackage);
    }

    public function update(Request $request, $id)
    {
        $departure = $this->ownedDeparture($id);
        return $this->updateDeparture($request, $departure);
    }

    public function destroy($id)
    {
        $departure = $this->ownedDeparture($id);
        return $this->destroyDeparture($departure);
    }

    private function ownedDeparture($id): TourDeparture
    {
        return TourDeparture::whereHas('tourPackage', function ($query) {
            $query->where('user_type', 'agency')->where('user_id', auth('agency')->id());
        })->findOrFail($id);
    }
}
