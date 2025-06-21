<?php

namespace App\Repositories;


use App\Models\Admin;
use App\Models\School_stage;
use App\Models\School_subjects;
use App\Models\University_stage;
use App\Models\University_subjects;
use App\Repositories\AdminRepositoriesInterface ;
use Illuminate\Support\Facades\Hash;

 class AdminRepositories implements AdminRepositoriesInterface{



    public function AddSchool_stage($request)
    {
        $school_stage=School_stage::create([
            'className'=>$request->className,
            'school_stage'=>$request->school_stage,
            'semester'=>$request->semester
        ]);
        return $school_stage;
    }
    public function AddSchool_subjects($school_stage, $request,$imagepath)
    {

        if($imagepath!=null){
            if($request->has('about_subject')){
                $school_subjects = School_subjects::create([
                    'name_subject' => $request->name_subject,
                    'about_subject' => $request->about_subject,
                    'subject_cover_image' => $imagepath,
                    'school_stage_id' => $school_stage->id
                ]);
            }else{
                $school_subjects = School_subjects::create([
                    'name_subject' => $request->name_subject,
                    'subject_cover_image' => $imagepath,
                    'school_stage_id' => $school_stage->id
                ]);
            }
        }else{
            if($request->has('about_subject')){
                $school_subjects = School_subjects::create([
                    'name_subject' => $request->name_subject,
                    'about_subject' => $request->about_subject,
                    'school_stage_id' => $school_stage->id
                ]);
            }else{
                $school_subjects = School_subjects::create([
                    'name_subject' => $request->name_subject,
                    'school_stage_id' => $school_stage->id
                ]);
            }
        }
            return $school_subjects;
    }
    public function AddUniversity_stage($request,$specialize)
    {
        if($specialize==true){
            $university_stage = University_stage::create([
                'university_type' => $request->university_type,
                'university_branch' => $request->university_branch,
                'college_name' => $request->college_name,
                'study_year' => $request->study_year,
                'specialize' => $request->specialize,
                'specialize_name' => $request->specialize_name,
                'semester' => $request->semester
            ]);
        }else{
            $university_stage = University_stage::create([
                'university_type' => $request->university_type,
                'university_branch' => $request->university_branch,
                'college_name' => $request->college_name,
                'study_year' => $request->study_year,
                'specialize' => false,
                'specialize_name' => null,
                'semester' => $request->semester
            ]);
        }
    return $university_stage;
    }
    public function AddUniversity_subject($university_stage, $request,$imagepath)
    {
        if($imagepath!=null){
            $university_subjects = University_subjects::create([
                'university_stage_id' => $university_stage->id,
                'subject_name' => $request->subject_name,
                'about_subject' => $request->about_subject,
                'subject_cover_image' => $request->subject_cover_image
            ]);
        }
        $university_subjects=University_subjects::create([
            'university_stage_id'=> $university_stage->id,
            'subject_name'=>$request->subject_name,
            'about_subject'=>$request->about_subject,
        ]);
        return $university_subjects;
    }
    public function create($request,$imagepath){
        if($imagepath!=null){
            $admin=Admin::create([
                'firstName'=>$request->firstName,
                'lastName'=>$request->lastName,
                'phoneNumber'=>$request->phoneNumber,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
                'image'=>$imagepath,
                'birthdate'=>$request->birthdate,
                'gender'=>$request->gender,
                'bankAccount'=>$request->bankAccount
            ]);
        }
        else{
            $admin = Admin::create([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'phoneNumber' => $request->phoneNumber,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'birthdate' => $request->birthdate,
                'gender' => $request->gender,
                'bankAccount' => $request->bankAccount
            ]);
        }
        return $admin;
    }

}
