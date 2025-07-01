<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseLecture;
use App\Models\CourseRegistration;
use App\Models\LectureAttendance;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;



class StudentAPIController extends Controller
{
    /**
     * API Login, login student and return token
     *
     * @authenticated
     * @bodyParam login string required The students login credential, either email or matric_number.
     * @bodyParam password string required The students password.
     * @response {
     * "message": "Successfully logged in",
     * "data": {
     * "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9",
     * "token_type": "bearer",
     * "expires_in": 3600,
     * "student": {
     * "id": 1,
     * "matric_number": "UTME/2018/12345678",
     * "first_name": "John",
     * "last_name": "Doe",
     * "email": "john@example.com",
     * "phone_number": "08012345678",
     * "active": true,
     * "created_at": "2019-05-23 12:48:45",
     * "updated_at": "2019-05-23 12:48:45"
     * }
     * }
     * }
     * @response 400 {
     * "message": "Invalid credentials",
     * "data": null
     * }
     */
   public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'login' => 'required|string',  // can be email or matric_number
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->dataResponse($validator->errors()->first(), null, 'error');
        }

        $login = $request->input('login');
        $password = $request->input('password');

        // Determine if login is email or matric number
        $loginField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'matric_number';

        $credentials = [
            $loginField => $login,
            'password' => $password
        ];

        if (!$token = auth('student_api')->attempt($credentials)) {
            return $this->dataResponse('Invalid credentials', null, 'error');
        }

        $response = new \stdClass();
        $response->token = $token;
        $response->token_type = 'bearer';
        $response->expires_in = auth('student_api')->factory()->getTTL() * 60;
        $response->student = auth('student_api')->user();

        return $this->dataResponse('Successfully logged in', $response);
    }

    /**
     * API Retrieve student data
     *
     * @authenticated
     * @response {
     * "message": "Student data retrieved successfully",
     * "data": {
     * "id": 1,
     * "matric_number": "UTME/2018/12345678",
     * "first_name": "John",
     * "last_name": "Doe",
     * "email": "john@example.com",
     * "phone_number": "08012345678",
     * "active": true,
     * "created_at": "2019-05-23 12:48:45",
     * "updated_at": "2019-05-23 12:48:45"
     * }
     * }
     * @response 401 {
     * "message": "Unauthorized",
     * "data": null
     * }
     */
    public function me()
    {
        $student = auth('student_api')->user();
        return $this->dataResponse('Student data retrieved successfully', $student);
    }

    /**
     * Log the student out of the application.
     *
     * This method invalidates the student's authentication token,
     * effectively logging them out from the student API.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout()
    {
        auth('student_api')->logout();
        return $this->dataResponse('Successfully logged out');
    }

    /**
     * Refresh the student's authentication token.
     *
     * This method refreshes the student's authentication token,
     * effectively resetting the expiration time of the token.
     *
     * @response {
     * "message": "Token refreshed successfully",
     * "data": {
     * "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9",
     * "token_type": "bearer",
     * "expires_in": 3600,
     * "student": {
     * "id": 1,
     * "matric_number": "UTME/2018/12345678",
     * "first_name": "John",
     * "last_name": "Doe",
     * "email": "john@example.com",
     * "phone_number": "08012345678",
     * "active": true,
     * "created_at": "2019-05-23 12:48:45",
     * "updated_at": "2019-05-23 12:48:45"
     * }
     * }
     * }
     * @response 401 {
     * "message": "Unauthorized",
     * "data": null
     * }
     */
    public function refresh()
    {
        $response = new \stdClass();
        $response->token = auth('student_api')->refresh();
        $response->token_type = 'bearer';
        $response->expires_in = auth('student_api')->factory()->getTTL() * 60;
        $response->student = auth('student_api')->user();

        return $this->dataResponse('Token refreshed successfully', $response);
    }

    /**
     * Retrieve the authenticated student's information.
     *
     * This method fetches the currently authenticated student's data
     * from the student API and returns it in a structured response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getStudent(Request $request)
    {
        $student = auth('student_api')->user();
        return $this->dataResponse('Student retrieved', $student);
    }

    /**
     * API Mark student attendance
     *
     * This method marks the attendance of a student for a given lecture.
     * The student must be registered for the course and the lecture must exist.
     *
     * @authenticated
     * @response {
     * "message": "Attendance marked successfully",
     * "data": null
     * }
     * @response 401 {
     * "message": "Unauthorized",
     * "data": null
     * }
     * @response 400 {
     * "message": "You did not register for this course",
     * "data": null
     * }
     * @response 400 {
     * "message": "Attendance already marked",
     * "data": null
     * }
     */
    public function markAttendance(Request $request){
        $validator = Validator::make($request->all(), [
            'lecture_id' => 'required|integer|exists:course_lectures,id',
        ]);

        if ($validator->fails()) {
            return $this->dataResponse($validator->errors()->first(), null, 'error');
        }

        $student = auth('student_api')->user();
        $lectureId = $request->lecture_id;

        $courseLecture = CourseLecture::find($lectureId);
        $courseId = $courseLecture->course_id;

        $academicSession = $courseLecture->academic_session;

        // Check course registration
        $studentCourseRegistration = CourseRegistration::where([
            'student_id' => $student->id,
            'course_id' => $courseId,
            'academic_session' => $academicSession
        ])->first();

        if (!$studentCourseRegistration) {
            return $this->dataResponse("You did not register for this course", null, 'error');
        }

        // Prevent duplicate
        $alreadyMarked = LectureAttendance::where([
            'course_lecture_id' => $lectureId,
            'student_id' => $student->id
        ])->exists();

        if ($alreadyMarked) {
            return $this->dataResponse("Attendance already marked", null, 'error');
        }

        // Save attendance
        LectureAttendance::create([
            'course_lecture_id' => $lectureId,
            'student_id' => $student->id,
            'status' => 1
        ]);

        return $this->dataResponse("Attendance marked successfully");
    }
}
