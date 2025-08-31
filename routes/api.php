<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\JitsiSessionController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\StagesSubjectsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ZoomSessionController;
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
    Route::post('update_profiles',[StudentController::class,'update_profile']);
    Route::get('get_teacher',[StudentController::class,'get_teacher']);
    Route::get('teacherProfile/{teacher}',[StudentController::class,'teacher_profile']);
    Route::post('teacher_filter',[StudentController::class,'filter_result']);
    Route::post('teacher_Rating/{teacher}',[StudentController::class,'Rating_teacher']);
    Route::post('teacher_following/{teacher}',[StudentController::class,'Following_teacher']);
    Route::post('getWeeklyAvailableSlots_reservations',[StudentController::class,'getWeeklyAvailableSlots_reservations']);
    Route::post('student_search_teacher',[AdminController::class,'search_teacher']);
    Route::post('lesson_reservation',[StudentController::class,'lesson_reservation']);
    Route::get('get_all_reservation',[StudentController::class,'get_all_reservation']);
    Route::get('all_student_session',[ZoomSessionController::class,'get_session']);
    Route::post('uplode_recording_session/{session}',[StudentController::class,'add_session_video']);
    Route::post('cancle_reservation/{reservation}',[StudentController::class,'cancle_reservation']);
    Route::post('report_session/{session}',[StudentController::class,'report_session']);
});
Route::middleware('check_admin')->group(function(){
Route::post('Add_school_stage',[AdminController::class, 'Add_school_stage']);
Route::post('Add_school_subject/{school_stage}',[AdminController::class, 'Add_school_subject']);
Route::post('Add_university_stage',[AdminController::class, 'Add_university_stage']);
Route::post('Add_university_subject/{university_stage}',[AdminController::class, 'Add_university_subject']);
Route::get('Teacher_accounts_for_approve',[AdminController::class, 'get_teacher_account_for_approve'])->middleware('check_SuperAdmin');
Route::post('proccess_teacher_account/{teacher}',[AdminController::class, 'proccess_teacher_account'])->middleware('check_SuperAdmin');
Route::get('get_profile',[AdminController::class,'Admin_profile']);
Route::post('update_profile',[AdminController::class,'update_profile']);
Route::post('student_card_charging',[AdminController::class,'student_card_charging']);
Route::post('search_student1',[AdminController::class,'search_student']);
Route::post('search_teacher',[AdminController::class,'search_teacher']);
Route::post('delivery_cash_teacher',[AdminController::class,'delivery_cash_teacher']);
Route::get('get_teacher_for_delivery',[AdminController::class,'get_teacher_for_delivery']);
});

Route::middleware('check_teacher')->group(function(){
    Route::post('SendAccountForAprrove',[TeacherController::class, 'Register']);
    Route::get('teacher_profile',[TeacherController::class,'teacher_profile']);
    Route::post('update_teacher_profile',[TeacherController::class,'update_profile']);
    Route::post('add_worktime',[TeacherController::class, 'teacher_available_worktime'])->middleware('check_teacher_activate');
    Route::post('search_student',[AdminController::class,'search_student']);
    Route::get('getAllReservation',[TeacherController::class,'get_all_reservation'])->middleware('check_teacher_activate');
    Route::get('all_teacher_session',[ZoomSessionController::class,'get_session'])->middleware('check_teacher_activate');
    Route::post('proccess_reservation/{reservation}',[TeacherController::class,'proccess_reservation'])->middleware('check_teacher_activate');
    Route::post('Acceptance_cash_delivery/{delivery_cash}',[TeacherController::class,'Acceptance_cash_delivery']);
});



Route::prefix('zoom')->group(function () {
    Route::post('/sessions/{reservationId}/auto-create', [ZoomSessionController::class, 'autoCreateSession']);
    Route::post('/sessions/{sessionId}/join/teacher',    [ZoomSessionController::class, 'joinAsTeacher']);
    Route::post('/sessions/{sessionId}/join/student',    [ZoomSessionController::class, 'joinAsStudent']);
    Route::post('/sessions/{sessionId}/end',             [ZoomSessionController::class, 'endSession']);
    Route::post('/sessions/{sessionId}/leave',           [ZoomSessionController::class, 'leave']);
    Route::get('/sessions/{sessionId}',                  [ZoomSessionController::class, 'getSessionInfo']);
});
