<?php

namespace App\Repositories;

interface AdminRepositoriesInterface{
    public function AddSchool_stage($request);
    public function AddSchool_subjects($school_stage,$request,$imagePath);
    public function AddUniversity_stage($request,$specialize);
    public function AddUniversity_subject($university_stage,$request,$imagePath);
    public function create($request,$imagePath);
    public function Admin_profile($admin);
    public function proccess_teacher_account($teacher, $request);
}
