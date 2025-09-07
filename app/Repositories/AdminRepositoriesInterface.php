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
    public function charging_card($admin,$request);
    public function delivery_cash_teacher($admin_id,$request);
    public function teacher_for_delivery($admin_id);
    public function proccess_report($request,$report_proccess,$admin_id);
    public function transform_money($admin_id,$session);
    public function show_commisions($request);  
}
