<?php

namespace App\Http\Controllers;

use App\Models\School_stage;
use App\Models\University_stage;
use App\Services\Stages_subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StagesSubjectsController extends Controller
{
    protected $stages_subjects;
    public function __construct(Stages_subjects $stages_subjects)
    {
        $this->stages_subjects=$stages_subjects;
    }

    public function get_school_stage()
    {
        $response = $this->stages_subjects->School_stage();
        // if ($response->isEmpty()) {
        //     return response()->json(['message' => 'not found any stage']);
        // }
        return response()->json(['message' => 'get school stage successfully', 'result' => $response]);
    }
    public function choose_school_study_stage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'School_stage_id' => 'required|array',
            'School_stage_id.*' => 'integer|exists:school_stage,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        $School_stage_id = $request->input('School_stage_id');
        $result = $this->stages_subjects->Choose_school_stage($request, $School_stage_id);
        return response()->json(['message' => 'school stages is added successfully', 'result' => $result]);
    }
    public function get_school_subjects_stage(School_stage $school_stage)
    {
        $subjects = $this->stages_subjects->School_stage_subjects($school_stage);
        if ($subjects == null) {
            return response()->json(['message' => 'not found any subjects']);
        }
        return response()->json(['message' => 'subjects in school stage', 'subjects' => $subjects]);
    }

    public function choose_school_subjects(Request $request)
    {
        $result = $this->stages_subjects->choose_school_subjects($request);
        return response()->json(['message' => 'school subjects is added successfully', 'result' => $result]);
    }

    public function get_university_stage()
    {
        $response = $this->stages_subjects->University_stage();
        if ($response->isEmpty()) {
            return response()->json(['message' => 'not found any stage']);
        }
        return response()->json(['message' => 'get university stage successfully', 'result' => $response]);
    }

    public function Choose_university_study_stage(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'university_stage_id' => 'required|array',
            'university_stage_id.*' => 'integer|exists:university_stage,id'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()]);
        }
        $university_stage_id = $request->input('university_stage_id');
        $university_stage = $this->stages_subjects->Choose_university_stage($request,$university_stage_id);
        
        return response()->json(['message' => 'university stage added successfully', 'result' => $university_stage]);
    }

    public function get_university_stage_subjects(University_stage $university_stage)
    {
        $subjects = $this->stages_subjects->get_university_stage_subjects($university_stage);
        if ($subjects == null) {
            return response()->json(['message' => 'not found any subjects']);
        }
        return response()->json(['message' => 'subjects in university stage', 'subjects' => $subjects]);
    }
    public function choose_university_subjects(Request $request)
    {

        $result = $this->stages_subjects->choose_university_subjects($request);
        return response()->json(['message' => 'university subjects added successfully', 'subjects' => $result]);
    }
}
