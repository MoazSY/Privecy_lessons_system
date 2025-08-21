<?php
namespace App\Repositories;

use App\Models\Payment_transaction;
use App\Models\RefreshToken;
use App\Models\School_stage;
use App\Models\School_subjects;
use App\Models\Student_subject;
use App\Models\Students;
use App\Models\University_stage;
use App\Models\University_subjects;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;

 class StudentRepositories implements StudentRepositoriesInterface{
public function create($request)
{
$student= Students::create([
"phoneNumber"=>$request->phoneNumber
]);
return $student;
}

    public function findStudent($student){
        $student=Students::findOrFail($student->id);
        return $student;
    }
    public function Auth($credintals)
{

}
public function StudentSchoolStage($student_id, $School_stage_id)
{
    $student=Students::findOrFail($student_id);
    $result= $student->School_stage()->sync($School_stage_id);
    $attachedStages = School_stage::whereIn('id', $result['attached'])->get();
    return $attachedStages;
    }
    // public function get_school_stage()
    // {
    //     $data=School_stage::all();
    //     $grouped = $data->groupBy('school_stage')->map(function ($classes) {
    //         return $classes->groupBy('className')->map(function ($semesters) {
    //             return $semesters->map(function ($record) {
    //                 return [
    //                     'semester' => $record->semester,
    //                     'id' => $record->id
    //                 ];
    //             })->unique('semester')->values();
    //         });
    //     });

    //     return $grouped;
    // }

    public function get_school_stage()
    {
        $data = School_stage::all();

        $grouped = $data->groupBy(function ($record) {
            return $record->specialize ? 'specialized' : 'general';
        });

        $general = collect();
        if ($grouped->has('general')) {
            $general = $grouped->get('general')->groupBy('school_stage')->map(function ($classes) {
                return $classes->groupBy('className')->map(function ($records) {
                    return $records->map(function ($record) {
                        return [
                            'semester' => $record->semester,
                            'id' => $record->id,
                        ];
                    })->unique('semester')->values();
                });
            });
        }

        $specialized = collect();
        if ($grouped->has('specialized')) {
            $specialized = $grouped->get('specialized')
                ->groupBy('secondary_school_branch')
                ->map(function ($branchGroup, $branchKey) {
                    if ($branchKey === 'vocational') {
                        return $branchGroup->groupBy('vocational_type')->map(function ($types) {
                            return $types->groupBy('className')->map(function ($records) {
                                return $records->map(function ($record) {
                                    return [
                                        'semester' => $record->semester,
                                        'id' => $record->id,
                                    ];
                                })->unique('semester')->values();
                            });
                        });
                    } else {
                        return $branchGroup->groupBy('className')->map(function ($records) {
                            return $records->map(function ($record) {
                                return [
                                    'semester' => $record->semester,
                                    'id' => $record->id,
                                ];
                            })->unique('semester')->values();
                        });
                    }
                });
        }

        return array_filter([
            'primary' => $general['primary'] ?? null,
            'preparatory' => $general['preparatory'] ?? null,
            'secondary' => $specialized->isEmpty() ? null : $specialized,
        ]);
    }


    public function SchoolSubjects($stage)
{
    $school_stage=School_stage::findOrFail($stage->id);
    if($school_stage){
        $subjects= $school_stage->School_subjects;
        if(!$school_stage->School_subjects()->exists()){
            $subjects=null;
        }
    }else{
        $subjects=null;
    }
    return $subjects;
}
public function StudentSchoolSubjects($student,$subjects){
    $student=Students::findOrFail($student);
    $result=$student->Subjects()->sync($subjects);
    $attachedSubjects=School_subjects::whereIn('id',$result['attached'])->get();
    return $attachedSubjects;
}
public function Student_profile($student){
        $student = Students::with([
            'School_stage',
            'Subjects',
            'University_stage',
            'Univesity_subjects'
        ])->find($student);
        return $student;
    }
    public function get_university_stage()
 {
       $data = University_stage::all();
       // return $data;
    $grouped = $data->groupBy('university_type')->map(function ($branches) {
    return $branches->groupBy('university_branch')->map(function ($colleges) {
     return $colleges->groupBy('college_name')->map(function ($years) {
     return $years->groupBy('study_year')->map(function ($records) {
    $hasSpecialize = $records->first()->specialize;
     if ($hasSpecialize) {
      return [
     'has_specialise' => true,
     'specialise' => $records->groupBy('specialize_name')->map(function ($semesters) {
       return $semesters->map(function ($record) {
        return [
        'semester' => $record->semester,
         'id' => $record->id
           ];
         })->unique('semester')->values();
         }),
         ];
        } else {
         return [
         'has_specialise' => false,
         'semesters' => $records->map(function ($record) {
         return [
         'semester' => $record->semester,
        'id' => $record->id
         ];
         })->unique('semester')->values(),
        ];
        }
            });
        });
    });
   });
   return $grouped;
    }

    public function UniversityStage($student_id,$university_stage_id)
{
    $student=Students::findOrFail($student_id);
    $result=$student->University_stage()->sync($university_stage_id);
    $attachedStage=University_stage::whereIn('id',$result['attached'])->get();
    return $attachedStage;
}
public function UniversitySubjects($stage)
{
$subjects_stage=University_stage::where('id','=',$stage->id)->first();
if($subjects_stage){
    $subjects_U_stage = $subjects_stage->University_subjects;
    if(!$subjects_stage->University_subjects()->exists()){
  $subjects_U_stage = null;
}
}else{
$subjects_U_stage=null;
}
return $subjects_U_stage;
}
public function Student_university_subjects($student,$subjects){
$student=Students::findOrFail($student);
$result=$student->Univesity_subjects()->sync($subjects);
$attached=University_subjects::whereIn('id',$result['attached'])->get();
return $attached;
}

public function reservation($request,$student_id,$subject,$lessonDuration,$lessonPrice)
{
DB::transaction(function() use ($request,$student_id,$subject,$lessonDuration,$lessonPrice){
    // $payment=Payment_transaction::create([
    //     // 'teacher_id'=>$request->
    // ]);
});
}







public function ShowTeacherAvailable($student)
{

}
public function TeacherRating($student, $teacher)
{

}
public function TeacherFollowing($student, $teacher, $RecieveNotification)
{

}
public function ShowTeacherProfile($teacher)
{

}

public function GetAvailableTimeTeacher($teacher)
{

}
public function GetStages_Subjecs_Teacher($teacher)
{

}
public function GetReservation($student)
{

}
public function GetLessonse($student)
{

}
public function ShowSlider()
{

}
public function Report($student, $lesson)
{

}
public function Payment($student, $teacher, $request)
{

}
}
