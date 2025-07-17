<?php
namespace App\Services;

use App\Models\Students;
use App\Models\Teacher;
use App\Repositories\TeacherRepositories;
use App\Repositories\TeacherRepositoriesInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherServices{
protected $teacher_repositories_interface;
    public function __construct(TeacherRepositoriesInterface $teacher_repositories_interface)
    {
    $this->teacher_repositories_interface= $teacher_repositories_interface;
    }
    public function Register($request,$data){
        $teacher_id=Auth::guard('teacher')->user()->id;
        $teacher=Teacher::where('id','=',$teacher_id)->first();
        if($request->hasFile('url_certificate_file')){
        $experienceFile=$request->file('url_certificate_file')->getClientOriginalName();
        $path_experienceFile=$request->file('url_certificate_file')->storeAs('teacher/Files',$experienceFile,'public');
        $data['url_certificate_file']= $path_experienceFile;
        $fileUrl=asset('storage/'.$path_experienceFile);
        $teacher->Activate_Account=false;
        $teacher->save();
        }else{
            if($teacher->url_certificate_file==null){
              $fileUrl=null;
            }
            $fileUrl=asset('storage/'.$teacher->url_certificate_file);
        }
        if($request->hasFile('image')){
            $originalName = $request->file('image')->getClientOriginalName();
            $path = $request->file('image')->storeAs('teacher/images', $originalName, 'public');
            $data['image'] = $path;
            $imageUrl=asset('storage/' . $path);
        }else{
            if($teacher->image==null){
            $imageUrl=null;
            }
            else{
              $imageUrl=asset('storage/' .$teacher->image);
            }
        }
        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }
        $teacher->update($data);
        $teacher->fresh();
        return [$teacher,$imageUrl,$fileUrl];
    }

    public function teacher_profile(){
    $teacher=Auth::guard('teacher')->user();
        $profile=$this->teacher_repositories_interface->teacher_profile($teacher);
        return $profile;
    }

    public function UnActivate_account(){
    return $this->teacher_repositories_interface->UnActivate_account();
    }
    public function add_worktime($request){
        $teacher_id = Auth::guard('teacher')->user()->id;
        return $this->teacher_repositories_interface->add_worktime($request, $teacher_id);
    }

    public function get_teacher(){
        $student_id=Auth::guard('student')->user()->id;
        $student=Students::findOrFail($student_id);
        if($student->Subjects()->exists()||$student->Univesity_subjects()->exists()){
            $school_subjects=$student->Subjects;
            $university_subjects=$student->Univesity_subjects;
            return $this->teacher_repositories_interface->get_teacher($school_subjects,$university_subjects);
        }
        return null;
    }
}
