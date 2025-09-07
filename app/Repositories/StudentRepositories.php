<?php
namespace App\Repositories;

use App\Models\Admin;
use App\Models\Lesson_reservation;
use App\Models\Payment_transaction;
use App\Models\RefreshToken;
use App\Models\School_stage;
use App\Models\School_subjects;
use App\Models\Student_subject;
use App\Models\Students;
use App\Models\University_stage;
use App\Models\University_subjects;
use App\Models\Lesson_session;
use App\Models\Report;
use App\Models\Teacher;
use App\Notifications\ReportSession;
use App\Notifications\LessonBooked;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
    $student=Students::findOrFail($student_id);
    $teacher=Teacher::findOrFail($request->teacher_id);

    $durationObj     = Carbon::createFromFormat('H:i:s', $lessonDuration);
    $lesson_duration = $durationObj->hour * 60 + $durationObj->minute;

    $requestedStart = Carbon::createFromFormat('Y-m-d H:i', $request->reservation_time);
    $requestedEnd   = $requestedStart->copy()->addMinutes($lesson_duration);

    $hasOverlap = Lesson_reservation::where('student_id', $student_id)
        ->whereIn('state_reservation', ['Watting_approve', 'accepted'])
        ->whereDate('reservation_time', $requestedStart->toDateString())
        ->where('reservation_time', '<', $requestedEnd->format('Y-m-d H:i:s')) // existing_start < requested_end
        ->whereRaw('DATE_ADD(reservation_time, INTERVAL duration MINUTE) > ?', [
            $requestedStart->format('Y-m-d H:i:s') // existing_end > requested_start
        ])
        ->exists();

    if ($hasOverlap) {
        return 'overlap';
    }

 DB::transaction(function() use ($request,$student_id,$subject,$lessonDuration,$lessonPrice,$teacher,$student){
   $admin= $this->getRandomAdmin();

    $duration = Carbon::createFromFormat('H:i:s', $lessonDuration);
    $lesson_duration = $duration->hour * 60 + $duration->minute;

    $payment=Payment_transaction::create([
        'teacher_id'=>$request->teacher_id,
        'student_id'=>$student_id,
        'amount'=>$lessonPrice,
        'admin_payout_teacher'=>false,
        'Admin_Id'=>$admin->id,
        'commission_value'=>0.15*$lessonPrice,
        'payment_transaction_time'=>Carbon::now()

    ]);

    $reservation=$subject->reservations()->create([
        'teacher_id'=>$request->teacher_id,
        'student_id'=>$student_id,
        'reservation_time'=>$request->reservation_time,
        'reservation_day'=>$request->reservation_day,
        'state_reservation'=>"Watting_approve",
        'duration'=> $lesson_duration
    ]);


    $payment->S_or_G_lesson()->associate($reservation);
    $payment->save();
    $student=Students::findOrFail($student_id);
    $student->CardValue-=$lessonPrice;
    $student->save();

    $teacher->notify(new LessonBooked($reservation,$student,$subject));

});
   $lastReservation = $student->Reservations()->orderBy('id', 'desc')->first();
return $lastReservation;

}
public function getRandomAdmin()
{

    $admins = Admin::where('SuperAdmin','=',false)->get();
    $count = $admins->count();

    if ($count === 0) {
        return null;
    }

    $currentIndex = Cache::get('round_robin_index', 0);

    $admin = $admins[$currentIndex];

    Cache::put(
        'round_robin_index',
        ($currentIndex + 1) % $count,
        now()->addDay()
    );

    return $admin;
}
public function get_all_reservations($student_id){
$student=Students::findOrFail($student_id);
$reservations = $student->reservations()
    ->whereIn('state_reservation', ['Watting_approve', 'accepted','rejectd'])
    ->whereDoesntHave('lesson_session')
    ->with(['teacher', 'subjectable'])
    ->orderBy('reservation_day', 'asc')
    ->orderBy('reservation_time', 'asc')->get()
->map(function ($reservation) {
    $currentDateTime = Carbon::now();

    $reservationDateTime = Carbon::parse($reservation->reservation_time);

    $timeDifference = $currentDateTime->diff($reservationDateTime);

    $reservation->time_remaining = [
        'days' => $timeDifference->d,
        'hours' => $timeDifference->h,
        'minutes' => $timeDifference->i,
        'total_hours' => $timeDifference->h + ($timeDifference->d * 24)
    ];

    $reservation->is_past = $currentDateTime->greaterThan($reservationDateTime);
    $reservation->is_upcoming = !$reservation->is_past;
    $reservation->human_readable = $timeDifference->format('%d day, %h hour, %i minute');

    return $reservation;
});
return $reservations;
}
public function add_session_video($path,$student,$session){
$student=Students::findOrFail($student->id);
$session=$student->lesson_session()->where('status','=','completed')->findOrFail($session->id);
if($session){
    $session->recording_path=$path;
    $session->save();
    $session_recording_url=asset('storage/' . $path);
}
else{
    $session_recording_url=null;
}
return [$session,$session_recording_url];
}

public function report($student,$request,$session,$path){
$student=Students::findOrFail($student);
$session=Lesson_session::findOrFail($session->id);
$teacher=Teacher::findOrFail($session->teacher_id);
$now=Carbon::now();
$end_time=Carbon::parse($session->end_time);
if($now > $end_time->copy()->addMinutes(10)|| $now<$end_time){
    return null;
}
$report=$student->Report()->create([
    'admin_id'=>$session->S_or_G_lesson->payments->first()->Admin_Id,
    'student_id'=>$student->id,
    'lesson_session'=>$session->id,
    'type_report'=>$request->input("type_report"),
    'reference_report_path'=>$path,
    'descreption'=>$request->input('descreption') ?: null,
    'time_report'=>$now,
    'state'=>'In_Review'
]);
$teacher->notify(new ReportSession($student,$report));
return $report;
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

public function Payment($student, $teacher, $request)
{

}
}
