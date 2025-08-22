<?php
namespace App\Services;

use App\Models\RefreshToken;
use App\Models\School_subjects;
use App\Models\Students;
use App\Models\Teacher;
use App\Models\Teacher_school_subjects;
use App\Models\Teacher_university_subjects;
use App\Models\University_subjects;
use App\Repositories\StudentRepositoriesInterface;
use App\Repositories\TeacherRepositoriesInterface;
use App\Repositories\TokenRepositories;
use App\Repositories\TokenRepositoriesInterface;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class StudentServices{
    protected $student_repositories_interface;
    protected $token_repositories_interface;
    protected $teacher_repositories_interface;
    public function __construct(StudentRepositoriesInterface $student_repositories_interface ,
     TokenRepositoriesInterface $token_repositories_interface,TeacherRepositoriesInterface $teacher_repositories_interface)
    {
    $this->student_repositories_interface=$student_repositories_interface;
    $this->token_repositories_interface=$token_repositories_interface;
    $this->teacher_repositories_interface=$teacher_repositories_interface;
    }

    public function Profile_complate($request,$data){
        $student_id = Auth::guard('student')->user()->id;
        $student = Students::where('id', '=', $student_id)->first();
        if($request->hasFile('image')){
            $originalName=$request->file('image')->getClientOriginalName();
            $path=$request->file('image')->storeAs('students/images',$originalName,'public');
            $data['image']=$path;
            $imageUrl = asset('storage/' . $path);
        }else{
            if($student->image==null){
                $imageUrl=null;
            }
            else{
            $imageUrl = asset('storage/' .$student->image);

            }
        }
        if(!empty($data['password'])){
            $data['password']=Hash::make($data['password']);
        }
        $student->update($data);
        $student->fresh();
        $student->is_profile_completed=true;
        $student->save();

        return [$student,$imageUrl];
    }
    public function Student_profile(){
    $student=Auth::guard('student')->user();
    $profile=$this->student_repositories_interface->Student_profile($student->id);
    return $profile;
    }

    public function  get_Available_reservations($request){
            $teacher=Teacher::with(['available_worktime','Reservations'])->findOrFail($request->teacher_id);
            $subject_type=$request->subject_type;
            if($subject_type=='school'){
                $subject=School_subjects::where('id','=',$request->subject_id)->first();
                $lesson_duration=Teacher_school_subjects::where('school_subject_id','=',$subject->id)->first()->lesson_duration;
                }else{
                    $subject=University_subjects::where('id','=',$request->subject_id)->first();
                    $lesson_duration=Teacher_university_subjects::where('university_subjects_id','=',$subject->id)->first()->lesson_duration;//
                }
                $duration = Carbon::createFromFormat('H:i:s', $lesson_duration);
                $lesson_duration = $duration->hour * 60 + $duration->minute;

                $startOfWeek = now();
                $result = [];
            foreach ($teacher->available_worktime as $worktime) {
            $dayName = $worktime->workingDay;
            $workStart = Carbon::createFromFormat('H:i:s', $worktime->start_time);
            $workEnd   = Carbon::createFromFormat('H:i:s', $worktime->end_time);
            $dayDate   = $startOfWeek->copy()->nextOrCurrent($dayName)->format('Y-m-d');
            $break_lessons=$worktime->break_duration_lessons;//
            $duration_break=Carbon::createFromFormat('H:i:s',$break_lessons);
            $break_lessons=$duration_break->hour * 60 + $duration_break->minute;

          //  جمع الحجوزات لهذا اليوم وتحويلها إلى فترات زمنية
            $reservations = $teacher->Reservations
                ->filter(function ($res) use ($dayDate, $dayName) {
                    $resDate = Carbon::parse($res->reservation_time)->format('Y-m-d');
                    return $res->reservation_day === $dayName && $resDate === $dayDate;
                })
                ->map(function ($res) {
                    $start = Carbon::parse($res->reservation_time);
                    $end = $start->copy()->addMinutes($res->duration);
                    return ['start' => $start, 'end' => $end];
                })->sortBy('start')->values();

            $freePeriods = [];
            $cursor = $workStart->copy();

            foreach ($reservations as $res) {
            // إذا كان هناك فراغ بين المؤشر وبداية الحجز
            if ($cursor->lt($res['start'])) {
                $freePeriods[] = ['start' => $cursor->copy(), 'end' => $res['start']->copy()->subMinutes($break_lessons)];
            }
            // تقدم المؤشر لنهاية الحجز الحالي
            if ($cursor->lt($res['end'])) {
                $cursor = $res['end']->copy()->addMinutes($break_lessons);
            }
        }
                // إضافة الفترة الأخيرة إذا بقي وقت بعد آخر حجز
        if ($cursor->lt($workEnd)) {
            $freePeriods[] = ['start' => $cursor->copy(), 'end' => $workEnd->copy()];
        }

        //  تقسيم الفترات الحرة بحسب مدة الجلسة مع مراعاة الاستراحة
        $availableSlots = [];
        foreach ($freePeriods as $period) {
            $slotStart = $period['start']->copy();
            while ($slotStart->copy()->addMinutes($lesson_duration)->lte($period['end'])) {
                $slotEnd = $slotStart->copy()->addMinutes($lesson_duration);
                $availableSlots[] = [
                    'start' => $slotStart->format('H:i'),
                    'end'   => $slotEnd->format('H:i'),
                    'date'  => $dayDate,
                    'status' => 'available',
                ];
                // نضيف فترة الاستراحة بعد كل جلسة
                $slotStart = $slotEnd->copy()->addMinutes($break_lessons);
            }
        }
        $reservedSlots = $reservations->map(function ($res) use ($dayDate) {
            return [
                'start' => $res['start']->format('H:i'),
                'end'   => $res['end']->format('H:i'),
                'date'  => $dayDate,
                'status'=> 'reserved',
            ];
        })->toArray();
                //  دمج الحجوزات مع الأوقات المتاحة
        $allSlots = array_merge($reservedSlots, $availableSlots);

        // ترتيب الأوقات حسب البداية
        usort($allSlots, function ($a, $b) {
            return strcmp($a['start'], $b['start']);
        });

        $result[$dayName] = $allSlots;

        }
        return $result;

    }
    public function reservation($request){
        $student_id=Auth::guard('student')->user()->id;
        $student=Students::findOrFail($student_id);
        $teacher=Teacher::findOrFail($request->teacher_id);
        if($request->subject_type=='school'){
            $subject=School_subjects::findOrFail($request->subject_id);
            $teacher_subject = $teacher->School_subjects()->wherePivot('school_subject_id', $subject->id)->first();
            $lessonDuration = $teacher_subject->pivot->lesson_duration;
            $lessonPrice    = $teacher_subject->pivot->lesson_price;
        }else{
            $subject=University_subjects::findOrFail($request->subject_id);
            $teacher_subject = $teacher->University_subjects()->wherePivot('university_subjects_id', $subject->id)->first();
            $lessonDuration = $teacher_subject->pivot->lesson_duration;
            $lessonPrice    = $teacher_subject->pivot->lesson_price;
        }

        if($lessonPrice <= $student->CardValue){
        return $this->student_repositories_interface->reservation($request,$student_id,$subject,$lessonDuration,$lessonPrice);
        }
        else{
            return "null";
        }

    }

}
