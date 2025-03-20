<?php
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');


use Illuminate\Support\Facades\Route;
use App\Http\Middleware\GlobalDataMiddleware;
use App\Http\Middleware\StudentAccess;


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
  return view('welcome');
});

Auth::routes(['register' => false, 'login' => false]);



Route::get('/testEmail', [App\Http\Controllers\HomeController::class, 'csrfErrorPage'])->name('csrf_error_page');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/checkDataBalance', [App\Http\Controllers\HomeController::class, 'checkDataBalance'])->name('checkDataBalance');

Route::post('/updateNotificationStatus', [App\Http\Controllers\HomeController::class, 'updateNotificationStatus'])->name('updateNotificationStatus');
Route::get('/verifyPayment', [App\Http\Controllers\PaymentController::class, 'verifyPayment'])->name('verifyPayment');
Route::get('/raveVerifyPayment', [App\Http\Controllers\PaymentController::class, 'raveVerifyPayment'])->name('raveVerifyPayment');
Route::get('/upperlinkVerifyPayment', [App\Http\Controllers\PaymentController::class, 'upperlinkVerifyPayment'])->name('upperlinkVerifyPayment');
Route::get('/monnifyVerifyPayment', [App\Http\Controllers\Student\StudentController::class, 'monnifyVerifyPayment'])->name('monnifyVerifyPayment');


Route::post('/paystackWebhook', [App\Http\Controllers\PaymentController::class, 'paystackWebhook']);
Route::post('/raveWebhook', [App\Http\Controllers\PaymentController::class, 'raveWebhook']);
Route::post('/monnifyWebhook', [App\Http\Controllers\PaymentController::class, 'monnifyWebhook']);
Route::get('/callback', [App\Http\Controllers\PaymentController::class, 'callback']);

Route::get('/examDocket/{slug}', [App\Http\Controllers\HomeController::class, 'getExamDocket']);
Route::get('/studentDetails/{slug}', [App\Http\Controllers\HomeController::class, 'studentDetails']);
Route::get('/calculateStudentCGPA', [App\Http\Controllers\Admin\CronController::class, 'calculateStudentCGPA']);
Route::get('/generateStudentReferrerCode', [App\Http\Controllers\Admin\StudentController::class, 'generateStudentReferrerCode']);
Route::get('/sendParentOnboardingMail', [App\Http\Controllers\Admin\CronController::class, 'sendParentOnboardingMail']);
Route::get('/deletePendingTransactions', [App\Http\Controllers\CronController::class, 'deletePendingTransactions']);
Route::get('/exportDatabase', [App\Http\Controllers\CronController::class, 'exportDatabase']);
Route::get('/updateReferrers', [App\Http\Controllers\CronController::class, 'updateReferrers']);
Route::get('/massBandwidthCreation', [App\Http\Controllers\CronController::class, 'massBandwidthCreation']);
Route::get('/massEmailCreation', [App\Http\Controllers\CronController::class, 'massEmailCreation']);
Route::get('/updateGrades', [App\Http\Controllers\CronController::class, 'updateGrades']);
Route::post('/getPayments', [App\Http\Controllers\HomeController::class, 'getPayments']);
Route::post('/getProgrammeCategory', [App\Http\Controllers\HomeController::class, 'getProgrammeCategory']);


Route::post('/addStaffRecord', [App\Http\Controllers\HomeController::class, 'addStaffRecord'])->name('addStaffRecord');
Route::get('/staffRecord', [App\Http\Controllers\HomeController::class, 'staffRecord'])->name('staffRecord');

Route::get('/getDepartments/{id}', [App\Http\Controllers\HomeController::class, 'getDepartments'])->name('getDepartments');

