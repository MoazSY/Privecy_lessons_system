<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StudentServices;
use App\Services\TeacherServices;
use Laravel\Sanctum\PersonalAccessToken;

class NotificationsController extends Controller
{
    protected $student_services;
    protected $teacher_services;
    public function __construct(StudentServices $student_services,TeacherServices $teacher_services){
        $this->teacher_services=$teacher_services;
        $this->student_services=$student_services;
    }
    public function ShowAllNotifications(Request $request){
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        if ($user instanceof \App\Models\Students) {
            return $this->student_services->ShowAllNotifications();
        } elseif ($user instanceof \App\Models\Teacher) {
            return $this->teacher_services->ShowAllNotifications();
        }
    }
        public function markAsRead(Request $request,$id){
         $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        // $student=Auth::guard('student')->user()->id;
        if ($user instanceof \App\Models\Students) {
            return $this->student_services->markAsRead($id);
        } elseif ($user instanceof \App\Models\Teacher) {
            return $this->teacher_services->markAsRead($id);
        }
    }
}
