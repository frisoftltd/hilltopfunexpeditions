<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceCategory;
use Illuminate\Http\Request;

class PriceCategoryController extends Controller
{
    public function index()
    {
        $pageTitle = 'Price Categories';
        $priceCategories = PriceCategory::ordered()->paginate(getPaginate());
        return view('admin.price_category.index', compact('pageTitle', 'priceCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:60',
            'sort_order' => 'nullable|integer',
        ]);

        $priceCategory = new PriceCategory();
        $priceCategory->name = $request->name;
        $priceCategory->sort_order = $request->sort_order ?? 0;
        $priceCategory->status = 1;
        $priceCategory->save();

        $notify[] = ['success', 'Price category added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:price_categories,id',
            'name' => 'required|string|max:60',
            'sort_order' => 'nullable|integer',
        ]);

        $priceCategory = PriceCategory::findOrFail($request->id);
        $priceCategory->name = $request->name;
        $priceCategory->sort_order = $request->sort_order ?? 0;
        $priceCategory->save();

        $notify[] = ['success', 'Price category updated successfully'];
        return back()->withNotify($notify);
    }

    public function statusChange($id)
    {
        $priceCategory = PriceCategory::findOrFail($id);
        $priceCategory->status = $priceCategory->status == 1 ? 0 : 1;
        $priceCategory->save();

        $notify[] = ['success', 'Status changed successfully'];
        return back()->withNotify($notify);
    }
}
