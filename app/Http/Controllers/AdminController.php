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
            'bankAccount'=>'required|string',
            'SuperAdmin'=>'sometimes|boolean'
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $result=$this->admin_services->Regester($request);
        return response()->json(['message'=>'admin register successfully','admin'=>$result['admin'],'token'=>$result['token'],'refresh_token'=>$result['refresh_token'],
        'imageUrl'=>$result['imageUrl']]);
    }
    public function Admin_profile(){
       $profile= $this->admin_services->Admin_profile();
       if(!$profile){
        return response()->json(['message'=>'admin profile not found',404]);
       }
       return response()->json(['message'=>'admin profile retrieved successfully',
       'profile'=>$profile]);
    }

    public function update_profile(Request $request){
            $validator=Validator::make($request->all(),[
            "firstName"=> 'sometimes|string|max:255',
            "lastName"=> 'sometimes|string|max:255',
            "birthdate"=> 'sometimes|date',
            "email"=>'sometimes|email',
            "password"=> 'sometimes|min:8|alpha_num',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
            'bankAccount'=>'sometimes|string'
        ]);
        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()]);
        }
        $data=$validator->validated();
       $admin= $this->admin_services->profile_update($request,$data);
        return response()->json(['message'=>'admin profile update','profile'=>$admin[0],'imageUrl'=>$admin[1]]);
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
    public function student_card_charging(Request $request){
        $validate=Validator::make($request->all(),[
            'student_id'=>'required|exists:students,id',
            'card_charging'=>'required|integer',
        ]);
        if($validate->fails()){
            return response()->json(['message'=>$validate->errors()]);
        }
        $charging=$this->admin_services->card_charging($request);
        return response()->json(['message'=>'student card is charging successfully','charging_card'=>$charging]);
    }
    public function search_student(Request $request){
        $validate=Validator::make($request->all(),[
            'phoneNumber'=>'sometimes|integer',
            'firstName'=>'sometimes|string',
            'lastName'=>'sometimes|string'
        ]);
        $student=$this->admin_services->search_student($request);
        return response()->json(['message'=>'result search to students','students'=>$student]);
    }
    public function search_teacher(Request $request){
            $validate=Validator::make($request->all(),[
            'phoneNumber'=>'sometimes|integer',
            'firstName'=>'sometimes|string',
            'lastName'=>'sometimes|string'
        ]);
        $teacher=$this->admin_services->search_teacher($request);
        return response()->json(['message'=>'result search to teacher','teachers'=>$teacher]);
    }
    public function delivery_cash_teacher(Request $request ){
        $validate=Validator::make($request->all(),[
            'cash_value'=>'required|integer',
            'teacher_id'=>'required|exists:teacher,id'
            // 'delivery_time'=>'required|date_format:Y-m-d H:i'
        ]);
        $delivery_cash=$this->admin_services->delivery_cash_teacher($request);
        if($delivery_cash=='cash_larger_card'){
            return response()->json(['message'=>'teacher card is less than cash']);
        }
        if($delivery_cash=='Not_enought_cash'){
            return response()->json(['message'=>'admin dont have enought cash for deliver']);
        }
        return response()->json(['message'=>'cash deliver to teacher successfully','cash'=>$delivery_cash]);
    }
    public function get_teacher_for_delivery(){
        $deliver_teacher=$this->admin_services->teacher_for_delivery();
        if($deliver_teacher==null){
            return response()->json(['message'=>'not found any teacher for delivery']);
        }
        return response()->json(['message'=>'all teacher who is not recieve money','teacher'=>$deliver_teacher]);
    }
}
