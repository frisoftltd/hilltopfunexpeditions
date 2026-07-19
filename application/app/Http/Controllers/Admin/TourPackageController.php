<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\TourPackage;
use App\Traits\TourService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class TourPackageController extends Controller
{
    use TourService;
    public function index()
    {
      
        $pageTitle = 'Tour Package Lists';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('allTour');
        return view('admin.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

    public function create()
    {
        $pageTitle = 'Create Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        return view('admin.tour_package.create', compact('pageTitle', 'categories'));
    }

    public function edit($id)
    {
        $pageTitle = 'Tour Package Edit';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackage =  TourPackage::with('category')->where('id',$id)->first();
        return view('admin.tour_package.edit', compact('pageTitle', 'categories', 'tourPackage'));
    }

    public function active()
    {
        $pageTitle = 'Pending Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('active');
        return view('admin.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }


    public function pending()
    {
        $pageTitle = 'Pending Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('pending');
        return view('admin.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

    public function cancelled()
    {
        $pageTitle = 'Cancelled Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('cancelled');
        return view('admin.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

    public function running()
    {
        $pageTitle = 'Running Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('running');
        return view('admin.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

    public function expired()
    {
        $pageTitle = 'Expired Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('expired');
        return view('admin.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

    public function allAgency(Request $request)
    {
        $pageTitle = 'Agency Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = TourPackage::with('category','TourPackagePrimaryImage','agency')
        ->where('user_type','agency')
        ->orderBy('id', 'desc');
        if ($request->search || $request->category_id) {

            $search = $request->search;
            $categoryId = $request->category_id;
            $tourPackages = $tourPackages->where(function ($query) use ($search, $categoryId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                } if ($search) {
                    $query->where('title', 'like', "%$search%");
                }
            });
        }

        $tourPackages = $tourPackages->paginate(getPaginate());
 
        return view('admin.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

    public function statusChange($id){
        $tourPackage = TourPackage::where('id',$id)->where('user_id',auth('admin')->id())->where('user_type','admin')->first();
    
        $tourPackage->status = $tourPackage->status == 1 ? 0 : 1;
        $tourPackage->save();
        $notify[] = ['success', 'Status change has been successfully'];
        return back()->withNotify($notify);
    }

    public function myList(Request $request)
    {
        $pageTitle = 'My Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = TourPackage::with('category','TourPackagePrimaryImage','agency')
        ->where('user_type','admin')
        ->where('user_id',auth('admin')->id())
        ->orderBy('id', 'desc');

        if ($request->search || $request->category_id) {

            $search = $request->search;
            $categoryId = $request->category_id;
            $tourPackages = $tourPackages->where(function ($query) use ($search, $categoryId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                } if ($search) {
                    $query->where('title', 'like', "%$search%");
                }
            });
        }

        $tourPackages = $tourPackages->paginate(getPaginate());
        return view('admin.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

 
    protected function tourPackageData($scope = null)
    {
        if ($scope) {
            $tourPackages = TourPackage::$scope();
        } else {
            $tourPackages = TourPackage::query();
        }
        //search
        $request = request();
        if ($request->search || $request->category_id) {

            $search = $request->search;
            $categoryId = $request->category_id;
            $tourPackages = $tourPackages->where(function ($query) use ($search, $categoryId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                } if ($search) {
                    $query->where('title', 'like', "%$search%");
                }
            });
        }
        return $tourPackages->with('category','agency','TourPackagePrimaryImage','agency')->orderBy('id', 'desc')->paginate(getPaginate());
    }
  
}
