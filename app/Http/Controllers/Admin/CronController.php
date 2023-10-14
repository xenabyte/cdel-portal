<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Faculty;
use App\Models\Department;
use App\Models\Programme;
use App\Models\ProgrammeCategory;
use App\Models\Course;
use App\Models\AcademicLevel;
use App\Models\Staff;
use App\Models\SessionSetting;
use App\Models\Student;
use App\Models\Guardian;

use App\Libraries\Result\Result;

use Illuminate\Support\Facades\Http;
use App\Jobs\SendGuardianOnboardingMail;


use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;


class CronController extends Controller
{

    public function populateFaculty(){

        try {
            // Fetch the API response
            $response = Http::get(env('FACULTY_API_URL'));
            $sessionSetting = SessionSetting::first();
            $academicSession = $sessionSetting->academic_session;

            // Check if the API call was successful
            if ($response->successful()) {
                $data = $response->json()['data'];

                $departmentNames = [];
                $programmeNames = [];
                $facultyNames = [];

                // Process the faculties
                foreach ($data as $facultyData) {
                    $facultyNeededData = ([
                        'name' => $facultyData['name'],
                        'web_id' => $facultyData['id'],
                        'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $facultyData['name'])))
                    ]);

                    $facultyNames[] = $facultyData['name'];
                    if(!$faculty = Faculty::where('web_id', $facultyData['id'])->first()){
                        $faculty = Faculty::create($facultyNeededData);
                    }else{
                        $faculty->update($facultyNeededData);
                    }

                    // Process the departments for each faculty
                    foreach ($facultyData['departments'] as $departmentData) {
                        $departmentNeededData = ([
                            'name' => $departmentData['name'],
                            'faculty_id' => $departmentData['facultyid'],
                            'web_id' => $departmentData['id'],
                            'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $departmentData['name'])))
                        ]);

                        $departmentNames[] = $departmentData['name'];
                        if(!$department = Department::where('web_id', $departmentData['id'])->first()){
                            $department = Department::create($departmentNeededData);
                        }else{
                            $department->update($departmentNeededData);
                        }
                        $faculty->departments()->save($department);

                        // Process the programmes for each department
                        foreach ($departmentData['programmes'] as $programmeData) {
                            $programmeNeededData = ([
                                'name' => $programmeData['name'],
                                'web_id' => $programmeData['id'],
                                'department_id' => $programmeData['deptid'],
                                'category_id' => ProgrammeCategory::where('category', $programmeData['category'])->value('id'),
                                'award' => $programmeData['award'],
                                'duration' => intval($programmeData['duration']),
                                'max_duration' => intval($programmeData['max_duration']),
                                'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $programmeData['name']))),
                                'academic_session' => $academicSession
                            ]);

                            $programmeNames[] = $programmeData['name'];
                            if(!$programme = Programme::where('web_id', $programmeData['id'])->first()){
                                $programme = Programme::create($programmeNeededData);
                            }else{
                                $programme->update($programmeNeededData);
                            }
                            // Associate the programme with the department
                            $department->programmes()->save($programme);
                        }
                    }
                }

                // Delete any missing faculties, departments, or programmes
                Faculty::whereNotIn('name', $facultyNames)->delete();
                Department::whereNotIn('name', $departmentNames)->where('id', '!=', 25)->delete();
                Programme::whereNotIn('name', $programmeNames)->delete();
            }
        
            alert()->success('Changes Saved', 'Faculty populated successfully')->persistent('Close');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::info($e);
            alert()->error('Oops!', 'Something went wrong')->persistent('Close');
            return redirect()->back();
        }
    }

    public function populateCourse(){
        try {

            $programmes = Programme::orderBy('id', 'ASC')->get();
            $courseWebID = [];
            foreach ($programmes as $programme){
                $response = Http::get(env('PROGRAMME_API_URL').'/'.$programme->web_id);

                if ($response->successful()) {
                    $programmeData = $response->json()['data'];

                    $courseData = $programmeData['courses'];
                    $programmeId = $programmeData['id'];
                    $portalProgramme = Programme::where('web_id', $programmeId)->first();
                    if($portalProgramme){
                        foreach ($courseData as $course) {

                            $existingCourse = Course::where([
                                'web_id' => $course['id'],
                            ])->first();

                            $courseNeededData = [
                                'name' => $course['course_name'],
                                'web_id' => $course['id'],
                                'programme_id' => $portalProgramme->id,
                                'code' => $course['course_code'],
                                'semester' => $course['course_semester'],
                                'credit_unit' => $course['creditUnits'],
                                'level_id' => AcademicLevel::where('level', ($course['course_year'] * 100))->value('id'),
                                'status' => $course['status'],
                            ];

                            if ($existingCourse) {
                                unset($courseNeededData['web_id']);
                                $existingCourse->update($courseNeededData);
                            } else {
                                Course::create($courseNeededData);
                            }
                           
                            $courseWebID[] = $course['id'];
                        }
                    }
                }
            }
        
            Course::whereNotIn('web_id', $courseWebID)->delete();

            alert()->success('Changes Saved', 'Course populated successfully')->persistent('Close');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::info($e);
            alert()->error('Oops!', 'Something went wrong')->persistent('Close');
            return redirect()->back();
        }
    }

