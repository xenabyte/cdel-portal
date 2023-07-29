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
    //]

    public function programmeCategory(){

        $programmeCategories = ProgrammeCategory::get();
        
        return view('admin.programmeCategory', [
            'programmeCategories' => $programmeCategories
        ]);
    }
}
