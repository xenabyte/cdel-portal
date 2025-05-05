<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [App\Http\Controllers\ApiController::class, 'login'])->name('apiLogin');
});

Route::group(['prefix' => 'user'], function () {
    Route::post('/validateUser', [App\Http\Controllers\ApiController::class, 'validateUser'])->name('apiValidateUser');
    Route::post('/getStudent', [App\Http\Controllers\ApiController::class, 'getStudent'])->name('getStudent');
});

Route::group(['prefix' => 'lecture'], function () {
    Route::post('/getCourseLecture', [App\Http\Controllers\ApiController::class, 'getCourseLecture'])->name('getCourseLecture');
});

Route::get('/changeCourseManagementPasscode', [App\Http\Controllers\CronController::class, 'changeCourseManagementPasscode'])->name('changeCourseManagementPasscode');
Route::post('/getRequiredPassMark', [App\Http\Controllers\ApiController::class, 'getRequiredPassMark'])->name('getRequiredPassMark');

