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



    public function get_school_stage()
    {
        $response = $this->student_services->School_stage();
        if ($response->isEmpty()) {
            return response()->json(['message' => 'not found any stage']);
        }
        return response()->json(['message' => 'get school stage successfully', 'result' => $response]);
    }
    public function choose_school_study_stage(Request $request){

        $validator=Validator::make($request->all(),['School_stage_id'=>'required|array',
            'School_stage_id.*' => 'integer|exists:school_stage,id']);
        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()]);
        }
        $School_stage_id = $request->input('School_stage_id');
        $result=$this->student_services->Choose_school_stage($School_stage_id);
        return response()->json(['message'=>'student add school stages successfully','result'=>$result]);
    }
    public function get_school_subjects_stage(School_stage $school_stage){
      $subjects=$this->student_services->School_stage_subjects($school_stage);
      if($subjects==null){
        return response()->json(['message'=>'not found any subjects']);
      }
      return response()->json(['message'=>'subjects in school stage','subjects'=>$subjects]);
    }

    public function choose_school_subjects( Request $request){
        $validator = Validator::make($request->all(), [
            'School_subject_id' => 'required|array',
            'School_subject_id.*' => 'integer|exists:school_subjects,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        $school_subjects=$request->input('School_subject_id');
        $result=$this->student_services->Student_subject($school_subjects);
        return response()->json(['message' => 'student add school subjects successfully', 'result' => $result]);
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
    public function get_university_stage(){
        $response = $this->student_services->University_stage();
        if ($response->isEmpty()) {
            return response()->json(['message' => 'not found any stage']);
        }
        return response()->json(['message' => 'get university stage successfully', 'result' => $response]);
    }

    public function Choose_university_study_stage(Request $request){
        $validate=Validator::make($request->all(),[
            'university_stage_id'=>'required|array',
            'university_stage_id.*'=> 'integer|exists:university_stage,id'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $university_stage_id=$request->input('university_stage_id');
       $university_stage= $this->student_services->Choose_university_stage($university_stage_id);
       return response()->json(['message'=>'university stage added successfully','result'=>$university_stage]);
    }
    public function get_university_stage_subjects(University_stage $university_stage){
        $subjects = $this->student_services->get_university_stage_subjects($university_stage);
        if ($subjects==null) {
            return response()->json(['message' => 'not found any subjects']);
        }
        return response()->json(['message' => 'subjects in university stage', 'subjects' => $subjects]);
    }
    public function choose_university_subjects(Request $request){
        $validate=Validator::make($request->all(),[
            'University_subjects_id'=>'required|array',
            'University_subjects_id.*'=>'integer|exists:University_subjects,id'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $University_subjects=$request->input('University_subjects_id');
       $result= $this->student_services->Student_university_subjects($University_subjects);
        return response()->json(['message'=>'university subjects added successfully','subjects'=>$result]);

    }
}
