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

use App\Models\Career;
use App\Models\Staff;
use App\Models\Session;
use App\Models\JobVacancy;
use App\Models\JobApplication;
use App\MOdels\Unit;
use App\MOdels\Faculty;
use App\MOdels\Department;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class CareerController extends Controller
{
    //

    public function jobVacancy(){

        return view('admin.jobVacancy');
    }

    public function prospectiveStaff(){

        return view('admin.prospectiveStaff');
    }
}
