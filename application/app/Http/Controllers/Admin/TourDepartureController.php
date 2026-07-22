<?php

namespace App\Http\Controllers\Admin;

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
        $tourPackage = TourPackage::findOrFail($tourPackageId);
        return $this->storeDeparture($request, $tourPackage);
    }

    public function update(Request $request, $id)
    {
        $departure = TourDeparture::findOrFail($id);
        return $this->updateDeparture($request, $departure);
    }

    public function destroy($id)
    {
        $departure = TourDeparture::findOrFail($id);
        return $this->destroyDeparture($departure);
    }
}
