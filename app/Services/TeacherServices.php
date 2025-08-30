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
        if($request->hasFile('identification_image')){

        $identification_image=$request->file('identification_image')->getClientOriginalName();
        $path_identification_image=$request->file('identification_image')->storeAs('teacher/images/id',$identification_image,'public');
        $data['identification_image']= $path_identification_image;
        $ImageIdURl=asset('storage/'.$path_identification_image);
        $teacher->Activate_Account=false;
        $teacher->save();
        }
        else{
            if($teacher->identification_image==null){
              $ImageIdURl=null;
            }
            $ImageIdURl=asset('storage/'.$teacher->identification_image);
        }

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
        return [$teacher,$imageUrl,$fileUrl,$ImageIdURl];
    }

    public function teacher_profile(){
    $teacher=Auth::guard('teacher')->user();
        $profile=$this->teacher_repositories_interface->teacher_profile($teacher);
        return $profile;
    }
        public function teacherprofile($teacherid){
            $teacher=Teacher::findOrFail($teacherid->id);
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
    public function teacher_filter($request){
        $array=[];
        // $query=Teacher::query();
    //    $query= Teacher::select('teacher.*');
        $query = Teacher::withAvg('Rating', 'rate')->
        withCount(['folowing_value' => fn($q) => $q->where('following_state', true)])
        ->with(['School_subjects', 'University_subjects', 'available_worktime'])
        ->where('Activate_Account', true);

        // $query->where('Activate_Account',true);

        if($request->filled('gender')){
        $query->where('gender',$request->input('gender'));
        }
        if($request->filled('stage_type')){
            if($request->stage_type=='school'){
                if($request->filled('study_stage_id')){
                $query->whereHas('School_stage',function($q)use ($request){
                    $q->where('school_stage.id', $request->input('study_stage_id'));
                });
                if($request->filled('stage_subject_id')){
                    $query->whereHas('School_subjects',function($q)use ($request){
                        $q->where('school_stage_id',$request->input('study_stage_id'))->where('id',$request->input('stage_subject_id'));
                    });
                }
                }
                else{
                $query->whereHas('School_stage');
                }
                    if($request->filled('min_price') && $request->filled('max_price')){
                    $query->whereHas('School_subjects',function($q) use($request){
                    $q->whereRaw('teacher_school_subjects.lesson_price BETWEEN ? AND ?', [
                    $request->input('min_price'),
                    $request->input('max_price'),
                    ]);
                    });
                }
            }
            if($request->stage_type=='university'){
                if($request->filled('study_stage_id')){
                $query->whereHas('University_stage',function($q)use ($request){
                    $q->where('University_stage.id', $request->input('study_stage_id'));
                });
                     if($request->filled('stage_subject_id')){
                    $query->whereHas('University_subjects',function($q)use ($request){
                        $q->where('university_stage_id',$request->input('study_stage_id'))->where('id',$request->input('stage_subject_id'));
                    });
                }
                }else{
                $query->whereHas('University_stage');
                }
                    if($request->filled('min_price') && $request->filled('max_price')){
                    $query->whereHas('University_subjects',function($q) use($request){
                    $q->whereRaw('teacher_university_subjects.lesson_price BETWEEN ? AND ?', [
                    $request->input('min_price'),
                    $request->input('max_price'),
                    ]);
                    });
                }}}
            if($request->filled('work_available_day')){
            $query->whereHas('available_worktime',function($q)use($request){
            $q->where('workingDay',$request->input('work_available_day'));
            });
            // يجب اضافة فلتلرة المتاح الان
            if($request->filled('work_available_time')){
            $query->whereHas('available_worktime',function($q)use($request){
            $q->where('workingDay',$request->input('work_available_day'))->whereTime('start_time', '<=', $request->input('work_available_time'))
            ->whereTime('end_time', '>=', $request->input('work_available_time'));
            });
            // هنا يتم الفلترة على وقت العمل يجب اضافة وقت الاتاحة اي عدم الحجز
            }
            }
            if ($request->filled('rate')) {
                 $query->having('rating_avg_rate', '>=', $request->input('rate'));
                }

            $result = $query->get()->map(function ($teacher) use ($request) {
                return [
                    "teacher" => $teacher,
                    "teacherImageUrl" => asset('storage/' . $teacher->image),
                    "subjects" => $request->stage_type == 'school' ? $teacher->School_subjects : $teacher->University_subjects,
                    "workTime" => $teacher->available_worktime,
                    "rating_avg" => $teacher->rating_avg_rate,
                ];
            });
            return $result;
    }
    public function Rating($request,$teacher){
        $student=Auth::guard('student')->user()->id;
        return $this->teacher_repositories_interface->Rating($request,$student,$teacher);
    }
    public function following($request,$teacher){
        $student=Auth::guard('student')->user()->id;
        return $this->teacher_repositories_interface->following($request,$student,$teacher);
    }
    public function all_reservation(){
        $teacher_id=Auth::guard('teacher')->user()->id;
        return $this->teacher_repositories_interface->get_all_reservations($teacher_id);
    }
    public function proccess_reservation($request,$reservation){
    $teacher_id=Auth::guard('teacher')->user()->id;
    return $this->teacher_repositories_interface->proccess_reservation($request,$teacher_id,$reservation);
    }

    public function Acceptance_cash_delivery($request,$cash_delevery){
    $teacher_id=Auth::guard('teacher')->user()->id;
    return $this->teacher_repositories_interface->Acceptance_cash_delivery($teacher_id,$request,$cash_delevery);
    }
}
