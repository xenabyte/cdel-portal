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

use App\Models\ProgrammeCategory;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class ProgrammeController extends Controller
{
    //

    public function programmeCategory(){

        $programmeCategories = ProgrammeCategory::get();
        
        return view('admin.programmeCategory', [
            'programmeCategories' => $programmeCategories
        ]);
    }

    public function addProgrammeCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|unique:programme_categories',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newLevel = [
            'category' => $request->category,
        ];
        
        if(ProgrammeCategory::create($newLevel)){
            alert()->success('Programme category added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateProgrammeCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$programmeCategory = ProgrammeCategory::find($request->category_id)){
            alert()->error('Oops', 'Invalid Programme Category ')->persistent('Close');
            return redirect()->back();
        }

        $programmeCategory->category = $request->category;

        if($programmeCategory->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function deleteProgrammeCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$programmeCategory = ProgrammeCategory::find($request->category_id)){
            alert()->error('Oops', 'Invalid Prograamme Category ')->persistent('Close');
            return redirect()->back();
        }
        
        if($programmeCategory->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

}
