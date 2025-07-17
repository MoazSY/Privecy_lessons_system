<?php

namespace App\Http\Controllers;

use App\Services\TeacherServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    protected $teacher_services;
    public function __construct(TeacherServices $teacher_services)
    {
    $this->teacher_services=$teacher_services;
    }
    public function Register(Request $request){
      $validate=Validator::make($request->all(),[
            'firstName'=>'required|string',
            'lastName'=>'required|string',
            'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'identification_image'=> 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'birthdate' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(24)->format('Y-m-d'),
            ],
            'url_certificate_file'=> 'required|file|mimes:pdf,doc,docx,txt',
            'about_teacher'=>'sometimes|nullable|string',
            'email'=>'required|email|unique:teacher',
            'password'=> 'required|min:8|alpha_num',
            'gender'=>'required|string',
            'account_number'=>'sometimes|nullable|unique:teacher|string'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()]);
        }
        $data=$validate->validated();
        $teacher=$this->teacher_services->Register($request,$data);
        return response()->json(['message'=>'teacher complete account and wait to approvement','teacher'=>$teacher[0],'imageUrl'=>$teacher[1],
        'Certificate_File_Url'=>$teacher[2]]);
}
    public function teacher_profile(){
        $profile=$this->teacher_services->teacher_profile();
        if(!$profile){
        return response()->json(['message'=>'teacher profile not found',404]);
       }
       return response()->json(['message'=>'teacher profile retrieved successfully',
       'profile'=>$profile]);
    }

public function update_profile(Request $request){
      $validate=Validator::make($request->all(),[
            'firstName'=>'sometimes|string',
            'lastName'=>'sometimes|string',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
            'identification_image'=> 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
            'birthdate' => [
                'sometimes',
                'date',
                'before_or_equal:' . now()->subYears(24)->format('Y-m-d'),
            ],
            'url_certificate_file'=> 'sometimes|file|mimes:pdf,doc,docx,txt',
            'about_teacher'=>'sometimes|string',
            'email'=>'sometimes|email',
            'password'=> 'sometimes|min:8|alpha_num',
            'account_number'=>'sometimes|string'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()]);
        }
         $data=$validate->validated();
        $teacher=$this->teacher_services->Register($request,$data);
        return response()->json(['message'=>'teacher complete account and wait to approvement','teacher'=>$teacher[0],'imageUrl'=>$teacher[1],
        'Certificate_File_Url'=>$teacher[2]]);
}
    public function teacher_available_worktime(Request $request){
        $validate=Validator::make($request->all(),[
            'available_worktime'=>'required|array',
            'available_worktime.*.workingDay'=>'required|string',
            'available_worktime.*.start_time'=>'required|date_format:H:i',
            'available_worktime.*.end_time' => 'required|date_format:H:i',
            'available_worktime.*.break_duration_lessons' => 'required|date_format:H:i',
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $workTime=$this->teacher_services->add_worktime($request);
        return response()->json(['message'=>'teacher adding work time successfully','WorkTime'=>$workTime]);
    }


}
