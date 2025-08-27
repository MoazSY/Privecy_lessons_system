<?php

namespace App\Http\Controllers;

use App\Models\Lesson_session;
use App\Models\School_stage;
use App\Models\Students;
use App\Models\University_stage;
use App\Models\Teacher;
use App\Repositories\StudentRepositoriesInterface;
use Illuminate\Http\Request;
use App\Services\StudentServices;
use App\Services\TeacherServices;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    protected $student_services;
    protected $teacher_services;
    public function __construct(StudentServices $student_services,TeacherServices $teacher_services)
    {
        $this->student_services=$student_services;
        $this->teacher_services=$teacher_services;
    }

    public function Profile_complete(Request $request){
        $validator=Validator::make($request->all(),[
            "firstName"=> 'sometimes|string|max:255',
            "lastName"=> 'sometimes|string|max:255',
            "birthdate"=> 'sometimes|date',
            "email"=>'required|email|unique:students',
            "password"=> 'required|min:8|alpha_num',
            "gender"=> 'sometimes|in:male,female',
            "about_him"=> 'sometimes|string|max:255',
            'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'accountNumber'=>'sometimes|unique:students|string'
        ]);
        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()]);
        }
        $data=$validator->validated();
       $student= $this->student_services->Profile_complate($request,$data);
        return response()->json(['message'=>'student profile complete','student'=>$student[0],'imageurl'=>$student[1]]);
    }
    public function Student_profile(){
       $profile= $this->student_services->Student_profile();
       if(!$profile){
        return response()->json(['message'=>'student profile not found',404]);
       }
       return response()->json(['message'=>'student profile retrieved successfully',
       'profile'=>$profile]);
    }
    public function update_profile(Request $request){
            $validator=Validator::make($request->all(),[
            "firstName"=> 'sometimes|string|max:255',
            "lastName"=> 'sometimes|string|max:255',
            "birthdate"=> 'sometimes|date',
            "email"=>'sometimes|email',
            "password"=> 'sometimes|min:8|alpha_num',
            "about_him"=> 'sometimes|string|max:255',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
            'accountNumber'=>'sometimes|string'
        ]);
        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()]);
        }
        $data=$validator->validated();
       $student= $this->student_services->Profile_complate($request,$data);
        return response()->json(['message'=>'student profile complete','student'=>$student[0],'imageurl'=>$student[1]]);
    }
    public function filter_result(Request $request){
        $validate=Validator::make($request->all(),[
             "gender"=> 'sometimes|in:male,female',
             "stage_type"=>'required|in:school,university',
             "study_stage_id"=>'sometimes|integer',
             "stage_subject_id"=>"sometimes|integer",
             "work_available_day"=>'sometimes|string',
             "work_available_time"=>'sometimes|date_format:H:i',
             'min_price'=>'sometimes|numeric|min:0',
             'max_price'=>'sometimes|numeric',
             'rate'=>'sometimes|numeric|min:1|max:5'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $teachers=$this->teacher_services->teacher_filter($request);
        return response()->json(['message'=>'all filter teachers','teachers'=>$teachers]);
    }

    public function get_teacher(){
    $results=$this->teacher_services->get_teacher();
    if($results==null){
        return response()->json(['message'=>"system dont have any teachers for you",404]);
    }
    return response()->json(['message'=>'teachers for you','teachers'=>$results]);
    }
    public function teacher_profile(Teacher $teacher){
    $profile=$this->teacher_services->teacherprofile($teacher);
    return response()->json(['message'=>'teacher profile','profile'=>$profile]);

    }
    public function Rating_teacher(Request $request,Teacher $teacher){
        $validate=Validator::make($request->all(),[
            "rate"=>'required|integer|min:1|max:5'
        ]);
    if($validate->fails()){
    return response()->json(['message'=>$validate->errors()]);
    }
    $rating= $this->teacher_services->Rating($request,$teacher);
    return response()->json(['message'=>'student has rated teacher','rate'=>$request->rate,'teacher'=>$rating]);
    }
    public function Following_teacher(Request $request,Teacher $teacher){
        $validate=Validator::make($request->all(),[
            'following_state'=>'required|boolean',
            'recieve_notifications'=>'required|boolean'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $result=$this->teacher_services->following($request,$teacher);
        return response()->json(['message'=>'student has followed teacher','following'=>$result]);
    }
    public function lesson_reservation(Request $request){
        $validate=Validator::make($request->all(),[
            'teacher_id'=>'required|integer|exists:teacher,id',
            'subject_type'=>'required|in:school,university',
            'subject_id'=>'required|integer|exists:school_subjects,id',
            'reservation_time' => 'required|date_format:Y-m-d H:i',
            'reservation_day'=>'required|string',
        ]);
            if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $reservation=$this->student_services->reservation($request);
        if($reservation=="null"){
            return response()->json(['message'=>'you should check your card not enought money ']);
        }
        return response()->json(['message'=>'the reservation of lesson is done successfully','reservation'=>$reservation]);
    }
    public function get_all_reservation(){
        $reservation=$this->student_services->all_reservation();
        return response()->json(['message'=>'all reservation related to student','reservation'=>$reservation]);
    }
    public function getWeeklyAvailableSlots_reservations(Request $request){
        $validator=Validator::make($request->all(),[
        'teacher_id'=>'required|integer',
        'subject_type'=>'required|in:school,university',
        'subject_id'=>'required|integer'
        ]);
        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()]);
        }
        $result=$this->student_services->get_Available_reservations($request);
        return response()->json(['message'=>'all reservations table for teacher','reservation_table'=>$result]);
    }
    public function add_session_video(Request $request,Lesson_session $session){
        $request->validate([
        'recording_file' => 'required|file|mimes:mp4,mov,mkv,webm,avi|max:512000', // حتى 500MB مثالاً
        ]);
        $session=$this->student_services->add_session_video($request,$session);
        return response()->json(['message'=>'recording uplode to session','session'=>$session[0],'recording_url'=>$session[1]]);
        
    }

}
