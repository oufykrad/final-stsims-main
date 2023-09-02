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

Route::get('/', function () {return inertia('Auth/Login'); });
Route::middleware(['auth'])->group(function () {
    Route::resource('/home', App\Http\Controllers\HomeController::class);
    Route::resource('/staffs', App\Http\Controllers\StaffController::class);
    Route::resource('/courses', App\Http\Controllers\CourseController::class);
    Route::resource('/schools', App\Http\Controllers\SchoolController::class);
    Route::resource('/scholars', App\Http\Controllers\ScholarController::class);
    Route::resource('/qualifiers', App\Http\Controllers\QualifierController::class);
    Route::resource('/schools-temporary', App\Http\Controllers\SchoolTemporaryController::class);
    
    Route::prefix('excel')->group(function(){
        Route::post('/course/import', [App\Http\Controllers\CourseController::class, 'index']);
        Route::post('/course/store', [App\Http\Controllers\CourseController::class, 'store']);
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/lists.php';