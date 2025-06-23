<?php
namespace App\Repositories;

use App\Models\Teacher;

 class TeacherRepositories implements TeacherRepositoriesInterface{
public function create($request)
{
$teacher=Teacher::create(['phoneNumber'=>$request->phoneNumber]);
$teacher->Activate_Account=false;
$teacher->save();
return $teacher;
}
public function SendAccountForAprrove($request){
    
}
}
