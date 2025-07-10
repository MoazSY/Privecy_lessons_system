<?php

namespace App\Http\Controllers;

use App\Models\School_stage;
use App\Models\Students;
use App\Models\University_stage;
use App\Repositories\StudentRepositoriesInterface;
use Illuminate\Http\Request;
use App\Services\StudentServices;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    protected $student_services;
    public function __construct(StudentServices $student_services)
    {
        $this->student_services=$student_services;
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
    public function update_profile(){

    }
    public function filter_result(){
        
    }
    public function get_teacher(){

    }


}
