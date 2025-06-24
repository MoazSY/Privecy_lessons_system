<?php

namespace App\Http\Controllers;

use App\Services\TeacherServices;
use Illuminate\Http\Request;
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
            'email'=>'required|email',
            'password'=> 'required|min:8|alpha_num',
            'gender'=>'required|string',
            'account_number'=>'sometimes|nullable|string'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()]);
        }
        $data=$validate->validated();
        $teacher=$this->teacher_services->Register($request,$data);
        return response()->json(['message'=>'teacher complete account and wait to approvement','teacher'=>$teacher[0],'imageUrl'=>$teacher[1],
    'Certificate_File_Url'=>$teacher[2]]);
}
    
}
