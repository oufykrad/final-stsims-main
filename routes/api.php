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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('01101011 01110010 01100001 01100100')->group(function(){
        Route::get('/scholars/{code}', [App\Http\Controllers\ScholarController::class, 'api']);
        Route::get('/courses', [App\Http\Controllers\CourseController::class, 'api']);
        Route::get('/schools', [App\Http\Controllers\SchoolController::class, 'api']);
        Route::get('/agencies', [App\Http\Controllers\ListController::class, 'api_agencies']);
        Route::get('/programs', [App\Http\Controllers\ListController::class, 'api_programs']);
        Route::get('/privileges', [App\Http\Controllers\ListController::class, 'api_privileges']);
        Route::get('/statuses', [App\Http\Controllers\ListController::class, 'api_statuses']);
        Route::get('/dropdowns', [App\Http\Controllers\ListController::class, 'api_dropdowns']);
        Route::get('/location/{type}', [App\Http\Controllers\ListController::class, 'api_location']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
