<?php
namespace App\Repositories;

interface StudentRepositoriesInterface {
public function create($request);
public function Auth($credintals);
    public function findStudent($student);
    public function StudentSchoolStage($student,$school_stage);
    public function get_school_stage();
public function SchoolSubjects($stage);
public function StudentSchoolSubjects($student,$subjects);
public function Student_profile($student);
public function get_university_stage();
public function UniversityStage($student,$university_stage_id);
public function UniversitySubjects($stage);
public function Student_university_subjects($student,$subjects);
public function ShowTeacherAvailable($student);
public function TeacherRating($student,$teacher);
public function TeacherFollowing($student,$teacher,$RecieveNotification);
public function ShowTeacherProfile($teacher);
public function reservation($request,$student_id,$subject,$lessonDuration,$lessonPrice);
public function report($student,$request,$session,$path);

public function GetAvailableTimeTeacher($teacher);
public function GetStages_Subjecs_Teacher($teacher);
public function GetReservation($student);
public function GetLessonse($student);
public function ShowSlider();
public function Payment($student,$teacher,$request);
public function get_all_reservations($student_id);
public function add_session_video($path,$student,$session);
}
