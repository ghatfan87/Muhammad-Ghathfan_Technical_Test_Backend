<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
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
//leads
Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
Route::post('/create', [LeadController::class, 'store'])->name('leads.store');


//users
Route::get('/user', [UserController::class, 'index'])->name('user.index');
Route::post('/createUser', [UserController::class, 'store']);

//surveys
Route::post('surveys/request', [SurveyController::class, 'requestSurvey']); // Mengajukan survey
Route::patch('surveys/{surveyId}/status', [SurveyController::class, 'updateSurveyStatus']); // Approve/Reject survey
Route::post('surveys/{surveyId}/complete', [SurveyController::class, 'completeSurvey']); // Melengkapi survey


