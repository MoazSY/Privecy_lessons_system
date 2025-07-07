<?php

namespace App\Http\Controllers;

use App\Models\School_stage;
use App\Models\Teacher;
use App\Models\University_stage;
use App\Services\AdminServices;
use App\Services\TeacherServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    protected $admin_services;
    protected $teacherServices;
    public function __construct(AdminServices $admin_services,TeacherServices $teacherServices)
    {
        $this->admin_services=$admin_services;
        $this->teacherServices=$teacherServices;
    }
    public function Regester(Request $request){
        $validate=Validator::make($request->all(),[
            'firstName'=>'required|string',
            'lastName'=>'required|string',
            'phoneNumber'=>'required|string',
            'email'=>'required|email|unique:admin',
            'password'=> 'required|min:8|alpha_num',
            'image'=> 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'birthdate'=>'required|date',
            'gender'=>'required|string',
            'bankAccount'=>'required|string'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $result=$this->admin_services->Regester($request);
        return response()->json(['message'=>'admin register successfully','admin'=>$result['admin'],'token'=>$result['token'],'refresh_token'=>$result['refresh_token'],
        'imageUrl'=>$result['imageUrl']]);
    }
    public function Login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|alpha_num|min:8'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        $result=$this->admin_services->login($request);
        return $result;
    }
    public function logout(Request $request){
        return $this->admin_services->Logout($request);
    }
    public function Add_school_stage(Request $request){
        $validate=Validator::make($request->all(),[
            'className'=>'required|string',
            'school_stage'=>'required|string',
            'semester'=>'required|string',
            'specialize'=>'sometimes|nullable|boolean',
            'secondary_school_branch'=>'sometimes|nullable|string',
            'vocational_type'=> 'sometimes|nullable|string'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
      $result= $this->admin_services->Add_school_stage($request);
      return response()->json(['message'=>'school stage added successfully','result'=>$result]);
    }
    public function Add_school_subject(Request $request,School_stage $school_stage){
        $validate=Validator::make($request->all(),[
            'name_subject'=>'required|string',
            'about_subject'=>'sometimes|nullable|string',
            'subject_cover_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
       $result= $this->admin_services->Add_school_subject($request,$school_stage);
        return response()->json(['message'=>'school subject added successfully','result'=>$result]);
    }
    public function Add_university_stage(Request $request){
     $validate=Validator::make($request->all(),[
            'university_type'=>'required|string',
            'university_branch'=>'required|string',
            'college_name'=>'required|string',
            'study_year'=>'required|string',
            'specialize'=> 'sometimes|nullable|boolean',
            'specialize_name'=>'sometimes|nullable|string',
            'semester'=>'required|string'
     ]);
     if($validate->fails()){
        return response()->json(['message'=>$validate->errors()]);
     }

     $result=$this->admin_services->Add_university_stage($request);
     return response()->json(['message'=>'university stage added successfully','result'=>$result]);
    }
    public function Add_university_subject(Request $request, University_stage $university_stage){
        $validate = Validator::make($request->all(), [
            'subject_name'=>'required|string',
            'about_subject'=>'sometimes|nullable|string',
            'subject_cover_image'=> 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()]);
        }
        $result= $this->admin_services->Add_university_subject($request,$university_stage);
        return response()->json(['message'=>'university subject added successfully','result'=>$result]);
    }
    public function get_teacher_account_for_approve(){
        $accounts=$this->teacherServices->UnActivate_account();
        if(empty($accounts)){
            return response()->json(['message'=>'not found any accounts for approve',404]);
        }
        return response()->json(['message'=>'All teacher accounts for approve','Accounts'=>$accounts]);
    }
    public function proccess_teacher_account(Teacher $teacher,Request $request){
        $validate=Validator::make($request->all(),[
            'state'=> 'required|in:approve,reject',
            'cause_of_reject'=>'sometimes|nullable|string'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $proccess_account=$this->admin_services->proccess_teacher_account($teacher,$request);
        return response()->json(['message'=>'teacher account proccess successfully']);
    }
}
