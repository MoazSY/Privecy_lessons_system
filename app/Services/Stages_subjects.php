<?php
namespace App\Services;

use App\Repositories\StudentRepositoriesInterface;
use App\Repositories\TeacherRepositoriesInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class Stages_subjects{
    protected $student_repositories_interface;
    protected $teacherRepositoriesInterface;

    public function __construct(StudentRepositoriesInterface $student_repositories_interface , TeacherRepositoriesInterface $teacherRepositoriesInterface)
    {
        $this->student_repositories_interface=$student_repositories_interface;
        $this->teacherRepositoriesInterface=$teacherRepositoriesInterface;
    }


    public function School_stage()
    {
        return $this->student_repositories_interface->get_school_stage();
    }

    public function Choose_school_stage($request, $School_stage_id)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        // $student=Auth::guard('student')->user()->id;
        if ($user instanceof \App\Models\Students) {
            return $this->student_repositories_interface->StudentSchoolStage($user->id, $School_stage_id);
        } elseif ($user instanceof \App\Models\Teacher) {
            return $this->teacherRepositoriesInterface->teacherSchoolStage($user->id,$School_stage_id);
        }

    }

    public function School_stage_subjects($school_stage)
    {
        $array = [];
        $result = $this->student_repositories_interface->SchoolSubjects($school_stage);
        if ($result != null) {
            foreach ($result as $subject) {
                if ($subject->subject_cover_image != null) {
                    $imageUrl = asset('storage/' . $subject->subject_cover_image);
                    $array[] = ['subject' => $subject, 'imageUrl' => $imageUrl];
                } else {
                    $array[] = ['subject' => $subject, 'imageUrl' => null];
                }
            }
            return $array;
        }
        return null;
    }

    public function choose_school_subjects($request)
    {
        $token=PersonalAccessToken::findToken($request->bearerToken());
        $user=$token->tokenable;
        if($user instanceof \App\Models\Students){
            $validator = Validator::make($request->all(), [
                'School_subject_id' => 'required|array',
                'School_subject_id.*' => 'integer|exists:school_subjects,id'
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()]);
            }
            $school_subjects = $request->input('School_subject_id');
            return $this->student_repositories_interface->StudentSchoolSubjects($user->id, $school_subjects);
        }
        elseif($user instanceof \App\Models\Teacher){
            $validate=Validator::make($request->all(),[
                'School_subject_id' => 'required|array|min:1',
                'School_subject_id.*.id' => 'required|integer|exists:school_subjects,id',
                'School_subject_id.*.lesson_duration' => [
                    'required',
                    'date_format:H:i',
                    function ($attribute, $value, $fail) {
                        [$hours, $minutes] = explode(':', $value);
                        $totalMinutes = $hours * 60 + $minutes;
                        if ($totalMinutes < 30) {
                            $fail('lesson duration must dont to be less 30 minute');
                        }
                    },
                ],
                'School_subject_id.*.lesson_price'=>'required|numeric|min:0'
            ]);
            if($validate->fails()){
                return response()->json(['message'=>$validate->errors()]);
            }
            $school_subjects = $request->input('School_subject_id');
            return $this->teacherRepositoriesInterface->TeacherSchoolSubjects($user->id, $school_subjects,$request);
        }else {
            return "not found";
        }
    }

    public function University_stage()
    {
        return $this->student_repositories_interface->get_university_stage();
    }


    public function Choose_university_stage($request,$university_stage_id)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        
        if($user instanceof \App\Models\Students){
            // return $user;
            return  $this->student_repositories_interface->UniversityStage($user->id, $university_stage_id);
        }
        elseif($user instanceof \App\Models\Teacher){
            // return $user;
            return $this->teacherRepositoriesInterface->UniversityStage($user->id,$university_stage_id);
        }
        else{
        return null;
        }
    }

    public function get_university_stage_subjects($university_stage)
    {
        $array = [];
        $result = $this->student_repositories_interface->UniversitySubjects($university_stage);
        if ($result != null) {
            foreach ($result as $subject) {
                if ($subject->subject_cover_image != null) {
                    $imageUrl = asset('storage/' . $subject->subject_cover_image);
                    $array[] = ['subject' => $subject, 'imageUrl' => $imageUrl];
                } else {
                    $array[] = ['subject' => $subject, 'imageUrl' => null];
                }
            }
            return $array;
        }
        return null;
    }

    public function choose_university_subjects($request)
    {
       $token=PersonalAccessToken::findToken($request->bearerToken());
       $user=$token->tokenable;
       if($user instanceof \App\Models\Students){
            $validate = Validator::make($request->all(), [
                'University_subjects_id' => 'required|array',
                'University_subjects_id.*' => 'integer|exists:University_subjects,id'
            ]);
            if ($validate->fails()) {
                return response()->json(['message' => $validate->errors()]);
            }
            $University_subjects = $request->input('University_subjects_id');
            return $this->student_repositories_interface->Student_university_subjects($user->id, $University_subjects);
        }
        elseif($user instanceof \App\Models\Teacher){
            $validate = Validator::make($request->all(), [
                'University_subjects_id' => 'required|array|min:1',
                'University_subjects_id.*.id' => 'required|integer|exists:University_subjects,id',
                'University_subjects_id.*.lesson_duration' => [
                    'required',
                    'date_format:H:i',
                    function ($attribute, $value, $fail) {
                        [$hours, $minutes] = explode(':', $value);
                        $totalMinutes = $hours * 60 + $minutes;
                        if ($totalMinutes < 30) {
                            $fail('lesson duration must dont to be less 30 minute');
                        }
                    },
                ],
                'University_subjects_id.*.lesson_price' => 'required|numeric|min:0'
            ]);
            if ($validate->fails()) {
                return response()->json(['message' => $validate->errors()]);
            }
            $University_subjects = $request->input('University_subjects_id');
            return $this->teacherRepositoriesInterface->Teacher_university_subjects($user->id, $University_subjects,$request);
        }
    }
}
