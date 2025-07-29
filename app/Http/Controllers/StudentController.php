<?php

namespace App\Http\Controllers;

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
             'rate'=>'sometimes|numeric|min:0'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $teachers=$this->teacher_services->teacher_filter($request);
        return response()->json(['message'=>'all filter teachers','teacher'=>$teachers]);
    }

    public function get_teacher(){
    $results=$this->teacher_services->get_teacher();
    if($results==null){
        return response()->json(['message'=>"system dont have any teachers for you",404]);
    }
    return response()->json(['message'=>'teachers for you','teachers'=>$results]);
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



}
