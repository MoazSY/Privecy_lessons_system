<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('Send_verify_code',[StudentController::class, 'sendOtp']);
Route::post('Verify_code',[StudentController::class, 'verifyOtp']);
Route::post('Refresh_token',[StudentController::class, 'Refresh_token']);
Route::post('adminRegester',[AdminController::class,'Regester']);
Route::post('login',[AdminController::class,'login']);
Route::post('logout',[AdminController::class,'Logout'])->middleware('auth:sanctum');

Route::middleware('check_students')->group(function(){
    Route::get('get_school_stage',[StudentController::class, 'get_school_stage']);
    Route::post('choose_school_study_stage',[StudentController::class, 'choose_school_study_stage']);
    Route::post('choose_school_subjects',[StudentController::class, 'choose_school_subjects']);
    Route::post('Profile_complete',[StudentController::class, 'Profile_complete']);
    Route::get('Student_profile',[StudentController::class, 'Student_profile']);
    Route::get('get_university_stage',[StudentController::class, 'get_university_stage']);
    Route::post('Choose_university_study_stage',[StudentController::class, 'Choose_university_study_stage']);
    Route::get('get_university_stage_subjects/{university_stage}',[StudentController::class, 'get_university_stage_subjects']);
    Route::post('choose_university_subjects',[StudentController::class, 'choose_university_subjects']);

});
Route::middleware('check_admin')->group(function(){
Route::post('Add_school_stage',[AdminController::class, 'Add_school_stage']);
Route::post('Add_school_subject/{school_stage}',[AdminController::class, 'Add_school_subject']);
Route::post('Add_university_stage',[AdminController::class, 'Add_university_stage']);
Route::post('Add_university_subject/{university_stage}',[AdminController::class, 'Add_university_subject']);
});