    public function populateStaff(){

        try {
            // Fetch the API response
            $response = Http::get(env('STAFF_API_URL'));

            if ($response->successful()) {

                $staffRecords = $response->json()['data'];

                foreach ($staffRecords as $staffRecord) {
                    $existingStaff = Staff::where('staffId', $staffRecord['staffid'])->first();
                    
                    $email = $staffRecord['email'];
                    $password = $staffRecord['password'];
                    $title = $staffRecord['title'];
                    $lastname = $staffRecord['lastname'];
                    $othernames = $staffRecord['firstname'].' '.$staffRecord['middlename'];
                    $phoneNumber = $staffRecord['phone'];
                    $address = $staffRecord['address'];
                    $staffId = $staffRecord['staffid'];
                    $dob = $staffRecord['dob'];
                    $description = $staffRecord['biodata'];
                    $currentPosition = $staffRecord['currentposition'];
                    $nationality = $staffRecord['nationality'];
                    $image = $staffRecord['photo'];
                    $category = $staffRecord['category'];
                    $qualification = $staffRecord['qualification'];
                    $url = $staffRecord['url'];
                    $department = $staffRecord['department'];
                    $academicDepartment = $staffRecord['academic_department'];
                    $acadDeptId = null;
                    $facultyId = null;
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $lastname.' '.$othernames)));
                    if(!empty($academicDepartment)){
                        $portalDept = Department::where('web_id', $academicDepartment['id'])->first();
                        $acadDeptId = $portalDept->id;
                        $facultyId = $portalDept->faculty_id;
                    }
                
                    if ($existingStaff) {

                        $existingStaff->update([
                            'email' => $email,
                            'lastname' => $lastname,
                            'othernames' => $othernames,
                            'phone_number' => $phoneNumber,
                            'address' => $address,
                            'staffId' => $staffId,
                            'description' => $description,
                            'current_position' => $currentPosition,
                            'nationality' => $nationality,
                            'image' => !empty($image)?env('WEBSITE_URL') . str_replace('..', '', $image):null,
                            'qualification' => $qualification,
                            'url' => $url,
                            'slug' => $slug,
                            'department_id' => $acadDeptId,
                            'faculty_id' => $facultyId,
                            'dob' => $dob,
                            'title' => $title,
                        ]);
                    } else {
                        // Create new staff record
                        Staff::create([
                            'email' => $email,
                            'password' => $password,
                            'lastname' => $lastname,
                            'othernames' => $othernames,
                            'phone_number' => $phoneNumber,
                            'address' => $address,
                            'staffId' => $staffId,
                            'description' => $description,
                            'current_position' => $currentPosition,
                            'nationality' => $nationality,
                            'image' => !empty($image)?env('WEBSITE_URL') . str_replace('..', '', $image):null,
                            'category' => $category,
                            'qualification' => $qualification,
                            'url' => $url,
                            'slug' => $slug,
                            'department_id' => $acadDeptId,
                            'faculty_id' => $facultyId,
                            'dob' => $dob,
                            'title' => $title,
                            'referral_code' => $this->generateReferralCode(),
                        ]);
                    }
                }
            }
        
            alert()->success('Changes Saved', 'Staff populated successfully')->persistent('Close');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::info($e);
            alert()->error('Oops!', 'Something went wrong')->persistent('Close');
            return redirect()->back();
        }
    }

    public function calculateStudentCGPA(){
        $students = Student::get();

        foreach($students as $student){
            Result::calculateCGPA($student->id);
        }
        
        return $students;
    }

    public function sendParentOnboardingMail(){
        $students = Student::with('applicant')->get();

        foreach($students as $student){
            if(!empty($student->applicant) && !empty($student->applicant->guardian_id)){
                $guardianId = $student->applicant->guardian_id;
                $guardian = Guardian::find($guardianId);
                $guardianEmail = $guardian->email;
                $guardianPasscode = $guardian->passcode; 

                if(!empty($guardianEmail) && filter_var($guardianEmail, FILTER_VALIDATE_EMAIL)){
                    SendGuardianOnboardingMail::dispatch($guardian)->delay(now()->addSeconds(10));
                }
            }
        }
        
        return $students;
    }

}
