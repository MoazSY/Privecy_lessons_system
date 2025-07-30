<?php
namespace App\Repositories;
 interface TeacherRepositoriesInterface{
    public function create($request);
    public function teacher_profile($teacher);
    public function SendAccountForAprrove($request);
    public function teacherSchoolStage($teacher_id,$school_stage_id);
    public function TeacherSchoolSubjects($teacher,$subjects,$request);
    public function UniversityStage($teacher, $university_stage_id);
    public function Teacher_university_subjects($teacher,$subjects,$request);
    public function UnActivate_account();
    public function add_worktime($request,$teacher_id);
    public function get_teacher($school_subjects,$university_subjects);
    public function Rating($request,$student,$teacher);
    public function following($request,$student,$teacher); 
 }
