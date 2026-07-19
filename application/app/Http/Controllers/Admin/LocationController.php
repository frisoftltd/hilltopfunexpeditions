<?php

namespace App\Http\Controllers\Admin;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function index(){
        $pageTitle = 'Locations';
        $locations = Location::latest()->paginate(getPaginate());
        return view('admin.location.index',compact('pageTitle','locations'));
    }

    public function create(){
        $pageTitle = 'Locations';
        return view('admin.location.create',compact('pageTitle'));
    }

    public function store(Request $request){
   
        $request->validate([
            'name'=>'required',
            'location'=>'nullable',
            'latitude'=>'required',
            'longitude'=>'required',
            'status'=>'required|in:0,1',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);
     
        $location = new Location();
        $location->name = $request->name;
        $location->location = $request->location;
        $location->latitude = $request->latitude;
        $location->longitude = $request->longitude;
        $location->status = 1;
        $location->image = '';

        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                try {
                    $filePath = fileUploader($request->file('image'), getFilePath('location') , getFileSize('location'));
                    $location->image = $filePath;
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload your file'];
                    return back()->withNotify($notify);
                }
            }
        }

        $location->save();

        $notify[] = ['success', 'Location has been created successfully'];
        return back()->withNotify($notify);
    }

    public function edit($id){
        $pageTitle="Update Location";
        $location =  Location::findOrFail($id);
        return view('admin.location.edit',compact('pageTitle','location'));
    }
    
    public function update(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'location'=>'required',
            'latitude'=>'required',
            'longitude'=>'required',
            'status'=>'nullable|in:0,1',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $location = Location::findOrFail($id);
        $location->name = $request->name;
        $location->location = $request->location;
        $location->latitude = $request->latitude ?? $location->latitude;
        $location->longitude = $request->longitude ?? $location->longitude;
        $location->status = $request->status ?? $location->status;

        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                try {
                    $old =  $location->image;
                    $filePath = fileUploader($request->file('image'), getFilePath('location') , getFileSize('location'),$old);
                    $location->image = $filePath;
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload your file'];
                    return back()->withNotify($notify);
                }
            }
        }

        $location->save();

        $notify[] = ['success', 'Location has been updated successfully'];
        return to_route('admin.location.index')->withNotify($notify);
    }
}
