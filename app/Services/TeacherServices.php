<?php
namespace App\Services;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherServices{
    public function Register($request,$data){
        $teacher_id=Auth::guard('teacher')->user()->id;
        $teacher=Teacher::where('id','=',$teacher_id)->first();
        $experienceFile=$request->file('url_certificate_file')->getClientOriginalName();
        $path_experienceFile=$request->file('url_certificate_file')->storeAs('teacher/Files',$experienceFile,'public');
        $data['url_certificate_file']= $path_experienceFile;
        $fileUrl=asset('storage/'.$path_experienceFile);
        if($request->hasFile('image')){
            $originalName = $request->file('image')->getClientOriginalName();
            $path = $request->file('image')->storeAs('teacher/images', $originalName, 'public');
            $data['image'] = $path;
            $imageUrl=asset('storage/' . $path);
        }else{$imageUrl=null;}
        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }
        $teacher->update($data);
        $teacher->fresh();
        return [$teacher,$imageUrl,$fileUrl];

    }
}
