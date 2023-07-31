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
    return view('welcome');
});

Auth::routes(['register' => false, 'login' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('verifyPayment', [App\Http\Controllers\PaymentController::class, 'verifyPayment'])->name('verifyPayment');
Route::post('/paystackWebhook', [App\Http\Controllers\PaymentController::class, 'paystackWebhook']);


Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'admin'], function () {
  Route::get('/', [App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
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

  Route::get('/home', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('home');
  Route::get('/academicLevel', [App\Http\Controllers\Admin\AcademicController::class, 'academicLevel'])->name('academicLevel');
  Route::post('/addLevel', [App\Http\Controllers\Admin\AcademicController::class, 'addLevel'])->name('addLevel');
  Route::post('/updateLevel', [App\Http\Controllers\Admin\AcademicController::class, 'updateLevel'])->name('updateLevel');
  Route::post('/deleteLevel', [App\Http\Controllers\Admin\AcademicController::class, 'deleteLevel'])->name('deleteLevel');
  
  Route::get('/programmeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'programmeCategory'])->name('programmeCategory');
  Route::post('/addProgrammeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'addProgrammeCategory'])->name('addProgrammeCategory');
  Route::post('/updateProgrammeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'updateProgrammeCategory'])->name('updateProgrammeCategory');
  Route::post('/deleteProgrammeCategory', [App\Http\Controllers\Admin\ProgrammeController::class, 'deleteProgrammeCategory'])->name('deleteProgrammeCategory');
  Route::get('/populateFaculty', [App\Http\Controllers\Admin\CronController::class, 'populateFaculty'])->name('populateFaculty');
  Route::get('/populateCourse', [App\Http\Controllers\Admin\CronController::class, 'populateCourse'])->name('populateCourse');


  Route::get('/sessionSetup', [App\Http\Controllers\Admin\AcademicController::class, 'sessionSetup'])->name('sessionSetup');
  Route::post('/setSession', [App\Http\Controllers\Admin\AcademicController::class, 'setSession'])->name('setSession');
  Route::post('/addSession', [App\Http\Controllers\Admin\AcademicController::class, 'addSession'])->name('addSession');
  Route::post('/updateSession', [App\Http\Controllers\Admin\AcademicController::class, 'updateSession'])->name('updateSession');
  Route::post('/deleteSession', [App\Http\Controllers\Admin\AcademicController::class, 'deleteSession'])->name('deleteSession');
  
  Route::get('payments', [App\Http\Controllers\Admin\PaymentController::class, 'payments'])->name('payments');
  Route::post('/addPayment', [App\Http\Controllers\Admin\PaymentController::class, 'addPayment'])->name('addPayment');
  Route::post('/updatePayment', [App\Http\Controllers\Admin\PaymentController::class, 'updatePayment'])->name('updatePayment');
  Route::post('/deletePayment', [App\Http\Controllers\Admin\PaymentController::class, 'deletePayment'])->name('deletePayment');

  Route::get('payment/{slug}', [App\Http\Controllers\Admin\PaymentController::class, 'payment'])->name('payment');
  Route::get('paymentById/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'paymentById'])->name('paymentById');
  Route::post('/addStructure', [App\Http\Controllers\Admin\PaymentController::class, 'addStructure'])->name('addStructure');
  Route::post('/updateStructure', [App\Http\Controllers\Admin\PaymentController::class, 'updateStructure'])->name('updateStructure');
  Route::post('/deleteStructure', [App\Http\Controllers\Admin\PaymentController::class, 'deleteStructure'])->name('deleteStructure');

  Route::post('chargeStudent', [App\Http\Controllers\Admin\PaymentController::class, 'chargeStudent'])->name('chargeStudent');
  Route::get('chargeStudent', [App\Http\Controllers\Admin\AdminController::class, 'chargeStudent'])->name('chargeStudent');


  Route::get('applicants', [App\Http\Controllers\Admission\AdmissionController::class, 'applicants'])->name('applicants');
  Route::get('applicant/{slug}', [App\Http\Controllers\Admission\AdmissionController::class, 'applicant'])->name('applicant');
  Route::post('applicantWithSession', [App\Http\Controllers\Admission\AdmissionController::class, 'applicantWithSession'])->name('applicantWithSession');


});

Route::group(['prefix' => 'student'], function () {
  Route::get('/', [App\Http\Controllers\Student\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::get('/login', [App\Http\Controllers\Student\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Student\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Student\Auth\LoginController::class, 'logout'])->name('logout');

  // Route::get('/register', [App\Http\Controllers\Student\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  // Route::post('/register', [App\Http\Controllers\Student\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Student\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Student\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Student\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Student\Auth\ResetPasswordController::class, 'showResetForm']);
});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'staff'], function () {
  Route::get('/', [App\Http\Controllers\Staff\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::get('/login', [App\Http\Controllers\Staff\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Staff\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Staff\Auth\LoginController::class, 'logout'])->name('logout');

  // Route::get('/register', [App\Http\Controllers\Staff\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  // Route::post('/register', [App\Http\Controllers\Staff\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Staff\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Staff\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Staff\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Staff\Auth\ResetPasswordController::class, 'showResetForm']);
});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'bursary'], function () {
  Route::get('/', [App\Http\Controllers\Bursary\Auth\LoginController::class, 'showLoginForm'])->name('login');
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
  Route::get('/', [App\Http\Controllers\Partner\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::get('/login', [App\Http\Controllers\Partner\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Partner\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Partner\Auth\LoginController::class, 'logout'])->name('logout');

  Route::get('/register', [App\Http\Controllers\Partner\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  Route::post('/register', [App\Http\Controllers\Partner\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Partner\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Partner\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Partner\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Partner\Auth\ResetPasswordController::class, 'showResetForm']);
});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'guardian'], function () {
  Route::get('/', [App\Http\Controllers\Guardian\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::get('/login', [App\Http\Controllers\Guardian\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\Guardian\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\Guardian\Auth\LoginController::class, 'logout'])->name('logout');

  // Route::get('/register', [App\Http\Controllers\Guardian\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
  // Route::post('/register', [App\Http\Controllers\Guardian\Auth\RegisterController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\Guardian\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\Guardian\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\Guardian\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\Guardian\Auth\ResetPasswordController::class, 'showResetForm']);
});

Route::group(['middleware' => GlobalDataMiddleware::class, 'prefix' => 'applicant'], function () {
  Route::get('/', [App\Http\Controllers\User\ApplicationController::class, 'showRegistrationForm'])->name('showRegistrationForm');
  Route::get('/login', [App\Http\Controllers\User\Auth\LoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [App\Http\Controllers\User\Auth\LoginController::class, 'login']);
  Route::post('/logout', [App\Http\Controllers\User\Auth\LoginController::class, 'logout'])->name('logout');

  Route::get('/register', [App\Http\Controllers\User\ApplicationController::class, 'showRegistrationForm'])->name('showRegistrationForm');
  Route::post('/register', [App\Http\Controllers\User\ApplicationController::class, 'register']);

  Route::post('/password/email', [App\Http\Controllers\User\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
  Route::post('/password/reset', [App\Http\Controllers\User\Auth\ResetPasswordController::class, 'reset'])->name('password.email');
  Route::get('/password/reset', [App\Http\Controllers\User\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
  Route::get('/password/reset/{token}', [App\Http\Controllers\User\Auth\ResetPasswordController::class, 'showResetForm']);

  Route::get('/home', [App\Http\Controllers\User\ApplicationController::class, 'index']);
  Route::get('programmeById/{id}', [App\Http\Controllers\User\ApplicationController::class, 'programmeById'])->name('programmeById');

});
