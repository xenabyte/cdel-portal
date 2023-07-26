<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'admin'], function () {
  Route::get('/login', 'Admin\Auth\LoginController@showLoginForm')->name('login');
  Route::post('/login', 'Admin\Auth\LoginController@login');
  Route::post('/logout', 'Admin\Auth\LoginController@logout')->name('logout');

  Route::get('/register', 'Admin\Auth\RegisterController@showRegistrationForm')->name('register');
  Route::post('/register', 'Admin\Auth\RegisterController@register');

  Route::post('/password/email', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
  Route::post('/password/reset', 'Admin\Auth\ResetPasswordController@reset')->name('password.email');
  Route::get('/password/reset', 'Admin\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
  Route::get('/password/reset/{token}', 'Admin\Auth\ResetPasswordController@showResetForm');
});

Route::group(['prefix' => 'student'], function () {
  Route::get('/login', 'Student\Auth\LoginController@showLoginForm')->name('login');
  Route::post('/login', 'Student\Auth\LoginController@login');
  Route::post('/logout', 'Student\Auth\LoginController@logout')->name('logout');

  Route::get('/register', 'Student\Auth\RegisterController@showRegistrationForm')->name('register');
  Route::post('/register', 'Student\Auth\RegisterController@register');

  Route::post('/password/email', 'Student\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
  Route::post('/password/reset', 'Student\Auth\ResetPasswordController@reset')->name('password.email');
  Route::get('/password/reset', 'Student\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
  Route::get('/password/reset/{token}', 'Student\Auth\ResetPasswordController@showResetForm');
});

Route::group(['prefix' => 'staff'], function () {
  Route::get('/login', 'Staff\Auth\LoginController@showLoginForm')->name('login');
  Route::post('/login', 'Staff\Auth\LoginController@login');
  Route::post('/logout', 'Staff\Auth\LoginController@logout')->name('logout');

  Route::get('/register', 'Staff\Auth\RegisterController@showRegistrationForm')->name('register');
  Route::post('/register', 'Staff\Auth\RegisterController@register');

  Route::post('/password/email', 'Staff\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
  Route::post('/password/reset', 'Staff\Auth\ResetPasswordController@reset')->name('password.email');
  Route::get('/password/reset', 'Staff\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
  Route::get('/password/reset/{token}', 'Staff\Auth\ResetPasswordController@showResetForm');
});

Route::group(['prefix' => 'bursary'], function () {
  Route::get('/login', 'Bursary\Auth\LoginController@showLoginForm')->name('login');
  Route::post('/login', 'Bursary\Auth\LoginController@login');
  Route::post('/logout', 'Bursary\Auth\LoginController@logout')->name('logout');

  Route::get('/register', 'Bursary\Auth\RegisterController@showRegistrationForm')->name('register');
  Route::post('/register', 'Bursary\Auth\RegisterController@register');

  Route::post('/password/email', 'Bursary\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
  Route::post('/password/reset', 'Bursary\Auth\ResetPasswordController@reset')->name('password.email');
  Route::get('/password/reset', 'Bursary\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
  Route::get('/password/reset/{token}', 'Bursary\Auth\ResetPasswordController@showResetForm');
});

Route::group(['prefix' => 'partner'], function () {
  Route::get('/login', 'Partner\Auth\LoginController@showLoginForm')->name('login');
  Route::post('/login', 'Partner\Auth\LoginController@login');
  Route::post('/logout', 'Partner\Auth\LoginController@logout')->name('logout');

  Route::get('/register', 'Partner\Auth\RegisterController@showRegistrationForm')->name('register');
  Route::post('/register', 'Partner\Auth\RegisterController@register');

  Route::post('/password/email', 'Partner\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
  Route::post('/password/reset', 'Partner\Auth\ResetPasswordController@reset')->name('password.email');
  Route::get('/password/reset', 'Partner\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
  Route::get('/password/reset/{token}', 'Partner\Auth\ResetPasswordController@showResetForm');
});

Route::group(['prefix' => 'guardian'], function () {
  Route::get('/login', 'Guardian\Auth\LoginController@showLoginForm')->name('login');
  Route::post('/login', 'Guardian\Auth\LoginController@login');
  Route::post('/logout', 'Guardian\Auth\LoginController@logout')->name('logout');

  Route::get('/register', 'Guardian\Auth\RegisterController@showRegistrationForm')->name('register');
  Route::post('/register', 'Guardian\Auth\RegisterController@register');

  Route::post('/password/email', 'Guardian\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
  Route::post('/password/reset', 'Guardian\Auth\ResetPasswordController@reset')->name('password.email');
  Route::get('/password/reset', 'Guardian\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
  Route::get('/password/reset/{token}', 'Guardian\Auth\ResetPasswordController@showResetForm');
});