Route::get('google/auth', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('google.auth');
Route::get('google/callback', [App\Http\Controllers\GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::group(['prefix' => 's'], function () {
  Route::get('/{code}', [App\Http\Controllers\UrlShortenerController::class, 'redirect']);
});


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

  Route::get('/admins', [App\Http\Controllers\Admin\AdminController::class, 'admins'])->name('admins')->middleware(['auth:admin']);
  Route::post('/addAdmin', [App\Http\Controllers\Admin\AdminController::class, 'addAdmin'])->name('addAdmin')->middleware(['auth:admin']);
  Route::post('/updateAdmin', [App\Http\Controllers\Admin\AdminController::class, 'updateAdmin'])->name('updateAdmin')->middleware(['auth:admin']);
  Route::post('/deleteAdmin', [App\Http\Controllers\Admin\AdminController::class, 'deleteAdmin'])->name('deleteAdmin')->middleware(['auth:admin']);

  Route::post('/createNewApplicant', [App\Http\Controllers\Admin\AdmissionController::class, 'createNewApplicant'])->name('createNewApplicant')->middleware(['auth:admin']);

  Route::post('/expel', [App\Http\Controllers\Admin\StudentDisciplinaryController::class, 'expel'])->name('expel')->middleware(['auth:admin']);
  Route::post('/suspend', [App\Http\Controllers\Admin\StudentDisciplinaryController::class, 'suspend'])->name('suspend')->middleware(['auth:admin']);
  Route::post('/recall', [App\Http\Controllers\Admin\StudentDisciplinaryController::class, 'recall'])->name('recall')->middleware(['auth:admin']);

  Route::get('/messageStudent', [App\Http\Controllers\Admin\CommunicationController::class, 'messageStudent'])->name('messageStudent')->middleware(['auth:admin']);
  Route::get('/messageParent', [App\Http\Controllers\Admin\CommunicationController::class, 'messageParent'])->name('messageParent')->middleware(['auth:admin']);
  Route::get('/messageAllStudent', [App\Http\Controllers\Admin\CommunicationController::class, 'messageAllStudent'])->name('messageAllStudent')->middleware(['auth:admin']);
  Route::get('/messageAllParent', [App\Http\Controllers\Admin\CommunicationController::class, 'messageAllParent'])->name('messageAllParent')->middleware(['auth:admin']);

  Route::post('/communication/getStudent', [App\Http\Controllers\Admin\CommunicationController::class, 'getStudent'])->name('getStudent')->middleware(['auth:admin']);
  Route::post('/sendStudentMessage', [App\Http\Controllers\Admin\CommunicationController::class, 'sendStudentMessage'])->name('sendStudentMessage')->middleware(['auth:admin']);
  Route::post('/sendParentMessage', [App\Http\Controllers\Admin\CommunicationController::class, 'sendParentMessage'])->name('sendParentMessage')->middleware(['auth:admin']);

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
  
  Route::post('/genExamDocket', [App\Http\Controllers\Admin\AcademicController::class, 'genExamDocket'])->name('genExamDocket')->middleware(['auth:admin']);

  Route::get('/programmeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'programmeCategory'])->name('programmeCategory')->middleware(['auth:admin']);
  Route::post('/addProgrammeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'addProgrammeCategory'])->name('addProgrammeCategory')->middleware(['auth:admin']);
  Route::post('/updateProgrammeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'updateProgrammeCategory'])->name('updateProgrammeCategory')->middleware(['auth:admin']);
  Route::post('/deleteProgrammeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'deleteProgrammeCategory'])->name('deleteProgrammeCategory')->middleware(['auth:admin']);
  
  Route::get('/programmeRequirement', [App\Http\Controllers\Admin\ProgrammeController::class, 'programmeRequirement'])->name('programmeRequirement')->middleware(['auth:admin']);
  Route::post('/addProgrammeRequirement', [App\Http\Controllers\Admin\ProgrammeController::class, 'addProgrammeRequirement'])->name('addProgrammeRequirement')->middleware(['auth:admin']);
  Route::post('/updateProgrammeRequirement', [App\Http\Controllers\Admin\ProgrammeController::class, 'updateProgrammeRequirement'])->name('updateProgrammeRequirement')->middleware(['auth:admin']);
  Route::post('/deleteProgrammeRequirement', [App\Http\Controllers\Admin\ProgrammeController::class, 'deleteProgrammeRequirement'])->name('deleteProgrammeRequirement')->middleware(['auth:admin']);
  
  Route::get('/populateFaculty', [App\Http\Controllers\Admin\CronController::class, 'populateFaculty'])->name('populateFaculty')->middleware(['auth:admin']);
  Route::get('/populateCourse', [App\Http\Controllers\Admin\CronController::class, 'populateCourse'])->name('populateCourse')->middleware(['auth:admin']);
  Route::get('/populateStaff', [App\Http\Controllers\Admin\CronController::class, 'populateStaff'])->name('populateStaff')->middleware(['auth:admin']);

  Route::post('/addFaculty', [App\Http\Controllers\Admin\AcademicController::class, 'addFaculty'])->name('addFaculty')->middleware(['auth:admin']);
  Route::post('/updateFaculty', [App\Http\Controllers\Admin\AcademicController::class, 'updateFaculty'])->name('updateFaculty')->middleware(['auth:admin']);
  Route::post('/deleteFaculty', [App\Http\Controllers\Admin\AcademicController::class, 'deleteFaculty'])->name('deleteFaculty')->middleware(['auth:admin']);

  Route::post('/addDepartment', [App\Http\Controllers\Admin\AcademicController::class, 'addDepartment'])->name('addDepartment')->middleware(['auth:admin']);
  Route::post('/updateDepartment', [App\Http\Controllers\Admin\AcademicController::class, 'updateDepartment'])->name('updateDepartment')->middleware(['auth:admin']);
  Route::post('/deleteDepartment', [App\Http\Controllers\Admin\AcademicController::class, 'deleteDepartment'])->name('deleteDepartment')->middleware(['auth:admin']);

  Route::get('/getDepartments/{id}', [App\Http\Controllers\Admin\ProgrammeController::class, 'getDepartments'])->name('getDepartments')->middleware(['auth:admin']);
  Route::get('/getProgrammes/{id}', [App\Http\Controllers\Admin\ProgrammeController::class, 'getProgrammes'])->name('getProgrammes')->middleware(['auth:admin']);

  Route::post('/addProgramme', [App\Http\Controllers\Admin\AcademicController::class, 'addProgramme'])->name('addProgramme')->middleware(['auth:admin']);
  Route::post('/updateProgramme', [App\Http\Controllers\Admin\AcademicController::class, 'updateProgramme'])->name('updateProgramme')->middleware(['auth:admin']);
  Route::post('/deleteProgramme', [App\Http\Controllers\Admin\AcademicController::class, 'deleteProgramme'])->name('deleteProgramme')->middleware(['auth:admin']);

  Route::get('/sessionSetup', [App\Http\Controllers\Admin\AcademicController::class, 'sessionSetup'])->name('sessionSetup')->middleware(['auth:admin']);
  Route::post('/setSession', [App\Http\Controllers\Admin\AcademicController::class, 'setSession'])->name('setSession')->middleware(['auth:admin']);
  Route::post('/setRegistrarSetting', [App\Http\Controllers\Admin\AcademicController::class, 'setRegistrarSetting'])->name('setRegistrarSetting')->middleware(['auth:admin']);
  Route::post('/setFeeStatus', [App\Http\Controllers\Admin\AcademicController::class, 'setFeeStatus'])->name('setFeeStatus')->middleware(['auth:admin']);
  Route::post('/setCampusWideMessage', [App\Http\Controllers\Admin\AcademicController::class, 'setCampusWideMessage'])->name('setCampusWideMessage')->middleware(['auth:admin']);

  Route::post('/addSession', [App\Http\Controllers\Admin\AcademicController::class, 'addSession'])->name('addSession')->middleware(['auth:admin']);
  Route::post('/updateSession', [App\Http\Controllers\Admin\AcademicController::class, 'updateSession'])->name('updateSession')->middleware(['auth:admin']);
  Route::post('/deleteSession', [App\Http\Controllers\Admin\AcademicController::class, 'deleteSession'])->name('deleteSession')->middleware(['auth:admin']);
  
  Route::get('/payments/{programmeCategory}', [App\Http\Controllers\Admin\PaymentController::class, 'payments'])->name('payments')->middleware(['auth:admin']);
  Route::post('/addPayment', [App\Http\Controllers\Admin\PaymentController::class, 'addPayment'])->name('addPayment')->middleware(['auth:admin']);
  Route::post('/updatePayment', [App\Http\Controllers\Admin\PaymentController::class, 'updatePayment'])->name('updatePayment')->middleware(['auth:admin']);
  Route::post('/deletePayment', [App\Http\Controllers\Admin\PaymentController::class, 'deletePayment'])->name('deletePayment')->middleware(['auth:admin']);
  Route::post('/uploadBulkPayment', [App\Http\Controllers\Admin\PaymentController::class, 'uploadBulkPayment'])->name('uploadBulkPayment')->middleware(['auth:admin']);
  Route::post('/generateInvoice', [App\Http\Controllers\Admin\PaymentController::class, 'generateInvoice'])->name('generateInvoice')->middleware(['auth:admin']);
  Route::post('/billsForSessions', [App\Http\Controllers\Admin\PaymentController::class, 'billsForSessions'])->name('billsForSessions')->middleware(['auth:admin']);

  Route::get('/transactions', [App\Http\Controllers\Admin\PaymentController::class, 'transactions'])->name('transactions')->middleware(['auth:admin']);
  Route::get('/transactionReport', [App\Http\Controllers\Admin\PaymentController::class, 'transactionReport'])->name('transactionReport')->middleware(['auth:admin']);
  Route::post('/generateReport', [App\Http\Controllers\Admin\PaymentController::class, 'generateReport'])->name('generateReport')->middleware(['auth:admin']);

  Route::get('/payment/{slug}', [App\Http\Controllers\Admin\PaymentController::class, 'payment'])->name('payment')->middleware(['auth:admin']);
  Route::post('/getPayment', [App\Http\Controllers\Admin\PaymentController::class, 'getPayment'])->name('getPayment')->middleware(['auth:admin']);
  Route::post('/addStructure', [App\Http\Controllers\Admin\PaymentController::class, 'addStructure'])->name('addStructure')->middleware(['auth:admin']);
  Route::post('/updateStructure', [App\Http\Controllers\Admin\PaymentController::class, 'updateStructure'])->name('updateStructure')->middleware(['auth:admin']);
  Route::post('/deleteStructure', [App\Http\Controllers\Admin\PaymentController::class, 'deleteStructure'])->name('deleteStructure')->middleware(['auth:admin']);

  Route::post('/chargeStudent', [App\Http\Controllers\Admin\PaymentController::class, 'chargeStudent'])->name('chargeStudent')->middleware(['auth:admin']);
  Route::get('/chargeStudent', [App\Http\Controllers\Admin\AdminController::class, 'chargeStudent'])->name('chargeStudent')->middleware(['auth:admin']);
  Route::post('/getStudentPayment', [App\Http\Controllers\Admin\PaymentController::class, 'getStudentPayment'])->name('getStudentPayment')->middleware(['auth:admin']);
  Route::post('/editTransaction', [App\Http\Controllers\Admin\PaymentController::class, 'editTransaction'])->name('editTransaction')->middleware(['auth:admin']);
  Route::post('/deleteTransaction', [App\Http\Controllers\Admin\PaymentController::class, 'deleteTransaction'])->name('deleteTransaction')->middleware(['auth:admin']);
  Route::post('/walletTopUp', [App\Http\Controllers\Admin\PaymentController::class, 'walletTopUp'])->name('walletTopUp')->middleware(['auth:admin']);
  Route::post('/chargeStudents', [App\Http\Controllers\Admin\PaymentController::class, 'chargeStudents'])->name('chargeStudents')->middleware(['auth:admin']);

  Route::get('/applicants/{programmeCategory}', [App\Http\Controllers\Admin\AdmissionController::class, 'applicants'])->name('applicants')->middleware(['auth:admin']);
  Route::get('/applicant/{slug}', [App\Http\Controllers\Admin\AdmissionController::class, 'applicant'])->name('applicant')->middleware(['auth:admin']);
  Route::post('/applicantWithSession', [App\Http\Controllers\Admin\AdmissionController::class, 'applicantWithSession'])->name('applicantWithSession')->middleware(['auth:admin']);

  Route::get('/students/{programmeCategory}', [App\Http\Controllers\Admin\AdmissionController::class, 'students'])->name('students')->middleware(['auth:admin']);
  Route::get('/student/{slug}', [App\Http\Controllers\Admin\AdmissionController::class, 'student'])->name('student')->middleware(['auth:admin']);
  Route::post('/resendGuardianOnboarding', [App\Http\Controllers\Admin\StudentController::class, 'resendGuardianOnboarding'])->name('resendGuardianOnboarding')->middleware(['auth:admin']);
  Route::post('/refreshPasscode', [App\Http\Controllers\Admin\StudentController::class, 'refreshPasscode'])->name('refreshPasscode')->middleware(['auth:admin']);

  Route::get('/faculties', [App\Http\Controllers\Admin\AcademicController::class, 'faculties'])->name('faculties')->middleware(['auth:admin']);
  Route::get('/faculty/{slug}', [App\Http\Controllers\Admin\AcademicController::class, 'faculty'])->name('faculty')->middleware(['auth:admin']);
  Route::post('/saveFaculty', [App\Http\Controllers\Admin\AcademicController::class, 'saveFaculty'])->name('saveFaculty')->middleware(['auth:admin']);

  Route::get('/departments', [App\Http\Controllers\Admin\AcademicController::class, 'departments'])->name('departments')->middleware(['auth:admin']);
  Route::get('/department/{slug}', [App\Http\Controllers\Admin\AcademicController::class, 'department'])->name('department')->middleware(['auth:admin']);
  Route::post('/saveDepartment', [App\Http\Controllers\Admin\AcademicController::class, 'saveDepartment'])->name('saveDepartment')->middleware(['auth:admin']);

  Route::get('/programmes', [App\Http\Controllers\Admin\ProgrammeController::class, 'programmes'])->name('programmes')->middleware(['auth:admin']);
  Route::get('/programme/{slug}', [App\Http\Controllers\Admin\ProgrammeController::class, 'programme'])->name('programme')->middleware(['auth:admin']);
  Route::post('/saveProgramme', [App\Http\Controllers\Admin\ProgrammeController::class, 'saveProgramme'])->name('saveProgramme')->middleware(['auth:admin']);

  Route::post('/manageAdmission', [App\Http\Controllers\Admin\AdmissionController::class, 'manageAdmission'])->name('manageAdmission')->middleware(['auth:admin']);
  Route::post('/updateApplicant', [App\Http\Controllers\Admin\AdmissionController::class, 'updateApplicant'])->name('updateApplicant')->middleware(['auth:admin']); 
  
  
  Route::post('/manageCourseReg', [App\Http\Controllers\Admin\AcademicController::class, 'manageCourseReg'])->name('manageCourseReg')->middleware(['auth:admin']);
  Route::get('/courseRegMgt', [App\Http\Controllers\Admin\AcademicController::class, 'courseRegMgt'])->name('courseRegMgt')->middleware(['auth:admin']);
  Route::post('/setCourseRegStatus', [App\Http\Controllers\Admin\AcademicController::class, 'setCourseRegStatus'])->name('setCourseRegStatus')->middleware(['auth:admin']);
  Route::get('/matriculants/{programmeCategory}', [App\Http\Controllers\Admin\AdmissionController::class, 'matriculants'])->name('matriculants')->middleware(['auth:admin']);
  Route::post('/generateAdmissionLetter', [App\Http\Controllers\Admin\AdmissionController::class, 'generateAdmissionLetter'])->name('generateAdmissionLetter')->middleware(['auth:admin']);


  Route::get('/courseAllocation', [App\Http\Controllers\Admin\StaffController::class, 'courseAllocation'])->name('courseAllocation')->middleware(['auth:admin']);
  Route::post('/getCourses', [App\Http\Controllers\Admin\StaffController::class, 'getCourses'])->name('getCourses')->middleware(['auth:admin']);

  Route::get('/staff', [App\Http\Controllers\Admin\StaffController::class, 'staff'])->name('staff')->middleware(['auth:admin']);
  Route::get('/staff/{slug}', [App\Http\Controllers\Admin\StaffController::class, 'singleStaff'])->name('singleStaff')->middleware(['auth:admin']);

  Route::post('/updateStaff', [App\Http\Controllers\Admin\StaffController::class, 'updateStaff'])->name('updateStaff')->middleware(['auth:admin']);

  Route::get('/staffRoles', [App\Http\Controllers\Admin\StaffController::class, 'roles'])->name('roles')->middleware(['auth:admin']);
  Route::post('/addRole', [App\Http\Controllers\Admin\StaffController::class, 'addRole'])->name('addRole')->middleware(['auth:admin']);
  Route::post('/updateRole', [App\Http\Controllers\Admin\StaffController::class, 'updateRole'])->name('updateRole')->middleware(['auth:admin']);
  Route::post('/deleteRole', [App\Http\Controllers\Admin\StaffController::class, 'deleteRole'])->name('deleteRole')->middleware(['auth:admin']);
  
  Route::post('/assignRole', [App\Http\Controllers\Admin\StaffController::class, 'assignRole'])->name('assignRole')->middleware(['auth:admin']);
  Route::post('/unAssignRole', [App\Http\Controllers\Admin\StaffController::class, 'unAssignRole'])->name('unAssignRole')->middleware(['auth:admin']);

  Route::post('/disableStaff', [App\Http\Controllers\Admin\StaffController::class, 'disableStaff'])->name('disableStaff')->middleware(['auth:admin']);
  Route::post('/enableStaff', [App\Http\Controllers\Admin\StaffController::class, 'enableStaff'])->name('enableStaff')->middleware(['auth:admin']);

  Route::post('/assignDeanToFaculty', [App\Http\Controllers\Admin\StaffController::class, 'assignDeanToFaculty'])->name('assignDeanToFaculty')->middleware(['auth:admin']);
  Route::post('/assignFacultyOfficerToFaculty', [App\Http\Controllers\Admin\StaffController::class, 'assignFacultyOfficerToFaculty'])->name('assignFacultyOfficerToFaculty')->middleware(['auth:admin']);
  Route::post('/assignHodToDepartment', [App\Http\Controllers\Admin\StaffController::class, 'assignHodToDepartment'])->name('assignHodToDepartment')->middleware(['auth:admin']);
  Route::post('/assignSubDeanToFaculty', [App\Http\Controllers\Admin\StaffController::class, 'assignSubDeanToFaculty'])->name('assignSubDeanToFaculty')->middleware(['auth:admin']);
  
  Route::get('/examDocketMgt', [App\Http\Controllers\Admin\AcademicController::class, 'examDocketMgt'])->name('examDocketMgt')->middleware(['auth:admin']);
  Route::post('/setExamSetting', [App\Http\Controllers\Admin\AcademicController::class, 'setExamSetting'])->name('setExamSetting')->middleware(['auth:admin']);

  Route::get('/campusCapacity', [App\Http\Controllers\Admin\AcademicController::class, 'campusCapacity'])->name('campusCapacity')->middleware(['auth:admin']);
  Route::get('/allStudents', [App\Http\Controllers\Admin\AcademicController::class, 'allStudents'])->name('allStudents')->middleware(['auth:admin']);
  Route::get('/massPromotion/{programmeCategory}', [App\Http\Controllers\Admin\AcademicController::class, 'massPromotion'])->name('massPromotion')->middleware(['auth:admin']);
  Route::get('/demoteStudent', [App\Http\Controllers\Admin\AcademicController::class, 'demoteStudent'])->name('demoteStudent')->middleware(['auth:admin']);
  Route::get('/graduatingStudents/{programmeCategory}', [App\Http\Controllers\Admin\StudentController::class, 'graduatingStudents'])->name('graduatingStudents')->middleware(['auth:admin']);
  Route::post('/graduateStudents', [App\Http\Controllers\Admin\StudentController::class, 'graduateStudents'])->name('graduateStudents')->middleware(['auth:admin']);

  Route::post('/promoteStudent', [App\Http\Controllers\Admin\AcademicController::class, 'promoteStudent'])->name('promoteStudent')->middleware(['auth:admin']);
  Route::post('/getStudent', [App\Http\Controllers\Admin\AcademicController::class, 'getStudent'])->name('getStudent')->middleware(['auth:admin']);
  Route::post('/makeDemoteStudent', [App\Http\Controllers\Admin\AcademicController::class, 'makeDemoteStudent'])->name('makeDemoteStudent')->middleware(['auth:admin']);

  Route::post('/addAdviser', [App\Http\Controllers\Admin\StaffController::class, 'addAdviser'])->name('addAdviser')->middleware(['auth:admin']);
  Route::post('/addExamOfficer', [App\Http\Controllers\Admin\StaffController::class, 'addExamOfficer'])->name('addExamOfficer')->middleware(['auth:admin']);
  Route::post('/getStudents', [App\Http\Controllers\Admin\StaffController::class, 'getStudents'])->name('getStudents')->middleware(['auth:admin']);

  Route::get('/courseRegistrations/{programmeCategory}', [App\Http\Controllers\Admin\AcademicController::class, 'courseRegistrations'])->name('courseRegistrations')->middleware(['auth:admin']);
  Route::get('/courseRegistrations/{programmeCategory}/{academicSession}', [App\Http\Controllers\Admin\AcademicController::class, 'courseRegistrations'])->name('courseRegistrations')->middleware(['auth:admin']);

  Route::post('/downloadStudentCourseRegistrations', [App\Http\Controllers\Admin\AcademicController::class, 'downloadStudentCourseRegistrations'])->name('downloadStudentCourseRegistrations')->middleware(['auth:admin']);


  Route::post('/approveReg', [App\Http\Controllers\Admin\AcademicController::class, 'approveReg'])->name('approveReg')->middleware(['auth:admin']);
  Route::post('/undoReg', [App\Http\Controllers\Admin\AcademicController::class, 'undoReg'])->name('undoReg')->middleware(['auth:admin']);
  Route::post('/resetCourseReg', [App\Http\Controllers\Admin\AcademicController::class, 'resetCourseReg'])->name('resetCourseReg')->middleware(['auth:admin']);

  Route::post('/resetStudentCourseReg', [App\Http\Controllers\Admin\AcademicController::class, 'resetStudentCourseReg'])->name('resetStudentCourseReg')->middleware(['auth:admin']);
  Route::post('/setStudentCourseRegStatus', [App\Http\Controllers\Admin\AcademicController::class, 'setStudentCourseRegStatus'])->name('setStudentCourseRegStatus')->middleware(['auth:admin']);

  Route::get('/studentProfile/{slug}', [App\Http\Controllers\Admin\AcademicController::class, 'studentProfile'])->name('studentProfile')->middleware(['auth:admin']);

  Route::get('/getStudentResults', [App\Http\Controllers\Admin\ResultController::class, 'getStudentResults'])->name('getStudentResults')->middleware(['auth:admin']);
  Route::get('/getStudentResultPerYear', [App\Http\Controllers\Admin\ResultController::class, 'getStudentResultPerYear'])->name('getStudentResultPerYear')->middleware(['auth:admin']);
  Route::post('/studentResultPerYear', [App\Http\Controllers\Admin\ResultController::class, 'studentResultPerYear'])->name('studentResultPerYear')->middleware(['auth:admin']);

  Route::get('/getStudentMissingResults/{semester}', [App\Http\Controllers\Admin\ResultController::class, 'getStudentMissingResults'])->name('getStudentMissingResults')->middleware(['auth:admin']);
  Route::get('/getStudentMissingResults/{semester}/{academicSession}', [App\Http\Controllers\Admin\ResultController::class, 'getStudentMissingResults'])->name('getStudentMissingResults')->middleware(['auth:admin']);
  Route::post('/generateStudentResults', [App\Http\Controllers\Admin\ResultController::class, 'generateStudentResults'])->name('generateStudentResults')->middleware(['auth:admin']);
  Route::get('/getStudentResultSummary', [App\Http\Controllers\Admin\ResultController::class, 'getStudentResultSummary'])->name('getStudentResultSummary')->middleware(['auth:admin']);
  Route::post('/generateStudentResultSummary', [App\Http\Controllers\Admin\ResultController::class, 'generateStudentResultSummary'])->name('generateStudentResultSummary')->middleware(['auth:admin']);
  Route::post('/generateResultBroadSheet', [App\Http\Controllers\Admin\ResultController::class, 'generateResultBroadSheet'])->name('generateResultBroadSheet')->middleware(['auth:admin']);

  Route::post('/approveResult', [App\Http\Controllers\Admin\ResultController::class, 'approveResult'])->name('approveResult')->middleware(['auth:admin']);

  Route::get('/studentCourses/{programmeCategory}', [App\Http\Controllers\Admin\ProgrammeController::class, 'studentCourses'])->name('studentCourses')->middleware(['auth:admin']);
  Route::post('/getStudentCourses', [App\Http\Controllers\Admin\ProgrammeController::class, 'getStudentCourses'])->name('getStudentCourses')->middleware(['auth:admin']);
  Route::get('/courseDetail/{id}/{programmeCategory}/{academicSession}', [App\Http\Controllers\Admin\ProgrammeController::class, 'courseDetail'])->name('courseDetail')->middleware(['auth:admin']);
  Route::get('/courseDetail/{id}/{programmeCategory}', [App\Http\Controllers\Admin\ProgrammeController::class, 'courseDetail'])->name('courseDetail')->middleware(['auth:admin']);

  Route::post('/sendMessage', [App\Http\Controllers\Admin\ProgrammeController::class, 'sendMessage'])->name('sendMessage')->middleware(['auth:admin']);

  
  Route::post('/uploadVocationResult', [App\Http\Controllers\Admin\ProgrammeController::class, 'uploadVocationResult'])->name('uploadVocationResult')->middleware(['auth:admin']);
  Route::post('/staffUploadResult', [App\Http\Controllers\Admin\ProgrammeController::class, 'staffUploadResult'])->name('staffUploadResult')->middleware(['auth:admin']);
  Route::post('/updateStudentResult', [App\Http\Controllers\Admin\ProgrammeController::class, 'updateStudentResult'])->name('updateStudentResult')->middleware(['auth:admin']);

  Route::post('/generateResult', [App\Http\Controllers\Admin\ResultController::class, 'generateResult'])->name('generateResult')->middleware(['auth:admin']);
  Route::post('/uploadStudentImage', [App\Http\Controllers\Admin\StaffController::class, 'uploadStudentImage'])->name('uploadStudentImage')->middleware(['auth:admin']);
  Route::post('/changeStudentPassword', [App\Http\Controllers\Admin\StaffController::class, 'changeStudentPassword'])->name('changeStudentPassword')->middleware(['auth:admin']);
  Route::post('/changeStudentCreditLoad', [App\Http\Controllers\Admin\StaffController::class, 'changeStudentCreditLoad'])->name('changeStudentCreditLoad')->middleware(['auth:admin']);
  Route::post('/changeStudentName', [App\Http\Controllers\Admin\StaffController::class, 'changeStudentName'])->name('changeStudentName')->middleware(['auth:admin']);
  Route::post('/changeStudentLevel', [App\Http\Controllers\Admin\StaffController::class, 'changeStudentLevel'])->name('changeStudentLevel')->middleware(['auth:admin']);
  Route::post('/changeStudentBatch', [App\Http\Controllers\Admin\StaffController::class, 'changeStudentBatch'])->name('changeStudentBatch')->middleware(['auth:admin']);

  Route::get('/partners', [App\Http\Controllers\Admin\PartnerController::class, 'partners'])->name('partners')->middleware(['auth:admin']);
  Route::get('/partnerApproval', [App\Http\Controllers\Admin\PartnerController::class, 'partnerApproval'])->name('partnerApproval')->middleware(['auth:admin']);
  Route::post('/approvePartner', [App\Http\Controllers\Admin\PartnerController::class, 'approvePartner'])->name('approvePartner')->middleware(['auth:admin']);
  Route::post('/deletePartner', [App\Http\Controllers\Admin\PartnerController::class, 'deletePartner'])->name('deletePartner')->middleware(['auth:admin']);
  Route::get('/partner/{slug}', [App\Http\Controllers\Admin\PartnerController::class, 'partner'])->name('partner')->middleware(['auth:admin']);

  Route::get('/departmentForCourses', [App\Http\Controllers\Admin\AcademicController::class, 'departmentForCourses'])->name('departmentForCourses')->middleware(['auth:admin']);
  Route::get('/departmentCourse/{slug}', [App\Http\Controllers\Admin\AcademicController::class, 'departmentForCourse'])->name('departmentForCourse')->middleware(['auth:admin']);
  Route::post('/addCourse', [App\Http\Controllers\Admin\AcademicController::class, 'addCourse'])->name('addCourse')->middleware(['auth:admin']);
  Route::post('/updateCourse', [App\Http\Controllers\Admin\AcademicController::class, 'updateCourse'])->name('updateCourse')->middleware(['auth:admin']);
  Route::post('/assignCourse', [App\Http\Controllers\Admin\StaffController::class, 'assignCourse'])->name('assignCourse')->middleware(['auth:admin']);
  Route::post('/unsetStaff', [App\Http\Controllers\Admin\StaffController::class, 'unsetStaff'])->name('unsetStaff')->middleware(['auth:admin']);
  
  Route::get('/getCourseResult', [App\Http\Controllers\Admin\ProgrammeController::class, 'getCourseResult'])->name('getCourseResult')->middleware(['auth:admin']);
  Route::post('/getCourseResults', [App\Http\Controllers\Admin\ProgrammeController::class, 'getCourseResults'])->name('getCourseResults')->middleware(['auth:admin']);
  Route::post('/updateCourseResult', [App\Http\Controllers\Admin\ProgrammeController::class, 'updateCourseResult'])->name('updateCourseResult')->middleware(['auth:admin']);

  Route::post('/addCourseForStudent', [App\Http\Controllers\Admin\ProgrammeController::class, 'addCourseForStudent'])->name('addCourseForStudent')->middleware(['auth:admin']);
  Route::post('/deleteCourseForStudent', [App\Http\Controllers\Admin\ProgrammeController::class, 'deleteCourseForStudent'])->name('deleteCourseForStudent')->middleware(['auth:admin']);
  Route::post('/updateCourseForStudent', [App\Http\Controllers\Admin\ProgrammeController::class, 'updateCourseForStudent'])->name('updateCourseForStudent')->middleware(['auth:admin']);

  Route::post('/changeStudentProgramme', [App\Http\Controllers\Admin\ProgrammeController::class, 'changeStudentProgramme'])->name('changeStudentProgramme')->middleware(['auth:admin']);
  Route::get('/changeProgramme', [App\Http\Controllers\Admin\ProgrammeController::class, 'changeProgramme'])->name('changeProgramme')->middleware(['auth:admin']);
  Route::post('/acad/getStudent', [App\Http\Controllers\Admin\ProgrammeController::class, 'getStudent'])->name('getStudent')->middleware(['auth:admin']);

  Route::get('/studentExits', [App\Http\Controllers\Admin\StudentCareController::class, 'studentExits'])->name('studentExits')->middleware(['auth:admin']);
  Route::post('getExitApplication', [App\Http\Controllers\Admin\StudentCareController::class, 'getExitApplication'])->name('getExitApplication')->middleware(['auth:admin']);
  Route::post('manageExitApplication', [App\Http\Controllers\Admin\StudentCareController::class, 'manageExitApplication'])->name('manageExitApplication')->middleware(['auth:admin']);
  Route::post('bulkManageExitApplications', [App\Http\Controllers\Admin\StudentCareController::class, 'bulkManageExitApplications'])->name('bulkManageExitApplications')->middleware(['auth:admin']);

  

  Route::get('/studentResult', [App\Http\Controllers\Admin\ResultController::class, 'studentResult'])->name('studentResult')->middleware(['auth:admin']);
  Route::post('/result/getStudent', [App\Http\Controllers\Admin\ResultController::class, 'getStudent'])->name('getStudent')->middleware(['auth:admin']);
  Route::post('/result/getStudentResult', [App\Http\Controllers\Admin\ResultController::class, 'getStudentResult'])->name('getStudentResult')->middleware(['auth:admin']);
  Route::post('/result/updateStudentResult', [App\Http\Controllers\Admin\ResultController::class, 'updateStudentResult'])->name('updateStudentResult')->middleware(['auth:admin']);
  Route::post('/result/deleteStudentResult', [App\Http\Controllers\Admin\ResultController::class, 'deleteStudentResult'])->name('deleteStudentResult')->middleware(['auth:admin']);
  Route::post('/result/addStudentCourse', [App\Http\Controllers\Admin\ResultController::class, 'addStudentCourse'])->name('addStudentCourse')->middleware(['auth:admin']);
  
  Route::get('/committees', [App\Http\Controllers\Admin\CommitteeController::class, 'committees'])->name('committees')->middleware(['auth:admin']);
  Route::post('/addCommittee', [App\Http\Controllers\Admin\CommitteeController::class, 'addCommittee'])->name('addCommittee')->middleware(['auth:admin']);
  Route::post('/updateCommittee', [App\Http\Controllers\Admin\CommitteeController::class, 'updateCommittee'])->name('updateCommittee')->middleware(['auth:admin']);
  Route::post('/deleteCommittee', [App\Http\Controllers\Admin\CommitteeController::class, 'deleteCommittee'])->name('deleteCommittee')->middleware(['auth:admin']);
  
  Route::get('/committee/{slug}', [App\Http\Controllers\Admin\CommitteeController::class, 'committee'])->name('committee')->middleware(['auth:admin']);
  Route::post('/assignCommitteePosition', [App\Http\Controllers\Admin\CommitteeController::class, 'assignCommitteePosition'])->name('assignCommitteePosition')->middleware(['auth:admin']);
  Route::post('/addMember', [App\Http\Controllers\Admin\CommitteeController::class, 'addMember'])->name('addMember')->middleware(['auth:admin']);
  Route::post('/deleteMember', [App\Http\Controllers\Admin\CommitteeController::class, 'deleteMember'])->name('deleteMember')->middleware(['auth:admin']);


  Route::post('/createMeeting', [App\Http\Controllers\Admin\CommitteeController::class, 'createMeeting'])->name('createMeeting')->middleware(['auth:admin']);
  Route::post('/updateMeeting', [App\Http\Controllers\Admin\CommitteeController::class, 'updateMeeting'])->name('updateMeeting')->middleware(['auth:admin']);
  Route::post('/deleteMeeting', [App\Http\Controllers\Admin\CommitteeController::class, 'deleteMeeting'])->name('deleteMeeting')->middleware(['auth:admin']);
  
  Route::post('/enableStudent', [App\Http\Controllers\Admin\AcademicController::class, 'enableStudent'])->name('enableStudent')->middleware(['auth:admin']);
  Route::post('/expelStudent', [App\Http\Controllers\Admin\AcademicController::class, 'expelStudent'])->name('expelStudent')->middleware(['auth:admin']);
  Route::get('/deletedStudents', [App\Http\Controllers\Admin\AcademicController::class, 'deletedStudents'])->name('expelStudent')->middleware(['auth:admin']);
  
  Route::get('/expelledStudents', [App\Http\Controllers\Admin\AcademicController::class, 'expelledStudents'])->name('expelledStudents')->middleware(['auth:admin']);
  Route::get('/suspendedStudents', [App\Http\Controllers\Admin\AcademicController::class, 'suspendedStudents'])->name('suspendedStudents')->middleware(['auth:admin']);

  Route::get('/verifyStudentExits', [App\Http\Controllers\Admin\StudentCareController::class, 'verifyStudentExits'])->name('verifyStudentExits')->middleware(['auth:admin']);
  Route::post('/verifyStudentExit', [App\Http\Controllers\Admin\StudentCareController::class, 'verifyStudentExit'])->name('verifyStudentExit')->middleware(['auth:admin']);
  
  Route::post('/leftSchool', [App\Http\Controllers\Admin\StudentCareController::class, 'leftSchool'])->name('leftSchool')->middleware(['auth:admin']);
  Route::post('/enterSchool', [App\Http\Controllers\Admin\StudentCareController::class, 'enterSchool'])->name('enterSchool')->middleware(['auth:admin']);
  
  Route::get('/units', [App\Http\Controllers\Admin\UnitController::class, 'units'])->name('units')->middleware(['auth:admin']);
  Route::post('/addUnit', [App\Http\Controllers\Admin\UnitController::class, 'addUnit'])->name('addUnit')->middleware(['auth:admin']);
  Route::post('/updateUnit', [App\Http\Controllers\Admin\UnitController::class, 'updateUnit'])->name('updateUnit')->middleware(['auth:admin']);
  Route::post('/deleteUnit', [App\Http\Controllers\Admin\UnitController::class, 'deleteUnit'])->name('deleteUnit')->middleware(['auth:admin']);

  Route::get('/attendance', [App\Http\Controllers\Admin\AttendanceController::class, 'attendance'])->name('attendance')->middleware(['auth:admin']);
  Route::get('/monthlyAttendance/{slug}', [App\Http\Controllers\Admin\AttendanceController::class, 'monthlyAttendance'])->middleware(['auth:admin']);
  Route::get('/updateAttendance/{staffId}', [App\Http\Controllers\Admin\AttendanceController::class, 'updateAttendance'])->middleware(['auth:admin']);
  Route::post('/uploadAttendance', [App\Http\Controllers\Admin\AttendanceController::class, 'uploadAttendance'])->name('uploadAttendance')->middleware(['auth:admin']);

  Route::get('/manageLeaves', [App\Http\Controllers\Admin\LeaveController::class, 'manageLeaves'])->name('manageLeaves')->middleware(['auth:admin']);
  Route::post('/manageLeave', [App\Http\Controllers\Admin\LeaveController::class, 'manageLeave'])->name('manageLeave')->middleware(['auth:admin']);

  Route::get('/adviserProgrammes/{programmeCategory}', [App\Http\Controllers\Admin\ProgrammeController::class, 'adviserProgrammes'])->name('adviserProgrammes')->middleware(['auth:admin']);
  Route::get('/levelCourseReg/{programmeCategory}/{id}', [App\Http\Controllers\Admin\ProgrammeController::class, 'levelCourseReg'])->name('levelCourseReg')->middleware(['auth:admin']);
  Route::get('/levelStudents/{programmeCategory}/{id}', [App\Http\Controllers\Admin\ProgrammeController::class, 'levelStudents'])->name('levelStudents')->middleware(['auth:admin']);
  Route::post('/courseApproval', [App\Http\Controllers\Admin\ProgrammeController::class, 'courseApproval'])->name('courseApproval')->middleware(['auth:admin']);

  Route::post('/manageClearanceApplication', [App\Http\Controllers\Admin\StudentController::class, 'manageClearanceApplication'])->name('manageClearanceApplication')->middleware(['auth:admin']);

  Route::get('/hostelType', [App\Http\Controllers\Admin\HostelController::class, 'hostelType'])->name('hostelType')->middleware(['auth:admin']);
  Route::post('/addRoomType', [App\Http\Controllers\Admin\HostelController::class, 'addRoomType'])->name('addRoomType')->middleware(['auth:admin']);
  Route::post('/updateRoomType', [App\Http\Controllers\Admin\HostelController::class, 'updateRoomType'])->name('updateRoomType')->middleware(['auth:admin']);
  Route::post('/deleteRoomType', [App\Http\Controllers\Admin\HostelController::class, 'deleteRoomType'])->name('deleteRoomType')->middleware(['auth:admin']);

  Route::get('/hostel', [App\Http\Controllers\Admin\HostelController::class, 'hostel'])->name('hostel')->middleware(['auth:admin']);
  Route::post('/addHostel', [App\Http\Controllers\Admin\HostelController::class, 'addHostel'])->name('addHostel')->middleware(['auth:admin']);
  Route::post('/updateHostel', [App\Http\Controllers\Admin\HostelController::class, 'updateHostel'])->name('updateHostel')->middleware(['auth:admin']);
  Route::post('/deleteHostel', [App\Http\Controllers\Admin\HostelController::class, 'deleteHostel'])->name('deleteHostel')->middleware(['auth:admin']);
  Route::get('/viewHostel/{slug}', [App\Http\Controllers\Admin\HostelController::class, 'viewHostel'])->name('viewHostel')->middleware(['auth:admin']);
  Route::post('/addRoom', [App\Http\Controllers\Admin\HostelController::class, 'addRoom'])->name('addRoom')->middleware(['auth:admin']);
  Route::post('/deleteRoom', [App\Http\Controllers\Admin\HostelController::class, 'deleteRoom'])->name('deleteRoom')->middleware(['auth:admin']);
  Route::post('/reserveRoom', [App\Http\Controllers\Admin\HostelController::class, 'reserveRoom'])->name('reserveRoom')->middleware(['auth:admin']);


  Route::get('/allocations', [App\Http\Controllers\Admin\HostelController::class, 'allocations'])->name('allocations')->middleware(['auth:admin']);
  Route::post('/deleteAllocation', [App\Http\Controllers\Admin\HostelController::class, 'deleteAllocation'])->name('deleteAllocation')->middleware(['auth:admin']);
  Route::post('/allocateRoom', [App\Http\Controllers\Admin\HostelController::class, 'allocateRoom'])->name('allocateRoom')->middleware(['auth:admin']);
  
  Route::get('/bankAccounts', [App\Http\Controllers\Admin\PaymentController::class, 'bankAccounts'])->name('bankAccounts')->middleware(['auth:admin']);
  Route::post('/addBankAccount', [App\Http\Controllers\Admin\PaymentController::class, 'addBankAccount'])->name('addBankAccount')->middleware(['auth:admin']);
  Route::post('/updateBankAccount', [App\Http\Controllers\Admin\PaymentController::class, 'updateBankAccount'])->name('updateBankAccount')->middleware(['auth:admin']);
  Route::post('/deleteBankAccount', [App\Http\Controllers\Admin\PaymentController::class, 'deleteBankAccount'])->name('deleteBankAccount')->middleware(['auth:admin']);

  Route::get('/alumni', [App\Http\Controllers\Admin\StudentController::class, 'alumni'])->name('alumni')->middleware(['auth:admin']);
  Route::post('/manageFinalYearStudentClearance', [App\Http\Controllers\Admin\StudentController::class, 'manageFinalYearStudentClearance'])->name('manageFinalYearStudentClearance')->middleware(['auth:admin']);

  Route::post('/createLecture', [App\Http\Controllers\Admin\ProgrammeController::class, 'createLecture'])->name('createLecture')->middleware(['auth:admin']);
  Route::post('/updateLecture', [App\Http\Controllers\Admin\ProgrammeController::class, 'updateLecture'])->name('updateLecture')->middleware(['auth:admin']);
  Route::post('/deleteLecture', [App\Http\Controllers\Admin\ProgrammeController::class, 'deleteLecture'])->name('deleteLecture')->middleware(['auth:admin']);
  Route::post('/staffUploadAttendance', [App\Http\Controllers\Admin\ProgrammeController::class, 'staffUploadAttendance'])->name('staffUploadAttendance')->middleware(['auth:admin']);
  Route::post('/markStudentAttendance', [App\Http\Controllers\Admin\ProgrammeController::class, 'markStudentAttendance'])->name('markStudentAttendance')->middleware(['auth:admin']);
  Route::post('/deleteStudentAttendance', [App\Http\Controllers\Admin\ProgrammeController::class, 'deleteStudentAttendance'])->name('deleteStudentAttendance')->middleware(['auth:admin']);
  
  Route::post('/postJobVacancy', [App\Http\Controllers\Admin\CareerController::class, 'postJobVacancy'])->name('postJobVacancy')->middleware(['auth:admin']);
  Route::get('/viewJobVacancy/{slug}', [App\Http\Controllers\Admin\CareerController::class, 'viewJobVacancy'])->name('viewJobVacancy')->middleware(['auth:admin']);
  Route::post('/updateJobVacancy', [App\Http\Controllers\Admin\CareerController::class, 'updateJobVacancy'])->name('updateJobVacancy')->middleware(['auth:admin']);
  Route::post('/deleteJobVacancy', [App\Http\Controllers\Admin\CareerController::class, 'deleteJobVacancy'])->name('deleteJobVacancy')->middleware(['auth:admin']);
  Route::post('/updateApplicantStatus', [App\Http\Controllers\Admin\CareerController::class, 'updateApplicantStatus'])->name('updateApplicantStatus')->middleware(['auth:admin']);
  Route::post('/uploadApplicantAppointmentLetter', [App\Http\Controllers\Admin\CareerController::class, 'uploadApplicantAppointmentLetter'])->name('uploadApplicantAppointmentLetter')->middleware(['auth:admin']);

  

  Route::get('/prospectiveStaff', [App\Http\Controllers\Admin\CareerController::class, 'prospectiveStaff'])->name('prospectiveStaff')->middleware(['auth:admin']);
  Route::get('/workStudyApplicants', [App\Http\Controllers\Admin\CareerController::class, 'workStudyApplicants'])->name('workStudyApplicants')->middleware(['auth:admin']);
  Route::get('/jobVacancy', [App\Http\Controllers\Admin\CareerController::class, 'jobVacancy'])->name('jobVacancy')->middleware(['auth:admin']);
  Route::get('/siwesApplicants', [App\Http\Controllers\Admin\CareerController::class, 'siwesApplicants'])->name('siwesApplicant')->middleware(['auth:admin']);


  Route::get('/deans', [App\Http\Controllers\Admin\AcademicController::class, 'deans'])->name('deans')->middleware(['auth:admin']);
  Route::get('/hods', [App\Http\Controllers\Admin\AcademicController::class, 'hods'])->name('hods')->middleware(['auth:admin']);
  Route::get('/facultyOfficers', [App\Http\Controllers\Admin\AcademicController::class, 'facultyOfficers'])->name('facultyOfficers')->middleware(['auth:admin']);

  Route::get('/applaudBoards', [App\Http\Controllers\Admin\ApplaudController::class, 'applaudBoards'])->name('applaudBoards')->middleware(['auth:admin']);
  Route::post('/createBoard', [App\Http\Controllers\Admin\ApplaudController::class, 'createBoard'])->name('createBoard')->middleware(['auth:admin']);
  Route::get('/applaudBoard/{slug}', [App\Http\Controllers\Admin\ApplaudController::class, 'applaudBoard'])->name('applaudBoard')->middleware(['auth:admin']);
  Route::post('/updateBoard', [App\Http\Controllers\Admin\ApplaudController::class, 'updateBoard'])->name('updateBoard')->middleware(['auth:admin']);
  Route::post('/deleteBoard', [App\Http\Controllers\Admin\ApplaudController::class, 'deleteBoard'])->name('deleteBoard')->middleware(['auth:admin']);

  Route::get('/viewSuspension/{slug}', [App\Http\Controllers\Admin\StudentDisciplinaryController::class, 'viewSuspension'])->name('admin.viewSuspension')->middleware(['auth:admin']);
  Route::post('/manageSuspension', [App\Http\Controllers\Admin\StudentDisciplinaryController::class, 'manageSuspension'])->name('admin.manageSuspension')->middleware(['auth:admin']);  

});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'student'], function () {
  Route::get('/onBoarding', [App\Http\Controllers\Student\StudentController::class, 'onBoarding'])->name('onBoarding');
  Route::post('/getStudent', [App\Http\Controllers\Student\StudentController::class, 'getStudent'])->name('getStudent');
  Route::post('/saveStudentDetails', [App\Http\Controllers\Student\StudentController::class, 'saveStudentDetails'])->name('saveStudentDetails');

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

  Route::get('/verifyStudentExits', [App\Http\Controllers\HomeController::class, 'verifyStudentExits'])->name('verifyStudentExits');
  Route::post('/verifyStudentExit', [App\Http\Controllers\HomeController::class, 'verifyStudentExit'])->name('verifyStudentExit');
  Route::post('/requeryUpperlinkPayment', [App\Http\Controllers\PaymentController::class, 'requeryUpperlinkPayment']);

  Route::group(['middleware' => ['auth:student', StudentAccess::class]], function () {
    Route::get('/home', [App\Http\Controllers\Student\StudentController::class, 'index'])->name('student.home');

    Route::post('/makePayment', [App\Http\Controllers\Student\StudentController::class, 'makePayment'])->name('student.makePayment');
    Route::post('/setMode', [App\Http\Controllers\Student\StudentController::class, 'setMode'])->name('setMode');

    Route::get('/profile', [App\Http\Controllers\Student\StudentController::class, 'profile'])->name('student.profile');
    Route::post('/profile/saveBioData', [App\Http\Controllers\Student\StudentController::class, 'saveBioData'])->name('student.saveBioData');
    Route::post('/updatePassword', [App\Http\Controllers\Student\StudentController::class, 'updatePassword'])->name('student.updatePassword');
    Route::post('/uploadImage', [App\Http\Controllers\Student\StudentController::class, 'uploadImage'])->name('student.uploadImage');

    Route::get('/antiDrugDeclaration', [App\Http\Controllers\Student\StudentController::class, 'antiDrugDeclaration'])->name('antiDrugDeclaration');
    Route::post('/saveAntiDrugDeclaration', [App\Http\Controllers\Student\StudentController::class, 'saveAntiDrugDeclaration'])->name('saveAntiDrugDeclaration');
    
    Route::get('/transactions', [App\Http\Controllers\Student\StudentController::class, 'transactions'])->name('student.transactions');
    Route::get('/walletTransactions', [App\Http\Controllers\Student\StudentController::class, 'walletTransactions'])->name('student.walletTransactions');
    Route::post('/getPayment', [App\Http\Controllers\Student\StudentController::class, 'getPayment'])->name('student.getPayment');

    Route::get('/courseRegistration', [App\Http\Controllers\Student\AcademicController::class, 'courseRegistration'])->name('courseRegistration');
    Route::post('/registerCourses', [App\Http\Controllers\Student\AcademicController::class, 'registerCourses'])->name('registerCourses');
    Route::post('/printCourseReg', [App\Http\Controllers\Student\AcademicController::class, 'printCourseReg'])->name('printCourseReg');
    Route::get('/editCourseReg', [App\Http\Controllers\Student\AcademicController::class, 'editCourseReg'])->name('editCourseReg');
    Route::get('/allCourseRegs', [App\Http\Controllers\Student\AcademicController::class, 'allCourseRegs'])->name('allCourseRegs');

    Route::get('/examDocket', [App\Http\Controllers\Student\AcademicController::class, 'examDocket'])->name('examDocket');
    Route::post('/genExamDocket', [App\Http\Controllers\Student\AcademicController::class, 'genExamDocket'])->name('genExamDocket');
    Route::get('/allExamDockets', [App\Http\Controllers\Student\AcademicController::class, 'allExamDockets'])->name('allExamDockets');
    Route::post('/printExamCard', [App\Http\Controllers\Student\AcademicController::class, 'printExamCard'])->name('printExamCard');

    Route::get('/examResult', [App\Http\Controllers\Student\AcademicController::class, 'examResult'])->name('student.examResult');
    Route::post('/generateResult', [App\Http\Controllers\Student\AcademicController::class, 'generateResult'])->name('student.generateResult');

    Route::get('/transcript', [App\Http\Controllers\Student\AcademicController::class, 'transcript'])->name('transcript');
    Route::post('/generateInvoice', [App\Http\Controllers\Student\StudentController::class, 'generateInvoice'])->name('generateInvoice');

    Route::get('/mentor', [App\Http\Controllers\Student\StudentController::class, 'mentor'])->name('mentor');
    Route::get('/exits', [App\Http\Controllers\Student\StudentController::class, 'exits'])->name('exits');
    Route::post('exitApplication', [App\Http\Controllers\Student\StudentController::class, 'exitApplication'])->name('exitApplication');

    Route::get('/hallOfFame', [App\Http\Controllers\HomeController::class, 'hallOfFame']);
    Route::get('/purchaseBandwidth', [App\Http\Controllers\Student\StudentController::class, 'purchaseBandwidth'])->name('purchaseBandwidth');
    Route::post('createBandwidthPayment', [App\Http\Controllers\Student\StudentController::class, 'createBandwidthPayment'])->name('createBandwidthPayment');

    Route::get('/reffs', [App\Http\Controllers\Student\StudentController::class, 'reffs'])->name('reffs');
    Route::post('/applicantWithSession', [App\Http\Controllers\Student\StudentController::class, 'applicantWithSession'])->name('applicantWithSession');
    Route::get('/student/{slug}', [App\Http\Controllers\Student\StudentController::class, 'student'])->name('student');
    
    Route::post('/submitClearanceApplication', [App\Http\Controllers\Student\ClearanceController::class, 'submitClearanceApplication'])->name('submitClearanceApplication');
    Route::post('/saveBioData', [App\Http\Controllers\Student\ClearanceController::class, 'saveBioData']);
    Route::post('/guardianBioData', [App\Http\Controllers\Student\ClearanceController::class, 'guardianBioData']);
    Route::post('/saveUtme', [App\Http\Controllers\Student\ClearanceController::class, 'saveUtme']);
    Route::post('/saveSitting', [App\Http\Controllers\Student\ClearanceController::class, 'saveSitting']);
    Route::post('/addOlevel', [App\Http\Controllers\Student\ClearanceController::class, 'addOlevel']);
    Route::post('/addUtme', [App\Http\Controllers\Student\ClearanceController::class, 'addUtme']);
    Route::post('/updateUtme', [App\Http\Controllers\Student\ClearanceController::class, 'updateUtme']);
    Route::post('/updateOlevel', [App\Http\Controllers\Student\ClearanceController::class, 'updateOlevel']);

    Route::post('/deleteUtme', [App\Http\Controllers\Student\ClearanceController::class, 'deleteUtme']);
    Route::post('/deleteOlevel', [App\Http\Controllers\Student\ClearanceController::class, 'deleteOlevel']);
    Route::post('/nokBioData', [App\Http\Controllers\Student\ClearanceController::class, 'nokBioData']);

    Route::post('/saveDe', [App\Http\Controllers\Student\ClearanceController::class, 'saveDe']);
    Route::post('/saveProgramme', [App\Http\Controllers\Student\ClearanceController::class, 'saveProgramme']);
    Route::post('/uploadOlevel', [App\Http\Controllers\Student\ClearanceController::class, 'uploadOlevel']);
    Route::post('/uploadUtme', [App\Http\Controllers\Student\ClearanceController::class, 'uploadUtme']);
    Route::post('/deleteFile', [App\Http\Controllers\Student\ClearanceController::class, 'deleteFile']);

    Route::get('/hostelBooking', [App\Http\Controllers\Student\StudentController::class, 'hostelBooking'])->name('hostelBooking'); 
    Route::post('/getHostels', [App\Http\Controllers\Student\HostelController::class, 'getHostels'])->name('getHostels'); 
    Route::post('/getRoomTypes', [App\Http\Controllers\Student\HostelController::class, 'getRoomTypes'])->name('getRoomTypes'); 
    Route::post('/getTypes', [App\Http\Controllers\Student\HostelController::class, 'getTypes'])->name('getTypes'); 
    Route::post('/getRooms', [App\Http\Controllers\Student\HostelController::class, 'getRooms'])->name('getRooms'); 

    Route::post('/startClearance', [App\Http\Controllers\Student\ClearanceController::class, 'startClearance'])->name('startClearance'); 
    Route::post('/downloadClearance', [App\Http\Controllers\Student\ClearanceController::class, 'downloadClearance'])->name('downloadClearance');

    Route::get('/registeredCourses', [App\Http\Controllers\Student\AcademicController::class, 'registeredCourses'])->name('registeredCourses'); 

    Route::post('/save-player-id', [App\Http\Controllers\Student\StudentController::class, 'savePlayerId'])->name('savePlayerId');

    Route::get('/vacancies', [App\Http\Controllers\Student\CareerController::class, 'vacancies'])->name('vacancies');
    Route::get('/applications', [App\Http\Controllers\Student\CareerController::class, 'applications'])->name('applications');
    Route::post('/apply', [App\Http\Controllers\Student\CareerController::class, 'apply'])->name('apply');
    Route::post('/deleteApplication', [App\Http\Controllers\Student\CareerController::class, 'deleteApplication'])->name('deleteApplication');
    Route::post('/manageApplication', [App\Http\Controllers\Student\CareerController::class, 'manageApplication'])->name('manageApplication');  

    Route::get('/studentDisciplinary', [App\Http\Controllers\Student\StudentDisciplinaryController::class, 'studentDisciplinary'])->name('student.studentDisciplinary');
    Route::get('/viewSuspension/{slug}', [App\Http\Controllers\Student\StudentDisciplinaryController::class, 'viewSuspension'])->name('student.viewSuspension');
    Route::post('/manageSuspension', [App\Http\Controllers\Student\StudentDisciplinaryController::class, 'manageSuspension'])->name('student.manageSuspension');  

  });
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
  Route::get('/courseDetail/{id}/{programmeCategory}/{academicSession}', [App\Http\Controllers\Staff\StaffController::class, 'courseDetail'])->name('courseDetail')->middleware(['auth:staff']);
  Route::get('/courseDetail/{id}/{programmeCategory}', [App\Http\Controllers\Staff\StaffController::class, 'courseDetail'])->name('courseDetail')->middleware(['auth:staff']);

  Route::post('/sendMessage', [App\Http\Controllers\Staff\StaffController::class, 'sendMessage'])->name('sendMessage')->middleware(['auth:staff']);
  Route::post('/unsetStaff', [App\Http\Controllers\Staff\StaffController::class, 'unsetStaff'])->name('unsetStaff')->middleware(['auth:staff']);

  Route::get('/messageStudent', [App\Http\Controllers\Staff\CommunicationController::class, 'messageStudent'])->name('messageStudent')->middleware(['auth:staff']);
  Route::get('/messageParent', [App\Http\Controllers\Staff\CommunicationController::class, 'messageParent'])->name('messageParent')->middleware(['auth:staff']);
  Route::get('/messageAllStudent', [App\Http\Controllers\Staff\CommunicationController::class, 'messageAllStudent'])->name('messageAllStudent')->middleware(['auth:staff']);
  Route::get('/messageAllParent', [App\Http\Controllers\Staff\CommunicationController::class, 'messageAllParent'])->name('messageAllParent')->middleware(['auth:staff']);

  Route::post('/communication/getStudent', [App\Http\Controllers\Staff\CommunicationController::class, 'getStudent'])->name('getStudent')->middleware(['auth:staff']);
  Route::post('/sendStudentMessage', [App\Http\Controllers\Staff\CommunicationController::class, 'sendStudentMessage'])->name('sendStudentMessage')->middleware(['auth:staff']);
  Route::post('/sendParentMessage', [App\Http\Controllers\Staff\CommunicationController::class, 'sendParentMessage'])->name('sendParentMessage')->middleware(['auth:staff']);

  Route::get('/studentCourses/{programmeCategory}', [App\Http\Controllers\Staff\StaffController::class, 'studentCourses'])->name('studentCourses')->middleware(['auth:staff']);
  Route::post('/getStudentCourses', [App\Http\Controllers\Staff\StaffController::class, 'getStudentCourses'])->name('getStudentCourses')->middleware(['auth:staff']);
  Route::post('/staffUploadResult', [App\Http\Controllers\Staff\StaffController::class, 'staffUploadResult'])->name('staffUploadResult')->middleware(['auth:staff']);
  Route::post('/updateStudentResult', [App\Http\Controllers\Staff\StaffController::class, 'updateStudentResult'])->name('updateStudentResult')->middleware(['auth:staff']);
  Route::post('/uploadVocationResult', [App\Http\Controllers\Staff\StaffController::class, 'uploadVocationResult'])->name('uploadVocationResult')->middleware(['auth:staff']);

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

  Route::get('/adviserProgrammes/{programmeCategory}', [App\Http\Controllers\Staff\ProgrammeController::class, 'adviserProgrammes'])->name('adviserProgrammes')->middleware(['auth:staff']);
  Route::get('/studentCourseReg', [App\Http\Controllers\Staff\ProgrammeController::class, 'studentCourseReg'])->name('studentCourseReg')->middleware(['auth:staff']);
  Route::get('/levelCourseReg/{programmeCategory}/{id}', [App\Http\Controllers\Staff\ProgrammeController::class, 'levelCourseReg'])->name('levelCourseReg')->middleware(['auth:staff']);
  Route::get('/levelStudents/{programmeCategory}/{id}', [App\Http\Controllers\Staff\ProgrammeController::class, 'levelStudents'])->name('levelStudents')->middleware(['auth:staff']);

  Route::post('/approveReg', [App\Http\Controllers\Staff\ProgrammeController::class, 'approveReg'])->name('approveReg')->middleware(['auth:staff']);

  Route::get('/getDepartments/{id}', [App\Http\Controllers\Staff\ProgrammeController::class, 'getDepartments'])->name('getDepartments')->middleware(['auth:staff']);
  Route::get('/getProgrammes/{id}', [App\Http\Controllers\Staff\ProgrammeController::class, 'getProgrammes'])->name('getProgrammes')->middleware(['auth:staff']);

  Route::get('/getStudentResults', [App\Http\Controllers\Staff\ResultController::class, 'getStudentResults'])->name('getStudentResults')->middleware(['auth:staff']);
  Route::post('/generateStudentResults', [App\Http\Controllers\Staff\ResultController::class, 'generateStudentResults'])->name('generateStudentResults')->middleware(['auth:staff']);
  Route::post('/approveResult', [App\Http\Controllers\Staff\ResultController::class, 'approveResult'])->name('approveResult')->middleware(['auth:staff']); 

  Route::get('/getStudentResultSummary', [App\Http\Controllers\Staff\ResultController::class, 'getStudentResultSummary'])->name('getStudentResultSummary')->middleware(['auth:staff']);
  Route::post('/generateStudentResultSummary', [App\Http\Controllers\Staff\ResultController::class, 'generateStudentResultSummary'])->name('generateStudentResultSummary')->middleware(['auth:staff']);

  Route::post('/generateResult', [App\Http\Controllers\Staff\ResultController::class, 'generateResult'])->name('generateResult')->middleware(['auth:staff']);
  Route::post('/uploadStudentImage', [App\Http\Controllers\Staff\StaffController::class, 'uploadStudentImage'])->name('uploadStudentImage')->middleware(['auth:staff']);
  Route::post('/changeStudentPassword', [App\Http\Controllers\Staff\StaffController::class, 'changeStudentPassword'])->name('changeStudentPassword')->middleware(['auth:staff']);
  Route::post('/changeStudentCreditLoad', [App\Http\Controllers\Staff\StaffController::class, 'changeStudentCreditLoad'])->name('changeStudentCreditLoad')->middleware(['auth:staff']);
  Route::post('/changeStudentBatch', [App\Http\Controllers\Staff\StaffController::class, 'changeStudentBatch'])->name('changeStudentBatch')->middleware(['auth:staff']);

  Route::get('/payments/{programmeCategory}', [App\Http\Controllers\Staff\PaymentController::class, 'payments'])->name('payments')->middleware(['auth:staff']);
  Route::post('/addPayment', [App\Http\Controllers\Staff\PaymentController::class, 'addPayment'])->name('addPayment')->middleware(['auth:staff']);
  Route::post('/updatePayment', [App\Http\Controllers\Staff\PaymentController::class, 'updatePayment'])->name('updatePayment')->middleware(['auth:staff']);
  Route::post('/deletePayment', [App\Http\Controllers\Staff\PaymentController::class, 'deletePayment'])->name('deletePayment')->middleware(['auth:staff']);
  Route::post('/billsForSessions', [App\Http\Controllers\Staff\PaymentController::class, 'billsForSessions'])->name('billsForSessions')->middleware(['auth:staff']);

  Route::get('/transactions', [App\Http\Controllers\Staff\PaymentController::class, 'transactions'])->name('transactions')->middleware(['auth:staff']);
  Route::get('/transactionReport', [App\Http\Controllers\Staff\PaymentController::class, 'transactionReport'])->name('transactionReport')->middleware(['auth:staff']);
  Route::post('/generateReport', [App\Http\Controllers\Staff\PaymentController::class, 'generateReport'])->name('generateReport')->middleware(['auth:staff']);

  Route::get('/payment/{slug}', [App\Http\Controllers\Staff\PaymentController::class, 'payment'])->name('payment')->middleware(['auth:staff']);
  Route::post('/getPayment', [App\Http\Controllers\Staff\PaymentController::class, 'getPayment'])->name('getPayment')->middleware(['auth:staff']);
  Route::post('/addStructure', [App\Http\Controllers\Staff\PaymentController::class, 'addStructure'])->name('addStructure')->middleware(['auth:staff']);
  Route::post('/updateStructure', [App\Http\Controllers\Staff\PaymentController::class, 'updateStructure'])->name('updateStructure')->middleware(['auth:staff']);
  Route::post('/deleteStructure', [App\Http\Controllers\Staff\PaymentController::class, 'deleteStructure'])->name('deleteStructure')->middleware(['auth:staff']);
  Route::post('/uploadBulkPayment', [App\Http\Controllers\Staff\PaymentController::class, 'uploadBulkPayment'])->name('uploadBulkPayment')->middleware(['auth:staff']);
  Route::post('/generateInvoice', [App\Http\Controllers\Staff\PaymentController::class, 'generateInvoice'])->name('generateInvoice')->middleware(['auth:staff']);

  Route::post('/chargeStudents', [App\Http\Controllers\Staff\PaymentController::class, 'chargeStudents'])->name('chargeStudents')->middleware(['auth:staff']);

  Route::post('/chargeStudent', [App\Http\Controllers\Staff\PaymentController::class, 'chargeStudent'])->name('chargeStudent')->middleware(['auth:staff']);
  Route::get('/chargeStudent', [App\Http\Controllers\Staff\StaffController::class, 'chargeStudent'])->name('chargeStudent')->middleware(['auth:staff']);
  Route::post('/getStudent', [App\Http\Controllers\Staff\StaffController::class, 'getStudent'])->name('getStudent')->middleware(['auth:staff']);
  Route::post('/getStudentPayment', [App\Http\Controllers\Staff\PaymentController::class, 'getStudentPayment'])->name('getStudentPayment')->middleware(['auth:staff']);
  Route::post('/editTransaction', [App\Http\Controllers\Staff\PaymentController::class, 'editTransaction'])->name('editTransaction')->middleware(['auth:staff']);
  Route::post('/deleteTransaction', [App\Http\Controllers\Staff\PaymentController::class, 'deleteTransaction'])->name('deleteTransaction')->middleware(['auth:staff']);
  Route::post('/walletTopUp', [App\Http\Controllers\Staff\PaymentController::class, 'walletTopUp'])->name('walletTopUp')->middleware(['auth:staff']);

  Route::post('/uploadSignature', [App\Http\Controllers\Staff\StaffController::class, 'uploadSignature'])->name('uploadSignature')->middleware(['auth:staff']);

  Route::get('/applicants/{programmeCategory}', [App\Http\Controllers\Staff\AdmissionController::class, 'applicants'])->name('applicants')->middleware(['auth:staff']);
  Route::get('/applicant/{slug}', [App\Http\Controllers\Staff\AdmissionController::class, 'applicant'])->name('applicant')->middleware(['auth:staff']);
  Route::post('/applicantWithSession', [App\Http\Controllers\Staff\AdmissionController::class, 'applicantWithSession'])->name('applicantWithSession')->middleware(['auth:staff']);
  Route::get('/matriculants/{programmeCategory}', [App\Http\Controllers\Staff\AdmissionController::class, 'matriculants'])->name('matriculants')->middleware(['auth:staff']);
  Route::post('/manageAdmission', [App\Http\Controllers\Staff\AdmissionController::class, 'manageAdmission'])->name('manageAdmission')->middleware(['auth:staff']);

  Route::get('/students/{programmeCategory}', [App\Http\Controllers\Staff\AdmissionController::class, 'students'])->name('students')->middleware(['auth:staff']);
  Route::get('/student/{slug}', [App\Http\Controllers\Staff\AdmissionController::class, 'student'])->name('student')->middleware(['auth:staff']);
  Route::get('/allStudents', [App\Http\Controllers\Staff\AdmissionController::class, 'allStudents'])->name('allStudents')->middleware(['auth:staff']);
  Route::get('/graduatingStudents/{programmeCategory}', [App\Http\Controllers\Staff\StudentController::class, 'graduatingStudents'])->name('graduatingStudents')->middleware(['auth:staff']);

  Route::get('/departmentForCourses', [App\Http\Controllers\Staff\AcademicController::class, 'departmentForCourses'])->name('departmentForCourses')->middleware(['auth:staff']);
  Route::get('/departmentCourse/{slug}', [App\Http\Controllers\Staff\AcademicController::class, 'departmentForCourse'])->name('departmentForCourse')->middleware(['auth:staff']);
  Route::post('/addCourse', [App\Http\Controllers\Staff\AcademicController::class, 'addCourse'])->name('addCourse')->middleware(['auth:staff']);
  Route::post('/updateCourse', [App\Http\Controllers\Staff\AcademicController::class, 'updateCourse'])->name('updateCourse')->middleware(['auth:staff']);
  
  Route::post('/addCourseForStudent', [App\Http\Controllers\Staff\ProgrammeController::class, 'addCourseForStudent'])->name('addCourseForStudent')->middleware(['auth:staff']);
  Route::post('/deleteCourseForStudent', [App\Http\Controllers\Staff\ProgrammeController::class, 'deleteCourseForStudent'])->name('deleteCourseForStudent')->middleware(['auth:staff']);
  Route::post('/updateCourseForStudent', [App\Http\Controllers\Staff\ProgrammeController::class, 'updateCourseForStudent'])->name('updateCourseForStudent')->middleware(['auth:staff']);

  Route::get('/studentExits', [App\Http\Controllers\Staff\StudentCareController::class, 'studentExits'])->name('studentExits')->middleware(['auth:staff']);
  Route::post('getExitApplication', [App\Http\Controllers\Staff\StudentCareController::class, 'getExitApplication'])->name('getExitApplication')->middleware(['auth:staff']);
  Route::post('manageExitApplication', [App\Http\Controllers\Staff\StudentCareController::class, 'manageExitApplication'])->name('manageExitApplication')->middleware(['auth:staff']);
  Route::post('bulkManageExitApplications', [App\Http\Controllers\Staff\StudentCareController::class, 'bulkManageExitApplications'])->name('bulkManageExitApplications')->middleware(['auth:staff']);



  Route::get('/committees', [App\Http\Controllers\Staff\CommitteeController::class, 'committees'])->name('committees')->middleware(['auth:staff']);
  Route::post('/addCommittee', [App\Http\Controllers\Staff\CommitteeController::class, 'addCommittee'])->name('addCommittee')->middleware(['auth:staff']);
  Route::post('/updateCommittee', [App\Http\Controllers\Staff\CommitteeController::class, 'updateCommittee'])->name('updateCommittee')->middleware(['auth:staff']);
  Route::post('/deleteCommittee', [App\Http\Controllers\Staff\CommitteeController::class, 'deleteCommittee'])->name('deleteCommittee')->middleware(['auth:staff']);
  
  Route::get('/committee/{slug}', [App\Http\Controllers\Staff\CommitteeController::class, 'committee'])->name('committee')->middleware(['auth:staff']);
  Route::post('/assignCommitteePosition', [App\Http\Controllers\Staff\CommitteeController::class, 'assignCommitteePosition'])->name('assignCommitteePosition')->middleware(['auth:staff']);
  Route::post('/addMember', [App\Http\Controllers\Staff\CommitteeController::class, 'addMember'])->name('addMember')->middleware(['auth:staff']);

  Route::post('/createMeeting', [App\Http\Controllers\Staff\CommitteeController::class, 'createMeeting'])->name('createMeeting')->middleware(['auth:staff']);
  Route::post('/updateMeeting', [App\Http\Controllers\Staff\CommitteeController::class, 'updateMeeting'])->name('updateMeeting')->middleware(['auth:staff']);
  Route::post('/deleteMeeting', [App\Http\Controllers\Staff\CommitteeController::class, 'deleteMeeting'])->name('deleteMeeting')->middleware(['auth:staff']);

  Route::get('/verifyStudentExits', [App\Http\Controllers\Staff\StudentCareController::class, 'verifyStudentExits'])->name('verifyStudentExits')->middleware(['auth:staff']);
  Route::post('/verifyStudentExit', [App\Http\Controllers\Staff\StudentCareController::class, 'verifyStudentExit'])->name('verifyStudentExit')->middleware(['auth:staff']);
  
  Route::get('/attendance', [App\Http\Controllers\Staff\AttendanceController::class, 'attendance'])->name('attendance')->middleware(['auth:staff']);
  Route::get('/monthlyAttendance/{slug}', [App\Http\Controllers\Staff\AttendanceController::class, 'monthlyAttendance'])->middleware(['auth:staff']);
  Route::get('/updateAttendance/{staffId}', [App\Http\Controllers\Staff\AttendanceController::class, 'updateAttendance'])->middleware(['auth:staff']);
  Route::post('/uploadAttendance', [App\Http\Controllers\Staff\AttendanceController::class, 'uploadAttendance'])->name('uploadAttendance')->middleware(['auth:staff']);

  Route::get('/leaveApplication', [App\Http\Controllers\Staff\LeaveController::class, 'leaveApplication'])->name('leaveApplication')->middleware(['auth:staff']);
  Route::post('/applyForLeave', [App\Http\Controllers\Staff\LeaveController::class, 'applyForLeave'])->name('applyForLeave')->middleware(['auth:staff']);
  Route::get('/leaves', [App\Http\Controllers\Staff\LeaveController::class, 'leaves'])->name('leaves')->middleware(['auth:staff']);
  Route::get('/leave/{Slug}', [App\Http\Controllers\Staff\LeaveController::class, 'leave'])->name('leave')->middleware(['auth:staff']);
  Route::get('/manageLeaves', [App\Http\Controllers\Staff\LeaveController::class, 'manageLeaves'])->name('manageLeaves')->middleware(['auth:staff']);
  Route::post('/manageLeave', [App\Http\Controllers\Staff\LeaveController::class, 'manageLeave'])->name('manageLeave')->middleware(['auth:staff']);

  Route::post('/updateStaffUnit', [App\Http\Controllers\Staff\StaffController::class, 'updateStaffUnit'])->name('updateStaffUnit')->middleware(['auth:staff']);

  Route::post('/requestCourseApproval', [App\Http\Controllers\Staff\AcademicController::class, 'requestCourseApproval'])->name('requestCourseApproval')->middleware(['auth:staff']);
  Route::post('/courseApproval', [App\Http\Controllers\Staff\AcademicController::class, 'courseApproval'])->name('courseApproval')->middleware(['auth:staff']);

  Route::post('/manageClearanceApplication', [App\Http\Controllers\Staff\StudentController::class, 'manageClearanceApplication'])->name('manageClearanceApplication')->middleware(['auth:staff']);

  Route::get('/hostelType', [App\Http\Controllers\Staff\HostelController::class, 'hostelType'])->name('hostelType')->middleware(['auth:staff']);
  Route::post('/addRoomType', [App\Http\Controllers\Staff\HostelController::class, 'addRoomType'])->name('addRoomType')->middleware(['auth:staff']);
  Route::post('/updateRoomType', [App\Http\Controllers\Staff\HostelController::class, 'updateRoomType'])->name('updateRoomType')->middleware(['auth:staff']);
  Route::post('/deleteRoomType', [App\Http\Controllers\Staff\HostelController::class, 'deleteRoomType'])->name('deleteRoomType')->middleware(['auth:staff']);

  Route::get('/hostel', [App\Http\Controllers\Staff\HostelController::class, 'hostel'])->name('hostel')->middleware(['auth:staff']);
  Route::post('/addHostel', [App\Http\Controllers\Staff\HostelController::class, 'addHostel'])->name('addHostel')->middleware(['auth:staff']);
  Route::post('/updateHostel', [App\Http\Controllers\Staff\HostelController::class, 'updateHostel'])->name('updateHostel')->middleware(['auth:staff']);
  Route::post('/deleteHostel', [App\Http\Controllers\Staff\HostelController::class, 'deleteHostel'])->name('deleteHostel')->middleware(['auth:staff']);
  Route::get('/viewHostel/{slug}', [App\Http\Controllers\Staff\HostelController::class, 'viewHostel'])->name('viewHostel')->middleware(['auth:staff']);
  Route::post('/addRoom', [App\Http\Controllers\Staff\HostelController::class, 'addRoom'])->name('addRoom')->middleware(['auth:staff']);
  Route::post('/deleteRoom', [App\Http\Controllers\Staff\HostelController::class, 'deleteRoom'])->name('deleteRoom')->middleware(['auth:staff']);
  Route::post('/reserveRoom', [App\Http\Controllers\Staff\HostelController::class, 'reserveRoom'])->name('reserveRoom')->middleware(['auth:staff']);


  Route::get('/allocations', [App\Http\Controllers\Staff\HostelController::class, 'allocations'])->name('allocations')->middleware(['auth:staff']);
  Route::post('/deleteAllocation', [App\Http\Controllers\Staff\HostelController::class, 'deleteAllocation'])->name('deleteAllocation')->middleware(['auth:staff']);
  Route::post('/allocateRoom', [App\Http\Controllers\Staff\HostelController::class, 'allocateRoom'])->name('allocateRoom')->middleware(['auth:staff']);

  Route::get('/alumni', [App\Http\Controllers\Staff\StudentController::class, 'alumni'])->name('alumni')->middleware(['auth:staff']);

  Route::get('/studentFinalClearance', [App\Http\Controllers\Staff\StudentController::class, 'studentFinalClearance'])->name('studentFinalClearance')->middleware(['auth:staff']);
  Route::post('/manageFinalYearStudentClearance', [App\Http\Controllers\Staff\StudentController::class, 'manageFinalYearStudentClearance'])->name('manageFinalYearStudentClearance')->middleware(['auth:staff']);

  Route::post('/createLecture', [App\Http\Controllers\Staff\StaffController::class, 'createLecture'])->name('createLecture')->middleware(['auth:staff']);
  Route::post('/updateLecture', [App\Http\Controllers\Staff\StaffController::class, 'updateLecture'])->name('updateLecture')->middleware(['auth:staff']);
  Route::post('/deleteLecture', [App\Http\Controllers\Staff\StaffController::class, 'deleteLecture'])->name('deleteLecture')->middleware(['auth:staff']);
  Route::post('/staffUploadAttendance', [App\Http\Controllers\Staff\StaffController::class, 'staffUploadAttendance'])->name('staffUploadAttendance')->middleware(['auth:staff']);
  Route::post('/markStudentAttendance', [App\Http\Controllers\Staff\StaffController::class, 'markStudentAttendance'])->name('markStudentAttendance')->middleware(['auth:staff']);
  Route::post('/deleteStudentAttendance', [App\Http\Controllers\Staff\StaffController::class, 'deleteStudentAttendance'])->name('deleteStudentAttendance')->middleware(['auth:staff']);
  
  Route::post('/postJobVacancy', [App\Http\Controllers\Staff\CareerController::class, 'postJobVacancy'])->name('postJobVacancy')->middleware(['auth:staff']);
  Route::get('/viewJobVacancy/{slug}', [App\Http\Controllers\Staff\CareerController::class, 'viewJobVacancy'])->name('viewJobVacancy')->middleware(['auth:staff']);
  Route::post('/updateJobVacancy', [App\Http\Controllers\Staff\CareerController::class, 'updateJobVacancy'])->name('updateJobVacancy')->middleware(['auth:staff']);
  Route::post('/deleteJobVacancy', [App\Http\Controllers\Staff\CareerController::class, 'deleteJobVacancy'])->name('deleteJobVacancy')->middleware(['auth:staff']);
  Route::post('/updateApplicantStatus', [App\Http\Controllers\Staff\CareerController::class, 'updateApplicantStatus'])->name('updateApplicantStatus')->middleware(['auth:staff']);
  Route::post('/uploadApplicantAppointmentLetter', [App\Http\Controllers\Staff\CareerController::class, 'uploadApplicantAppointmentLetter'])->name('uploadApplicantAppointmentLetter')->middleware(['auth:staff']);

  Route::get('/prospectiveStaff', [App\Http\Controllers\Staff\CareerController::class, 'prospectiveStaff'])->name('prospectiveStaff')->middleware(['auth:staff']);
  Route::get('/workStudyApplicants', [App\Http\Controllers\Staff\CareerController::class, 'workStudyApplicants'])->name('workStudyApplicants')->middleware(['auth:staff']);
  Route::get('/jobVacancy', [App\Http\Controllers\Staff\CareerController::class, 'jobVacancy'])->name('jobVacancy')->middleware(['auth:staff']);
  Route::get('/siwesApplicants', [App\Http\Controllers\Staff\CareerController::class, 'siwesApplicants'])->name('siwesApplicant')->middleware(['auth:staff']);


  Route::get('/viewSuspension/{slug}', [App\Http\Controllers\Staff\StudentDisciplinaryController::class, 'viewSuspension'])->name('admin.viewSuspension')->middleware(['auth:admin']);
  Route::post('/manageSuspension', [App\Http\Controllers\Staff\StudentDisciplinaryController::class, 'manageSuspension'])->name('admin.manageSuspension')->middleware(['auth:admin']);  
  Route::post('/expel', [App\Http\Controllers\Staff\StudentDisciplinaryController::class, 'expel'])->name('expel')->middleware(['auth:admin']);
  Route::post('/suspend', [App\Http\Controllers\Staff\StudentDisciplinaryController::class, 'suspend'])->name('suspend')->middleware(['auth:admin']);
  Route::post('/recall', [App\Http\Controllers\Staff\StudentDisciplinaryController::class, 'recall'])->name('recall')->middleware(['auth:admin']);

  Route::get('/expelledStudents', [App\Http\Controllers\Staff\AcademicController::class, 'expelledStudents'])->name('expelledStudents')->middleware(['auth:admin']);
  Route::get('/suspendedStudents', [App\Http\Controllers\Staff\AcademicController::class, 'suspendedStudents'])->name('suspendedStudents')->middleware(['auth:admin']);

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
  Route::get('/applicants', [App\Http\Controllers\Partner\PartnerController::class, 'applicants'])->name('applicants')->middleware(['auth:partner']);
  Route::get('/transactions', [App\Http\Controllers\Partner\PartnerController::class, 'transactions'])->name('transactions')->middleware(['auth:partner']);
  Route::get('/profile', [App\Http\Controllers\Partner\PartnerController::class, 'profile'])->name('profile')->middleware(['auth:partner']);

  Route::get('/applicant/{slug}', [App\Http\Controllers\Partner\PartnerController::class, 'applicant'])->name('applicant')->middleware(['auth:partner']);
  Route::post('/applicantWithSession', [App\Http\Controllers\Partner\PartnerController::class, 'applicantWithSession'])->name('applicantWithSession')->middleware(['auth:partner']);

  Route::get('/students', [App\Http\Controllers\Partner\PartnerController::class, 'students'])->name('students')->middleware(['auth:partner']);
  Route::get('/student/{slug}', [App\Http\Controllers\Partner\PartnerController::class, 'student'])->name('student')->middleware(['auth:partner']);

  Route::post('/updatePassword', [App\Http\Controllers\Partner\PartnerController::class, 'updatePassword'])->name('updatePassword')->middleware(['auth:partner']);


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

  Route::get('/studentProfile/{slug}', [App\Http\Controllers\Guardian\GuardianController::class, 'studentProfile'])->name('studentProfile')->middleware(['auth:guardian']);
  Route::post('/makePayment', [App\Http\Controllers\Guardian\GuardianController::class, 'makePayment'])->name('makePayment')->middleware(['auth:guardian']);
  Route::post('/generateResult', [App\Http\Controllers\Guardian\GuardianController::class, 'generateResult'])->name('generateResult')->middleware(['auth:guardian']);
  Route::post('/generateInvoice', [App\Http\Controllers\Guardian\GuardianController::class, 'generateInvoice'])->name('generateInvoice')->middleware(['auth:guardian']);

  Route::post('/updatePassword', [App\Http\Controllers\Guardian\GuardianController::class, 'updatePassword'])->name('updatePassword')->middleware(['auth:guardian']);


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
  Route::post('/updateUtme', [App\Http\Controllers\User\ApplicationController::class, 'updateUtme'])->middleware(['auth:user']);
  Route::post('/updateOlevel', [App\Http\Controllers\User\ApplicationController::class, 'updateOlevel'])->middleware(['auth:user']);

  Route::post('/deleteUtme', [App\Http\Controllers\User\ApplicationController::class, 'deleteUtme'])->middleware(['auth:user']);
  Route::post('/deleteOlevel', [App\Http\Controllers\User\ApplicationController::class, 'deleteOlevel'])->middleware(['auth:user']);
  Route::post('/submitApplication', [App\Http\Controllers\User\ApplicationController::class, 'submitApplication'])->middleware(['auth:user']);
  Route::post('/nokBioData', [App\Http\Controllers\User\ApplicationController::class, 'nokBioData'])->middleware(['auth:user']);

  Route::post('/saveDe', [App\Http\Controllers\User\ApplicationController::class, 'saveDe'])->middleware(['auth:user']);
  Route::post('/saveProgramme', [App\Http\Controllers\User\ApplicationController::class, 'saveProgramme'])->middleware(['auth:user']);
  Route::post('/uploadOlevel', [App\Http\Controllers\User\ApplicationController::class, 'uploadOlevel'])->middleware(['auth:user']);
  Route::post('/uploadUtme', [App\Http\Controllers\User\ApplicationController::class, 'uploadUtme'])->middleware(['auth:user']);
  Route::post('/deleteFile', [App\Http\Controllers\User\ApplicationController::class, 'deleteFile'])->middleware(['auth:user']);
  

  Route::get('/programmeById/{id}', [App\Http\Controllers\User\ApplicationController::class, 'programmeById'])->name('programmeById');
  Route::get('/facultyById/{id}', [App\Http\Controllers\User\ApplicationController::class, 'facultyById'])->name('facultyById');
  Route::get('/departmentById/{id}', [App\Http\Controllers\User\ApplicationController::class, 'departmentById'])->name('departmentById');

});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'career'], function () {
  Route::get('/', [App\Http\Controllers\Career\Auth\LoginController::class, 'showLoginForm'])->name('bursary.login');
  Route::get('/login', [App\Http\Controllers\Career\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Career\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Career\Auth\LoginController::class, 'logout'])->name('logout');

  Route::get('/register', [App\Http\Controllers\Career\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  Route::post('/register', [App\Http\Controllers\Career\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Career\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Career\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Career\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Career\Auth\ResetPasswordController::class, 'showResetForm']);

  Route::get('/home', [App\Http\Controllers\Career\CareerController::class, 'index'])->middleware(['auth:career']);
  Route::get('/profile', [App\Http\Controllers\Career\CareerController::class, 'profile'])->name('profile')->middleware(['auth:career']);
  Route::get('/applications', [App\Http\Controllers\Career\CareerController::class, 'applications'])->name('applications')->middleware(['auth:career']);

  Route::post('/manageProfile', [App\Http\Controllers\Career\CareerController::class, 'manageProfile'])->name('manageProfile')->middleware(['auth:career']);
  Route::post('/apply', [App\Http\Controllers\Career\CareerController::class, 'apply'])->name('apply')->middleware(['auth:career']);
  Route::post('/deleteApplication', [App\Http\Controllers\Career\CareerController::class, 'deleteApplication'])->name('deleteApplication')->middleware(['auth:career']);
  Route::post('/manageApplication', [App\Http\Controllers\Career\CareerController::class, 'manageApplication'])->name('manageApplication')->middleware(['auth:career']);

});

Route::group(['prefix' => 'center'], function () {
  Route::get('/', [App\Http\Controllers\Center\Auth\LoginController::class, 'showLoginForm'])->name('guardian.login');
  Route::get('/login', [App\Http\Controllers\Center\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Center\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Center\Auth\LoginController::class, 'logout'])->name('logout');

  Route::get('/register', [App\Http\Controllers\Center\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  Route::post('/register', [App\Http\Controllers\Center\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Center\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Center\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Center\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Center\Auth\ResetPasswordController::class, 'showResetForm']);

});
