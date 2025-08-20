<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\StagesSubjectsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Services\Stages_subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('Send_verify_code',[OtpController::class, 'sendOtp']);
Route::post('Verify_code',[OtpController::class, 'verifyOtp']);
Route::post('Refresh_token',[OtpController::class, 'Refresh_token']);
Route::post('adminRegester',[AdminController::class,'Regester']);
Route::post('login',[AdminController::class,'login']);
Route::post('logout',[AdminController::class,'Logout'])->middleware('auth:sanctum');

Route::middleware('check_auth')->group(function(){
    Route::get('get_school_stage', [StagesSubjectsController::class, 'get_school_stage']);
    Route::post('choose_school_study_stage', [StagesSubjectsController::class, 'choose_school_study_stage']);
    Route::get('get_school_stage_subjects/{school_stage}', [StagesSubjectsController::class, 'get_school_subjects_stage']);
    Route::post('choose_school_subjects', [StagesSubjectsController::class, 'choose_school_subjects']);
    Route::get('get_university_stage', [StagesSubjectsController::class, 'get_university_stage']);
    Route::post('Choose_university_study_stage', [StagesSubjectsController::class, 'Choose_university_study_stage']);
    Route::get('get_university_stage_subjects/{university_stage}', [StagesSubjectsController::class, 'get_university_stage_subjects']);
    Route::post('choose_university_subjects', [StagesSubjectsController::class, 'choose_university_subjects']);
});

Route::middleware('check_students')->group(function(){

    Route::post('Profile_complete',[StudentController::class, 'Profile_complete']);
    Route::get('Student_profile',[StudentController::class, 'Student_profile']);
    Route::post('update_profile',[StudentController::class,'update_profile']);
    Route::get('get_teacher',[StudentController::class,'get_teacher']);
    Route::post('teacher_filter',[StudentController::class,'filter_result']);
    Route::post('teacher_Rating/{teacher}',[StudentController::class,'Rating_teacher']);
    Route::post('teacher_following/{teacher}',[StudentController::class,'Following_teacher']);
    Route::post('getWeeklyAvailableSlots_reservations',[StudentController::class,'getWeeklyAvailableSlots_reservations']);

});
Route::middleware('check_admin')->group(function(){
Route::post('Add_school_stage',[AdminController::class, 'Add_school_stage']);
Route::post('Add_school_subject/{school_stage}',[AdminController::class, 'Add_school_subject']);
Route::post('Add_university_stage',[AdminController::class, 'Add_university_stage']);
Route::post('Add_university_subject/{university_stage}',[AdminController::class, 'Add_university_subject']);
Route::get('Teacher_accounts_for_approve',[AdminController::class, 'get_teacher_account_for_approve']);
Route::post('proccess_teacher_account/{teacher}',[AdminController::class, 'proccess_teacher_account']);
Route::get('get_profile',[AdminController::class,'Admin_profile']);
Route::post('update_profile',[AdminController::class,'update_profile']);
});

Route::middleware('check_teacher')->group(function(){
    Route::post('SendAccountForAprrove',[TeacherController::class, 'Register']);
    Route::get('teacher_profile',[TeacherController::class,'teacher_profile']);
    Route::post('update_teacher_profile',[TeacherController::class,'update_profile']);
    Route::post('add_worktime',[TeacherController::class, 'teacher_available_worktime'])->middleware('check_teacher_activate');
});

