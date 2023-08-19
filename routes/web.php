<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\GlobalDataMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return redirect('https://tau.edu.ng');
});

Auth::routes(['register' => false, 'login' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/updateNotificationStatus', [App\Http\Controllers\HomeController::class, 'updateNotificationStatus'])->name('updateNotificationStatus');
Route::get('/verifyPayment', [App\Http\Controllers\PaymentController::class, 'verifyPayment'])->name('verifyPayment');
Route::post('/paystackWebhook', [App\Http\Controllers\PaymentController::class, 'paystackWebhook']);

Route::get('/examDocket/{slug}', [App\Http\Controllers\HomeController::class, 'getExamDocket']);



Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'admin'], function () {
  Route::get('/', [App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
  Route::get('/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');

  // Route::get('/register', [App\Http\Controllers\Admin\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  // Route::post('/register', [App\Http\Controllers\Admin\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Admin\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Admin\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Admin\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Admin\Auth\ResetPasswordController::class, 'showResetForm']);

  Route::get('/setting', [App\Http\Controllers\Admin\AdminController::class, 'setting'])->name('setting');
  Route::post('/updateSiteInfo', [App\Http\Controllers\Admin\AdminController::class, 'updateSiteInfo'])->name('updateSiteInfo');
  Route::post('/updateSocialAccounts', [App\Http\Controllers\Admin\AdminController::class, 'updateSocialAccounts'])->name('updateSocialAccounts');

  Route::get('/home', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('home')->middleware(['auth:admin']);
  Route::get('/academicLevel', [App\Http\Controllers\Admin\AcademicController::class, 'academicLevel'])->name('academicLevel')->middleware(['auth:admin']);
  Route::post('/addLevel', [App\Http\Controllers\Admin\AcademicController::class, 'addLevel'])->name('addLevel')->middleware(['auth:admin']);
  Route::post('/updateLevel', [App\Http\Controllers\Admin\AcademicController::class, 'updateLevel'])->name('updateLevel')->middleware(['auth:admin']);
  Route::post('/deleteLevel', [App\Http\Controllers\Admin\AcademicController::class, 'deleteLevel'])->name('deleteLevel')->middleware(['auth:admin']);

  Route::get('/approvalLevel', [App\Http\Controllers\Admin\AcademicController::class, 'approvalLevel'])->name('approvalLevel')->middleware(['auth:admin']);
  Route::post('/addApprovalLevel', [App\Http\Controllers\Admin\AcademicController::class, 'addApprovalLevel'])->name('addApprovalLevel')->middleware(['auth:admin']);
  Route::post('/updateApprovalLevel', [App\Http\Controllers\Admin\AcademicController::class, 'updateApprovalLevel'])->name('updateApprovalLevel')->middleware(['auth:admin']);
  Route::post('/deleteApprovalLevel', [App\Http\Controllers\Admin\AcademicController::class, 'deleteApprovalLevel'])->name('deleteApprovalLevel')->middleware(['auth:admin']);
  
  Route::get('/resultApprovalStatus', [App\Http\Controllers\Admin\AcademicController::class, 'resultApprovalStatus'])->name('resultApprovalStatus')->middleware(['auth:admin']);
  Route::post('/addResultApprovalStatus', [App\Http\Controllers\Admin\AcademicController::class, 'addResultApprovalStatus'])->name('addResultApprovalStatus')->middleware(['auth:admin']);
  Route::post('/updateResultApprovalStatus', [App\Http\Controllers\Admin\AcademicController::class, 'updateResultApprovalStatus'])->name('updateResultApprovalStatus')->middleware(['auth:admin']);
  Route::post('/deleteResultApprovalStatus', [App\Http\Controllers\Admin\AcademicController::class, 'deleteResultApprovalStatus'])->name('deleteResultApprovalStatus')->middleware(['auth:admin']);
  

  Route::get('/programmeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'programmeCategory'])->name('programmeCategory')->middleware(['auth:admin']);
  Route::post('/addProgrammeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'addProgrammeCategory'])->name('addProgrammeCategory')->middleware(['auth:admin']);
  Route::post('/updateProgrammeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'updateProgrammeCategory'])->name('updateProgrammeCategory')->middleware(['auth:admin']);
  Route::post('/deleteProgrammeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'deleteProgrammeCategory'])->name('deleteProgrammeCategory')->middleware(['auth:admin']);
  Route::get('/populateFaculty', [App\Http\Controllers\Admin\CronController::class, 'populateFaculty'])->name('populateFaculty')->middleware(['auth:admin']);
  Route::get('/populateCourse', [App\Http\Controllers\Admin\CronController::class, 'populateCourse'])->name('populateCourse')->middleware(['auth:admin']);
  Route::get('/populateStaff', [App\Http\Controllers\Admin\CronController::class, 'populateStaff'])->name('populateStaff')->middleware(['auth:admin']);

  Route::get('/getDepartments/{id}', [App\Http\Controllers\Admin\ProgrammeController::class, 'getDepartments'])->name('getDepartments')->middleware(['auth:admin']);
  Route::get('/getProgrammes/{id}', [App\Http\Controllers\Admin\ProgrammeController::class, 'getProgrammes'])->name('getProgrammes')->middleware(['auth:admin']);

  Route::get('/sessionSetup', [App\Http\Controllers\Admin\AcademicController::class, 'sessionSetup'])->name('sessionSetup')->middleware(['auth:admin']);
  Route::post('/setSession', [App\Http\Controllers\Admin\AcademicController::class, 'setSession'])->name('setSession')->middleware(['auth:admin']);
  Route::post('/addSession', [App\Http\Controllers\Admin\AcademicController::class, 'addSession'])->name('addSession')->middleware(['auth:admin']);
  Route::post('/updateSession', [App\Http\Controllers\Admin\AcademicController::class, 'updateSession'])->name('updateSession')->middleware(['auth:admin']);
  Route::post('/deleteSession', [App\Http\Controllers\Admin\AcademicController::class, 'deleteSession'])->name('deleteSession')->middleware(['auth:admin']);
  
  Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'payments'])->name('payments')->middleware(['auth:admin']);
  Route::post('/addPayment', [App\Http\Controllers\Admin\PaymentController::class, 'addPayment'])->name('addPayment')->middleware(['auth:admin']);
  Route::post('/updatePayment', [App\Http\Controllers\Admin\PaymentController::class, 'updatePayment'])->name('updatePayment')->middleware(['auth:admin']);
  Route::post('/deletePayment', [App\Http\Controllers\Admin\PaymentController::class, 'deletePayment'])->name('deletePayment')->middleware(['auth:admin']);

  Route::get('/payment/{slug}', [App\Http\Controllers\Admin\PaymentController::class, 'payment'])->name('payment')->middleware(['auth:admin']);
  Route::post('/getPayment', [App\Http\Controllers\Admin\PaymentController::class, 'getPayment'])->name('getPayment')->middleware(['auth:admin']);
  Route::post('/addStructure', [App\Http\Controllers\Admin\PaymentController::class, 'addStructure'])->name('addStructure')->middleware(['auth:admin']);
  Route::post('/updateStructure', [App\Http\Controllers\Admin\PaymentController::class, 'updateStructure'])->name('updateStructure')->middleware(['auth:admin']);
  Route::post('/deleteStructure', [App\Http\Controllers\Admin\PaymentController::class, 'deleteStructure'])->name('deleteStructure')->middleware(['auth:admin']);

  Route::post('/chargeStudent', [App\Http\Controllers\Admin\PaymentController::class, 'chargeStudent'])->name('chargeStudent')->middleware(['auth:admin']);
  Route::get('/chargeStudent', [App\Http\Controllers\Admin\AdminController::class, 'chargeStudent'])->name('chargeStudent')->middleware(['auth:admin']);


  Route::get('/applicants', [App\Http\Controllers\Admin\AdmissionController::class, 'applicants'])->name('applicants')->middleware(['auth:admin']);
  Route::get('/applicant/{slug}', [App\Http\Controllers\Admin\AdmissionController::class, 'applicant'])->name('applicant')->middleware(['auth:admin']);
  Route::post('/applicantWithSession', [App\Http\Controllers\Admin\AdmissionController::class, 'applicantWithSession'])->name('applicantWithSession')->middleware(['auth:admin']);

  Route::get('/students', [App\Http\Controllers\Admin\AdmissionController::class, 'students'])->name('students')->middleware(['auth:admin']);
  Route::get('/student/{slug}', [App\Http\Controllers\Admin\AdmissionController::class, 'student'])->name('student')->middleware(['auth:admin']);

  Route::get('/faculties', [App\Http\Controllers\Admin\AcademicController::class, 'faculties'])->name('faculties')->middleware(['auth:admin']);
  Route::get('/faculty/{slug}', [App\Http\Controllers\Admin\AcademicController::class, 'faculty'])->name('faculty')->middleware(['auth:admin']);

  Route::get('/departments', [App\Http\Controllers\Admin\AcademicController::class, 'departments'])->name('departments')->middleware(['auth:admin']);
  Route::get('/department/{slug}', [App\Http\Controllers\Admin\AcademicController::class, 'department'])->name('department')->middleware(['auth:admin']);

  Route::get('/programmes', [App\Http\Controllers\Admin\ProgrammeController::class, 'programmes'])->name('programmes')->middleware(['auth:admin']);
  Route::get('/programme/{slug}', [App\Http\Controllers\Admin\ProgrammeController::class, 'programme'])->name('programme')->middleware(['auth:admin']);
  Route::post('/saveProgramme', [App\Http\Controllers\Admin\ProgrammeController::class, 'saveProgramme'])->name('saveProgramme')->middleware(['auth:admin']);

  Route::post('/manageAdmission', [App\Http\Controllers\Admin\AdmissionController::class, 'manageAdmission'])->name('manageAdmission')->middleware(['auth:admin']);

  Route::get('/courseRegMgt', [App\Http\Controllers\Admin\AcademicController::class, 'courseRegMgt'])->name('courseRegMgt')->middleware(['auth:admin']);
  Route::post('/setCourseRegStatus', [App\Http\Controllers\Admin\AcademicController::class, 'setCourseRegStatus'])->name('setCourseRegStatus')->middleware(['auth:admin']);
  Route::get('/applicants', [App\Http\Controllers\Admin\AdmissionController::class, 'applicants'])->name('applicants')->middleware(['auth:admin']);

  Route::get('/staff', [App\Http\Controllers\Admin\StaffController::class, 'staff'])->name('staff')->middleware(['auth:admin']);
  Route::get('/staff/{slug}', [App\Http\Controllers\Admin\StaffController::class, 'singleStaff'])->name('singleStaff')->middleware(['auth:admin']);

  Route::get('/staffRoles', [App\Http\Controllers\Admin\StaffController::class, 'roles'])->name('roles')->middleware(['auth:admin']);
  Route::post('/addRole', [App\Http\Controllers\Admin\StaffController::class, 'addRole'])->name('addRole')->middleware(['auth:admin']);
  Route::post('/updateRole', [App\Http\Controllers\Admin\StaffController::class, 'updateRole'])->name('updateRole')->middleware(['auth:admin']);
  Route::post('/deleteRole', [App\Http\Controllers\Admin\StaffController::class, 'deleteRole'])->name('deleteRole')->middleware(['auth:admin']);

  Route::post('/assignRole', [App\Http\Controllers\Admin\StaffController::class, 'assignRole'])->name('assignRole')->middleware(['auth:admin']);
  Route::post('/unAssignRole', [App\Http\Controllers\Admin\StaffController::class, 'unAssignRole'])->name('unAssignRole')->middleware(['auth:admin']);

  Route::post('/disableStaff', [App\Http\Controllers\Admin\StaffController::class, 'disableStaff'])->name('disableStaff')->middleware(['auth:admin']);
  Route::post('/enableStaff', [App\Http\Controllers\Admin\StaffController::class, 'enableStaff'])->name('enableStaff')->middleware(['auth:admin']);
  
  Route::post('/assignDeanToFaculty', [App\Http\Controllers\Admin\StaffController::class, 'assignDeanToFaculty'])->name('assignDeanToFaculty')->middleware(['auth:admin']);
  Route::post('/assignHodToDepartment', [App\Http\Controllers\Admin\StaffController::class, 'assignHodToDepartment'])->name('assignHodToDepartment')->middleware(['auth:admin']);
  Route::post('/assignSubDeanToFaculty', [App\Http\Controllers\Admin\StaffController::class, 'assignSubDeanToFaculty'])->name('assignSubDeanToFaculty')->middleware(['auth:admin']);

  Route::get('/examDocketMgt', [App\Http\Controllers\Admin\AcademicController::class, 'examDocketMgt'])->name('examDocketMgt')->middleware(['auth:admin']);
  Route::post('/setExamSetting', [App\Http\Controllers\Admin\AcademicController::class, 'setExamSetting'])->name('setExamSetting')->middleware(['auth:admin']);

  Route::get('/campusCapacity', [App\Http\Controllers\Admin\AcademicController::class, 'campusCapacity'])->name('campusCapacity')->middleware(['auth:admin']);
  Route::get('/allStudents', [App\Http\Controllers\Admin\AcademicController::class, 'allStudents'])->name('allStudents')->middleware(['auth:admin']);
  Route::get('/massPromotion', [App\Http\Controllers\Admin\AcademicController::class, 'massPromotion'])->name('massPromotion')->middleware(['auth:admin']);
  Route::get('/demoteStudent', [App\Http\Controllers\Admin\AcademicController::class, 'demoteStudent'])->name('demoteStudent')->middleware(['auth:admin']);

  Route::post('/promoteStudent', [App\Http\Controllers\Admin\AcademicController::class, 'promoteStudent'])->name('promoteStudent')->middleware(['auth:admin']);
  Route::post('/getStudent', [App\Http\Controllers\Admin\AcademicController::class, 'getStudent'])->name('getStudent')->middleware(['auth:admin']);
  Route::post('/makeDemoteStudent', [App\Http\Controllers\Admin\AcademicController::class, 'makeDemoteStudent'])->name('makeDemoteStudent')->middleware(['auth:admin']);

  Route::post('/addAdviser', [App\Http\Controllers\Admin\StaffController::class, 'addAdviser'])->name('addAdviser')->middleware(['auth:admin']);
  Route::post('/addExamOfficer', [App\Http\Controllers\Admin\StaffController::class, 'addExamOfficer'])->name('addExamOfficer')->middleware(['auth:admin']);
  Route::post('/getStudents', [App\Http\Controllers\Admin\StaffController::class, 'getStudents'])->name('getStudents')->middleware(['auth:admin']);

  Route::get('/courseRegistrations', [App\Http\Controllers\Admin\AcademicController::class, 'courseRegistrations'])->name('courseRegistrations')->middleware(['auth:admin']);
  Route::post('/approveReg', [App\Http\Controllers\Admin\AcademicController::class, 'approveReg'])->name('approveReg')->middleware(['auth:admin']);

  Route::get('/studentProfile/{slug}', [App\Http\Controllers\Admin\AcademicController::class, 'studentProfile'])->name('studentProfile')->middleware(['auth:admin']);

  Route::get('/getStudentResults', [App\Http\Controllers\Admin\ResultController::class, 'getStudentResults'])->name('getStudentResults')->middleware(['auth:admin']);
  Route::post('/generateStudentResults', [App\Http\Controllers\Admin\ResultController::class, 'generateStudentResults'])->name('generateStudentResults')->middleware(['auth:admin']);
  Route::post('/approveResult', [App\Http\Controllers\Admin\ResultController::class, 'approveResult'])->name('approveResult')->middleware(['auth:admin']);

  Route::get('/studentCourses', [App\Http\Controllers\Admin\ProgrammeController::class, 'studentCourses'])->name('studentCourses')->middleware(['auth:admin']);
  Route::post('/getStudentCourses', [App\Http\Controllers\Admin\ProgrammeController::class, 'getStudentCourses'])->name('getStudentCourses')->middleware(['auth:admin']);
  Route::get('/courseDetail/{id}', [App\Http\Controllers\Admin\ProgrammeController::class, 'courseDetail'])->name('courseDetail')->middleware(['auth:admin']);
  Route::post('/sendMessage', [App\Http\Controllers\Admin\ProgrammeController::class, 'sendMessage'])->name('sendMessage')->middleware(['auth:admin']);

  Route::post('/staffUploadResult', [App\Http\Controllers\Admin\ProgrammeController::class, 'staffUploadResult'])->name('staffUploadResult')->middleware(['auth:admin']);
  Route::post('/updateStudentResult', [App\Http\Controllers\Admin\ProgrammeController::class, 'updateStudentResult'])->name('updateStudentResult')->middleware(['auth:admin']);

  Route::post('/generateResult', [App\Http\Controllers\Admin\ResultController::class, 'generateResult'])->name('generateResult')->middleware(['auth:admin']);
  Route::post('/uploadStudentImage', [App\Http\Controllers\Admin\StaffController::class, 'uploadStudentImage'])->name('uploadStudentImage')->middleware(['auth:admin']);
  Route::post('/changeStudentPassword', [App\Http\Controllers\Admin\StaffController::class, 'changeStudentPassword'])->name('changeStudentPassword')->middleware(['auth:admin']);
  Route::post('/changeStudentCreditLoad', [App\Http\Controllers\Admin\StaffController::class, 'changeStudentCreditLoad'])->name('changeStudentCreditLoad')->middleware(['auth:admin']);

  
});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'student'], function () {
  Route::get('/', [App\Http\Controllers\Student\Auth\LoginController::class, 'showLoginForm'])->name('student.login');
  Route::get('/login', [App\Http\Controllers\Student\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Student\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Student\Auth\LoginController::class, 'logout'])->name('logout');
  Route::get('/home', [App\Http\Controllers\Student\StudentController::class, 'index'])->name('home');

  // Route::get('/register', [App\Http\Controllers\Student\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  // Route::post('/register', [App\Http\Controllers\Student\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Student\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Student\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Student\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Student\Auth\ResetPasswordController::class, 'showResetForm']);

  Route::get('/home', [App\Http\Controllers\Student\StudentController::class, 'index'])->name('home')->middleware(['auth:student']);
  Route::post('/makePayment', [App\Http\Controllers\Student\StudentController::class, 'makePayment'])->name('makePayment')->middleware(['auth:student']);
  
  Route::get('/profile', [App\Http\Controllers\Student\StudentController::class, 'profile'])->name('profile')->middleware(['auth:student']);
  Route::post('/saveBioData', [App\Http\Controllers\Student\StudentController::class, 'saveBioData'])->name('saveBioData')->middleware(['auth:student']);
  Route::post('/updatePassword', [App\Http\Controllers\Student\StudentController::class, 'updatePassword'])->name('updatePassword')->middleware(['auth:student']);
  
  
  Route::get('/transactions', [App\Http\Controllers\Student\StudentController::class, 'transactions'])->name('transactions')->middleware(['auth:student']);
  Route::get('/courseRegistration', [App\Http\Controllers\Student\AcademicController::class, 'courseRegistration'])->name('courseRegistration')->middleware(['auth:student']);
  Route::post('/registerCourses', [App\Http\Controllers\Student\AcademicController::class, 'registerCourses'])->name('registerCourses')->middleware(['auth:student']);
  Route::post('/printCourseReg', [App\Http\Controllers\Student\AcademicController::class, 'printCourseReg'])->name('printCourseReg')->middleware(['auth:student']);
  Route::get('/editCourseReg', [App\Http\Controllers\Student\AcademicController::class, 'editCourseReg'])->name('editCourseReg')->middleware(['auth:student']);
  Route::get('/allCourseRegs', [App\Http\Controllers\Student\AcademicController::class, 'allCourseRegs'])->name('allCourseRegs')->middleware(['auth:student']);

  Route::get('/examDocket', [App\Http\Controllers\Student\AcademicController::class, 'examDocket'])->name('examDocket')->middleware(['auth:student']);
  Route::post('/genExamDocket', [App\Http\Controllers\Student\AcademicController::class, 'genExamDocket'])->name('genExamDocket')->middleware(['auth:student']);
  Route::get('/allExamDockets', [App\Http\Controllers\Student\AcademicController::class, 'allExamDockets'])->name('allExamDockets')->middleware(['auth:student']);
  Route::post('/printExamCard', [App\Http\Controllers\Student\AcademicController::class, 'printExamCard'])->name('printExamCard')->middleware(['auth:student']);

  Route::get('/examResult', [App\Http\Controllers\Student\AcademicController::class, 'examResult'])->name('examResult')->middleware(['auth:student']);
  Route::post('/generateResult', [App\Http\Controllers\Student\AcademicController::class, 'generateResult'])->name('generateResult')->middleware(['auth:student']);

  Route::get('/mentor', [App\Http\Controllers\Student\StudentController::class, 'mentor'])->name('mentor')->middleware(['auth:student']);
});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'staff'], function () {
  Route::get('/', [App\Http\Controllers\Staff\Auth\LoginController::class, 'showLoginForm'])->name('staff.login');
  Route::get('/login', [App\Http\Controllers\Staff\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Staff\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Staff\Auth\LoginController::class, 'logout'])->name('logout');

  Route::post('/password/email', [App\Http\Controllers\Staff\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Staff\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Staff\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Staff\Auth\ResetPasswordController::class, 'showResetForm']);

  Route::get('/home', [App\Http\Controllers\Staff\StaffController::class, 'index'])->name('home')->middleware(['auth:staff']);
  Route::get('/profile', [App\Http\Controllers\Staff\StaffController::class, 'profile'])->name('profile')->middleware(['auth:staff']);
  Route::post('/saveBioData', [App\Http\Controllers\Staff\StaffController::class, 'saveBioData'])->name('saveBioData')->middleware(['auth:staff']);
  Route::post('/updatePassword', [App\Http\Controllers\Staff\StaffController::class, 'updatePassword'])->name('updatePassword')->middleware(['auth:staff']);

  Route::get('/mentee', [App\Http\Controllers\Staff\StaffController::class, 'mentee'])->name('mentee')->middleware(['auth:staff']);
  Route::get('/reffs', [App\Http\Controllers\Staff\StaffController::class, 'reffs'])->name('reffs')->middleware(['auth:staff']);
  Route::get('/courses', [App\Http\Controllers\Staff\StaffController::class, 'courses'])->name('courses')->middleware(['auth:staff']);
  
  Route::get('/courseAllocation', [App\Http\Controllers\Staff\StaffController::class, 'courseAllocation'])->name('courseAllocation')->middleware(['auth:staff']);
  Route::get('/roleAllocation', [App\Http\Controllers\Staff\StaffController::class, 'roleAllocation'])->name('roleAllocation')->middleware(['auth:staff']);
  Route::get('/student/{slug}', [App\Http\Controllers\Staff\StaffController::class, 'student'])->name('student')->middleware(['auth:staff']);
  Route::get('/applicant/{slug}', [App\Http\Controllers\Staff\StaffController::class, 'applicant'])->name('applicant')->middleware(['auth:staff']);
  Route::post('/applicantWithSession', [App\Http\Controllers\Staff\StaffController::class, 'applicantWithSession'])->name('applicantWithSession')->middleware(['auth:staff']);

  Route::post('/getCourses', [App\Http\Controllers\Staff\StaffController::class, 'getCourses'])->name('getCourses')->middleware(['auth:staff']);
  Route::post('/assignCourse', [App\Http\Controllers\Staff\StaffController::class, 'assignCourse'])->name('assignCourse')->middleware(['auth:staff']);
  Route::get('/courseDetail/{id}', [App\Http\Controllers\Staff\StaffController::class, 'courseDetail'])->name('courseDetail')->middleware(['auth:staff']);
  Route::post('/sendMessage', [App\Http\Controllers\Staff\StaffController::class, 'sendMessage'])->name('sendMessage')->middleware(['auth:staff']);

  Route::get('/studentCourses', [App\Http\Controllers\Staff\StaffController::class, 'studentCourses'])->name('studentCourses')->middleware(['auth:staff']);
  Route::post('/getStudentCourses', [App\Http\Controllers\Staff\StaffController::class, 'getStudentCourses'])->name('getStudentCourses')->middleware(['auth:staff']);
  Route::post('/staffUploadResult', [App\Http\Controllers\Staff\StaffController::class, 'staffUploadResult'])->name('staffUploadResult')->middleware(['auth:staff']);
  Route::post('/updateStudentResult', [App\Http\Controllers\Staff\StaffController::class, 'updateStudentResult'])->name('updateStudentResult')->middleware(['auth:staff']);

  Route::get('/staff', [App\Http\Controllers\Staff\StaffController::class, 'staff'])->name('staff')->middleware(['auth:staff']);
  Route::get('/staff/{slug}', [App\Http\Controllers\Staff\StaffController::class, 'singleStaff'])->name('singleStaff')->middleware(['auth:staff']);

  Route::get('/staffRoles', [App\Http\Controllers\Staff\StaffController::class, 'roles'])->name('roles')->middleware(['auth:staff']);
  Route::post('/addRole', [App\Http\Controllers\Staff\StaffController::class, 'addRole'])->name('addRole')->middleware(['auth:staff']);
  Route::post('/updateRole', [App\Http\Controllers\Staff\StaffController::class, 'updateRole'])->name('updateRole')->middleware(['auth:staff']);
  Route::post('/deleteRole', [App\Http\Controllers\Staff\StaffController::class, 'deleteRole'])->name('deleteRole')->middleware(['auth:staff']);

  Route::post('/assignRole', [App\Http\Controllers\Staff\StaffController::class, 'assignRole'])->name('assignRole')->middleware(['auth:staff']);
  Route::post('/unAssignRole', [App\Http\Controllers\Staff\StaffController::class, 'unAssignRole'])->name('unAssignRole')->middleware(['auth:staff']);

  Route::post('/disableStaff', [App\Http\Controllers\Staff\StaffController::class, 'disableStaff'])->name('disableStaff')->middleware(['auth:staff']);
  Route::post('/enableStaff', [App\Http\Controllers\Staff\StaffController::class, 'enableStaff'])->name('enableStaff')->middleware(['auth:staff']);
  
  Route::post('/assignDeanToFaculty', [App\Http\Controllers\Staff\StaffController::class, 'assignDeanToFaculty'])->name('assignDeanToFaculty')->middleware(['auth:staff']);
  Route::post('/assignHodToDepartment', [App\Http\Controllers\Staff\StaffController::class, 'assignHodToDepartment'])->name('assignHodToDepartment')->middleware(['auth:staff']);
  Route::post('/assignSubDeanToFaculty', [App\Http\Controllers\Staff\StaffController::class, 'assignSubDeanToFaculty'])->name('assignSubDeanToFaculty')->middleware(['auth:staff']);
  
  Route::get('/studentProfile/{slug}', [App\Http\Controllers\Staff\StaffController::class, 'studentProfile'])->name('studentProfile')->middleware(['auth:staff']);

  Route::get('/faculties', [App\Http\Controllers\Staff\AcademicController::class, 'faculties'])->name('faculties')->middleware(['auth:staff']);
  Route::get('/faculty/{slug}', [App\Http\Controllers\Staff\AcademicController::class, 'faculty'])->name('faculty')->middleware(['auth:staff']);

  Route::get('/departments', [App\Http\Controllers\Staff\AcademicController::class, 'departments'])->name('departments')->middleware(['auth:staff']);
  Route::get('/department/{slug}', [App\Http\Controllers\Staff\AcademicController::class, 'department'])->name('department')->middleware(['auth:staff']);

  Route::get('/programmes', [App\Http\Controllers\Staff\ProgrammeController::class, 'programmes'])->name('programmes')->middleware(['auth:staff']);
  Route::get('/programme/{slug}', [App\Http\Controllers\Staff\ProgrammeController::class, 'programme'])->name('programme')->middleware(['auth:staff']);
  Route::post('/saveProgramme', [App\Http\Controllers\Staff\ProgrammeController::class, 'saveProgramme'])->name('saveProgramme')->middleware(['auth:staff']);

  Route::post('/addAdviser', [App\Http\Controllers\Staff\StaffController::class, 'addAdviser'])->name('addAdviser')->middleware(['auth:staff']);
  Route::post('/addExamOfficer', [App\Http\Controllers\Staff\StaffController::class, 'addExamOfficer'])->name('addExamOfficer')->middleware(['auth:staff']);
  Route::post('/getStudents', [App\Http\Controllers\Staff\StaffController::class, 'getStudents'])->name('getStudents')->middleware(['auth:staff']);

  Route::get('/adviserProgrammes', [App\Http\Controllers\Staff\ProgrammeController::class, 'adviserProgrammes'])->name('adviserProgrammes')->middleware(['auth:staff']);
  Route::get('/levelCourseReg/{id}', [App\Http\Controllers\Staff\ProgrammeController::class, 'levelCourseReg'])->name('levelCourseReg')->middleware(['auth:staff']);
  Route::get('/levelStudents/{id}', [App\Http\Controllers\Staff\ProgrammeController::class, 'levelStudents'])->name('levelStudents')->middleware(['auth:staff']);

  Route::post('/approveReg', [App\Http\Controllers\Staff\ProgrammeController::class, 'approveReg'])->name('approveReg')->middleware(['auth:staff']);

  Route::get('/getDepartments/{id}', [App\Http\Controllers\Staff\ProgrammeController::class, 'getDepartments'])->name('getDepartments')->middleware(['auth:staff']);
  Route::get('/getProgrammes/{id}', [App\Http\Controllers\Staff\ProgrammeController::class, 'getProgrammes'])->name('getProgrammes')->middleware(['auth:staff']);


  Route::get('/getStudentResults', [App\Http\Controllers\Staff\ResultController::class, 'getStudentResults'])->name('getStudentResults')->middleware(['auth:staff']);
  Route::post('/generateStudentResults', [App\Http\Controllers\Staff\ResultController::class, 'generateStudentResults'])->name('generateStudentResults')->middleware(['auth:staff']);
  Route::post('/approveResult', [App\Http\Controllers\Staff\ResultController::class, 'approveResult'])->name('approveResult')->middleware(['auth:staff']); 

  Route::post('/generateResult', [App\Http\Controllers\Staff\ResultController::class, 'generateResult'])->name('generateResult')->middleware(['auth:staff']);
  Route::post('/uploadStudentImage', [App\Http\Controllers\Staff\StaffController::class, 'uploadStudentImage'])->name('uploadStudentImage')->middleware(['auth:staff']);
  Route::post('/changeStudentPassword', [App\Http\Controllers\Staff\StaffController::class, 'changeStudentPassword'])->name('changeStudentPassword')->middleware(['auth:staff']);
  Route::post('/changeStudentCreditLoad', [App\Http\Controllers\Staff\StaffController::class, 'changeStudentCreditLoad'])->name('changeStudentCreditLoad')->middleware(['auth:staff']);

  
});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'bursary'], function () {
  Route::get('/', [App\Http\Controllers\Bursary\Auth\LoginController::class, 'showLoginForm'])->name('bursary.login');
  Route::get('/login', [App\Http\Controllers\Bursary\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Bursary\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Bursary\Auth\LoginController::class, 'logout'])->name('logout');

  // Route::get('/register', [App\Http\Controllers\Bursary\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  // Route::post('/register', [App\Http\Controllers\Bursary\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Bursary\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Bursary\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Bursary\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Bursary\Auth\ResetPasswordController::class, 'showResetForm']);
});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'partner'], function () {
  Route::get('/', [App\Http\Controllers\Partner\Auth\LoginController::class, 'showLoginForm'])->name('partner.login');
  Route::get('/login', [App\Http\Controllers\Partner\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Partner\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Partner\Auth\LoginController::class, 'logout'])->name('logout');

  Route::get('/register', [App\Http\Controllers\Partner\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  Route::post('/register', [App\Http\Controllers\Partner\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Partner\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Partner\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Partner\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Partner\Auth\ResetPasswordController::class, 'showResetForm']);

  Route::get('/home', [App\Http\Controllers\Partner\PartnerController::class, 'index'])->name('home')->middleware(['auth:partner']);
  Route::get('/students', [App\Http\Controllers\Partner\PartnerController::class, 'students'])->name('students')->middleware(['auth:partner']);
  Route::get('/applicants', [App\Http\Controllers\Partner\PartnerController::class, 'applicants'])->name('applicants')->middleware(['auth:partner']);
  Route::get('/transactions', [App\Http\Controllers\Partner\PartnerController::class, 'transactions'])->name('transactions')->middleware(['auth:partner']);
  Route::get('/profile', [App\Http\Controllers\Partner\PartnerController::class, 'profile'])->name('profile')->middleware(['auth:partner']);

});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'guardian'], function () {
  Route::get('/', [App\Http\Controllers\Guardian\Auth\LoginController::class, 'showLoginForm'])->name('guardian.login');
  Route::get('/login', [App\Http\Controllers\Guardian\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Guardian\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Guardian\Auth\LoginController::class, 'logout'])->name('logout');

  // Route::get('/register', [App\Http\Controllers\Guardian\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  // Route::post('/register', [App\Http\Controllers\Guardian\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Guardian\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Guardian\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Guardian\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Guardian\Auth\ResetPasswordController::class, 'showResetForm']);

  Route::get('/home', [App\Http\Controllers\Guardian\GuardianController::class, 'index'])->name('home')->middleware(['auth:guardian']);
  Route::get('/students', [App\Http\Controllers\Guardian\GuardianController::class, 'students'])->name('students')->middleware(['auth:guardian']);
  Route::get('/profile', [App\Http\Controllers\Guardian\GuardianController::class, 'profile'])->name('profile')->middleware(['auth:guardian']);
  
});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'applicant'], function () {
  Route::get('/', [App\Http\Controllers\User\ApplicationController::class, 'showRegistrationForm'])->name('showRegistrationForm');
  Route::get('/login', [App\Http\Controllers\User\Auth\LoginController::class, 'showLoginForm'])->name('applicant.login');
  Route::post('/login', [App\Http\Controllers\User\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\User\Auth\LoginController::class, 'logout'])->name('logout');

  Route::get('/register', [App\Http\Controllers\User\ApplicationController::class, 'showRegistrationForm'])->name('showRegistrationForm');
  Route::post('/register', [App\Http\Controllers\User\ApplicationController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\User\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\User\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\User\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\User\Auth\ResetPasswordController::class, 'showResetForm']);

  Route::get('/home', [App\Http\Controllers\User\ApplicationController::class, 'index'])->middleware(['auth:user']);
  Route::post('/saveBioData', [App\Http\Controllers\User\ApplicationController::class, 'saveBioData'])->middleware(['auth:user']);
  Route::post('/guardianBioData', [App\Http\Controllers\User\ApplicationController::class, 'guardianBioData'])->middleware(['auth:user']);
  Route::post('/saveUtme', [App\Http\Controllers\User\ApplicationController::class, 'saveUtme'])->middleware(['auth:user']);
  Route::post('/saveSitting', [App\Http\Controllers\User\ApplicationController::class, 'saveSitting'])->middleware(['auth:user']);
  Route::post('/addOlevel', [App\Http\Controllers\User\ApplicationController::class, 'addOlevel'])->middleware(['auth:user']);
  Route::post('/addUtme', [App\Http\Controllers\User\ApplicationController::class, 'addUtme'])->middleware(['auth:user']);
  Route::post('/deleteUtme', [App\Http\Controllers\User\ApplicationController::class, 'deleteUtme'])->middleware(['auth:user']);
  Route::post('/deleteOlevel', [App\Http\Controllers\User\ApplicationController::class, 'deleteOlevel'])->middleware(['auth:user']);
  Route::post('/submitApplication', [App\Http\Controllers\User\ApplicationController::class, 'submitApplication'])->middleware(['auth:user']);
  Route::post('/nokBioData', [App\Http\Controllers\User\ApplicationController::class, 'nokBioData'])->middleware(['auth:user']);

  Route::post('/saveDe', [App\Http\Controllers\User\ApplicationController::class, 'saveDe'])->middleware(['auth:user']);
  Route::post('/saveProgramme', [App\Http\Controllers\User\ApplicationController::class, 'saveProgramme'])->middleware(['auth:user']);
  Route::post('/uploadOlevel', [App\Http\Controllers\User\ApplicationController::class, 'uploadOlevel'])->middleware(['auth:user']);
  Route::post('/uploadUtme', [App\Http\Controllers\User\ApplicationController::class, 'uploadUtme'])->middleware(['auth:user']);

  Route::get('/programmeById/{id}', [App\Http\Controllers\User\ApplicationController::class, 'programmeById'])->name('programmeById');
  Route::get('/facultyById/{id}', [App\Http\Controllers\User\ApplicationController::class, 'facultyById'])->name('facultyById');
  Route::get('/departmentById/{id}', [App\Http\Controllers\User\ApplicationController::class, 'departmentById'])->name('departmentById');

});
