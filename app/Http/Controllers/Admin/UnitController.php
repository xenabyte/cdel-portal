<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Unit;
use App\Models\Staff;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class UnitController extends Controller
{
    public function units(){
        $units = Unit::all();
        $staffMembers = Staff::all();

        return view('admin.units', [
            'units' => $units,
            'staffMembers' => $staffMembers
        ]);
    }

    public function addUnit(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        $newUnit = [
            'name' => $request->name,
            'slug' => $slug
        ];
        
        if(Unit::create($newUnit)){
            alert()->success('Unit created successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updateUnit(Request $request){
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$unit = Unit::find($request->unit_id)){
            alert()->error('Oops', 'Invalid Unit ')->persistent('Close');
            return redirect()->back();
        }

        
        if(!empty($request->name) && $request->name != $unit->name){
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
            $unit->name = $request->name;
            $unit->slug = $slug;
        }

        if(!empty($request->unit_head_id) && $request->unit_head_id!= $unit->unit_head_id){
            $unit->unit_head_id = $request->unit_head_id;
        }

        if($unit->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function deleteUnit(Request $request){
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$unit = Unit::find($request->unit_id)){
            alert()->error('Oops', 'Invalid Unit ')->persistent('Close');
            return redirect()->back();
        }

        if($unit->delete()){
            alert()->success('Unit Deleted', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }
}
