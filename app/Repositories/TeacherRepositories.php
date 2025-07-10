<?php
namespace App\Repositories;

use App\Models\School_stage;
use App\Models\School_subjects;
use App\Models\Teacher;
use App\Models\Teacher_school_subjects;
use App\Models\Teacher_university_stage;
use App\Models\University_stage;
use App\Models\University_subjects;

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
   public function  teacherSchoolStage($teacher_id,$school_stage_id){
        $teacher = Teacher::findOrFail($teacher_id);
        $result = $teacher->School_stage()->sync($school_stage_id);
        $attachedStage = School_stage::whereIn('id', $result['attached'])->get();
        return $attachedStage;
   }
    public function TeacherSchoolSubjects($teacher_id, $subjects, $request)
    {
        $teacher = Teacher::findOrFail($teacher_id);
        $pivotData = [];
        foreach ($subjects as $subject) {
            if (isset($subject['id'], $subject['lesson_duration'], $subject['lesson_price'])) {
                $pivotData[$subject['id']] = [
                    'lesson_duration' => $subject['lesson_duration'],
                    'lesson_price' => $subject['lesson_price'],
                ];
            }
        }
        $result = $teacher->School_subjects()->sync($pivotData);

        $attachedSubjects = School_subjects::whereIn('id', $result['attached'])->get();

        return $attachedSubjects;
    }

    public function UniversityStage($teacher_id, $university_stage_id){
        $teacher = Teacher::findOrFail($teacher_id);
        $result = $teacher->University_stage()->sync($university_stage_id);
        $attachedStage = University_stage::whereIn('id', $result['attached'])->get();
        return $attachedStage;
   }
   public function Teacher_university_subjects($teacher,$subjects,$request){
        $teacher = Teacher::findOrFail($teacher);
        $pivotData = [];
        foreach ($subjects as $subject) {
            if (isset($subject['id'], $subject['lesson_duration'], $subject['lesson_price'])) {
                $pivotData[$subject['id']] = [
                    'lesson_duration' => $subject['lesson_duration'],
                    'lesson_price' => $subject['lesson_price'],
                ];
            }
        }
        $result = $teacher->University_subjects()->sync($pivotData);
        $attached = University_subjects::whereIn('id', $result['attached'])->get();
        return $attached;
   }

   public function UnActivate_account(){
    $array=[];
    $accounts= Teacher::where('Activate_Account','=',false)->get();
    foreach($accounts as $account){
        if($account->School_subjects()->exists()|| $account->University_subjects()->exists()){
            $teacher_School_Subjects=$account->School_subjects;
            $teacher_University_Subjects=$account->University_subjects;
            $array[]=["account"=>$account];
        }
    }
    return $array;
   }
   public function add_worktime($request,$teacher_id){
    $teacher=Teacher::findOrFail($teacher_id);
    $teacher->available_worktime()->createMany($request->available_worktime);
    return $teacher->available_worktime;
   }

}
