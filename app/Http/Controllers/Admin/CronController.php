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

use Illuminate\Support\Facades\Http;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class CronController extends Controller
{

    public function populateFaculty(){
        // Fetch the API response
        $response = Http::get(env('FACULTY_API_URL'));

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
                            'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $programmeData['name'])))
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
            Department::whereNotIn('name', $departmentNames)->delete();
            Programme::whereNotIn('name', $programmeNames)->delete();
        }
    }

    public function populateCourse(){
        $programmes = Programme::get();
        $courseCodes = [];
        foreach ($programmes as $programme){
            $response = Http::get(env('PROGRAMME_API_URL').'/'.$programme->web_id);

            if ($response->successful()) {
                $programmeData = $response->json()['data'];

                $courseData = $programmeData['courses'];
                $programmeId = $programmeData['id'];

                foreach ($courseData as $course) {
                    $courseNeededData = ([
                        'name' => $course['course_name'],
                        'web_id' => $course['id'],
                        'programme_id' => $programmeId,
                        'code' => $course['course_code'],
                        'semester' => $course['course_semester'],
                        'credit_unit' => $course['creditUnits'],
                        'level_id' => AcademicLevel::where('level', ($course['course_year']*100))->value('id'),
                        'status' => $course['status'],
                    ]);
                    $courseCodes[] = $course['course_code'];
                    if(!$course = Course::where('web_id', $course['id'])->first()){
                        $course = Course::create($courseNeededData);
                    }else{
                        $course->update($facultyNeededData);
                    }
                    $programme->courses()->save($course);
                }

            }
            // Delete courses that are not in the API response
            Course::whereNotIn('code', $courseCodes)->delete();
        }
    }

}
